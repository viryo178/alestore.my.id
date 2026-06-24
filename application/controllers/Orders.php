<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->ensure_order_account_columns();
    }

    public function index()
    {
        $this->db->select('o.*, s.shop_name, c.name customer_name, u.name admin_name')
            ->from('orders o')
            ->join('shopee_stores s', 's.id = o.shopee_store_id', 'left')
            ->join('customers c', 'c.id = o.customer_id', 'left')
            ->join('users u', 'u.id = o.user_id', 'left')
            ->order_by('o.created_at', 'DESC')
            ->limit(500);

        if ($this->input->get('q')) {
            $q = $this->input->get('q', true);
            $this->db->group_start()
                ->like('o.shopee_order_id', $q)
                ->or_like('o.product_name', $q)
                ->or_like('o.buyer_email', $q)
                ->or_like('c.name', $q)
                ->or_like('s.shop_name', $q)
                ->group_end();
        }

        if ($this->input->get('store_id') && $this->input->get('store_id') !== 'all') {
            $this->db->where('o.shopee_store_id', $this->input->get('store_id', true));
        }

        if ($this->input->get('user_id') && $this->input->get('user_id') !== 'all') {
            $this->db->where('o.user_id', (int) $this->input->get('user_id', true));
        }

        if ($this->input->get('order_type') && $this->input->get('order_type') !== 'all') {
            $this->db->where('o.order_type', $this->input->get('order_type', true));
        }

        if ($this->input->get('expired_buyer') === 'active') {
            $this->db->group_start()->where('o.expired_at >=', date('Y-m-d 00:00:00'))->or_where('o.expired_at IS NULL', null, false)->group_end();
        } elseif ($this->input->get('expired_buyer') === 'expired') {
            $this->db->where('o.expired_at <', date('Y-m-d 00:00:00'));
        }

        if ($this->input->get('from')) {
            $this->db->where('DATE(o.created_at) >=', $this->input->get('from', true));
        }

        if ($this->input->get('to')) {
            $this->db->where('DATE(o.created_at) <=', $this->input->get('to', true));
        }

        $rows = $this->db->get()->result();

        $products = $this->db->table_exists('digital_products') ? $this->App_model->all('digital_products', 'name ASC') : array();
        $variations = $this->db->table_exists('digital_product_variations') ? $this->App_model->all('digital_product_variations', 'label ASC') : array();
        $privateStock = $this->db
            ->where('account_type', 'private')
            ->where('status', 'available')
            ->where('used_slot <', 1)
            ->count_all_results('digital_accounts');
        $sharingRows = $this->db
            ->select('COALESCE(SUM(GREATEST(max_slot - used_slot, 0)), 0) total_slot', false)
            ->where('account_type', 'sharing')
            ->where('status', 'available')
            ->get('digital_accounts')
            ->row();

        $data = array(
            'title' => 'Orders',
            'rows' => $rows,
            'stores' => $this->App_model->all('shopee_stores', 'shop_name ASC'),
            'admins' => $this->db->table_exists('users') ? $this->App_model->all('users', 'name ASC') : array(),
            'products' => $products,
            'variations' => $variations,
            'durations' => $this->db->table_exists('expire_durations') ? $this->App_model->all('expire_durations', 'is_default DESC, days ASC') : array(),
            'private_stock' => $privateStock,
            'sharing_stock' => $sharingRows ? (int) $sharingRows->total_slot : 0,
        );
        $this->render('orders/index', $data);
    }

    public function create()
    {
        $products = $this->db->table_exists('digital_products') ? $this->App_model->all('digital_products', 'name ASC') : array();
        $variations = $this->db->table_exists('digital_product_variations') ? $this->App_model->all('digital_product_variations', 'label ASC') : array();
        $privateStock = $this->db
            ->where('account_type', 'private')
            ->where('status', 'available')
            ->where('used_slot <', 1)
            ->count_all_results('digital_accounts');
        $sharingRows = $this->db
            ->select('COALESCE(SUM(GREATEST(max_slot - used_slot, 0)), 0) total_slot', false)
            ->where('account_type', 'sharing')
            ->where('status', 'available')
            ->get('digital_accounts')
            ->row();

        $this->render('orders/create', array(
            'title' => 'Input Order Baru',
            'stores' => $this->App_model->all('shopee_stores', 'shop_name ASC'),
            'products' => $products,
            'variations' => $variations,
            'durations' => $this->db->table_exists('expire_durations') ? $this->App_model->all('expire_durations', 'is_default DESC, days ASC') : array(),
            'private_stock' => $privateStock,
            'sharing_stock' => $sharingRows ? (int) $sharingRows->total_slot : 0,
        ));
    }

    public function store()
    {
        $data = $this->post(array('shopee_order_id', 'shopee_store_id', 'status', 'order_type', 'product_name', 'variation', 'buyer_email', 'expired_at', 'total'));

        if ($this->input->post('new_store_name', true)) {
            $data['shopee_store_id'] = $this->create_store($this->input->post('new_store_name', true));
        }

        if (empty($data['shopee_order_id'])) {
            $data['shopee_order_id'] = 'MANUAL-'.date('YmdHis');
        }
        if (empty($data['status'])) {
            $data['status'] = 'completed';
        }
        if (empty($data['order_type'])) {
            $data['order_type'] = 'private';
        }
        if ($this->input->post('expired_days', true) && empty($data['expired_at'])) {
            $data['expired_at'] = date('Y-m-d H:i:s', strtotime('+'.(int) $this->input->post('expired_days', true).' days'));
        }
        $data['created_at'] = $this->posted_order_datetime();
        $data['user_id'] = (int) $this->session->userdata('user_id');

        if ($data['status'] !== 'pending') {
            $account = $this->find_available_account($data['product_name'], $data['variation'], $data['order_type']);
            if (!$account) {
                $this->session->set_flashdata('error', 'Tidak ada akun available untuk produk dan tipe yang dipilih.');
                redirect('orders/create');
            }

            $assignment = $this->assign_account_to_order($account, $data['order_type']);
            $data = array_merge($data, $assignment);
        }

        $orderId = $this->App_model->insert('orders', $data);
        $this->redirect_success('orders/show/'.$orderId, 'Order berhasil ditambahkan dan akun berhasil diberikan.');
    }

    public function available_account()
    {
        $productName = $this->input->get('product_name', true);
        $variation = $this->input->get('variation', true);
        $orderType = $this->input->get('order_type', true) ?: 'private';
        $account = $this->find_available_account($productName, $variation, $orderType);

        $this->output->set_content_type('application/json');
        echo json_encode(array(
            'available' => (bool) $account,
            'message' => $account ? 'Akun tersedia dan akan dikirim otomatis setelah order disimpan.' : 'Akun tidak tersedia untuk kombinasi produk ini.',
            'account' => $account ? array(
                'id' => (int) $account->id,
                'username' => $account->email,
                'max_user' => $orderType === 'sharing' ? max(1, (int) $account->max_slot) : 1,
                'used_slot' => (int) $account->used_slot,
            ) : null,
        ));
    }

    public function update($id)
    {
        $data = $this->post(array('shopee_order_id', 'shopee_store_id', 'status', 'order_type', 'product_name', 'variation', 'buyer_email', 'account_username', 'account_password', 'account_max_user', 'expired_at', 'total'));

        if (empty($data['shopee_order_id'])) {
            $data['shopee_order_id'] = 'MANUAL-'.date('YmdHis');
        }
        if (empty($data['status'])) {
            $data['status'] = 'pending';
        }
        if (empty($data['order_type'])) {
            $data['order_type'] = 'private';
        }
        if (empty($data['account_max_user'])) {
            $data['account_max_user'] = 1;
        }
        if (!empty($data['expired_at'])) {
            $data['expired_at'] = date('Y-m-d H:i:s', strtotime($data['expired_at']));
        } elseif ($this->input->post('expired_days', true)) {
            $data['expired_at'] = date('Y-m-d H:i:s', strtotime('+'.(int) $this->input->post('expired_days', true).' days'));
        } else {
            $data['expired_at'] = null;
        }

        $this->App_model->update('orders', $id, $data);
        $this->redirect_success('orders', 'Order berhasil diperbarui.');
    }

    public function quick_store()
    {
        $orders = (array) $this->input->post('orders', true);
        if ($orders) {
            $created = 0;
            foreach ($orders as $i => $order) {
                if (empty($order['product_name'])) {
                    continue;
                }

                $expired_days = isset($order['expired_days']) ? (int) $order['expired_days'] : 0;
                $this->App_model->insert('orders', array(
                    'shopee_order_id' => !empty($order['shopee_order_id']) ? $order['shopee_order_id'] : 'QO-'.date('YmdHis').'-'.$i,
                    'shopee_store_id' => !empty($order['shopee_store_id']) ? $order['shopee_store_id'] : null,
                    'user_id' => (int) $this->session->userdata('user_id'),
                    'status' => 'pending',
                    'order_type' => !empty($order['order_type']) ? $order['order_type'] : 'private',
                    'product_name' => $order['product_name'],
                    'variation' => isset($order['variation']) ? $order['variation'] : null,
                    'buyer_email' => isset($order['buyer_email']) ? $order['buyer_email'] : null,
                    'expired_at' => $expired_days > 0 ? date('Y-m-d H:i:s', strtotime('+'.$expired_days.' days')) : null,
                    'total' => isset($order['total']) ? $order['total'] : 0,
                    'created_at' => $this->posted_order_datetime(),
                ));
                $created++;
            }
            $this->redirect_success('orders', $created.' quick order berhasil diproses.');
        }

        $product_names = $this->input->post('product_name', true);
        $order_codes = (array) $this->input->post('shopee_order_id', true);
        $store_ids = (array) $this->input->post('shopee_store_id', true);
        $order_types = (array) $this->input->post('order_type', true);
        $variations = (array) $this->input->post('variation', true);
        $buyer_emails = (array) $this->input->post('buyer_email', true);
        $expired_days_rows = (array) $this->input->post('expired_days', true);
        $totals = (array) $this->input->post('total', true);
        $created = 0;
        foreach ((array) $product_names as $i => $product_name) {
            if (!$product_name) {
                continue;
            }
            $expired_days = isset($expired_days_rows[$i]) ? (int) $expired_days_rows[$i] : 0;
            $this->App_model->insert('orders', array(
                'shopee_order_id' => !empty($order_codes[$i]) ? $order_codes[$i] : 'QO-'.date('YmdHis').'-'.$i,
                'shopee_store_id' => isset($store_ids[$i]) ? $store_ids[$i] : null,
                'user_id' => (int) $this->session->userdata('user_id'),
                'status' => 'pending',
                'order_type' => !empty($order_types[$i]) ? $order_types[$i] : 'private',
                'product_name' => $product_name,
                'variation' => isset($variations[$i]) ? $variations[$i] : null,
                'buyer_email' => isset($buyer_emails[$i]) ? $buyer_emails[$i] : null,
                'expired_at' => $expired_days > 0 ? date('Y-m-d H:i:s', strtotime('+'.$expired_days.' days')) : null,
                'total' => isset($totals[$i]) ? $totals[$i] : 0,
                'created_at' => $this->posted_order_datetime(),
            ));
            $created++;
        }
        $this->redirect_success('orders', $created.' quick order berhasil diproses.');
    }

    private function create_store($shop_name)
    {
        $this->App_model->insert('shopee_stores', array(
            'shop_name' => $shop_name,
            'platform' => 'Manual',
            'shop_id' => 'manual-'.date('YmdHis'),
            'status' => 'active',
        ));

        return $this->db->insert_id();
    }

    private function posted_order_datetime()
    {
        $clientDateTime = trim((string) $this->input->post('created_at_client', true));
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $clientDateTime)) {
            return $clientDateTime;
        }

        $orderDate = trim((string) $this->input->post('created_at_date', true));
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $orderDate)) {
            return $orderDate.' '.date('H:i:s');
        }

        return now_sql();
    }

    public function show($id)
    {
        $order = $this->db->select('o.*, s.shop_name, c.name customer_name, u.name admin_name')->from('orders o')
            ->join('shopee_stores s', 's.id = o.shopee_store_id', 'left')
            ->join('customers c', 'c.id = o.customer_id', 'left')
            ->join('users u', 'u.id = o.user_id', 'left')
            ->where('o.id', $id)->get()->row();
        $assignedAccount = null;
        if ($order && !empty($order->assigned_digital_account_id)) {
            $assignedAccount = $this->db->where('id', $order->assigned_digital_account_id)->get('digital_accounts')->row();
        }
        $claims = $this->db->where('order_id', $id)->or_where('order_code', $order ? $order->shopee_order_id : '')->order_by('id', 'DESC')->get('warranty_claims')->result();
        $this->render('orders/show', array('title' => 'Detail Order', 'order' => $order, 'assigned_account' => $assignedAccount, 'claims' => $claims));
    }

    public function delete($id)
    {
        $this->App_model->delete('orders', $id);
        $this->redirect_success('orders', 'Order berhasil dihapus.');
    }

    private function ensure_order_account_columns()
    {
        if (!$this->db->table_exists('orders')) {
            return;
        }

        if (!$this->db->field_exists('account_username', 'orders')) {
            $this->db->query("ALTER TABLE `orders` ADD `account_username` VARCHAR(255) DEFAULT NULL AFTER `buyer_email`");
        }
        if (!$this->db->field_exists('user_id', 'orders')) {
            $this->db->query("ALTER TABLE `orders` ADD `user_id` BIGINT UNSIGNED DEFAULT NULL AFTER `customer_id`");
        }
        if (!$this->db->field_exists('assigned_digital_account_id', 'orders')) {
            $this->db->query("ALTER TABLE `orders` ADD `assigned_digital_account_id` BIGINT UNSIGNED DEFAULT NULL AFTER `account_username`");
        }
        if (!$this->db->field_exists('account_password', 'orders')) {
            $this->db->query("ALTER TABLE `orders` ADD `account_password` TEXT DEFAULT NULL AFTER `assigned_digital_account_id`");
        }
        if (!$this->db->field_exists('account_max_user', 'orders')) {
            $this->db->query("ALTER TABLE `orders` ADD `account_max_user` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `account_password`");
        }
    }

    private function find_available_account($productName, $variation, $orderType)
    {
        $orderType = $orderType === 'sharing' ? 'sharing' : 'private';
        $this->db->from('digital_accounts')
            ->where('account_type', $orderType)
            ->where('status', 'available')
            ->group_start()
                ->where('digital_product_id IN (SELECT id FROM digital_products WHERE name = '.$this->db->escape($productName).')', null, false)
                ->or_where('product_name', $productName)
            ->group_end();

        if ($variation !== '') {
            $this->db->group_start()
                ->where('digital_product_variation_id IN (SELECT id FROM digital_product_variations WHERE label = '.$this->db->escape($variation).')', null, false)
                ->or_where('variation', $variation)
            ->group_end();
        }

        if ($orderType === 'sharing') {
            $this->db->where('used_slot < max_slot', null, false);
        } else {
            $this->db->where('used_slot <', 1);
        }

        return $this->db->order_by('id', 'ASC')->limit(1)->get()->row();
    }

    private function assign_account_to_order($account, $orderType)
    {
        $maxUser = $orderType === 'sharing' ? max(1, (int) $account->max_slot) : 1;
        $usedSlot = (int) $account->used_slot + 1;
        $status = $usedSlot >= $maxUser ? 'sold' : 'available';

        $this->db->where('id', $account->id)->update('digital_accounts', array(
            'used_slot' => $usedSlot,
            'status' => $status,
            'sold_at' => date('Y-m-d H:i:s'),
        ));

        return array(
            'assigned_digital_account_id' => (int) $account->id,
            'account_username' => $account->email,
            'account_password' => $account->password,
            'account_max_user' => $maxUser,
        );
    }
}

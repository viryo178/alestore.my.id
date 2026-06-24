<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Digital_accounts extends MY_Controller
{
    public function index()
    {
        $this->db->select('a.*, p.name product_master, v.label variation_master')
            ->from('digital_accounts a')
            ->join('digital_products p', 'p.id = a.digital_product_id', 'left')
            ->join('digital_product_variations v', 'v.id = a.digital_product_variation_id', 'left');

        if ($this->input->get('q')) {
            $q = $this->input->get('q', true);
            $this->db->group_start()->like('a.product_name', $q)->or_like('a.email', $q)->or_like('a.variation', $q)->group_end();
        }
        if ($this->input->get('account_type') && $this->input->get('account_type') !== 'all') {
            $this->db->where('a.account_type', $this->input->get('account_type', true));
        }
        if ($this->input->get('status') && $this->input->get('status') !== 'all') {
            $this->db->where('a.status', $this->input->get('status', true));
        }

        $rows = $this->db->order_by('a.id', 'DESC')->get()->result();
        $digitalProducts = $this->App_model->all('digital_products', 'name ASC');
        $variations = $this->db->table_exists('digital_product_variations') ? $this->App_model->all('digital_product_variations', 'label ASC') : array();
        $expiredSoon = $this->db
            ->where('expired_at IS NOT NULL', null, false)
            ->where('expired_at >=', date('Y-m-d H:i:s'))
            ->where('expired_at <=', date('Y-m-d H:i:s', strtotime('+2 days')))
            ->where_not_in('status', array('sold', 'deactived', 'no_access'))
            ->count_all_results('digital_accounts');

        $this->render('digital_accounts/index', array(
            'title' => 'Akun Digital',
            'rows' => $rows,
            'total_accounts' => $this->App_model->count('digital_accounts'),
            'expired_soon_count' => $expiredSoon,
            'products' => $digitalProducts,
            'digital_products' => $digitalProducts,
            'variations' => $variations,
            'product_stocks' => $this->product_stocks($rows, $digitalProducts, $variations),
            'license_stocks' => $this->license_stocks($rows),
            'durations' => $this->db->table_exists('expire_durations') ? $this->App_model->all('expire_durations', 'days ASC') : array(),
            'stock_section' => $this->input->get('section', true) ?: 'account-stock',
        ));
    }

    public function create()
    {
        $this->render('digital_accounts/form', array('title' => 'Tambah Akun Digital', 'row' => null, 'products' => $this->App_model->all('digital_products', 'name ASC')));
    }

    public function store()
    {
        $this->App_model->insert('digital_accounts', $this->account_data());
        $section = $this->input->post('_redirect_section', true) ?: 'account-stock';
        $this->redirect_success('digital-accounts?section='.$section, 'Akun digital berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $this->render('digital_accounts/form', array('title' => 'Edit Akun Digital', 'row' => $this->App_model->find('digital_accounts', $id), 'products' => $this->App_model->all('digital_products', 'name ASC')));
    }

    public function update($id)
    {
        $this->App_model->update('digital_accounts', $id, $this->account_data());
        $this->redirect_success('digital-accounts', 'Akun digital berhasil diperbarui.');
    }

    public function delete($id)
    {
        $this->App_model->delete('digital_accounts', $id);
        $this->redirect_success('digital-accounts', 'Akun digital berhasil dihapus.');
    }

    public function bulk_create()
    {
        $this->render('digital_accounts/bulk_create', array(
            'title' => 'Bulk Tambah Akun',
            'products' => $this->App_model->all('digital_products', 'name ASC'),
            'durations' => $this->db->table_exists('expire_durations') ? $this->App_model->all('expire_durations', 'is_default DESC, days ASC') : array(),
        ));
    }

    public function password_expired()
    {
        $q = $this->input->get('q', true);
        $status = $this->input->get('status', true) ?: 'all';

        $this->db->from('digital_accounts')
            ->where('expired_at IS NOT NULL', null, false)
            ->where('expired_at <', date('Y-m-d H:i:s'));

        if ($q) {
            $this->db->group_start()
                ->like('product_name', $q)
                ->or_like('variation', $q)
                ->or_like('email', $q)
                ->or_like('password', $q)
                ->group_end();
        }

        if ($status !== 'all') {
            if ($status === 'sold') {
                $this->db->where_in('status', array('sold', 'unavailable', 'unvailabel'));
            } else {
                $this->db->where('status', $status);
            }
        }

        $rows = $this->db->order_by('expired_at', 'ASC')->limit(500)->get()->result();

        $this->render('digital_accounts/password_expired', array(
            'title' => 'Ganti Password Exp',
            'rows' => $rows,
            'durations' => $this->db->table_exists('expire_durations') ? $this->App_model->all('expire_durations', 'is_default DESC, days ASC') : array(),
            'total_expired' => $this->db
                ->where('expired_at IS NOT NULL', null, false)
                ->where('expired_at <', date('Y-m-d H:i:s'))
                ->count_all_results('digital_accounts'),
            'status_filter' => $status,
        ));
    }

    public function update_expired_password($id)
    {
        $data = array(
            'email' => $this->input->post('email', true),
            'password' => $this->input->post('password', true),
            'status' => $this->normalize_account_status($this->input->post('status', true) ?: 'available'),
            'extra_info' => $this->input->post('extra_info', true),
            'notes' => $this->input->post('notes', true),
        );

        $days = (int) $this->input->post('expired_days', true);
        if ($days > 0) {
            $data['expired_at'] = date('Y-m-d H:i:s', strtotime('+'.$days.' days'));
        } elseif ($this->input->post('expired_at', true)) {
            $data['expired_at'] = date('Y-m-d H:i:s', strtotime($this->input->post('expired_at', true)));
        }

        $this->App_model->update('digital_accounts', $id, $this->filter_existing_fields('digital_accounts', $data));
        $this->redirect_success('digital-accounts/password-expired', 'Password akun berhasil diperbarui.');
    }

    public function bulk_update_expired_password()
    {
        $ids = (array) $this->input->post('account_ids', true);
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if (!$ids) {
            $this->redirect_success('digital-accounts/password-expired', 'Pilih akun yang ingin di-bulk edit.');
            return;
        }

        $data = array(
            'status' => $this->normalize_account_status($this->input->post('status', true) ?: 'available'),
        );

        $days = (int) $this->input->post('expired_days', true);
        if ($days > 0) {
            $data['expired_at'] = date('Y-m-d H:i:s', strtotime('+'.$days.' days'));
        } elseif ($this->input->post('expired_at', true)) {
            $data['expired_at'] = date('Y-m-d H:i:s', strtotime($this->input->post('expired_at', true)));
        }

        $bulkNotes = trim((string) $this->input->post('notes', true));
        if ($bulkNotes !== '') {
            $data['notes'] = $bulkNotes;
        }

        $emails = (array) $this->input->post('emails', true);
        $passwords = (array) $this->input->post('passwords', true);
        $updated = 0;

        foreach ($ids as $id) {
            $payload = $data;
            if (isset($emails[$id])) {
                $payload['email'] = trim((string) $emails[$id]);
            }
            if (isset($passwords[$id])) {
                $payload['password'] = trim((string) $passwords[$id]);
            }

            $this->App_model->update('digital_accounts', $id, $this->filter_existing_fields('digital_accounts', $payload));
            $updated++;
        }

        $this->redirect_success('digital-accounts/password-expired', $updated.' akun berhasil di-bulk edit.');
    }

    public function bulk_store()
    {
        $productName = $this->input->post('product_name', true);
        $variationLabel = $this->input->post('variation', true);
        if ($this->input->post('digital_product_variation_id', true)) {
            $variation = $this->App_model->find('digital_product_variations', $this->input->post('digital_product_variation_id', true));
            if ($variation) {
                $variationLabel = $variation->label;
            }
        }
        $lines = preg_split('/\r\n|\r|\n/', (string) $this->input->post('stock_lines', true));
        $days = (int) $this->input->post('expired_days', true);
        $created = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $parts = array_map('trim', explode('|', $line, 2));
            $this->App_model->insert('digital_accounts', array(
                'digital_product_id' => $this->input->post('digital_product_id', true) ?: null,
                'digital_product_variation_id' => $this->input->post('digital_product_variation_id', true) ?: null,
                'product_name' => $productName,
                'variation' => $variationLabel,
                'account_type' => $this->input->post('account_type', true) ?: 'private',
                'email' => $parts[0] ?? null,
                'password' => $parts[1] ?? null,
                'method' => $this->input->post('method', true) ?: 'credentials',
                'max_slot' => $this->input->post('max_slot', true) ?: 1,
                'used_slot' => 0,
                'status' => 'available',
                'hpp' => $this->input->post('hpp', true) ?: 0,
                'notes' => $this->input->post('notes', true),
                'expired_at' => $days > 0 ? date('Y-m-d H:i:s', strtotime('+'.$days.' days')) : null,
            ));
            $created++;
        }

        $this->redirect_success('digital-accounts?section=product-stock', $created.' akun berhasil ditambahkan.');
    }

    public function stock_store()
    {
        $this->bulk_store();
    }

    public function product_store()
    {
        $name = $this->input->post('name', true);
        $productId = $this->App_model->insert('digital_products', array(
            'name' => $name,
            'account_type' => $this->input->post('account_type', true) ?: 'private',
            'method' => $this->input->post('method', true) ?: 'credentials',
            'max_slot' => $this->input->post('max_slot', true) ?: 1,
            'hpp' => $this->input->post('hpp', true) ?: 0,
            'is_active' => 1,
            'notes' => $this->input->post('notes', true),
        ));

        $variations = preg_split('/\r\n|\r|\n|,/', (string) $this->input->post('variations', true));
        foreach ($variations as $variation) {
            $variation = trim($variation);
            if ($variation !== '') {
                $this->App_model->insert('digital_product_variations', array(
                    'digital_product_id' => $productId,
                    'label' => $variation,
                    'sale_price' => 0,
                    'hpp' => 0,
                    'is_active' => 1,
                ));
            }
        }

        $this->redirect_success('digital-accounts?section=product-stock', 'Produk berhasil ditambahkan.');
    }

    public function product_update($id)
    {
        $oldProduct = $this->App_model->find('digital_products', $id);
        $this->App_model->update('digital_products', $id, array(
            'name' => $this->input->post('name', true),
            'account_type' => $this->input->post('account_type', true),
            'method' => $this->input->post('method', true),
            'max_slot' => $this->input->post('max_slot', true),
            'hpp' => $this->input->post('hpp', true),
            'notes' => $this->input->post('notes', true),
        ));

        if ($this->db->table_exists('digital_product_variations')) {
            $this->sync_product_variations($id, (string) $this->input->post('variations', true));
        }

        $this->db->group_start()
            ->where('digital_product_id', $id);
        if ($oldProduct) {
            $this->db->or_where('product_name', $oldProduct->name);
        }
        $this->db->group_end()->update('digital_accounts', $this->filter_existing_fields('digital_accounts', array(
            'product_name' => $this->input->post('name', true),
            'account_type' => $this->input->post('account_type', true),
            'method' => $this->input->post('method', true),
            'max_slot' => max(1, (int) $this->input->post('max_slot', true)),
        )));

        $this->redirect_success('digital-accounts?section=product-stock', 'Produk berhasil diperbarui.');
    }

    public function product_destroy($id)
    {
        $this->db->where('digital_product_id', $id)->update('digital_accounts', array(
            'digital_product_id' => null,
            'digital_product_variation_id' => null,
        ));
        $this->App_model->delete('digital_products', $id);
        $this->redirect_success('digital-accounts?section=product-stock', 'Produk berhasil dihapus.');
    }

    public function feed()
    {
        $this->output->set_content_type('application/json');
        $this->db->from('digital_accounts');

        if ($this->input->get('q')) {
            $q = $this->input->get('q', true);
            $this->db->group_start()->like('product_name', $q)->or_like('email', $q)->or_like('variation', $q)->group_end();
        }
        if ($this->input->get('account_type') && $this->input->get('account_type') !== 'all') {
            $this->db->where('account_type', $this->input->get('account_type', true));
        }
        if ($this->input->get('status') && $this->input->get('status') !== 'all') {
            $status = $this->input->get('status', true);
            if ($status === 'sold') {
                $this->db->where_in('status', array('sold', 'unavailable', 'unvailabel'));
            } else {
                $this->db->where('status', $status);
            }
        }

        $accounts = $this->db->order_by('id', 'DESC')->limit(500)->get()->result();
        $items = array();
        foreach ($accounts as $account) {
            $status = $this->normalize_account_status($account->status);
            $items[] = array(
                'id' => (int) $account->id,
                'product_name' => $account->product_name,
                'variation' => $account->variation,
                'email' => $account->email,
                'account_type' => $account->account_type,
                'method' => $account->method,
                'slot' => $account->account_type === 'sharing' ? $account->used_slot.'/'.$account->max_slot : '-',
                'hpp' => $account->hpp > 0 ? rupiah($account->hpp) : '-',
                'status' => $status,
                'status_label' => ucwords(str_replace('_', ' ', $status)),
                'expired_at' => $account->expired_at ? date('d/m/Y', strtotime($account->expired_at)) : null,
                'expired_warning' => $account->expired_at && strtotime($account->expired_at) <= strtotime('+2 days'),
                'edit_modal' => 'editAccountModal'.$account->id,
                'delete_modal' => 'deleteAccountModal'.$account->id,
            );
        }

        echo json_encode(array(
            'total' => $this->App_model->count('digital_accounts'),
            'accounts' => $items,
            'server_time' => date('d M Y, H:i:s').' WIB',
            'expired_soon' => $this->db
                ->where('expired_at IS NOT NULL', null, false)
                ->where('expired_at >=', date('Y-m-d H:i:s'))
                ->where('expired_at <=', date('Y-m-d H:i:s', strtotime('+2 days')))
                ->where_not_in('status', array('sold', 'deactived', 'no_access'))
                ->count_all_results('digital_accounts'),
        ));
    }

    private function account_data()
    {
        $data = $this->post(array(
            'digital_product_id', 'digital_product_variation_id', 'product_name', 'variation',
            'account_type', 'email', 'password', 'extra_info', 'method', 'max_slot', 'used_slot',
            'status', 'hpp', 'notes', 'expired_at', 'sold_at'
        ));

        foreach (array('digital_product_id', 'digital_product_variation_id') as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        $product = !empty($data['digital_product_id']) ? $this->App_model->find('digital_products', $data['digital_product_id']) : null;
        $variation = !empty($data['digital_product_variation_id']) ? $this->App_model->find('digital_product_variations', $data['digital_product_variation_id']) : null;

        if ($product) {
            $data['product_name'] = $product->name;
            $data['account_type'] = $data['account_type'] ?: ($product->account_type ?? 'private');
            $data['method'] = $data['method'] ?: ($product->method ?? 'credentials');
            $data['max_slot'] = $data['max_slot'] ?: ($product->max_slot ?? 1);
            $data['hpp'] = $data['hpp'] !== '' ? $data['hpp'] : ($product->hpp ?? 0);
        }

        if ($variation) {
            $data['variation'] = $variation->label;
        }

        if ($this->input->post('expired_days', true) && empty($data['expired_at'])) {
            $data['expired_at'] = date('Y-m-d H:i:s', strtotime('+'.(int) $this->input->post('expired_days', true).' days'));
        }

        if (!empty($data['expired_at'])) {
            $data['expired_at'] = date('Y-m-d H:i:s', strtotime($data['expired_at']));
        } else {
            $data['expired_at'] = null;
        }
        if (!empty($data['sold_at'])) {
            $data['sold_at'] = date('Y-m-d H:i:s', strtotime($data['sold_at']));
        } else {
            $data['sold_at'] = null;
        }
        if ($this->input->post('_resolve_notification', true) && $this->db->field_exists('notification_resolved_at', 'digital_accounts')) {
            $data['notification_resolved_at'] = now_sql();
        }

        if (empty($data['product_name'])) {
            $data['product_name'] = 'Tanpa Produk';
        }
        $data['account_type'] = $data['account_type'] ?: 'private';
        $data['method'] = $data['method'] ?: 'credentials';
        $data['max_slot'] = max(1, (int) ($data['max_slot'] ?: 1));
        $data['used_slot'] = max(0, (int) ($data['used_slot'] ?: 0));
        $data['status'] = $this->normalize_account_status($data['status'] ?: 'available');
        $data['hpp'] = $data['hpp'] !== '' ? $data['hpp'] : 0;

        return $this->filter_existing_fields('digital_accounts', $data);
    }

    private function filter_existing_fields($table, $data)
    {
        foreach (array_keys($data) as $field) {
            if (!$this->db->field_exists($field, $table)) {
                unset($data[$field]);
            }
        }
        return $data;
    }

    private function sync_product_variations($productId, $rawVariations)
    {
        $labels = array();
        foreach (preg_split('/\r\n|\r|\n|,/', $rawVariations) as $label) {
            $label = trim($label);
            if ($label !== '') {
                $labels[$label] = $label;
            }
        }

        $existing = $this->db
            ->where('digital_product_id', $productId)
            ->get('digital_product_variations')
            ->result();
        $existingByLabel = array();
        foreach ($existing as $variation) {
            $existingByLabel[$variation->label] = $variation;
        }

        foreach ($labels as $label) {
            if (isset($existingByLabel[$label])) {
                $this->App_model->update('digital_product_variations', $existingByLabel[$label]->id, array(
                    'label' => $label,
                    'is_active' => 1,
                ));
                continue;
            }

            $this->App_model->insert('digital_product_variations', array(
                'digital_product_id' => $productId,
                'label' => $label,
                'sale_price' => 0,
                'hpp' => 0,
                'is_active' => 1,
            ));
        }

        foreach ($existing as $variation) {
            if (isset($labels[$variation->label])) {
                continue;
            }

            $accountCount = $this->db
                ->where('digital_product_variation_id', $variation->id)
                ->count_all_results('digital_accounts');
            if ($accountCount > 0) {
                $this->App_model->update('digital_product_variations', $variation->id, array('is_active' => 0));
            } else {
                $this->App_model->delete('digital_product_variations', $variation->id);
            }
        }
    }

    private function normalize_account_status($status)
    {
        return in_array($status, array('unavailable', 'unvailabel'), true) ? 'sold' : $status;
    }

    private function product_stocks($accounts, $products, $variations)
    {
        $rows = array();
        foreach ($products as $product) {
            $productVariations = array_values(array_filter($variations, function ($variation) use ($product) {
                return (int) $variation->digital_product_id === (int) $product->id;
            }));
            if (!$productVariations) {
                $productVariations = array((object) array('id' => null, 'label' => null));
            }

            foreach ($productVariations as $variation) {
                $items = array_values(array_filter($accounts, function ($account) use ($product, $variation) {
                    if (!empty($account->digital_product_id) && (int) $account->digital_product_id === (int) $product->id) {
                        if (!empty($variation->id)) {
                            return (int) $account->digital_product_variation_id === (int) $variation->id
                                || (empty($account->digital_product_variation_id) && $account->variation === $variation->label);
                        }
                        return empty($account->digital_product_variation_id) && empty($account->variation);
                    }
                    return $account->product_name === $product->name && (empty($variation->label) ? empty($account->variation) : $account->variation === $variation->label);
                }));
                $rows[] = $this->product_stock_payload($items, array(
                    'product_id' => $product->id,
                    'variation_id' => $variation->id,
                    'product_name' => $product->name,
                    'variation' => $variation->label,
                    'account_type' => $product->account_type ?? 'private',
                    'method' => $product->method ?? 'credentials',
                    'max_slot' => $product->max_slot ?? 1,
                    'hpp' => $product->hpp ?? 0,
                    'is_catalog' => true,
                ));
            }
        }

        $legacy = array();
        foreach ($accounts as $account) {
            if (!empty($account->digital_product_id)) {
                continue;
            }
            $key = ($account->product_name ?: 'Tanpa Produk').'|'.($account->variation ?: '').'|'.($account->account_type ?: 'private');
            if (!isset($legacy[$key])) {
                $legacy[$key] = array();
            }
            $legacy[$key][] = $account;
        }
        foreach ($legacy as $items) {
            $first = $items[0];
            $rows[] = $this->product_stock_payload($items, array(
                'product_id' => null,
                'variation_id' => null,
                'product_name' => $first->product_name ?: 'Tanpa Produk',
                'variation' => $first->variation,
                'account_type' => $first->account_type ?: 'private',
                'method' => $first->method ?: 'credentials',
                'max_slot' => $first->max_slot ?: 1,
                'hpp' => $first->hpp ?: 0,
                'is_catalog' => false,
            ));
        }

        usort($rows, function ($a, $b) {
            return strcmp($a['product_name'].' '.$a['variation'], $b['product_name'].' '.$b['variation']);
        });
        return $rows;
    }

    private function product_stock_payload($items, $base)
    {
        $activeStatuses = array('available', 'verified', 'active_age');
        $available = $full = $sold = $banned = $emptySlot = 0;
        foreach ($items as $item) {
            $status = $this->normalize_account_status($item->status);
            if ($status === 'available') {
                $available++;
            }
            if ($status === 'sold') {
                $sold++;
            }
            if (in_array($status, array('deactived', 'no_access'), true)) {
                $banned++;
            }
            if ($item->account_type === 'sharing' && in_array($status, $activeStatuses, true)) {
                $remaining = max(0, (int) $item->max_slot - (int) $item->used_slot);
                $emptySlot += $remaining;
                if ((int) $item->used_slot >= (int) $item->max_slot) {
                    $full++;
                }
            }
        }
        return array_merge($base, array(
            'total' => count($items),
            'available' => $available,
            'empty_slot' => $emptySlot,
            'full' => $full,
            'sold' => $sold,
            'banned' => $banned,
        ));
    }

    private function license_stocks($accounts)
    {
        $groups = array();
        foreach ($accounts as $account) {
            if (!in_array($account->method, array('license', 'link'), true)) {
                continue;
            }
            $key = ($account->product_name ?: 'Tanpa Produk').'|'.($account->variation ?: '').'|'.($account->method ?: 'license');
            if (!isset($groups[$key])) {
                $groups[$key] = array();
            }
            $groups[$key][] = $account;
        }

        $rows = array();
        foreach ($groups as $items) {
            $first = $items[0];
            $row = array(
                'product_name' => $first->product_name ?: 'Tanpa Produk',
                'variation' => $first->variation,
                'method' => $first->method ?: 'license',
                'total' => count($items),
                'available' => 0,
                'sold' => 0,
                'problem' => 0,
            );
            foreach ($items as $item) {
                $status = $this->normalize_account_status($item->status);
                if ($status === 'available') $row['available']++;
                if ($status === 'sold') $row['sold']++;
                if (in_array($status, array('deactived', 'no_access'), true)) $row['problem']++;
            }
            $rows[] = $row;
        }
        return $rows;
    }
}

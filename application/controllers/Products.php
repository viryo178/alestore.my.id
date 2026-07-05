<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends MY_Controller
{
    public function index()
    {
        $products = $this->db->select('p.*, COUNT(a.id) accounts_count, COALESCE(AVG(a.hpp), 0) accounts_avg_hpp')
            ->from('digital_products p')->join('digital_accounts a', 'a.digital_product_id = p.id', 'left')
            ->group_by('p.id')->order_by('p.name')->get()->result();
        $variations = $this->db->order_by('label')->get('digital_product_variations')->result();
        foreach ($products as $product) {
            $product->variations_count = 0;
            $product->is_legacy = false;
            $product->row_key = (string) $product->id;
            foreach ($variations as $variation) {
                if ((int) $variation->digital_product_id === (int) $product->id) {
                    $product->variations_count++;
                }
            }
        }
        $products = array_merge($products, $this->legacy_products($products));
        usort($products, function ($a, $b) {
            return strcasecmp($a->name, $b->name);
        });
        $this->render('products/index', array(
            'title' => 'Nama Produk',
            'products' => $products,
            'variations' => $variations,
            'stores' => $this->db->table_exists('shopee_stores') ? $this->App_model->all('shopee_stores', 'shop_name ASC') : array(),
            'fulfillment_options' => $this->fulfillment_options(),
        ));
    }

    public function store()
    {
        $data = $this->product_data();
        $data['is_active'] = 1;
        $this->App_model->insert('digital_products', $data);
        $productId = $this->db->insert_id();
        $this->link_legacy_accounts($productId, $data);
        $this->redirect_success('products', 'Produk berhasil ditambahkan.');
    }

    public function update($id)
    {
        $data = $this->product_data();
        $this->App_model->update('digital_products', $id, $data);
        $this->db->where('digital_product_id', $id)->update('digital_accounts', array(
            'product_name' => $data['name'],
            'method' => $data['method'],
            'hpp' => $data['hpp'],
        ));
        $this->redirect_success('products', 'Produk berhasil diperbarui.');
    }

    public function delete($id)
    {
        $this->App_model->delete('digital_products', $id);
        $this->redirect_success('products', 'Produk berhasil dihapus.');
    }

    public function legacy_update()
    {
        $data = $this->product_data();
        $data['account_type'] = $this->input->post('old_account_type', true) ?: $data['account_type'];
        $this->legacy_scope()
            ->update('digital_accounts', array(
                'product_name' => $data['name'],
                'method' => $data['method'],
                'account_type' => $data['account_type'],
                'hpp' => $data['hpp'],
            ));

        $this->redirect_success('products', 'Produk stok berhasil diperbarui.');
    }

    public function legacy_delete()
    {
        $this->legacy_scope()->delete('digital_accounts');
        $this->redirect_success('products', 'Produk stok berhasil dihapus.');
    }

    public function store_variation($product_id)
    {
        $data = $this->post(array('label', 'sale_price', 'hpp'));
        $data['digital_product_id'] = $product_id;
        $data['is_active'] = 1;
        $this->App_model->insert('digital_product_variations', $data);
        $this->redirect_success('products', 'Variasi berhasil ditambahkan.');
    }

    private function product_data()
    {
        $method = $this->input->post('method', true) ?: 'credentials';
        return array(
            'name' => $this->input->post('name', true),
            'method' => $method,
            'account_type' => $method === 'invite_email' ? 'sharing' : 'private',
            'max_slot' => 1,
            'hpp' => $this->input->post('hpp', true) ?: 0,
            'notes' => $this->input->post('notes', true),
        );
    }

    private function legacy_products($catalogProducts)
    {
        $catalogNames = array();
        foreach ($catalogProducts as $product) {
            $catalogNames[strtolower(trim($product->name))] = true;
        }

        $rows = $this->db
            ->select('product_name name, account_type, method, COUNT(id) accounts_count, COALESCE(AVG(hpp), 0) accounts_avg_hpp, COALESCE(MAX(hpp), 0) hpp', false)
            ->from('digital_accounts')
            ->group_start()
                ->where('digital_product_id IS NULL', null, false)
                ->or_where('digital_product_id', 0)
                ->or_where('digital_product_id', '')
            ->group_end()
            ->where('product_name IS NOT NULL', null, false)
            ->where('product_name !=', '')
            ->group_by(array('product_name', 'account_type', 'method'))
            ->order_by('product_name', 'ASC')
            ->get()
            ->result();

        $products = array();
        foreach ($rows as $row) {
            if (isset($catalogNames[strtolower(trim($row->name))])) {
                continue;
            }
            $key = md5(strtolower($row->name).'|'.$row->account_type.'|'.$row->method);
            $row->id = 'legacy_'.$key;
            $row->row_key = $row->id;
            $row->is_legacy = true;
            $row->is_active = 1;
            $row->variations_count = $this->legacy_variations_count($row->name, $row->account_type, $row->method);
            $products[] = $row;
        }

        return $products;
    }

    private function legacy_variations_count($productName, $accountType, $method)
    {
        return (int) $this->db
            ->select('variation')
            ->from('digital_accounts')
            ->group_start()
                ->where('digital_product_id IS NULL', null, false)
                ->or_where('digital_product_id', 0)
                ->or_where('digital_product_id', '')
            ->group_end()
            ->where('product_name', $productName)
            ->where('account_type', $accountType)
            ->where('method', $method)
            ->group_by('variation')
            ->get()
            ->num_rows();
    }

    private function link_legacy_accounts($productId, $data)
    {
        $this->db
            ->group_start()
                ->where('digital_product_id IS NULL', null, false)
                ->or_where('digital_product_id', 0)
                ->or_where('digital_product_id', '')
            ->group_end()
            ->where('product_name', $data['name'])
            ->update('digital_accounts', array(
                'digital_product_id' => $productId,
                'method' => $data['method'],
                'account_type' => $data['account_type'],
            ));
    }

    private function legacy_scope()
    {
        return $this->db
            ->group_start()
                ->where('digital_product_id IS NULL', null, false)
                ->or_where('digital_product_id', 0)
                ->or_where('digital_product_id', '')
            ->group_end()
            ->where('product_name', $this->input->post('old_product_name', true))
            ->where('account_type', $this->input->post('old_account_type', true))
            ->where('method', $this->input->post('old_method', true));
    }

    private function fulfillment_options()
    {
        return array(
            'credentials' => 'Akun Login (Credentials)',
            'invite_email' => 'Akun Invite (Family/Team)',
            'license' => 'License Key',
            'link' => 'Access Link',
        );
    }
}

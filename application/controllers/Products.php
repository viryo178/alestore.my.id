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
            foreach ($variations as $variation) {
                if ((int) $variation->digital_product_id === (int) $product->id) {
                    $product->variations_count++;
                }
            }
        }
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

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shopee_stores extends MY_Controller
{
    public function index()
    {
        $this->render('shopee_stores/index', array('title' => 'Toko', 'rows' => $this->App_model->all('shopee_stores', 'shop_name ASC')));
    }

    public function create()
    {
        $this->render('shopee_stores/form', array('title' => 'Tambah Toko', 'row' => null));
    }

    public function store()
    {
        $data = $this->post(array('shop_name', 'platform', 'description', 'admin_fee_percentage', 'shop_id', 'status'));
        if (empty($data['shop_id'])) {
            $data['shop_id'] = 'manual-'.date('YmdHis');
        }
        $this->App_model->insert('shopee_stores', $data);
        $this->redirect_success('shopee-stores', 'Toko berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $this->render('shopee_stores/form', array('title' => 'Edit Toko', 'row' => $this->App_model->find('shopee_stores', $id)));
    }

    public function update($id)
    {
        $this->App_model->update('shopee_stores', $id, $this->post(array('shop_name', 'platform', 'description', 'admin_fee_percentage', 'shop_id', 'status')));
        $this->redirect_success('shopee-stores', 'Toko berhasil diperbarui.');
    }

    public function delete($id)
    {
        $this->App_model->delete('shopee_stores', $id);
        $this->redirect_success('shopee-stores', 'Toko berhasil dihapus.');
    }
}

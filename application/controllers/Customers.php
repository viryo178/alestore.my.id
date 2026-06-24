<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends MY_Controller
{
    public function index()
    {
        $this->render('customers/index', array('title' => 'Customers', 'rows' => $this->App_model->all('customers', 'id DESC')));
    }

    public function create()
    {
        $this->render('customers/form', array('title' => 'Tambah Customer', 'row' => null));
    }

    public function store()
    {
        $this->App_model->insert('customers', $this->post(array('name', 'email', 'phone')));
        $this->App_model->log('create_customer', 'Menambah customer '.$this->input->post('name', true));
        $this->redirect_success('customers', 'Customer berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $this->render('customers/form', array('title' => 'Edit Customer', 'row' => $this->App_model->find('customers', $id)));
    }

    public function update($id)
    {
        $this->App_model->update('customers', $id, $this->post(array('name', 'email', 'phone')));
        $this->redirect_success('customers', 'Customer berhasil diperbarui.');
    }

    public function delete($id)
    {
        $this->App_model->delete('customers', $id);
        $this->redirect_success('customers', 'Customer berhasil dihapus.');
    }
}

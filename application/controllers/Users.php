<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller
{
    public function index()
    {
        $this->render('users/index', array('title' => 'Users', 'rows' => $this->App_model->all('users', 'name ASC')));
    }

    public function create()
    {
        $this->render('users/form', array('title' => 'Tambah User', 'row' => null));
    }

    public function store()
    {
        $this->App_model->insert('users', array(
            'name' => $this->input->post('name', true),
            'email' => $this->input->post('email', true),
            'role' => $this->input->post('role', true) ?: 'staff',
            'password' => password_hash($this->input->post('password', true) ?: 'password', PASSWORD_BCRYPT),
            'email_verified_at' => now_sql(),
        ));
        $this->redirect_success('users', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $this->render('users/form', array('title' => 'Edit User', 'row' => $this->App_model->find('users', $id)));
    }

    public function update($id)
    {
        $data = array(
            'name' => $this->input->post('name', true),
            'email' => $this->input->post('email', true),
            'role' => $this->input->post('role', true) ?: 'staff',
        );
        if ($this->input->post('password', true)) {
            $data['password'] = password_hash($this->input->post('password', true), PASSWORD_BCRYPT);
        }
        $this->App_model->update('users', $id, $data);
        $this->redirect_success('users', 'User berhasil diperbarui.');
    }

    public function delete($id)
    {
        $this->App_model->delete('users', $id);
        $this->redirect_success('users', 'User berhasil dihapus.');
    }

    public function change_password()
    {
        $this->render('users/change_password', array('title' => 'Change Password'));
    }

    public function update_password()
    {
        $userId = $this->input->post('user_id', true);
        if ($userId && $this->input->post('password', true)) {
            $this->App_model->update('users', $userId, array(
                'password' => password_hash($this->input->post('password', true), PASSWORD_BCRYPT),
            ));
        }
        $this->redirect_success('users', 'Password berhasil diperbarui.');
    }
}

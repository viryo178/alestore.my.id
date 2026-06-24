<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expire_durations extends MY_Controller
{
    public function index()
    {
        $this->render('expire_durations/index', array('title' => 'Durasi Expire', 'rows' => $this->App_model->all('expire_durations', 'is_default DESC, days ASC')));
    }

    public function store()
    {
        $this->App_model->insert('expire_durations', $this->post(array('label', 'days', 'is_default')));
        $this->redirect_success('expire-durations', 'Durasi berhasil ditambahkan.');
    }

    public function set_default($id)
    {
        $this->db->update('expire_durations', array('is_default' => 0));
        $this->App_model->update('expire_durations', $id, array('is_default' => 1));
        $this->redirect_success('expire-durations', 'Durasi default diperbarui.');
    }

    public function delete($id)
    {
        $this->App_model->delete('expire_durations', $id);
        $this->redirect_success('expire-durations', 'Durasi berhasil dihapus.');
    }
}

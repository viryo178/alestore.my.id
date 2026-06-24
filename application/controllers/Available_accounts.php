<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Available_accounts extends MY_Controller
{
    public function index()
    {
        $rows = $this->db->where('status', 'available')->order_by('product_name')->get('digital_accounts')->result();
        $this->render('available_accounts/index', array('title' => 'Stok Tersedia', 'rows' => $rows));
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('is_logged_in')) {
            $this->session->set_userdata('redirect_after_login', current_url());
            redirect('login');
        }
    }

    protected function render($view, $data = array())
    {
        $data['content_view'] = $view;
        $this->load->view('layouts/admin', $data);
    }

    protected function redirect_success($uri, $message)
    {
        $this->session->set_flashdata('success', $message);
        redirect($uri);
    }

    protected function post($keys)
    {
        $data = array();
        foreach ($keys as $key) {
            $data[$key] = $this->input->post($key, true);
        }
        return $data;
    }
}

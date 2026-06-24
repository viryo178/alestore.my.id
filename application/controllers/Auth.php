<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function index()
    {
        $this->login();
    }

    public function login()
    {
        if (strtoupper($this->input->method()) === 'POST') {
            $this->attempt();
            return;
        }

        if ($this->session->userdata('is_logged_in')) {
            redirect('admin/dashboard');
        }

        $this->load->view('auth/login', array(
            'title' => 'Login AleStore',
        ));
    }

    public function attempt()
    {
        $email = trim((string) $this->input->post('email', true));
        $password = (string) $this->input->post('password', true);

        if ($email === '' || $password === '') {
            $this->session->set_flashdata('error', 'Email dan password wajib diisi.');
            redirect('login');
        }

        $user = $this->db
            ->where('email', $email)
            ->limit(1)
            ->get('users')
            ->row();

        if (!$user || !password_verify($password, $user->password)) {
            $this->session->set_flashdata('error', 'Email atau password tidak sesuai.');
            redirect('login');
        }

        $this->session->sess_regenerate(true);
        $this->session->set_userdata(array(
            'is_logged_in' => true,
            'user_id' => (int) $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_role' => $user->role,
        ));

        if ($this->db->table_exists('activity_logs')) {
            $this->db->insert('activity_logs', array(
                'user_id' => (int) $user->id,
                'admin_name' => $user->name,
                'admin_role' => $user->role,
                'action' => 'login',
                'description' => 'Login ke dashboard AleStore.',
                'ip_address' => $this->input->ip_address(),
                'created_at' => date('Y-m-d H:i:s'),
            ));
        }

        $redirect = $this->session->userdata('redirect_after_login') ?: 'admin/dashboard';
        $this->session->unset_userdata('redirect_after_login');
        redirect($redirect);
    }

    public function logout()
    {
        $this->session->unset_userdata(array(
            'is_logged_in',
            'user_id',
            'user_name',
            'user_email',
            'user_role',
            'redirect_after_login',
        ));
        $this->session->sess_destroy();

        redirect('login');
    }
}

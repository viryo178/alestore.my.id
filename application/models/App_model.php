<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        if ($this->db && $this->db->dbdriver === 'mysqli') {
            $this->db->query("SET time_zone = '+07:00'");
        }
    }

    public function all($table, $order = 'id DESC', $limit = null)
    {
        if ($order) {
            $this->db->order_by($order);
        }
        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get($table)->result();
    }

    public function find($table, $id)
    {
        return $this->db->where('id', $id)->get($table)->row();
    }

    public function count($table, $where = array())
    {
        if ($where) {
            $this->db->where($where);
        }
        return (int) $this->db->count_all_results($table);
    }

    public function sum($table, $field, $where = array())
    {
        if ($where) {
            $this->db->where($where);
        }
        $row = $this->db->select_sum($field)->get($table)->row();
        return $row ? (float) $row->{$field} : 0;
    }

    public function insert($table, $data)
    {
        if ($this->db->field_exists('created_at', $table) && empty($data['created_at'])) {
            $data['created_at'] = now_sql();
        }
        if ($this->db->field_exists('updated_at', $table) && empty($data['updated_at'])) {
            $data['updated_at'] = now_sql();
        }
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    public function update($table, $id, $data)
    {
        if ($this->db->field_exists('updated_at', $table)) {
            $data['updated_at'] = now_sql();
        }
        return $this->db->where('id', $id)->update($table, $data);
    }

    public function delete($table, $id)
    {
        return $this->db->where('id', $id)->delete($table);
    }

    public function log($action, $description)
    {
        if (!$this->db->table_exists('activity_logs')) {
            return;
        }
        $this->db->insert('activity_logs', array(
            'user_id' => $this->session->userdata('user_id') ?: null,
            'admin_name' => $this->session->userdata('user_name') ?: 'CI Admin',
            'admin_role' => $this->session->userdata('user_role') ?: 'admin',
            'action' => $action,
            'description' => $description,
            'ip_address' => $this->input->ip_address(),
            'created_at' => now_sql(),
        ));
    }
}

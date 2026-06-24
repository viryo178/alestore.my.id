<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Renewal_pipeline extends MY_Controller
{
    public function index()
    {
        $period = $this->input->get('period', true) ?: 'upcoming_30';
        $hasStoreColumn = $this->db->field_exists('shopee_store_id', 'digital_accounts');

        $this->db->from('digital_accounts a')
            ->where('a.expired_at IS NOT NULL', null, false)
            ->where('a.status !=', 'sold');

        if ($hasStoreColumn) {
            $this->db->select('a.*, s.shop_name')
                ->join('shopee_stores s', 's.id = a.shopee_store_id', 'left');
        } else {
            $this->db->select('a.*');
        }

        if ($this->input->get('q')) {
            $q = $this->input->get('q', true);
            $this->db->group_start()
                ->like('a.email', $q)
                ->or_like('a.product_name', $q)
                ->or_like('a.variation', $q)
                ->or_like('a.notes', $q)
                ->group_end();
        }

        if ($hasStoreColumn && $this->input->get('store_id') && $this->input->get('store_id') !== 'all') {
            $this->db->where('a.shopee_store_id', $this->input->get('store_id', true));
        }

        if ($period === 'upcoming_7') {
            $this->db->where('a.expired_at >=', date('Y-m-d 00:00:00'))
                ->where('a.expired_at <=', date('Y-m-d 23:59:59', strtotime('+7 days')));
        } elseif ($period === 'today') {
            $this->db->where('DATE(a.expired_at) =', date('Y-m-d'), false);
        } elseif ($period === 'overdue') {
            $this->db->where('a.expired_at <', date('Y-m-d 00:00:00'));
        } elseif ($period !== 'all') {
            $this->db->where('a.expired_at >=', date('Y-m-d 00:00:00'))
                ->where('a.expired_at <=', date('Y-m-d 23:59:59', strtotime('+30 days')));
        }

        $rows = $this->db->order_by('a.expired_at', 'ASC')->limit(500)->get()->result();

        $upcomingCount = $this->db
            ->where('expired_at IS NOT NULL', null, false)
            ->where('expired_at >=', date('Y-m-d 00:00:00'))
            ->where('expired_at <=', date('Y-m-d 23:59:59', strtotime('+30 days')))
            ->where('status !=', 'sold')
            ->count_all_results('digital_accounts');

        $overdueCount = $this->db
            ->where('expired_at IS NOT NULL', null, false)
            ->where('expired_at <', date('Y-m-d 00:00:00'))
            ->where('status !=', 'sold')
            ->count_all_results('digital_accounts');

        $this->render('renewal_pipeline/index', array(
            'title' => 'Pengingat Expired',
            'rows' => $rows,
            'stores' => $this->db->table_exists('shopee_stores') ? $this->App_model->all('shopee_stores', 'shop_name ASC') : array(),
            'period' => $period,
            'upcoming_count' => $upcomingCount,
            'overdue_count' => $overdueCount,
            'has_store_column' => $hasStoreColumn,
        ));
    }
}

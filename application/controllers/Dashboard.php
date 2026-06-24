<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
    public function index()
    {
        $today = date('Y-m-d');
        $total_accounts = max(1, $this->App_model->count('digital_accounts'));
        $range = $this->input->get('range', true) ?: '7';

        $chartLabels = array();
        $chartOrders = array();
        $chartRevenue = array();
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime('-'.$i.' days'));
            $chartLabels[] = date('d M', strtotime($date));
            $chartOrders[] = (int) $this->db->where('DATE(created_at) =', $date, false)->count_all_results('orders');
            $revenueRow = $this->db->select_sum('total')->where('DATE(created_at) =', $date, false)->get('orders')->row();
            $chartRevenue[] = $revenueRow ? (float) $revenueRow->total : 0;
        }

        $today_revenue_row = $this->db->select_sum('total')
            ->where('DATE(created_at) =', $today, false)
            ->get('orders')
            ->row();

        $data = array(
            'title' => 'Dashboard',
            'customers_count' => $this->App_model->count('customers'),
            'orders_count' => $this->App_model->count('orders'),
            'active_stores_count' => $this->App_model->count('shopee_stores', array('status' => 'active')),
            'today_orders_count' => $this->db->like('created_at', $today, 'after')->count_all_results('orders'),
            'today_revenue' => $today_revenue_row ? (float) $today_revenue_row->total : 0,
            'verified_count' => $this->App_model->count('digital_accounts', array('status' => 'verified')),
            'available_count' => $this->App_model->count('digital_accounts', array('status' => 'available')),
            'deactived_count' => $this->App_model->count('digital_accounts', array('status' => 'deactived')),
            'sold_count' => (int) $this->db->where_in('status', array('sold', 'unavailable', 'unvailabel'))->count_all_results('digital_accounts'),
            'sharing_count' => $this->App_model->count('digital_accounts', array('account_type' => 'sharing')),
            'private_count' => $this->App_model->count('digital_accounts', array('account_type' => 'private')),
            'full_slot_count' => (int) $this->db->where('account_type', 'sharing')->where('used_slot >= max_slot', null, false)->count_all_results('digital_accounts'),
            'total_accounts' => $total_accounts,
            'available_accounts' => $this->db->where('status', 'available')->order_by('id', 'DESC')->limit(8)->get('digital_accounts')->result(),
            'top_products' => $this->db->select('product_name, COUNT(*) total')->group_by('product_name')->order_by('total', 'DESC')->limit(5)->get('digital_accounts')->result(),
            'low_stock_products' => $this->db->select("product_name, variation, account_type, SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) available_total, COUNT(*) total", false)
                ->from('digital_accounts')
                ->group_by(array('product_name', 'variation', 'account_type'))
                ->having('available_total <=', 3)
                ->order_by('available_total', 'ASC')
                ->order_by('total', 'DESC')
                ->limit(6)
                ->get()
                ->result(),
            'chart_labels' => $chartLabels,
            'chart_orders' => $chartOrders,
            'chart_revenue' => $chartRevenue,
            'range' => $range,
            'store_stats' => $this->db->select("s.*, COUNT(o.id) total_orders, COALESCE(SUM(o.total),0) revenue, SUM(CASE WHEN DATE(o.created_at) = '".$today."' THEN 1 ELSE 0 END) today_orders, COALESCE(SUM(CASE WHEN DATE(o.created_at) = '".$today."' THEN o.total ELSE 0 END),0) today_revenue", false)
                ->from('shopee_stores s')->join('orders o', 'o.shopee_store_id = s.id', 'left')
                ->group_by('s.id')->order_by('s.shop_name')->get()->result(),
        );
        $this->render('dashboard/index', $data);
    }
}

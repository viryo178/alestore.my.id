<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Financial_reports extends MY_Controller
{
    public function index()
    {
        list($from, $to) = $this->date_range();
        $storeId = $this->input->get('store_id', true) ?: 'all';
        $ads = (float) ($this->input->get('ads_cost', true) ?: 0);
        $extraCost = (float) ($this->input->get('extra_cost', true) ?: 0);

        $this->db->where('created_at >=', $from.' 00:00:00')->where('created_at <=', $to.' 23:59:59');
        if ($storeId !== 'all' && $storeId !== '') {
            $this->db->where('shopee_store_id', $storeId);
        }
        $orders = $this->db->get('orders')->result();

        $gross = 0;
        foreach ($orders as $order) {
            $gross += (float) $order->total;
        }

        $fee = $gross * 0.085;
        $refund = 0;
        $cogsRow = $this->db->select_sum('hpp')->where_in('status', array('sold', 'unavailable', 'unvailabel'))->get('digital_accounts')->row();
        $cogs = $cogsRow ? (float) $cogsRow->hpp : 0;
        $net = $gross - $fee - $refund;
        $profit = $net - $cogs - $ads - $extraCost;

        $profitRows = $this->db
            ->select('product_name, COUNT(*) qty, SUM(hpp) cogs')
            ->where_in('status', array('sold', 'unavailable', 'unvailabel'))
            ->group_by('product_name')
            ->order_by('qty', 'DESC')
            ->get('digital_accounts')
            ->result();

        $profitProducts = array();
        foreach ($profitRows as $item) {
            $productGross = ((int) $item->qty) * 20000;
            $productFee = $productGross * 0.085;
            $productNet = $productGross - $productFee;
            $productCogs = (float) $item->cogs;
            $productProfit = $productNet - $productCogs;

            $profitProducts[] = array(
                'product' => $item->product_name,
                'qty' => (int) $item->qty,
                'gross' => $productGross,
                'fee' => $productFee,
                'net' => $productNet,
                'cogs' => $productCogs,
                'profit' => $productProfit,
                'margin' => $productGross > 0 ? ($productProfit / $productGross) * 100 : 0,
            );
        }

        $this->render('financial_reports/index', array(
            'title' => 'Laporan Keuangan',
            'from' => $from,
            'to' => $to,
            'store_id' => $storeId,
            'stores' => $this->App_model->all('shopee_stores', 'shop_name ASC'),
            'summary' => array(
                'transactions' => count($orders),
                'gross' => $gross,
                'fee' => $fee,
                'extra_cost' => $extraCost,
                'refund' => $refund,
                'net' => $net,
                'ads' => $ads,
                'profit' => $profit,
            ),
            'profit_products' => $profitProducts,
        ));
    }

    public function download()
    {
        list($from, $to) = $this->date_range();
        $storeId = $this->input->get('store_id', true) ?: 'all';
        $this->db->select('o.*, s.shop_name, s.platform, s.admin_fee_percentage')
            ->from('orders o')
            ->join('shopee_stores s', 's.id = o.shopee_store_id', 'left')
            ->where('o.created_at >=', $from.' 00:00:00')
            ->where('o.created_at <=', $to.' 23:59:59');
        if ($storeId !== 'all' && $storeId !== '') {
            $this->db->where('o.shopee_store_id', $storeId);
        }
        $orders = $this->db->order_by('o.created_at', 'DESC')->get()->result();

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="laporan_'.$from.'_'.$to.'.csv"');

        $out = fopen('php://output', 'w');
        fputs($out, "\xEF\xBB\xBF");
        fputcsv($out, array(
            'No Pesanan Marketplace',
            'No Order Internal',
            'Tgl Order',
            'Toko',
            'Marketplace',
            'Fee (%)',
            'Pembeli',
            'Produk',
            'Variasi',
            'Tipe Akun',
            'Harga Jual (Rp)',
            'Fee Admin (Rp)',
            'Net Revenue (Rp)',
            'COGS (Rp)',
            'Extra Cost Klaim (Rp)',
            'Refund Amount (Rp)',
            'Refund Fee (Rp)',
            'Laba Kotor (Rp)',
            'Renewal Status',
            'Tgl Expired',
            'Admin',
            'Catatan',
        ));
        foreach ($orders as $order) {
            $gross = (float) $order->total;
            $fee = round($gross * 0.065);
            $net = $gross - $fee;
            fputcsv($out, array(
                $order->shopee_order_id,
                'ORD-'.strtoupper(dechex($order->id)).date('His'),
                date('d/m/Y', strtotime($order->created_at)),
                $order->shop_name ?: '-',
                $order->platform ?: 'shopee',
                '6.5%',
                'Buyer '.$order->shopee_order_id,
                $order->product_name,
                $order->variation,
                $order->order_type,
                number_format($gross, 2, '.', ''),
                $fee,
                $net,
                0,
                0,
                0,
                0,
                $net,
                $order->status,
                $order->expired_at,
                'Admin',
                '',
            ));
        }
        fclose($out);
        exit;
    }

    private function date_range()
    {
        $quick = $this->input->get('quick', true);

        if ($quick === 'week') {
            return array(date('Y-m-d', strtotime('monday this week')), date('Y-m-d'));
        }

        if ($quick === 'month') {
            return array(date('Y-m-01'), date('Y-m-d'));
        }

        if ($quick === 'year') {
            return array(date('Y-01-01'), date('Y-m-d'));
        }

        return array(
            $this->input->get('from', true) ?: date('Y-m-01'),
            $this->input->get('to', true) ?: date('Y-m-d'),
        );
    }
}

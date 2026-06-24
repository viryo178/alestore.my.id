<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warranty_claims extends MY_Controller
{
    public function index()
    {
        $status = $this->input->get('status', true) ?: 'all';

        $this->db->select('w.*, s.shop_name')
            ->from('warranty_claims w')
            ->join('shopee_stores s', 's.id = w.shopee_store_id', 'left');

        if (in_array($status, array('pending', 'approved', 'rejected'), true)) {
            $this->db->where('w.status', $status);
        }

        if ($this->input->get('q')) {
            $q = $this->input->get('q', true);
            $this->db->group_start()
                ->like('w.order_code', $q)
                ->or_like('w.buyer_name', $q)
                ->or_like('w.reason', $q)
                ->or_like('w.notes', $q)
                ->group_end();
        }

        $rows = $this->db->order_by('w.claimed_at', 'DESC')->order_by('w.id', 'DESC')->limit(1000)->get()->result();

        $this->render('warranty_claims/index', array(
            'title' => 'Klaim Garansi',
            'rows' => $rows,
            'stores' => $this->App_model->all('shopee_stores', 'shop_name ASC'),
            'status' => $status,
            'pending_count' => $this->db->where('status', 'pending')->count_all_results('warranty_claims'),
            'approved_count' => $this->db->where('status', 'approved')->count_all_results('warranty_claims'),
            'rejected_count' => $this->db->where('status', 'rejected')->count_all_results('warranty_claims'),
        ));
    }

    public function store()
    {
        $data = $this->post(array('order_id', 'shopee_store_id', 'order_code', 'buyer_name', 'reason', 'extra_cost', 'notes'));
        if (empty($data['order_id']) && !empty($data['order_code'])) {
            $order = $this->db->where('shopee_order_id', $data['order_code'])->limit(1)->get('orders')->row();
            if ($order) {
                $data['order_id'] = $order->id;
                $data['shopee_store_id'] = $data['shopee_store_id'] ?: $order->shopee_store_id;
                $data['buyer_name'] = $data['buyer_name'] ?: $order->buyer_email;
            }
        }
        $data['status'] = 'pending';
        $data['claimed_at'] = now_sql();
        $this->App_model->insert('warranty_claims', $data);
        $this->redirect_success('warranty-claims', 'Klaim garansi berhasil ditambahkan.');
    }

    public function approve($id)
    {
        $claim = $this->App_model->find('warranty_claims', $id);
        if (!$claim) {
            $this->session->set_flashdata('error', 'Klaim tidak ditemukan.');
            redirect('warranty-claims');
        }

        $order = $this->find_order_for_claim($claim);
        if (!$order) {
            $this->session->set_flashdata('error', 'Order untuk klaim ini tidak ditemukan.');
            redirect('warranty-claims');
        }

        $oldAccount = !empty($order->assigned_digital_account_id)
            ? $this->App_model->find('digital_accounts', $order->assigned_digital_account_id)
            : null;
        $replacement = $this->find_replacement_account($order, $oldAccount);
        if (!$replacement) {
            $this->session->set_flashdata('error', 'Tidak ada akun ready untuk pengganti klaim ini.');
            redirect('warranty-claims');
        }

        $this->db->trans_start();

        if ($oldAccount) {
            $this->db->where('id', $oldAccount->id)->update('digital_accounts', array(
                'status' => $this->problem_status_from_reason($claim->reason),
            ));
        }

        $maxSlot = $order->order_type === 'sharing' ? max(1, (int) $replacement->max_slot) : 1;
        $usedSlot = (int) $replacement->used_slot + 1;
        $this->db->where('id', $replacement->id)->update('digital_accounts', array(
            'used_slot' => $usedSlot,
            'status' => $usedSlot >= $maxSlot ? 'sold' : 'available',
            'sold_at' => now_sql(),
        ));

        $this->db->where('id', $order->id)->update('orders', array(
            'assigned_digital_account_id' => (int) $replacement->id,
            'account_username' => $replacement->email,
            'account_password' => $replacement->password,
            'account_max_user' => $maxSlot,
            'updated_at' => now_sql(),
        ));

        $notes = trim((string) $claim->notes);
        $notes .= ($notes !== '' ? "\n" : '').'Auto replace akun: '.($oldAccount ? $oldAccount->email : '-').' -> '.$replacement->email;
        $this->App_model->update('warranty_claims', $id, array(
            'status' => 'approved',
            'notes' => $notes,
        ));

        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            $this->session->set_flashdata('error', 'Gagal memproses penggantian akun.');
            redirect('warranty-claims');
        }

        $this->redirect_success('warranty-claims', 'Klaim disetujui dan akun pengganti berhasil diberikan.');
    }

    public function reject($id)
    {
        $this->App_model->update('warranty_claims', $id, array('status' => 'rejected'));
        $this->redirect_success('warranty-claims', 'Klaim ditolak.');
    }

    private function find_order_for_claim($claim)
    {
        if (!empty($claim->order_id)) {
            $order = $this->App_model->find('orders', $claim->order_id);
            if ($order) {
                return $order;
            }
        }

        if (!empty($claim->order_code)) {
            return $this->db->where('shopee_order_id', $claim->order_code)->limit(1)->get('orders')->row();
        }

        return null;
    }

    private function find_replacement_account($order, $oldAccount = null)
    {
        $orderType = $order->order_type === 'sharing' ? 'sharing' : 'private';
        $this->db->from('digital_accounts')
            ->where('account_type', $orderType)
            ->where('status', 'available')
            ->group_start()
                ->where('digital_product_id IN (SELECT id FROM digital_products WHERE name = '.$this->db->escape($order->product_name).')', null, false)
                ->or_where('product_name', $order->product_name)
            ->group_end();

        if (!empty($order->variation)) {
            $this->db->group_start()
                ->where('digital_product_variation_id IN (SELECT id FROM digital_product_variations WHERE label = '.$this->db->escape($order->variation).')', null, false)
                ->or_where('variation', $order->variation)
            ->group_end();
        }

        if ($oldAccount) {
            $this->db->where('id !=', $oldAccount->id);
        }

        if ($orderType === 'sharing') {
            $this->db->where('used_slot < max_slot', null, false);
        } else {
            $this->db->where('used_slot <', 1);
        }

        return $this->db->order_by('id', 'ASC')->limit(1)->get()->row();
    }

    private function problem_status_from_reason($reason)
    {
        $reason = strtolower((string) $reason);
        if (strpos($reason, 'ban') !== false || strpos($reason, 'deact') !== false || strpos($reason, 'nonaktif') !== false) {
            return 'deactived';
        }
        if (strpos($reason, 'umur') !== false || strpos($reason, 'age') !== false || strpos($reason, 'aktif') !== false) {
            return 'active_age';
        }
        return 'no_access';
    }
}

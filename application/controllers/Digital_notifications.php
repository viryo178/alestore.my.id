<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Digital_notifications extends MY_Controller
{
    public function index()
    {
        $statusLabels = array(
            'deactived' => 'Deactived',
            'no_access' => 'No Access',
            'active_age' => 'Umur Aktif',
            'verified' => 'Verif',
        );
        $attentionStatuses = array_keys($statusLabels);
        $type = $this->input->get('type', true) ?: 'all';
        $hasResolvedColumn = $this->db->field_exists('notification_resolved_at', 'digital_accounts');

        $this->db->from('digital_accounts');
        if ($hasResolvedColumn) {
            $this->db->where('notification_resolved_at IS NULL', null, false);
        }

        $this->db->group_start()
            ->where_in('status', $attentionStatuses)
            ->or_group_start()
                ->where('expired_at IS NOT NULL', null, false)
                ->where('expired_at >=', date('Y-m-d H:i:s'))
                ->where('expired_at <=', date('Y-m-d H:i:s', strtotime('+2 days')))
                ->where_not_in('status', array('sold', 'deactived', 'no_access'))
            ->group_end()
        ->group_end();

        if ($type !== 'all') {
            if ($type === 'expired') {
                $this->db
                    ->where('expired_at IS NOT NULL', null, false)
                    ->where('expired_at >=', date('Y-m-d H:i:s'))
                    ->where('expired_at <=', date('Y-m-d H:i:s', strtotime('+2 days')))
                    ->where_not_in('status', array('sold', 'deactived', 'no_access'));
            } elseif (isset($statusLabels[$type])) {
                $this->db->where('status', $type);
            }
        }

        $notifications = $this->db->order_by('id', 'DESC')->get()->result();

        $expiredCount = $this->expired_soon_count($hasResolvedColumn);
        $statusCounts = array();
        foreach ($attentionStatuses as $status) {
            $this->db->where('status', $status);
            if ($hasResolvedColumn) {
                $this->db->where('notification_resolved_at IS NULL', null, false);
            }
            $statusCounts[$status] = $this->db->count_all_results('digital_accounts');
        }

        $this->render('digital_notifications/index', array(
            'title' => 'Kelola Notifikasi',
            'notifications' => $notifications,
            'status_labels' => $statusLabels,
            'expired_count' => $expiredCount,
            'status_counts' => $statusCounts,
            'type' => $type,
        ));
    }

    private function expired_soon_count($hasResolvedColumn)
    {
        $this->db
            ->where('expired_at IS NOT NULL', null, false)
            ->where('expired_at >=', date('Y-m-d H:i:s'))
            ->where('expired_at <=', date('Y-m-d H:i:s', strtotime('+2 days')))
            ->where_not_in('status', array('sold', 'deactived', 'no_access'));

        if ($hasResolvedColumn) {
            $this->db->where('notification_resolved_at IS NULL', null, false);
        }

        return (int) $this->db->count_all_results('digital_accounts');
    }
}

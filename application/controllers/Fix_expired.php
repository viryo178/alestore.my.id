<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fix_expired extends CI_Controller {

    public function index() {
        $this->db->select('id, variation, sold_at, expired_at');
        $query = $this->db->get('digital_accounts');
        
        $updated = 0;
        
        foreach ($query->result() as $row) {
            $variation = $row->variation;
            $soldAt = $row->sold_at;
            $currentExpiredAt = $row->expired_at;
            
            if (empty($variation) || empty($soldAt) || $soldAt === '0000-00-00 00:00:00') {
                continue;
            }

            if (preg_match('/(\d+)\s*(bulan|month|hari|day|tahun|year)/i', $variation, $matches)) {
                $num = (int)$matches[1];
                $unit = strtolower($matches[2]);
                
                $date = new DateTime($soldAt);
                
                if (strpos($unit, 'bulan') === 0 || strpos($unit, 'month') === 0) {
                    $date->modify("+$num months");
                } elseif (strpos($unit, 'hari') === 0 || strpos($unit, 'day') === 0) {
                    $date->modify("+$num days");
                } elseif (strpos($unit, 'tahun') === 0 || strpos($unit, 'year') === 0) {
                    $date->modify("+$num years");
                }
                
                $newExpiredAt = $date->format('Y-m-d');
                
                if ($currentExpiredAt !== $newExpiredAt) {
                    $this->db->where('id', $row->id);
                    $this->db->update('digital_accounts', ['expired_at' => $newExpiredAt]);
                    $updated++;
                }
            }
        }
        
        echo "<h1>Selesai!</h1>";
        echo "<p>Berhasil memperbarui / mengisi tanggal expired untuk <b>$updated</b> akun lama di database.</p>";
        echo "<p>Silakan cek kembali halaman Expired Akun Anda.</p>";
    }
}

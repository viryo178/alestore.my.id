<?php
$money = function ($value) {
    return (float) $value > 0 ? rupiah($value) : '-';
};
$statusMeta = array(
    'pending' => array('label' => 'Menunggu', 'class' => 'claim-pending', 'icon' => 'hourglass-split'),
    'approved' => array('label' => 'Disetujui', 'class' => 'claim-approved', 'icon' => 'check-square-fill'),
    'rejected' => array('label' => 'Ditolak', 'class' => 'claim-rejected', 'icon' => 'x-lg'),
);
?>

<style>
    .warranty-page .warranty-header{align-items:center;display:flex;flex-wrap:wrap;gap:14px;justify-content:space-between;margin-bottom:18px}
    .warranty-page .warranty-title-wrap{align-items:center;display:flex;gap:12px}
    .warranty-page .warranty-icon{align-items:center;background:rgba(47,124,255,.16);border:1px solid rgba(47,124,255,.28);border-radius:10px;color:#7fb0ff;display:inline-flex;height:40px;justify-content:center;width:40px}
    .warranty-page .warranty-title{color:#fff;font-size:20px;font-weight:800;margin:0}
    .warranty-page .pending-pill{background:rgba(255,173,0,.15);border-radius:999px;color:#ffad00;display:inline-flex;font-size:13px;font-weight:800;gap:7px;padding:8px 13px}
    .warranty-page .claim-tabs{display:flex;flex-wrap:wrap;gap:9px;margin-bottom:18px}
    .warranty-page .claim-tabs .btn{min-width:92px}
    .warranty-page .warranty-table thead th{font-size:11px;letter-spacing:.04em;text-transform:uppercase;white-space:nowrap}
    .warranty-page .order-code{color:#5da2ff;font-size:12px;font-weight:750}
    .warranty-page .main-text{color:#dce8ff;font-weight:650}
    .warranty-page .subtext{color:#6f8fbd;display:block;font-size:11px;margin-top:2px}
    .warranty-page .cost-text{color:#ff7a00;font-weight:800;white-space:nowrap}
    .warranty-page .claim-badge{border-radius:999px;display:inline-flex;font-size:11px;font-weight:800;gap:5px;padding:5px 9px;white-space:nowrap}
    .warranty-page .claim-approved{background:rgba(0,168,116,.16);color:#00d18f}
    .warranty-page .claim-pending{background:rgba(255,173,0,.15);color:#ffad00}
    .warranty-page .claim-rejected{background:rgba(217,54,89,.15);color:#ff6078}
    .warranty-page .warranty-actions{align-items:center;display:flex;flex-wrap:wrap;gap:7px;justify-content:flex-end}
    .warranty-page .modal-detail-grid{display:grid;gap:12px;grid-template-columns:repeat(2,minmax(0,1fr))}
    .warranty-page .detail-box{background:rgba(47,124,255,.08);border:1px solid rgba(47,124,255,.14);border-radius:8px;padding:12px}
    .warranty-page .warranty-search{max-width:320px}
    @media (max-width:767.98px){.warranty-page .modal-detail-grid{grid-template-columns:1fr}.warranty-page .warranty-actions{justify-content:flex-start}.warranty-page .warranty-search{max-width:100%}}
</style>

<section class="section warranty-page">
    <div class="warranty-header">
        <div class="warranty-title-wrap">
            <span class="warranty-icon"><i class="bi bi-shield-check"></i></span>
            <div>
                <h1 class="warranty-title">Klaim Garansi</h1>
                <p class="text-muted mb-0">Kelola semua permintaan garansi dari pembeli.</p>
            </div>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="pending-pill"><i class="bi bi-hourglass-split"></i> <?= number_format($pending_count); ?> klaim menunggu</span>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createClaimModal">
                <i class="bi bi-plus-circle"></i> Tambah Klaim
            </button>
        </div>
    </div>

    <div class="claim-tabs">
        <a href="<?= site_url('warranty-claims'); ?>" class="btn btn-sm <?= $status === 'all' ? 'btn-primary' : 'btn-outline-primary'; ?>">Semua</a>
        <a href="<?= site_url('warranty-claims?status=pending'); ?>" class="btn btn-sm <?= $status === 'pending' ? 'btn-primary' : 'btn-outline-primary'; ?>"><i class="bi bi-hourglass-split"></i> Pending</a>
        <a href="<?= site_url('warranty-claims?status=approved'); ?>" class="btn btn-sm <?= $status === 'approved' ? 'btn-primary' : 'btn-outline-primary'; ?>"><i class="bi bi-check-square-fill"></i> Approved</a>
        <a href="<?= site_url('warranty-claims?status=rejected'); ?>" class="btn btn-sm <?= $status === 'rejected' ? 'btn-primary' : 'btn-outline-primary'; ?>"><i class="bi bi-x-lg"></i> Ditolak</a>
    </div>

    <div class="card datatable-card">
        <div class="card-body">
            <form method="GET" class="d-flex flex-wrap justify-content-end gap-2 mb-3">
                <input type="hidden" name="status" value="<?= h($status); ?>">
                <input type="text" name="q" class="form-control warranty-search" value="<?= h($this->input->get('q')); ?>" placeholder="Cari order, pembeli, alasan...">
                <button class="btn btn-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="<?= site_url('warranty-claims'.($status !== 'all' ? '?status='.$status : '')); ?>" class="btn btn-outline-secondary">Reset</a>
            </form>

            <div class="table-responsive">
                <table class="table datatable align-middle warranty-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Pembeli</th>
                            <th>Toko</th>
                            <th>Alasan</th>
                            <th>Extra Cost</th>
                            <th>Status</th>
                            <th>Tgl Klaim</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($rows): foreach ($rows as $row): ?>
                        <?php
                        $meta = isset($statusMeta[$row->status]) ? $statusMeta[$row->status] : $statusMeta['pending'];
                        $claimedAt = !empty($row->claimed_at) ? strtotime($row->claimed_at) : null;
                        ?>
                        <tr>
                            <td>
                                <div class="order-code"><?= h($row->order_code ?: 'CLM-'.$row->id); ?></div>
                                <span class="subtext">Internal: CLM-<?= strtoupper(dechex($row->id)); ?></span>
                            </td>
                            <td><span class="main-text"><?= h($row->buyer_name); ?></span></td>
                            <td><span class="text-muted"><?= h($row->shop_name ?: '-'); ?></span></td>
                            <td><?= h($row->reason); ?></td>
                            <td><span class="cost-text"><?= $money($row->extra_cost); ?></span></td>
                            <td><span class="claim-badge <?= h($meta['class']); ?>"><i class="bi bi-<?= h($meta['icon']); ?>"></i> <?= h($meta['label']); ?></span></td>
                            <td>
                                <span class="text-muted"><?= $claimedAt ? date('d M Y', $claimedAt) : '-'; ?></span>
                                <span class="subtext"><?= $claimedAt ? date('H:i', $claimedAt) : ''; ?></span>
                            </td>
                            <td class="text-end">
                                <div class="warranty-actions">
                                    <?php if ($row->status === 'pending'): ?>
                                        <a class="btn btn-sm btn-outline-success" href="<?= site_url('warranty-claims/'.$row->id.'/approve'); ?>" onclick="return confirm('Setujui klaim ini?');"><i class="bi bi-check-lg"></i> Approve</a>
                                        <a class="btn btn-sm btn-outline-danger" href="<?= site_url('warranty-claims/'.$row->id.'/reject'); ?>" onclick="return confirm('Tolak klaim ini?');"><i class="bi bi-x-lg"></i> Tolak</a>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#claimDetailModal<?= $row->id; ?>"><i class="bi bi-eye"></i> Detail</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada klaim garansi.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createClaimModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form action="<?= site_url('warranty-claims/store'); ?>" method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Klaim Garansi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Order</label>
                            <input type="text" name="order_code" class="form-control" placeholder="No order marketplace">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pembeli *</label>
                            <input type="text" name="buyer_name" class="form-control" placeholder="Buyer / nama pembeli" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Toko</label>
                            <select name="shopee_store_id" class="form-select">
                                <option value="">Pilih toko</option>
                                <?php foreach ($stores as $store): ?><option value="<?= $store->id; ?>"><?= h($store->shop_name); ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Extra Cost</label>
                            <input type="number" name="extra_cost" class="form-control" value="0" min="0" step="100">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alasan *</label>
                            <input type="text" name="reason" class="form-control" placeholder="akun tidak bisa masuk, akun limit, dll" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Detail tambahan"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan Klaim</button>
                </div>
            </form>
        </div>
    </div>

    <?php foreach ($rows as $row): ?>
        <?php
        $meta = isset($statusMeta[$row->status]) ? $statusMeta[$row->status] : $statusMeta['pending'];
        $claimedAt = !empty($row->claimed_at) ? strtotime($row->claimed_at) : null;
        ?>
        <div class="modal fade" id="claimDetailModal<?= $row->id; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Klaim Garansi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-detail-grid">
                            <div class="detail-box"><span class="subtext">Order</span><div class="main-text"><?= h($row->order_code ?: 'CLM-'.$row->id); ?></div></div>
                            <div class="detail-box"><span class="subtext">Pembeli</span><div class="main-text"><?= h($row->buyer_name); ?></div></div>
                            <div class="detail-box"><span class="subtext">Toko</span><div class="main-text"><?= h($row->shop_name ?: '-'); ?></div></div>
                            <div class="detail-box"><span class="subtext">Status</span><span class="claim-badge <?= h($meta['class']); ?>"><i class="bi bi-<?= h($meta['icon']); ?>"></i> <?= h($meta['label']); ?></span></div>
                            <div class="detail-box"><span class="subtext">Extra Cost</span><div class="cost-text"><?= $money($row->extra_cost); ?></div></div>
                            <div class="detail-box"><span class="subtext">Tanggal Klaim</span><div class="main-text"><?= $claimedAt ? date('d M Y H:i', $claimedAt) : '-'; ?></div></div>
                        </div>
                        <div class="detail-box mt-3"><span class="subtext">Alasan</span><div class="main-text"><?= h($row->reason); ?></div></div>
                        <?php if (!empty($row->notes)): ?><div class="detail-box mt-3"><span class="subtext">Catatan</span><div class="main-text"><?= h($row->notes); ?></div></div><?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</section>

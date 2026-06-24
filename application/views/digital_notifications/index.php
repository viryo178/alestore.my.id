<style>
    .notification-inbox .card-body{padding:22px 28px}
    .notification-inbox .list-group{background:transparent;gap:12px}
    .notification-inbox .list-group-item{align-items:center;background:#07182b!important;border:1px solid rgba(29,63,102,.9)!important;border-radius:8px!important;color:#f8fbff!important;margin-bottom:12px;padding:16px 18px!important}
    .notification-inbox .list-group-item:hover{background:#0b213c!important;border-color:#2f83ff!important}
    .notification-inbox .list-group-item strong,
    .notification-inbox .list-group-item small,
    .notification-inbox .list-group-item div{color:#f8fbff}
    .notification-inbox .list-group-item .text-muted{color:#9bb4d6!important}
    .notification-avatar{align-items:center;background:#1f55b5;border:1px solid #2f6ed9;border-radius:50%;box-shadow:0 0 16px rgba(47,110,217,.24);color:#fff;display:flex;flex:0 0 46px;font-weight:800;height:46px;justify-content:center;width:46px}
    .notification-filter{min-width:230px}
    .notification-meta-row{align-items:flex-start;display:flex;gap:12px;justify-content:space-between}
    .notification-badges{display:flex;flex-wrap:wrap;gap:6px;margin-top:6px}
    @media (max-width:767.98px){.notification-inbox .card-body{padding:18px}.notification-meta-row{flex-direction:column;gap:4px}.notification-filter{min-width:100%}}
</style>

<div class="pagetitle">
    <h1>Kelola Notifikasi</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard'); ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Notifikasi</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card notification-inbox">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h5 class="card-title mb-0">Inbox Akun Digital</h5>
                <form method="GET" class="d-flex flex-wrap gap-2">
                    <select name="type" class="form-select notification-filter">
                        <option value="all">Semua</option>
                        <option value="expired" <?= $type === 'expired' ? 'selected' : ''; ?>>Password Expired</option>
                        <?php foreach ($status_labels as $status => $label): ?>
                            <option value="<?= h($status); ?>" <?= $type === $status ? 'selected' : ''; ?>><?= h($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary" type="submit">Filter</button>
                </form>
            </div>

            <div class="list-group list-group-flush">
                <?php if ($notifications): foreach ($notifications as $account): ?>
                    <?php
                    $expiredTs = !empty($account->expired_at) ? strtotime($account->expired_at) : null;
                    $isExpiredSoon = $expiredTs && $expiredTs >= time() && $expiredTs <= strtotime('+2 days');
                    $initial = strtoupper(substr((string) ($account->product_name ?: 'A'), 0, 1));
                    $timeSource = $expiredTs ?: (!empty($account->updated_at) ? strtotime($account->updated_at) : time());
                    ?>
                    <a href="<?= site_url('digital-accounts?edit='.$account->id.'&notify=1'); ?>" class="list-group-item list-group-item-action d-flex gap-3 py-3">
                        <div class="notification-avatar"><?= h($initial); ?></div>
                        <div class="flex-grow-1">
                            <div class="notification-meta-row">
                                <strong><?= h($account->product_name); ?><?= $account->variation ? ' - '.h($account->variation) : ''; ?></strong>
                                <small class="text-muted"><?= date('H:i', $timeSource); ?></small>
                            </div>
                            <div class="text-muted small"><?= h($account->email ?: 'Akun belum diisi email'); ?></div>
                            <div class="small notification-badges">
                                <?php if ($isExpiredSoon): ?>
                                    <span class="badge bg-warning text-dark">Password expired <?= date('d/m/Y', $expiredTs); ?></span>
                                <?php endif; ?>
                                <?php if (isset($status_labels[$account->status])): ?>
                                    <span class="badge bg-danger"><?= h($status_labels[$account->status]); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; else: ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-bell-slash" style="font-size:2rem;"></i>
                        <p class="mb-0 mt-2">Tidak ada notifikasi.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

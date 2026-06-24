<?php
$statusLabels = array(
    'available' => 'Tersedia',
    'verified' => 'Verif',
    'active_age' => 'Umur Aktif',
    'sold' => 'Sold',
    'no_access' => 'No Access',
    'deactived' => 'Deactived',
);
$statusBadgeClasses = array(
    'available' => 'bg-success',
    'sold' => 'bg-danger',
    'no_access' => 'bg-danger',
    'deactived' => 'bg-danger',
    'verified' => 'bg-primary',
    'active_age' => 'bg-warning',
);
if (!function_exists('password_exp_status_key')) {
    function password_exp_status_key($status)
    {
        return in_array($status, array('unavailable', 'unvailabel'), true) ? 'sold' : $status;
    }
}
$urgentCount = 0;
foreach ($rows as $account) {
    if (!empty($account->expired_at) && strtotime($account->expired_at) < strtotime('-7 days')) {
        $urgentCount++;
    }
}
?>

<style>
    .password-exp-page .summary-grid{display:grid;gap:14px;grid-template-columns:repeat(3,minmax(0,1fr));margin-bottom:18px}
    .password-exp-page .summary-item{background:rgba(47,110,217,.09);border:1px solid rgba(47,110,217,.18);border-radius:8px;padding:14px 16px}
    .password-exp-page .summary-item strong{display:block;font-size:24px;line-height:1.2}
    .password-exp-page .summary-item span{color:#8da0bd;font-size:12px;text-transform:uppercase}
    .password-exp-page .toolbar{align-items:flex-end;display:flex;flex-wrap:wrap;gap:12px;justify-content:space-between;margin-bottom:16px}
    .password-exp-page .filter-group,.password-exp-page .action-group{align-items:flex-end;display:flex;flex-wrap:wrap;gap:10px}
    .password-exp-page .filter-control{min-width:190px}
    .password-exp-page .password-chip{background:rgba(6,20,38,.72);border:1px solid rgba(141,160,189,.18);border-radius:6px;color:#e5ecf8;display:inline-block;font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;max-width:220px;overflow:hidden;padding:5px 8px;text-overflow:ellipsis;vertical-align:middle;white-space:nowrap}
    .password-exp-page .expired-date{color:#e04e6c;font-weight:700;white-space:nowrap}
    .password-exp-page .row-subtext{color:#8da0bd;display:block;font-size:12px;margin-top:2px}
    .password-exp-page .table-actions{display:flex;gap:6px;justify-content:flex-end}
    @media (max-width: 767.98px){
        .password-exp-page .summary-grid{grid-template-columns:1fr}
        .password-exp-page .toolbar,.password-exp-page .filter-group,.password-exp-page .action-group{align-items:stretch;flex-direction:column}
        .password-exp-page .filter-control{min-width:100%}
    }
</style>

<div class="pagetitle">
    <h1>Ganti Password Exp</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard'); ?>">Dashboard</a></li><li class="breadcrumb-item"><a href="<?= site_url('digital-accounts'); ?>">Management Akun Digital</a></li><li class="breadcrumb-item active">Ganti Password Exp</li></ol></nav>
</div>

<section class="section password-exp-page">
    <div class="summary-grid">
        <div class="summary-item"><span>Total Expired</span><strong><?= number_format($total_expired); ?></strong></div>
        <div class="summary-item"><span>Ditampilkan</span><strong><?= number_format(count($rows)); ?></strong></div>
        <div class="summary-item"><span>Lewat 7 Hari</span><strong><?= number_format($urgentCount); ?></strong></div>
    </div>

    <div class="card datatable-card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h5 class="card-title mb-0"><i class="bi bi-key-fill text-warning"></i> Data Akun Harus Ganti Password</h5>
                <small class="text-muted">Akun dengan tanggal expired yang sudah lewat.</small>
            </div>

            <form class="toolbar" method="get" action="<?= site_url('digital-accounts/password-expired'); ?>">
                <div class="filter-group">
                    <div class="filter-control">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="all">Semua Status</option>
                            <?php foreach ($statusLabels as $key => $label): ?>
                                <option value="<?= $key; ?>" <?= $status_filter === $key ? 'selected' : ''; ?>><?= h($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-control">
                        <label class="form-label">Search</label>
                        <input class="form-control" type="search" name="q" value="<?= h($this->input->get('q')); ?>" placeholder="Cari nama, username, password...">
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Filter</button>
                    <a class="btn btn-outline-secondary" href="<?= site_url('digital-accounts/password-expired'); ?>">Reset</a>
                </div>
                <div class="action-group">
                    <a class="btn btn-outline-primary" href="<?= site_url('digital-accounts/bulk/create'); ?>"><i class="bi bi-archive"></i> Bulk Tambah</a>
                    <button class="btn btn-outline-primary" type="button" id="bulkEditButton" data-bs-toggle="modal" data-bs-target="#bulkEditExpiredModal" disabled><i class="bi bi-pencil-square"></i> Bulk Edit (<span id="bulkEditCount">0</span>)</button>
                    <a class="btn btn-primary" href="<?= site_url('digital-accounts/create'); ?>"><i class="bi bi-plus-circle"></i> Tambah Akun</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead>
                        <tr>
                            <th style="width:70px">#</th>
                            <th>Produk</th>
                            <th>Email Akun</th>
                            <th>Tipe</th>
                            <th>Slot</th>
                            <th>HPP / Modal</th>
                            <th>Status</th>
                            <th>Expired</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($rows): foreach ($rows as $account): ?>
                        <?php
                        $statusKey = password_exp_status_key($account->status);
                        $expiredTs = strtotime($account->expired_at);
                        $daysLate = $expiredTs ? floor((time() - $expiredTs) / 86400) : 0;
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <input class="form-check-input expired-account-check" type="checkbox" value="<?= (int) $account->id; ?>" data-product="<?= h($account->product_name ?: 'Tanpa Produk'); ?>" data-variation="<?= h($account->variation ?: ''); ?>" data-email="<?= h($account->email ?: ''); ?>" data-password="<?= h($account->password ?: ''); ?>">

                                </div>
                            </td>
                            <td>
                                <strong><?= h($account->product_name ?: 'Tanpa Produk'); ?></strong>
                                <?php if ($account->variation): ?><span class="row-subtext"><?= h($account->variation); ?></span><?php endif; ?>
                            </td>
                            <td><?= h($account->email ?: '-'); ?></td>
                            <td>
                                <span class="badge bg-info"><?= h(ucfirst($account->account_type ?: 'private')); ?></span>
                                <span class="badge bg-secondary"><?= h(ucfirst(str_replace('_', ' ', $account->method ?: 'credentials'))); ?></span>
                            </td>
                            <td><?= $account->account_type === 'sharing' ? (int) $account->used_slot.'/'.(int) $account->max_slot : '-'; ?></td>
                            <td><?= $account->hpp > 0 ? rupiah($account->hpp) : '-'; ?></td>
                            <td><span class="badge <?= h($statusBadgeClasses[$statusKey] ?? 'bg-primary'); ?>"><?= h($statusLabels[$statusKey] ?? ucfirst($statusKey)); ?></span></td>
                            <td><span class="expired-date"><?= $expiredTs ? date('d/m/Y', $expiredTs) : '-'; ?></span><span class="row-subtext"><?= max(0, $daysLate); ?> hari lewat</span></td>
                            <td>
                                <div class="table-actions">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal<?= (int) $account->id; ?>" title="Edit akun"><i class="bi bi-pencil"></i></button>
                                    <a class="btn btn-sm btn-outline-danger" href="<?= site_url('digital-accounts/delete/'.$account->id); ?>" onclick="return confirm('Hapus akun ini?')" title="Hapus akun"><i class="bi bi-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!$rows): ?>
                <div class="text-center text-muted py-4">Tidak ada akun expired untuk filter ini.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php foreach ($rows as $account): ?>
    <div class="modal fade" id="changePasswordModal<?= (int) $account->id; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?= site_url('digital-accounts/password-expired/update/'.$account->id); ?>" method="POST">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title"><i class="bi bi-pencil-square text-warning"></i> Edit Akun Expired</h5>
                            <small class="text-muted"><?= h(($account->product_name ?: 'Tanpa Produk').($account->variation ? ' - '.$account->variation : '')); ?></small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="status" value="available">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Username</label>
                                <input class="form-control" type="text" name="email" value="<?= h($account->email ?: ''); ?>" placeholder="email atau username akun">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input class="form-control" type="text" name="password" value="<?= h($account->password ?: ''); ?>" placeholder="Password baru" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Durasi Expired Baru</label>
                                <select name="expired_days" class="form-select password-exp-duration">
                                    <?php foreach ($durations as $duration): ?>
                                        <option value="<?= (int) $duration->days; ?>" <?= $duration->is_default ? 'selected' : ''; ?>><?= h($duration->label); ?> - <?= (int) $duration->days; ?> hari</option>
                                    <?php endforeach; ?>
                                    <option value="30" <?= !$durations ? 'selected' : ''; ?>>30 Hari</option>
                                    <option value="0">Pilih tanggal manual</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Manual</label>
                                <input class="form-control" type="datetime-local" name="expired_at" value="">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status Setelah Edit</label>
                                <input class="form-control" value="Tersedia" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Produk</label>
                                <input class="form-control" value="<?= h($account->product_name ?: 'Tanpa Produk'); ?>" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Catatan</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Catatan admin"><?= h($account->notes ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan Edit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<div class="modal fade" id="bulkEditExpiredModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= site_url('digital-accounts/password-expired/bulk-update'); ?>" method="POST" id="bulkEditExpiredForm">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title"><i class="bi bi-pencil-square text-warning"></i> Bulk Edit Akun Expired</h5>
                        <small class="text-muted"><span id="bulkModalCount">0</span> akun dipilih.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Durasi Expired Baru</label>
                            <select name="expired_days" class="form-select password-exp-duration">
                                <?php foreach ($durations as $duration): ?>
                                    <option value="<?= (int) $duration->days; ?>" <?= $duration->is_default ? 'selected' : ''; ?>><?= h($duration->label); ?> - <?= (int) $duration->days; ?> hari</option>
                                <?php endforeach; ?>
                                <option value="30" <?= !$durations ? 'selected' : ''; ?>>30 Hari</option>
                                <option value="0">Pilih tanggal manual</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Manual</label>
                            <input class="form-control" type="datetime-local" name="expired_at" value="">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Status Setelah Diganti</label>
                            <select name="status" class="form-select">
                                <option value="available">Tersedia</option>
                                <option value="verified">Verif</option>
                                <option value="active_age">Umur Aktif</option>
                                <option value="sold">Sold</option>
                                <option value="no_access">No Access</option>
                                <option value="deactived">Deactived</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Data Akun Terpilih</label>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Username</th>
                                            <th>Password</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bulkSelectedInputs">
                                        <tr><td colspan="3" class="text-muted">Belum ada akun dipilih.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Catatan ini akan diterapkan ke semua akun terpilih. Kosongkan jika tidak ingin mengubah catatan."></textarea>
                            <div class="form-text">Catatan diterapkan ke semua akun yang dipilih.</div>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-primary py-2 mb-0">
                                <i class="bi bi-info-circle"></i> Expired dan status akan diterapkan ke semua akun terpilih. Password hanya diganti jika barisnya diisi.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan Bulk Edit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checks = Array.from(document.querySelectorAll('.expired-account-check'));
    const selectAll = document.getElementById('selectAllExpiredAccounts');
    const bulkButton = document.getElementById('bulkEditButton');
    const bulkCount = document.getElementById('bulkEditCount');
    const bulkModalCount = document.getElementById('bulkModalCount');
    const bulkSelectedInputs = document.getElementById('bulkSelectedInputs');

    function selectedChecks() {
        return checks.filter(function (check) { return check.checked; });
    }

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char];
        });
    }

    function refreshBulkEdit() {
        const selected = selectedChecks();
        if (bulkCount) bulkCount.textContent = selected.length;
        if (bulkModalCount) bulkModalCount.textContent = selected.length;
        if (bulkButton) bulkButton.disabled = selected.length === 0;
        if (selectAll) selectAll.checked = checks.length > 0 && selected.length === checks.length;
        if (bulkSelectedInputs) {
            bulkSelectedInputs.innerHTML = selected.length ? selected.map(function (check) {
                const id = escapeHtml(check.value);
                const product = escapeHtml(check.dataset.product || 'Tanpa Produk');
                const variation = escapeHtml(check.dataset.variation || '');
                const email = escapeHtml(check.dataset.email || '');
                const password = escapeHtml(check.dataset.password || '');
                return `<tr>
                    <td>
                        <input type="hidden" name="account_ids[]" value="${id}">
                        <strong>${product}</strong>${variation ? `<span class="row-subtext">${variation}</span>` : ''}
                    </td>
                    <td><input class="form-control" type="text" name="emails[${id}]" value="${email}" placeholder="Username"></td>
                    <td><input class="form-control" type="text" name="passwords[${id}]" value="${password}" placeholder="Password"></td>
                </tr>`;
            }).join('') : '<tr><td colspan="3" class="text-muted">Belum ada akun dipilih.</td></tr>';
        }
    }

    checks.forEach(function (check) {
        check.addEventListener('change', refreshBulkEdit);
    });
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checks.forEach(function (check) { check.checked = selectAll.checked; });
            refreshBulkEdit();
        });
    }
    refreshBulkEdit();

    function fillManualDate(select, force) {
        const days = Number(select.value);
        const modal = select.closest('.modal-body');
        const input = modal ? modal.querySelector('input[name="expired_at"]') : null;
        if (!input || days <= 0) return;
        if (!force && input.value) return;
        const date = new Date();
        date.setDate(date.getDate() + days);
        const pad = (value) => String(value).padStart(2, '0');
        input.value = `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
    }

    document.querySelectorAll('.password-exp-duration').forEach(function (select) {
        fillManualDate(select, false);
        select.addEventListener('change', function () { fillManualDate(select, true); });
    });
});
</script>

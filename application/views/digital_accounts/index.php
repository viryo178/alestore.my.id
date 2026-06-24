<?php
$section = $stock_section ?: 'account-stock';
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
if (!function_exists('digital_account_status_key')) {
    function digital_account_status_key($status)
    {
        return in_array($status, array('unavailable', 'unvailabel'), true) ? 'sold' : $status;
    }
}
$productNames = array();
foreach ($digital_products as $product) {
    $productNames[$product->name] = $product->name;
}
foreach ($rows as $account) {
    if (!empty($account->product_name)) {
        $productNames[$account->product_name] = $account->product_name;
    }
}
ksort($productNames);
?>

<style>
    .digital-account-toolbar{display:flex;flex-direction:column;gap:14px;margin-bottom:18px}
    .digital-account-filter-row,.digital-account-table-row{align-items:flex-end;display:flex;flex-wrap:wrap;gap:12px;justify-content:space-between}
    .digital-account-filter-controls,.digital-account-actions{align-items:flex-end;display:flex;flex-wrap:wrap;gap:10px}
    .datatable-filter-control{min-width:170px}
    .datatable-length{align-items:center;display:flex;gap:8px}.datatable-length .form-select{width:75px}
    .datatable-search{min-width:260px}.stock-summary-table th{font-size:11px;letter-spacing:.04em;text-transform:uppercase;white-space:nowrap}
    .section-tabs{display:flex;flex-wrap:wrap;gap:9px;margin-bottom:18px}
    .account-method-grid{display:grid;gap:10px;grid-template-columns:repeat(2,minmax(0,1fr))}
    .account-method-card{align-items:flex-start;background:rgba(47,124,255,.08);border:1px solid rgba(47,124,255,.16);border-radius:8px;display:flex;gap:10px;padding:12px}
    .account-method-card small{color:#8da0bd;display:block;margin-top:2px}
    .digital-account-expired-row td{background:rgba(212,155,31,.10)!important;color:#f8fbff!important}
    .digital-account-expired-row td .small,.digital-account-expired-row td .text-muted{color:#9fb5d4!important}
    .stock-lines-input{font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace}
    @media (max-width:767.98px){.digital-account-filter-row,.digital-account-table-row{align-items:stretch;flex-direction:column}.digital-account-filter-controls,.digital-account-actions{align-items:stretch}.datatable-filter-control,.datatable-search{min-width:100%}.account-method-grid{grid-template-columns:1fr}}
</style>

<div class="pagetitle">
    <h1>Management Akun Digital</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard'); ?>">Dashboard</a></li><li class="breadcrumb-item active">Management Akun Digital</li></ol></nav>
</div>

<section class="section" id="digitalAccountApp" data-feed-url="<?= site_url('digital-accounts/feed'); ?>">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <div class="text-muted">Total: <span id="totalAccounts"><?= number_format($total_accounts); ?></span> akun</div>
            <small class="text-muted">Realtime: <span id="serverTime"><?= date('d M Y, H:i:s'); ?> WIB</span></small>
        </div>
    </div>

    <div class="alert alert-warning <?= $expired_soon_count > 0 ? '' : 'd-none'; ?>" id="expiredNotice">
        <i class="bi bi-exclamation-triangle"></i>
        <strong>Password expired:</strong> <span id="expiredCount"><?= (int) $expired_soon_count; ?></span> akun akan expired dalam 2 hari.
    </div>

    <div class="section-tabs">
        <a href="<?= site_url('digital-accounts?section=account-stock'); ?>" class="btn btn-sm <?= $section === 'account-stock' ? 'btn-primary' : 'btn-outline-primary'; ?>"><i class="bi bi-person-fill"></i> Stok Akun</a>
        <a href="<?= site_url('digital-accounts?section=product-stock'); ?>" class="btn btn-sm <?= $section === 'product-stock' ? 'btn-primary' : 'btn-outline-primary'; ?>"><i class="bi bi-box-seam"></i> Stok Produk</a>
        <a href="<?= site_url('digital-accounts?section=license-stock'); ?>" class="btn btn-sm <?= $section === 'license-stock' ? 'btn-primary' : 'btn-outline-primary'; ?>"><i class="bi bi-lock-fill"></i> License / Link</a>
    </div>

    <?php if ($section === 'product-stock'): ?>
        <div class="card datatable-card">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h5 class="card-title mb-0"><i class="bi bi-box-seam text-warning"></i> Stok Per Produk/Variasi</h5>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <small class="text-muted"><?= number_format(count($product_stocks)); ?> grup</small>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createProductModal"><i class="bi bi-plus-circle"></i> Tambah Produk</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle stock-summary-table">
                        <thead><tr><th>Produk / Variasi</th><th>Tipe</th><th>Total</th><th class="text-success">Tersedia</th><th class="text-primary">Slot Kosong</th><th class="text-warning">Full</th><th class="text-danger">Sold</th><th>Banned</th><th>Aksi</th></tr></thead>
                        <tbody>
                        <?php if ($product_stocks): foreach ($product_stocks as $stockIndex => $stock): ?>
                            <tr>
                                <td><strong><?= h($stock['product_name']); ?></strong><?php if ($stock['variation']): ?><div class="small text-primary"><i class="bi bi-tag-fill text-warning"></i> <?= h($stock['variation']); ?></div><?php endif; ?></td>
                                <td><span class="badge bg-info"><?= h(ucfirst($stock['account_type'])); ?></span></td>
                                <td><strong><?= number_format($stock['total']); ?></strong></td>
                                <td><span class="badge bg-success"><?= number_format($stock['available']); ?></span></td>
                                <td><span class="text-primary fw-bold"><?= number_format($stock['empty_slot']); ?></span></td>
                                <td><?= $stock['full'] > 0 ? '<span class="badge bg-warning">'.number_format($stock['full']).'</span>' : '<span class="text-muted">0</span>'; ?></td>
                                <td><?= $stock['sold'] > 0 ? '<span class="badge bg-danger">'.number_format($stock['sold']).'</span>' : '<span class="text-muted">0</span>'; ?></td>
                                <td><?= $stock['banned'] > 0 ? '<span class="badge bg-danger">'.number_format($stock['banned']).'</span>' : '<span class="text-muted">0</span>'; ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#stockProductModal<?= $stockIndex; ?>"><i class="bi bi-plus-lg"></i> Stok</button>
                                    <?php if ($stock['product_id']): ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProductModal<?= $stock['product_id']; ?>"><i class="bi bi-pencil"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteProductModal<?= $stock['product_id']; ?>"><i class="bi bi-trash"></i></button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="9" class="text-center text-muted py-4">Belum ada stok produk.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php elseif ($section === 'license-stock'): ?>
        <div class="card datatable-card">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h5 class="card-title mb-0"><i class="bi bi-lock-fill text-warning"></i> Stok Digital (License / Link)</h5>
                    <small class="text-muted"><?= number_format(count($license_stocks)); ?> item</small>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle stock-summary-table">
                        <thead><tr><th>Produk / Variasi</th><th>Method</th><th>Total</th><th class="text-success">Tersedia</th><th class="text-danger">Sold</th><th>Problem</th><th>Aksi</th></tr></thead>
                        <tbody>
                        <?php if ($license_stocks): foreach ($license_stocks as $stockIndex => $stock): ?>
                            <tr>
                                <td><strong><?= h($stock['product_name']); ?></strong><?php if ($stock['variation']): ?><div class="small text-primary"><i class="bi bi-tag-fill text-warning"></i> <?= h($stock['variation']); ?></div><?php endif; ?></td>
                                <td><span class="badge bg-secondary"><?= h(ucfirst($stock['method'])); ?></span></td>
                                <td><strong><?= number_format($stock['total']); ?></strong></td>
                                <td><span class="badge bg-success"><?= number_format($stock['available']); ?></span></td>
                                <td><span class="badge bg-danger"><?= number_format($stock['sold']); ?></span></td>
                                <td><span class="badge bg-danger"><?= number_format($stock['problem']); ?></span></td>
                                <td><button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#stockLicenseModal<?= $stockIndex; ?>"><i class="bi bi-plus-lg"></i> Stok</button></td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada stok license atau link.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card datatable-card">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h5 class="card-title mb-0"><i class="bi bi-person-fill text-primary"></i> Stok Akun Digital</h5>
                    <small class="text-muted"><span id="visibleRows"><?= number_format(count($rows)); ?></span> akun</small>
                </div>

                <div class="digital-account-toolbar">
                    <div class="digital-account-filter-row">
                        <div class="digital-account-filter-controls">
                            <div class="datatable-filter-control"><label class="form-label">Tipe</label><select class="form-select" id="typeFilter"><option value="all">Semua Tipe</option><option value="private">Private</option><option value="sharing">Sharing</option></select></div>
                            <div class="datatable-filter-control"><label class="form-label">Status</label><select class="form-select" id="statusFilter"><option value="all">Semua Status</option><?php foreach ($statusLabels as $key => $label): ?><option value="<?= $key; ?>"><?= h($label); ?></option><?php endforeach; ?></select></div>
                            <button type="button" class="btn btn-secondary" id="resetFilter">Reset</button>
                        </div>
                        <div class="digital-account-actions">
                            <a href="<?= site_url('digital-accounts/bulk/create'); ?>" class="btn btn-outline-primary"><i class="bi bi-archive"></i> Bulk Tambah</a>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAccountModal"><i class="bi bi-plus-circle"></i> Tambah Akun</button>
                        </div>
                    </div>
                    <div class="digital-account-table-row">
                        <div class="datatable-length"><select class="form-select form-select-sm"><option selected>10</option><option>25</option><option>50</option></select><span>entries per page</span></div>
                        <div class="datatable-search"><input type="text" class="form-control" id="accountSearch" value="<?= h($this->input->get('q')); ?>" placeholder="Search..."></div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead><tr><th>#</th><th>Produk</th><th>Email Akun</th><th>Tipe</th><th>Slot</th><th>HPP / Modal</th><th>Status</th><th>Expired</th><th>Aksi</th></tr></thead>
                        <tbody id="accountsTableBody">
                        <?php foreach ($rows as $account): ?>
                            <tr>
                                <td><?= (int) $account->id; ?></td>
                                <td><strong><?= h($account->product_name); ?></strong><?php if ($account->variation): ?><div class="small text-muted"><?= h($account->variation); ?></div><?php endif; ?></td>
                                <td><?= h($account->email ?: '-'); ?></td>
                                <td><span class="badge bg-info"><?= h(ucfirst($account->account_type)); ?></span> <span class="badge bg-secondary"><?= h(ucfirst($account->method)); ?></span></td>
                                <td><?= $account->account_type === 'sharing' ? (int) $account->used_slot.'/'.(int) $account->max_slot : '-'; ?></td>
                                <td><?= $account->hpp > 0 ? rupiah($account->hpp) : '-'; ?></td>
                                <?php $accountStatus = digital_account_status_key($account->status); ?>
                                <td><span class="badge <?= h($statusBadgeClasses[$accountStatus] ?? 'bg-primary'); ?>"><?= h($statusLabels[$accountStatus] ?? ucfirst($accountStatus)); ?></span></td>
                                <td><?= $account->expired_at ? date('d/m/Y', strtotime($account->expired_at)) : '-'; ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editAccountModal<?= $account->id; ?>"><i class="bi bi-pencil"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal<?= $account->id; ?>"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<datalist id="productOptions">
    <?php foreach ($productNames as $name): ?><option value="<?= h($name); ?>"></option><?php endforeach; ?>
</datalist>

<div class="modal fade" id="createProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <form action="<?= site_url('digital-accounts/products'); ?>" method="POST">
                <div class="modal-header"><div><h5 class="modal-title"><i class="bi bi-box-seam text-warning"></i> Tambah Produk</h5><small class="text-muted">Buat katalog produk AI. Akun ditambahkan lewat tombol stok.</small></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><div class="row g-4">
                    <div class="col-md-6"><label class="form-label">Nama Produk</label><input type="text" name="name" class="form-control" list="productOptions" placeholder="Contoh: Canva Pro" required></div>
                    <div class="col-md-6"><label class="form-label">Variasi</label><textarea name="variations" class="form-control" rows="3" placeholder="Lifetime&#10;1 Bulan&#10;35 Days"></textarea><div class="form-text">Satu variasi per baris atau pisahkan dengan koma.</div></div>
                    <div class="col-md-6"><label class="form-label">Kategori Akun</label><select name="account_type" class="form-select"><option value="private">Private</option><option value="sharing">Sharing</option></select></div>
                    <div class="col-md-6"><label class="form-label">Max Slot</label><input type="number" name="max_slot" class="form-control" value="1" min="1"></div>
                    <div class="col-md-6"><label class="form-label">Metode</label><select name="method" class="form-select"><option value="credentials">Credentials</option><option value="invite_email">Invite Email</option><option value="link">Link</option><option value="license">License</option></select></div>
                    <div class="col-md-6"><label class="form-label">HPP Default</label><input type="number" name="hpp" class="form-control" value="0" min="0" step="100"></div>
                    <div class="col-12"><label class="form-label">Catatan</label><textarea name="notes" class="form-control" rows="3"></textarea></div>
                </div></div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Produk</button></div>
            </form>
        </div>
    </div>
</div>

<?php foreach ($digital_products as $product): ?>
    <?php $productVars = array_values(array_filter($variations, function ($variation) use ($product) { return (int) $variation->digital_product_id === (int) $product->id; })); ?>
    <div class="modal fade" id="editProductModal<?= $product->id; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable"><div class="modal-content"><form action="<?= site_url('digital-accounts/products/'.$product->id); ?>" method="POST">
            <div class="modal-header"><div><h5 class="modal-title"><i class="bi bi-pencil-square text-primary"></i> Edit Produk</h5><small class="text-muted"><?= h($product->name); ?></small></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><div class="row g-4">
                <div class="col-md-6"><label class="form-label">Nama Produk</label><input type="text" name="name" class="form-control" value="<?= h($product->name); ?>" required></div>
                <div class="col-md-6"><label class="form-label">Variasi</label><textarea name="variations" class="form-control" rows="3"><?php foreach ($productVars as $variation): ?><?= h($variation->label)."\n"; ?><?php endforeach; ?></textarea></div>
                <div class="col-md-6"><label class="form-label">Kategori Akun</label><select name="account_type" class="form-select"><option value="private" <?= $product->account_type === 'private' ? 'selected' : ''; ?>>Private</option><option value="sharing" <?= $product->account_type === 'sharing' ? 'selected' : ''; ?>>Sharing</option></select></div>
                <div class="col-md-6"><label class="form-label">Metode</label><select name="method" class="form-select"><?php foreach (array('credentials','invite_email','link','license') as $method): ?><option value="<?= $method; ?>" <?= $product->method === $method ? 'selected' : ''; ?>><?= h(ucfirst(str_replace('_',' ', $method))); ?></option><?php endforeach; ?></select></div>
                <div class="col-md-6"><label class="form-label">Max Slot</label><input type="number" name="max_slot" class="form-control" value="<?= h($product->max_slot ?? 1); ?>" min="1"></div>
                <div class="col-md-6"><label class="form-label">HPP Default</label><input type="number" name="hpp" class="form-control" value="<?= h($product->hpp ?? 0); ?>" min="0" step="100"></div>
                <div class="col-12"><label class="form-label">Catatan</label><textarea name="notes" class="form-control" rows="3"><?= h($product->notes ?? ''); ?></textarea></div>
            </div></div>
            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Produk</button></div>
        </form></div></div>
    </div>
    <div class="modal fade" id="deleteProductModal<?= $product->id; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Hapus Produk</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">Hapus produk <strong><?= h($product->name); ?></strong>? Akun yang sudah ada tidak ikut terhapus.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><a href="<?= site_url('digital-accounts/products/delete/'.$product->id); ?>" class="btn btn-danger">Hapus Produk</a></div></div></div>
    </div>
<?php endforeach; ?>

<?php foreach ($product_stocks as $stockIndex => $stock): ?>
    <?php $modalTitle = $stock['product_name'].($stock['variation'] ? ' - '.$stock['variation'] : ''); ?>
    <div class="modal fade" id="stockProductModal<?= $stockIndex; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable"><div class="modal-content"><form action="<?= site_url('digital-accounts/stock'); ?>" method="POST">
            <input type="hidden" name="product_name" value="<?= h($stock['product_name']); ?>"><input type="hidden" name="digital_product_id" value="<?= h($stock['product_id']); ?>"><input type="hidden" name="account_type" value="<?= h($stock['account_type'] ?: 'sharing'); ?>"><input type="hidden" name="method" value="<?= h($stock['method'] ?: 'credentials'); ?>"><input type="hidden" name="max_slot" value="<?= h($stock['max_slot'] ?: 1); ?>">
            <div class="modal-header"><div><h5 class="modal-title"><i class="bi bi-box-seam text-warning"></i> Tambah Stok</h5><div class="fw-bold mt-2"><?= h($modalTitle); ?></div><small class="text-muted">Satu akun per baris. Format: username|password</small></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <?php $variationOptions = array_values(array_filter($variations, function ($variation) use ($stock) { return (int) $variation->digital_product_id === (int) $stock['product_id']; })); ?>
                <?php if ($variationOptions): ?><div class="mb-3"><label class="form-label">Variasi Stok (opsional)</label><select name="digital_product_variation_id" class="form-select"><option value="">-- Tanpa Variasi --</option><?php foreach ($variationOptions as $variation): ?><option value="<?= $variation->id; ?>" <?= (int) $variation->id === (int) $stock['variation_id'] ? 'selected' : ''; ?>><?= h($variation->label); ?></option><?php endforeach; ?></select><div class="form-text">Opsional. Jika dipilih, stok akan dipisah per variasi.</div></div><?php else: ?><input type="hidden" name="variation" value="<?= h($stock['variation']); ?>"><?php endif; ?>
                <div class="mb-3"><textarea name="stock_lines" class="form-control stock-lines-input" rows="8" placeholder="user1@gmail.com|password123&#10;user2@gmail.com|pass456" required></textarea></div>
                <div class="mb-3"><label class="form-label">Durasi Expired</label><select name="expired_days" class="form-select"><?php foreach ($durations as $duration): ?><option value="<?= (int) $duration->days; ?>" <?= $duration->is_default ? 'selected' : ''; ?>><?= h($duration->label); ?> - <?= (int) $duration->days; ?> hari</option><?php endforeach; ?><option value="30">30 Hari</option></select></div>
                <div class="alert alert-primary py-2 mb-0"><i class="bi bi-info-circle"></i> Stok akan ditambahkan sebagai belum terjual.</div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Update Stok</button></div>
        </form></div></div>
    </div>
<?php endforeach; ?>

<?php foreach ($license_stocks as $stockIndex => $stock): ?>
    <div class="modal fade" id="stockLicenseModal<?= $stockIndex; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable"><div class="modal-content"><form action="<?= site_url('digital-accounts/stock'); ?>" method="POST">
            <input type="hidden" name="product_name" value="<?= h($stock['product_name']); ?>"><input type="hidden" name="variation" value="<?= h($stock['variation']); ?>"><input type="hidden" name="method" value="<?= h($stock['method']); ?>">
            <div class="modal-header"><div><h5 class="modal-title"><i class="bi bi-lock-fill text-warning"></i> Tambah Stok Digital</h5><div class="fw-bold mt-2"><?= h($stock['product_name'].($stock['variation'] ? ' - '.$stock['variation'] : '')); ?></div><small class="text-muted">Satu item per baris. Format: email|password atau kode/link.</small></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><div class="mb-3"><textarea name="stock_lines" class="form-control stock-lines-input" rows="8" placeholder="akun@email.com|password&#10;LICENSE-KEY-123&#10;https://link-license.example" required></textarea></div><div class="mb-2"><label class="form-label">Durasi Expired</label><select name="expired_days" class="form-select"><?php foreach ($durations as $duration): ?><option value="<?= (int) $duration->days; ?>" <?= $duration->is_default ? 'selected' : ''; ?>><?= h($duration->label); ?> - <?= (int) $duration->days; ?> hari</option><?php endforeach; ?><option value="30">30 Hari</option></select></div><div class="alert alert-primary py-2 mb-0"><i class="bi bi-info-circle"></i> Item akan masuk ke stok digital produk ini.</div></div>
            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Update Stok</button></div>
        </form></div></div>
    </div>
<?php endforeach; ?>

<div class="modal fade" id="createAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content"><form action="<?= site_url('digital-accounts/store'); ?>" method="POST"><input type="hidden" name="_redirect_section" value="account-stock"><div class="modal-header"><h5 class="modal-title">Tambah Akun Digital</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><?php $row = null; $this->load->view('digital_accounts/form_fields', compact('row', 'products', 'durations')); ?></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Akun</button></div></form></div></div>
</div>

<?php foreach ($rows as $account): ?>
    <div class="modal fade" id="editAccountModal<?= $account->id; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content"><form action="<?= site_url('digital-accounts/update/'.$account->id); ?>" method="POST"><?php if ((string) $this->input->get('edit') === (string) $account->id && $this->input->get('notify')): ?><input type="hidden" name="_resolve_notification" value="1"><?php endif; ?><div class="modal-header"><h5 class="modal-title">Edit Akun Digital</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><?php $row = $account; $this->load->view('digital_accounts/form_fields', compact('row', 'products', 'durations')); ?></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div></form></div></div>
    </div>
    <div class="modal fade" id="deleteAccountModal<?= $account->id; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Hapus Akun</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">Yakin hapus akun <strong><?= h($account->email ?: $account->product_name); ?></strong>?</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><a href="<?= site_url('digital-accounts/delete/'.$account->id); ?>" class="btn btn-danger">Hapus</a></div></div></div>
    </div>
<?php endforeach; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const app = document.getElementById('digitalAccountApp');
    const tbody = document.getElementById('accountsTableBody');
    const search = document.getElementById('accountSearch');
    const type = document.getElementById('typeFilter');
    const status = document.getElementById('statusFilter');
    const total = document.getElementById('totalAccounts');
    const visibleRows = document.getElementById('visibleRows');
    const serverTime = document.getElementById('serverTime');
    const expiredNotice = document.getElementById('expiredNotice');
    const expiredCount = document.getElementById('expiredCount');
    const reset = document.getElementById('resetFilter');
    const labels = <?= json_encode($statusLabels); ?>;
    const badgeClasses = <?= json_encode($statusBadgeClasses); ?>;

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char];
        });
    }

    function renderRows(accounts) {
        if (!tbody) return;
        tbody.innerHTML = accounts.map(function (account) {
            const statusClass = badgeClasses[account.status] || 'bg-primary';
            return `<tr class="${account.expired_warning ? 'digital-account-expired-row' : ''}">
                <td>${account.id}</td>
                <td><strong>${escapeHtml(account.product_name)}</strong>${account.variation ? `<div class="small text-muted">${escapeHtml(account.variation)}</div>` : ''}</td>
                <td>${escapeHtml(account.email || '-')}</td>
                <td><span class="badge bg-info">${escapeHtml(account.account_type === 'sharing' ? 'Sharing' : 'Private')}</span> <span class="badge bg-secondary">${escapeHtml(account.method)}</span></td>
                <td>${escapeHtml(account.slot)}</td>
                <td>${escapeHtml(account.hpp)}</td>
                <td><span class="badge ${escapeHtml(statusClass)}">${escapeHtml(labels[account.status] || account.status_label)}</span></td>
                <td>${escapeHtml(account.expired_at || '-')}</td>
                <td><button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#${account.edit_modal}"><i class="bi bi-pencil"></i></button> <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#${account.delete_modal}"><i class="bi bi-trash"></i></button></td>
            </tr>`;
        }).join('');
    }

    async function loadAccounts() {
        if (!app || !tbody || !search || !type || !status) return;
        const params = new URLSearchParams({q: search.value, account_type: type.value, status: status.value});
        const response = await fetch(`${app.dataset.feedUrl}?${params.toString()}`, {headers: {'X-Requested-With': 'XMLHttpRequest'}});
        const data = await response.json();
        total.textContent = new Intl.NumberFormat('id-ID').format(data.total);
        visibleRows.textContent = new Intl.NumberFormat('id-ID').format(data.accounts.length);
        serverTime.textContent = data.server_time;
        expiredCount.textContent = data.expired_soon;
        expiredNotice.classList.toggle('d-none', data.expired_soon < 1);
        renderRows(data.accounts);
    }

    let timer;
    function queueLoad() {
        clearTimeout(timer);
        timer = setTimeout(loadAccounts, 300);
    }

    if (search) search.addEventListener('input', queueLoad);
    if (type) type.addEventListener('change', loadAccounts);
    if (status) status.addEventListener('change', loadAccounts);
    if (reset) reset.addEventListener('click', function () { search.value = ''; type.value = 'all'; status.value = 'all'; loadAccounts(); });
    loadAccounts();
    if (tbody) setInterval(loadAccounts, 5000);

    function applyDuration(picker, force) {
        const days = Number(picker.value);
        const modal = picker.closest('.modal-body');
        const expiredInput = modal ? modal.querySelector('input[name="expired_at"]') : null;
        if (!days || !expiredInput) return;
        if (!force && expiredInput.value) return;
        const date = new Date();
        date.setDate(date.getDate() + days);
        const pad = (value) => String(value).padStart(2, '0');
        expiredInput.value = `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
    }

    document.querySelectorAll('.expire-duration-picker').forEach(function (picker) {
        applyDuration(picker, false);
        picker.addEventListener('change', function () { applyDuration(picker, true); });
    });
});
</script>

<?php if ($this->input->get('edit')): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('editAccountModal<?= (int) $this->input->get('edit'); ?>');
    if (modal && window.bootstrap) {
        bootstrap.Modal.getOrCreateInstance(modal).show();
    }
});
</script>
<?php endif; ?>

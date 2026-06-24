<div class="pagetitle">
    <h1>Bulk Tambah Akun</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard'); ?>">Dashboard</a></li><li class="breadcrumb-item"><a href="<?= site_url('digital-accounts'); ?>">Management Akun Digital</a></li><li class="breadcrumb-item active">Bulk Tambah</li></ol></nav>
</div>

<section class="section">
    <div class="card datatable-card">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-archive text-warning"></i> Bulk Tambah Stok Akun</h5>
            <form method="post" action="<?= site_url('digital-accounts/bulk'); ?>">
                <div class="digital-account-form row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Produk</label>
                        <select class="form-select" name="digital_product_id">
                            <option value="">Manual</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?= $product->id; ?>"><?= h($product->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6"><label class="form-label">Nama Produk</label><input class="form-control" name="product_name" required placeholder="Contoh: Canva Pro"></div>
                    <div class="col-md-4"><label class="form-label">Variasi</label><input class="form-control" name="variation" placeholder="1 Bulan"></div>
                    <div class="col-md-4"><label class="form-label">Tipe</label><select class="form-select" name="account_type"><option value="private">Private</option><option value="sharing">Sharing</option></select></div>
                    <div class="col-md-4"><label class="form-label">Metode</label><select class="form-select" name="method"><option value="credentials">Credentials</option><option value="invite_email">Invite Email</option><option value="license">License</option><option value="link">Access Link</option></select></div>
                    <div class="col-md-3"><label class="form-label">Max Slot</label><input class="form-control" type="number" name="max_slot" value="1"></div>
                    <div class="col-md-3"><label class="form-label">HPP</label><input class="form-control" type="number" name="hpp" value="0"></div>
                    <div class="col-md-3"><label class="form-label">Durasi Expired</label><select class="form-select" name="expired_days"><?php foreach ($durations as $duration): ?><option value="<?= $duration->days; ?>" <?= $duration->is_default ? 'selected' : ''; ?>><?= h($duration->label); ?> - <?= (int) $duration->days; ?> hari</option><?php endforeach; ?><option value="30">30 Hari</option></select></div>
                    <div class="col-12">
                        <label class="form-label">Data Akun</label>
                        <textarea name="stock_lines" class="form-control stock-lines-input" rows="10" placeholder="user1@gmail.com|password123&#10;user2@gmail.com|pass456&#10;LICENSE-KEY-123" required></textarea>
                        <div class="form-text">Satu akun per baris. Format akun login: email|password. License/link boleh satu item per baris.</div>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2 justify-content-end">
                    <a href="<?= site_url('digital-accounts'); ?>" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan Bulk</button>
                </div>
            </form>
        </div>
    </div>
</section>

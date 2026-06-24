<div class="pagetitle">
    <h1>Shopee Stores</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard'); ?>">Dashboard</a></li><li class="breadcrumb-item active">Shopee Stores</li></ol></nav>
</div>

<section class="section">
    <div class="card datatable-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0"><i class="bi bi-shop text-warning"></i> Daftar Toko</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createStoreModal"><i class="bi bi-plus-circle"></i> Tambah Toko</button>
            </div>
            <div class="table-responsive">
                <table class="table datatable align-middle">
                    <thead><tr><th>Nama Toko</th><th>Platform</th><th>Shop ID</th><th>Fee Admin</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><strong><?= h($row->shop_name); ?></strong><?php if (!empty($row->description)): ?><div class="small text-muted"><?= h(character_limiter($row->description, 70)); ?></div><?php endif; ?></td>
                            <td><span class="badge bg-primary"><?= h($row->platform ?? 'Shopee'); ?></span></td>
                            <td><?= h($row->shop_id); ?></td>
                            <td><?= h($row->admin_fee_percentage ?? 0); ?>%</td>
                            <td><?= status_badge($row->status); ?></td>
                            <td><a class="btn btn-sm btn-outline-primary" href="<?= site_url('shopee-stores/edit/'.$row->id); ?>"><i class="bi bi-pencil"></i></a> <a class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus toko?')" href="<?= site_url('shopee-stores/delete/'.$row->id); ?>"><i class="bi bi-trash"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="createStoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="post" action="<?= site_url('shopee-stores/store'); ?>">
                <div class="modal-header"><h5 class="modal-title">Tambah Toko</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6"><label class="form-label">Nama Toko</label><input class="form-control" name="shop_name" required></div>
                        <div class="col-md-3"><label class="form-label">Platform</label><input class="form-control" name="platform" value="Shopee"></div>
                        <div class="col-md-3"><label class="form-label">Shop ID</label><input class="form-control" name="shop_id"></div>
                        <div class="col-md-3"><label class="form-label">Fee Admin %</label><input class="form-control" type="number" step="0.01" name="admin_fee_percentage" value="0"></div>
                        <div class="col-md-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                        <div class="col-12"><label class="form-label">Deskripsi</label><textarea class="form-control" name="description"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan Toko</button></div>
            </form>
        </div>
    </div>
</div>

<div class="pagetitle"><h1><?= h($title); ?></h1></div>
<div class="card"><div class="card-body"><h5 class="card-title">Form Toko</h5>
<form method="post" action="<?= $row ? site_url('shopee-stores/update/'.$row->id) : site_url('shopee-stores/store'); ?>">
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Nama Toko</label><input class="form-control" name="shop_name" required value="<?= h($row->shop_name ?? ''); ?>"></div>
        <div class="col-md-3"><label class="form-label">Platform</label><input class="form-control" name="platform" value="<?= h($row->platform ?? 'Shopee'); ?>"></div>
        <div class="col-md-3"><label class="form-label">Shop ID</label><input class="form-control" name="shop_id" value="<?= h($row->shop_id ?? ''); ?>"></div>
        <div class="col-md-3"><label class="form-label">Fee Admin %</label><input class="form-control" type="number" step="0.01" name="admin_fee_percentage" value="<?= h($row->admin_fee_percentage ?? '0'); ?>"></div>
        <div class="col-md-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="active">Active</option><option value="inactive" <?= isset($row->status) && $row->status === 'inactive' ? 'selected' : ''; ?>>Inactive</option></select></div>
        <div class="col-12"><label class="form-label">Deskripsi</label><textarea class="form-control" name="description"><?= h($row->description ?? ''); ?></textarea></div>
    </div>
    <div class="mt-3"><button class="btn btn-primary">Simpan</button> <a class="btn btn-outline-secondary" href="<?= site_url('shopee-stores'); ?>">Kembali</a></div>
</form></div></div>

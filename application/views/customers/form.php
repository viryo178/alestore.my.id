<div class="pagetitle"><h1><?= h($title); ?></h1></div>
<div class="card"><div class="card-body"><h5 class="card-title">Form Customer</h5>
    <form method="post" action="<?= $row ? site_url('customers/update/'.$row->id) : site_url('customers/store'); ?>">
        <div class="mb-3"><label class="form-label">Nama</label><input class="form-control" name="name" required value="<?= h($row->name ?? ''); ?>"></div>
        <div class="mb-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="<?= h($row->email ?? ''); ?>"></div>
        <div class="mb-3"><label class="form-label">Phone</label><input class="form-control" name="phone" value="<?= h($row->phone ?? ''); ?>"></div>
        <button class="btn btn-primary">Simpan</button> <a class="btn btn-outline-secondary" href="<?= site_url('customers'); ?>">Kembali</a>
    </form>
</div></div>

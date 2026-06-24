<div class="pagetitle"><h1><?= h($title); ?></h1></div>
<div class="card datatable-card">
    <div class="card-body">
        <h5 class="card-title">Form User</h5>
        <form method="post" action="<?= $row ? site_url('users/update/'.$row->id) : site_url('users/store'); ?>">
            <div class="row g-4">
                <div class="col-md-6"><label class="form-label">Nama</label><input class="form-control" name="name" required value="<?= h($row->name ?? ''); ?>"></div>
                <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required value="<?= h($row->email ?? ''); ?>"></div>
                <div class="col-md-6"><label class="form-label">Role</label><select class="form-select" name="role"><option value="admin" <?= isset($row->role) && $row->role === 'admin' ? 'selected' : ''; ?>>Admin</option><option value="staff" <?= isset($row->role) && $row->role === 'staff' ? 'selected' : ''; ?>>Staff</option></select></div>
                <div class="col-md-6"><label class="form-label">Password <?= $row ? '(kosongkan jika tidak diganti)' : ''; ?></label><input class="form-control" type="password" name="password" <?= $row ? '' : 'required'; ?>></div>
            </div>
            <div class="mt-4"><button class="btn btn-primary">Simpan</button> <a class="btn btn-outline-secondary" href="<?= site_url('users'); ?>">Kembali</a></div>
        </form>
    </div>
</div>

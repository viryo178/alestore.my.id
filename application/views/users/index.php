<div class="pagetitle">
    <h1>Users</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard'); ?>">Dashboard</a></li><li class="breadcrumb-item active">Users</li></ol></nav>
</div>

<section class="section">
    <div class="card datatable-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">User Management</h5>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-primary btn-sm" href="<?= site_url('users/change-password'); ?>"><i class="bi bi-key"></i> Change Password</a>
                    <a class="btn btn-primary btn-sm" href="<?= site_url('users/create'); ?>"><i class="bi bi-plus-circle"></i> Tambah User</a>
                </div>
            </div>
            <table class="table datatable align-middle">
                <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Dibuat</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><strong><?= h($row->name); ?></strong></td>
                        <td><?= h($row->email); ?></td>
                        <td><span class="badge bg-primary"><?= h($row->role ?? '-'); ?></span></td>
                        <td><?= h($row->created_at); ?></td>
                        <td>
                            <a class="btn btn-sm btn-outline-primary" href="<?= site_url('users/edit/'.$row->id); ?>"><i class="bi bi-pencil"></i></a>
                            <a class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus user?')" href="<?= site_url('users/delete/'.$row->id); ?>"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

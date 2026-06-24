<div class="pagetitle"><h1>Change Password</h1></div>
<div class="card datatable-card">
    <div class="card-body">
        <h5 class="card-title">Update Password User</h5>
        <form method="post" action="<?= site_url('users/update-password'); ?>">
            <div class="row g-4">
                <div class="col-md-6"><label class="form-label">User ID</label><input class="form-control" type="number" name="user_id" required placeholder="Masukkan ID user"></div>
                <div class="col-md-6"><label class="form-label">Password Baru</label><input class="form-control" type="password" name="password" required></div>
            </div>
            <div class="mt-4"><button class="btn btn-primary">Update Password</button> <a class="btn btn-outline-secondary" href="<?= site_url('users'); ?>">Kembali</a></div>
        </form>
    </div>
</div>

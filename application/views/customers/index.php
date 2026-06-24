<div class="pagetitle">
    <h1>Data Customer</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard'); ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Customers</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card datatable-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Daftar Customer</h5>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createCustomerModal">
                            <i class="bi bi-plus-circle"></i> Tambah Customer
                        </button>
                    </div>

                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th><b>No</b></th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($rows): foreach ($rows as $index => $c): ?>
                            <tr>
                                <td><?= $index + 1; ?></td>
                                <td><?= h($c->name); ?></td>
                                <td><?= h($c->email); ?></td>
                                <td><?= h($c->phone); ?></td>
                                <td>
                                    <a href="<?= site_url('customers/edit/'.$c->id); ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <a href="<?= site_url('customers/delete/'.$c->id); ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus?');"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox" style="font-size:2rem;"></i>
                                    <p>Tidak ada data customer</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="createCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form action="<?= site_url('customers/store'); ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="customerName" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="customerName" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="customerEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="customerEmail" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="customerPhone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="customerPhone" name="phone" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

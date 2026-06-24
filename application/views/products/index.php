<?php
$money = function ($value) {
    return 'Rp'.number_format((float) $value, 0, ',', '.');
};
$methodBadge = array(
    'credentials' => 'Akun - Credentials',
    'invite_email' => 'Akun - Invite',
    'license' => 'License Key',
    'link' => 'Access Link',
);
?>

<style>
    .products-page .products-shell{width:100%}
    .products-page .product-table thead th{font-size:12px;letter-spacing:.04em;text-transform:uppercase}
    .products-page .product-name{color:#dce8ff;font-weight:650;line-height:1.18}
    .products-page .fulfillment-pill,.products-page .variation-pill{align-items:center;border-radius:999px;display:inline-flex;font-size:12px;font-weight:650;line-height:1;padding:6px 11px}
    .products-page .fulfillment-pill{background:rgba(124,88,255,.20);border:1px solid rgba(151,121,255,.55);color:#b890ff}
    .products-page .variation-pill{background:rgba(47,124,255,.18);color:#78a7ff;justify-content:center;min-width:36px}
    .products-page .status-pill{background:rgba(0,168,116,.16);border:1px solid rgba(0,168,116,.32);border-radius:999px;color:#00d18f;display:inline-flex;font-size:12px;font-weight:700;padding:5px 10px}
    .products-page .status-pill.inactive{background:rgba(125,143,176,.14);border-color:rgba(125,143,176,.24);color:#9aa9c4}
    .products-page .price-default,.products-page .weighted-hpp{color:#fff;font-weight:800;white-space:nowrap}
    .products-page .weighted-hpp{color:#ffad00}
    .products-page .row-subtext{color:#6f8fbd;display:block;font-size:11px;margin-top:2px}
    .products-page .product-action{align-items:center;display:inline-flex;gap:6px;height:31px}
    .products-page .variation-item{border-bottom:1px solid rgba(141,160,189,.12);padding:9px 8px}
    .products-page .variation-item:last-child{border-bottom:0}
    .products-page .variation-name{color:#dce8ff;font-size:13px;font-weight:650}
    .products-page .variation-price{color:#00d18f;font-size:12px;font-weight:750;white-space:nowrap}
    .products-page .variation-meta{color:#7f96bb;display:block;font-size:11px;margin-top:3px}
    .products-page .product-detail-row td{background:#252a3f!important;border-top:0!important;padding:0!important}
    .products-page .product-detail-panel{background:#252a3f;border-top:1px solid rgba(141,160,189,.14);padding:16px 48px}
    .products-page .variation-list-row{align-items:center;border-bottom:1px solid rgba(141,160,189,.10);display:grid;gap:14px;grid-template-columns:28px minmax(200px,1fr) 130px 90px 78px;padding:10px 0}
    .products-page .variation-list-row:last-child{border-bottom:0}
    .products-page .variation-chip{background:rgba(124,88,255,.20);border-radius:999px;color:#b890ff;display:inline-flex;font-size:12px;font-weight:750;padding:5px 11px}
    .products-page .detail-section{border-top:1px solid rgba(141,160,189,.12);margin-top:14px;padding-top:14px}
    .products-page .detail-section-title{color:#7f96bb;font-size:12px;font-weight:800;letter-spacing:.05em;margin-bottom:10px;text-transform:uppercase}
    .products-page .store-price-grid{display:grid;gap:10px 62px;grid-template-columns:repeat(4,minmax(140px,1fr));max-width:1040px}
    .products-page .store-price-field label{color:#6f8fbd;font-size:12px;margin-bottom:4px}
    .products-page .store-price-field .input-group-text{background:transparent!important;border:0!important;color:#7f96bb!important;padding-left:0}
    .products-page .store-price-field .form-control{height:26px;min-height:26px;padding:2px 10px}
    .products-page .batch-grid{align-items:end;display:grid;gap:10px;grid-template-columns:90px 140px 140px minmax(220px,1fr) auto}
    .products-page .batch-grid label{color:#6f8fbd;font-size:12px;margin-bottom:4px}
    .products-page .detail-help{color:#6f8fbd;font-size:12px;margin-top:8px}
    .products-page .product-modal .modal-dialog{max-width:500px}
    .products-page .product-modal .modal-content{border-radius:14px}
    .products-page .product-modal .modal-header{border-bottom:0;padding-bottom:8px}
    .products-page .product-modal .modal-title{align-items:center;display:flex;font-weight:800;gap:9px}
    .products-page .product-modal .modal-title i{color:#9568ff;font-size:22px}
    .products-page .product-modal .modal-footer{border-top:0;padding-top:8px}
    .products-page .product-modal .form-label{color:#9fc0ec!important;font-weight:700}
    .products-page .product-modal .form-control,.products-page .product-modal .form-select{border-radius:9px;min-height:40px}
    .products-page .product-table-controls{align-items:center;display:flex;flex-wrap:wrap;gap:12px;justify-content:space-between;padding:8px 10px}
    .products-page .product-table-controls .form-select{width:64px}
    .products-page .product-table-controls .form-control{width:210px}
    .products-page .product-pagination{align-items:center;border-top:1px solid rgba(141,160,189,.22);display:flex;flex-wrap:wrap;gap:12px;justify-content:space-between;padding:10px}
    .products-page .product-pages{align-items:center;display:flex;gap:4px;margin-left:auto}
    .products-page .product-pages .btn{align-items:center;background:#061426;border:1px solid #245fbd;border-radius:4px;color:#6f9ff0;display:inline-flex;font-size:14px;height:32px;justify-content:center;line-height:1;min-width:36px;padding:0 11px}
    .products-page .product-pages .btn:hover{background:rgba(47,110,217,.18);color:#fff}
    .products-page .product-pages .btn.btn-primary{background:#1f55b5;border-color:#2f6ed9;color:#fff}
    .products-page .product-pages .btn.disabled{background:#061426;border-color:#142d49;color:#8da0bd;opacity:1}
    @media (max-width:991.98px){.products-page .store-price-grid{grid-template-columns:repeat(2,minmax(140px,1fr))}.products-page .batch-grid{grid-template-columns:1fr 1fr}.products-page .variation-list-row{grid-template-columns:28px minmax(160px,1fr) 110px}.products-page .variation-list-row .variation-state,.products-page .variation-list-row .variation-actions{grid-column:2 / -1}}
    @media (max-width:575.98px){.products-page .product-detail-panel{padding:14px 18px}.products-page .store-price-grid,.products-page .batch-grid{grid-template-columns:1fr}}
</style>

<div class="pagetitle">
    <h1>Nama Produk</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard'); ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Nama Produk</li>
        </ol>
    </nav>
</div>

<section class="section products-page">
    <div class="products-shell">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <p class="text-muted mb-0">Kelola daftar nama produk dan variasi harga.</p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProductModal">
                <i class="bi bi-plus-circle"></i> Tambah Produk
            </button>
        </div>

        <div class="card datatable-card">
            <div class="card-body p-0">
                <div class="product-table-controls">
                    <label class="d-flex align-items-center gap-2 mb-0">
                        <select class="form-select form-select-sm" id="productPerPage">
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span>entries per page</span>
                    </label>
                    <input type="text" class="form-control" id="productSearch" placeholder="Search...">
                </div>
                <div class="table-responsive">
                    <table class="table align-middle product-table mb-0" id="productTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Produk</th>
                                <th>Fulfillment</th>
                                <th class="text-end">Harga Default</th>
                                <th class="text-end">Weighted HPP</th>
                                <th>Variasi</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($products): foreach ($products as $index => $product): ?>
                            <?php $weightedHpp = $product->accounts_count > 0 ? $product->accounts_avg_hpp : 0; ?>
                            <tr class="product-main-row" data-search="<?= h(strtolower($product->name.' '.$product->method)); ?>">
                                <td class="text-muted"><?= $index + 1; ?></td>
                                <td><div class="product-name"><?= h($product->name); ?></div></td>
                                <td><span class="fulfillment-pill"><?= h($methodBadge[$product->method] ?? $product->method); ?></span></td>
                                <td class="text-end"><span class="price-default"><?= $money($product->hpp); ?></span></td>
                                <td class="text-end"><span class="weighted-hpp"><?= $money($weightedHpp); ?></span><span class="row-subtext"><?= number_format($product->accounts_count); ?> unit - 0 batch</span></td>
                                <td>
                                    <?php if ($product->variations_count > 0): ?>
                                        <button class="variation-pill border-0 dropdown-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#productDetail<?= $product->id; ?>" aria-expanded="false" aria-controls="productDetail<?= $product->id; ?>"><?= (int) $product->variations_count; ?></button>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="status-pill <?= !isset($product->is_active) || $product->is_active ? '' : 'inactive'; ?>"><?= !isset($product->is_active) || $product->is_active ? 'Aktif' : 'Nonaktif'; ?></span></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary product-action" data-bs-toggle="modal" data-bs-target="#variationProductModal<?= $product->id; ?>"><i class="bi bi-plus-lg"></i> Variasi</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" title="Edit" data-bs-toggle="modal" data-bs-target="#editProductModal<?= $product->id; ?>"><i class="bi bi-pencil"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus" data-bs-toggle="modal" data-bs-target="#deleteProductModal<?= $product->id; ?>"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            <?php if ($product->variations_count > 0): ?>
                                <tr class="product-detail-row">
                                    <td colspan="8">
                                        <div class="collapse" id="productDetail<?= $product->id; ?>">
                                            <div class="product-detail-panel">
                                                <?php $variationNo = 1; foreach ($variations as $variation): if ((int) $variation->digital_product_id === (int) $product->id): ?>
                                                    <div class="variation-list-row">
                                                        <div class="text-muted"><?= $variationNo++; ?>.</div>
                                                        <div><span class="variation-chip"><?= h($variation->label); ?></span></div>
                                                        <div class="variation-price"><?= $money($variation->sale_price ?? 0); ?></div>
                                                        <div class="variation-state text-success">✓ Aktif</div>
                                                        <div class="variation-actions text-end">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                                        </div>
                                                    </div>
                                                <?php endif; endforeach; ?>

                                                <div class="detail-section">
                                                    <div class="detail-section-title"><i class="bi bi-shop text-info"></i> Harga Per Toko</div>
                                                    <div class="store-price-grid">
                                                        <?php if ($stores): foreach ($stores as $store): ?>
                                                            <div class="store-price-field">
                                                                <label><?= h($store->shop_name); ?></label>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text">Rp</span>
                                                                    <input type="number" class="form-control" value="0" min="0" step="100">
                                                                </div>
                                                            </div>
                                                        <?php endforeach; else: ?>
                                                            <div class="store-price-field">
                                                                <label>Default</label>
                                                                <div class="input-group input-group-sm"><span class="input-group-text">Rp</span><input type="number" class="form-control" value="0" min="0" step="100"></div>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="store-price-field d-flex align-items-end">
                                                            <button type="button" class="btn btn-primary btn-sm"><i class="bi bi-check-circle"></i> Simpan</button>
                                                        </div>
                                                    </div>
                                                    <div class="detail-help">Kosongkan = pakai harga default (<?= $money($product->hpp); ?>)</div>
                                                </div>

                                                <div class="detail-section">
                                                    <div class="detail-section-title"><i class="bi bi-coin text-warning"></i> Batch Pembelian &amp; HPP Tertimbang</div>
                                                    <div class="mb-2 text-muted">Weighted HPP saat ini: <span class="weighted-hpp"><?= $money($weightedHpp); ?></span></div>
                                                    <div class="batch-grid">
                                                        <div><label>Qty Unit</label><input type="number" class="form-control form-control-sm" min="1"></div>
                                                        <div><label>Unit Cost (Rp)</label><input type="number" class="form-control form-control-sm" min="0" step="100"></div>
                                                        <div><label>Tanggal</label><input type="date" class="form-control form-control-sm" value="<?= date('Y-m-d'); ?>"></div>
                                                        <div><label>Catatan</label><input type="text" class="form-control form-control-sm" placeholder="opsional"></div>
                                                        <button type="button" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle"></i> Simpan Batch</button>
                                                    </div>
                                                    <div class="detail-help">Belum ada batch pembelian.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; else: ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">Belum ada produk.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="product-pagination">
                    <div id="productInfo" class="text-muted">Showing 0 to 0 of 0 entries</div>
                    <div class="product-pages" id="productPages"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade product-modal" id="createProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form action="<?= site_url('products/store'); ?>" method="POST" class="modal-content">
                <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-lg"></i> Tambah Nama Produk</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><?php $product = null; include APPPATH.'views/products/partials/form.php'; ?></div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan</button></div>
            </form>
        </div>
    </div>

    <?php foreach ($products as $product): ?>
        <div class="modal fade product-modal" id="editProductModal<?= $product->id; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form action="<?= site_url('products/update/'.$product->id); ?>" method="POST" class="modal-content">
                    <div class="modal-header"><h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Nama Produk</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body"><?php include APPPATH.'views/products/partials/form.php'; ?></div>
                    <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan</button></div>
                </form>
            </div>
        </div>

        <div class="modal fade product-modal" id="variationProductModal<?= $product->id; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form action="<?= site_url('products/'.$product->id.'/variations'); ?>" method="POST" class="modal-content">
                    <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-lg"></i> Tambah Variasi</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Produk</label><input type="text" class="form-control" value="<?= h($product->name); ?>" disabled></div>
                        <div class="mb-3"><label class="form-label">Nama Variasi <span class="text-danger">*</span></label><input type="text" name="label" class="form-control" placeholder="cth: Sharing 3 User 1 Bulan" required></div>
                        <div class="mb-3"><label class="form-label">Harga Jual (Rp) <span class="text-danger">*</span></label><input type="number" name="sale_price" class="form-control" min="0" step="100" placeholder="cth: 35000" required></div>
                        <div class="mb-3"><label class="form-label">HPP / COGS (Rp)</label><input type="number" name="hpp" class="form-control" value="0" min="0" step="100"><div class="form-text">Harga pokok - digunakan di laporan keuangan.</div></div>
                        <?php if ($product->variations_count > 0): ?>
                            <div class="small text-muted">Variasi saat ini:
                                <?php $names = array(); foreach ($variations as $variation) { if ((int) $variation->digital_product_id === (int) $product->id) $names[] = $variation->label; } echo h(implode(', ', $names)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan</button></div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="deleteProductModal<?= $product->id; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Hapus Produk</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">Yakin ingin menghapus produk <strong><?= h($product->name); ?></strong>? Akun yang sudah ada tidak ikut terhapus.</div>
                    <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><a href="<?= site_url('products/delete/'.$product->id); ?>" class="btn btn-danger">Hapus</a></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rows = Array.from(document.querySelectorAll('#productTable tbody .product-main-row'));
    const perPage = document.getElementById('productPerPage');
    const search = document.getElementById('productSearch');
    const info = document.getElementById('productInfo');
    const pages = document.getElementById('productPages');
    let currentPage = 1;

    function detailRow(row) {
        const next = row.nextElementSibling;
        return next && next.classList.contains('product-detail-row') ? next : null;
    }

    function filteredRows() {
        const term = (search.value || '').toLowerCase().trim();
        return rows.filter(function (row) {
            return !term || row.dataset.search.indexOf(term) !== -1;
        });
    }

    function render() {
        const visible = filteredRows();
        const size = parseInt(perPage.value, 10) || 10;
        const totalPages = Math.max(1, Math.ceil(visible.length / size));
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * size;
        const end = start + size;

        rows.forEach(function (row) {
            row.style.display = 'none';
            const detail = detailRow(row);
            if (detail) detail.style.display = 'none';
        });

        visible.slice(start, end).forEach(function (row) {
            row.style.display = '';
            const detail = detailRow(row);
            if (detail) detail.style.display = '';
        });

        const from = visible.length ? start + 1 : 0;
        const to = Math.min(end, visible.length);
        info.textContent = 'Showing ' + from + ' to ' + to + ' of ' + visible.length + ' entries';

        pages.innerHTML = '';
        for (let page = 1; page <= totalPages; page++) {
            if (totalPages > 8 && page > 6 && page < totalPages) {
                if (page === 7) {
                    const dots = document.createElement('span');
                    dots.className = 'btn btn-sm btn-outline-secondary disabled';
                    dots.textContent = '...';
                    pages.appendChild(dots);
                }
                continue;
            }
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn btn-sm ' + (page === currentPage ? 'btn-primary' : 'btn-outline-primary');
            button.textContent = page;
            button.addEventListener('click', function () {
                currentPage = page;
                render();
            });
            pages.appendChild(button);
        }
    }

    if (perPage && search && info && pages) {
        perPage.addEventListener('change', function () { currentPage = 1; render(); });
        search.addEventListener('input', function () { currentPage = 1; render(); });
        render();
    }
});
</script>

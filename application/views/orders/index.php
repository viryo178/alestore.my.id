<?php
$orderProductsPayload = array();
foreach ($products as $product) {
    $orderProductsPayload[] = array(
        'id' => (int) $product->id,
        'name' => $product->name,
        'account_type' => $product->account_type ?: 'private',
        'price' => (float) ($product->hpp ?: 0),
    );
}

$orderVariationsPayload = array();
foreach ($variations as $variation) {
    $orderVariationsPayload[] = array(
        'id' => (int) $variation->id,
        'product_id' => (int) $variation->digital_product_id,
        'label' => $variation->label,
        'price' => (float) ($variation->sale_price ?: 0),
    );
}
?>

<style>
    .orders-page .order-toolbar{align-items:center;display:flex;flex-wrap:wrap;gap:10px;justify-content:space-between;margin-bottom:22px}
    .orders-page .order-actions{display:flex;flex-wrap:wrap;gap:8px;justify-content:flex-end}
    .orders-page .filter-card .form-label{color:#9fc0ec!important;font-size:12px;font-weight:800}
    .orders-page .orders-table thead th{background:#20263b!important;border-bottom:1px solid rgba(141,160,189,.18)!important;color:#7f96bb!important;font-size:11px;letter-spacing:.04em;padding:11px 12px!important;text-transform:uppercase;white-space:nowrap}
    .orders-page .orders-table tbody td{border-bottom:1px solid rgba(141,160,189,.09)!important;padding:12px!important;vertical-align:middle}
    .orders-page .order-code{color:#3d8bff;font-size:12px;font-weight:600}.orders-page .order-main,.orders-page .order-product,.orders-page .order-buyer{color:#dce8ff;font-weight:500}
    .orders-page .order-subtext{color:#6f8fbd;display:block;font-size:11px;margin-top:2px}.orders-page .price-text,.orders-page .expired-text{color:#00d18f;font-weight:600;white-space:nowrap}
    .orders-page .type-pill,.orders-page .renewal-pill,.orders-page .refund-pill{border-radius:999px;display:inline-flex;font-size:11px;font-weight:600;padding:5px 9px;white-space:nowrap}
    .orders-page .type-pill{background:rgba(0,157,214,.16);color:#25c4f2}.orders-page .type-pill.sharing{background:rgba(124,88,255,.18);color:#a777ff}
    .orders-page .renewal-pill{background:rgba(47,124,255,.16);color:#75a8ff}.orders-page .refund-pill{background:rgba(0,168,116,.14);color:#00d18f}
    .orders-page .account-given{color:#fff;font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;font-size:12px;font-weight:500}.orders-page .record-only,.orders-page .admin-text,.orders-page .date-text{color:#7f96bb;font-size:12px}
    .orders-page .order-action-group{display:inline-flex;gap:7px;justify-content:flex-end;white-space:nowrap}.orders-page .order-action-group .btn{align-items:center;display:inline-flex;height:30px;justify-content:center;padding:0;width:30px}
    .orders-page .manual-order-modal .modal-dialog{max-width:780px}.orders-page .manual-order-modal .modal-content{background:#1d2236!important;border:1px solid rgba(86,126,214,.34)!important}.orders-page .manual-order-modal .modal-header{background:#0b0e15!important;border-bottom-color:rgba(86,126,214,.24)!important}.orders-page .manual-order-modal .form-label{color:#9fc0ec!important;font-size:12px;font-weight:800;margin-bottom:6px}.orders-page .manual-order-modal .form-text{color:#617da9!important;font-size:11px}.orders-page .manual-order-modal .form-control,.orders-page .manual-order-modal .form-select{background:#080b14!important;border-color:#314266!important;color:#fff!important;min-height:42px}.orders-page .manual-order-modal .form-control:focus,.orders-page .manual-order-modal .form-select:focus{border-color:#2f7cff!important;box-shadow:0 0 0 .18rem rgba(47,124,255,.18)!important}.orders-page .manual-toolbar{display:flex;flex-wrap:wrap;gap:10px;justify-content:space-between;margin-bottom:16px}.orders-page .manual-stock-grid{display:grid;gap:16px;grid-template-columns:repeat(2,minmax(0,1fr));margin-bottom:16px}.orders-page .manual-stock-card{align-items:center;background:#20263b;border:1px solid rgba(86,126,214,.32);border-radius:9px;display:flex;gap:14px;padding:18px}.orders-page .manual-stock-icon{align-items:center;background:rgba(47,124,255,.16);border-radius:9px;color:#72a6ff;display:inline-flex;font-size:22px;height:48px;justify-content:center;width:48px}.orders-page .manual-stock-icon.sharing{background:rgba(124,88,255,.18);color:#b28cff}.orders-page .manual-stock-label{color:#7f96bb;font-size:12px}.orders-page .manual-stock-value{color:#fff;font-size:30px;font-weight:900;line-height:1}.orders-page .manual-stock-help{color:#9fc0ec;font-size:12px}.orders-page .manual-order-card{background:#20263b;border:1px solid rgba(86,126,214,.28);border-radius:12px;padding:24px}.orders-page .manual-title{align-items:center;color:#fff;display:flex;font-weight:900;gap:8px;margin-bottom:16px}.orders-page .mode-tabs{border:1px solid rgba(141,160,189,.18);display:grid;grid-template-columns:1fr 1fr;margin-bottom:8px}.orders-page .mode-tab{align-items:center;background:transparent;border:0;color:#7f96bb;display:flex;font-size:13px;font-weight:800;gap:8px;justify-content:center;min-height:42px}.orders-page .mode-tab.active{background:rgba(0,168,116,.22);box-shadow:inset 0 0 0 1px #00a874;color:#fff}.orders-page .order-product-alert{background:rgba(0,168,116,.15);border:1px solid #00a874;border-radius:8px;color:#6df0c3;font-size:12px;font-weight:800;padding:11px 13px}.orders-page .order-info-box{background:rgba(47,124,255,.16);border:1px solid rgba(92,142,255,.48);border-radius:9px;color:#9fc0ff;font-size:12px;line-height:1.45;padding:13px 15px}.orders-page .manual-footer-sticky{background:#1d2236;border-top:1px solid rgba(86,126,214,.22);bottom:0;display:flex;gap:8px;justify-content:flex-end;position:sticky}
    .orders-page .manual-order-modal .modal-content{background:#071b31!important;border-color:#142d49!important;color:#f8fbff!important}
    .orders-page .manual-order-modal .modal-header,.orders-page .manual-order-modal .modal-footer{background:#06172d!important;border-color:rgba(47,124,255,.14)!important}
    .orders-page .manual-order-modal .modal-title,.orders-page .manual-order-modal h5,.orders-page .manual-order-modal strong{color:#f8fbff!important}
    .orders-page .manual-order-modal .modal-body{background:#071b31!important;scrollbar-width:none;-ms-overflow-style:none}
    .orders-page .manual-order-modal .modal-body::-webkit-scrollbar{display:none;height:0;width:0}
    .orders-page .manual-order-modal .manual-order-card,.orders-page .manual-order-modal .manual-stock-card{background:#071b31!important;border-color:#142d49!important}
    .orders-page .manual-order-modal .form-control,.orders-page .manual-order-modal .form-select{background:#061426!important;border-color:#142d49!important;color:#f8fbff!important}
    .orders-page .manual-order-modal .btn-close{filter:invert(1) grayscale(100%) brightness(180%);opacity:.8}
    .orders-page .quick-order-modal .modal-dialog{max-width:min(1640px,calc(100vw - 24px))}
    .orders-page .quick-order-table{min-width:1530px}.orders-page .quick-order-table th{color:#7f96bb;font-size:11px;letter-spacing:.04em;text-transform:uppercase;white-space:nowrap}.orders-page .quick-order-table td{vertical-align:middle}
    .orders-page .quick-order-table .form-control,.orders-page .quick-order-table .form-select{min-height:34px}.orders-page .quick-order-index{color:#7f96bb;min-width:24px}
    @media (max-width:767.98px){.orders-page .order-toolbar{align-items:stretch;flex-direction:column}.orders-page .order-actions{justify-content:flex-start}}
</style>

<div class="pagetitle">
    <h1>Riwayat Order</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard'); ?>">Dashboard</a></li><li class="breadcrumb-item active">Riwayat Order</li></ol></nav>
</div>

<section class="section orders-page">
    <div class="order-toolbar">
        <div class="text-muted">Total: <?= number_format(count($rows)); ?> order</div>
        <div class="order-actions">
            <a href="<?= site_url('renewal-pipeline'); ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-arrow-repeat"></i> Renewal Pipeline</a>
            <a href="<?= site_url('financial-reports/download'); ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-file-earmark-spreadsheet"></i> Export CSV</a>
            <button type="button" class="btn btn-outline-primary btn-sm"><i class="bi bi-upload"></i> Import CSV</button>
            <a href="<?= site_url('financial-reports'); ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-check2-square"></i> Rekonsiliasi</a>
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#quickOrderModal"><i class="bi bi-list-check"></i> Quick Order</button>
        </div>
    </div>

    <div class="card filter-card">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-lg-2"><label class="form-label">Cari Order</label><input type="text" name="q" class="form-control" value="<?= h($this->input->get('q')); ?>" placeholder="Search..."></div>
                <div class="col-lg-2"><label class="form-label">Toko</label><select name="store_id" class="form-select"><option value="all">Semua Toko</option><?php foreach ($stores as $store): ?><option value="<?= $store->id; ?>" <?= (string) $this->input->get('store_id') === (string) $store->id ? 'selected' : ''; ?>><?= h($store->shop_name); ?></option><?php endforeach; ?></select></div>
                <div class="col-lg-2"><label class="form-label">Admin</label><select name="user_id" class="form-select"><option value="all">Semua Admin</option><?php foreach ($admins as $admin): ?><option value="<?= (int) $admin->id; ?>" <?= (string) $this->input->get('user_id') === (string) $admin->id ? 'selected' : ''; ?>><?= h($admin->name); ?></option><?php endforeach; ?></select></div>
                <div class="col-lg-1"><label class="form-label">Tipe</label><select name="order_type" class="form-select"><option value="all">Semua</option><option value="private" <?= $this->input->get('order_type') === 'private' ? 'selected' : ''; ?>>Private</option><option value="sharing" <?= $this->input->get('order_type') === 'sharing' ? 'selected' : ''; ?>>Sharing</option></select></div>
                <div class="col-lg-1"><label class="form-label">Expired Buyer</label><select name="expired_buyer" class="form-select"><option value="all">Semua</option><option value="active" <?= $this->input->get('expired_buyer') === 'active' ? 'selected' : ''; ?>>Aktif</option><option value="expired" <?= $this->input->get('expired_buyer') === 'expired' ? 'selected' : ''; ?>>Expired</option></select></div>
                <div class="col-lg-2"><label class="form-label">Dari Tanggal</label><input type="date" name="from" class="form-control" value="<?= h($this->input->get('from')); ?>"></div>
                <div class="col-lg-2s"><label class="form-label">Sampai</label><input type="date" name="to" class="form-control" value="<?= h($this->input->get('to')); ?>"></div>
                <div class="col-lg-12 d-flex flex-wrap justify-content-end gap-2"><button class="btn btn-primary"><i class="bi bi-search"></i> Filter</button><a href="<?= site_url('orders'); ?>" class="btn btn-outline-secondary">Reset</a></div>
            </form>
        </div>
    </div>

    <div class="card datatable-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table datatable align-middle orders-table">
                    <thead><tr><th>No. Pesanan</th><th>Buyer</th><th>Toko</th><th>Produk</th><th>Akun Diberikan</th><th>Tipe</th><th>Harga</th><th>Expired</th><th>Admin</th><th>Tanggal</th><th>Renewal</th><th>Refund</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                    <?php if ($rows): foreach ($rows as $order): ?>
                        <?php
                        $orderCode = $order->shopee_order_id ?: 'MANUAL-'.$order->id;
                        $buyerName = $order->customer_name ?: ($order->buyer_email ?: 'Buyer '.$orderCode);
                        $orderType = $order->order_type ?: 'private';
                        $expired = $order->expired_at ?: date('Y-m-d H:i:s', strtotime($order->created_at.' +30 days'));
                        $daysDiff = floor((strtotime($expired) - strtotime(date('Y-m-d H:i:s'))) / 86400);
                        $expiredLabel = $daysDiff < 0 ? 'Expired' : ($daysDiff === 0 ? 'Hari ini' : $daysDiff.' hari lagi');
                        $adminName = !empty($order->admin_name) ? $order->admin_name : '-';
                        $statusLabels = array('pending' => 'Belum Diproses', 'processing' => 'Diproses', 'completed' => 'Selesai', 'cancelled' => 'Batal');
                        $statusLabel = isset($statusLabels[$order->status]) ? $statusLabels[$order->status] : ucfirst($order->status);
                        ?>
                        <tr>
                            <td><div class="order-code"><?= h($orderCode); ?></div><span class="order-subtext">Internal: ORD-<?= strtoupper(dechex($order->id)); ?></span></td>
                            <td><span class="order-buyer"><?= h($buyerName); ?></span></td>
                            <td><span class="order-main"><?= h($order->shop_name ?: '-'); ?></span></td>
                            <td><span class="order-product"><?= h($order->product_name ?: 'Produk Manual'); ?></span><span class="order-subtext"><i class="bi bi-tag-fill text-warning"></i> <?= h($order->variation ?: 'Manual'); ?></span></td>
                            <td><?= !empty($order->account_username) ? '<span class="account-given">'.h($order->account_username).'</span>' : '<span class="record-only">Record Only</span>'; ?></td>
                            <td><span class="type-pill <?= $orderType === 'sharing' ? 'sharing' : ''; ?>"><?= h(ucfirst($orderType)); ?></span></td>
                            <td><span class="price-text"><?= rupiah($order->total); ?></span></td>
                            <td><span class="expired-text"><?= date('d/m/Y', strtotime($expired)); ?></span><span class="order-subtext"><?= h($expiredLabel); ?></span></td>
                            <td><span class="admin-text"><?= h($adminName); ?></span></td>
                            <td><span class="date-text"><?= date('d/m/Y', strtotime($order->created_at)); ?></span><span class="order-subtext"><?= date('H:i', strtotime($order->created_at)); ?></span></td>
                            <td><span class="renewal-pill"><?= h($statusLabel); ?></span></td>
                            <td><span class="refund-pill">Normal</span></td>
                            <td class="text-end"><div class="order-action-group"><button type="button" class="btn btn-sm btn-outline-primary edit-order-btn" title="Edit" data-bs-toggle="modal" data-bs-target="#editOrderModal" data-id="<?= (int) $order->id; ?>" data-code="<?= h($order->shopee_order_id); ?>" data-store-id="<?= h($order->shopee_store_id); ?>" data-status="<?= h($order->status); ?>" data-type="<?= h($orderType); ?>" data-product="<?= h($order->product_name); ?>" data-variation="<?= h($order->variation); ?>" data-email="<?= h($order->buyer_email); ?>" data-account-username="<?= h($order->account_username ?? ''); ?>" data-account-password="<?= h($order->account_password ?? ''); ?>" data-account-max-user="<?= h($order->account_max_user ?? 1); ?>" data-total="<?= h($order->total); ?>" data-expired="<?= $order->expired_at ? h(date('Y-m-d\TH:i', strtotime($order->expired_at))) : ''; ?>"><i class="bi bi-pencil-square"></i></button><a href="<?= site_url('orders/show/'.$order->id); ?>" class="btn btn-sm btn-outline-primary" title="Lihat"><i class="bi bi-eye"></i></a><a onclick="return confirm('Hapus order?')" href="<?= site_url('orders/delete/'.$order->id); ?>" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></a></div></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="13" class="text-center text-muted py-4">Belum ada order. Klik Input Order Baru untuk menambahkan data manual.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade manual-order-modal" id="editOrderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form method="POST" action="#" class="modal-content" id="editOrderForm">
                <div class="modal-header">
                    <div><h5 class="modal-title">Edit Order</h5><div class="small text-muted">Ubah data order yang sudah tercatat.</div></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="manual-order-card">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">No. Pesanan Marketplace</label>
                                <input type="text" name="shopee_order_id" class="form-control" id="editOrderCode">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Toko</label>
                                <select name="shopee_store_id" class="form-select" id="editOrderStore">
                                    <option value="">Pilih Toko</option>
                                    <?php foreach ($stores as $store): ?><option value="<?= $store->id; ?>"><?= h($store->shop_name); ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" id="editOrderStatus">
                                    <option value="pending">Pending</option>
                                    <option value="processing">Diproses</option>
                                    <option value="completed">Selesai</option>
                                    <option value="cancelled">Batal</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipe Akun</label>
                                <select name="order_type" class="form-select" id="editOrderType">
                                    <option value="private">Private</option>
                                    <option value="sharing">Sharing</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Harga Jual (Rp)</label>
                                <input type="number" name="total" class="form-control" id="editOrderTotal" min="0" step="100" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Produk</label>
                                <select name="product_name" class="form-select order-product-select" id="editOrderProduct" required>
                                    <option value="">Pilih Produk</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?= h($product->name); ?>" data-id="<?= (int) $product->id; ?>"><?= h($product->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Mengganti produk akan menyesuaikan tipe dan harga default.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Variasi Harga</label>
                                <select name="variation" class="form-select order-variation-select" id="editOrderVariation">
                                    <option value="">Pilih variasi</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Buyer</label>
                                <input type="email" name="buyer_email" class="form-control" id="editOrderEmail">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Max User</label>
                                <input type="number" name="account_max_user" class="form-control" id="editOrderMaxUser" value="1" min="1" step="1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Username / Email Akun Diberikan</label>
                                <input type="text" name="account_username" class="form-control" id="editOrderAccountUsername">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password Akun Diberikan</label>
                                <input type="text" name="account_password" class="form-control" id="editOrderAccountPassword">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Expired</label>
                                <input type="datetime-local" name="expired_at" class="form-control" id="editOrderExpired">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer manual-footer-sticky">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade manual-order-modal" id="manualOrderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form method="POST" action="<?= site_url('orders/store'); ?>" class="modal-content" id="manualOrderForm">
                <input type="hidden" name="created_at_client" class="order-created-at-client">
                <div class="modal-header">
                    <div><h5 class="modal-title">Input Order Baru</h5></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="manual-toolbar">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-dismiss="modal"><i class="bi bi-arrow-left"></i> Kembali</button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#quickOrderModal" data-bs-dismiss="modal"><i class="bi bi-list-check"></i> Quick Order (Bulk)</button>
                            <button type="button" class="btn btn-outline-primary btn-sm"><i class="bi bi-upload"></i> Import CSV</button>
                        </div>
                    </div>

                    <div class="manual-stock-grid">
                        <div class="manual-stock-card">
                            <span class="manual-stock-icon"><i class="bi bi-lock-fill"></i></span>
                            <div>
                                <div class="manual-stock-label">Stok Private</div>
                                <div class="manual-stock-value"><?= number_format((int) ($private_stock ?? 0)); ?></div>
                                <div class="manual-stock-help">akun available</div>
                            </div>
                        </div>
                        <div class="manual-stock-card">
                            <span class="manual-stock-icon sharing"><i class="bi bi-link-45deg"></i></span>
                            <div>
                                <div class="manual-stock-label">Stok Sharing</div>
                                <div class="manual-stock-value"><?= number_format((int) ($sharing_stock ?? 0)); ?></div>
                                <div class="manual-stock-help">akun ada slot</div>
                            </div>
                        </div>
                    </div>

                    <div class="manual-order-card">
                        <div class="manual-title"><i class="bi bi-cart-check"></i> Form Input Order</div>
                        <p class="text-muted small mb-3">Pilih mode input: auto assign akun atau catat order saja.</p>

                        <label class="form-label">Mode Assign Akun</label>
                        <input type="hidden" name="status" id="manualStatus" value="completed">
                        <div class="mode-tabs" role="group" aria-label="Mode assign akun">
                            <button type="button" class="mode-tab active" data-mode="auto"><i class="bi bi-lightning-charge-fill text-warning"></i> Auto Assign</button>
                            <button type="button" class="mode-tab" data-mode="record"><i class="bi bi-file-earmark-text text-danger"></i> Catat Saja</button>
                        </div>
                        <div class="small mb-3" id="manualModeHelp"><strong class="text-success">Mode aktif: Auto Assign</strong><br><span class="text-muted">Auto: sistem otomatis pilih akun.</span></div>

                        <div class="mb-3">
                            <label class="form-label">No. Pesanan Marketplace</label>
                            <input type="text" name="shopee_order_id" class="form-control" placeholder="Contoh: 2503031234ABCDE (Shopee) atau INV/20260303/MPL/12345 (Tokopedia)">
                            <div class="form-text">Bisa sama untuk checkout 2+ produk. Sistem tetap membuat nomor internal unik otomatis.</div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Toko <span class="text-danger">*</span></label>
                                <select name="shopee_store_id" class="form-select" required>
                                    <option value="">Pilih Toko</option>
                                    <?php foreach ($stores as $store): ?><option value="<?= $store->id; ?>"><?= h($store->shop_name); ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipe Akun</label>
                                <select name="order_type" class="form-select" id="manualOrderType" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="private">Private</option>
                                    <option value="sharing">Sharing</option>
                                </select>
                                <div class="form-text">Pilih private/sharing untuk auto assign akun.</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Produk <span class="text-danger">*</span></label>
                                <select name="product_name" class="form-select" id="manualProduct" required>
                                    <option value="">Pilih Produk</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?= h($product->name); ?>" data-id="<?= (int) $product->id; ?>"><?= h($product->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="mt-3 order-product-alert" id="manualProductHelp"><i class="bi bi-box-seam"></i> Pilih produk untuk cek stok.</div>
                            </div>

                            <div class="col-12" id="manualVariationWrap">
                                <label class="form-label">Variasi Harga</label>
                                <select name="variation" class="form-select" id="manualVariation">
                                    <option value="">Pilih variasi</option>
                                </select>
                                <div class="form-text">Variasi difilter otomatis dari produk yang dipilih.</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Email Buyer</label>
                                <input type="email" name="buyer_email" class="form-control" placeholder="buyer@gmail.com">
                                <div class="form-text">Email pembeli untuk keperluan kontak atau invite sharing.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Harga Jual (Rp)</label>
                                <input type="number" name="total" class="form-control" id="manualTotal" value="0" min="0" step="100" required>
                                <div class="form-text">Otomatis terisi dari produk/variasi, bisa di-override.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Durasi Expired</label>
                                <select name="expired_days" class="form-select" id="manualExpiredDays">
                                    <?php if ($durations): foreach ($durations as $duration): ?><option value="<?= (int) $duration->days; ?>" <?= $duration->is_default ? 'selected' : ''; ?>><?= h($duration->label); ?></option><?php endforeach; else: ?><option value="30">30 Hari</option><?php endif; ?>
                                </select>
                                <div class="form-text" id="manualExpiredText">Expired: -</div>
                            </div>

                            <div class="col-12">
                                <div class="border border-secondary border-opacity-25 p-3">
                                    <div class="d-flex flex-wrap justify-content-between gap-2">
                                        <div>
                                            <strong class="small">Item Tambahan (Multi-Produk)</strong>
                                            <div class="form-text">Item utama pakai field produk di atas. Tambah baris di bawah untuk checkout 2+ produk.</div>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#quickOrderModal" data-bs-dismiss="modal"><i class="bi bi-plus-circle"></i> Tambah Item</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Tanggal Order</label>
                                <input type="date" name="created_at_date" class="form-control" value="<?= today_sql_date(); ?>">
                                <div class="form-text">Default hari ini. Ubah untuk order lama/backdate.</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Catatan Order</label>
                                <textarea class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
                            </div>

                            <div class="col-12">
                                <div class="order-info-box"><i class="bi bi-info-circle-fill"></i> <strong>Auto-Assign</strong><br>Setelah klik "Simpan Order", sistem akan mencatat order dengan data produk, tipe akun, variasi, harga, dan expired yang sudah terisi otomatis.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer manual-footer-sticky">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-lightning-charge-fill"></i> Simpan Order</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade quick-order-modal" id="quickOrderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form method="POST" action="<?= site_url('orders/quick'); ?>" class="modal-content" id="quickOrderForm">
                <input type="hidden" name="created_at_client" class="order-created-at-client">
                <div class="modal-header"><div><h5 class="modal-title">Quick Order</h5><div class="small text-muted">Input beberapa order sekaligus tanpa pindah halaman.</div></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table quick-order-table align-middle mb-0">
                            <thead><tr><th>#</th><th>No. Order Marketplace</th><th>Toko *</th><th>Tipe *</th><th>Produk *</th><th>Variasi</th><th>Email</th><th>Harga</th><th>Durasi Expired</th><th></th></tr></thead>
                            <tbody id="quickOrderRows">
                            <?php for ($row = 0; $row < 3; $row++): ?>
                                <tr class="quick-order-row">
                                    <td class="quick-order-index"><?= $row + 1; ?></td>
                                    <td><input type="text" name="orders[<?= $row; ?>][shopee_order_id]" class="form-control" placeholder="e.g. 250303ABCDE"></td>
                                    <td><select name="orders[<?= $row; ?>][shopee_store_id]" class="form-select" required><?php foreach ($stores as $store): ?><option value="<?= $store->id; ?>"><?= h($store->shop_name); ?></option><?php endforeach; ?></select></td>
                                    <td><select name="orders[<?= $row; ?>][order_type]" class="form-select" required><option value="sharing">Sharing</option><option value="private">Private</option></select></td>
                                    <td>
                                        <select name="orders[<?= $row; ?>][product_name]" class="form-select quick-product-select" required>
                                            <option value="">-- Pilih Produk --</option>
                                            <?php foreach ($products as $product): ?><option value="<?= h($product->name); ?>" data-id="<?= (int) $product->id; ?>"><?= h($product->name); ?></option><?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="orders[<?= $row; ?>][variation]" class="form-select quick-variation-select">
                                            <option value="">-- Tanpa --</option>
                                            <?php foreach ($variations as $variation): ?>
                                                <?php
                                                $productName = '';
                                                foreach ($products as $product) {
                                                    if ((int) $product->id === (int) $variation->digital_product_id) {
                                                        $productName = $product->name;
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <option value="<?= h($variation->label); ?>" data-product="<?= h($productName); ?>" data-product-id="<?= (int) $variation->digital_product_id; ?>" data-price="<?= h($variation->sale_price ?? 0); ?>"><?= h($variation->label); ?><?= !empty($variation->sale_price) ? ' - '.rupiah($variation->sale_price) : ''; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="email" name="orders[<?= $row; ?>][buyer_email]" class="form-control" placeholder="email@gmail.com"></td>
                                    <td><input type="number" name="orders[<?= $row; ?>][total]" class="form-control" value="0" min="0" step="100" required></td>
                                    <td>
                                        <select name="orders[<?= $row; ?>][expired_days]" class="form-select">
                                            <?php if ($durations): foreach ($durations as $duration): ?><option value="<?= (int) $duration->days; ?>" <?= $duration->is_default ? 'selected' : ''; ?>><?= h($duration->label); ?></option><?php endforeach; else: ?><option value="30">30 Hari</option><?php endif; ?>
                                        </select>
                                    </td>
                                    <td><button type="button" class="btn btn-sm btn-outline-danger quick-remove-row" title="Hapus baris"><i class="bi bi-trash"></i></button></td>
                                </tr>
                            <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-primary" id="quickAddRow"><i class="bi bi-plus-lg"></i> Tambah Baris</button>
                    <div class="d-flex gap-2"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary"><i class="bi bi-lightning-charge-fill"></i> Proses Semua Order</button></div>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var orderProducts = <?= json_encode($orderProductsPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
    var orderVariations = <?= json_encode($orderVariationsPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
    var productById = {};
    orderProducts.forEach(function (product) {
        productById[String(product.id)] = product;
    });

    function rupiah(value) {
        return 'Rp' + (Number(value || 0)).toLocaleString('id-ID');
    }

    function currentWibSqlDateTime(dateOverride) {
        var parts = new Intl.DateTimeFormat('en-CA', {
            timeZone: 'Asia/Jakarta',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        }).formatToParts(new Date()).reduce(function (carry, part) {
            carry[part.type] = part.value;
            return carry;
        }, {});

        var datePart = dateOverride || (parts.year + '-' + parts.month + '-' + parts.day);
        return datePart + ' ' + parts.hour + ':' + parts.minute + ':' + parts.second;
    }

    document.querySelectorAll('#manualOrderForm, #quickOrderForm').forEach(function (form) {
        form.addEventListener('submit', function () {
            var dateField = form.querySelector('[name="created_at_date"]');
            var dateOverride = dateField ? dateField.value : '';
            form.querySelectorAll('.order-created-at-client').forEach(function (field) {
                field.value = currentWibSqlDateTime(dateOverride);
            });
        });
    });

    function selectedProduct(select) {
        if (!select || !select.selectedOptions.length) return null;
        var id = select.selectedOptions[0].dataset.id;
        return id ? productById[String(id)] : null;
    }

    function ensureOption(select, value, label) {
        if (!select || !value) return;
        var exists = Array.from(select.options).some(function (option) {
            return option.value === value;
        });
        if (!exists) {
            var option = document.createElement('option');
            option.value = value;
            option.textContent = label || value;
            select.appendChild(option);
        }
    }

    function populateVariationSelect(select, product, keepValue) {
        if (!select) return;

        select.innerHTML = '<option value="">Pilih variasi</option>';
        if (!product) return;

        orderVariations
            .filter(function (variation) { return String(variation.product_id) === String(product.id); })
            .forEach(function (variation) {
                var option = document.createElement('option');
                option.value = variation.label;
                option.dataset.price = variation.price;
                option.textContent = variation.label + (Number(variation.price) > 0 ? ' - ' + rupiah(variation.price) : '');
                select.appendChild(option);
            });

        if (keepValue) {
            ensureOption(select, keepValue, keepValue);
            select.value = keepValue;
        }
        if (!select.value && select.options.length === 2) {
            select.selectedIndex = 1;
        }
    }

    function setExpiredPreview() {
        var duration = document.getElementById('manualExpiredDays');
        var text = document.getElementById('manualExpiredText');
        if (!duration || !text) return;

        var days = parseInt(duration.value || '0', 10);
        if (!days) {
            text.textContent = 'Expired: -';
            return;
        }

        var date = new Date();
        date.setDate(date.getDate() + days);
        text.textContent = 'Expired: ' + date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }

    var manualProduct = document.getElementById('manualProduct');
    var manualVariation = document.getElementById('manualVariation');
    var manualOrderType = document.getElementById('manualOrderType');
    var manualTotal = document.getElementById('manualTotal');
    var manualProductHelp = document.getElementById('manualProductHelp');
    var manualStatus = document.getElementById('manualStatus');
    var manualModeHelp = document.getElementById('manualModeHelp');

    function populateManualVariations(product, keepValue) {
        populateVariationSelect(manualVariation, product, keepValue);
    }

    function syncManualOrder() {
        var product = selectedProduct(manualProduct);
        populateManualVariations(product, manualVariation ? manualVariation.value : '');

        if (product) {
            manualOrderType.value = product.account_type || 'private';
            manualTotal.value = Number(product.price || 0);
            manualProductHelp.textContent = 'Produk dipilih: ' + product.name + '. Harga default ' + rupiah(product.price) + '.';
        } else {
            manualOrderType.value = '';
            manualTotal.value = 0;
            manualProductHelp.textContent = 'Pilih produk untuk cek stok.';
        }

        syncManualVariationPrice();
    }

    function syncManualVariationPrice() {
        if (!manualVariation || !manualTotal) return;
        var selected = manualVariation.selectedOptions[0];
        var price = selected ? Number(selected.dataset.price || 0) : 0;
        if (price > 0) {
            manualTotal.value = price;
        }
    }

    if (manualProduct) {
        manualProduct.addEventListener('change', syncManualOrder);
    }
    if (manualVariation) {
        manualVariation.addEventListener('change', syncManualVariationPrice);
    }
    document.querySelectorAll('.mode-tab').forEach(function (button) {
        button.addEventListener('click', function () {
            document.querySelectorAll('.mode-tab').forEach(function (item) { item.classList.remove('active'); });
            button.classList.add('active');
            if (button.dataset.mode === 'record') {
                manualStatus.value = 'pending';
                manualModeHelp.innerHTML = '<strong class="text-info">Mode aktif: Catat Saja</strong><br><span class="text-muted">Order dicatat tanpa assign akun otomatis.</span>';
            } else {
                manualStatus.value = 'completed';
                manualModeHelp.innerHTML = '<strong class="text-success">Mode aktif: Auto Assign</strong><br><span class="text-muted">Auto: sistem otomatis pilih akun.</span>';
            }
        });
    });
    document.getElementById('manualExpiredDays')?.addEventListener('change', setExpiredPreview);
    setExpiredPreview();

    var editForm = document.getElementById('editOrderForm');
    var editProduct = document.getElementById('editOrderProduct');
    var editVariation = document.getElementById('editOrderVariation');
    var editType = document.getElementById('editOrderType');
    var editTotal = document.getElementById('editOrderTotal');

    function syncEditProduct(keepVariation, shouldOverridePrice) {
        var product = selectedProduct(editProduct);
        populateVariationSelect(editVariation, product, keepVariation || '');

        if (product) {
            editType.value = product.account_type || editType.value || 'private';
            if (shouldOverridePrice) {
                editTotal.value = Number(product.price || 0);
            }
        }

        syncEditVariationPrice(shouldOverridePrice);
    }

    function syncEditVariationPrice(shouldOverridePrice) {
        if (!editVariation || !editTotal || !shouldOverridePrice) return;
        var selected = editVariation.selectedOptions[0];
        var price = selected ? Number(selected.dataset.price || 0) : 0;
        if (price > 0) {
            editTotal.value = price;
        }
    }

    document.querySelectorAll('.edit-order-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            editForm.action = '<?= site_url('orders/update'); ?>/' + button.dataset.id;
            document.getElementById('editOrderCode').value = button.dataset.code || '';
            document.getElementById('editOrderStore').value = button.dataset.storeId || '';
            document.getElementById('editOrderStatus').value = button.dataset.status || 'pending';
            editType.value = button.dataset.type || 'private';
            ensureOption(editProduct, button.dataset.product || '', button.dataset.product || '');
            editProduct.value = button.dataset.product || '';
            editTotal.value = button.dataset.total || 0;
            document.getElementById('editOrderEmail').value = button.dataset.email || '';
            document.getElementById('editOrderAccountUsername').value = button.dataset.accountUsername || '';
            document.getElementById('editOrderAccountPassword').value = button.dataset.accountPassword || '';
            document.getElementById('editOrderMaxUser').value = button.dataset.accountMaxUser || 1;
            document.getElementById('editOrderExpired').value = button.dataset.expired || '';
            syncEditProduct(button.dataset.variation || '', false);
        });
    });

    if (editProduct) {
        editProduct.addEventListener('change', function () { syncEditProduct('', true); });
    }
    if (editVariation) {
        editVariation.addEventListener('change', function () { syncEditVariationPrice(true); });
    }

    var rows = document.getElementById('quickOrderRows');
    var addButton = document.getElementById('quickAddRow');

    if (!rows || !addButton) {
        return;
    }

    function renumberRows() {
        rows.querySelectorAll('.quick-order-row').forEach(function (row, index) {
            row.querySelector('.quick-order-index').textContent = index + 1;
            row.querySelectorAll('input, select').forEach(function (field) {
                field.name = field.name.replace(/orders\[\d+\]/, 'orders[' + index + ']');
            });
        });
    }

    function filterVariations(row) {
        var productSelect = row.querySelector('.quick-product-select');
        var product = productSelect?.value || '';
        var productData = selectedProduct(productSelect);
        var variation = row.querySelector('.quick-variation-select');
        var type = row.querySelector('select[name$="[order_type]"]');
        var total = row.querySelector('input[name$="[total]"]');

        if (!variation) return;

        variation.querySelectorAll('option').forEach(function (option) {
            if (!option.dataset.product) {
                option.hidden = false;
                return;
            }

            option.hidden = product && option.dataset.product !== product;
        });

        if (variation.selectedOptions[0]?.hidden) {
            variation.value = '';
        }

        if (productData) {
            if (type) type.value = productData.account_type || 'private';
            if (total) total.value = Number(productData.price || 0);
        }

        syncQuickVariationPrice(row);
    }

    function syncQuickVariationPrice(row) {
        var variation = row.querySelector('.quick-variation-select');
        var total = row.querySelector('input[name$="[total]"]');
        if (!variation || !total) return;

        var selected = variation.selectedOptions[0];
        var price = selected ? Number(selected.dataset.price || 0) : 0;
        if (price > 0) {
            total.value = price;
        }
    }

    addButton.addEventListener('click', function () {
        var clone = rows.querySelector('.quick-order-row').cloneNode(true);
        clone.querySelectorAll('input').forEach(function (input) {
            input.value = input.type === 'number' ? '0' : '';
        });
        clone.querySelectorAll('select').forEach(function (select) {
            select.selectedIndex = 0;
        });
        rows.appendChild(clone);
        renumberRows();
        filterVariations(clone);
    });

    rows.addEventListener('click', function (event) {
        var remove = event.target.closest('.quick-remove-row');

        if (!remove || rows.querySelectorAll('.quick-order-row').length === 1) {
            return;
        }

        remove.closest('.quick-order-row').remove();
        renumberRows();
    });

    rows.addEventListener('change', function (event) {
        if (event.target.classList.contains('quick-product-select')) {
            filterVariations(event.target.closest('.quick-order-row'));
        }
        if (event.target.classList.contains('quick-variation-select')) {
            syncQuickVariationPrice(event.target.closest('.quick-order-row'));
        }
    });

    rows.querySelectorAll('.quick-order-row').forEach(filterVariations);
});
</script>

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
    .order-create-page .create-toolbar{align-items:center;display:flex;justify-content:space-between;margin-bottom:18px}
    .order-create-page .stock-grid{display:grid;gap:16px;grid-template-columns:repeat(2,minmax(0,1fr));margin-bottom:16px}
    .order-create-page .stock-card{align-items:center;background:#071b31;border:1px solid #142d49;border-radius:8px;display:flex;gap:14px;padding:18px}
    .order-create-page .stock-icon{align-items:center;background:rgba(47,110,217,.16);border-radius:8px;color:#72a6ff;display:inline-flex;font-size:22px;height:48px;justify-content:center;width:48px}
    .order-create-page .stock-icon.sharing{background:rgba(124,88,255,.18);color:#b28cff}
    .order-create-page .stock-label{color:#8da0bd;font-size:12px}
    .order-create-page .stock-value{color:#fff;font-size:30px;font-weight:900;line-height:1}
    .order-create-page .stock-help{color:#9fc0ec;font-size:12px}
    .order-create-page .form-card{background:#071b31;border:1px solid #142d49;border-radius:8px;padding:24px}
    .order-create-page .form-title{align-items:center;color:#fff;display:flex;font-weight:900;gap:8px;margin-bottom:16px}
    .order-create-page .form-label{color:#9fc0ec!important;font-size:12px;font-weight:800;margin-bottom:6px}
    .order-create-page .form-text{color:#617da9!important;font-size:11px}
    .order-create-page .form-control,.order-create-page .form-select{background:#061426!important;border-color:#142d49!important;color:#f8fbff!important;min-height:42px}
    .order-create-page .form-control:focus,.order-create-page .form-select:focus{border-color:#2f6ed9!important;box-shadow:0 0 0 .18rem rgba(47,110,217,.18)!important}
    .order-create-page .mode-tabs{border:1px solid rgba(141,160,189,.18);display:grid;grid-template-columns:1fr 1fr;margin-bottom:8px}
    .order-create-page .mode-tab{align-items:center;background:transparent;border:0;color:#8da0bd;display:flex;font-size:13px;font-weight:800;gap:8px;justify-content:center;min-height:42px}
    .order-create-page .mode-tab.active{background:rgba(0,168,116,.22);box-shadow:inset 0 0 0 1px #00a874;color:#fff}
    .order-create-page .product-alert{background:rgba(0,168,116,.15);border:1px solid #00a874;border-radius:8px;color:#6df0c3;font-size:12px;font-weight:800;padding:11px 13px}
    .order-create-page .product-alert.unavailable{background:rgba(224,78,108,.14);border-color:rgba(224,78,108,.44);color:#ff9caf}
    .order-create-page .product-alert.loading{background:rgba(47,124,255,.14);border-color:rgba(92,142,255,.42);color:#9fc0ff}
    .order-create-page .info-box{background:rgba(47,124,255,.16);border:1px solid rgba(92,142,255,.48);border-radius:8px;color:#9fc0ff;font-size:12px;line-height:1.45;padding:13px 15px}
    @media (max-width:767.98px){.order-create-page .stock-grid{grid-template-columns:1fr}.order-create-page .create-toolbar{align-items:stretch;flex-direction:column;gap:10px}}
</style>

<div class="pagetitle">
    <h1>Input Order Baru</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard'); ?>">Dashboard</a></li><li class="breadcrumb-item"><a href="<?= site_url('orders'); ?>">Riwayat Order</a></li><li class="breadcrumb-item active">Tambah Order</li></ol></nav>
</div>

<section class="section order-create-page">
    <div class="create-toolbar">
        <a href="<?= site_url('orders'); ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
        <button type="button" class="btn btn-outline-primary btn-sm"><i class="bi bi-upload"></i> Import CSV</button>
    </div>

    <div class="stock-grid">
        <div class="stock-card">
            <span class="stock-icon"><i class="bi bi-lock-fill"></i></span>
            <div><div class="stock-label">Stok Private</div><div class="stock-value"><?= number_format((int) ($private_stock ?? 0)); ?></div><div class="stock-help">akun available</div></div>
        </div>
        <div class="stock-card">
            <span class="stock-icon sharing"><i class="bi bi-link-45deg"></i></span>
            <div><div class="stock-label">Stok Sharing</div><div class="stock-value"><?= number_format((int) ($sharing_stock ?? 0)); ?></div><div class="stock-help">akun ada slot</div></div>
        </div>
    </div>

    <form action="<?= site_url('orders/store'); ?>" method="POST" class="form-card" id="createOrderForm">
        <div class="form-title"><i class="bi bi-cart-check"></i> Form Input Order</div>
        <p class="text-muted small mb-3">Pilih mode input: auto assign akun atau catat order saja.</p>

        <label class="form-label">Mode Assign Akun</label>
        <input type="hidden" name="status" id="orderStatus" value="completed">
        <div class="mode-tabs" role="group" aria-label="Mode assign akun">
            <button type="button" class="mode-tab active" data-mode="auto"><i class="bi bi-lightning-charge-fill text-warning"></i> Auto Assign</button>
            <button type="button" class="mode-tab" data-mode="record"><i class="bi bi-file-earmark-text text-danger"></i> Catat Saja</button>
        </div>
        <div class="small mb-3" id="modeHelp"><strong class="text-success">Mode aktif: Auto Assign</strong><br><span class="text-muted">Auto: sistem otomatis pilih akun.</span></div>

        <div class="mb-3">
            <label class="form-label">No. Pesanan Marketplace</label>
            <input type="text" name="shopee_order_id" class="form-control" placeholder="Contoh: 2503031234ABCDE (Shopee) atau INV/20260303/MPL/12345 (Tokopedia)">
            <div class="form-text">Bisa dikosongkan, sistem akan membuat nomor manual otomatis.</div>
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
                <select name="order_type" class="form-select" id="orderType" required>
                    <option value="">Pilih Tipe</option>
                    <option value="private">Private</option>
                    <option value="sharing">Sharing</option>
                </select>
                <div class="form-text">Pilih private/sharing untuk auto assign akun.</div>
            </div>

            <div class="col-12">
                <label class="form-label">Produk <span class="text-danger">*</span></label>
                <select name="product_name" class="form-select" id="orderProduct" required>
                    <option value="">Pilih Produk</option>
                    <?php foreach ($products as $product): ?><option value="<?= h($product->name); ?>" data-id="<?= (int) $product->id; ?>"><?= h($product->name); ?></option><?php endforeach; ?>
                </select>
                <div class="mt-3 product-alert" id="productHelp">Pilih produk untuk cek stok.</div>
            </div>

            <div class="col-12">
                <label class="form-label">Variasi Harga</label>
                <select name="variation" class="form-select" id="orderVariation">
                    <option value="">Pilih variasi</option>
                </select>
                <div class="form-text">Variasi difilter otomatis dari produk yang dipilih.</div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email Buyer</label>
                <input type="email" name="buyer_email" class="form-control" placeholder="buyer@gmail.com">
            </div>
            <div class="col-md-6">
                <label class="form-label">Akun yang Akan Diberikan</label>
                <div class="product-alert loading" id="accountPreview">Pilih produk untuk cek akun available.</div>
                <div class="form-text">Akun dipilih otomatis dari stok available saat order disimpan.</div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Harga Jual (Rp)</label>
                <input type="number" name="total" class="form-control" id="orderTotal" value="0" min="0" step="100" required>
                <div class="form-text">Otomatis terisi dari produk/variasi, bisa di-override.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Durasi Expired</label>
                <select name="expired_days" class="form-select" id="expiredDays">
                    <?php if ($durations): foreach ($durations as $duration): ?><option value="<?= (int) $duration->days; ?>" <?= $duration->is_default ? 'selected' : ''; ?>><?= h($duration->label); ?></option><?php endforeach; else: ?><option value="30">30 Hari</option><?php endif; ?>
                </select>
                <div class="form-text" id="expiredText">Expired: -</div>
            </div>

            <div class="col-12">
                <label class="form-label">Tanggal Order</label>
                <input type="date" class="form-control" value="<?= date('Y-m-d'); ?>">
                <div class="form-text">Default hari ini. Ubah untuk order lama/backdate.</div>
            </div>

            <div class="col-12">
                <div class="info-box"><i class="bi bi-info-circle-fill"></i> Setelah klik "Simpan Order", sistem akan mengambil akun available secara otomatis. Jika akun sudah penuh, status akun berubah menjadi sold.</div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="<?= site_url('orders'); ?>" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-lightning-charge-fill"></i> Simpan Order</button>
        </div>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var orderProducts = <?= json_encode($orderProductsPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
    var orderVariations = <?= json_encode($orderVariationsPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
    var productById = {};
    orderProducts.forEach(function (product) { productById[String(product.id)] = product; });

    function rupiah(value) {
        return 'Rp' + (Number(value || 0)).toLocaleString('id-ID');
    }

    function selectedProduct(select) {
        if (!select || !select.selectedOptions.length) return null;
        var id = select.selectedOptions[0].dataset.id;
        return id ? productById[String(id)] : null;
    }

    var productSelect = document.getElementById('orderProduct');
    var variationSelect = document.getElementById('orderVariation');
    var typeSelect = document.getElementById('orderType');
    var totalInput = document.getElementById('orderTotal');
    var productHelp = document.getElementById('productHelp');
    var accountPreview = document.getElementById('accountPreview');
    var availabilityTimer = null;

    function populateVariations(product) {
        variationSelect.innerHTML = '<option value="">Pilih variasi</option>';
        if (!product) return;
        orderVariations.filter(function (variation) {
            return String(variation.product_id) === String(product.id);
        }).forEach(function (variation) {
            var option = document.createElement('option');
            option.value = variation.label;
            option.dataset.price = variation.price;
            option.textContent = variation.label + (Number(variation.price) > 0 ? ' - ' + rupiah(variation.price) : '');
            variationSelect.appendChild(option);
        });
        if (variationSelect.options.length === 2) {
            variationSelect.selectedIndex = 1;
        }
    }

    function syncProduct() {
        var product = selectedProduct(productSelect);
        populateVariations(product);
        if (product) {
            typeSelect.value = product.account_type || 'private';
            totalInput.value = Number(product.price || 0);
            productHelp.textContent = 'Produk dipilih: ' + product.name + '. Harga default ' + rupiah(product.price) + '.';
        } else {
            typeSelect.value = '';
            totalInput.value = 0;
            productHelp.textContent = 'Pilih produk untuk cek stok.';
        }
        syncVariationPrice();
        checkAvailableAccount();
    }

    function syncVariationPrice() {
        var selected = variationSelect.selectedOptions[0];
        var price = selected ? Number(selected.dataset.price || 0) : 0;
        if (price > 0) {
            totalInput.value = price;
        }
        checkAvailableAccount();
    }

    function setAccountPreview(state, message) {
        accountPreview.classList.remove('loading', 'unavailable');
        if (state === 'loading') {
            accountPreview.classList.add('loading');
        }
        if (state === 'unavailable') {
            accountPreview.classList.add('unavailable');
        }
        accountPreview.textContent = message;
    }

    function checkAvailableAccount() {
        clearTimeout(availabilityTimer);
        availabilityTimer = setTimeout(function () {
            var product = productSelect.value;
            var orderType = typeSelect.value;
            var variation = variationSelect.value;

            if (!product || !orderType) {
                setAccountPreview('loading', 'Pilih produk dan tipe akun untuk cek akun available.');
                return;
            }

            setAccountPreview('loading', 'Mengecek akun available...');
            var url = '<?= site_url('orders/available-account'); ?>'
                + '?product_name=' + encodeURIComponent(product)
                + '&variation=' + encodeURIComponent(variation)
                + '&order_type=' + encodeURIComponent(orderType);

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (response) { return response.json(); })
                .then(function (payload) {
                    if (!payload.available) {
                        setAccountPreview('unavailable', payload.message || 'Akun tidak tersedia.');
                        return;
                    }

                    setAccountPreview('available', 'Akun available: ' + payload.account.username + ' | Max user: ' + payload.account.max_user + '. Akan dikirim otomatis setelah simpan.');
                })
                .catch(function () {
                    setAccountPreview('unavailable', 'Gagal mengecek akun available.');
                });
        }, 250);
    }

    function setExpiredPreview() {
        var duration = document.getElementById('expiredDays');
        var text = document.getElementById('expiredText');
        var days = parseInt(duration.value || '0', 10);
        if (!days) {
            text.textContent = 'Expired: -';
            return;
        }
        var date = new Date();
        date.setDate(date.getDate() + days);
        text.textContent = 'Expired: ' + date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    }

    document.querySelectorAll('.mode-tab').forEach(function (button) {
        button.addEventListener('click', function () {
            document.querySelectorAll('.mode-tab').forEach(function (item) { item.classList.remove('active'); });
            button.classList.add('active');
            document.getElementById('orderStatus').value = button.dataset.mode === 'record' ? 'pending' : 'completed';
            document.getElementById('modeHelp').innerHTML = button.dataset.mode === 'record'
                ? '<strong class="text-info">Mode aktif: Catat Saja</strong><br><span class="text-muted">Order dicatat tanpa assign akun otomatis.</span>'
                : '<strong class="text-success">Mode aktif: Auto Assign</strong><br><span class="text-muted">Auto: sistem otomatis pilih akun.</span>';
        });
    });

    productSelect.addEventListener('change', syncProduct);
    variationSelect.addEventListener('change', syncVariationPrice);
    typeSelect.addEventListener('change', checkAvailableAccount);
    document.getElementById('expiredDays').addEventListener('change', setExpiredPreview);
    setExpiredPreview();
});
</script>

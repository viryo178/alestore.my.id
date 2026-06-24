<style>
    .dashboard{padding-top:4px}.dashboard .card-body{padding:28px}.dashboard .row{row-gap:22px}
    .summary-grid{display:grid;gap:16px;grid-template-columns:repeat(4,minmax(0,1fr))}
    .summary-card{min-height:102px}.summary-card .card-body{display:flex;align-items:center;gap:16px;padding:24px}
    .summary-icon{width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex:0 0 46px;font-size:22px}
    .summary-icon.teal{background:rgba(0,168,116,.14);color:#00b77f}.summary-icon.blue{background:rgba(47,124,255,.13);color:#6f9ff0}
    .summary-icon.purple{background:rgba(104,84,210,.15);color:#8574ef}.summary-icon.orange{background:rgba(212,120,31,.14);color:#d98a2e}
    .summary-icon.red{background:rgba(217,54,89,.14);color:#e04e6c}.summary-label{color:#8fa8cc;font-size:13px;margin-bottom:2px}
    .summary-value{color:#fff;font-size:28px;font-weight:800;line-height:1}.summary-sub{color:#b7c9e8;font-size:12px;margin-top:5px}
    .sales-card-panel .card-title{padding:0;margin-bottom:18px}.sales-filter{display:flex;flex-wrap:wrap;gap:8px;justify-content:flex-end}
    .sales-filter .btn{border-radius:8px!important;min-width:76px}.dashboard-chart-wrap{height:360px;position:relative;margin-top:18px}
    .dashboard-chart-wrap canvas{width:100%!important;height:100%!important}.top-products-panel .card-body{padding:28px}
    .top-product-row,.low-stock-row{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:13px 0;border-bottom:1px solid rgba(141,160,189,.11)}
    .top-product-row strong,.low-stock-title{color:#fff!important;font-weight:700}.top-product-meter{display:flex;align-items:center;gap:10px;min-width:170px}
    .low-stock-card{margin-top:22px}.low-stock-row:last-child{border-bottom:0}.low-stock-meta{color:#9bb4d6;font-size:13px}.low-stock-count{min-width:74px;text-align:right}
    @media (max-width:1199.98px){.summary-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (max-width:575.98px){.summary-grid{grid-template-columns:1fr}}
</style>

<div class="pagetitle">
    <h1>Dashboard</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard'); ?>">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row">
        <div class="col-12">
            <div class="summary-grid">
                <div class="card summary-card"><div class="card-body"><div class="summary-icon teal"><i class="bi bi-box-seam"></i></div><div><div class="summary-label">Stok Tersedia</div><div class="summary-value"><?= number_format($available_count); ?></div><div class="summary-sub"><?= number_format($sharing_count); ?> akun sharing</div></div></div></div>
                <div class="card summary-card"><div class="card-body"><div class="summary-icon blue"><i class="bi bi-cart-check"></i></div><div><div class="summary-label">Order Hari Ini</div><div class="summary-value"><?= number_format($today_orders_count); ?></div><div class="summary-sub"><?= rupiah($today_revenue); ?></div></div></div></div>
                <div class="card summary-card"><div class="card-body"><div class="summary-icon purple"><i class="bi bi-link-45deg"></i></div><div><div class="summary-label">Akun Sharing</div><div class="summary-value"><?= number_format($sharing_count); ?></div><div class="summary-sub"><?= number_format($full_slot_count); ?> penuh</div></div></div></div>
                <div class="card summary-card"><div class="card-body"><div class="summary-icon teal"><i class="bi bi-lock"></i></div><div><div class="summary-label">Akun Private</div><div class="summary-value"><?= number_format($private_count); ?></div><div class="summary-sub"><?= number_format($sold_count); ?> terjual</div></div></div></div>
                <div class="card summary-card"><div class="card-body"><div class="summary-icon orange"><i class="bi bi-lightning-charge"></i></div><div><div class="summary-label">Akun Full Slot</div><div class="summary-value"><?= number_format($full_slot_count); ?></div><div class="summary-sub">sharing slots habis</div></div></div></div>
                <div class="card summary-card"><div class="card-body"><div class="summary-icon teal"><i class="bi bi-cash-coin"></i></div><div><div class="summary-label">Akun Terjual</div><div class="summary-value"><?= number_format($sold_count); ?></div><div class="summary-sub">sold out</div></div></div></div>
                <div class="card summary-card"><div class="card-body"><div class="summary-icon red"><i class="bi bi-slash-circle"></i></div><div><div class="summary-label">Akun Banned</div><div class="summary-value"><?= number_format($deactived_count); ?></div><div class="summary-sub">diblokir</div></div></div></div>
                <div class="card summary-card"><div class="card-body"><div class="summary-icon blue"><i class="bi bi-shop"></i></div><div><div class="summary-label">Total Toko</div><div class="summary-value"><?= number_format($active_stores_count); ?></div><div class="summary-sub">marketplace</div></div></div></div>
            </div>
        </div>

        <div class="col-xl-9">
            <div class="card h-50 sales-card-panel">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-start  mb-3">
                        <h5 class="card-title mb-0"><i class="bi bi-bar-chart"></i> Penjualan</h5>
                        <div class="sales-filter">
                            <?php foreach (array('today' => 'Hari Ini', 'yesterday' => 'Kemarin', '7' => '7 Hari', 'month' => 'Bulanan', 'year' => 'Tahunan') as $value => $label): ?>
                                <a href="<?= site_url('admin/dashboard?range='.$value); ?>" class="btn btn-outline-primary <?= $range === $value ? 'active' : ''; ?>"><?= h($label); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="d-flex gap-4 mb-2">
                        <div><span class="text-muted small">Total Order</span><h4 class="mb-0 text-primary"><?= number_format($orders_count); ?></h4></div>
                        <div><span class="text-muted small">Pendapatan</span><h4 class="mb-0 text-success"><?= rupiah($today_revenue); ?></h4></div>
                    </div>
                    <div class="dashboard-chart-wrap"><canvas id="salesChart"></canvas></div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Statistik Per Toko <span>| Hari Ini</span></h5>
                    <div class="table-responsive">
                        <table class="table datatable align-middle">
                            <thead>
                                <tr>
                                    <th>Nama Toko</th>
                                    <th>Platform</th>
                                    <th>Order Hari Ini</th>
                                    <th>Pendapatan Hari Ini</th>
                                    <th>Total Order</th>
                                </tr>
                             </thead>
                            <tbody>
                            <?php if ($store_stats): foreach ($store_stats as $store): ?>
                                <tr>
                                    <td><strong><?= h($store->shop_name); ?></strong></td>
                                    <td><span class="badge bg-primary"><?= h($store->platform ?? 'Shopee'); ?></span></td>
                                    <td class="text-primary fw-bold"><?= number_format($store->today_orders); ?></td>
                                    <td class="text-success fw-bold"><?= rupiah($store->today_revenue); ?></td>
                                    <td><?= number_format($store->total_orders); ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data toko.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3">
            <div class="card h-50 top-products-panel">
                <div class="card-body">
                    <h5 class="card-title mb-4"><i class="bi bi-trophy"></i> Produk Teratas</h5>
                    <?php $maxTop = 0; foreach ($top_products as $p) { $maxTop = max($maxTop, (int) $p->total); } ?>
                    <?php if ($top_products): foreach ($top_products as $product): ?>
                        <?php $width = $maxTop > 0 ? ((int) $product->total / $maxTop) * 100 : 0; ?>
                        <div class="top-product-row">
                            <strong><?= h($product->product_name); ?></strong>
                            <div class="top-product-meter">
                                <div class="progress flex-grow-1" style="height:6px;"><div class="progress-bar" style="width:<?= $width; ?>%"></div></div>
                                <span class="text-primary fw-bold"><?= number_format($product->total); ?></span>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                        <p class="text-muted mb-0">Belum ada produk.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card low-stock-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0"><i class="bi bi-exclamation-triangle"></i> Stok Menipis</h5>
                        <a href="<?= site_url('digital-accounts?status=available'); ?>" class="small text-primary">Kelola</a>
                    </div>
                    <?php if ($low_stock_products): foreach ($low_stock_products as $stock): ?>
                        <div class="low-stock-row">
                            <div>
                                <div class="low-stock-title"><?= h($stock->product_name); ?></div>
                                <div class="low-stock-meta"><?= h($stock->variation ?: 'Tanpa variasi'); ?> - <?= h(ucfirst($stock->account_type)); ?></div>
                            </div>
                            <div class="low-stock-count"><span class="badge <?= $stock->available_total < 1 ? 'bg-danger' : 'bg-warning'; ?>"><?= number_format($stock->available_total); ?> tersedia</span></div>
                        </div>
                    <?php endforeach; else: ?>
                        <p class="text-muted mb-0">Semua stok aman.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-12">

        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('salesChart');
    if (!canvas || typeof Chart === 'undefined') return;
    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chart_labels); ?>,
            datasets: [{
                label: 'Order',
                data: <?= json_encode($chart_orders); ?>,
                borderColor: '#4d8cff',
                backgroundColor: 'rgba(77, 140, 255, .45)',
                borderWidth: 2,
                borderRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {legend: {display: false}},
            scales: {
                x: {grid: {color: 'rgba(126,151,201,.12)'}},
                y: {beginAtZero: true, grid: {color: 'rgba(126,151,201,.12)'}}
            }
        }
    });
});
</script>

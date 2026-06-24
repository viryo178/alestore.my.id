<?php $money = function ($value) { return 'Rp '.number_format((float) $value, 0, ',', '.'); }; ?>

<style>
    .finance-report-scope {
        margin-top: -1px;
    }

    .finance-report-scope + .finance-page,
    .finance-page {
        --finance-border: #142d49;
        --finance-panel: #071b31;
        --finance-head: #0a2038;
        --finance-muted: #8fb2df;
        --finance-blue: #1f55b5;
        --finance-blue-border: #2f6ed9;
    }

    .finance-report-scope.pagetitle {
        margin-bottom: 14px;
    }

    .finance-report-scope.pagetitle h1 {
        color: #ffffff !important;
        font-size: 24px;
        font-weight: 900;
        line-height: 1.15;
        margin: 0 0 4px;
    }

    .finance-report-scope .breadcrumb {
        margin: 0;
        font-size: 14px;
        font-weight: 800;
    }

    .finance-page .page-kicker h3 {
        color: #ffffff;
        font-size: 28px;
        font-weight: 500;
        line-height: 1.2;
    }

    .finance-page .page-kicker p {
        color: #8fb2df !important;
        font-size: 16px;
    }

    .finance-page .report-title-row {
        margin-bottom: 17px !important;
    }

    .finance-page .download-report-btn {
        align-items: center;
        display: inline-flex;
        gap: 6px;
        height: 38px;
        padding: 0 14px;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 700;
    }

    .finance-page .card-body {
        padding: 24px;
    }

    .finance-filter-card {
        margin-top: 18px;
        margin-bottom: 30px;
        border-radius: 5px !important;
        border-color: var(--finance-border) !important;
        background: var(--finance-panel) !important;
    }

    .finance-filter-card .card-body {
        padding: 26px 24px 23px;
        min-height: 162px;
    }

    .finance-filter-grid {
        display: grid;
        grid-template-columns: minmax(260px, 1fr) minmax(260px, 1fr) minmax(280px, 1.05fr) 110px 110px 112px;
        gap: 16px;
        align-items: end;
    }

    .finance-filter-card .form-label {
        color: #ffffff !important;
        margin-bottom: 9px;
        font-size: 16px;
        font-weight: 800;
    }

    .finance-filter-card .form-control,
    .finance-filter-card .form-select {
        height: 38px;
        border-radius: 6px;
        font-size: 16px;
        padding-left: 12px;
    }

    .finance-filter-card .btn {
        height: 38px;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 700;
    }

    .finance-quick-filter {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        grid-column: 1 / 4;
        margin-top: -5px;
    }

    .finance-quick-filter .btn {
        min-width: 104px;
    }

    .finance-page .card-body {
        padding: 24px;
    }

    .finance-filter-card .form-label {
        margin-bottom: 8px;
        font-weight: 600;
    }

    .finance-quick-filter {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 6px;
    }

    .finance-metrics {
        display: grid;
        grid-template-columns: repeat(8, minmax(150px, 1fr));
        gap: 12px;
        overflow-x: auto;
        padding-bottom: 8px;
    }

    .finance-metric-card {
        min-height: 118px;
        margin-bottom: 0;
    }

    .finance-metric-card .card-body {
        padding: 18px 14px;
    }

    .finance-metric-card h5 {
        margin-bottom: 12px;
        font-size: 12px;
        letter-spacing: .04em;
    }

    .finance-metric-card h4 {
        font-size: 21px;
        margin-bottom: 8px;
        white-space: nowrap;
    }

    .finance-page .profit-card .card-body {
        padding: 44px 24px 0;
    }

    .finance-page .profit-card .card-title {
        color: #ffffff !important;
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 24px;
        padding: 0;
    }

    .finance-page .profit-card {
        border-radius: 5px !important;
        border-color: var(--finance-border) !important;
        background: var(--finance-panel) !important;
        min-height: 338px;
    }

    .finance-page .profit-card table thead th {
        color: #83a8da !important;
        font-size: 16px;
        font-weight: 900;
        padding: 10px 8px !important;
        background: var(--finance-head) !important;
    }

    .finance-page .profit-card table tbody td {
        color: #ffffff !important;
        font-size: 16px;
        padding: 9px 8px !important;
    }

    .finance-page .profit-card table tbody td strong {
        font-weight: 900;
    }

    .finance-report-scope .breadcrumb,
    .finance-report-scope .breadcrumb a,
    .finance-report-scope .breadcrumb .active {
        color: #7fa3d0 !important;
    }

    .finance-page .page-kicker p,
    .finance-page .finance-metric-card small,
    .finance-page .profit-card .text-muted {
        color: #91b5e3 !important;
    }

    .finance-page .finance-metric-card h4.text-primary {
        color: #5f96ff !important;
    }

    .finance-page .finance-metric-card h4.text-warning {
        color: #e09705 !important;
    }

    .finance-page .finance-metric-card h4.text-danger {
        color: #ff2f68 !important;
        background: transparent !important;
    }

    .finance-page .finance-metric-card h4.text-success {
        color: #00c985 !important;
    }

    .finance-page .profit-card table thead th {
        color: #8fbaf0 !important;
    }

    .finance-page .profit-card table tbody td,
    .finance-page .profit-card table tbody td.text-success,
    .finance-page .profit-card table tbody td.text-primary {
        color: #f8fbff !important;
    }

    .finance-page .expense-text,
    .finance-page .profit-card table tbody td.expense-text,
    .finance-page .profit-card table tbody td.text-danger {
        background: transparent !important;
        color: #ff2f68 !important;
    }

    .finance-page .finance-quick-filter .btn {
        color: #70a0f0 !important;
    }

    .finance-page .finance-quick-filter .btn.active {
        color: #ffffff !important;
    }

    @media (max-width: 1400px) {
        .finance-filter-grid {
            grid-template-columns: repeat(3, minmax(220px, 1fr));
        }

        .finance-quick-filter {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 768px) {
        .finance-filter-grid {
            grid-template-columns: 1fr;
        }

        .finance-quick-filter {
            grid-column: auto;
        }
    }
</style>

<div class="pagetitle finance-report-scope">
    <h1>Laporan & Export</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard'); ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Laporan Keuangan</li>
        </ol>
    </nav>
</div>

<section class="section finance-page">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 report-title-row">
        <div class="page-kicker">
            <h3 class="mb-1">Laporan Penjualan</h3>
            <p class="text-muted mb-0">Analisa pendapatan dengan biaya fee, HPP, iklan, dan biaya tambahan.</p>
        </div>
        <a class="btn btn-primary download-report-btn" href="<?= site_url('financial-reports/download?'.http_build_query($this->input->get())); ?>">
            <i class="bi bi-file-earmark-spreadsheet"></i> Download Excel CSV
        </a>
    </div>

    <div class="card finance-filter-card mb-4">
        <div class="card-body">
            <form method="GET" class="finance-filter-grid">
                <div>
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="from" class="form-control" value="<?= h($from); ?>">
                </div>
                <div>
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="to" class="form-control" value="<?= h($to); ?>">
                </div>
                <div>
                    <label class="form-label">Toko</label>
                    <select name="store_id" class="form-select">
                        <option value="all">Semua Toko</option>
                        <?php foreach ($stores as $store): ?>
                            <option value="<?= $store->id; ?>" <?= (string) $store_id === (string) $store->id ? 'selected' : ''; ?>><?= h($store->shop_name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Iklan</label>
                    <input type="number" name="ads_cost" class="form-control" value="<?= h($this->input->get('ads_cost') ?: 0); ?>">
                </div>
                <div>
                    <label class="form-label">Extra</label>
                    <input type="number" name="extra_cost" class="form-control" value="<?= h($this->input->get('extra_cost') ?: 0); ?>">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                </div>
                <div class="finance-quick-filter">
                    <a href="<?= site_url('financial-reports?quick=week'); ?>" class="btn btn-outline-primary <?= $this->input->get('quick') === 'week' ? 'active' : ''; ?>">Minggu Ini</a>
                    <a href="<?= site_url('financial-reports?quick=month'); ?>" class="btn btn-outline-primary <?= ($this->input->get('quick') ?: 'month') === 'month' ? 'active' : ''; ?>">Bulan Ini</a>
                    <a href="<?= site_url('financial-reports?quick=year'); ?>" class="btn btn-outline-primary <?= $this->input->get('quick') === 'year' ? 'active' : ''; ?>">Tahun Ini</a>
                </div>
            </form>
        </div>
    </div>

    <div class="finance-metrics mb-3">
        <?php
        $cards = array(
            array('Transaksi', number_format($summary['transactions']), 'order', 'text-primary'),
            array('Gross Revenue', $money($summary['gross']), 'total harga jual', 'text-warning'),
            array('Fee Admin', '- '.$money($summary['fee']), 'biaya marketplace', 'expense-text'),
            array('Extra Cost Klaim', '- '.$money($summary['extra_cost']), 'biaya tambahan', 'text-warning'),
            array('Total Refund', '- '.$money($summary['refund']), 'refund', 'expense-text'),
            array('Net Revenue', $money($summary['net']), 'gross - fee - refund', 'text-success'),
            array('Biaya Iklan', '- '.$money($summary['ads']), 'iklan', 'text-warning'),
            array('Laba Bersih', $money($summary['profit']), 'net - cogs - biaya', $summary['profit'] < 0 ? 'expense-text' : 'text-success'),
        );
        foreach ($cards as $card):
        ?>
            <div class="card info-card finance-metric-card">
                <div class="card-body text-center">
                    <h5 class="card-title text-uppercase small"><?= h($card[0]); ?></h5>
                    <h4 class="<?= h($card[3]); ?>"><?= h($card[1]); ?></h4>
                    <small class="text-muted"><?= h($card[2]); ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card profit-card">
        <div class="card-body">
            <h5 class="card-title">Profit Per Produk</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Gross</th>
                            <th>Fee</th>
                            <th>Net</th>
                            <th>COGS</th>
                            <th>Profit</th>
                            <th>Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($profit_products): foreach ($profit_products as $product): ?>
                            <tr>
                                <td><strong><?= h($product['product']); ?></strong></td>
                                <td><?= number_format($product['qty']); ?></td>
                                <td><?= $money($product['gross']); ?></td>
                                <td class="expense-text">- <?= $money($product['fee']); ?></td>
                                <td class="text-success"><?= $money($product['net']); ?></td>
                                <td class="expense-text">- <?= $money($product['cogs']); ?></td>
                                <td class="<?= $product['profit'] < 0 ? 'expense-text' : 'text-success'; ?> fw-bold"><?= $money($product['profit']); ?></td>
                                <td><?= number_format($product['margin'], 2); ?>%</td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada data produk terjual.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

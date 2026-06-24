<style>
    .renewal-page{max-width:100%}
    .renewal-page .renewal-header{align-items:center;display:flex;flex-wrap:wrap;gap:18px;justify-content:space-between;margin-bottom:20px}
    .renewal-page .renewal-heading{align-items:center;display:flex;gap:12px}
    .renewal-page .renewal-icon{align-items:center;background:rgba(47,124,255,.16);border:1px solid rgba(47,124,255,.28);border-radius:10px;color:#7fb0ff;display:inline-flex;height:40px;justify-content:center;width:40px}
    .renewal-page .renewal-title{color:#fff;font-size:20px;font-weight:800;margin:0}
    .renewal-page .metric-grid{display:grid;gap:12px;grid-template-columns:repeat(2,minmax(0,1fr));margin-bottom:18px}
    .renewal-page .metric-card{align-items:center;background:linear-gradient(180deg,#081f39,#07172d);border:1px solid #142d49;border-radius:8px;display:flex;gap:14px;min-height:78px;padding:16px}
    .renewal-page .metric-icon{align-items:center;background:rgba(47,124,255,.14);border:1px solid rgba(47,124,255,.22);border-radius:8px;color:#7fb0ff;display:inline-flex;height:42px;justify-content:center;width:42px}
    .renewal-page .metric-icon.overdue{background:rgba(255,173,0,.12);border-color:rgba(255,173,0,.24);color:#ffad00}
    .renewal-page .metric-label{color:#7f96bb;font-size:12px;font-weight:700;margin-bottom:3px}
    .renewal-page .metric-value{color:#fff;font-size:24px;font-weight:800;line-height:1.1}
    .renewal-page .metric-value.overdue{color:#ffad00}
    .renewal-page .filter-card .form-label{color:#9fc0ec!important;font-size:12px;font-weight:800}
    .renewal-page .filter-card .card-body{padding:18px 22px}
    .renewal-page .renewal-table thead th{font-size:11px;letter-spacing:.04em;text-transform:uppercase;white-space:nowrap}
    .renewal-page .order-code{color:#5da2ff;font-size:12px;font-weight:750}
    .renewal-page .account-code{color:#8aa4cc;font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;font-size:11px}
    .renewal-page .main-text{color:#dce8ff;font-weight:650}
    .renewal-page .subtext{color:#6f8fbd;display:block;font-size:11px;margin-top:2px}
    .renewal-page .expire-text{color:#ff4967;font-weight:800;white-space:nowrap}
    .renewal-page .expire-text.upcoming{color:#00d18f}
    .renewal-page .renewal-pill{background:rgba(47,124,255,.16);border:1px solid rgba(47,124,255,.22);border-radius:999px;color:#75a8ff;display:inline-flex;font-size:11px;font-weight:750;padding:5px 9px;white-space:nowrap}
    .renewal-page .note-input{max-width:320px;min-height:36px;min-width:230px}
    .renewal-page .action-stack{align-items:flex-end;display:flex;flex-direction:column;gap:6px}
    .renewal-page .action-row{display:flex;flex-wrap:wrap;gap:7px;justify-content:flex-end}
    .renewal-page .action-row .btn{font-size:12px;min-height:30px}
    .renewal-page .product-chip{background:rgba(124,88,255,.18);border-radius:999px;color:#b890ff;display:inline-flex;font-size:11px;font-weight:700;margin-top:5px;padding:4px 8px}
    .renewal-page .empty-state{padding:34px 16px}
    @media (max-width:767.98px){.renewal-page .metric-grid{grid-template-columns:1fr}.renewal-page .action-stack{align-items:flex-start}.renewal-page .action-row{justify-content:flex-start}}
</style>

<section class="section renewal-page">
    <div class="renewal-header">
        <div class="renewal-heading">
            <span class="renewal-icon"><i class="bi bi-calendar2-check"></i></span>
            <div>
                <h1 class="renewal-title">Pengingat Expired</h1>
                <p class="text-muted mb-0">Pantau akun yang mendekati expired dan lakukan follow-up perpanjangan.</p>
            </div>
        </div>
        <a href="<?= site_url('orders'); ?>" class="btn btn-outline-primary">
            <i class="bi bi-card-list"></i> Kembali ke Riwayat Order
        </a>
    </div>

    <div class="metric-grid">
        <div class="metric-card">
            <span class="metric-icon"><i class="bi bi-hourglass-split"></i></span>
            <div>
                <div class="metric-label">Upcoming 30 Hari</div>
                <div class="metric-value"><?= number_format($upcoming_count); ?></div>
                <div class="subtext">Akun yang perlu dipantau sebelum expired.</div>
            </div>
        </div>
        <div class="metric-card">
            <span class="metric-icon overdue"><i class="bi bi-exclamation-triangle"></i></span>
            <div>
                <div class="metric-label text-warning">Overdue</div>
                <div class="metric-value overdue"><?= number_format($overdue_count); ?></div>
                <div class="subtext">Akun sudah melewati tanggal expired.</div>
            </div>
        </div>
    </div>

    <div class="card filter-card">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-lg-7">
                    <label class="form-label">Cari</label>
                    <input type="text" name="q" class="form-control" value="<?= h($this->input->get('q')); ?>" placeholder="order no, nama buyer, produk, kontak...">
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Periode</label>
                    <select name="period" class="form-select">
                        <option value="upcoming_30" <?= $period === 'upcoming_30' ? 'selected' : ''; ?>>Upcoming 30 Hari</option>
                        <option value="upcoming_7" <?= $period === 'upcoming_7' ? 'selected' : ''; ?>>Upcoming 7 Hari</option>
                        <option value="today" <?= $period === 'today' ? 'selected' : ''; ?>>Hari Ini</option>
                        <option value="overdue" <?= $period === 'overdue' ? 'selected' : ''; ?>>Overdue</option>
                        <option value="all" <?= $period === 'all' ? 'selected' : ''; ?>>Semua</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Toko</label>
                    <select name="store_id" class="form-select" <?= empty($has_store_column) ? 'disabled' : ''; ?>>
                        <option value="all">Semua Toko</option>
                        <?php foreach ($stores as $store): ?>
                            <option value="<?= $store->id; ?>" <?= (string) $this->input->get('store_id') === (string) $store->id ? 'selected' : ''; ?>><?= h($store->shop_name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-1 d-flex gap-2">
                    <button class="btn btn-primary"><i class="bi bi-search"></i> Filter</button>
                </div>
                <div class="col-lg-1 d-flex gap-2">
                    <a href="<?= site_url('renewal-pipeline'); ?>" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card datatable-card">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="card-title mb-0">Daftar Renewal</h5>
                <span class="text-muted"><?= number_format(count($rows)); ?> akun</span>
            </div>
            <div class="table-responsive">
                <table class="table datatable align-middle renewal-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Buyer</th>
                            <th>Produk</th>
                            <th>Toko</th>
                            <th>Expire</th>
                            <th>Renewal</th>
                            <th>Catatan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($rows): foreach ($rows as $account): ?>
                        <?php
                        $expiredTs = $account->expired_at ? strtotime($account->expired_at) : null;
                        $isOverdue = $expiredTs && $expiredTs < strtotime(date('Y-m-d 00:00:00'));
                        $days = $expiredTs ? floor(($expiredTs - time()) / 86400) : null;
                        $diffLabel = '-';
                        if ($days !== null) {
                            $diffLabel = $days < 0 ? abs($days).' hari lalu' : ($days === 0 ? 'Hari ini' : $days.' hari lagi');
                        }
                        ?>
                        <tr>
                            <td>
                                <div class="order-code">ACC-<?= str_pad((string) $account->id, 6, '0', STR_PAD_LEFT); ?></div>
                                <span class="account-code">Internal: ACC-<?= strtoupper(dechex($account->id)); ?></span>
                                <span class="subtext"><?= !empty($account->created_at) ? date('d M Y', strtotime($account->created_at)) : '-'; ?></span>
                            </td>
                            <td>
                                <span class="main-text"><?= h($account->email ? 'Buyer '.$account->id : 'Buyer ACC-'.$account->id); ?></span>
                                <span class="subtext"><?= h($account->email ?: '-'); ?></span>
                            </td>
                            <td>
                                <span class="main-text"><?= h($account->product_name ?: 'Tanpa Produk'); ?></span>
                                <?php if (!empty($account->variation)): ?><span class="product-chip"><?= h($account->variation); ?></span><?php endif; ?>
                            </td>
                            <td><span class="text-muted"><?= h(isset($account->shop_name) && $account->shop_name ? $account->shop_name : '-'); ?></span></td>
                            <td>
                                <span class="expire-text <?= $isOverdue ? '' : 'upcoming'; ?>"><?= $expiredTs ? date('d/m/Y', $expiredTs) : '-'; ?></span>
                                <span class="subtext"><?= h($diffLabel); ?></span>
                            </td>
                            <td><span class="renewal-pill">Belum Diproses</span></td>
                            <td><input type="text" class="form-control note-input" placeholder="catatan opsional" value="<?= h($account->notes ?? ''); ?>"></td>
                            <td class="text-end">
                                <div class="action-stack">
                                    <div class="action-row">
                                        <button type="button" class="btn btn-sm btn-outline-primary">Follow Up</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger">Lost</button>
                                        <a href="<?= site_url('orders'); ?>" class="btn btn-sm btn-outline-success">Buat Renewal</a>
                                    </div>
                                    <span class="subtext">Perpanjangan: checkout ulang</span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted empty-state">
                                <div class="mb-2"><i class="bi bi-check-circle" style="font-size:2rem;"></i></div>
                                Tidak ada akun expired untuk periode ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

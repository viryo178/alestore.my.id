<div class="pagetitle">
    <h1>Detail Order</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard'); ?>">Dashboard</a></li><li class="breadcrumb-item"><a href="<?= site_url('orders'); ?>">Riwayat Order</a></li><li class="breadcrumb-item active">Detail</li></ol></nav>
</div>

<?php if ($order): ?>
    <?php
    $accountUsedSlot = $assigned_account ? (int) $assigned_account->used_slot : null;
    $accountMaxUser = !empty($order->account_max_user) ? (int) $order->account_max_user : 1;
    if ($assigned_account && (int) $assigned_account->max_slot > 0) {
        $accountMaxUser = (int) $assigned_account->max_slot;
    } elseif (!empty($order->account_max_user)) {
        $accountMaxUser = (int) $order->account_max_user;
    }
    $slotLabel = $accountUsedSlot !== null ? $accountUsedSlot.'/'.$accountMaxUser : '-/'.$accountMaxUser;
    ?>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><?= h($order->shopee_order_id); ?></h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <p>Toko: <strong><?= h($order->shop_name); ?></strong></p>
                    <p>Produk: <?= h($order->product_name); ?> <?= h($order->variation); ?></p>
                    <p>Buyer: <?= h($order->buyer_email); ?></p>
                    <p>Admin: <?= h($order->admin_name ?: '-'); ?></p>
                </div>
                <div class="col-md-6">
                    <p>Status: <?= status_badge($order->status); ?></p>
                    <p>Total: <strong><?= rupiah($order->total); ?></strong></p>
                    <p>Expired: <?= h($order->expired_at); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Akun Diberikan</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="small text-muted">Username / Email</div>
                    <strong><?= h($order->account_username ?? '-'); ?></strong>
                </div>
                <div class="col-md-4">
                    <div class="small text-muted">Password</div>
                    <strong><?= h($order->account_password ?? '-'); ?></strong>
                </div>
                <div class="col-md-4">
                    <div class="small text-muted">Slot User</div>
                    <strong><?= h($slotLabel); ?></strong>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Klaim Garansi Terkait</h5>
            <table class="table">
                <thead><tr><th>Pembeli</th><th>Alasan</th><th>Status</th></tr></thead>
                <tbody>
                <?php if ($claims): foreach ($claims as $claim): ?>
                    <tr><td><?= h($claim->buyer_name); ?></td><td><?= h($claim->reason); ?></td><td><?= status_badge($claim->status); ?></td></tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="3" class="text-muted text-center">Belum ada klaim garansi.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php
$accountType = $row->account_type ?? 'private';
$method = $row->method ?? 'credentials';
$status = $row->status ?? 'available';
$status = in_array($status, array('unavailable', 'unvailabel'), true) ? 'sold' : $status;
$hasExtraInfo = $this->db->field_exists('extra_info', 'digital_accounts');
$hasSoldAt = $this->db->field_exists('sold_at', 'digital_accounts');
$hasVariationId = $this->db->field_exists('digital_product_variation_id', 'digital_accounts');
?>

<div class="digital-account-form row g-4">
    <div class="col-md-6">
        <label class="form-label">Produk AI</label>
        <input type="text" name="product_name" class="form-control" value="<?= h($row->product_name ?? ''); ?>" list="productOptions" placeholder="Contoh: ChatGPT PLUS" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Master Produk</label>
        <select class="form-select" name="digital_product_id">
            <option value="">Manual</option>
            <?php foreach ($products as $product): ?>
                <option value="<?= $product->id; ?>" <?= isset($row->digital_product_id) && (int) $row->digital_product_id === (int) $product->id ? 'selected' : ''; ?>><?= h($product->name); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if ($hasVariationId): ?>
        <div class="col-md-6">
            <label class="form-label">Variasi Master</label>
            <select class="form-select" name="digital_product_variation_id">
                <option value="">Manual / Tanpa Variasi</option>
                <?php if ($this->db->table_exists('digital_product_variations')): foreach ($this->App_model->all('digital_product_variations', 'label ASC') as $variation): ?>
                    <option value="<?= $variation->id; ?>" <?= isset($row->digital_product_variation_id) && (int) $row->digital_product_variation_id === (int) $variation->id ? 'selected' : ''; ?>><?= h($variation->label); ?></option>
                <?php endforeach; endif; ?>
            </select>
        </div>
    <?php endif; ?>

    <div class="col-md-6">
        <label class="form-label">Variasi</label>
        <input class="form-control" name="variation" value="<?= h($row->variation ?? ''); ?>" placeholder="Contoh: 1 Bulan, Lifetime, 35 Days">
    </div>

    <div class="col-md-6">
        <label class="form-label">Tipe Akun</label>
        <select name="account_type" class="form-select" required>
            <option value="private" <?= $accountType === 'private' ? 'selected' : ''; ?>>Private</option>
            <option value="sharing" <?= $accountType === 'sharing' ? 'selected' : ''; ?>>Sharing</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Method</label>
        <select name="method" class="form-select" required>
            <?php foreach (array('credentials' => 'Credentials', 'invite_email' => 'Invite Email', 'link' => 'Invite / Link', 'license' => 'License Key') as $value => $label): ?>
                <option value="<?= $value; ?>" <?= $method === $value ? 'selected' : ''; ?>><?= h($label); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            <?php foreach (array('available' => 'Tersedia', 'verified' => 'Verif', 'active_age' => 'Umur Aktif', 'sold' => 'Sold', 'no_access' => 'No Access', 'deactived' => 'Deactived') as $value => $label): ?>
                <option value="<?= $value; ?>" <?= $status === $value ? 'selected' : ''; ?>><?= h($label); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Email / Username Akun</label>
        <input type="text" name="email" class="form-control" value="<?= h($row->email ?? ''); ?>" placeholder="email@example.com">
    </div>

    <div class="col-md-6">
        <label class="form-label">Password / Link / License</label>
        <input type="text" name="password" class="form-control" value="<?= h($row->password ?? ''); ?>" placeholder="Password, invite link, atau license key">
    </div>

    <?php if ($hasExtraInfo): ?>
        <div class="col-12">
            <label class="form-label">Info Tambahan</label>
            <input type="text" name="extra_info" class="form-control" value="<?= h($row->extra_info ?? ''); ?>" placeholder="Profil ke-3, PIN: 1234, Slot A, dll">
        </div>
    <?php endif; ?>

    <div class="col-md-6">
        <label class="form-label">Max Slot</label>
        <input type="number" name="max_slot" class="form-control" value="<?= h($row->max_slot ?? '1'); ?>" min="1" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Slot Terpakai</label>
        <input type="number" name="used_slot" class="form-control" value="<?= h($row->used_slot ?? '0'); ?>" min="0" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">HPP</label>
        <input type="number" name="hpp" class="form-control" value="<?= h($row->hpp ?? '0'); ?>" min="0" step="0.01">
    </div>

    <div class="col-md-6">
        <label class="form-label">Expired Akun</label>
        <input type="datetime-local" name="expired_at" class="form-control" value="<?= isset($row->expired_at) && $row->expired_at ? date('Y-m-d\TH:i', strtotime($row->expired_at)) : ''; ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Pilih Durasi Cepat</label>
        <select class="form-select expire-duration-picker" name="expired_days">
            <option value="">Manual / tidak disetting</option>
            <?php foreach (($durations ?? array()) as $duration): ?>
                <option value="<?= (int) $duration->days; ?>" <?= !isset($row->id) && $duration->is_default ? 'selected' : ''; ?>><?= h($duration->label); ?> - <?= (int) $duration->days; ?> hari</option>
            <?php endforeach; ?>
        </select>
        <div class="form-text">Memilih durasi akan mengisi tanggal expired otomatis dari hari ini.</div>
    </div>

    <?php if ($hasSoldAt): ?>
        <div class="col-md-6">
            <label class="form-label">Tanggal Sold</label>
            <input type="datetime-local" name="sold_at" class="form-control" value="<?= isset($row->sold_at) && $row->sold_at ? date('Y-m-d\TH:i', strtotime($row->sold_at)) : ''; ?>">
        </div>
    <?php endif; ?>

    <div class="col-12">
        <label class="form-label">Info Tambahan</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="PIN, profil, buyer, catatan admin"><?= h($row->notes ?? ''); ?></textarea>
    </div>
</div>

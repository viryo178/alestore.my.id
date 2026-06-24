<div class="mb-3">
    <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" value="<?= h($product->name ?? ''); ?>" placeholder="cth: Netflix, Spotify, dll" required>
</div>

<div class="mb-3">
    <label class="form-label">Harga Default (Rp)</label>
    <input type="number" name="hpp" class="form-control" value="<?= h($product->hpp ?? 0); ?>" min="0" step="100" required>
    <div class="form-text">Harga saat tidak ada variasi. Bisa di-override per variasi.</div>
</div>

<div class="mb-0">
    <label class="form-label">Jenis Fulfillment <span class="text-danger">*</span></label>
    <select name="method" class="form-select" required>
        <?php foreach ($fulfillment_options as $value => $label): ?>
            <option value="<?= h($value); ?>" <?= (($product->method ?? 'credentials') === $value) ? 'selected' : ''; ?>><?= h($label); ?></option>
        <?php endforeach; ?>
    </select>
</div>

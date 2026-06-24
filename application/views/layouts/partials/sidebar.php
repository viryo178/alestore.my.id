<?php
$current = $this->uri->segment(1);
$second = $this->uri->segment(2);
$digitalOpen = ($current === 'digital-accounts' && $second !== 'password-expired') || $current === 'available-accounts';
$pendingWarranty = 0;
if (isset($this->db) && $this->db->table_exists('warranty_claims')) {
    $pendingWarranty = $this->db->where('status', 'pending')->count_all_results('warranty_claims');
}
?>
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link <?= in_array($current, array('admin', 'dashboard'), true) ? 'active' : 'collapsed'; ?>" href="<?= site_url('admin/dashboard'); ?>">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-heading">Management</li>

        <li class="nav-item">
            <a class="nav-link <?= active_menu('customers'); ?>" href="<?= site_url('customers'); ?>">
                <i class="bi bi-people"></i>
                <span>Customers</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= active_menu('users'); ?>" href="<?= site_url('users'); ?>">
                <i class="bi bi-person-gear"></i>
                <span>Users</span>
            </a>
        </li>

                <li class="nav-item">
            <a class="nav-link <?= $current === 'orders' && $second === 'create' ? 'active' : 'collapsed'; ?>" href="<?= site_url('orders/create'); ?>">
                <i class="bi bi-cart-plus"></i>
                <span>Input Order</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= $current === 'orders' && $second !== 'create' ? 'active' : 'collapsed'; ?>" href="<?= site_url('orders'); ?>">
                <i class="bi bi-cart"></i>
                <span>Riwayat Order</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= active_menu('renewal-pipeline'); ?>" href="<?= site_url('renewal-pipeline'); ?>">
                <i class="bi bi-arrow-repeat"></i>
                <span>Renewal Pipeline</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= active_menu('warranty-claims'); ?>" href="<?= site_url('warranty-claims'); ?>">
                <i class="bi bi-shield-check"></i>
                <span>Klaim Garansi</span>
                <?php if ($pendingWarranty > 0): ?>
                    <span class="badge bg-warning ms-auto"><?= (int) $pendingWarranty; ?></span>
                <?php endif; ?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= active_menu('activity-logs'); ?>" href="<?= site_url('activity-logs'); ?>">
                <i class="bi bi-clock-history"></i>
                <span>Activity Log</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= active_menu('financial-reports'); ?>" href="<?= site_url('financial-reports'); ?>">
                <i class="bi bi-bar-chart"></i>
                <span>Laporan Keuangan</span>
            </a>
        </li>

        <li class="nav-heading">Management Akun Digital</li>

        <li class="nav-item">
            <a class="nav-link <?= $digitalOpen ? '' : 'collapsed'; ?>" data-bs-target="#digital-account-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-collection"></i>
                <span>Management Akun Digital</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="digital-account-nav" class="nav-content collapse <?= $digitalOpen ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a class="<?= $current === 'digital-accounts' && $second !== 'password-expired' && $this->input->get('section') === 'product-stock' ? 'active' : ''; ?>" href="<?= site_url('digital-accounts?section=product-stock'); ?>">
                        <i class="bi bi-box-seam"></i>
                        <span>Stok Per Produk/Variasi</span>
                    </a>
                </li>
                <li>
                    <a class="<?= $current === 'digital-accounts' && $second !== 'password-expired' && $this->input->get('section') !== 'product-stock' ? 'active' : ''; ?>" href="<?= site_url('digital-accounts?section=account-stock'); ?>">
                        <i class="bi bi-person-fill"></i>
                        <span>Stok Akun Digital</span>
                    </a>
                </li>
                <li>
                    <a class="<?= $current === 'digital-accounts' && $second !== 'password-expired' && $this->input->get('method') === 'license' ? 'active' : ''; ?>" href="<?= site_url('digital-accounts?section=license-stock&method=license'); ?>">
                        <i class="bi bi-lock-fill"></i>
                        <span>Stok Digital (License / Link)</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= active_menu('digital-notifications'); ?>" href="<?= site_url('digital-notifications'); ?>">
                <i class="bi bi-bell"></i>
                <span>Kelola Notifikasi</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= active_menu('expire-durations'); ?>" href="<?= site_url('expire-durations'); ?>">
                <i class="bi bi-clock"></i>
                <span>Durasi Expired</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= $current === 'digital-accounts' && $second === 'password-expired' ? 'active' : 'collapsed'; ?>" href="<?= site_url('digital-accounts/password-expired'); ?>">
                <i class="bi bi-key"></i>
                <span>Ganti Password Exp</span>
            </a>
        </li>

        <li class="nav-heading">Shopee Integration</li>

        <li class="nav-item">
            <a class="nav-link <?= active_menu('shopee-stores'); ?>" href="<?= site_url('shopee-stores'); ?>">
                <i class="bi bi-shop"></i>
                <span>Shopee Stores</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= active_menu('products'); ?>" href="<?= site_url('products'); ?>">
                <i class="bi bi-tags"></i>
                <span>Nama Produk</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="<?= site_url('logout'); ?>">
                <i class="bi bi-box-arrow-left"></i>
                <span>Logout</span>
            </a>
        </li>

    </ul>
</aside>

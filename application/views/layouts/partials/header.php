<?php
$adminName = $this->session->userdata('user_name') ?: 'Admin';
$adminRole = $this->session->userdata('user_role') ?: 'Administrator';
$expiredSoon = 0;
$statusAttention = 0;
if (isset($this->db) && $this->db->table_exists('digital_accounts')) {
    $expiredSoon = $this->db
        ->where('expired_at IS NOT NULL', null, false)
        ->where('expired_at >=', date('Y-m-d H:i:s'))
        ->where('expired_at <=', date('Y-m-d H:i:s', strtotime('+2 days')))
        ->where_not_in('status', array('sold', 'deactived', 'no_access'))
        ->count_all_results('digital_accounts');

    $statusAttention = $this->db
        ->where_in('status', array('deactived', 'active_age', 'no_access', 'verified'))
        ->count_all_results('digital_accounts');
}
$totalNotification = $expiredSoon + $statusAttention;
?>
<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
        <a href="<?= site_url('admin/dashboard'); ?>" class="logo d-flex align-items-center">
            <img src="<?= base_url('assets/img/logo.png'); ?>" alt="">
            <span class="d-none d-lg-block">Alestore</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <div class="search-bar">
        <form class="search-form d-flex align-items-center" method="GET" action="<?= site_url('digital-accounts'); ?>">
            <input type="text" name="q" value="<?= h($this->input->get('q')); ?>" placeholder="Cari akun...">
            <button type="submit"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">

            <li class="nav-item d-block d-lg-none">
                <a class="nav-link nav-icon search-bar-toggle">
                    <i class="bi bi-search"></i>
                </a>
            </li>

            <li class="nav-item pe-3">
                <span class="nav-link small text-muted" id="wibClock"><?= date('d M Y, H:i'); ?> WIB</span>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link nav-icon" data-bs-toggle="dropdown" href="#">
                    <i class="bi bi-bell"></i>
                    <?php if ($totalNotification > 0): ?>
                        <span class="badge bg-primary badge-number"><?= (int) $totalNotification; ?></span>
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                    <li class="dropdown-header"><?= (int) $totalNotification; ?> notifikasi akun digital</li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="notification-item">
                        <i class="bi bi-key text-warning"></i>
                        <div>
                            <h4>Password Expired</h4>
                            <p><?= (int) $expiredSoon; ?> akun akan expired dalam 2 hari.</p>
                            <p><?= date('d M Y, H:i'); ?> WIB</p>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="notification-item">
                        <i class="bi bi-person-exclamation text-danger"></i>
                        <div>
                            <h4>Status Perhatian</h4>
                            <p><?= (int) $statusAttention; ?> akun butuh dicek.</p>
                            <p><?= date('d M Y, H:i'); ?> WIB</p>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="dropdown-footer"><a href="<?= site_url('digital-notifications'); ?>">Kelola semua notifikasi</a></li>
                </ul>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link nav-icon" data-bs-toggle="dropdown" href="#">
                    <i class="bi bi-chat-left-text"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
                    <li class="dropdown-header">You have 3 new messages</li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="message-item">
                        <a href="#">
                            <img src="<?= base_url('assets/img/messages-1.jpg'); ?>" class="rounded-circle" alt="">
                            <div>
                                <h4>Admin</h4>
                                <p>Order sudah diproses</p>
                                <p>5 min ago</p>
                            </div>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="dropdown-footer"><a href="#">Show all messages</a></li>
                </ul>
            </li>

            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center" data-bs-toggle="dropdown" href="#">
                    <img src="<?= base_url('assets/img/profile-img.jpg'); ?>" class="rounded-circle" alt="">
                    <span class="d-none d-md-block dropdown-toggle ps-2"><?= h($adminName); ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6><?= h($adminName); ?></h6>
                        <span><?= h(ucwords($adminRole)); ?></span>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="<?= site_url('users'); ?>"><i class="bi bi-person"></i><span>Profile</span></a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i class="bi bi-gear"></i><span>Settings</span></a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="<?= site_url('logout'); ?>"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const clock = document.getElementById('wibClock');
    if (!clock) return;
    const formatter = new Intl.DateTimeFormat('id-ID', {
        timeZone: 'Asia/Jakarta',
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    const tick = function () { clock.textContent = formatter.format(new Date()) + ' WIB'; };
    tick();
    setInterval(tick, 1000);
});
</script>

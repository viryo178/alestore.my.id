<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= isset($title) ? h($title) : 'Login AleStore'; ?></title>
    <link href="<?= base_url('assets/img/favicon.png'); ?>" rel="icon">
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Nunito|Poppins" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/boxicons/css/boxicons.min.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/remixicon/remixicon.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/alestore-login.css'); ?>" rel="stylesheet">
</head>
<body class="alestore-login">
    <main class="login-shell">
        <section class="login-brand">
            <a href="<?= site_url('login'); ?>" class="brand-mark">
                <img src="<?= base_url('assets/img/logo.png'); ?>" alt="AleStore">
                <span>AleStore</span>
            </a>
            <div class="brand-copy">
                <span class="eyebrow">Digital Account Management</span>
                <h1>Kelola stok, order, dan garansi dari satu dashboard.</h1>
                <p>Masuk untuk memantau akun digital, toko, laporan, dan notifikasi operasional dengan cepat.</p>
            </div>
            <div class="brand-stats">
                <div>
                    <strong>24/7</strong>
                    <span>Monitoring</span>
                </div>
                <div>
                    <strong>WIB</strong>
                    <span>Realtime Clock</span>
                </div>
                <div>
                    <strong>CI3</strong>
                    <span>Admin Panel</span>
                </div>
            </div>
        </section>

        <section class="login-panel" aria-label="Form login">
            <div class="panel-top">
                <div>
                    <span class="eyebrow">Welcome Back</span>
                    <h2>Login Admin</h2>
                </div>
                <i class="bi bi-shield-lock"></i>
            </div>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger"><?= h($this->session->flashdata('error')); ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success"><?= h($this->session->flashdata('success')); ?></div>
            <?php endif; ?>

            <form action="<?= site_url('login'); ?>" method="POST" autocomplete="on">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" value="<?= h($this->input->post('email')); ?>" placeholder="admin@alestore.test" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                    </div>
                </div>

                <button class="btn btn-login w-100" type="submit">
                    <span>Masuk Dashboard</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

        </section>
    </main>
    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
</body>
</html>

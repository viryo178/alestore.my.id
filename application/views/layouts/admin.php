<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= isset($title) ? h($title) : 'Ale Store'; ?></title>
    <link href="<?= base_url('assets/img/favicon.png'); ?>" rel="icon">
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Nunito|Poppins" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/boxicons/css/boxicons.min.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/remixicon/remixicon.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/simple-datatables/style.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css'); ?>" rel="stylesheet">
    <style>
        <?php if (is_file(FCPATH.'assets/css/alestore-layout.css')) { readfile(FCPATH.'assets/css/alestore-layout.css'); } ?>
        html,
        body,
        body.toggle-sidebar,
        #main.main {
            background: #06172d !important;
            color: #f8fbff !important;
            min-height: 100vh;
        }

        .header,
        .sidebar {
            background: #06172d !important;
            border-color: rgba(47, 124, 255, .14) !important;
        }

        .card,
        .modal-content,
        .dropdown-menu,
        .datatable-card.card,
        .filter-card.card {
            background: #071b31 !important;
            border-color: #142d49 !important;
            color: #f8fbff !important;
        }

        .table,
        .table td,
        .table th,
        .card,
        .card-body,
        .card-title,
        .pagetitle h1,
        label {
            color: #f8fbff !important;
        }

        .table thead th {
            background: #0a2038 !important;
            color: #8da0bd !important;
        }

        .form-control,
        .form-select,
        textarea.form-control,
        .datatable-input,
        .datatable-selector {
            background: #061426 !important;
            border-color: #142d49 !important;
            color: #f8fbff !important;
        }

        .text-muted,
        .small,
        .breadcrumb,
        .breadcrumb a,
        .card-title span {
            color: #8da0bd !important;
        }

        /* Final AleStore color lock: keep CI 3 visually aligned with the Laravel theme. */
        :root {
            --alestore-bg: #06172d;
            --alestore-panel: #071b31;
            --alestore-panel-soft: #07182d;
            --alestore-table-head: #0a2038;
            --alestore-input: #061426;
            --alestore-border: #142d49;
            --alestore-text: #f8fbff;
            --alestore-muted: #8da0bd;
            --alestore-link: #6f9ff0;
            --alestore-active: #1f55b5;
            --alestore-active-border: #2f6ed9;
            --alestore-green: #00b77f;
            --alestore-red: #e04e6c;
            --alestore-yellow: #d49b1f;
            --alestore-info: #56a7e6;
        }

        html,
        body,
        body.toggle-sidebar,
        #main.main,
        .main {
            background-color: var(--alestore-bg) !important;
            color: var(--alestore-text) !important;
        }

        .header,
        .sidebar {
            background-color: var(--alestore-bg) !important;
            border-color: rgba(47, 124, 255, .14) !important;
            box-shadow: none !important;
        }

        .header {
            border-bottom: 1px solid rgba(47, 124, 255, .14) !important;
        }

        .sidebar {
            border-right: 1px solid rgba(47, 124, 255, .14) !important;
        }

        .card,
        .card.info-card,
        .modal-content,
        .dropdown-menu,
        .datatable-card.card,
        .filter-card.card,
        .finance-filter-card.card {
            background: var(--alestore-panel) !important;
            border: 1px solid var(--alestore-border) !important;
            box-shadow: 0 18px 42px rgba(0, 0, 0, .18) !important;
        }

        .pagetitle h1,
        .logo span,
        .card-title,
        .card h1,
        .card h2,
        .card h3,
        .card h4,
        .card h5,
        .card h6,
        .card p,
        .card strong,
        .table,
        .table td,
        .table th,
        label,
        .nav-profile span {
            color: var(--alestore-text) !important;
        }

        .breadcrumb,
        .breadcrumb a,
        .text-muted,
        .small,
        .form-text,
        .card-title span,
        .sidebar-nav .nav-heading {
            color: var(--alestore-muted) !important;
        }

        a,
        .text-primary {
            color: var(--alestore-link) !important;
        }

        .table {
            --bs-table-bg: transparent !important;
            --bs-table-color: var(--alestore-text) !important;
            --bs-table-border-color: rgba(145, 168, 201, .18) !important;
            background: transparent !important;
        }

        .table thead th,
        .datatable-card .table thead th,
        .dataTable-table thead th {
            background: var(--alestore-table-head) !important;
            color: var(--alestore-muted) !important;
            border-bottom-color: rgba(141, 160, 189, .20) !important;
        }

        .table tbody td,
        .dataTable-table tbody td {
            color: #e5ecf8 !important;
            border-bottom-color: rgba(141, 160, 189, .11) !important;
        }

        .form-control,
        .form-select,
        textarea.form-control,
        .datatable-input,
        .datatable-selector,
        .search-bar .search-form input {
            background-color: var(--alestore-input) !important;
            border-color: var(--alestore-border) !important;
            color: var(--alestore-text) !important;
            box-shadow: none !important;
        }

        .form-control::placeholder,
        .datatable-input::placeholder,
        .search-bar .search-form input::placeholder {
            color: #9bb4d6 !important;
            opacity: 1 !important;
        }

        .form-control:focus,
        .form-select:focus,
        textarea.form-control:focus {
            background-color: var(--alestore-input) !important;
            border-color: var(--alestore-active-border) !important;
            color: var(--alestore-text) !important;
            box-shadow: 0 0 0 .2rem rgba(47, 110, 217, .16) !important;
        }

        .sidebar-nav .nav-link,
        .sidebar-nav .nav-link.collapsed {
            background: transparent !important;
            border: 1px solid transparent !important;
            color: #dce8ff !important;
        }

        .sidebar-nav .nav-link i,
        .sidebar-nav .nav-link span,
        .sidebar-nav .nav-link.collapsed i,
        .sidebar-nav .nav-link.collapsed span {
            color: #dce8ff !important;
        }

        .sidebar-nav .nav-link.active,
        .sidebar-nav .nav-link:not(.collapsed),
        .sidebar-nav .nav-link:hover {
            background: var(--alestore-active) !important;
            border-color: var(--alestore-active-border) !important;
            color: #ffffff !important;
            box-shadow: 0 0 14px rgba(47, 110, 217, .22) !important;
        }

        .sidebar-nav .nav-content a {
            color: #dce8ff !important;
        }

        .sidebar-nav .nav-content a:hover,
        .sidebar-nav .nav-content a.active {
            background: rgba(47, 110, 217, .12) !important;
            border-color: rgba(47, 110, 217, .28) !important;
            color: #ffffff !important;
        }

        .btn-primary {
            background: var(--alestore-active) !important;
            border-color: var(--alestore-active-border) !important;
            color: #ffffff !important;
            box-shadow: 0 0 14px rgba(47, 110, 217, .22) !important;
        }

        .btn-outline-primary {
            background: transparent !important;
            border-color: #285caf !important;
            color: #70a0f0 !important;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary.active {
            background: var(--alestore-active) !important;
            border-color: var(--alestore-active-border) !important;
            color: #ffffff !important;
        }

        .btn-outline-secondary {
            border-color: #26314f !important;
            color: #8ea5d0 !important;
        }

        .badge.bg-success,
        .text-success {
            color: var(--alestore-green) !important;
        }

        .badge.bg-success,
        .badge.bg-primary {
            background: rgba(0, 168, 116, .13) !important;
            color: var(--alestore-green) !important;
        }

        .badge.bg-info {
            background: rgba(38, 115, 185, .16) !important;
            color: var(--alestore-info) !important;
        }

        .badge.bg-warning {
            background: rgba(212, 155, 31, .14) !important;
            color: var(--alestore-yellow) !important;
        }

        .badge.bg-danger,
        .text-danger {
            background: rgba(217, 54, 89, .14) !important;
            color: var(--alestore-red) !important;
        }

        .badge.bg-secondary {
            background: rgba(125, 143, 176, .14) !important;
            color: #9aa9c4 !important;
        }

        .progress {
            background-color: rgba(110, 130, 165, .12) !important;
        }

        .progress-bar {
            background: linear-gradient(90deg, #245fbd, #3f58b8) !important;
            box-shadow: 0 0 12px rgba(47, 110, 217, .32) !important;
        }

        .dataTable-top,
        .dataTable-bottom,
        .datatable-top,
        .datatable-bottom,
        .datatable-info,
        .datatable-dropdown,
        .datatable-search {
            color: var(--alestore-text) !important;
            background: transparent !important;
        }

        .dataTable-bottom,
        .datatable-bottom {
            align-items: center !important;
            border-top: 1px solid rgba(141, 160, 189, .22) !important;
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 12px !important;
            justify-content: space-between !important;
            padding: 10px !important;
        }

        .datatable-pagination ul,
        .datatable-pagination-list {
            align-items: center !important;
            display: flex !important;
            gap: 4px !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .datatable-pagination {
            margin-left: auto !important;
            text-align: right !important;
        }

        .datatable-pagination li,
        .datatable-pagination-list li {
            margin: 0 !important;
        }

        .datatable-pagination a,
        .datatable-pagination button {
            align-items: center !important;
            background: #061426 !important;
            border: 1px solid #245fbd !important;
            border-radius: 4px !important;
            color: #6f9ff0 !important;
            display: inline-flex !important;
            font-size: 14px !important;
            height: 32px !important;
            justify-content: center !important;
            line-height: 1 !important;
            min-width: 36px !important;
            padding: 0 11px !important;
        }

        .datatable-pagination a:hover,
        .datatable-pagination button:hover {
            background: rgba(47, 110, 217, .18) !important;
            color: #ffffff !important;
        }

        .datatable-pagination .datatable-active a,
        .datatable-pagination .datatable-active a:focus,
        .datatable-pagination .datatable-active a:hover,
        .datatable-pagination .datatable-active button,
        .datatable-pagination .datatable-active button:focus,
        .datatable-pagination .datatable-active button:hover {
            background: #1f55b5 !important;
            border-color: #2f6ed9 !important;
            color: #ffffff !important;
            box-shadow: none !important;
        }

        .datatable-pagination .datatable-ellipsis a,
        .datatable-pagination .datatable-disabled a,
        .datatable-pagination .datatable-disabled a:focus,
        .datatable-pagination .datatable-disabled a:hover,
        .datatable-pagination .datatable-ellipsis button,
        .datatable-pagination .datatable-disabled button,
        .datatable-pagination .datatable-disabled button:focus,
        .datatable-pagination .datatable-disabled button:hover {
            background: #061426 !important;
            border-color: #142d49 !important;
            color: #8da0bd !important;
            opacity: 1 !important;
        }

        .dropdown-divider,
        .modal-header,
        .modal-footer,
        .border-bottom {
            border-color: rgba(110, 130, 165, .14) !important;
        }

    </style>
</head>
<body>
<?php $this->load->view('layouts/partials/header'); ?>
<?php $this->load->view('layouts/partials/sidebar'); ?>
<main id="main" class="main">
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= h($this->session->flashdata('success')); ?></div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= h($this->session->flashdata('error')); ?></div>
    <?php endif; ?>
    <?php $this->load->view($content_view); ?>
</main>
<?php $this->load->view('layouts/partials/footer'); ?>
<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendor/chart.js/chart.umd.js'); ?>"></script>
<script src="<?= base_url('assets/vendor/simple-datatables/simple-datatables.js'); ?>"></script>
<script src="<?= base_url('assets/vendor/apexcharts/apexcharts.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/main.js'); ?>"></script>
<script>
document.querySelectorAll('.datatable').forEach(function(table){ new simpleDatatables.DataTable(table); });
</script>
</body>
</html>

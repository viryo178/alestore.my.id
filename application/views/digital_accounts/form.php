<div class="pagetitle"><h1><?= h($title); ?></h1></div>
<div class="card"><div class="card-body"><h5 class="card-title">Form Akun Digital</h5>
<form method="post" action="<?= $row ? site_url('digital-accounts/update/'.$row->id) : site_url('digital-accounts/store'); ?>">
    <?php $this->load->view('digital_accounts/form_fields', compact('row', 'products')); ?>
    <div class="mt-3"><button class="btn btn-primary">Simpan</button> <a class="btn btn-outline-secondary" href="<?= site_url('digital-accounts'); ?>">Kembali</a></div>
</form></div></div>

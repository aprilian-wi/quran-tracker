<?php
// src/Views/admin/create_teacher.php
require_once __DIR__ . '/../../Helpers/functions.php';
include __DIR__ . '/../layouts/main.php';
?>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Masukan Data Guru</h4>
            <a href="<?= BASE_URL ?>public/index.php?page=admin/teachers" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
        <form method="POST" action="<?= BASE_URL ?>public/index.php?page=create_teacher">
            <?= csrfInput() ?>
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            <button class="btn btn-primary">Tambah Guru</button>
            <a href="<?= BASE_URL ?>public/index.php?page=admin/teachers" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

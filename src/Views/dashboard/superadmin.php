<?php
// src/Views/dashboard/superadmin.php
require_once __DIR__ . '/../../Controllers/DashboardController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new DashboardController($pdo);
$data = $controller->index();

include __DIR__ . '/../layouts/main.php';
?>

<div class="dashboard-panel">

<div class="row g-4">
    <!-- Total Teachers -->
    <div class="col-md-3">
        <div class="card text-white bg-primary h-100">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title"><i class="bi bi-person-badge"></i> Guru</h5>
                <h2 class="mb-0"><?= $data['total_teachers'] ?></h2>
            </div>
        </div>
    </div>

    <!-- Total Parents -->
    <div class="col-md-3">
        <div class="card text-white bg-success h-100">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title"><i class="bi bi-people"></i> Wali Siswa</h5>
                <h2 class="mb-0"><?= $data['total_parents'] ?></h2>
            </div>
        </div>
    </div>

    <!-- Total Children -->
    <div class="col-md-3">
        <div class="card text-white bg-info h-100">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title"><i class="bi bi-person-hearts"></i> Siswa</h5>
                <h2 class="mb-0"><?= $data['total_children'] ?></h2>
            </div>
        </div>
    </div>

    <!-- Total Classes -->
    <div class="col-md-3">
        <div class="card text-white bg-warning h-100">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title"><i class="bi bi-building"></i> Kelas</h5>
                <h2 class="mb-0"><?= $data['total_classes'] ?></h2>
            </div>
        </div>
    </div>
</div>

    <div class="mt-5">
        <h3>Aksi Cepat</h3>
        <div class="row g-3">
            <div class="col-md-3">
                <a href="<?= BASE_URL ?>public/index.php?page=admin/teachers" class="btn btn-primary w-100 py-3">
                    <i class="bi bi-person-plus"></i> Tambah Guru
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= BASE_URL ?>public/index.php?page=admin/parents" class="btn btn-success w-100 py-3">
                    <i class="bi bi-person-plus"></i> Tambah Wali
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= BASE_URL ?>public/index.php?page=admin/classes" class="btn btn-warning w-100 py-3">
                    <i class="bi bi-building"></i> Atur Kelas
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= BASE_URL ?>public/index.php?page=admin/teaching_books" class="btn btn-info w-100 py-3">
                    <i class="bi bi-book"></i> Buku Tahsin
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= BASE_URL ?>public/index.php?page=admin/manage_hadiths" class="btn btn-warning w-100 py-3">
                    <i class="bi bi-book-half"></i> Hadits
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= BASE_URL ?>public/index.php?page=admin/manage_short_prayers" class="btn btn-success w-100 py-3">
                    <i class="bi bi-journal-text"></i> Doa Pendek
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= BASE_URL ?>public/index.php?page=admin/list_children" class="btn btn-info w-100 py-3">
                    <i class="bi bi-people"></i> Daftar Siswa
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= BASE_URL ?>public/index.php?page=admin/users" class="btn btn-secondary w-100 py-3">
                    <i class="bi bi-people"></i> Semua User
                </a>
            </div>
        </div>
    </div>

</div>
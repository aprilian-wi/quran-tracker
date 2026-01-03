<?php
// src/Views/dashboard/school_admin.php
$pageTitle = 'Dashboard School Admin';
include __DIR__ . '/../layouts/main.php';

require_once __DIR__ . '/../../Controllers/DashboardController.php';

$controller = new DashboardController($pdo);
$data = $controller->index(); 
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2>Admin Sekolah</h2>
        <p class="text-muted">Selamat Datang, <?= h($_SESSION['user_name']) ?></p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card bg-primary text-white h-100 shadow-sm border-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0 small">Total Siswa</h6>
                        <h2 class="mt-2 mb-0 fs-3"><?= $data['total_children'] ?></h2>
                    </div>
                    <i class="bi bi-people fs-2 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card bg-success text-white h-100 shadow-sm border-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0 small">Total Guru</h6>
                        <h2 class="mt-2 mb-0 fs-3"><?= $data['total_teachers'] ?></h2>
                    </div>
                    <i class="bi bi-person-badge fs-2 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card bg-info text-white h-100 shadow-sm border-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0 small">Total Kelas</h6>
                        <h2 class="mt-2 mb-0 fs-3"><?= $data['total_classes'] ?></h2>
                    </div>
                    <i class="bi bi-building fs-2 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card bg-warning text-dark h-100 shadow-sm border-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0 small">Total Qrang Tua</h6>
                        <h2 class="mt-2 mb-0 fs-3"><?= $data['total_parents'] ?></h2>
                    </div>
                    <i class="bi bi-person-hearts fs-2 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <!-- Administration Section -->
    <div class="col-12 mb-4">
        <h5 class="mb-3 text-secondary"><i class="bi bi-gear-fill me-2"></i>Administrasi</h5>
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <a href="index.php?page=admin/users" class="card text-decoration-none h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="card-body text-center p-3">
                        <div class="bg-primary bg-opacity-10 text-primary icon-circle mb-2">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                        <h6 class="card-title text-dark mb-0 small">Kelola Pengguna</h6>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="index.php?page=admin/classes" class="card text-decoration-none h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="card-body text-center p-3">
                        <div class="bg-info bg-opacity-10 text-info icon-circle mb-2">
                            <i class="bi bi-building fs-4"></i>
                        </div>
                        <h6 class="card-title text-dark mb-0 small">Kelola Kelas</h6>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="index.php?page=admin/teachers" class="card text-decoration-none h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="card-body text-center p-3">
                        <div class="bg-success bg-opacity-10 text-success icon-circle mb-2">
                            <i class="bi bi-person-badge fs-4"></i>
                        </div>
                        <h6 class="card-title text-dark mb-0 small">Kelola Guru</h6>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="index.php?page=admin/parents" class="card text-decoration-none h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="card-body text-center p-3">
                        <div class="bg-warning bg-opacity-10 text-warning icon-circle mb-2">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                        <h6 class="card-title text-dark mb-0 small">Kelola Orang Tua</h6>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="index.php?page=admin/list_children" class="card text-decoration-none h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="card-body text-center p-3">
                        <div class="bg-danger bg-opacity-10 text-danger icon-circle mb-2">
                            <i class="bi bi-emoji-smile fs-4"></i>
                        </div>
                        <h6 class="card-title text-dark mb-0 small">Kelola Siswa</h6>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Content Management Section -->
    <div class="col-12 mb-4">
        <h5 class="mb-3 text-secondary"><i class="bi bi-collection-play-fill me-2"></i>Manajemen Materi Ajar</h5>
        <div class="row g-3">
            <div class="col-6 col-md-4">
                <a href="index.php?page=admin/teaching_books" class="card text-decoration-none h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="card-body text-center p-3">
                        <div class="bg-purple bg-opacity-10 text-purple icon-circle mb-2" style="color: #6f42c1;">
                            <i class="bi bi-book fs-4"></i>
                        </div>
                        <h6 class="card-title text-dark mb-0 small">Buku Tahsin</h6>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4">
                <a href="index.php?page=admin/manage_short_prayers" class="card text-decoration-none h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="card-body text-center p-3">
                        <div class="bg-teal bg-opacity-10 text-teal icon-circle mb-2" style="color: #20c997;">
                            <i class="bi bi-heart fs-4"></i>
                        </div>
                        <h6 class="card-title text-dark mb-0 small">Doa Pendek</h6>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4">
                <a href="index.php?page=admin/manage_hadiths" class="card text-decoration-none h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="card-body text-center p-3">
                        <div class="bg-orange bg-opacity-10 text-orange icon-circle mb-2" style="color: #fd7e14;">
                            <i class="bi bi-quote fs-4"></i>
                        </div>
                        <h6 class="card-title text-dark mb-0 small">Hadits</h6>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow:hover {
    transform: translateY(-5px);
    box_shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    transition: all 0.3s ease;
}
.transition-all {
    transition: all 0.3s ease;
}
.icon-circle {
    width: 48px;
    height: 48px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}
</style>

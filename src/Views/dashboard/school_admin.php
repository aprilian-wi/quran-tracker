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
        <h2>Dashboard School Admin</h2>
        <p class="text-muted">Welcome, <?= h($_SESSION['user_name']) ?> (<?= h(ucfirst($_SESSION['role'])) ?>)</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Students</h6>
                        <h2 class="mt-2 mb-0"><?= $data['total_children'] ?></h2>
                    </div>
                    <i class="bi bi-people fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Teachers</h6>
                        <h2 class="mt-2 mb-0"><?= $data['total_teachers'] ?></h2>
                    </div>
                    <i class="bi bi-person-badge fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Classes</h6>
                        <h2 class="mt-2 mb-0"><?= $data['total_classes'] ?></h2>
                    </div>
                    <i class="bi bi-building fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Parents</h6>
                        <h2 class="mt-2 mb-0"><?= $data['total_parents'] ?></h2>
                    </div>
                    <i class="bi bi-person-hearts fs-1"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <!-- Administration Section -->
    <div class="col-12 mb-4">
        <h5 class="mb-3 text-secondary"><i class="bi bi-gear-fill me-2"></i>Administration</h5>
        <div class="row g-3">
            <div class="col-md-3 col-sm-6">
                <a href="index.php?page=admin/users" class="card text-decoration-none h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 text-primary icon-circle mb-3">
                            <i class="bi bi-people fs-3"></i>
                        </div>
                        <h6 class="card-title text-dark mb-0">Manage Users</h6>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="index.php?page=admin/classes" class="card text-decoration-none h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="card-body text-center p-4">
                        <div class="bg-info bg-opacity-10 text-info icon-circle mb-3">
                            <i class="bi bi-building fs-3"></i>
                        </div>
                        <h6 class="card-title text-dark mb-0">Manage Classes</h6>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="index.php?page=admin/teachers" class="card text-decoration-none h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="card-body text-center p-4">
                        <div class="bg-success bg-opacity-10 text-success icon-circle mb-3">
                            <i class="bi bi-person-badge fs-3"></i>
                        </div>
                        <h6 class="card-title text-dark mb-0">Manage Teachers</h6>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="index.php?page=admin/parents" class="card text-decoration-none h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="card-body text-center p-4">
                        <div class="bg-warning bg-opacity-10 text-warning icon-circle mb-3">
                            <i class="bi bi-people fs-3"></i>
                        </div>
                        <h6 class="card-title text-dark mb-0">Manage Parents</h6>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="index.php?page=admin/list_children" class="card text-decoration-none h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="card-body text-center p-4">
                        <div class="bg-danger bg-opacity-10 text-danger icon-circle mb-3">
                            <i class="bi bi-emoji-smile fs-3"></i>
                        </div>
                        <h6 class="card-title text-dark mb-0">Manage Children</h6>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Content Management Section -->
    <div class="col-12 mb-4">
        <h5 class="mb-3 text-secondary"><i class="bi bi-collection-play-fill me-2"></i>Content Management</h5>
        <div class="row g-3">
            <div class="col-md-4 col-sm-6">
                <a href="index.php?page=admin/teaching_books" class="card text-decoration-none h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="card-body text-center p-4">
                        <div class="bg-purple bg-opacity-10 text-purple icon-circle mb-3" style="color: #6f42c1;">
                            <i class="bi bi-book fs-3"></i>
                        </div>
                        <h6 class="card-title text-dark mb-0">Teaching Books</h6>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-sm-6">
                <a href="index.php?page=admin/manage_short_prayers" class="card text-decoration-none h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="card-body text-center p-4">
                        <div class="bg-teal bg-opacity-10 text-teal icon-circle mb-3" style="color: #20c997;">
                            <i class="bi bi-heart fs-3"></i>
                        </div>
                        <h6 class="card-title text-dark mb-0">Short Prayers</h6>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-sm-6">
                <a href="index.php?page=admin/manage_hadiths" class="card text-decoration-none h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="card-body text-center p-4">
                        <div class="bg-orange bg-opacity-10 text-orange icon-circle mb-3" style="color: #fd7e14;">
                            <i class="bi bi-quote fs-3"></i>
                        </div>
                        <h6 class="card-title text-dark mb-0">Hadiths</h6>
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
    width: 64px;
    height: 64px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}
</style>

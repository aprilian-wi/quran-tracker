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
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Quick Management</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="index.php?page=admin/users" class="list-group-item list-group-item-action">
                    <i class="bi bi-people me-2"></i> Manage Users
                </a>
                <a href="index.php?page=admin/classes" class="list-group-item list-group-item-action">
                    <i class="bi bi-building me-2"></i> Manage Classes
                </a>
                
                <a href="index.php?page=admin/teachers" class="list-group-item list-group-item-action">
                    <i class="bi bi-person-badge me-2"></i> Manage Teachers
                </a>
                <a href="index.php?page=admin/parents" class="list-group-item list-group-item-action">
                    <i class="bi bi-people me-2"></i> Manage Parents
                </a>
                <a href="index.php?page=admin/list_children" class="list-group-item list-group-item-action">
                    <i class="bi bi-emoji-smile me-2"></i> Manage Children
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Content Management</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="index.php?page=admin/teaching_books" class="list-group-item list-group-item-action">
                    <i class="bi bi-book me-2"></i> Manage Teaching Books
                </a>
                <a href="index.php?page=admin/manage_short_prayers" class="list-group-item list-group-item-action">
                    <i class="bi bi-heart me-2"></i>  Manage Short Prayers
                </a>
                <a href="index.php?page=admin/manage_hadiths" class="list-group-item list-group-item-action">
                    <i class="bi bi-quote me-2"></i> Manage Hadiths
                </a>
            </div>
        </div>
    </div>
</div>

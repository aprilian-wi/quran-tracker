<?php
// src/Views/dashboard/teacher.php
require_once __DIR__ . '/../../Controllers/DashboardController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new DashboardController($pdo);
$data = $controller->index();

include __DIR__ . '/../layouts/main.php';
?>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-journal-text"></i> My Classes</h5>
                <?php if (empty($data['classes'])): ?>
                    <p class="text-muted">No classes assigned yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Students</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['classes'] as $class): ?>
                                    <tr>
                                        <td><strong><?= h($class['name']) ?></strong></td>
                                        <td><span class="badge bg-primary"><?= $class['student_count'] ?></span></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>public/index.php?page=teacher/class_students&class_id=<?= $class['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body text-center">
                <h5 class="card-title"><i class="bi bi-people"></i> Total Students</h5>
                <h1 class="display-4 text-primary"><?= $data['total_students'] ?></h1>
                <p class="text-muted">Across all your classes</p>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> 
        Click on a class to view students and update their Quranic progress.
    </div>
</div>
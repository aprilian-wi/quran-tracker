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
                <h5 class="card-title"><i class="bi bi-journal-text"></i> Kelas Saya</h5>
                <?php if (empty($data['classes'])): ?>
                    <p class="text-muted">Belum ada kelas yang ditugaskan.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kelas</th>
                                    <th>Siswa</th>
                                    <th>Aksi</th>
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
                                                Lihat
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
                <h5 class="card-title"><i class="bi bi-people"></i> Total Siswa</h5>
                <h1 class="display-4 text-primary"><?= $data['total_students'] ?></h1>
                <p class="text-muted">Di semua kelas Anda</p>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> 
        Klik pada kelas untuk melihat siswa dan memperbarui kemajuan Al-Qur'an mereka.
    </div>
</div>

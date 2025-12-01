<?php
// src/Views/teacher/update_profile.php
global $pdo;
require_once __DIR__ . '/../../Helpers/functions.php';

include __DIR__ . '/../layouts/main.php';
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="d-flex align-items-center mb-4">
            <a href="<?= BASE_URL ?>public/index.php?page=admin/teachers" class="btn btn-outline-secondary btn-sm me-2">
                <i class="bi bi-arrow-left"></i> Back to Teachers
            </a>
            <h3 class="mb-0"><i class="bi bi-person-badge"></i> Teacher Profile</h3>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-person"></i> Teacher Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Full Name</label>
                            <p class="fw-bold fs-5"><?= h($teacher['name']) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Email</label>
                            <p class="fw-bold"><?= h($teacher['email']) ?></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Role</label>
                            <p><span class="badge bg-info fs-6">Teacher</span></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Account Created</label>
                            <p class="fw-bold"><?= date('d M Y H:i', strtotime($teacher['created_at'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Classes -->
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-house-door"></i> Assigned Classes</h5>
            </div>
            <div class="card-body">
                <?php
                // Fetch classes assigned to this teacher via classes_teachers table or direct teacher_id
                $stmt = $pdo->prepare("SELECT DISTINCT c.* FROM classes c LEFT JOIN classes_teachers ct ON c.id = ct.class_id WHERE c.teacher_id = ? OR ct.teacher_id = ? ORDER BY c.name");
                $stmt->execute([$teacher_id, $teacher_id]);
                $classes = $stmt->fetchAll();
                ?>
                <?php if (count($classes) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Class Name</th>
                                    <th>Students</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($classes as $class): ?>
                                    <tr>
                                        <td><strong><?= h($class['name']) ?></strong></td>
                                        <td>
                                            <?php
                                            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM children WHERE class_id = ?");
                                            $stmt->execute([$class['id']]);
                                            $student_count = $stmt->fetch()['count'];
                                            ?>
                                            <span class="badge bg-primary"><?= $student_count ?> student<?= $student_count !== 1 ? 's' : '' ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No classes assigned to this teacher yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

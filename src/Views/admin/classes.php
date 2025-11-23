<?php
// src/Views/admin/classes.php
global $pdo;
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new AdminController($pdo);
$classes = $controller->classes();

include __DIR__ . '/../layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-building"></i> Classes</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createClassModal">
        <i class="bi bi-plus"></i> New Class
    </button>
</div>

<?php if (count($classes) > 0): ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th><i class="bi bi-building"></i> Name</th>
                    <th><i class="bi bi-person"></i> Teacher(s)</th>
                    <th><i class="bi bi-people-fill"></i> Students</th>
                    <th style="width:200px;"><i class="bi bi-gear"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classes as $class): ?>
                    <tr>
                        <td><strong><?= h($class['name']) ?></strong></td>
                        <td><span class="text-muted"><?= $class['teacher_names'] ?? '<em>Unassigned</em>' ?></span></td>
                        <td>
                            <span class="badge bg-info">
                                <?= $class['student_count'] ?> Student<?= $class['student_count'] != 1 ? 's' : '' ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="<?= BASE_URL ?>public/index.php?page=teacher/class_students&class_id=<?= $class['id'] ?>" class="btn btn-outline-primary" title="View Students">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="<?= BASE_URL ?>public/index.php?page=admin/edit_class&class_id=<?= $class['id'] ?>" class="btn btn-outline-warning" title="Edit Class">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="<?= BASE_URL ?>public/index.php?page=<?= $_SESSION['role'] === 'superadmin' ? 'teacher/update_progress&class_id=' . $class['id'] : 'teacher/class_students&class_id=' . $class['id'] ?>" class="btn btn-outline-success" title="Update Progress">
                                    <i class="bi bi-graph-up"></i> Progress
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No classes found. <a href="#" data-bs-toggle="modal" data-bs-target="#createClassModal">Create one now</a>
    </div>
<?php endif; ?>

<!-- Create Class Modal -->
<div class="modal fade" id="createClassModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="<?= BASE_URL ?>public/index.php?page=create_class">
            <?= csrfInput() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign Teachers (hold Ctrl/Cmd to select multiple)</label>
                        <select name="teacher_ids[]" class="form-select" multiple>
                            <?php
                            $stmt = $pdo->query("SELECT id, name FROM users WHERE role = 'teacher' ORDER BY name");
                            while ($teacher = $stmt->fetch()): ?>
                                <option value="<?= $teacher['id'] ?>"><?= h($teacher['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Class</button>
                </div>
            </div>
        </form>
    </div>
</div>
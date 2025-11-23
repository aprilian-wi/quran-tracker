<?php
// src/Views/admin/edit_class.php
global $pdo;
require_once __DIR__ . '/../../Models/Class.php';
require_once __DIR__ . '/../../Models/User.php';
require_once __DIR__ . '/../../Models/Child.php';

$class_id = (int)($_GET['class_id'] ?? 0);
if (!$class_id) {
    setFlash('danger', 'Invalid class.');
    redirect('admin/classes');
}

$classModel = new ClassModel($pdo);
$class = $classModel->getWithTeachers($class_id);

if (!$class) {
    setFlash('danger', 'Class not found.');
    redirect('admin/classes');
}

$students = $classModel->getStudents($class_id);

// Get all teachers for the dropdown
$userModel = new User($pdo);
$allTeachers = $pdo->query("SELECT id, name FROM users WHERE role = 'teacher' ORDER BY name")->fetchAll();

// Filter out already assigned teachers
$assigned_teacher_ids = array_column($class['teachers'], 'id');
$available_teachers = array_filter($allTeachers, function($t) use ($assigned_teacher_ids) {
    return !in_array($t['id'], $assigned_teacher_ids);
});

include __DIR__ . '/../layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-pencil-square"></i> Edit Class: <?= h($class['name']) ?></h3>
    <a href="<?= BASE_URL ?>public/index.php?page=admin/classes" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row">
    <!-- Left Column: Class Info & Teachers -->
    <div class="col-lg-6">
        <!-- Edit Class Name -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-card-text"></i> Class Name</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_class">
                    <?= csrfInput() ?>
                    <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                    <input type="hidden" name="action" value="update_name">
                    
                    <div class="input-group">
                        <input type="text" name="name" class="form-control" value="<?= h($class['name']) ?>" required>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Manage Teachers -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-person-badge"></i> Assigned Teachers (<?= count($class['teachers']) ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (count($class['teachers']) > 0): ?>
                    <div class="list-group mb-3">
                        <?php foreach ($class['teachers'] as $teacher): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><?= h($teacher['name']) ?></span>
                                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_class" style="display:inline;">
                                    <?= csrfInput() ?>
                                    <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                                    <input type="hidden" name="teacher_id" value="<?= $teacher['id'] ?>">
                                    <input type="hidden" name="action" value="remove_teacher">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Remove this teacher?')">
                                        <i class="bi bi-trash"></i> Remove
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No teachers assigned</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add Teacher -->
        <?php if (count($available_teachers) > 0): ?>
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Add Teacher</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_class">
                        <?= csrfInput() ?>
                        <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                        <input type="hidden" name="action" value="add_teacher">
                        
                        <div class="input-group">
                            <select name="teacher_id" class="form-select" required>
                                <option value="">Select Teacher</option>
                                <?php foreach ($available_teachers as $teacher): ?>
                                    <option value="<?= $teacher['id'] ?>"><?= h($teacher['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-plus"></i> Add
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: Students -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-people"></i> Students (<?= count($students) ?>)</h5>
            </div>
            <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                <?php if (count($students) > 0): ?>
                    <div class="list-group">
                        <?php foreach ($students as $student): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= h($student['name']) ?></strong>
                                    <br>
                                    <small class="text-muted">Parent: <?= h($student['parent_name'] ?? 'N/A') ?></small>
                                </div>
                                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_class" style="display:inline;">
                                    <?= csrfInput() ?>
                                    <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                                    <input type="hidden" name="child_id" value="<?= $student['id'] ?>">
                                    <input type="hidden" name="action" value="remove_student">
                                    <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Remove this student?')">
                                        <i class="bi bi-trash"></i> Remove
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-5">No students assigned to this class</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add Students to Class -->
        <?php
        $childModel = new Child($pdo);
        $unassignedChildren = $childModel->getUnassignedChildren();
        ?>
        <?php if (count($unassignedChildren) > 0): ?>
            <div class="card mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-person-plus"></i> Assign Students</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_class">
                        <?= csrfInput() ?>
                        <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                        <input type="hidden" name="action" value="assign_child">
                        
                        <div class="input-group">
                            <select name="child_id" class="form-select" required>
                                <option value="">Select Student to Add</option>
                                <?php foreach ($unassignedChildren as $child): ?>
                                    <option value="<?= $child['id'] ?>">
                                        <?= h($child['name']) ?> (Parent: <?= h($child['parent_name']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-plus"></i> Add
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Class Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-trash"></i> Danger Zone</h5>
            </div>
            <div class="card-body">
                <p class="text-danger mb-3">
                    <strong>Warning:</strong> Deleting this class will remove all associations but will not delete students.
                </p>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteClassModal">
                    <i class="bi bi-trash"></i> Delete Class
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_class">
                <?= csrfInput() ?>
                <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                <input type="hidden" name="action" value="delete_class">
                
                <div class="modal-body">
                    <p>Are you sure you want to delete the class "<strong><?= h($class['name']) ?></strong>"?</p>
                    <p class="text-muted">This action cannot be undone.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="confirm" value="yes" id="confirmDelete" required>
                        <label class="form-check-label" for="confirmDelete">
                            Yes, I want to delete this class
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Permanently</button>
                </div>
            </form>
        </div>
    </div>
</div>

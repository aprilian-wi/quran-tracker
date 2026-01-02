<?php
// src/Views/teacher/class_students.php
global $pdo;
require_once __DIR__ . '/../../Controllers/TeacherController.php';
require_once __DIR__ . '/../../Models/Progress.php';
require_once __DIR__ . '/../../Helpers/functions.php';
require_once __DIR__ . '/../../Models/Class.php';

$class_id = $_GET['class_id'] ?? 0;
if (!$class_id || !is_numeric($class_id)) {
    setFlash('danger', 'Invalid class. Please specify a class_id parameter.');
    redirect('dashboard');
}

$controller = new TeacherController($pdo);
$students = $controller->classStudents($class_id);

include __DIR__ . '/../layouts/main.php';
?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-secondary"><i class="bi bi-people me-2"></i>Students in Class</h4>
            <?php 
            $classModel = new ClassModel($pdo);
            $isOwner = $classModel->isOwnedBy($class_id, $_SESSION['user_id']);
            if ($isOwner): 
            ?>
                <button class="btn btn-success px-4" data-bs-toggle="modal" data-bs-target="#assignStudentsModal">
                    <i class="bi bi-person-plus-fill me-2"></i> Assign Students
                </button>
            <?php endif; ?>
        </div>

        <?php if (empty($students)): ?>
            <div class="alert alert-light text-center py-5 border">
                <div class="mb-3"><i class="bi bi-people text-muted display-4"></i></div>
                <h5 class="text-muted">No students in this class yet</h5>
                <?php if ($isOwner): ?>
                    <p class="text-muted mb-3">Start by assigning students to this class.</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignStudentsModal">Assign Students</button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive rounded border">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="py-3 ps-3">Name</th>
                            <th class="py-3">Parent</th>
                            <th class="py-3 text-center">Update Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-dark"><?= h($student['name']) ?></td>
                                <td class="text-muted"><?= h($student['parent_name']) ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= BASE_URL ?>public/index.php?page=teacher/update_progress&child_id=<?= $student['id'] ?>"
                                           class="btn btn-outline-primary" title="Update Tahfidz">
                                            Tahfidz
                                        </a>
                                        <a href="<?= BASE_URL ?>public/index.php?page=teacher/update_progress_books&child_id=<?= $student['id'] ?>"
                                           class="btn btn-outline-warning" title="Update Tahsin">
                                            Tahsin
                                        </a>
                                        <a href="<?= BASE_URL ?>public/index.php?page=teacher/update_progress_hadiths&child_id=<?= $student['id'] ?>"
                                           class="btn btn-outline-info" title="Update Hadith">
                                            Hadith
                                        </a>
                                        <a href="<?= BASE_URL ?>public/index.php?page=teacher/update_progress_prayers&child_id=<?= $student['id'] ?>"
                                           class="btn btn-outline-success" title="Update Doa">
                                            Doa
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($isOwner): ?>
<!-- Assign Students Modal -->
<div class="modal fade" id="assignStudentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Students to Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">List of unassigned children. Click "Assign" to add a child to this class.</p>
                <?php
                // Use Child model for consistent filtering
                require_once __DIR__ . '/../../Models/Child.php';
                $childModel = new Child($pdo);
                $unassigned = $childModel->getUnassignedChildren($_SESSION['school_id']);
                
                if (empty($unassigned)): ?>
                    <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info">
                        <i class="bi bi-info-circle me-2"></i> No unassigned children available.
                    </div>
                <?php else: ?>
                    <div class="table-responsive border rounded">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Name</th>
                                    <th>Parent</th>
                                    <th class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($unassigned as $c): ?>
                                    <tr>
                                        <td class="ps-3 fw-medium"><?= h($c['name']) ?></td>
                                        <td class="text-muted"><?= h($c['parent_name']) ?></td>
                                        <td class="text-end pe-3">
                                            <form method="POST" action="<?= BASE_URL ?>public/index.php?page=assign_class" style="display:inline;">
                                                <?= csrfInput() ?>
                                                <input type="hidden" name="child_id" value="<?= $c['id'] ?>">
                                                <input type="hidden" name="class_id" value="<?= $class_id ?>">
                                                <button class="btn btn-sm btn-primary px-3">Assign</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.hover-primary:hover { background-color: #0d6efd !important; color: white !important; border-color: #0d6efd !important; }
.hover-warning:hover { background-color: #ffc107 !important; color: black !important; border-color: #ffc107 !important; }
.hover-info:hover { background-color: #0dcaf0 !important; color: white !important; border-color: #0dcaf0 !important; }
.hover-success:hover { background-color: #198754 !important; color: white !important; border-color: #198754 !important; }
</style>
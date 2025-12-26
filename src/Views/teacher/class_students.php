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

<h3><i class="bi bi-people"></i> Students in Class</h3>

<?php
// Show Assign Students button if current teacher owns this class
$classModel = new ClassModel($pdo);
$isOwner = $classModel->isOwnedBy($class_id, $_SESSION['user_id']);
if ($isOwner): ?>
    <div class="mb-3 text-end">
        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#assignStudentsModal">
            <i class="bi bi-arrow-right-square"></i> Assign Students
        </button>
    </div>
<?php endif; ?>

<?php if (empty($students)): ?>
    <div class="alert alert-info">
        No students in this class yet.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Parent</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><strong><?= h($student['name']) ?></strong></td>
                        <td><?= h($student['parent_name']) ?></td>
                        
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= BASE_URL ?>public/index.php?page=teacher/update_progress&child_id=<?= $student['id'] ?>"
                                   class="btn btn-success">
                                    Tahfidz
                                </a>
                                <a href="<?= BASE_URL ?>public/index.php?page=teacher/update_progress_books&child_id=<?= $student['id'] ?>"
                                   class="btn btn-warning">
                                    Tahsin
                                </a>
                                <a href="<?= BASE_URL ?>public/index.php?page=teacher/update_progress_prayers&child_id=<?= $student['id'] ?>"
                                   class="btn btn-info">
                                    Doa
                                </a>
                                <a href="<?= BASE_URL ?>public/index.php?page=teacher/update_progress_hadiths&child_id=<?= $student['id'] ?>"
                                   class="btn btn-danger">
                                    Hadith
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php if ($isOwner): ?>
<!-- Assign Students Modal -->
<div class="modal fade" id="assignStudentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Students to "<?= h($classModel->getByTeacher($_SESSION['user_id']) ? 'Class' : 'Class') ?>"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>List of unassigned children. Click "Assign" to add child to this class.</p>
                <?php
                $stmt = $pdo->query("SELECT c.id, c.name, u.name as parent_name FROM children c JOIN users u ON c.parent_id = u.id WHERE c.class_id IS NULL ORDER BY c.name");
                $unassigned = $stmt->fetchAll();
                if (empty($unassigned)): ?>
                    <div class="alert alert-info">No unassigned children available.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr><th>Name</th><th>Parent</th><th></th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($unassigned as $c): ?>
                                    <tr>
                                        <td><?= h($c['name']) ?></td>
                                        <td><?= h($c['parent_name']) ?></td>
                                        <td>
                                            <form method="POST" action="<?= BASE_URL ?>public/index.php?page=assign_class" style="display:inline;">
                                                <?= csrfInput() ?>
                                                <input type="hidden" name="child_id" value="<?= $c['id'] ?>">
                                                <input type="hidden" name="class_id" value="<?= $class_id ?>">
                                                <button class="btn btn-sm btn-primary">Assign</button>
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
.progress-circle svg { width: 100%; height: auto; }
.progress-circle .bg { stroke: #e9ecef; stroke-width: 4; }
.progress-circle .progress { 
    stroke: #28a745; 
    stroke-width: 4; 
    stroke-dasharray: 113; 
    stroke-dashoffset: 113; 
}
.progress-circle .text {
    font-size: 0.6rem;
    font-weight: bold;
    color: #28a745;
}
</style>

<script>
document.querySelectorAll('.progress-circle').forEach(el => {
    const progress = el.dataset.progress;
    const offset = 113 - (113 * progress / 100);
    setTimeout(() => {
        el.querySelector('.progress').style.strokeDashoffset = offset;
    }, 200);
});
</script>
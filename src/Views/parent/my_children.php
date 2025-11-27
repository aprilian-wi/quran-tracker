<?php
// src/Views/parent/my_children.php
require_once __DIR__ . '/../../Controllers/ParentController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new ParentController($pdo);
$children = $controller->myChildren();
$parent_id = isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : null;

// Get parent name if viewing specific parent
$parent_name = '';
if ($parent_id && (hasRole('superadmin') || hasRole('teacher'))) {
    require_once __DIR__ . '/../../Models/User.php';
    $userModel = new User($pdo);
    $parent = $userModel->findById($parent_id);
    $parent_name = $parent ? $parent['name'] : 'Unknown Parent';
} elseif (hasRole('parent')) {
    $parent_name = $_SESSION['name'] ?? 'My';
}

include __DIR__ . '/../layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-people"></i> <?= h($parent_name) ?> Children</h3>
    <?php if (hasRole('superadmin')): ?>
        <a href="<?= BASE_URL ?>public/index.php?page=admin/parents" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Parents
        </a>
    <?php endif; ?>
</div>

<?php if (empty($children)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No children found.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th><i class="bi bi-person"></i> Name</th>
                    <th><i class="bi bi-calendar"></i> Date of Birth</th>
                    <th><i class="bi bi-house-door"></i> Class</th>
                    <th><i class="bi bi-graph-up"></i> Progress</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($children as $child): ?>
                    <tr>
                        <td>
                            <strong><?= h($child['name']) ?></strong>
                        </td>
                        <td>
                            <?= $child['date_of_birth'] ? date('d M Y', strtotime($child['date_of_birth'])) : '-' ?>
                        </td>
                        <td>
                            <?= h($child['class_name'] ?? 'No Class') ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="<?= BASE_URL ?>public/index.php?page=update_progress&child_id=<?= $child['id'] ?>" class="btn btn-primary" title="Tahfidz">
                                    <i class="bi bi-book"></i> Tahfidz
                                </a>
                                <a href="<?= BASE_URL ?>public/index.php?page=update_progress_books&child_id=<?= $child['id'] ?>" class="btn btn-warning" title="Tahsin">
                                    <i class="bi bi-pencil"></i> Tahsin
                                </a>
                                <a href="<?= BASE_URL ?>public/index.php?page=update_progress_prayers&child_id=<?= $child['id'] ?>" class="btn btn-success" title="Doa">
                                    <i class="bi bi-pray"></i> Doa
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

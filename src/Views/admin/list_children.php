<?php
// src/Views/admin/list_children.php
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new AdminController($pdo);

$class_id = $_GET['class_id'] ?? null;
$classes = $controller->classes();
$children = $controller->getChildren($class_id);

include __DIR__ . '/../layouts/main.php';
?>

<h3><i class="bi bi-people"></i> List Children</h3>

<form method="GET" class="mb-3">
    <input type="hidden" name="page" value="admin/list_children">
    <div class="row g-3 align-items-center">
        <div class="col-auto">
            <label for="classFilter" class="col-form-label">Filter by Class:</label>
        </div>
        <div class="col-auto">
            <select id="classFilter" name="class_id" class="form-select">
                <option value="">-- All Classes --</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= h($class['id']) ?>" <?= ($class_id == $class['id']) ? 'selected' : '' ?>>
                        <?= h($class['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="?page=admin/list_children" class="btn btn-secondary ms-2">Reset</a>
            <a href="?page=admin/export_children&class_id=<?= urlencode($class_id ?? '') ?>" class="btn btn-success ms-2">Export as CSV</a>
        </div>
    </div>
</form>

<?php if (empty($children)): ?>
    <div class="alert alert-info">No children found for the selected filter.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Class</th>
                    <th>Parent Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($children as $child): ?>
                    <tr>
                        <td><?= h($child['name']) ?></td>
                        <td><?= h($child['class_name']) ?></td>
                        <td><?= h($child['parent_name'] ?? '-') ?></td>
                        <td>
                            <a href="?page=teacher/update_progress&child_id=<?= h($child['id']) ?>" class="btn btn-sm btn-primary">
                                Update Progress
                            </a>
                            <!-- Add more actions here as needed -->
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

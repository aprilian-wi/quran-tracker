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

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-secondary"><i class="bi bi-emoji-smile me-2"></i>List Children</h4>
        </div>

        <form method="GET" class="mb-4 bg-light p-3 rounded border">
            <input type="hidden" name="page" value="admin/list_children">
            <div class="row g-3 align-items-center">
                <div class="col-md-auto">
                    <label for="classFilter" class="col-form-label fw-medium">Filter by Class:</label>
                </div>
                <div class="col-md-3">
                    <select id="classFilter" name="class_id" class="form-select">
                        <option value="">-- All Classes --</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= h($class['id']) ?>" <?= ($class_id == $class['id']) ? 'selected' : '' ?>>
                                <?= h($class['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-primary px-4">Filter</button>
                    <a href="?page=admin/list_children" class="btn btn-light border ms-2">Reset</a>
                    <a href="?page=admin/export_children&class_id=<?= urlencode($class_id ?? '') ?>" class="btn btn-outline-success ms-2">
                        <i class="bi bi-file-earmark-excel"></i> Export CSV
                    </a>
                </div>
            </div>
        </form>

        <?php if (empty($children)): ?>
             <div class="alert alert-light text-center py-5 border">
                <div class="mb-3"><i class="bi bi-emoji-frown text-muted display-4"></i></div>
                <h5 class="text-muted">No children found</h5>
                <p class="text-muted">Try adjusting the filter or add children via Parent management.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive rounded border">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="py-3 ps-3">Name</th>
                            <th class="py-3">Class</th>
                            <th class="py-3">Parent Name</th>
                            <th class="py-3 text-center">Progress Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($children as $child): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-dark"><?= h($child['name']) ?></td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary border"><?= h($child['class_name']) ?></span></td>
                                <td class="text-muted"><?= h($child['parent_name'] ?? '-') ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="?page=admin/update_progress&child_id=<?= h($child['id']) ?>" class="btn btn-outline-primary" title="Update Tahfidz">
                                            Tahfidz
                                        </a>
                                        <a href="?page=admin/update_progress_books&child_id=<?= h($child['id']) ?>" class="btn btn-outline-warning" title="Update Tahsin">
                                            Tahsin
                                        </a>
                                        <a href="?page=admin/update_progress_hadiths&child_id=<?= h($child['id']) ?>" class="btn btn-outline-info" title="Update Hadith">
                                            Hadith
                                        </a>
                                        <a href="?page=admin/update_progress_prayers&child_id=<?= h($child['id']) ?>" class="btn btn-outline-success" title="Update Doa">
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

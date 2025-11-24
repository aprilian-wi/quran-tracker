<?php
// src/Views/admin/users.php
global $pdo;
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$selectedRole = $_GET['role'] ?? '';
$controller = new AdminController($pdo);
$users = $controller->users($selectedRole);

include __DIR__ . '/../layouts/main.php';
?>

<h3><i class="bi bi-people"></i> All Users</h3>

<div class="card">
    <div class="card-body">
        <form method="GET" class="mb-3 d-flex gap-2 align-items-center">
            <input type="hidden" name="page" value="admin/users" />
            <label for="roleFilter" class="form-label mb-0">Filter by Role:</label>
            <select id="roleFilter" name="role" class="form-select" style="max-width: 200px;">
                <option value="" <?= $selectedRole === '' ? 'selected' : '' ?>>All Roles</option>
                <option value="superadmin" <?= $selectedRole === 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
                <option value="teacher" <?= $selectedRole === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                <option value="parent" <?= $selectedRole === 'parent' ? 'selected' : '' ?>>Parent</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="?page=admin/users" class="btn btn-secondary ms-2">Reset</a>
            <a href="?page=admin/export_users&role=<?= urlencode($selectedRole) ?>" class="btn btn-success ms-3">Export as Excel</a>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr data-role="<?= $user['role'] ?>">
                            <td><?= $user['id'] ?></td>
                            <td><?= h($user['name']) ?></td>
                            <td><?= h($user['email']) ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $user['role'] === 'superadmin' ? 'danger' : 
                                    ($user['role'] === 'teacher' ? 'primary' : 'success') 
                                ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <?php if ($user['role'] !== 'superadmin'): ?>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="confirmDelete(<?= $user['id'] ?>, 'user')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, type) {
    if (confirm(`Delete this ${type}? This action cannot be undone.`)) {
        window.location.href = `?page=delete_${type}&id=${id}`;
    }
}
</script>

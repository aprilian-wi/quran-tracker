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

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="GET" class="mb-4 d-flex gap-2 align-items-center flex-wrap">
            <input type="hidden" name="page" value="admin/users" />
            <label for="roleFilter" class="form-label mb-0 fw-medium">Filter by Role:</label>
            <select id="roleFilter" name="role" class="form-select" style="max-width: 200px;">
                <option value="" <?= $selectedRole === '' ? 'selected' : '' ?>>All Roles</option>
                <option value="superadmin" <?= $selectedRole === 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
                <option value="school_admin" <?= $selectedRole === 'school_admin' ? 'selected' : '' ?>>School Admin</option>
                <option value="teacher" <?= $selectedRole === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                <option value="parent" <?= $selectedRole === 'parent' ? 'selected' : '' ?>>Parent</option>
            </select>
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-filter"></i> Filter</button>
            <a href="?page=admin/users" class="btn btn-light border ms-2">Reset</a>
            <a href="?page=admin/export_users&role=<?= urlencode($selectedRole) ?>" class="btn btn-success ms-auto"><i class="bi bi-file-earmark-excel"></i> Export</a>
        </form>

        <div class="table-responsive rounded border">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="py-3 ps-3">ID</th>
                        <th class="py-3">Name</th>
                        <th class="py-3">Email</th>
                        <th class="py-3">Role</th>
                        <th class="py-3">Joined</th>
                        <th class="py-3 text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">No users found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($users as $user): ?>
                        <tr data-role="<?= $user['role'] ?>">
                            <td class="ps-3 fw-bold text-muted">#<?= $user['id'] ?></td>
                            <td class="fw-medium"><?= h($user['name']) ?></td>
                            <td class="text-muted"><?= h($user['email']) ?></td>
                            <td>
                                <span class="badge rounded-pill bg-<?= 
                                    $user['role'] === 'superadmin' ? 'danger' : 
                                    ($user['role'] === 'school_admin' ? 'warning text-dark' :
                                    ($user['role'] === 'teacher' ? 'primary' : 'success'))
                                ?> px-3 py-2">
                                    <?= ucfirst(str_replace('_', ' ', $user['role'])) ?>
                                </span>
                            </td>
                            <td class="text-muted"><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                            <td class="text-end pe-3">
                                <?php if ($user['role'] !== 'superadmin' && $user['role'] !== 'school_admin'): ?>
                                    <button class="btn btn-sm btn-light border text-danger hover-delete" 
                                            onclick="confirmDelete(<?= $user['id'] ?>, 'user')"
                                            title="Delete User">
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

<style>
.hover-delete:hover {
    background-color: #dc3545 !important;
    color: white !important;
    border-color: #dc3545 !important;
}
</style>

<script>
function confirmDelete(id, type) {
    if (confirm(`Delete this ${type}? This action cannot be undone.`)) {
        window.location.href = `?page=delete_${type}&id=${id}`;
    }
}
</script>

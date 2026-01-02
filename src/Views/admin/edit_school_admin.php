<?php
// src/Views/admin/edit_school_admin.php
$pageTitle = 'Edit School Admin';
include __DIR__ . '/../layouts/main.php';

require_once __DIR__ . '/../../Controllers/SystemAdminController.php';

$id = $_GET['id'] ?? 0;
// We need school_id to go back, so we'll fetch it from the user
$controller = new SystemAdminController($pdo);
$admin = $controller->getSchoolAdmin($id);

if (!$admin) {
    echo "<div class='alert alert-danger'>Admin not found.</div>";
    exit;
}

$schoolId = $admin['school_id'];
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Edit School Admin: <?= h($admin['name']) ?></h5>
            </div>
            <div class="card-body">
                <form action="index.php?page=admin/update_school_admin" method="POST">
                    <?= csrfInput() ?>
                    <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                    <input type="hidden" name="school_id" value="<?= $schoolId ?>">

                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required value="<?= h($admin['name']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required value="<?= h($admin['email']) ?>">
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">New Password <small class="text-muted">(Leave blank to keep current)</small></label>
                        <input type="password" name="password" class="form-control" placeholder="New Password">
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php?page=admin/edit_school&id=<?= $schoolId ?>" class="btn btn-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Admin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

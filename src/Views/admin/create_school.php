<?php
// src/Views/admin/create_school.php
$pageTitle = 'Create New School';
include __DIR__ . '/../layouts/main.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create New School (Tenant)</h5>
                    <a href="index.php?page=admin/schools" class="btn btn-sm btn-light">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="index.php?page=admin/store_school" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <h6 class="text-muted mb-3">School Details</h6>
                    <div class="mb-3">
                        <label class="form-label">School Name</label>
                        <input type="text" name="school_name" class="form-control" require placeholder="e.g. SD Islam Al-Azhar">
                    </div>

                    <hr>

                    <h6 class="text-muted mb-3">School Administrator</h6>
                    <div class="mb-3">
                        <label class="form-label">Admin Name</label>
                        <input type="text" name="admin_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin Email</label>
                        <input type="email" name="admin_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Initial Password</label>
                        <input type="password" name="admin_password" class="form-control" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Create School</button>
                        <a href="index.php?page=admin/schools" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php
// src/Views/admin/create_teacher.php
require_once __DIR__ . '/../../Helpers/functions.php';
include __DIR__ . '/../layouts/main.php';
?>

<div class="card">
    <div class="card-body">
        <h4 class="mb-3">Add Teacher</h4>
        <form method="POST" action="<?= BASE_URL ?>public/index.php?page=create_teacher">
            <?= csrfInput() ?>
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            <button class="btn btn-primary">Create Teacher</button>
            <a href="<?= BASE_URL ?>public/index.php?page=admin/users" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

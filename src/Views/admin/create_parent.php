<?php
// src/Views/admin/create_parent.php
require_once __DIR__ . '/../../Helpers/functions.php';
include __DIR__ . '/../layouts/main.php';
?>

<div class="card">
    <div class="card-body">
        <h4 class="mb-3">Add Parent</h4>
        <form method="POST" action="<?= BASE_URL ?>public/index.php?page=create_parent">
            <?= csrfInput() ?>
            <div class="mb-3">
                <label class="form-label">Parent Name</label>
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
            <button class="btn btn-success">Create Parent</button>
            <a href="<?= BASE_URL ?>public/index.php?page=admin/parents" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

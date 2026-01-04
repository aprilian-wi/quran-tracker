<?php
// src/Views/admin/edit_short_prayer.php
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new AdminController($pdo);
$id = $_GET['id'] ?? null;
if (!$id) {
    setFlash('danger', 'Invalid short prayer ID');
    redirect('admin/manage_short_prayers');
}

$prayer = $controller->getShortPrayer($id);
if (!$prayer) {
    setFlash('danger', 'Short prayer not found');
    redirect('admin/manage_short_prayers');
}

include __DIR__ . '/../layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-pencil"></i> Edit Short Prayer (Doa-doa Pendek)</h3>
    <a href="?page=admin/manage_short_prayers" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<form method="POST" action="<?= BASE_URL ?>public/index.php?page=admin/save_short_prayer" class="mt-4">
    <?= csrfInput() ?>
    <input type="hidden" name="id" value="<?= h($prayer['id']) ?>">
    <div class="mb-3">
        <label for="title" class="form-label">Title (Judul)</label>
        <input type="text" id="title" name="title" class="form-control" required value="<?= h($prayer['title']) ?>">
    </div>
    <div class="mb-3">
        <label for="arabic_text" class="form-label">Arabic Text (Teks Arab)</label>
        <textarea id="arabic_text" name="arabic_text" class="form-control" rows="4" required><?= h($prayer['arabic_text']) ?></textarea>
    </div>
    <div class="mb-3">
        <label for="translation" class="form-label">Translation (Terjemahan)</label>
        <textarea id="translation" name="translation" class="form-control" rows="4"><?= h($prayer['translation']) ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Update Short Prayer</button>
    <a href="?page=admin/manage_short_prayers" class="btn btn-secondary ms-2">Cancel</a>
</form>

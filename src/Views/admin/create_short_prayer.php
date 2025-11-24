<?php
// src/Views/admin/create_short_prayer.php
require_once __DIR__ . '/../../Helpers/functions.php';
include __DIR__ . '/../layouts/main.php';
?>

<h3><i class="bi bi-plus-circle"></i> Add New Short Prayer (Doa-doa Pendek)</h3>

<form method="POST" action="<?= BASE_URL ?>public/index.php?page=admin/save_short_prayer" class="mt-4">
    <?= csrfInput() ?>
    <div class="mb-3">
        <label for="title" class="form-label">Title (Judul)</label>
        <input type="text" id="title" name="title" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="arabic_text" class="form-label">Arabic Text (Teks Arab)</label>
        <textarea id="arabic_text" name="arabic_text" class="form-control" rows="4" required></textarea>
    </div>
    <div class="mb-3">
        <label for="translation" class="form-label">Translation (Terjemahan)</label>
        <textarea id="translation" name="translation" class="form-control" rows="4"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Add Short Prayer</button>
    <a href="?page=admin/manage_short_prayers" class="btn btn-secondary ms-2">Cancel</a>
</form>

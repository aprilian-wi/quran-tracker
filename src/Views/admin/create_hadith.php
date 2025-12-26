<?php
// src/Views/admin/create_hadith.php
require_once __DIR__ . '/../../Helpers/functions.php';

include __DIR__ . '/../layouts/main.php';
?>

<h3><i class="bi bi-plus-circle"></i> Add New Hadith</h3>

<form method="POST" action="<?= BASE_URL ?>public/index.php?page=admin/save_hadith" class="mt-4">
    <?= csrfInput() ?>
    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" class="form-control" id="title" name="title" required>
    </div>
    <div class="mb-3">
        <label for="arabic_text" class="form-label">Arabic Text</label>
        <textarea class="form-control" id="arabic_text" name="arabic_text" rows="4" required></textarea>
    </div>
    <div class="mb-3">
        <label for="translation" class="form-label">Translation</label>
        <textarea class="form-control" id="translation" name="translation" rows="4"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Add Hadith</button>
    <a href="?page=admin/manage_hadiths" class="btn btn-secondary ms-2">Cancel</a>
</form>

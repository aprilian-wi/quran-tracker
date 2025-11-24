<?php
// src/Views/admin/manage_short_prayers.php
global $pdo;
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$adminController = new AdminController($pdo);
$prayers = $adminController->getShortPrayers();

$editPrayer = null;
if (isset($_GET['edit_id'])) {
    $editPrayer = $adminController->getShortPrayer($_GET['edit_id']);
}

include __DIR__ . '/../layouts/main.php';
?>

<h3>Manage Short Prayers (Doa-doa Pendek)</h3>

<div class="row">
    <div class="col-md-6">
        <h4><?= $editPrayer ? 'Edit' : 'Add' ?> Short Prayer</h4>
        <form method="POST" action="<?= BASE_URL ?>public/index.php?page=admin/save_short_prayer">
            <?= csrfInput() ?>
            <?php if ($editPrayer): ?>
                <input type="hidden" name="id" value="<?= $editPrayer['id'] ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="title" class="form-label">Title (Judul)</label>
                <input type="text" id="title" name="title" class="form-control" required
                       value="<?= h($editPrayer['title'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="arabic_text" class="form-label">Arabic Text (Teks Arab)</label>
                <textarea id="arabic_text" name="arabic_text" class="form-control" rows="4" required><?= h($editPrayer['arabic_text'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="translation" class="form-label">Translation (Terjemahan)</label>
                <textarea id="translation" name="translation" class="form-control" rows="4"><?= h($editPrayer['translation'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><?= $editPrayer ? 'Update' : 'Add' ?></button>
            <?php if ($editPrayer): ?>
                <a href="?page=admin/manage_short_prayers" class="btn btn-secondary ms-2">Cancel</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="col-md-6">
        <h4>Existing Short Prayers</h4>
        <?php if (empty($prayers)): ?>
            <p>No short prayers found.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prayers as $prayer): ?>
                    <tr>
                        <td><?= h($prayer['id']) ?></td>
                        <td><?= h($prayer['title']) ?></td>
                        <td>
                            <a href="?page=admin/manage_short_prayers&edit_id=<?= $prayer['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="?page=admin/delete_short_prayer&id=<?= $prayer['id'] ?>" class="btn btn-sm btn-danger"
                               onclick="return confirm('Are you sure you want to delete this short prayer?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

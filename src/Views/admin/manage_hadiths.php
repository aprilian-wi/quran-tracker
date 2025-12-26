<?php
// src/Views/admin/manage_hadiths.php
require_once __DIR__ . '/../../Controllers/AdminController.php';

$controller = new AdminController($pdo);
$hadiths = $controller->getHadiths();

include __DIR__ . '/../layouts/main.php';
?>

<h3><i class="bi bi-journal-text"></i> Manage Hadiths</h3>

<div class="mb-3">
    <a href="?page=admin/create_hadith" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Hadith
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Hadiths List</h5>
    </div>
    <div class="card-body">
        <?php if (empty($hadiths)): ?>
            <p class="text-muted">No hadiths found. <a href="?page=admin/create_hadith">Create the first one</a>.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Arabic Text</th>
                            <th>Translation</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hadiths as $hadith): ?>
                            <tr>
                                <td><?= $hadith['id'] ?></td>
                                <td><?= h($hadith['title']) ?></td>
                                <td class="arabic-text" dir="rtl" style="font-size: 0.9em;"><?= h($hadith['arabic_text']) ?></td>
                                <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= h($hadith['translation']) ?>
                                </td>
                                <td>
                                    <a href="?page=admin/edit_hadith&id=<?= $hadith['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="?page=admin/delete_hadith&id=<?= $hadith['id'] ?>" class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Are you sure you want to delete this hadith?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

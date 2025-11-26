<?php
// src/Views/shared/list_short_prayers.php
global $pdo;
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$adminController = new AdminController($pdo);
$prayers = $adminController->getShortPrayers();

include __DIR__ . '/../layouts/main.php';
?>

<h3><i class="bi bi-journal-text"></i> List of Short Prayers (Doa-doa Pendek)</h3>

<div class="row row-cols-1 row-cols-md-2 g-4 mt-2">
    <?php foreach ($prayers as $prayer): ?>
    <div class="col">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title"><?= h($prayer['title']) ?></h5>
                <p class="card-text arabic-text" style="font-family: 'Amiri', serif; font-size: 1.5rem; direction: rtl; text-align: right;">
                    <?= nl2br(h($prayer['arabic_text'])) ?>
                </p>
                <p class="card-text text-muted">
                    <?= nl2br(h($prayer['translation'])) ?>
                </p>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>


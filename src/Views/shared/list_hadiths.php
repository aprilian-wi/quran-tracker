<?php
// src/Views/shared/list_hadiths.php
global $pdo;

$stmt = $pdo->prepare("SELECT * FROM hadiths WHERE school_id = ? ORDER BY id ASC");
$stmt->execute([$_SESSION['school_id'] ?? 1]);
$hadiths = $stmt->fetchAll();

include __DIR__ . '/../layouts/main.php';
?>

<h3><i class="bi bi-journal-text"></i> Hadiths</h3>

<div class="row">
    <?php foreach ($hadiths as $hadith): ?>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><?= h($hadith['title']) ?></h5>
                    <p class="card-text arabic-text mb-3" dir="rtl"><?= h($hadith['arabic_text']) ?></p>
                    <?php if ($hadith['translation']): ?>
                        <p class="card-text text-muted"><?= h($hadith['translation']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (empty($hadiths)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No hadiths available.
    </div>
<?php endif; ?>

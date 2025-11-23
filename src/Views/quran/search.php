<?php
// src/Views/quran/search.php
global $pdo;
require_once __DIR__ . '/../../Models/QuranVerse.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$query = trim($_GET['q'] ?? '');
$results = [];

if ($query) {
    $quranVerseModel = new QuranVerse($pdo);
    $results = $quranVerseModel->searchVerses($query);
}

include __DIR__ . '/../layouts/main.php';
?>

<h3><i class="bi bi-search"></i> Pencarian Ayat Al-Quran</h3>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="?page=quran/search">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Cari dalam Arabic, Latin, atau Indonesia..." value="<?= h($query) ?>" required>
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($query): ?>
    <div class="mb-3">
        <h5>Hasil pencarian untuk: "<?= h($query) ?>"</h5>
        <?php if (empty($results)): ?>
            <div class="alert alert-info">Tidak ada hasil ditemukan.</div>
        <?php else: ?>
            <p class="text-muted">Ditemukan <?= count($results) ?> hasil.</p>
        <?php endif; ?>
    </div>

    <?php foreach ($results as $result): ?>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title">
                        Surah <?= h($result['surah_name_ar']) ?> (<?= h($result['surah_name_en']) ?>) - Ayat <?= $result['verse_number'] ?>
                    </h6>
                    <a href="?page=quran/surah_detail&surah=<?= $result['surah_number'] ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> Baca Surah
                    </a>
                </div>

                <div class="arabic-text mb-2" style="font-family: 'Amiri', 'Tajawal', serif; font-size: 1.2rem; direction: rtl; text-align: right;">
                    <?= h($result['text_ar']) ?>
                </div>

                <div class="latin-text mb-2 text-muted">
                    <small><em><?= h($result['text_latin']) ?></em></small>
                </div>

                <div class="indonesian-text">
                    <?= h($result['text_id']) ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

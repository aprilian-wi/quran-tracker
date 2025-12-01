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
        <form method="GET" action="<?= BASE_URL ?>public/index.php?page=quran/search">
            <input type="hidden" name="page" value="quran/search">
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
            <?php
            // Aggregate unique surahs from results
            $surahs = [];
            foreach ($results as $r) {
                $sn = (int)$r['surah_number'];
                if (!isset($surahs[$sn])) {
                    $surahs[$sn] = [
                        'surah_number' => $sn,
                        'surah_name_ar' => $r['surah_name_ar'] ?? '',
                        'surah_name_en' => $r['surah_name_en'] ?? '',
                        'juz' => $r['juz'] ?? '',
                        'full_verses' => $r['full_verses'] ?? '',
                    ];
                }
            }
            ?>
            <p class="text-muted">Ditemukan <?= count($surahs) ?> surah yang cocok.</p>
        <?php endif; ?>
    </div>

    <?php if (!empty($surahs)): ?>
        <div class="row">
            <?php foreach ($surahs as $surah): ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <?= $surah['surah_number'] ?>. <?= h($surah['surah_name_ar']) ?>
                                <small class="text-muted">(<?= h($surah['surah_name_en']) ?>)</small>
                            </h5>
                            <p class="card-text">
                                Juz <?= $surah['juz'] ?> • <?= $surah['full_verses'] ?> ayat
                            </p>
                            <div class="mt-auto">
                                <a href="<?= BASE_URL ?>public/index.php?page=quran/surah_detail&surah=<?= $surah['surah_number'] ?>" class="btn btn-primary btn-sm">
                                    <i class="bi bi-eye"></i> Baca
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

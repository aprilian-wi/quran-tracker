<?php
// src/Views/quran/surah_list.php
global $pdo;
require_once __DIR__ . '/../../Models/Quran.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$selectedJuz = (int)($_GET['juz'] ?? 0);
$quranModel = new Quran($pdo);
$allJuz = $quranModel->getAllJuz();

if (isPwa()) {
    include __DIR__ . '/../layouts/pwa.php';
    include __DIR__ . '/surah_list_pwa.php';
    return;
}

include __DIR__ . '/../layouts/main.php';
?>

<h3><i class="bi bi-list-ul"></i> Daftar Surah Al-Quran</h3>

<!-- Juz Filter -->
<div class="mb-4">
    <div class="row">
        <div class="col-md-6">
            <form method="GET" action="?page=quran/surah_list" class="d-flex">
                <input type="hidden" name="page" value="quran/surah_list">
                <select name="juz" class="form-select me-2" onchange="this.form.submit()">
                    <option value="">Semua Juz</option>
                    <?php foreach ($allJuz as $juz): ?>
                        <option value="<?= $juz ?>" <?= $selectedJuz == $juz ? 'selected' : '' ?>>
                            Juz <?= $juz ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </form>
        </div>
        <div class="col-md-6 text-end">
            <?php if ($selectedJuz): ?>
                <a href="?page=quran/surah_list" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Hapus Filter
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row">
    <?php
    $surahs = $selectedJuz ? $quranModel->getSurahsByJuz($selectedJuz) : $quranModel->getAllSurahs();
    foreach ($surahs as $surah):
    ?>
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
                        <a href="?page=quran/surah_detail&surah=<?= $surah['surah_number'] ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-eye"></i> Baca
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (empty($surahs)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Tidak ada surah ditemukan untuk filter yang dipilih.
    </div>
<?php endif; ?>

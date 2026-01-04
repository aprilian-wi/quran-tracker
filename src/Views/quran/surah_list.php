<?php
// src/Views/quran/surah_list.php
global $pdo;
require_once __DIR__ . '/../../Models/Quran.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$selectedJuz = (int)($_GET['juz'] ?? 0);
$searchQuery = trim($_GET['search'] ?? '');

$quranModel = new Quran($pdo);
$allJuz = $quranModel->getAllJuz();

$surahs = [];

if ($searchQuery) {
    // Perform Search for Surahs
    $surahs = $quranModel->searchSurahs($searchQuery);
} else {
    // Standard List
    $surahs = $selectedJuz ? $quranModel->getSurahsByJuz($selectedJuz) : $quranModel->getAllSurahs();
}

// Separate PWA View
if (isPwa() || (isset($_GET['mode']) && $_GET['mode'] === 'pwa')) {
    include __DIR__ . '/../layouts/pwa.php';
    include __DIR__ . '/surah_list_pwa.php';
    return;
}

// Check role to decide layout
if (isLoggedIn()) {
    include __DIR__ . '/../layouts/admin.php';
} else {
    include __DIR__ . '/../layouts/main.php';
}
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-primary">
            <span class="material-icons-round text-2xl">menu_book</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Daftar Surah & Pencarian</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Jelajahi dan cari Surah</p>
        </div>
    </div>
    
    <!-- Controls: Search & Filter -->
    <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
        <!-- Search Form -->
        <form method="GET" action="" class="relative w-full sm:w-64">
            <input type="hidden" name="page" value="quran/surah_list">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <span class="material-icons-round text-lg">search</span>
            </span>
            <input type="text" name="search" value="<?= h($searchQuery) ?>" placeholder="Cari surah..." class="pl-10 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-300 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm placeholder-slate-400">
        </form>

        <!-- Filter Juz (Only show if not searching) -->
        <?php if (!$searchQuery): ?>
        <form method="GET" action="" class="flex items-center gap-2 w-full sm:w-auto">
            <input type="hidden" name="page" value="quran/surah_list">
            <select name="juz" onchange="this.form.submit()" class="block w-full sm:w-auto rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-300 focus:ring-emerald-500 focus:border-emerald-500 min-w-[140px]">
                <option value="">Semua Juz</option>
                <?php foreach ($allJuz as $juz): ?>
                    <option value="<?= $juz ?>" <?= $selectedJuz == $juz ? 'selected' : '' ?>>
                        Juz <?= $juz ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <?php endif; ?>

        <?php if ($selectedJuz || $searchQuery): ?>
            <a href="?page=quran/surah_list" class="flex items-center justify-center p-2 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors" title="Reset">
                <span class="material-icons-round text-lg">close</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($searchQuery): ?>
    <div class="mb-6">
        <p class="text-slate-600 dark:text-slate-400">Hasil pencarian surah untuk "<strong><?= h($searchQuery) ?></strong>":</p>
    </div>
<?php endif; ?>

<!-- Surah Grid (Reused for both List and Search Results) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php if (empty($surahs)): ?>
        <div class="col-span-full text-center py-12">
            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-400">
                <span class="material-icons-round text-3xl">search_off</span>
            </div>
            <h3 class="text-lg font-medium text-slate-900 dark:text-white">Tidak ada surah ditemukan</h3>
            <?php if ($searchQuery): ?>
                <p class="text-slate-500 dark:text-slate-400">Coba kata kunci lain.</p>
            <?php else: ?>
                <p class="text-slate-500 dark:text-slate-400">Coba pilih Juz yang lain.</p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <?php foreach ($surahs as $surah): ?>
            <a href="?page=quran/surah_detail&surah=<?= $surah['surah_number'] ?>" class="group bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 hover:shadow-md hover:border-emerald-500 dark:hover:border-emerald-500 transition-all duration-200 decoration-0 block relative overflow-hidden">
                 <!-- Decorative number background -->
                <span class="absolute -right-4 -bottom-6 text-9xl font-bold text-slate-50 dark:text-white/5 pointer-events-none group-hover:text-emerald-50 dark:group-hover:text-emerald-900/10 transition-colors">
                    <?= $surah['surah_number'] ?>
                </span>

                <div class="flex items-start justify-between relative z-10">
                    <div class="flex items-center gap-3">
                         <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-sm border border-emerald-100 dark:border-emerald-800 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <?= $surah['surah_number'] ?>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors font-amiri text-lg leading-none mb-1">
                                <?= h($surah['surah_name_ar']) ?>
                            </h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">
                                <?= h($surah['surah_name_en']) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-xs text-slate-400 font-medium relative z-10">
                    <span><?= $surah['full_verses'] ?> Ayat</span>
                    <span>Juz <?= $surah['juz'] ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
    /* Ensure Arabic font is loaded if not already global */
    @import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap');
    .font-amiri { font-family: 'Amiri', serif; }
</style>

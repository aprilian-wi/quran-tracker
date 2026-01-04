<?php
// src/Views/shared/list_hadiths.php
global $pdo;
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$adminController = new AdminController($pdo);
$hadiths = $adminController->getHadiths();

// PWA Logic
if (isPwa() || (isset($_GET['mode']) && $_GET['mode'] === 'pwa')) {
    include __DIR__ . '/../layouts/pwa.php';
    include __DIR__ . '/list_hadiths_pwa.php';
    return;
}

// Layout Decision
if (isLoggedIn()) {
    include __DIR__ . '/../layouts/admin.php';
} else {
    include __DIR__ . '/../layouts/main.php';
}
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-primary">
            <span class="material-icons-round text-2xl">format_quote</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Hadits Pilihan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kumpulan hadits untuk dihafal</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($hadiths)): ?>
        <div class="col-span-full">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 flex items-start gap-4">
                <span class="material-icons-round text-blue-500 text-2xl mt-1">info</span>
                <div>
                    <h3 class="font-medium text-blue-800 dark:text-blue-300 mb-1">Belum ada hadits</h3>
                    <p class="text-sm text-blue-600 dark:text-blue-400">
                        Belum ada daftar hadits yang tersedia saat ini.
                    </p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($hadiths as $hadith): ?>
        <div class="group bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow">
            <h5 class="text-lg font-bold text-slate-900 dark:text-white mb-4 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                <?= h($hadith['title']) ?>
            </h5>
            
            <div class="text-right font-amiri text-2xl leading-loose text-slate-800 dark:text-slate-200 mb-4" dir="rtl">
                <?= h($hadith['arabic_text']) ?>
            </div>
            
            <?php if ($hadith['translation']): ?>
            <div class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed border-t border-slate-100 dark:border-slate-700 pt-4">
                <?= h($hadith['translation']) ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap');
    .font-amiri { font-family: 'Amiri', serif; }
</style>

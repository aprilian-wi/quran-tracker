<?php
// src/Views/shared/list_short_prayers.php
global $pdo;
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$adminController = new AdminController($pdo);
$prayers = $adminController->getShortPrayers();

// PWA Logic
if (isPwa() || (isset($_GET['mode']) && $_GET['mode'] === 'pwa')) {
    include __DIR__ . '/../layouts/pwa.php';
    include __DIR__ . '/list_short_prayers_pwa.php';
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
            <span class="material-icons-round text-2xl">volunteer_activism</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Doa-doa Pendek</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kumpulan doa sehari-hari</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($prayers as $prayer): ?>
    <div class="group bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow">
        <h5 class="text-lg font-bold text-slate-900 dark:text-white mb-4 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
            <?= h($prayer['title']) ?>
        </h5>
        
        <div class="text-right font-amiri text-2xl leading-loose text-slate-800 dark:text-slate-200 mb-4" dir="rtl">
            <?= nl2br(h($prayer['arabic_text'])) ?>
        </div>
        
        <div class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed border-t border-slate-100 dark:border-slate-700 pt-4">
            <?= nl2br(h($prayer['translation'])) ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap');
    .font-amiri { font-family: 'Amiri', serif; }
</style>

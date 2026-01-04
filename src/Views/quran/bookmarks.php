<?php
// src/Views/quran/bookmarks.php
global $pdo;
require_once __DIR__ . '/../../Models/Bookmark.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$bookmarkModel = new Bookmark($pdo);
$user_id = $_SESSION['user_id'];
$bookmarks = $bookmarkModel->getByUser($user_id);

// PWA View
if (isPwa() || (isset($_GET['mode']) && $_GET['mode'] === 'pwa')) {
    include __DIR__ . '/../layouts/pwa.php';
    include __DIR__ . '/bookmarks_pwa.php';
    return;
}

// Logic for layout
if (isLoggedIn()) {
    include __DIR__ . '/../layouts/admin.php';
} else {
    include __DIR__ . '/../layouts/main.php';
}
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
         <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-primary">
            <span class="material-icons-round text-2xl">bookmark</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Penanda Ayat</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Daftar ayat yang Anda tandai</p>
        </div>
    </div>
</div>

<?php if (empty($bookmarks)): ?>
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 flex items-start gap-4">
        <span class="material-icons-round text-blue-500 text-2xl mt-1">info</span>
        <div>
            <h3 class="font-medium text-blue-800 dark:text-blue-300 mb-1">Belum ada bookmark</h3>
            <p class="text-sm text-blue-600 dark:text-blue-400">
                Anda belum memiliki bookmark. Kunjungi halaman surah dan klik ikon bookmark pada ayat yang ingin disimpan.
            </p>
        </div>
    </div>
<?php else: ?>
    <div class="mb-6">
        <p class="text-slate-500 dark:text-slate-400">Anda memiliki <strong class="text-slate-900 dark:text-white"><?= count($bookmarks) ?></strong> bookmark.</p>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <?php foreach ($bookmarks as $bookmark): ?>
            <div class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-md transition-shadow">
                <!-- Card Header -->
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
                        Surah <?= h($bookmark['surah_name_ar']) ?> (<?= h($bookmark['surah_name_en']) ?>) - Ayat <?= $bookmark['verse_number'] ?>
                    </h3>
                    <div class="flex items-center gap-2">
                         <a href="?page=quran/surah_detail&surah=<?= $bookmark['surah_number'] ?>&p=<?= floor(($bookmark['verse_number'] - 1) / 10) + 1 ?>#ayat-<?= $bookmark['verse_number'] ?>" class="inline-flex items-center px-3 py-1.5 border border-primary text-xs font-medium rounded-lg text-primary bg-white dark:bg-card-dark hover:bg-primary hover:text-white transition-colors">
                            <span class="material-icons-round text-sm mr-1">visibility</span>
                            Baca
                        </a>
                        <button class="inline-flex items-center px-3 py-1.5 border border-red-500 text-xs font-medium rounded-lg text-red-500 bg-white dark:bg-card-dark hover:bg-red-500 hover:text-white transition-colors remove-bookmark" data-surah="<?= $bookmark['surah_number'] ?>" data-verse="<?= $bookmark['verse_number'] ?>">
                            <span class="material-icons-round text-sm mr-1">delete</span>
                            Hapus
                        </button>
                    </div>
                </div>

                <div class="p-6">
                     <?php if ($bookmark['note']): ?>
                        <div class="mb-4 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg text-sm text-amber-800 dark:text-amber-300 border border-amber-100 dark:border-amber-800/30">
                            <strong>Catatan:</strong> <?= h($bookmark['note']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-6 text-right font-amiri text-2xl leading-[2.2] text-slate-800 dark:text-slate-100" dir="rtl">
                        <?= h($bookmark['text_ar']) ?>
                    </div>

                    <div class="space-y-2 text-sm">
                         <p class="text-emerald-600 dark:text-emerald-400 font-medium italic">
                            <?= h($bookmark['text_latin']) ?>
                        </p>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            <?= h($bookmark['text_id']) ?>
                        </p>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                        <small class="text-slate-400 text-xs">Ditambahkan: <?= date('d M Y H:i', strtotime($bookmark['created_at'])) ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap');
    .font-amiri { font-family: 'Amiri', serif; }
</style>

<script>
// Remove bookmark functionality with generic confirm replacement if needed, 
// using native confirm for now as per original.
document.querySelectorAll('.remove-bookmark').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Yakin ingin menghapus bookmark ini?')) return;

        const surah = this.dataset.surah;
        const verse = this.dataset.verse;
        const card = this.closest('.bg-white'); // Matches the card container classes

        fetch('?page=toggle_bookmark', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            },
            body: `surah=${surah}&verse=${verse}&action=remove`
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'removed') {
                // Animate removal
                card.style.transition = 'opacity 0.3s, transform 0.3s';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    card.remove();
                    // Optional: reload if list empty or just decrement count logic, 
                    // simplest to reload to update count text accurately
                    location.reload(); 
                }, 300);
            }
        })
        .catch(err => console.error('Error removing bookmark:', err));
    });
});
</script>

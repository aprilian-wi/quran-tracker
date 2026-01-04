<?php
// src/Views/quran/surah_detail.php
global $pdo;
require_once __DIR__ . '/../../Models/Quran.php';
require_once __DIR__ . '/../../Models/QuranVerse.php';
require_once __DIR__ . '/../../Models/Bookmark.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$surah = (int)($_GET['surah'] ?? 1);
if ($surah < 1 || $surah > 114) {
    setFlash('danger', 'Surah tidak valid.');
    redirect('quran/surah_list');
}

$quranModel = new Quran($pdo);
$surahInfo = $quranModel->getSurah($surah);
if (!$surahInfo) {
    setFlash('danger', 'Surah tidak ditemukan.');
    redirect('quran/surah_list');
}

$quranVerseModel = new QuranVerse($pdo);
// Pagination Logic
$page = (int)($_GET['p'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;
$totalVerses = $surahInfo['full_verses'];
$totalPages = ceil($totalVerses / $limit);

// Ensure page is within valid range
if ($page < 1) $page = 1;
if ($page > $totalPages) $page = $totalPages;

// Recalculate offset in case page was adjusted
$offset = ($page - 1) * $limit;

$verses = $quranVerseModel->getVersesBySurahPaginated($surah, $limit, $offset);

$bookmarkModel = new Bookmark($pdo);
$user_id = $_SESSION['user_id'];

// Get previous and next surah info
$prevSurahInfo = null;
$nextSurahInfo = null;
if ($surah > 1) {
    $prevSurahInfo = $quranModel->getSurah($surah - 1);
}
if ($surah < 114) {
    $nextSurahInfo = $quranModel->getSurah($surah + 1);
}

// PWA Logic
if (isPwa() || (isset($_GET['mode']) && $_GET['mode'] === 'pwa')) {
    include __DIR__ . '/../layouts/pwa.php';
    include __DIR__ . '/surah_detail_pwa.php';
    return;
}

// Use Admin Layout for Teachers/Parents, Main for Public/Others
if (isLoggedIn()) {
    include __DIR__ . '/../layouts/admin.php';
} else {
    include __DIR__ . '/../layouts/main.php';
}
?>

<!-- Header & Navigation -->
<div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-3 text-center md:text-left">
        <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-primary hidden md:block">
            <span class="material-icons-round text-2xl">menu_book</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white font-amiri leading-normal">
                Surah <?= h($surahInfo['surah_name_ar']) ?>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                <?= h($surahInfo['surah_name_en']) ?> • Juz <?= $surahInfo['juz'] ?> • <?= $surahInfo['full_verses'] ?> Ayat
            </p>
        </div>
    </div>
    
    <div class="flex items-center gap-2">
        <?php if ($surah > 1): ?>
             <a href="?page=quran/surah_detail&surah=<?= $surah - 1 ?>" class="flex items-center gap-1 px-3 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-sm font-medium decoration-0">
                <span class="material-icons-round text-lg">chevron_left</span>
                <span class="hidden sm:inline">Sebelumnya</span>
            </a>
        <?php endif; ?>

        <a href="?page=quran/surah_list" class="flex items-center justify-center p-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" title="Daftar Surah">
            <span class="material-icons-round text-xl">list</span>
        </a>

        <?php if ($surah < 114): ?>
            <a href="?page=quran/surah_detail&surah=<?= $surah + 1 ?>" class="flex items-center gap-1 px-3 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-sm font-medium decoration-0">
                <span class="hidden sm:inline">Selanjutnya</span>
                <span class="material-icons-round text-lg">chevron_right</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Main Content -->
<div class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    
    <!-- Info Bar -->
    <div class="px-6 py-3 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
        <span>Halaman <?= $page ?> dari <?= $totalPages ?></span>
        <span>Ayat <?= $offset + 1 ?> - <?= min($offset + $limit, $totalVerses) ?></span>
    </div>

    <!-- Bismillah -->
    <?php if (!in_array($surah, [1, 9])): ?>
        <div class="py-8 text-center border-b border-slate-100 dark:border-slate-800/50">
            <div class="font-amiri text-3xl text-slate-800 dark:text-slate-200 leading-loose" dir="rtl">
                بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ
            </div>
        </div>
    <?php endif; ?>

    <!-- Verses List -->
    <div class="divide-y divide-slate-100 dark:divide-slate-800">
        <?php foreach ($verses as $index => $verse): ?>
            <div class="p-6 transition-colors hover:bg-slate-50/50 dark:hover:bg-slate-800/30 verse-item" id="ayat-<?= $verse['verse_number'] ?>" data-surah="<?= $surah ?>" data-verse="<?= $verse['verse_number'] ?>">
                
                <!-- Verse Header (Number & Actions) -->
                <div class="flex items-center justify-between mb-6">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-bold border border-slate-200 dark:border-slate-700">
                        <?= $verse['verse_number'] ?>
                    </span>
                    
                    <div class="flex items-center gap-2">
                        <button class="bookmark-btn w-8 h-8 flex items-center justify-center rounded-full transition-colors focus:outline-none <?= $bookmarkModel->isBookmarked($user_id, $surah, $verse['verse_number']) ? 'text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/30' : 'text-slate-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/30' ?>" data-action="toggle" title="Tandai">
                             <span class="material-icons-round text-xl"><?= $bookmarkModel->isBookmarked($user_id, $surah, $verse['verse_number']) ? 'bookmark' : 'bookmark_border' ?></span>
                        </button>
                    </div>
                </div>

                <!-- Arabic Text -->
                <div class="mb-6 text-right font-amiri text-3xl leading-[2.5] text-slate-800 dark:text-slate-100" dir="rtl">
                    <?= h($verse['text_ar']) ?>
                </div>

                <!-- Translation -->
                <div class="space-y-2">
                    <p class="text-sm text-emerald-600 dark:text-emerald-400 font-medium italic">
                        <?= h($verse['text_latin']) ?>
                    </p>
                    <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                        <?= h($verse['text_id']) ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Footer Pagination -->
    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex justify-center">
        <nav class="flex items-center gap-2" aria-label="Pagination">
            <a href="?page=quran/surah_detail&surah=<?= $surah ?>&p=<?= $page - 1 ?>" class="flex items-center gap-1 px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors <?= $page <= 1 ? 'pointer-events-none opacity-50' : '' ?>">
                <span class="material-icons-round text-sm">arrow_back</span>
                Mundur
            </a>
            
            <a href="?page=quran/surah_detail&surah=<?= $surah ?>&p=<?= $page + 1 ?>" class="flex items-center gap-1 px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors <?= $page >= $totalPages ? 'pointer-events-none opacity-50' : '' ?>">
                Maju
                <span class="material-icons-round text-sm">arrow_forward</span>
            </a>
        </nav>
    </div>
</div>

<!-- Audio Player (Hidden) -->
<audio id="quran-audio" preload="none"></audio>

<!-- Back to Top Button -->
<button id="backToTopBtn" class="fixed bottom-8 right-8 w-12 h-12 bg-emerald-600 text-white rounded-full shadow-lg flex items-center justify-center hover:bg-emerald-700 transition-all transform translate-y-20 opacity-0 z-40 focus:outline-none">
    <span class="material-icons-round text-2xl">arrow_upward</span>
</button>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap');
    .font-amiri { font-family: 'Amiri', serif; }
</style>

<script>
// Back to Top Button Logic
const backToTopBtn = document.getElementById('backToTopBtn');

window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
        backToTopBtn.classList.remove('translate-y-20', 'opacity-0');
    } else {
        backToTopBtn.classList.add('translate-y-20', 'opacity-0');
    }
});

backToTopBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

// Bookmark functionality
document.querySelectorAll('.bookmark-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const verseItem = this.closest('.verse-item');
        const surah = verseItem.dataset.surah;
        const verse = verseItem.dataset.verse;
        const iconSpan = this.querySelector('span'); // Material Icon inside span
        
        // Check current state based on icon text
        const isBookmarked = iconSpan.textContent.trim() === 'bookmark';

        fetch('?page=toggle_bookmark', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            },
            body: `surah=${surah}&verse=${verse}&action=${isBookmarked ? 'remove' : 'add'}`
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'added') {
                iconSpan.textContent = 'bookmark';
                this.classList.remove('text-slate-400', 'hover:text-amber-500');
                this.classList.add('text-amber-500');
            } else if (data.status === 'removed') {
                iconSpan.textContent = 'bookmark_border';
                this.classList.remove('text-amber-500');
                this.classList.add('text-slate-400', 'hover:text-amber-500');
            }
        })
        .catch(err => console.error('Error toggling bookmark:', err));
    });
});

// Audio playback
let currentAudio = null;
const audioPlayer = document.getElementById('quran-audio');

document.querySelectorAll('.play-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const audioUrl = this.dataset.audio;
        const iconSpan = this.querySelector('span');

        if (currentAudio === audioUrl && !audioPlayer.paused) {
            audioPlayer.pause();
            iconSpan.textContent = 'play_circle';
        } else {
            // Reset all other buttons
            document.querySelectorAll('.play-btn span').forEach(s => s.textContent = 'play_circle');
            
            audioPlayer.src = audioUrl;
            audioPlayer.play();
            iconSpan.textContent = 'pause_circle';
            currentAudio = audioUrl;
        }
    });
});

audioPlayer.addEventListener('ended', function() {
    document.querySelectorAll('.play-btn span').forEach(s => s.textContent = 'play_circle');
    currentAudio = null;
});
</script>

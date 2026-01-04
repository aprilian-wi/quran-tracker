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

if (isPwa() || (isset($_GET['mode']) && $_GET['mode'] === 'pwa')) {
    include __DIR__ . '/../layouts/pwa.php';
    include __DIR__ . '/surah_detail_pwa.php';
    return;
}

include __DIR__ . '/../layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>
        <i class="bi bi-book"></i>
        Surah <?= h($surahInfo['surah_name_ar']) ?> (<?= h($surahInfo['surah_name_en']) ?>)
    </h3>
    <a href="?page=quran/surah_list" class="btn btn-secondary" title="Kembali ke Daftar Surah">
        <i class="bi bi-book-half"></i>
    </a>
</div>

<!-- Navigation Buttons -->
<div class="mb-3" style="background-color: white; padding: 12px 0; border-radius: 4px;">
    <div class="d-flex gap-2 justify-content-center">

<?php if ($surah < 114): ?>
            <a href="?page=quran/surah_detail&surah=<?= $surah + 1 ?>" class="btn btn-outline-primary">
                <i class="bi bi-chevron-left"></i> <?= h($nextSurahInfo['surah_name_en']) ?> 
            </a>
        <?php else: ?>
            <button class="btn btn-outline-primary" disabled>
                - <i class="bi bi-chevron-right"></i>
            </button>
        <?php endif; ?>

        <span class="align-self-center text-muted">
            <small><?= $surah ?> / 114</small>
        </span>

        <?php if ($surah > 1): ?>
            <a href="?page=quran/surah_detail&surah=<?= $surah - 1 ?>" class="btn btn-outline-primary">
                <?= h($prevSurahInfo['surah_name_en']) ?> <i class="bi bi-chevron-right"></i>
            </a>
        <?php else: ?>
            <button class="btn btn-outline-primary" disabled>
                <i class="bi bi-chevron-right"></i> -
            </button>
        <?php endif; ?>

    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Juz <?= $surahInfo['juz'] ?> • Total <?= $surahInfo['full_verses'] ?> Ayat</strong>
        <span class="badge bg-info text-dark">Ayat <?= $offset + 1 ?> - <?= min($offset + $limit, $totalVerses) ?></span>
    </div>
    <?php if (!in_array($surah, [1, 9])): ?>
        <div class="card-body">
            <div class="arabic-text basmala mb-3" style="font-family: 'Amiri', 'Tajawal', serif; font-size: 1.3rem; direction: rtl; text-align: center;">
                بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ
            </div>
        </div>
    <?php endif; ?>

    <div class="card-body">
        <?php foreach ($verses as $verse): ?>
            <div class="verse-item mb-4 p-3 border rounded" id="ayat-<?= $verse['verse_number'] ?>" data-surah="<?= $surah ?>" data-verse="<?= $verse['verse_number'] ?>">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-primary">Ayat <?= $verse['verse_number'] ?></span>
                    <div>
                        <button class="btn btn-sm btn-outline-warning bookmark-btn" data-action="toggle">
                            <i class="bi bi-bookmark<?= $bookmarkModel->isBookmarked($user_id, $surah, $verse['verse_number']) ? '-fill' : '' ?>"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-success play-btn" data-audio="<?= h($verse['audio_url']) ?>">
                            <i class="bi bi-play-fill"></i>
                        </button>
                    </div>
                </div>


                <div class="arabic-text mb-2" style="font-family: 'Amiri', 'Tajawal', serif; font-size: 1.5rem; direction: rtl; text-align: right;">
                    <?= h($verse['text_ar']) ?>
                </div>

                <div class="latin-text mb-2 text-muted">
                    <small><em><?= h($verse['text_latin']) ?></em></small>
                </div>

                <div class="indonesian-text">
                    <?= h($verse['text_id']) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="card-footer">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?= $page >= $totalPages? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=quran/surah_detail&surah=<?= $surah ?>&p=<?= $page + 1 ?>">Maju</a>
                </li>
                <li class="page-item disabled">
                    <span class="page-link">Halaman <?= $page ?> dari <?= $totalPages ?></span>
                </li>
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=quran/surah_detail&surah=<?= $surah ?>&p=<?= $page - 1 ?>">Mundur</a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<!-- Audio Player (Hidden) -->
<audio id="quran-audio" preload="none"></audio>

<!-- Back to Top Button -->
<button id="backToTopBtn" class="btn btn-primary rounded-circle shadow" style="display: none; position: fixed; bottom: 30px; right: 30px; z-index: 1000; width: 50px; height: 50px;">
    <i class="bi bi-arrow-up"></i>
</button>

<script>
// Back to Top Button Logic
const backToTopBtn = document.getElementById('backToTopBtn');

window.onscroll = function() {
    if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
        backToTopBtn.style.display = 'block';
    } else {
        backToTopBtn.style.display = 'none';
    }
};

backToTopBtn.addEventListener('click', function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

// Bookmark functionality
document.querySelectorAll('.bookmark-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const verseItem = this.closest('.verse-item');
        const surah = verseItem.dataset.surah;
        const verse = verseItem.dataset.verse;
        const icon = this.querySelector('i');
        const isBookmarked = icon.classList.contains('bi-bookmark-fill');

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
                icon.classList.remove('bi-bookmark');
                icon.classList.add('bi-bookmark-fill');
            } else if (data.status === 'removed') {
                icon.classList.remove('bi-bookmark-fill');
                icon.classList.add('bi-bookmark');
            }
        });
    });
});

// Audio playback
let currentAudio = null;
document.querySelectorAll('.play-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const audioUrl = this.dataset.audio;
        const audio = document.getElementById('quran-audio');

        if (currentAudio === audioUrl && !audio.paused) {
            audio.pause();
            this.innerHTML = '<i class="bi bi-play-fill"></i>';
        } else {
            audio.src = audioUrl;
            audio.play();
            this.innerHTML = '<i class="bi bi-pause-fill"></i>';
            currentAudio = audioUrl;
        }
    });
});

document.getElementById('quran-audio').addEventListener('ended', function() {
    document.querySelectorAll('.play-btn').forEach(btn => {
        btn.innerHTML = '<i class="bi bi-play-fill"></i>';
    });
    currentAudio = null;
});
</script>

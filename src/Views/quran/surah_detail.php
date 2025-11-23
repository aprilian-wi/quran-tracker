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
$verses = $quranVerseModel->getVersesBySurah($surah);

$bookmarkModel = new Bookmark($pdo);
$user_id = $_SESSION['user_id'];

include __DIR__ . '/../layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>
        <i class="bi bi-book"></i>
        Surah <?= h($surahInfo['surah_name_ar']) ?> (<?= h($surahInfo['surah_name_en']) ?>)
    </h3>
    <a href="?page=quran/surah_list" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-header">
        <strong>Juz <?= $surahInfo['juz'] ?> • <?= $surahInfo['full_verses'] ?> Ayat</strong>
    </div>
    <div class="card-body">
        <?php foreach ($verses as $verse): ?>
            <div class="verse-item mb-4 p-3 border rounded" data-surah="<?= $surah ?>" data-verse="<?= $verse['verse_number'] ?>">
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
</div>

<!-- Audio Player (Hidden) -->
<audio id="quran-audio" preload="none"></audio>

<script>
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

<?php
// src/Views/quran/bookmarks.php
global $pdo;
require_once __DIR__ . '/../../Models/Bookmark.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$bookmarkModel = new Bookmark($pdo);
$user_id = $_SESSION['user_id'];
$bookmarks = $bookmarkModel->getByUser($user_id);

include __DIR__ . '/../layouts/main.php';
?>

<h3><i class="bi bi-bookmark-fill"></i> Bookmark Ayat</h3>

<?php if (empty($bookmarks)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Anda belum memiliki bookmark. Kunjungi halaman surah dan klik ikon bookmark pada ayat yang ingin disimpan.
    </div>
<?php else: ?>
    <div class="mb-3">
        <p class="text-muted">Anda memiliki <?= count($bookmarks) ?> bookmark.</p>
    </div>

    <?php foreach ($bookmarks as $bookmark): ?>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title">
                        Surah <?= h($bookmark['surah_name_ar']) ?> (<?= h($bookmark['surah_name_en']) ?>) - Ayat <?= $bookmark['verse_number'] ?>
                    </h6>
                    <div>
                        <a href="?page=quran/surah_detail&surah=<?= $bookmark['surah_number'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> Baca Surah
                        </a>
                        <button class="btn btn-sm btn-outline-danger remove-bookmark" data-surah="<?= $bookmark['surah_number'] ?>" data-verse="<?= $bookmark['verse_number'] ?>">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </div>
                </div>

                <?php if ($bookmark['note']): ?>
                    <div class="mb-2">
                        <strong>Catatan:</strong> <?= h($bookmark['note']) ?>
                    </div>
                <?php endif; ?>

                <div class="arabic-text mb-2" style="font-family: 'Amiri', 'Tajawal', serif; font-size: 1.2rem; direction: rtl; text-align: right;">
                    <?= h($bookmark['text_ar']) ?>
                </div>

                <div class="latin-text mb-2 text-muted">
                    <small><em><?= h($bookmark['text_latin']) ?></em></small>
                </div>

                <div class="indonesian-text">
                    <?= h($bookmark['text_id']) ?>
                </div>

                <div class="mt-2 text-muted">
                    <small>Ditambahkan: <?= date('d M Y H:i', strtotime($bookmark['created_at'])) ?></small>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
// Remove bookmark functionality
document.querySelectorAll('.remove-bookmark').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Yakin ingin menghapus bookmark ini?')) return;

        const surah = this.dataset.surah;
        const verse = this.dataset.verse;
        const card = this.closest('.card');

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
                card.remove();
                location.reload(); // Refresh to update count
            }
        });
    });
});
</script>

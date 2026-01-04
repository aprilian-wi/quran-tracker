<?php
// src/Views/quran/bookmarks_pwa.php
// Variables available: $bookmarks
?>

<section class="mb-6 pt-2">
    <div class="flex items-center space-x-3 px-1">
        <a href="<?= BASE_URL ?>public/index.php?page=quran/surah_list&mode=pwa" class="text-text-main-light dark:text-white hover:text-primary transition-colors">
            <span class="material-icons-round text-2xl">arrow_back</span>
        </a>
        <div>
            <h2 class="text-xl font-display font-bold text-text-main-light dark:text-white">Bookmark Ayat</h2>
            <p class="text-xs text-text-sub-light dark:text-text-sub-dark">
                <?= count($bookmarks) ?> Ayat disimpan
            </p>
        </div>
    </div>
</section>

<!-- Bookmarks List -->
<section class="space-y-4 pb-20">
    <?php if (empty($bookmarks)): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center space-y-4">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center text-gray-400">
                <span class="material-icons-round text-3xl">bookmark_border</span>
            </div>
            <div class="max-w-xs">
                <h3 class="font-bold text-gray-800 dark:text-white text-lg">Belum ada bookmark</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Simpan ayat favorit Anda saat membaca untuk mengaksesnya dengan cepat di sini.
                </p>
            </div>
            <a href="?page=quran/surah_list&mode=pwa" class="px-6 py-2 bg-primary text-white rounded-xl text-sm font-medium shadow-sm hover:bg-primary-dark transition-colors">
                Mulai Membaca
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($bookmarks as $bookmark): ?>
            <div class="bookmark-item bg-surface-light dark:bg-surface-dark rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-800 relative group" data-surah="<?= $bookmark['surah_number'] ?>" data-verse="<?= $bookmark['verse_number'] ?>">
                
                <!-- Remove Button (Top Right) -->
                <button class="remove-bookmark absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Hapus Bookmark">
                    <span class="material-icons-round text-lg">delete_outline</span>
                </button>

                <div class="pr-8"> <!-- Padding right for remove button -->
                    <!-- Header: Surah & Verse -->
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="bg-primary/10 dark:bg-primary/20 text-primary dark:text-green-400 text-xs font-bold px-2.5 py-1 rounded-lg">
                            QS. <?= h($bookmark['surah_name_en']) ?> : <?= $bookmark['verse_number'] ?>
                        </span>
                        <span class="text-xs text-gray-400">•</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 font-arabic">
                            <?= h($bookmark['surah_name_ar']) ?>
                        </span>
                    </div>

                    <!-- Arabic Text -->
                     <p class="font-arabic text-xl text-gray-800 dark:text-white leading-loose arabic-text text-right mb-3" dir="rtl">
                        <?= h($bookmark['text_ar']) ?>
                    </p>

                    <!-- Latin -->
                    <p class="text-xs font-medium text-primary dark:text-green-400 italic mb-1">
                        <?= h($bookmark['text_latin']) ?>
                    </p>
                    
                    <!-- Translation -->
                    <p class="text-xs text-text-sub-light dark:text-text-sub-dark leading-relaxed line-clamp-2">
                        <?= h($bookmark['text_id']) ?>
                    </p>

                    <!-- Note if exists -->
                    <?php if ($bookmark['note']): ?>
                        <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl text-xs text-gray-600 dark:text-gray-300 border border-gray-100 dark:border-gray-700">
                             <span class="font-bold block mb-0.5">Catatan:</span>
                             <?= h($bookmark['note']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Action Link -->
                    <div class="mt-4 pt-3 border-t border-gray-50 dark:border-gray-800 flex justify-end">
                        <a href="?page=quran/surah_detail&surah=<?= $bookmark['surah_number'] ?>&p=<?= ceil($bookmark['verse_number']/10) ?>&mode=pwa#ayat-<?= $bookmark['verse_number'] ?>" class="inline-flex items-center space-x-1 text-sm font-medium text-primary dark:text-green-400 hover:text-primary-dark transition-colors">
                            <span>Lanjutkan Baca</span>
                            <span class="material-icons-round text-base">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<!-- CSRF Token for JS -->
<input type="hidden" id="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

<script>
// Remove Bookmark Logic
document.querySelectorAll('.remove-bookmark').forEach(btn => {
    btn.addEventListener('click', function() {
        // Confirmation could be a nice custom modal, for now standard alert
        if (!confirm('Hapus bookmark ini?')) return;

        const card = this.closest('.bookmark-item');
        const surah = card.dataset.surah;
        const verse = card.dataset.verse;

        const params = new URLSearchParams();
        params.append('surah', surah);
        params.append('verse', verse);
        params.append('action', 'remove');
        
        fetch('index.php?page=toggle_bookmark', {
            method: 'POST',
            credentials: 'include', // Send cookies
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: params
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'removed') {
                // Animate removal
                card.style.transition = 'opacity 0.3s, transform 0.3s';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    card.remove();
                    // Optional: Update count or show empty state if list is empty
                    if (document.querySelectorAll('.bookmark-item').length === 0) {
                        location.reload(); 
                    }
                }, 300);
            }
        })
        .catch(err => console.error(err));
    });
});
</script>

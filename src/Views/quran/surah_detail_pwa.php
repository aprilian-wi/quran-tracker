<?php
// src/Views/quran/surah_detail_pwa.php
// Variables available: $surahInfo, $verses, $prevSurahInfo, $nextSurahInfo, $page, $totalPages, $surah
?>

<section class="flex items-center space-x-4 mb-6 pt-2">
    <a href="<?= BASE_URL ?>public/index.php?page=quran/surah_list&mode=pwa" class="w-10 h-10 bg-white dark:bg-surface-dark rounded-full shadow-soft flex items-center justify-center text-text-sub-light dark:text-text-sub-dark hover:text-primary transition-colors">
        <span class="material-icons-round">arrow_back</span>
    </a>
    <div class="flex-1">
        <h2 class="text-xl font-display font-bold text-text-main-light dark:text-white"><?= h($surahInfo['surah_name_en']) ?></h2>
        <p class="text-sm text-text-sub-light dark:text-text-sub-dark">Ayat <?= $offset + 1 ?>-<?= min($offset + $limit, $totalVerses) ?> (Total <?= $surahInfo['full_verses'] ?>)</p>
    </div>
    <div class="w-12 h-12 flex items-center justify-center font-arabic text-3xl text-primary dark:text-green-400">
        <?= h($surahInfo['surah_name_ar']) ?>
    </div>
</section>

<!-- Surah Navigation (Prev/Next) -->
<section class="grid grid-cols-2 gap-4 mb-6">
    <!-- Next Surah (Logic flipped because Quran order? No, usually Next is Surah + 1) -->
    <!-- But User HTML shows "Next Surah" on left with Al-Baqarah (Surah 2). So "Next" means content-wise next. -->
    <?php if ($nextSurahInfo): ?>
        <a href="?page=quran/surah_detail&surah=<?= $nextSurahInfo['surah_number'] ?>&mode=pwa" class="flex items-center justify-start p-3 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-soft border border-transparent hover:shadow-card hover:border-primary/10 group transition-all cursor-pointer">
            <div class="w-8 h-8 rounded-full bg-primary/10 dark:bg-primary/20 flex items-center justify-center text-primary dark:text-green-400 group-hover:bg-primary group-hover:text-white transition-colors shrink-0">
                <span class="material-icons-round text-lg">arrow_back</span>
            </div>
            <div class="ml-3 flex flex-col items-start overflow-hidden">
                <span class="text-[10px] font-bold uppercase tracking-wider text-primary dark:text-green-400">Next Surah</span>
                <span class="text-xs font-semibold text-text-main-light dark:text-white truncate w-full text-left"><?= h($nextSurahInfo['surah_name_en']) ?></span>
            </div>
        </a>
    <?php else: ?>
        <button class="flex items-center justify-start p-3 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-soft border border-transparent disabled:opacity-40 disabled:cursor-not-allowed group transition-all" disabled>
            <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-text-sub-light dark:text-text-sub-dark shrink-0">
                <span class="material-icons-round text-lg">arrow_back</span>
            </div>
            <div class="ml-3 flex flex-col items-start overflow-hidden">
                <span class="text-[10px] font-bold uppercase tracking-wider text-text-sub-light dark:text-text-sub-dark">End</span>
                <span class="text-xs font-semibold text-text-main-light dark:text-white truncate w-full text-left">-</span>
            </div>
        </button>
    <?php endif; ?>

    <!-- Prev Surah -->
    <?php if ($prevSurahInfo): ?>
        <a href="?page=quran/surah_detail&surah=<?= $prevSurahInfo['surah_number'] ?>&mode=pwa" class="flex items-center justify-end p-3 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-soft border border-transparent hover:shadow-card hover:border-primary/10 group transition-all cursor-pointer">
            <div class="mr-3 flex flex-col items-end overflow-hidden">
                <span class="text-[10px] font-bold uppercase tracking-wider text-primary dark:text-green-400">Prev Surah</span>
                <span class="text-xs font-semibold text-text-main-light dark:text-white truncate w-full text-right"><?= h($prevSurahInfo['surah_name_en']) ?></span>
            </div>
            <div class="w-8 h-8 rounded-full bg-primary/10 dark:bg-primary/20 flex items-center justify-center text-primary dark:text-green-400 group-hover:bg-primary group-hover:text-white transition-colors shrink-0">
                 <span class="material-icons-round text-lg">arrow_forward</span>
            </div>
        </a>
    <?php else: ?>
        <button class="flex items-center justify-end p-3 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-soft border border-transparent disabled:opacity-40 disabled:cursor-not-allowed group transition-all" disabled>
            <div class="mr-3 flex flex-col items-end overflow-hidden">
                <span class="text-[10px] font-bold uppercase tracking-wider text-text-sub-light dark:text-text-sub-dark">Start</span>
                <span class="text-xs font-semibold text-text-main-light dark:text-white truncate w-full text-right">-</span>
            </div>
            <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-text-sub-light dark:text-text-sub-dark shrink-0">
                <span class="material-icons-round text-lg">arrow_forward</span>
            </div>
        </button>
    <?php endif; ?>
</section>

<!-- Basmalah -->
<?php if (!in_array($surah, [1, 9])): ?>
    <section class="flex justify-center py-2 mb-4">
        <div class="font-arabic text-2xl text-center text-text-main-light dark:text-white">
            بِسْمِ ٱللَّٰهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ
        </div>
    </section>
<?php endif; ?>

<!-- Verses List -->
<section class="space-y-4">
    <?php foreach ($verses as $verse): 
        $isBookmarked = $bookmarkModel->isBookmarked($user_id, $surah, $verse['verse_number']);
    ?>
    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-800 verse-item" id="ayat-<?= $verse['verse_number'] ?>" data-surah="<?= $surah ?>" data-verse="<?= $verse['verse_number'] ?>">
        <div class="flex flex-col space-y-4">
            <div class="flex justify-between items-start w-full">
                <!-- Verse Number & Bookmark -->
                <div class="flex flex-col items-center gap-2 shrink-0 mt-1">
                    <div class="w-8 h-8 bg-primary/10 dark:bg-primary/20 rounded-full flex items-center justify-center text-primary dark:text-green-400 font-bold text-sm">
                        <?= $verse['verse_number'] ?>
                    </div>
                    <button class="bookmark-btn w-8 h-8 flex items-center justify-center rounded-full text-text-sub-light dark:text-text-sub-dark hover:text-primary dark:hover:text-green-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" title="Penanda Ayat">
                        <span class="material-icons-round text-xl"><?= $isBookmarked ? 'bookmark' : 'bookmark_border' ?></span>
                    </button>
                </div>
                
                <!-- Arabic Text -->
                <div class="flex-1 text-right pl-4">
                    <p class="font-arabic text-2xl md:text-3xl text-gray-800 dark:text-white leading-loose arabic-text" dir="rtl">
                        <?= h($verse['text_ar']) ?>
                    </p>
                </div>
            </div>
            
            <!-- Translation & Latin -->
            <div class="pt-3 border-t border-gray-100 dark:border-gray-700 space-y-2">
                <p class="text-sm font-medium text-primary dark:text-green-400 italic">
                    <?= h($verse['text_latin']) ?>
                </p>
                <p class="text-sm text-text-sub-light dark:text-text-sub-dark leading-relaxed">
                    <?= h($verse['text_id']) ?>
                </p>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</section>

<!-- Pagination -->
<section class="flex items-center justify-between pt-6 pb-2">
    <!-- Next 10 (Maju means 'Next' in logic, usually higher verse numbers) -->
    <!-- Logic: $page + 1 -->
    <?php if ($page < $totalPages): ?>
        <a href="?page=quran/surah_detail&surah=<?= $surah ?>&p=<?= $page + 1 ?>&mode=pwa" class="flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-medium hover:bg-primary-dark transition-colors shadow-sm">
            <span class="material-icons-round text-lg">chevron_left</span>
            <span>Next 10</span>
        </a>
    <?php else: ?>
        <button class="flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-surface-dark text-text-sub-light dark:text-text-sub-dark disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium" disabled>
            <span class="material-icons-round text-lg">chevron_left</span>
            <span>Next 10</span>
        </button>
    <?php endif; ?>

    <span class="text-sm font-medium text-text-main-light dark:text-white">
        Page <?= $page ?> of <?= $totalPages ?>
    </span>

    <!-- Prev 10 -->
    <?php if ($page > 1): ?>
        <a href="?page=quran/surah_detail&surah=<?= $surah ?>&p=<?= $page - 1 ?>&mode=pwa" class="flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-medium hover:bg-primary-dark transition-colors shadow-sm">
            <span>Prev 10</span>
            <span class="material-icons-round text-lg">chevron_right</span>
        </a>
    <?php else: ?>
        <button class="flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-surface-dark text-text-sub-light dark:text-text-sub-dark disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium" disabled>
            <span>Prev 10</span>
            <span class="material-icons-round text-lg">chevron_right</span>
        </button>
    <?php endif; ?>
</section>

<!-- CSRF Token for JS -->
<input type="hidden" id="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verify Bookmark Scroll
    if (window.location.hash) {
        const targetId = window.location.hash.substring(1); // remove #
        const targetElement = document.getElementById(targetId);
        if (targetElement) {
            setTimeout(() => {
                // Scroll with offset for sticky header
                const headerOffset = 100; // adjust based on header height
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
      
                window.scrollTo({
                    top: offsetPosition,
                    behavior: "smooth"
                });

                // Add highlight effect
                targetElement.classList.add('ring-2', 'ring-primary', 'bg-primary/5');
                setTimeout(() => {
                    targetElement.classList.remove('ring-2', 'ring-primary', 'bg-primary/5');
                }, 3000);
            }, 500); // 500ms delay to ensure layout is stable
        }
    }

    // Bookmark Functionality using Event Delegation
    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.bookmark-btn');
        if (!btn) return;

        // Prevent default if it's inside a link or form (though it's a button)
        e.preventDefault();

        const verseItem = btn.closest('.verse-item');
        if (!verseItem) return;

        const surah = verseItem.dataset.surah;
        const verse = verseItem.dataset.verse;
        const icon = btn.querySelector('span');
        
        // Debug
        // alert('Clicked bookmark for Surah ' + surah + ':' + verse);

        const isBookmarked = icon.innerText.trim() === 'bookmark';
        const action = isBookmarked ? 'remove' : 'add';
        const originalIcon = icon.innerText;

        // Optimistic UI Update
        icon.innerText = isBookmarked ? 'bookmark_border' : 'bookmark';

        const params = new URLSearchParams();
        params.append('surah', surah);
        params.append('verse', verse);
        params.append('action', action);
        
        // Construct absolute URL
        const baseUrl = window.location.origin + window.location.pathname;
        
        fetch(baseUrl + '?page=toggle_bookmark', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: params
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error: ' + response.statusText);
            return response.json();
        })
        .then(data => {
            if (data.status === 'added') {
                icon.innerText = 'bookmark';
            } else if (data.status === 'removed') {
                icon.innerText = 'bookmark_border';
            } else {
                console.error('Bookmark toggle failed:', data);
                icon.innerText = originalIcon;
                alert('Gagal: ' + (data.error || 'Server error'));
            }
        })
        .catch(err => {
            console.error('Bookmark Fetch Error:', err);
            icon.innerText = originalIcon;
            alert('Koneksi bermasalah. Coba lagi.');
        });
    });
});
</script>

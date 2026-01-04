<?php
// src/Views/quran/surah_list_pwa.php
// $surahs and $allJuz are available from parent
?>

<section class="space-y-6 pb-20">
    <!-- Search Bar -->
    <div class="relative group">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <span class="material-icons-round text-gray-400 group-focus-within:text-primary transition-colors">search</span>
        </div>
        <!-- Simple search filter script can be added later, for now just UI -->
        <input id="surahSearch" class="w-full pl-10 pr-4 py-3.5 rounded-2xl bg-surface-light dark:bg-surface-dark border-none shadow-soft focus:ring-2 focus:ring-primary text-sm placeholder-text-sub-light dark:placeholder-text-sub-dark transition-all" placeholder="Cari Surah..." type="text">
    </div>

    <!-- Bookmark Link (Penanda) -->
    <a href="<?= BASE_URL ?>public/index.php?page=quran/bookmarks&mode=pwa" class="w-full bg-surface-light dark:bg-surface-dark rounded-2xl shadow-card p-4 flex items-center justify-between group active:scale-[0.98] transition-all border border-transparent hover:border-primary/20 block">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 text-primary dark:text-emerald-400 rounded-full flex items-center justify-center">
                <span class="material-icons-round text-2xl">bookmark</span>
            </div>
            <div class="text-left">
                <h3 class="font-display font-bold text-base text-gray-900 dark:text-white">Penanda</h3>
                <p class="text-xs text-text-sub-light dark:text-text-sub-dark mt-0.5">Lihat ayat yang Anda simpan</p>
            </div>
        </div>
        <div class="w-8 h-8 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center text-gray-400 group-hover:text-primary transition-colors">
            <span class="material-icons-round text-lg">chevron_right</span>
        </div>
    </a>

    <!-- Filter Toggle (Surah / Juz) -->
    <div class="space-y-4">
        <div class="flex items-center space-x-2 bg-surface-light dark:bg-surface-dark p-1.5 rounded-xl shadow-soft">
            <button class="flex-1 py-2 px-4 rounded-lg bg-primary text-white font-medium text-sm shadow-md transition-all">Surah</button>
            <form method="GET" action="index.php" class="flex-1">
                <input type="hidden" name="page" value="quran/surah_list">
                <input type="hidden" name="mode" value="pwa">
                <select name="juz" onchange="this.form.submit()" class="w-full py-2 px-4 rounded-lg text-text-sub-light dark:text-text-sub-dark hover:bg-gray-100 dark:hover:bg-white/5 font-medium text-sm transition-all bg-transparent border-none text-center appearance-none focus:ring-0 cursor-pointer">
                    <option value="" disabled selected>Juz</option>
                    <?php foreach ($allJuz as $juz): ?>
                        <option value="<?= $juz ?>">Juz <?= $juz ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <div class="flex items-center justify-between px-1">
            <h2 class="text-lg font-display font-bold text-text-main-light dark:text-white">Daftar Surah</h2>
            <!-- Sorting logic could be added here -->
            <button class="text-xs font-medium text-primary hover:underline">Urutkan</button>
        </div>

        <!-- Surah List -->
        <div class="space-y-3" id="surahContainer">
            <?php if (empty($surahs)): ?>
                <div class="text-center py-10 text-gray-400">
                    <p>Tidak ada surah ditemukan.</p>
                </div>
            <?php else: ?>
                <?php foreach ($surahs as $surah): ?>
                    <a href="?page=quran/surah_detail&surah=<?= $surah['surah_number'] ?>&mode=pwa" class="surah-item w-full bg-surface-light dark:bg-surface-dark rounded-2xl p-4 shadow-sm hover:shadow-md transition-all active:scale-[0.99] flex items-center justify-between group border border-gray-100 dark:border-gray-800 block text-left">
                        <div class="flex items-center space-x-4">
                            <!-- Diamond Number -->
                            <div class="relative w-10 h-10 flex items-center justify-center flex-shrink-0">
                                <div class="absolute inset-0 bg-primary/10 dark:bg-primary/20 rotate-45 rounded-lg group-hover:rotate-12 transition-transform duration-300"></div>
                                <span class="relative text-sm font-bold text-primary dark:text-green-400"><?= $surah['surah_number'] ?></span>
                            </div>
                            
                            <div class="text-left">
                                <h4 class="font-bold text-gray-800 dark:text-white text-base surah-name"><?= h($surah['surah_name_en']) ?></h4>
                                <div class="flex items-center space-x-2 text-xs text-text-sub-light dark:text-text-sub-dark mt-0.5">
                                    <!-- Meaning/Translation is fetched if available in DB, using placeholder or EN name for now as existing data might strictly be name_en -->
                                    <span><?= h($surah['surah_name_en']) ?></span> 
                                    <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                    <span><?= $surah['full_verses'] ?> Ayat</span>
                                </div>
                            </div>
                        </div>
                        <span class="font-display font-bold text-primary dark:text-green-400 text-xl opacity-80"><?= h($surah['surah_name_ar']) ?></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
    // Client-side search filter
    document.getElementById('surahSearch').addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        const items = document.querySelectorAll('.surah-item');
        
        items.forEach(item => {
            const name = item.querySelector('.surah-name').textContent.toLowerCase();
            const number = item.querySelector('.relative.text-sm').textContent;
            
            if (name.includes(query) || number.includes(query)) {
                item.style.display = 'flex'; // Restore flex display
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>

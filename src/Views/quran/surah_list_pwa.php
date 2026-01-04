<?php
// src/Views/quran/surah_list_pwa.php
?>
<section>
    <div class="flex items-center space-x-2 mb-4 px-1">
        <span class="material-icons-round text-primary dark:text-green-400">menu_book</span>
        <h2 class="text-lg font-display font-bold text-text-main-light dark:text-white">Daftar Surah</h2>
    </div>

    <!-- Juz Filter (Simplified for PWA - maybe just a dropdown or omitted for now to keep it clean, added as simple select) -->
    <div class="mb-4">
        <form method="GET" action="index.php">
            <input type="hidden" name="page" value="quran/surah_list">
            <input type="hidden" name="mode" value="pwa">
            <select name="juz" onchange="this.form.submit()" class="w-full bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">Semua Juz</option>
                <?php foreach ($allJuz as $juz): ?>
                    <option value="<?= $juz ?>" <?= $selectedJuz == $juz ? 'selected' : '' ?>>Juz <?= $juz ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if (empty($surahs)): ?>
        <div class="bg-surface-light dark:bg-surface-dark rounded-2xl shadow-card p-6 text-center">
            <p class="text-text-sub-light dark:text-text-sub-dark">Tidak ada surah.</p>
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($surahs as $surah): ?>
                <a href="?page=quran/surah_detail&surah=<?= $surah['surah_number'] ?>&mode=pwa" class="block bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm p-4 border border-gray-100 dark:border-gray-800 hover:border-primary/50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
                                <?= $surah['surah_number'] ?>
                            </div>
                            <div>
                                <h3 class="font-display font-bold text-gray-900 dark:text-white"><?= h($surah['surah_name_en']) ?></h3>
                                <p class="text-xs text-text-sub-light dark:text-text-sub-dark"><?= $surah['full_verses'] ?> Ayat • Juz <?= $surah['juz'] ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-display font-bold text-xl text-primary dark:text-green-400"><?= h($surah['surah_name_ar']) ?></span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

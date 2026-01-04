<?php
// src/Views/shared/list_short_prayers_pwa.php
?>
<section>
    <div class="flex items-center space-x-2 mb-4 px-1">
        <span class="material-icons-round text-primary dark:text-green-400">volunteer_activism</span>
        <h2 class="text-lg font-display font-bold text-text-main-light dark:text-white">Doa-doa Pendek</h2>
    </div>

    <div class="space-y-4">
        <?php foreach ($prayers as $prayer): ?>
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl shadow-card p-5 border border-gray-100 dark:border-gray-800">
                <h3 class="font-display font-bold text-gray-900 dark:text-white mb-3 border-b border-gray-100 dark:border-gray-700 pb-2"><?= h($prayer['title']) ?></h3>
                
                <div class="text-right mb-4">
                    <p class="font-display text-2xl leading-loose text-gray-800 dark:text-gray-200" style="font-family: 'Amiri', serif; direction: rtl;">
                        <?= nl2br(h($prayer['arabic_text'])) ?>
                    </p>
                </div>
                
                <div class="bg-primary/5 rounded-xl p-3">
                    <p class="text-sm text-text-sub-light dark:text-text-sub-dark italic">
                        "<?= nl2br(h($prayer['translation'])) ?>"
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

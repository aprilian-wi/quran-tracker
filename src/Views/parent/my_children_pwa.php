<?php
// src/Views/parent/my_children_pwa.php
// Assumes $children is available from the controller
?>

<section>
    <div class="flex items-center space-x-2 mb-4 px-1">
        <span class="material-icons-round text-primary dark:text-green-400">face</span>
        <h2 class="text-lg font-display font-bold text-text-main-light dark:text-white">Anak Saya</h2>
    </div>

    <?php if (empty($children)): ?>
        <div class="bg-surface-light dark:bg-surface-dark rounded-2xl shadow-card p-6 border border-gray-100 dark:border-gray-800 text-center">
            <p class="text-text-sub-light dark:text-text-sub-dark">Tidak ada anak ditemukan.</p>
        </div>
    <?php else: ?>
        <?php foreach ($children as $child): ?>
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl shadow-card p-6 border border-gray-100 dark:border-gray-800 relative overflow-hidden mb-6">
                <!-- Decoration -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-full -mr-10 -mt-10 z-0"></div>
                
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-display font-bold text-gray-900 dark:text-white capitalize"><?= h($child['name']) ?></h3>
                            <p class="text-xs text-text-sub-light dark:text-text-sub-dark mt-1">Siswa Aktif</p>
                        </div>
                        <?php if (!empty($child['class_name'])): ?>
                            <span class="bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
                                <?= h($child['class_name']) ?>
                            </span>
                        <?php else: ?>
                            <span class="bg-gray-400 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
                                Belum Ada Kelas
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="flex flex-col items-center mb-6">
                        <div class="relative group cursor-pointer">
                            <div class="w-28 h-28 rounded-full border-4 border-white dark:border-gray-700 shadow-md overflow-hidden bg-gray-200">
                                <?php if (!empty($child['photo'])): ?>
                                    <img alt="Foto profil <?= h($child['name']) ?>" class="w-full h-full object-cover" src="<?= BASE_URL ?>public/uploads/children_photos/<?= h($child['photo']) ?>"/>
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <span class="material-icons-round text-6xl">person</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- Edit Photo Button (Functionality to be added later if needed, kept for UI consistency) -->
                            <!--
                            <button class="absolute bottom-1 right-1 bg-white dark:bg-gray-700 text-gray-700 dark:text-white p-2 rounded-full shadow-md hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors border border-gray-200 dark:border-gray-600">
                                <span class="material-icons-round text-sm font-bold block">edit</span>
                            </button>
                            -->
                        </div>
                        
                        <div class="mt-4 text-center">
                            <!-- Helper to check notifications logic can be added here -->
                            <p class="text-sm text-text-sub-light dark:text-text-sub-dark italic bg-gray-50 dark:bg-gray-800/50 px-4 py-2 rounded-lg border border-gray-100 dark:border-gray-800">
                                "Semangat belajar Al-Qur'an!"
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <a href="<?= BASE_URL ?>public/index.php?page=parent/update_progress&child_id=<?= $child['id'] ?>&mode=pwa" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 px-2 rounded-xl text-sm shadow-md transition-all active:scale-95 flex items-center justify-center gap-1">
                            <span class="material-icons-round text-sm">mic</span> Tahfidz
                        </a>
                        <a href="<?= BASE_URL ?>public/index.php?page=parent/update_progress_books&child_id=<?= $child['id'] ?>&mode=pwa" class="bg-yellow-400 hover:bg-yellow-500 text-yellow-900 font-medium py-2.5 px-2 rounded-xl text-sm shadow-md transition-all active:scale-95 flex items-center justify-center gap-1">
                            <span class="material-icons-round text-sm">auto_stories</span> Tahsin
                        </a>
                        <a href="<?= BASE_URL ?>public/index.php?page=parent/update_progress_prayers&child_id=<?= $child['id'] ?>&mode=pwa" class="bg-cyan-400 hover:bg-cyan-500 text-cyan-900 font-medium py-2.5 px-2 rounded-xl text-sm shadow-md transition-all active:scale-95 flex items-center justify-center gap-1">
                            <span class="material-icons-round text-sm">waving_hand</span> Doa
                        </a>
                        <a href="<?= BASE_URL ?>public/index.php?page=parent/update_progress_hadiths&child_id=<?= $child['id'] ?>&mode=pwa" class="bg-rose-500 hover:bg-rose-600 text-white font-medium py-2.5 px-2 rounded-xl text-sm shadow-md transition-all active:scale-95 flex items-center justify-center gap-1">
                            <span class="material-icons-round text-sm">format_quote</span> Hadits
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

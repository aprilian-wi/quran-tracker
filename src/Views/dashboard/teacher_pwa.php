<?php
// src/Views/dashboard/teacher_pwa.php
?>
<section class="mb-6 pt-2 px-1">
    <div class="flex items-center justify-between">
        <div class="flex flex-col">
            <h2 class="text-xl font-display font-bold text-text-main-light dark:text-white">Dashboard Guru</h2>
            <p class="text-xs text-text-sub-light dark:text-text-sub-dark">Selamat Datang, <?= h($user['name']) ?></p>
        </div>
        
    </div>
</section>

<!-- Stats Card -->
<section class="mb-6">
    <div class="bg-gradient-to-br from-primary to-primary-dark rounded-2xl p-5 shadow-lg text-white relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
        <div class="absolute -left-4 -top-4 w-16 h-16 bg-white/10 rounded-full blur-lg"></div>

        <div class="relative z-10 flex flex-col items-center justify-center text-center py-2">
            <span class="text-xs font-medium text-green-100 uppercase tracking-wider mb-1">Total Siswa</span>
            <h3 class="text-4xl font-bold font-display"><?= $data['total_students'] ?? 0 ?></h3>
            <p class="text-xs text-green-100 mt-1">Di semua kelas Anda</p>
        </div>
    </div>
</section>

<!-- Classes List -->
<section class="space-y-4 pb-20">
    <h3 class="text-sm font-bold text-text-main-light dark:text-white px-1">Kelas Saya</h3>

    <?php if (empty($data['classes'])): ?>
        <div class="flex flex-col items-center justify-center py-10 text-center space-y-3">
            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center text-gray-400">
                <span class="material-icons-round text-2xl">class</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada kelas yang ditugaskan.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 gap-4">
            <?php foreach ($data['classes'] as $class): ?>
                <a href="<?= BASE_URL ?>public/index.php?page=teacher/class_students&class_id=<?= $class['id'] ?>&mode=pwa" class="bg-surface-light dark:bg-surface-dark rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-800 flex items-center justify-between group active:scale-[0.98] transition-all">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-xl bg-orange-100 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                            <span class="material-icons-round text-2xl">school</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 dark:text-white text-base"><?= h($class['name']) ?></h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                <span class="font-medium text-primary dark:text-green-400"><?= $class['student_count'] ?></span> Siswa
                            </p>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-400 group-hover:text-primary dark:group-hover:text-green-400 transition-colors">
                        <span class="material-icons-round text-lg">chevron_right</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

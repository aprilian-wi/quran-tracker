<?php
// src/Views/dashboard/teacher.php
require_once __DIR__ . '/../../Controllers/DashboardController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new DashboardController($pdo);
$data = $controller->index();
$pageTitle = 'Teacher Dashboard';

include __DIR__ . '/../layouts/admin.php';
?>

<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Dashboard Guru</h1>
    <p class="text-sm text-slate-500 dark:text-slate-400">Selamat Datang, <?= h($_SESSION['user_name']) ?></p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Stats Card -->
    <div class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Total Siswa</p>
            <h3 class="text-3xl font-bold text-primary"><?= $data['total_students'] ?></h3>
            <p class="text-xs text-slate-400 mt-1">Di semua kelas Anda</p>
        </div>
        <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
            <span class="material-icons-round text-3xl">groups</span>
        </div>
    </div>

    <!-- Info Card -->
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl shadow-sm border border-blue-100 dark:border-blue-800 p-6 flex items-start gap-4">
        <div class="flex-shrink-0">
            <span class="material-icons-round text-blue-500 text-2xl">info</span>
        </div>
        <div>
            <h4 class="text-blue-800 dark:text-blue-300 font-semibold mb-1">Informasi</h4>
            <p class="text-sm text-blue-700 dark:text-blue-200">
                Klik tombol "Lihat" pada tabel di bawah untuk melihat detail siswa dan memperbarui kemajuan hafalan Al-Qur'an (Tahfidz, Tahsin, dll) mereka.
            </p>
        </div>
    </div>
</div>

<div class="mt-8">
    <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex items-center gap-2">
            <span class="material-icons-round text-slate-500">class</span>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Kelas Saya</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-800/80">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jumlah Siswa</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-card-dark divide-y divide-slate-200 dark:divide-slate-700">
                    <?php if (empty($data['classes'])): ?>
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                                Belum ada kelas yang ditugaskan kepada Anda.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['classes'] as $class): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 dark:text-white"><?= h($class['name']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                        <?= $class['student_count'] ?> Siswa
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?= BASE_URL ?>public/index.php?page=teacher/class_students&class_id=<?= $class['id'] ?>" 
                                       class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors decoration-0">
                                        <span class="material-icons-round text-sm mr-1">visibility</span> Lihat
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

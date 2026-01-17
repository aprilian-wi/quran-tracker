<?php
// src/Views/admin/list_children.php
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new AdminController($pdo);

$class_id = $_GET['class_id'] ?? null;
$schoolSearch = $_GET['school_q'] ?? null;

// Only fetch classes if not global admin OR if we want to show it? User said "Tidak perlu filter by Class".
$classes = isGlobalAdmin() ? [] : $controller->classes();
$children = $controller->getChildren($class_id, ['school_search' => $schoolSearch]);

include __DIR__ . '/../layouts/admin.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div
            class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-primary">
            <span class="material-icons-round text-2xl">child_care</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Daftar Siswa</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">View and manage student progress</p>
        </div>
    </div>
    <a href="<?= BASE_URL ?>public/index.php?page=dashboard"
        class="flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 rounded-lg text-slate-600 dark:text-slate-300 text-sm font-medium transition-all shadow-sm hover:shadow decoration-0">
        <span class="material-icons-round text-lg">arrow_back</span>
        Kembali
    </a>
</div>

<div
    class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <!-- Filters -->
    <form method="GET"
        class="p-5 border-b border-slate-200 dark:border-slate-700 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-800/50">
        <input type="hidden" name="page" value="admin/list_children">
        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">

            <?php if (isGlobalAdmin()): ?>
                <!-- Superadmin School Search -->
                <div class="relative min-w-[200px]">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <span class="material-icons-round text-lg">domain</span>
                    </span>
                    <input type="text" name="school_q" value="<?= h($schoolSearch ?? '') ?>" placeholder="Cari Sekolah..."
                        class="pl-10 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm py-2.5 shadow-sm transition-shadow">
                </div>
            <?php else: ?>
                <!-- Standard Class Filter -->
                <div class="relative min-w-[200px]">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <span class="material-icons-round text-lg">filter_alt</span>
                    </span>
                    <select name="class_id"
                        class="pl-10 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm py-2.5 shadow-sm transition-shadow">
                        <option value="">-- Semua Kelas --</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= h($class['id']) ?>" <?= ($class_id == $class['id']) ? 'selected' : '' ?>>
                                <?= h($class['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <span class="material-icons-round text-sm mr-2">search</span>
                    Filter
                </button>
                <a href="?page=admin/list_children"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2.5 border border-slate-300 dark:border-slate-600 text-sm font-medium rounded-lg text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors decoration-0">
                    Reset
                </a>
            </div>
        </div>
        <div class="flex w-full lg:w-auto">
            <a href="?page=admin/export_children&class_id=<?= urlencode($class_id ?? '') ?>"
                class="w-full lg:w-auto inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors decoration-0">
                <span class="material-icons-round text-sm mr-2">file_download</span>
                Ekspor CSV
            </a>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-800/80">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                        scope="col">Nama</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                        scope="col">Tgl. Lahir</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase
                    tracking-wider"
                    scope="col">Nama Wali</th>
                    <?php if (isGlobalAdmin()): ?>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase
                        tracking-wider" scope="col">Sekolah</th>
                    <?php else: ?>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase
                    tracking-wider"
                        scope="col">Kelas</th>
                    <?php endif; ?>
                    <?php if (!isGlobalAdmin()): ?>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-80"
                            scope="col">Perbarui Kemajuan</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-card-dark divide-y divide-slate-200 dark:divide-slate-700">
                <?php if (empty($children)): ?>
                    <tr>
                        <td colspan="<?= isGlobalAdmin() ? 4 : 5 ?>"
                            class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                            Tidak ada siswa ditemukan. Coba sesuaikan filter atau tambahkan siswa.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($children as $child): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-bold text-slate-900 dark:text-white"><?= h($child['name']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                            <?= !empty($child['date_of_birth']) ? date('d M Y', strtotime($child['date_of_birth'])) : '-' ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                        <?= h($child['parent_name'] ?? '-') ?>
                        </td>
                        <?php if (isGlobalAdmin()): ?>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100
                            text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    <?= h($child['school_name'] ?? 'N/A') ?>
                                </span>
                            </td>
                        <?php else: ?>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800
                            dark:bg-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                    <?= h($child['class_name']) ?>
                                </span>
                                                       </td>
      
                            <?php endif; ?>
                            <?php if (!isGlobalAdmin()): ?>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex justify-center gap-2">
                                    <a href="?page=admin/update_progress&child_id=<?= h($child['id']) ?>"
                                        class="inline-flex items-center px-3 py-1.5 border border-blue-200 dark:border-blue-800 rounded-lg text-xs font-medium text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors decoration-0">
                                        Tahfidz
                                    </a>
                                    <a href="?page=admin/update_progress_books&child_id=<?= h($child['id']) ?>"
                                        class="inline-flex items-center px-3 py-1.5 border border-amber-200 dark:border-amber-800 rounded-lg text-xs font-medium text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-colors decoration-0">
                                        Tahsin
                                    </a>
                                    <a href="?page=admin/update_progress_hadiths&child_id=<?= h($child['id']) ?>"
                                        class="inline-flex items-center px-3 py-1.5 border border-cyan-200 dark:border-cyan-800 rounded-lg text-xs font-medium text-cyan-700 dark:text-cyan-300 bg-cyan-50 dark:bg-cyan-900/20 hover:bg-cyan-100 dark:hover:bg-cyan-900/40 transition-colors decoration-0">
                                        Hadits
                                    </a>
                                    <a href="?page=admin/update_progress_prayers&child_id=<?= h($child['id']) ?>"
                                        class="inline-flex items-center px-3 py-1.5 border border-emerald-200 dark:border-emerald-800 rounded-lg text-xs font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors decoration-0">
                                        Doa
                                    </a>
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div
        class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
        <p class="text-sm text-slate-500 dark:text-slate-400">Showing <span
                class="font-medium"><?= count($children) ?></span> students</p>
        <div class="flex gap-2">
            <button
                class="px-3 py-1 text-sm rounded border border-slate-200 dark:border-slate-600 text-slate-400 dark:text-slate-500 cursor-not-allowed bg-white dark:bg-slate-800"
                disabled="">Prev</button>
            <button
                class="px-3 py-1 text-sm rounded border border-slate-200 dark:border-slate-600 text-slate-400 dark:text-slate-500 cursor-not-allowed bg-white dark:bg-slate-800"
                disabled="">Next</button>
        </div>
    </div>
</div>
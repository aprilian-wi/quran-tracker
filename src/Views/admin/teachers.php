<?php
// src/Views/admin/teachers.php
global $pdo;
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new AdminController($pdo);
$teachers = $controller->teachers();

include __DIR__ . '/../layouts/admin.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div
            class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-primary">
            <span class="material-icons-round text-2xl">school</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Daftar Guru</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage teacher accounts</p>
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
    <div
        class="p-5 border-b border-slate-200 dark:border-slate-700 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-800/50">
        <div class="flex-1"></div>
        <div class="flex flex-col sm:flex-row w-full lg:w-auto gap-3">
            <a href="<?= BASE_URL ?>public/index.php?page=create_teacher"
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors decoration-0">
                <span class="material-icons-round text-sm mr-2">person_add</span>
                Tambah Guru
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-800/80">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                        scope="col">Nama</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                        scope="col">No. HP</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                        scope="col">Tgl. Dibuat</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-40"
                        scope="col">Tindakan</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-card-dark divide-y divide-slate-200 dark:divide-slate-700">
                <?php if (empty($teachers)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                            Tidak ada guru ditemukan. Mulai dengan menambahkan guru baru.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($teachers as $teacher): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-8 w-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-300 text-xs font-bold uppercase">
                                    <?= strtoupper(substr($teacher['name'], 0, 1)) ?>
                                </div>
                                <div class="text-sm font-bold text-slate-900 dark:text-white"><?= h($teacher['name']) ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                            <?= h($teacher['phone']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                            <?= date('d M Y', strtotime($teacher['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <a href="<?= BASE_URL ?>public/index.php?page=edit_teacher&teacher_id=<?= $teacher['id'] ?>"
                                    class="text-amber-500 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 p-1.5 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors decoration-0"
                                    title="Edit">
                                    <span class="material-icons-round text-lg">edit</span>
                                </a>
                                <button onclick="confirmDelete(<?= $teacher['id'] ?>, 'teacher')"
                                    class="text-rose-500 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 p-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors decoration-0"
                                    title="Hapus">
                                    <span class="material-icons-round text-lg">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div
        class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
        <p class="text-sm text-slate-500 dark:text-slate-400">Showing <span
                class="font-medium"><?= count($teachers) ?></span> teachers</p>
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

<script>
    function confirmDelete(id, type) {
        if (confirm(`Hapus guru ini? Tindakan ini tidak dapat dibatalkan.`)) {
            window.location.href = `?page=delete_${type}&id=${id}`;
        }
    }
</script>
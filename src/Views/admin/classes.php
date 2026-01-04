<?php
// src/Views/admin/classes.php
global $pdo;
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new AdminController($pdo);
$classes = $controller->classes();
// Helper to get teachers for the modal (reusing logic from existing file)
$teachers = $controller->teachers();

include __DIR__ . '/../layouts/admin.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-primary">
            <span class="material-icons-round text-2xl">apartment</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Daftar Kelas</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage classes and students</p>
        </div>
    </div>
    <a href="<?= BASE_URL ?>public/index.php?page=dashboard" class="flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 rounded-lg text-slate-600 dark:text-slate-300 text-sm font-medium transition-all shadow-sm hover:shadow decoration-0">
        <span class="material-icons-round text-lg">arrow_back</span>
        Kembali
    </a>
</div>

<div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-800/50">
        <div class="flex-1"></div>
        <div class="flex flex-col sm:flex-row w-full lg:w-auto gap-3">
            <a href="<?= BASE_URL ?>public/index.php?page=admin/promote_class" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 border border-blue-600 text-sm font-medium rounded-lg shadow-sm text-blue-600 bg-white hover:bg-blue-50 dark:bg-transparent dark:text-blue-400 dark:border-blue-500 dark:hover:bg-blue-900/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors decoration-0">
                <span class="material-icons-round text-sm mr-2">swap_horiz</span>
                Promosi / Pindah Kelas
            </a>
            <button onclick="document.getElementById('createClassModal').classList.remove('hidden')" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <span class="material-icons-round text-sm mr-2">add</span>
                Kelas Baru
            </button>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-800/80">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" scope="col">Nama</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" scope="col">Guru</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" scope="col">Siswa</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-40" scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-card-dark divide-y divide-slate-200 dark:divide-slate-700">
                <?php if (empty($classes)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                            Tidak ada kelas ditemukan. Mulai dengan membuat kelas pertama Anda.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($classes as $class): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-slate-900 dark:text-white"><?= h($class['name']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                            <?= $class['teacher_names'] ?? '<span class="italic text-slate-400">Belum Ditugaskan</span>' ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-100 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-800">
                                <?= $class['student_count'] ?> Siswa
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <a href="<?= BASE_URL ?>public/index.php?page=admin/edit_class&class_id=<?= $class['id'] ?>" class="text-amber-500 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 p-1.5 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors decoration-0" title="Edit">
                                    <span class="material-icons-round text-lg">edit</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
        <p class="text-sm text-slate-500 dark:text-slate-400">Showing <span class="font-medium"><?= count($classes) ?></span> classes</p>
        <div class="flex gap-2">
            <button class="px-3 py-1 text-sm rounded border border-slate-200 dark:border-slate-600 text-slate-400 dark:text-slate-500 cursor-not-allowed bg-white dark:bg-slate-800" disabled="">Prev</button>
            <button class="px-3 py-1 text-sm rounded border border-slate-200 dark:border-slate-600 text-slate-400 dark:text-slate-500 cursor-not-allowed bg-white dark:bg-slate-800" disabled="">Next</button>
        </div>
    </div>
</div>

<!-- Create Class Modal -->
<div id="createClassModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('createClassModal').classList.add('hidden')"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white dark:bg-card-dark rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form method="POST" action="<?= BASE_URL ?>public/index.php?page=create_class">
                <?= csrfInput() ?>
                <div class="bg-white dark:bg-card-dark px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/50 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-icons-round text-blue-600 dark:text-blue-400">add_business</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-slate-900 dark:text-white" id="modal-title">
                                Buat Kelas Baru
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Kelas</label>
                                    <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:text-white sm:text-sm">
                                </div>
                                <div>
                                    <label for="teacher_ids" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tetapkan Guru (Ctrl/Cmd untuk banyak)</label>
                                    <select name="teacher_ids[]" id="teacher_ids" multiple class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:text-white sm:text-sm h-32">
                                        <?php foreach ($teachers as $teacher): ?>
                                            <option value="<?= $teacher['id'] ?>"><?= h($teacher['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Buat Kelas
                    </button>
                    <button type="button" onclick="document.getElementById('createClassModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-700 text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

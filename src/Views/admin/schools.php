<?php
// src/Views/admin/schools.php
$pageTitle = 'Manage Schools';
include __DIR__ . '/../layouts/admin.php';

require_once __DIR__ . '/../../Controllers/SystemAdminController.php';
$controller = new SystemAdminController($pdo);
$schools = $controller->getAllSchools();
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div
            class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-teal-600 dark:text-teal-400">
            <span class="material-icons-round text-2xl">domain</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Kelola Sekolah</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Ikhtisar semua sekolah yang terdaftar</p>
        </div>
    </div>

    <div class="flex gap-3">
        <a href="<?= BASE_URL ?>public/index.php?page=dashboard"
            class="flex items-center justify-center px-4 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm">
            <span class="material-icons-round text-lg mr-2">arrow_back</span>
            Kembali
        </a>
        <a href="?page=admin/create_school"
            class="flex items-center justify-center px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-medium hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors shadow-sm">
            <span class="material-icons-round text-lg mr-2">add_circle</span>
            Tambah Sekolah
        </a>
    </div>
</div>

<div
    class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-800/80">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        ID</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Nama Sekolah</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Kota/Kabupaten</th>
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Guru</th>
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Siswa</th>
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Kelas</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Terdaftar</th>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php if (empty($schools)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">Tidak ada sekolah
                            ditemukan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($schools as $school): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                #<?= $school['id'] ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                                <?= h($school['name']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                <span class="truncate max-w-xs block" title="<?= h($school['kabupaten'] ?? '-') ?>">
                                    <?= h($school['kabupaten'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300">
                                    <?= $school['teacher_count'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                                    <?= $school['parent_count'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">
                                    <?= $school['class_count'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                <?= date('d M Y', strtotime($school['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <?php if ($school['id'] != 1): // Prevent editing/deleting Main School easily ?>
                                        <a href="?page=admin/edit_school&id=<?= $school['id'] ?>"
                                            class="text-amber-500 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 p-1 rounded hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors"
                                            title="Edit">
                                            <span class="material-icons-round text-lg">edit</span>
                                        </a>
                                        <button onclick="confirmDelete(<?= $school['id'] ?>, '<?= h($school['name']) ?>')"
                                            class="text-rose-500 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 p-1 rounded hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors"
                                            title="Hapus">
                                            <span class="material-icons-round text-lg">delete</span>
                                        </button>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                            System
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
        onclick="closeModal('deleteModal')"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative transform overflow-hidden rounded-xl bg-white dark:bg-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700">
                <div
                    class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-red-50 dark:bg-red-900/20 flex justify-between items-center">
                    <h3 class="text-lg font-medium leading-6 text-red-700 dark:text-red-400">Konfirmasi Penghapusan</h3>
                    <button type="button" onclick="closeModal('deleteModal')"
                        class="text-slate-400 hover:text-slate-500">
                        <span class="material-icons-round">close</span>
                    </button>
                </div>
                <div class="px-6 py-4">
                    <div class="flex items-start gap-4 mb-4">
                        <div
                            class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400">
                            <span class="material-icons-round text-2xl">warning</span>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                Apakah Anda yakin ingin menghapus <strong id="deleteTargetName"
                                    class="text-slate-900 dark:text-white"></strong>?
                            </p>
                            <div
                                class="mt-2 p-3 bg-red-50 dark:bg-red-900/10 rounded-lg border border-red-100 dark:border-red-900/50 text-xs text-red-700 dark:text-red-300">
                                <strong>PERINGATAN KRITIS:</strong> Ini akan menghapus SEMUA data terkait: pengguna,
                                siswa, kelas, dan data kemajuan. Tindakan ini tidak dapat dibatalkan.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex flex-row-reverse gap-3">
                    <form method="POST" action="?page=admin/delete_school">
                        <?= csrfInput() ?>
                        <input type="hidden" name="id" id="deleteTargetId">
                        <button type="submit"
                            class="inline-flex justify-center rounded-lg border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            Ya, Hapus Sekolah
                        </button>
                    </form>
                    <button type="button" onclick="closeModal('deleteModal')"
                        class="inline-flex justify-center rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, name) {
        document.getElementById('deleteTargetId').value = id;
        document.getElementById('deleteTargetName').textContent = name;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>
<?php
// src/Views/admin/manage_hadiths.php
require_once __DIR__ . '/../../Controllers/AdminController.php';

$controller = new AdminController($pdo);
$hadiths = $controller->getHadiths();

include __DIR__ . '/../layouts/admin.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-primary">
            <span class="material-icons-round text-2xl">menu_book</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Kelola Daftar Hadits</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage hadiths and translations</p>
        </div>
    </div>
    <a href="<?= BASE_URL ?>public/index.php?page=dashboard" class="flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 rounded-lg text-slate-600 dark:text-slate-300 text-sm font-medium transition-all shadow-sm hover:shadow decoration-0">
        <span class="material-icons-round text-lg">arrow_back</span>
        Kembali
    </a>
</div>

<div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-800/50">
        <div class="flex-1">
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Konfigurasikan daftar hadits dengan teks Arab dan terjemahan.
            </p>
        </div>
        <div class="flex flex-col sm:flex-row w-full lg:w-auto gap-3">
            <a href="?page=admin/create_hadith" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors decoration-0">
                <span class="material-icons-round text-sm mr-2">add_circle</span>
                Tambah Hadits Baru
            </a>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-800/80">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-20" scope="col">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" scope="col">Judul</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" scope="col">Teks Arab</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" scope="col">Terjemahan</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-32" scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-card-dark divide-y divide-slate-200 dark:divide-slate-700">
                <?php if (empty($hadiths)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                            Tidak ada hadits ditemukan. Tambahkan hadits pertama Anda.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($hadiths as $hadith): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                            #<?= $hadith['id'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900 dark:text-white">
                            <?= h($hadith['title']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-800 dark:text-slate-200 font-amiri text-lg" dir="rtl">
                            <?= h($hadith['arabic_text']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 max-w-xs truncate" title="<?= h($hadith['translation']) ?>">
                            <?= h($hadith['translation']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <a href="?page=admin/edit_hadith&id=<?= $hadith['id'] ?>" class="text-amber-500 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 p-1.5 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors decoration-0" title="Edit">
                                    <span class="material-icons-round text-lg">edit</span>
                                </a>
                                <button onclick="confirmDelete(<?= $hadith['id'] ?>, '<?= h($hadith['title']) ?>')" class="text-rose-500 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 p-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors decoration-0" title="Hapus">
                                    <span class="material-icons-round text-lg">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('deleteModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-card-dark rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white dark:bg-card-dark px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/50 sm:mx-0 sm:h-10 sm:w-10">
                        <span class="material-icons-round text-red-600 dark:text-red-400">warning</span>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-slate-900 dark:text-white" id="modal-title">
                            Konfirmasi Penghapusan
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                Apakah Anda yakin ingin menghapus hadits "<span id="itemTitle" class="font-bold"></span>"?
                            </p>
                            <p class="text-sm text-red-600 dark:text-red-400 mt-2 font-medium">
                                Peringatan: Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <!-- Using GET request because original implementation was linking to delete_hadith&id=... -->
                <a id="deleteConfirmLink" href="#" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm decoration-0">
                    Hapus
                </a>
                <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-700 text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, title) {
    document.getElementById('itemTitle').textContent = title;
    // Set the deletion link
    document.getElementById('deleteConfirmLink').href = `?page=admin/delete_hadith&id=${id}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}
</script>

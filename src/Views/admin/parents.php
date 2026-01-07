<?php
// src/Views/admin/parents.php
global $pdo;
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new AdminController($pdo);
$parents = $controller->parents();

include __DIR__ . '/../layouts/admin.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-primary">
            <span class="material-icons-round text-2xl">people_alt</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Wali Siswa</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage parents and students</p>
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
            <button type="button" onclick="document.getElementById('importCsvModal').classList.remove('hidden')" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 border border-slate-300 dark:border-slate-600 text-sm font-medium rounded-lg shadow-sm text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                <span class="material-icons-round text-sm mr-2">upload_file</span>
                Import CSV
            </button>
            <a href="<?= BASE_URL ?>public/index.php?page=create_parent" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors decoration-0">
                <span class="material-icons-round text-sm mr-2">person_add</span>
                Tambah Wali
            </a>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-800/80">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" scope="col">Nama</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" scope="col">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" scope="col">Anak</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" scope="col">Tgl. Dibuat</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-40" scope="col">Tindakan</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-card-dark divide-y divide-slate-200 dark:divide-slate-700">
                <?php if (empty($parents)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                            Tidak ada wali siswa ditemukan. Mulai dengan menambahkan wali baru.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($parents as $parent): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-300 text-xs font-bold uppercase">
                                    <?= strtoupper(substr($parent['name'], 0, 1)) ?>
                                </div>
                                <div class="text-sm font-bold text-slate-900 dark:text-white"><?= h($parent['name']) ?></div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                            <?= h($parent['email']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-100 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-800">
                                <?= $parent['child_count'] ?> Anak
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                            <?= date('d M Y', strtotime($parent['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <a href="<?= BASE_URL ?>public/index.php?page=edit_parent&parent_id=<?= $parent['id'] ?>" class="text-amber-500 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 p-1.5 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors decoration-0" title="Edit">
                                    <span class="material-icons-round text-lg">edit</span>
                                </a>
                                <button type="button" class="btn-add-child text-emerald-500 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 p-1.5 rounded-lg hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors" 
                                        data-parent-id="<?= $parent['id'] ?>" 
                                        data-parent-name="<?= h($parent['name']) ?>"
                                        title="Tambah Anak">
                                    <span class="material-icons-round text-lg">person_add</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
        <p class="text-sm text-slate-500 dark:text-slate-400">Showing <span class="font-medium"><?= count($parents) ?></span> parents</p>
        <div class="flex gap-2">
            <button class="px-3 py-1 text-sm rounded border border-slate-200 dark:border-slate-600 text-slate-400 dark:text-slate-500 cursor-not-allowed bg-white dark:bg-slate-800" disabled="">Prev</button>
            <button class="px-3 py-1 text-sm rounded border border-slate-200 dark:border-slate-600 text-slate-400 dark:text-slate-500 cursor-not-allowed bg-white dark:bg-slate-800" disabled="">Next</button>
        </div>
    </div>
</div>

<!-- Add Children Modal -->
<div id="addChildrenModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('addChildrenModal').classList.add('hidden')"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white dark:bg-card-dark rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <form id="addChildrenForm" method="POST" action="<?= BASE_URL ?>public/index.php?page=add_children">
                <?= csrfInput() ?>
                <input type="hidden" name="parent_id" id="addModalParentId" value="">
                
                <div class="bg-white dark:bg-card-dark px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 dark:bg-emerald-900/50 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-icons-round text-emerald-600 dark:text-emerald-400">child_care</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-slate-900 dark:text-white">
                                Tambahkan anak untuk <span id="addModalParentName" class="font-bold"></span>
                            </h3>
                            <div class="mt-2 mb-4">
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    Anda dapat menambahkan beberapa anak sekaligus (maks 10). Tanggal lahir opsional.
                                </p>
                            </div>

                            <div id="childrenRows" class="space-y-3">
                                <!-- Dynamic rows go here -->
                            </div>

                            <div class="mt-4 flex justify-between items-center">
                                <button type="button" id="addRowBtn" class="inline-flex items-center px-3 py-1.5 border border-slate-300 dark:border-slate-600 rounded-md text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                    <span class="material-icons-round text-sm mr-1">add</span> Tambah anak lain
                                </button>
                                <span class="text-xs text-slate-400">Maks 10 anak</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan
                    </button>
                    <button type="button" onclick="document.getElementById('addChildrenModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-700 text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Import CSV Modal -->
<div id="importCsvModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('importCsvModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white dark:bg-card-dark rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-700">
            <form action="<?= BASE_URL ?>public/index.php?page=admin/import_parents" method="POST" enctype="multipart/form-data">
                <?= csrfInput() ?>
                <div class="bg-white dark:bg-card-dark px-6 pt-8 pb-6">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-blue-50 dark:bg-blue-900/30 mb-5">
                            <span class="material-icons-round text-3xl text-blue-600 dark:text-blue-400">upload_file</span>
                        </div>
                        <h3 class="text-xl leading-6 font-bold text-slate-900 dark:text-white mb-2" id="modal-title">
                            Import Data via CSV
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 max-w-sm mx-auto">
                            Tambahkan data wali dan santri secara massal dengan mengupload file CSV sesuai format.
                        </p>

                        <div class="text-left bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-dashed border-slate-300 dark:border-slate-600 mb-6">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    File CSV
                                </label>
                                <a href="<?= BASE_URL ?>public/index.php?page=admin/download_csv_template" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 flex items-center gap-1 transition-colors">
                                    <span class="material-icons-round text-sm">download</span>
                                    Download Template
                                </a>
                            </div>
                            <input type="file" name="csv_file" accept=".csv" required 
                                   class="block w-full text-sm text-slate-500 dark:text-slate-400
                                    file:mr-4 file:py-2.5 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-600 file:text-white
                                    hover:file:bg-blue-700 file:transition-colors
                                    cursor-pointer">
                            <p class="mt-2 text-xs text-slate-400 dark:text-slate-500">Maksimal 2MB. Format .csv only.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 flex flex-row-reverse gap-3">
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent shadow-sm px-5 py-2.5 bg-blue-600 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                        Upload & Import
                    </button>
                    <button type="button" onclick="document.getElementById('importCsvModal').classList.add('hidden')" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-600 shadow-sm px-5 py-2.5 bg-white dark:bg-slate-700 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-all">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, type) {
        if (confirm(`Hapus wali murid ini? Tindakan ini tidak dapat dibatalkan.`)) {
            window.location.href = `?page=delete_${type}&id=${id}`;
        }
    }

    // Modal & Dynamic Form Logic
    const modal = document.getElementById('addChildrenModal');
    
    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.child-row');
        rows.forEach((row, idx) => {
            const removeBtn = row.querySelector('.remove-row');
            if(removeBtn) {
                removeBtn.disabled = rows.length <= 1;
                removeBtn.classList.toggle('opacity-50', rows.length <= 1);
                removeBtn.classList.toggle('cursor-not-allowed', rows.length <= 1);
            }
        });
    }

    // Helper to create a row
    function createRow(index) {
        const div = document.createElement('div');
        div.className = 'flex flex-col sm:flex-row gap-3 child-row p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-100 dark:border-slate-700';
        
        div.innerHTML = `
            <div class="flex-grow">
                <input type="text" name="children[${index}][name]" class="block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-slate-700 dark:text-white sm:text-sm" placeholder="Nama lengkap anak" required>
            </div>
            <div class="w-full sm:w-40">
                <input type="date" name="children[${index}][dob]" class="block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-slate-700 dark:text-white sm:text-sm" placeholder="Tanggal lahir">
            </div>
            <div>
                <button type="button" class="remove-row inline-flex items-center justify-center p-2 rounded-md text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                    <span class="material-icons-round text-lg">delete</span>
                </button>
            </div>
        `;
        return div;
    }

    // Open Modal Handlers
    document.querySelectorAll('.btn-add-child').forEach(btn => {
        btn.addEventListener('click', function() {
            const parentId = this.getAttribute('data-parent-id');
            const parentName = this.getAttribute('data-parent-name');

            document.getElementById('addModalParentId').value = parentId;
            document.getElementById('addModalParentName').textContent = parentName;

            // Reset rows
            const container = document.getElementById('childrenRows');
            container.innerHTML = '';
            container.appendChild(createRow(0));
            updateRemoveButtons();

            // Show modal
            modal.classList.remove('hidden');
        });
    });

    // Add Row Handler
    document.getElementById('addRowBtn').addEventListener('click', function() {
        const rows = document.querySelectorAll('.child-row');
        if (rows.length >= 10) return alert('Maksimal 10 anak per permintaan');
        
        const container = document.getElementById('childrenRows');
        container.appendChild(createRow(rows.length));
        updateRemoveButtons();
    });

    // Remove Row Handler
    document.getElementById('childrenRows').addEventListener('click', function(e) {
        const removeBtn = e.target.closest('.remove-row');
        if (removeBtn && !removeBtn.disabled) {
            const row = removeBtn.closest('.child-row');
            row.remove();
            
            // Re-index
            document.querySelectorAll('.child-row').forEach((r, i) => {
                r.querySelector('input[type="text"]').name = `children[${i}][name]`;
                r.querySelector('input[type="date"]').name = `children[${i}][dob]`;
            });
            updateRemoveButtons();
        }
    });

    // Form Client-side validation
    document.getElementById('addChildrenForm').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('.child-row');
        if (rows.length === 0) {
            e.preventDefault(); alert('Mohon tambahkan setidaknya satu anak'); return;
        }
    });

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if(e.key === "Escape" && !modal.classList.contains('hidden')) {
            modal.classList.add('hidden');
        }
    });
</script>
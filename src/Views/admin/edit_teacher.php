<?php
// src/Views/admin/edit_teacher.php
global $pdo;
require_once __DIR__ . '/../../Models/User.php';
require_once __DIR__ . '/../../Helpers/functions.php';

// Get teacher_id from URL parameter
$teacher_id = isset($_GET['teacher_id']) ? (int)$_GET['teacher_id'] : 0;

// Fetch teacher data
$User = new User($pdo);
$teacher = $User->findById($teacher_id);

// Check if teacher exists
if (!$teacher || $teacher['role'] !== 'teacher') {
    redirectTo(BASE_URL . 'public/index.php?page=admin/teachers');
}

include __DIR__ . '/../layouts/admin.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-primary">
            <span class="material-icons-round text-2xl">person_outline</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Sunting Data Guru</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Update teacher information and security</p>
        </div>
    </div>
    <a href="<?= BASE_URL ?>public/index.php?page=admin/teachers" class="flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 rounded-lg text-slate-600 dark:text-slate-300 text-sm font-medium transition-all shadow-sm hover:shadow decoration-0">
        <span class="material-icons-round text-lg">arrow_back</span>
        Kembali
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Main Content: Forms -->
    <div class="lg:col-span-2 space-y-6">
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 p-4 rounded-lg flex items-center gap-3">
                <span class="material-icons-round">check_circle</span>
                <?= $_SESSION['success'] ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 p-4 rounded-lg flex items-center gap-3">
                <span class="material-icons-round">error</span>
                <?= $_SESSION['error'] ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Edit Teacher Information -->
        <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-3 bg-blue-600 flex items-center gap-2 text-white">
                <span class="material-icons-round">badge</span>
                <h3 class="font-semibold">Informasi Guru</h3>
            </div>
            <div class="p-6">
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_teacher" class="space-y-4">
                    <?= csrfInput() ?>
                    <input type="hidden" name="teacher_id" value="<?= $teacher['id'] ?>">
                    <input type="hidden" name="action" value="update_info">

                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap *</label>
                        <input type="text" id="name" name="name" value="<?= h($teacher['name']) ?>" required class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email *</label>
                        <input type="email" id="email" name="email" value="<?= h($teacher['email']) ?>" required class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <span class="material-icons-round text-lg mr-2">save</span>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-3 bg-cyan-600 flex items-center gap-2 text-white">
                <span class="material-icons-round">lock</span>
                <h3 class="font-semibold">Ubah Password (Opsional)</h3>
            </div>
            <div class="p-6">
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_teacher" class="space-y-4">
                    <?= csrfInput() ?>
                    <input type="hidden" name="teacher_id" value="<?= $teacher['id'] ?>">
                    <input type="hidden" name="action" value="update_password">

                    <div>
                        <label for="new_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password Baru *</label>
                        <input type="password" id="new_password" name="new_password" required class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Minimal 6 karakter</p>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Konfirmasi Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" required class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition-colors">
                            <span class="material-icons-round text-lg mr-2">key</span>
                            Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-3 bg-rose-600 flex items-center gap-2 text-white">
                <span class="material-icons-round">warning</span>
                <h3 class="font-semibold">Zona Berbahaya</h3>
            </div>
            <div class="p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        <p class="font-medium text-rose-600 dark:text-rose-400 mb-1">
                             Menghapus guru ini akan memutuskan hubungan guru ke semua kelas.
                        </p>
                        <p>Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <button onclick="document.getElementById('deleteModal').classList.remove('hidden')" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-medium shadow-sm transition-colors flex items-center justify-center gap-2 whitespace-nowrap">
                        <span class="material-icons-round text-lg">delete_forever</span>
                        Hapus Guru
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar: Teacher Summary -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden sticky top-24">
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                <h2 class="font-semibold text-slate-900 dark:text-white">Ringkasan Guru</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-center py-4">
                    <div class="w-24 h-24 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-400 font-bold text-3xl uppercase">
                        <?= strtoupper(substr($teacher['name'], 0, 1)) ?>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                        <p class="text-slate-900 dark:text-white font-medium"><?= h($teacher['name']) ?></p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Email</label>
                        <p class="text-slate-900 dark:text-white break-all"><?= h($teacher['email']) ?></p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Peran</label>
                        <p><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-300 capitalize">
                            <?= h($teacher['role']) ?>
                        </span></p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tgl. Dibuat</label>
                        <p class="text-slate-900 dark:text-white"><?= date('d M Y H:i', strtotime($teacher['created_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
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
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-slate-900 dark:text-white" id="modal-title">
                            Hapus Guru
                        </h3>
                        <div class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                            <p class="mb-2">Apakah Anda yakin ingin menghapus <strong><?= h($teacher['name']) ?></strong>?</p>
                            <p class="mb-4">Tindakan ini tidak dapat dibatalkan. Hubungan guru ke kelas akan diputuskan.</p>
                            
                            <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-400 p-4 mb-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <span class="material-icons-round text-amber-400 text-lg">warning</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-xs text-amber-700 dark:text-amber-300">
                                            Ketik nama guru <strong><?= h($teacher['name']) ?></strong> untuk konfirmasi.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <input type="text" id="confirmName" class="block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-slate-700 dark:text-white sm:text-sm" placeholder="Ketik nama guru di sini...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=delete_teacher" class="w-full sm:w-auto sm:ml-3">
                    <?= csrfInput() ?>
                    <input type="hidden" name="teacher_id" value="<?= $teacher['id'] ?>">
                    <button type="submit" id="confirmDeleteBtn" disabled class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Hapus Guru
                    </button>
                </form>
                <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-700 text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('confirmName').addEventListener('input', function() {
    const teacherName = <?= json_encode($teacher['name']) ?>;
    const isMatch = this.value.trim() === teacherName;
    document.getElementById('confirmDeleteBtn').disabled = !isMatch;
});
</script>

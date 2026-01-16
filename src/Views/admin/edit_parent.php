<?php
// src/Views/admin/edit_parent.php
global $pdo;
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Models/User.php';
require_once __DIR__ . '/../../Models/Child.php';
require_once __DIR__ . '/../../Helpers/functions.php';

// Get parent_id from URL parameter
$parent_id = isset($_GET['parent_id']) ? (int) $_GET['parent_id'] : 0;

// Fetch parent data
$User = new User($pdo);
$parent = $User->findById($parent_id);

// Check if parent exists
if (!$parent || $parent['role'] !== 'parent') {
    redirect('admin/parents');
}

include __DIR__ . '/../layouts/admin.php';

// Fetch children for this parent
$childModel = new Child($pdo);
$children = $childModel->getByParent($parent_id);
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div
            class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-teal-600 dark:text-teal-400">
            <span class="material-icons-round text-2xl">person_remove</span>
            <!-- Icon changed slightly to distinguish edit -->
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Sunting Wali Siswa</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola data wali siswa dan anak-anaknya</p>
        </div>
    </div>

    <a href="<?= BASE_URL ?>public/index.php?page=admin/parents"
        class="flex items-center justify-center px-4 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm">
        <span class="material-icons-round text-lg mr-2">arrow_back</span>
        Kembali
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Left Column: Forms -->
    <div class="lg:col-span-2 space-y-8">

        <!-- Parent Info Card -->
        <div
            class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div
                class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-teal-50/50 dark:bg-teal-900/10 flex items-center gap-2">
                <span class="material-icons-round text-teal-600 dark:text-teal-400">badge</span>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Informasi Wali Siswa</h3>
            </div>
            <div class="p-6">
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_parent">
                    <?= csrfInput() ?>
                    <input type="hidden" name="parent_id" value="<?= $parent['id'] ?>">
                    <input type="hidden" name="action" value="update_info">

                    <div class="space-y-4">
                        <div>
                            <label for="name"
                                class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap
                                <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="<?= h($parent['name']) ?>" required
                                class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="phone"
                                class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">No. HP <span
                                    class="text-red-500">*</span></label>
                            <input type="tel" id="phone" name="phone" value="<?= h($parent['phone'] ?? '') ?>" required
                                class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                            class="flex items-center justify-center px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-medium hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors shadow-sm">
                            <span class="material-icons-round text-lg mr-2">save</span>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password Card -->
        <div
            class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div
                class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-sky-50/50 dark:bg-sky-900/10 flex items-center gap-2">
                <span class="material-icons-round text-sky-600 dark:text-sky-400">lock_reset</span>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Ubah Password</h3>
            </div>
            <div class="p-6">
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_parent">
                    <?= csrfInput() ?>
                    <input type="hidden" name="parent_id" value="<?= $parent['id'] ?>">
                    <input type="hidden" name="action" value="update_password">

                    <div class="space-y-4">
                        <div>
                            <label for="new_password"
                                class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password Baru
                                <span class="text-red-500">*</span></label>
                            <input type="password" id="new_password" name="new_password" required minlength="6"
                                class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Minimal 6 karakter.</p>
                        </div>
                        <div>
                            <label for="confirm_password"
                                class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Konfirmasi
                                Password <span class="text-red-500">*</span></label>
                            <input type="password" id="confirm_password" name="confirm_password" required
                                class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                            class="flex items-center justify-center px-4 py-2 bg-sky-600 text-white rounded-lg text-sm font-medium hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-colors shadow-sm">
                            <span class="material-icons-round text-lg mr-2">key</span>
                            Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Children Management Card -->
        <div
            class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div
                class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50 dark:bg-slate-800/50">
                <div class="flex items-center gap-2">
                    <span class="material-icons-round text-slate-500 dark:text-slate-400">child_care</span>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Daftar Anak</h3>
                </div>
                <button onclick="openModal('addChildModal')"
                    class="flex items-center justify-center px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm">
                    <span class="material-icons-round text-sm mr-1.5">add</span>
                    Tambah Anak
                </button>
            </div>

            <div class="p-0">
                <?php if (empty($children)): ?>
                    <div class="p-8 text-center text-slate-500 dark:text-slate-400">
                        <span
                            class="material-icons-round text-4xl mb-2 text-slate-300 dark:text-slate-600">family_restroom</span>
                        <p>Belum ada anak yang terdaftar untuk wali ini.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                            <thead class="bg-slate-50 dark:bg-slate-800/80">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                        Nama</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                        Tgl Lahir</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                        Kelas</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                <?php foreach ($children as $child): ?>
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                                            <?= h($child['name']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                            <?= $child['date_of_birth'] ? date('d M Y', strtotime($child['date_of_birth'])) : '-' ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                            <?= $child['class_name'] ? h($child['class_name']) : '<span class="text-orange-500 text-xs italic">Belum ada kelas</span>' ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <button
                                                    onclick="openEditChildModal('<?= $child['id'] ?>', '<?= h($child['name']) ?>', '<?= $child['date_of_birth'] ?>')"
                                                    class="text-amber-500 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 p-1 rounded hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors">
                                                    <span class="material-icons-round text-lg">edit</span>
                                                </button>
                                                <button
                                                    onclick="openDeleteChildModal('<?= $child['id'] ?>', '<?= h($child['name']) ?>')"
                                                    class="text-rose-500 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 p-1 rounded hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors">
                                                    <span class="material-icons-round text-lg">delete</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Danger Zone -->
        <div
            class="bg-red-50 dark:bg-red-900/10 rounded-xl shadow-sm border border-red-200 dark:border-red-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-red-200 dark:border-red-800 flex items-center gap-2">
                <span class="material-icons-round text-red-600 dark:text-red-400">warning</span>
                <h3 class="text-lg font-semibold text-red-700 dark:text-red-400">Zona Bahaya</h3>
            </div>
            <div class="p-6">
                <p class="text-sm text-red-600 dark:text-red-300 mb-4">
                    Menghapus wali siswa ini akan menghapus semua data anak dan data riwayat perkembangan anak yang
                    tersimpan secara permanen. Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="flex justify-start">
                    <button onclick="openModal('deleteParentModal')"
                        class="flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors shadow-sm">
                        <span class="material-icons-round text-lg mr-2">delete_forever</span>
                        Hapus Wali Siswa
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Right Column: Summary -->
    <div class="lg:col-span-1">
        <div
            class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 sticky top-6">
            <div class="text-center mb-6">
                <div
                    class="w-20 h-20 bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-3">
                    <?= substr($parent['name'], 0, 1) ?>
                </div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white"><?= h($parent['name']) ?></h2>
                <div
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-900/40 dark:text-teal-300 mt-2">
                    <?= ucfirst($parent['role']) ?>
                </div>
            </div>

            <div class="space-y-4 border-t border-slate-100 dark:border-slate-700 pt-4 text-sm">
                <div>
                    <span class="block text-slate-500 dark:text-slate-400">No. HP</span>
                    <span
                        class="block font-medium text-slate-900 dark:text-white mt-1 break-all"><?= h($parent['phone'] ?? '-') ?></span>
                </div>
                <div>
                    <span class="block text-slate-500 dark:text-slate-400">Bergabung Sejak</span>
                    <span
                        class="block font-medium text-slate-900 dark:text-white mt-1"><?= date('d M Y', strtotime($parent['created_at'])) ?></span>
                </div>
                <div>
                    <span class="block text-slate-500 dark:text-slate-400">Jumlah Anak</span>
                    <span class="block font-medium text-slate-900 dark:text-white mt-1"><?= count($children) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Child Modal -->
<div id="addChildModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
        onclick="closeModal('addChildModal')"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative transform overflow-hidden rounded-xl bg-white dark:bg-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700">
                <div
                    class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center">
                    <h3 class="text-lg font-medium leading-6 text-slate-900 dark:text-white">Tambah Anak</h3>
                    <button type="button" onclick="closeModal('addChildModal')"
                        class="text-slate-400 hover:text-slate-500">
                        <span class="material-icons-round">close</span>
                    </button>
                </div>
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_parent">
                    <?= csrfInput() ?>
                    <input type="hidden" name="parent_id" value="<?= $parent['id'] ?>">
                    <input type="hidden" name="action" value="add_child">
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Anak
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="child_name" required
                                class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal
                                Lahir</label>
                            <input type="date" name="child_dob"
                                class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex flex-row-reverse gap-3">
                        <button type="submit"
                            class="inline-flex justify-center rounded-lg border border-transparent bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">Simpan</button>
                        <button type="button" onclick="closeModal('addChildModal')"
                            class="inline-flex justify-center rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Child Modal -->
<div id="editChildModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
        onclick="closeModal('editChildModal')"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative transform overflow-hidden rounded-xl bg-white dark:bg-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700">
                <div
                    class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-amber-50 dark:bg-amber-900/20 flex justify-between items-center">
                    <h3 class="text-lg font-medium leading-6 text-slate-900 dark:text-white">Sunting Data Anak</h3>
                    <button type="button" onclick="closeModal('editChildModal')"
                        class="text-slate-400 hover:text-slate-500">
                        <span class="material-icons-round">close</span>
                    </button>
                </div>
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_parent">
                    <?= csrfInput() ?>
                    <input type="hidden" name="parent_id" value="<?= $parent['id'] ?>">
                    <input type="hidden" name="action" value="update_child">
                    <input type="hidden" name="child_id" id="editChildId">
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Anak
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="child_name" id="editChildName" required
                                class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal
                                Lahir</label>
                            <input type="date" name="child_dob" id="editChildDob"
                                class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex flex-row-reverse gap-3">
                        <button type="submit"
                            class="inline-flex justify-center rounded-lg border border-transparent bg-amber-500 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">Perbarui</button>
                        <button type="button" onclick="closeModal('editChildModal')"
                            class="inline-flex justify-center rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Child Modal -->
<div id="deleteChildModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
        onclick="closeModal('deleteChildModal')"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative transform overflow-hidden rounded-xl bg-white dark:bg-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-700">
                <div class="px-6 py-4 flex items-center justify-center flex-col text-center">
                    <div
                        class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mb-4 text-red-600 dark:text-red-400">
                        <span class="material-icons-round text-2xl">delete</span>
                    </div>
                    <h3 class="text-lg font-medium leading-6 text-slate-900 dark:text-white mb-2">Hapus Data Anak</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Apakah Anda yakin ingin menghapus <strong id="deleteChildName"
                            class="text-slate-900 dark:text-white"></strong>? Data yang dihapus tidak dapat
                        dikembalikan.
                    </p>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-center gap-3">
                    <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_parent">
                        <?= csrfInput() ?>
                        <input type="hidden" name="parent_id" value="<?= $parent['id'] ?>">
                        <input type="hidden" name="action" value="delete_child">
                        <input type="hidden" name="child_id" id="deleteChildId">
                        <button type="submit"
                            class="inline-flex justify-center rounded-lg border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Ya,
                            Hapus</button>
                    </form>
                    <button type="button" onclick="closeModal('deleteChildModal')"
                        class="inline-flex justify-center rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Parent Modal -->
<div id="deleteParentModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
        onclick="closeModal('deleteParentModal')"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative transform overflow-hidden rounded-xl bg-white dark:bg-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700">
                <div
                    class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-red-50 dark:bg-red-900/20 flex justify-between items-center">
                    <h3 class="text-lg font-medium leading-6 text-red-700 dark:text-red-400">Hapus Wali Siswa</h3>
                    <button type="button" onclick="closeModal('deleteParentModal')"
                        class="text-slate-400 hover:text-slate-500">
                        <span class="material-icons-round">close</span>
                    </button>
                </div>
                <div class="px-6 py-4">
                    <div
                        class="bg-red-50 dark:bg-red-900/30 p-3 rounded-lg flex gap-3 mb-4 border border-red-100 dark:border-red-900">
                        <span class="material-icons-round text-red-600 dark:text-red-400 shrink-0">warning</span>
                        <p class="text-sm text-red-700 dark:text-red-300">
                            Penghapusan ini bersifat permanen. Ketik nama wali
                            <strong><?= h($parent['name']) ?></strong> untuk mengonfirmasi.
                        </p>
                    </div>

                    <input type="text" id="confirmParentName" placeholder="Ketik nama wali..."
                        class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex flex-row-reverse gap-3">
                    <form method="POST" action="<?= BASE_URL ?>public/index.php?page=delete_parent">
                        <?= csrfInput() ?>
                        <input type="hidden" name="parent_id" value="<?= $parent['id'] ?>">
                        <button type="submit" id="deleteParentBtn" disabled
                            class="inline-flex justify-center rounded-lg border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">Hapus
                            Wali</button>
                    </form>
                    <button type="button" onclick="closeModal('deleteParentModal')"
                        class="inline-flex justify-center rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function openEditChildModal(id, name, dob) {
        document.getElementById('editChildId').value = id;
        document.getElementById('editChildName').value = name;
        document.getElementById('editChildDob').value = dob;
        openModal('editChildModal');
    }

    function openDeleteChildModal(id, name) {
        document.getElementById('deleteChildId').value = id;
        document.getElementById('deleteChildName').textContent = name;
        openModal('deleteChildModal');
    }

    // Delete Parent Confirmation logic
    document.getElementById('confirmParentName').addEventListener('input', function () {
        const parentName = <?= json_encode($parent['name']) ?>;
        const btn = document.getElementById('deleteParentBtn');
        if (this.value.trim() === parentName) {
            btn.disabled = false;
        } else {
            btn.disabled = true;
        }
    });
</script>
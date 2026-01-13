<?php
// src/Views/admin/edit_school.php
$pageTitle = 'Edit School';
include __DIR__ . '/../layouts/admin.php';

require_once __DIR__ . '/../../Controllers/SystemAdminController.php';

$id = $_GET['id'] ?? 0;
$controller = new SystemAdminController($pdo);
$school = $controller->getSchool($id);
$admins = $controller->getSchoolAdmins($id);

if (!$school) {
    echo "<div class='p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800' role='alert'>School not found.</div>";
    exit;
}
?>

<div class="max-w-4xl mx-auto" x-data="{ activeTab: 'details' }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-teal-600 dark:text-teal-400">
                <span class="material-icons-round text-2xl">domain</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Sekolah</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400"><?= h($school['name']) ?></p>
            </div>
        </div>
        
        <a href="index.php?page=admin/schools" class="flex items-center justify-center px-4 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm">
            <span class="material-icons-round text-lg mr-2">arrow_back</span>
            Kembali
        </a>
    </div>

    <div class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <!-- Tabs Header -->
        <div class="border-b border-slate-200 dark:border-slate-700">
            <nav class="flex -mb-px" aria-label="Tabs">
                <button @click="activeTab = 'details'" 
                        :class="{ 'border-teal-500 text-teal-600 dark:text-teal-400': activeTab === 'details', 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300': activeTab !== 'details' }"
                        class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm flex items-center justify-center gap-2">
                    <span class="material-icons-round text-base">info</span>
                    Detail Sekolah
                </button>
                <button @click="activeTab = 'admins'" 
                        :class="{ 'border-teal-500 text-teal-600 dark:text-teal-400': activeTab === 'admins', 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300': activeTab !== 'admins' }"
                        class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm flex items-center justify-center gap-2">
                    <span class="material-icons-round text-base">admin_panel_settings</span>
                    Admin Sekolah
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- Details Tab -->
            <div x-show="activeTab === 'details'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <form action="index.php?page=admin/update_school" method="POST" class="space-y-6">
                    <?= csrfInput() ?>
                    <input type="hidden" name="id" value="<?= $school['id'] ?>">

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Sekolah <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="<?= h($school['name']) ?>"
                               class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Alamat <span class="text-slate-400 text-xs font-normal ml-1">(Opsional)</span></label>
                        <textarea name="address" rows="3"
                                  class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"><?= h($school['address']) ?></textarea>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-700">
                        <button type="submit" class="inline-flex justify-center rounded-lg border border-transparent bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                            <span class="material-icons-round text-lg mr-2">save</span>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Admins Tab -->
            <div x-show="activeTab === 'admins'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="mb-4 bg-sky-50 dark:bg-sky-900/20 border border-sky-100 dark:border-sky-800 rounded-lg p-4 flex items-start gap-3">
                    <span class="material-icons-round text-sky-600 dark:text-sky-400 mt-0.5">info</span>
                    <p class="text-sm text-sky-700 dark:text-sky-300">
                        Ini adalah daftar administrator yang memiliki akses penuh untuk mengelola data sekolah ini.
                    </p>
                </div>

                <div class="overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-800/80">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">No. HP</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Bergabung</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-card-dark">
                            <?php if (empty($admins)): ?>
                                <tr><td colspan="4" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">Tidak ada admin ditemukan.</td></tr>
                            <?php else: ?>
                                <?php foreach ($admins as $admin): ?>
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                                            <?= h($admin['name']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                            <?= h($admin['phone']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                            <?= date('d M Y', strtotime($admin['created_at'])) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <a href="index.php?page=admin/edit_school_admin&id=<?= $admin['id'] ?>" class="text-amber-500 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 p-1 rounded hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors" title="Edit Admin">
                                                    <span class="material-icons-round text-lg">edit_note</span>
                                                </a>
                                                <a href="tel:<?= h($admin['phone']) ?>" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors" title="Hubungi">
                                                    <span class="material-icons-round text-lg">phone</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

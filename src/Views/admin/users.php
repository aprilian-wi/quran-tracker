<?php
// src/Views/admin/users.php
global $pdo;
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$selectedRole = $_GET['role'] ?? '';
$controller = new AdminController($pdo);
$users = $controller->users($selectedRole);

include __DIR__ . '/../layouts/admin.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div
            class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-primary">
            <span class="material-icons-round text-2xl">group</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Semua Pengguna</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage user access and roles</p>
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
        <input type="hidden" name="page" value="admin/users" />
        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
            <div class="relative min-w-[200px]">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <span class="material-icons-round text-lg">filter_alt</span>
                </span>
                <select name="role"
                    class="pl-10 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm py-2.5 shadow-sm transition-shadow">
                    <option value="" <?= $selectedRole === '' ? 'selected' : '' ?>>Semua Peran</option>
                    <option value="superadmin" <?= $selectedRole === 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
                    <option value="school_admin" <?= $selectedRole === 'school_admin' ? 'selected' : '' ?>>Admin Sekolah
                    </option>
                    <option value="teacher" <?= $selectedRole === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                    <option value="parent" <?= $selectedRole === 'parent' ? 'selected' : '' ?>>Parent</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <span class="material-icons-round text-sm mr-2">search</span>
                    Filter
                </button>
                <a href="?page=admin/users"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2.5 border border-slate-300 dark:border-slate-600 text-sm font-medium rounded-lg text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors decoration-0">
                    Reset
                </a>
            </div>
        </div>
        <div class="flex w-full lg:w-auto">
            <a href="?page=admin/export_users&role=<?= urlencode($selectedRole) ?>"
                class="w-full lg:w-auto inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors decoration-0">
                <span class="material-icons-round text-sm mr-2">file_download</span>
                Ekspor
            </a>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-800/80">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-20"
                        scope="col">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                        scope="col">Nama</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                        scope="col">No. HP</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                        scope="col">Peran</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                        scope="col">Bergabung</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24"
                        scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-card-dark divide-y divide-slate-200 dark:divide-slate-700">
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                            Tidak ada pengguna ditemukan.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($users as $user): ?>
                    <?php
                    // Determine colors based on role
                    $roleColors = [
                        'teacher' => ['bg' => 'bg-blue-100 dark:bg-blue-900/50', 'text' => 'text-blue-600 dark:text-blue-400', 'badge_bg' => 'bg-blue-100 dark:bg-blue-900/40', 'badge_text' => 'text-blue-800 dark:text-blue-300'],
                        'parent' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/50', 'text' => 'text-emerald-600 dark:text-emerald-400', 'badge_bg' => 'bg-emerald-100 dark:bg-emerald-900/40', 'badge_text' => 'text-emerald-800 dark:text-emerald-300'],
                        'school_admin' => ['bg' => 'bg-amber-100 dark:bg-amber-900/50', 'text' => 'text-amber-600 dark:text-amber-400', 'badge_bg' => 'bg-amber-100 dark:bg-amber-900/40', 'badge_text' => 'text-amber-800 dark:text-amber-300'],
                        'superadmin' => ['bg' => 'bg-rose-100 dark:bg-rose-900/50', 'text' => 'text-rose-600 dark:text-rose-400', 'badge_bg' => 'bg-rose-100 dark:bg-rose-900/40', 'badge_text' => 'text-rose-800 dark:text-rose-300'],
                    ];
                    // Default to teacher colors if unknown
                    $colors = $roleColors[$user['role']] ?? $roleColors['teacher'];
                    $roleLabel = ucfirst(str_replace('_', ' ', $user['role']));
                    ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400 font-mono">
                            #<?= $user['id'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 h-9 w-9 <?= $colors['bg'] ?> rounded-full flex items-center justify-center <?= $colors['text'] ?> font-bold text-sm mr-3">
                                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                </div>
                                <div class="text-sm font-medium text-slate-900 dark:text-white"><?= h($user['name']) ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                            <?= h($user['phone'] ?? '-') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $colors['badge_bg'] ?> <?= $colors['badge_text'] ?>">
                                <?= $roleLabel ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                            <div class="flex items-center gap-1">
                                <span class="material-icons-round text-sm text-slate-400">calendar_today</span>
                                <?= date('d M Y', strtotime($user['created_at'])) ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <?php if ($user['role'] !== 'superadmin' && $user['role'] !== 'school_admin'): ?>
                                <button onclick="confirmDelete(<?= $user['id'] ?>, 'pengguna')"
                                    class="text-rose-500 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 p-2 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors opacity-70 group-hover:opacity-100">
                                    <span class="material-icons-round text-lg">delete</span>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div
        class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
        <p class="text-sm text-slate-500 dark:text-slate-400">Showing <span
                class="font-medium"><?= count($users) ?></span> users</p>
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
        if (confirm(`Hapus ${type} ini? Tindakan ini tidak dapat dibatalkan.`)) {
            window.location.href = `?page=delete_user&id=${id}`;
        }
    }
</script>
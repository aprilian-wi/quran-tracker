<?php
// src/Views/teacher/update_progress_hadiths.php
global $pdo;
require_once __DIR__ . '/../../Models/Child.php';
require_once __DIR__ . '/../../Models/Progress.php';
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$child_id = $_GET['child_id'] ?? 0;
$class_id = $_GET['class_id'] ?? 0;

if ($child_id && !is_numeric($child_id)) {
    setFlash('danger', 'Invalid child.');
    redirect('dashboard');
}

if ($class_id && !is_numeric($class_id)) {
    setFlash('danger', 'Invalid class.');
    redirect('dashboard');
}

if (!$child_id && !$class_id) {
    setFlash('danger', 'Child or class ID required.');
    redirect('dashboard');
}

$childModel = new Child($pdo);
$role = $_SESSION['role'] ?? '';

// --- Part 1: Child Selection Logic ---
if ($child_id) {
    $child = $childModel->find($child_id, $_SESSION['user_id'], $role);
    if (!$child) {
        setFlash('danger', 'Access denied or child not found.');
        redirect('dashboard');
    }
    if (!$class_id || !is_numeric($class_id)) {
        $class_id = $child['class_id'] ?? 0;
    }
} elseif ($class_id) {
    // For class-based access, show child selection
    $children = $childModel->getByClass($class_id);
    if (empty($children)) {
        setFlash('danger', 'No children in this class.');
        redirect('admin/classes');
    }
    
    // Show child selection page
    include __DIR__ . '/../layouts/admin.php';
    ?>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-3">
             <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-primary">
                <span class="material-icons-round text-2xl">people</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Pilih Siswa</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Pilih siswa untuk memperbarui hafalan Hadits</p>
            </div>
        </div>
        <a href="?page=admin/classes" class="flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 rounded-lg text-slate-600 dark:text-slate-300 text-sm font-medium transition-all shadow-sm hover:shadow decoration-0">
            <span class="material-icons-round text-lg">arrow_back</span>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($children as $child): ?>
            <div class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 flex flex-col items-center text-center hover:shadow-md transition-shadow">
                <div class="w-16 h-16 rounded-full bg-orange-50 dark:bg-orange-900/40 text-orange-600 dark:text-orange-400 flex items-center justify-center mb-4 text-2xl font-bold uppercase">
                    <?= substr($child['name'], 0, 1) ?>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-1"><?= h($child['name']) ?></h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Wali: <?= h($child['parent_name']) ?></p>
                <a href="?page=teacher/update_progress_hadiths&child_id=<?= $child['id'] ?>" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                    Perbarui Hadits
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    exit;
}

// --- Part 2: Update Progress Form ---

$adminController = new AdminController($pdo);
$hadiths = $adminController->getHadiths();

include __DIR__ . '/../layouts/admin.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-primary">
            <span class="material-icons-round text-2xl">format_quote</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Hafalan Hadits</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Siswa: <strong><?= h($child['name']) ?></strong></p>
        </div>
    </div>
    <a href="<?= BASE_URL ?>public/index.php?page=teacher/class_students&class_id=<?= $class_id ?>" class="flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 rounded-lg text-slate-600 dark:text-slate-300 text-sm font-medium transition-all shadow-sm hover:shadow decoration-0">
        <span class="material-icons-round text-lg">arrow_back</span>
        Kembali
    </a>
</div>

<!-- Update Form -->
<div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Input Hafalan Baru</h3>
    </div>
    <div class="p-6">
        <form method="POST" action="<?= BASE_URL ?>public/index.php?page=update_progress_hadiths" class="space-y-6">
            <?= csrfInput() ?>
            <input type="hidden" name="child_id" value="<?= $child_id ?>">
            <input type="hidden" name="updated_by" value="<?= $_SESSION['user_id'] ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Hadith Selection -->
                <div>
                    <label for="hadith_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pilih Hadits</label>
                    <select name="hadith_id" id="hadith_id" required class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                        <option value="">-- Pilih Hadits --</option>
                        <?php foreach ($hadiths as $hadith): ?>
                            <option value="<?= $hadith['id'] ?>"><?= h($hadith['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status</label>
                    <select name="status" id="status" required class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                        <option value="in_progress">Murajaah</option>
                        <option value="memorized" selected>Menghafal</option>
                    </select>
                </div>
            </div>

            <!-- Note -->
            <div>
                <label for="note" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Catatan (Opsional)</label>
                <textarea name="note" id="note" rows="3" placeholder="Tambahkan catatan untuk progres ini..." class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"></textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end pt-4 border-t border-slate-200 dark:border-slate-700">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                    <span class="material-icons-round text-lg mr-2">save</span>
                    Simpan Progres
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Progress History -->
<?php
$progressModel = new Progress($pdo);
$history = $progressModel->getHadithHistory($child_id);
if ($history):
    $uniqueUpdatedBy = array_unique(array_column($history, 'updated_by_name'));
    sort($uniqueUpdatedBy);
?>
<div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Riwayat Hafalan</h3>

        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Filters -->
             <div class="flex items-center gap-2">
                <label for="statusFilter" class="text-sm font-medium text-slate-600 dark:text-slate-400">Status:</label>
                <select id="statusFilter" class="rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-300 focus:ring-orange-500 focus:border-orange-500">
                    <option value="">Semua</option>
                    <option value="Menghafal">Menghafal</option>
                    <option value="Murajaah">Murajaah</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <label for="updatedByFilter" class="text-sm font-medium text-slate-600 dark:text-slate-400">Oleh:</label>
                <select id="updatedByFilter" class="rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-300 focus:ring-orange-500 focus:border-orange-500 max-w-[150px]">
                    <option value="">Semua</option>
                    <?php foreach ($uniqueUpdatedBy as $name): ?>
                        <option value="<?= h($name) ?>"><?= h($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button id="exportBtn" class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                <span class="material-icons-round text-lg mr-1">download</span>
                Excel
            </button>
        </div>
    </div>

    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-800/80">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Hadits</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Catatan</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Oleh</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-card-dark divide-y divide-slate-200 dark:divide-slate-700">
                <?php foreach ($history as $entry): ?>
                    <?php
                        $statusText = $entry['status'] === 'memorized' ? 'Menghafal' :
                                      ($entry['status'] === 'in_progress' ? 'Murajaah' : ucfirst($entry['status']));
                        
                        $badgeClass = $entry['status'] === 'memorized' 
                            ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' 
                            : 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300';
                    ?>
                    <tr class="history-item hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors" data-status="<?= h($statusText) ?>" data-updated-by="<?= h($entry['updated_by_name']) ?>">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                            <?= date('d M Y H:i', strtotime($entry['updated_at'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white font-medium">
                            <?= h($entry['title']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $badgeClass ?>">
                                <?= $statusText ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 max-w-xs truncate">
                            <?= h($entry['note'] ?? '-') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                            <?= h($entry['updated_by_name']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden">
        <?php foreach ($history as $entry): ?>
            <?php
            $statusText = $entry['status'] === 'memorized' ? 'Menghafal' : ($entry['status'] === 'in_progress' ? 'Murajaah' : ucfirst($entry['status']));
            $badgeClass = $entry['status'] === 'memorized' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800';
            ?>
            <div class="p-4 border-b border-slate-100 dark:border-slate-800 history-item block" data-status="<?= h($statusText) ?>" data-updated-by="<?= h($entry['updated_by_name']) ?>">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-medium text-slate-900 dark:text-white"><?= h($entry['title']) ?></span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= $badgeClass ?>">
                        <?= $statusText ?>
                    </span>
                </div>
                <?php if (!empty($entry['note'])): ?>
                    <div class="bg-slate-50 dark:bg-slate-800 p-2 rounded text-xs text-slate-600 dark:text-slate-400 italic mb-2">
                        <span class="material-icons-round text-xs align-middle mr-1">sticky_note_2</span> 
                        <?= h($entry['note']) ?>
                    </div>
                <?php endif; ?>
                <div class="flex justify-between items-center text-xs text-slate-400">
                    <span class="flex items-center gap-1"><span class="material-icons-round text-xs">person</span> <?= h($entry['updated_by_name']) ?></span>
                    <span><?= date('d M Y H:i', strtotime($entry['updated_at'])) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
const statusFilter = document.getElementById('statusFilter');
const updatedByFilter = document.getElementById('updatedByFilter');

function filterHistory() {
    const selectedStatus = statusFilter.value;
    const selectedUpdatedBy = updatedByFilter.value;
    const items = document.querySelectorAll('.history-item');

    items.forEach(item => {
        const itemStatus = item.getAttribute('data-status');
        const itemUpdatedBy = item.getAttribute('data-updated-by');
        const statusMatch = selectedStatus === '' || itemStatus === selectedStatus;
        const updatedByMatch = selectedUpdatedBy === '' || itemUpdatedBy === selectedUpdatedBy;

        if (statusMatch && updatedByMatch) {
            if (item.tagName === 'TR') {
                item.style.display = 'table-row';
            } else {
                item.style.display = 'block';
            }
        } else {
            item.style.display = 'none';
        }
    });
}

statusFilter.addEventListener('change', filterHistory);
updatedByFilter.addEventListener('change', filterHistory);

document.getElementById('exportBtn').addEventListener('click', function() {
    const statusVal = statusFilter.value;
    const updatedByVal = updatedByFilter.value;
    let url = `?page=export_hadith_progress_excel&child_id=<?= $child_id ?>`;
    if (statusVal) url += `&status=${encodeURIComponent(statusVal)}`;
    if (updatedByVal) url += `&updated_by=${encodeURIComponent(updatedByVal)}`;
    window.location.href = url;
});
</script>
<?php endif; ?>

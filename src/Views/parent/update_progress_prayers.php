<?php
// src/Views/parent/update_progress_prayers.php
global $pdo;
require_once __DIR__ . '/../../Models/Child.php';
require_once __DIR__ . '/../../Models/Progress.php';
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$child_id = $_GET['child_id'] ?? 0;

if ($child_id && !is_numeric($child_id)) {
    setFlash('danger', 'Invalid child.');
    redirect('dashboard');
}

if (!$child_id) {
    setFlash('danger', 'Child ID required.');
    redirect('dashboard');
}

$childModel = new Child($pdo);
$role = $_SESSION['role'] ?? '';

$child = $childModel->find($child_id, $_SESSION['user_id'], $role);
if (!$child) {
    setFlash('danger', 'Access denied or child not found.');
    redirect('dashboard');
}

$adminController = new AdminController($pdo);
$prayers = $adminController->getShortPrayers();

// PWA Logic
if (isPwa() || (isset($_GET['mode']) && $_GET['mode'] === 'pwa')) {
    include __DIR__ . '/../layouts/pwa.php';
    include __DIR__ . '/update_progress_prayers_pwa.php';
    return;
}

// Layout Decision
if (isLoggedIn()) {
    include __DIR__ . '/../layouts/admin.php';
} else {
    include __DIR__ . '/../layouts/main.php';
}
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-teal-600 dark:text-teal-400">
            <span class="material-icons-round text-2xl">volunteer_activism</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Update Hafalan Doa</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Pembaruan progres doa harian untuk <strong><?= h($child['name']) ?></strong></p>
        </div>
    </div>
    
    <a href="<?= BASE_URL ?>public/index.php?page=dashboard" class="flex items-center justify-center px-4 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm">
        <span class="material-icons-round text-lg mr-2">arrow_back</span>
        Kembali
    </a>
</div>

<!-- Update Form -->
<div class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 mb-8">
    <form method="POST" action="<?= BASE_URL ?>public/index.php?page=update_progress_prayers">
        <?= csrfInput() ?>
        <input type="hidden" name="child_id" value="<?= $child_id ?>">
        <input type="hidden" name="updated_by" value="<?= $_SESSION['user_id'] ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Prayer Selection -->
            <div>
                <label for="prayer_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Pilih Doa</label>
                <select name="prayer_id" id="prayer_id" class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-teal-500 focus:border-teal-500 shadow-sm" required>
                    <option value="">Pilih doa...</option>
                    <?php foreach ($prayers as $prayer): ?>
                        <option value="<?= $prayer['id'] ?>"><?= h($prayer['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Status Selection -->
            <div>
                <label for="status" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status</label>
                <select name="status" id="status" class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-teal-500 focus:border-teal-500 shadow-sm" required>
                    <option value="memorized" selected>Menghafal</option>
                    <option value="in_progress">Murajaah</option>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Catatan (Opsional)</label>
            <textarea name="note" id="note" class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-teal-500 focus:border-teal-500 shadow-sm" rows="3" placeholder="Tambahkan catatan..."></textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors">
                <span class="material-icons-round text-lg mr-2">check_circle</span>
                Simpan Progres
            </button>
        </div>
    </form>
</div>

<!-- Progress History -->
<?php
$progressModel = new Progress($pdo);
$history = $progressModel->getPrayerHistory($child_id);

if ($history):
    // Collect unique updated_by_names for filter
    $uniqueUpdatedBy = array_unique(array_column($history, 'updated_by_name'));
    sort($uniqueUpdatedBy);
?>
<div class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <!-- Filters Header -->
    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <span class="material-icons-round text-slate-400">history</span>
            Riwayat Doa
        </h3>
        
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex items-center gap-2">
                <label for="statusFilter" class="text-sm font-medium text-slate-700 dark:text-slate-300 whitespace-nowrap">Status:</label>
                <select id="statusFilter" class="block w-full sm:w-auto rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-300 focus:ring-teal-500 focus:border-teal-500 py-1.5 pl-3 pr-8">
                    <option value="">Semua</option>
                    <option value="Menghafal">Menghafal</option>
                    <option value="Murajaah">Murajaah</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <label for="updatedByFilter" class="text-sm font-medium text-slate-700 dark:text-slate-300 whitespace-nowrap">Oleh:</label>
                <select id="updatedByFilter" class="block w-full sm:w-auto rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-300 focus:ring-teal-500 focus:border-teal-500 py-1.5 pl-3 pr-8">
                    <option value="">Semua</option>
                    <?php foreach ($uniqueUpdatedBy as $name): ?>
                        <option value="<?= h($name) ?>"><?= h($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Desktop Table -->
    <div class="overflow-x-auto hidden md:block">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Doa</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Catatan</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pengupdate</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-card-dark divide-y divide-slate-200 dark:divide-slate-700">
                <?php foreach ($history as $entry): ?>
                    <?php
                    $statusText = $entry['status'] === 'memorized' ? 'Menghafal' :
                                  ($entry['status'] === 'in_progress' ? 'Murajaah' : ucfirst($entry['status']));
                    
                    $badgeClass = $entry['status'] === 'memorized' 
                        ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400'
                        : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400';
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
                         <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 max-w-xs truncate" title="<?= h($entry['note'] ?? '') ?>">
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

    <!-- Mobile Card List -->
    <div class="md:hidden divide-y divide-slate-200 dark:divide-slate-700">
         <?php foreach ($history as $entry): ?>
            <?php
            $statusText = $entry['status'] === 'memorized' ? 'Menghafal' :
                          ($entry['status'] === 'in_progress' ? 'Murajaah' : ucfirst($entry['status']));
            
            $badgeClass = $entry['status'] === 'memorized' 
                ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400'
                : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400';
            ?>
            <div class="history-item p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors" data-status="<?= h($statusText) ?>" data-updated-by="<?= h($entry['updated_by_name']) ?>">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white"><?= h($entry['title']) ?></h4>
                    </div>
                     <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $badgeClass ?>">
                        <?= $statusText ?>
                    </span>
                </div>
                
                <?php if (!empty($entry['note'])): ?>
                    <div class="bg-slate-50 dark:bg-slate-800 rounded p-2 mb-3 text-xs text-slate-600 dark:text-slate-400 italic border border-slate-100 dark:border-slate-700">
                        <?= h($entry['note']) ?>
                    </div>
                <?php endif; ?>

                 <div class="flex justify-between items-center text-xs text-slate-400">
                    <span class="flex items-center gap-1">
                        <span class="material-icons-round text-sm">person</span>
                        <?= h($entry['updated_by_name']) ?>
                    </span>
                     <span><?= date('d M Y H:i', strtotime($entry['updated_at'])) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
// Filter Logic
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
            item.classList.remove('hidden');
            if (item.tagName === 'TR') item.style.display = ''; 
        } else {
            item.classList.add('hidden'); 
             item.style.display = 'none';
        }
    });
}

if (statusFilter && updatedByFilter) {
    statusFilter.addEventListener('change', filterHistory);
    updatedByFilter.addEventListener('change', filterHistory);
}
</script>

<?php else: ?>
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 flex items-start gap-4 mt-8">
        <span class="material-icons-round text-blue-500 text-2xl mt-1">history</span>
        <div>
             <h3 class="font-medium text-blue-800 dark:text-blue-300 mb-1">Belum ada riwayat</h3>
            <p class="text-sm text-blue-600 dark:text-blue-400">
                Belum ada data doa yang tercatat untuk anak ini.
            </p>
        </div>
    </div>
<?php endif; ?>

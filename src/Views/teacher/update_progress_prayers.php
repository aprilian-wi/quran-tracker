<?php
// src/Views/teacher/update_progress_prayers.php
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
    $children = $childModel->getByClass($class_id);
    if (empty($children)) {
        setFlash('danger', 'No children in this class.');
        redirect('admin/classes');
    }
    include __DIR__ . '/../layouts/main.php';
    ?>
    <h3><i class="bi bi-people"></i> Select Child to Update Progress for Short Prayers</h3>
    <div class="row">
        <?php foreach ($children as $child): ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= h($child['name']) ?></h5>
                        <p class="card-text">Parent: <?= h($child['parent_name']) ?></p>
                        <a href="?page=teacher/update_progress_prayers&child_id=<?= $child['id'] ?>" class="btn btn-primary">
                            Update Progress
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="mt-3">
        <a href="?page=admin/classes" class="btn btn-secondary">Back to Classes</a>
    </div>
    <?php
    exit;
}

$adminController = new AdminController($pdo);
$prayers = $adminController->getShortPrayers();

include __DIR__ . '/../layouts/main.php';
?>

<h3><i class="bi bi-journal-text"></i> Hafalan Doa Pendek <?= h($child['name']) ?></h3>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>public/index.php?page=update_progress_prayers">
            <?= csrfInput() ?>
            <input type="hidden" name="child_id" value="<?= $child_id ?>">
            <input type="hidden" name="updated_by" value="<?= $_SESSION['user_id'] ?>">

            <div class="mb-3">
                <label for="prayer_id" class="form-label">Select Short Prayer</label>
                <select name="prayer_id" id="prayer_id" class="form-select" required>
                    <option value="">Select a prayer</option>
                    <?php foreach ($prayers as $prayer): ?>
                        <option value="<?= $prayer['id'] ?>"><?= h($prayer['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="in_progress">Murajaah</option>
                    <option value="memorized" selected>Menghafal</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="note" class="form-label">Note (Optional)</label>
                <textarea name="note" id="note" class="form-control" rows="3" placeholder="Add any additional notes about this progress update..."></textarea>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="bi bi-check2"></i> Save Progress
            </button>
            <a href="<?= BASE_URL ?>public/index.php?page=teacher/class_students&class_id=<?= $class_id ?>" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>

<?php
$progressModel = new Progress($pdo);
$history = $progressModel->getPrayerHistory($child_id);
if ($history):
    $uniqueUpdatedBy = array_unique(array_column($history, 'updated_by_name'));
    sort($uniqueUpdatedBy);
?>
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Riwayat Hafalan Doa</h5>
        <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
            <div class="d-flex align-items-center gap-2">
                <label for="statusFilter" class="form-label mb-0">Status:</label>
                <select id="statusFilter" class="form-select form-select-sm" style="width: auto;">
                    <option value="">Semua</option>
                    <option value="Menghafal">Menghafal</option>
                    <option value="Murajaah">Murajaah</option>
                </select>
            </div>
            <div class="d-flex align-items-center gap-2">
                <label for="updatedByFilter" class="form-label mb-0">Oleh:</label>
                <select id="updatedByFilter" class="form-select form-select-sm" style="max-width: 150px;">
                    <option value="">Semua</option>
                    <?php foreach ($uniqueUpdatedBy as $name): ?>
                        <option value="<?= h($name) ?>"><?= h($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="ms-md-auto col-12 col-md-auto mt-2 mt-md-0">
                <button id="exportBtn" class="btn btn-success btn-sm w-100 w-md-auto">
                    <i class="bi bi-download"></i> Download Excel
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <!-- Desktop Table View -->
        <div class="table-responsive d-none d-md-block">
            <table id="progressHistoryTable" class="table table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Prayer</th>
                        <th>Status</th>
                        <th>Note</th>
                        <th>Updated By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $entry): ?>
                        <?php
                            $statusText = $entry['status'] === 'memorized' ? 'Menghafal' :
                                          ($entry['status'] === 'in_progress' ? 'Murajaah' : ucfirst($entry['status']));
                        ?>
                        <tr class="history-item" data-status="<?= h($statusText) ?>" data-updated-by="<?= h($entry['updated_by_name']) ?>">
                            <td><?= date('M j, Y g:i A', strtotime($entry['updated_at'])) ?></td>
                            <td><?= h($entry['title']) ?></td>
                            <td>
                                <span class="badge bg-<?= $entry['status'] === 'memorized' ? 'success' : 'warning' ?>">
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td><?= h($entry['note'] ?? '') ?></td>
                            <td><?= h($entry['updated_by_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="d-md-none">
            <?php foreach ($history as $entry): ?>
                <?php
                $statusText = $entry['status'] === 'memorized' ? 'Menghafal' :
                              ($entry['status'] === 'in_progress' ? 'Murajaah' : ucfirst($entry['status']));
                $badgeClass = $entry['status'] === 'memorized' ? 'success' : 'warning';
                ?>
                <div class="card-body border-bottom history-item" data-status="<?= h($statusText) ?>" data-updated-by="<?= h($entry['updated_by_name']) ?>">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong><?= h($entry['title']) ?></strong>
                        <span class="badge bg-<?= $badgeClass ?>"><?= $statusText ?></span>
                    </div>
                    <?php if (!empty($entry['note'])): ?>
                        <div class="alert alert-light p-2 mb-2 small text-muted fst-italic">
                            <i class="bi bi-sticky"></i> <?= h($entry['note']) ?>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between align-items-center text-muted small">
                        <span><i class="bi bi-person"></i> <?= h($entry['updated_by_name']) ?></span>
                        <span><?= date('M j, Y g:i A', strtotime($entry['updated_at'])) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
const statusFilter = document.getElementById('statusFilter');
const updatedByFilter = document.getElementById('updatedByFilter');

function filterHistory() {
    const selectedStatus = statusFilter.value;
    const selectedUpdatedBy = updatedByFilter.value;
    // Select both table rows and mobile cards
    const rows = document.querySelectorAll('.history-item');

    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        const rowUpdatedBy = row.getAttribute('data-updated-by');
        const statusMatch = selectedStatus === '' || rowStatus === selectedStatus;
        const updatedByMatch = selectedUpdatedBy === '' || rowUpdatedBy === selectedUpdatedBy;

        if (statusMatch && updatedByMatch) {
            // For table rows, display depends on parent (table-row), but standard is empty to reset
            // For div cards, separate display logic logic or just '' which usually works (block or table-row)
            // Ideally explicit:
            if (row.tagName === 'TR') {
                row.style.display = '';
            } else {
                row.style.display = 'block';
            }
        } else {
            row.style.display = 'none';
        }
    });
}

statusFilter.addEventListener('change', filterHistory);
updatedByFilter.addEventListener('change', filterHistory);

document.getElementById('exportBtn').addEventListener('click', function() {
    const statusVal = statusFilter.value;
    const updatedByVal = updatedByFilter.value;
    let url = `?page=export_prayer_progress_excel&child_id=<?= $child_id ?>`;
    if (statusVal) url += `&status=${encodeURIComponent(statusVal)}`;
    if (updatedByVal) url += `&updated_by=${encodeURIComponent(updatedByVal)}`;
    window.location.href = url;
});
</script>

<?php endif; ?>

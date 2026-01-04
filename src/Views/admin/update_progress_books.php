<?php
// src/Views/admin/update_progress_books.php
global $pdo;
require_once __DIR__ . '/../../Models/Child.php';
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Models/Progress.php';
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
} elseif ($class_id) {
    // For class-based access, show child selection for admin/superadmin
    $children = $childModel->getByClass($class_id);
    if (empty($children)) {
        setFlash('danger', 'No children in this class.');
        redirect('admin/classes');
    }
    include __DIR__ . '/../layouts/main.php';
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-people"></i> Select Child to Update Book Progress</h3>
        <a href="?page=admin/classes" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
    <div class="row">
        <?php foreach ($children as $child): ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= h($child['name']) ?></h5>
                        <p class="card-text">Parent: <?= h($child['parent_name']) ?></p>
                        <a href="?page=admin/update_progress_books&child_id=<?= $child['id'] ?>" class="btn btn-primary">
                            Update Book Progress
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
$books = $adminController->getAllTeachingBooks();

include __DIR__ . '/../layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-book"></i> Update Book Progress for <?= h($child['name']) ?></h3>
    <a href="<?= BASE_URL ?>public/index.php?page=admin/list_children" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>public/index.php?page=update_progress_books">
            <?= csrfInput() ?>
            <input type="hidden" name="child_id" value="<?= $child_id ?>">
            <input type="hidden" name="updated_by" value="<?= $_SESSION['user_id'] ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Teaching Book (Jilid)</label>
                    <select name="book_id" id="book_id" class="form-select" required>
                        <option value="">Select Book</option>
                        <?php foreach ($books as $book): ?>
                            <option value="<?= $book['id'] ?>" data-pages="<?= $book['total_pages'] ?>">
                                Jilid <?= $book['volume_number'] ?> - <?= h($book['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Page</label>
                    <input type="number" name="page" id="page" class="form-control" min="1" required disabled>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="fluent">Lancar</option>
                        <option value="repeating">Mengulang</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-12">
                    <label class="form-label">Note (Optional)</label>
                    <textarea name="note" class="form-control" rows="3" placeholder="Add any additional notes about this progress update..."></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check2"></i> Save Progress
                </button>
                <a href="<?= BASE_URL ?>public/index.php?page=admin/list_children" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>

<script>
// Dynamic page limit based on selected book
document.getElementById('book_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const pageInput = document.getElementById('page');

    if (selectedOption.value) {
        const maxPages = selectedOption.getAttribute('data-pages');
        pageInput.max = maxPages;
        pageInput.placeholder = `1–${maxPages}`;
        pageInput.disabled = false;
    } else {
        pageInput.disabled = true;
        pageInput.value = '';
    }
});
</script>

<!-- Progress History -->
<?php
$progressModel = new Progress($pdo);
$history = $progressModel->getBookHistory($child_id);
if ($history):
    // Collect unique updated_by_names for filter
    $uniqueUpdatedBy = array_unique(array_column($history, 'updated_by_name'));
    sort($uniqueUpdatedBy);
?>
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Book Progress History</h5>
        <div class="mt-3 d-flex gap-2 align-items-center">
            <label for="statusFilter" class="form-label mb-0">Filter by Status:</label>
            <select id="statusFilter" class="form-select" style="max-width: 150px;">
                <option value="">All Status</option>
                <option value="Lancar">Lancar</option>
                <option value="Mengulang">Mengulang</option>
            </select>
            <label for="updatedByFilter" class="form-label mb-0">Filter by Updated By:</label>
            <select id="updatedByFilter" class="form-select" style="max-width: 200px;">
                <option value="">All Users</option>
                <?php foreach ($uniqueUpdatedBy as $name): ?>
                    <option value="<?= h($name) ?>"><?= h($name) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="ms-auto">
                <button id="exportBtn" class="btn btn-success btn-sm">
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
                        <th>Book</th>
                        <th>Page</th>
                        <th>Status</th>
                        <th>Note</th>
                        <th>Updated By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $entry): ?>
                        <?php
                        $statusText = $entry['status'] === 'fluent' ? 'Lancar' :
                                      ($entry['status'] === 'repeating' ? 'Mengulang' : ucfirst($entry['status']));
                        ?>
                        <tr class="history-item" data-status="<?= h($statusText) ?>" data-updated-by="<?= h($entry['updated_by_name']) ?>">
                            <td><?= date('M j, Y g:i A', strtotime($entry['updated_at'])) ?></td>
                            <td>Jilid <?= $entry['volume_number'] ?> - <?= h($entry['title']) ?></td>
                            <td><?= $entry['page'] ?></td>
                            <td>
                                <span class="badge bg-<?= $entry['status'] === 'fluent' ? 'success' :
                                                            ($entry['status'] === 'repeating' ? 'warning' : 'secondary') ?>">
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
                $statusText = $entry['status'] === 'fluent' ? 'Lancar' :
                              ($entry['status'] === 'repeating' ? 'Mengulang' : ucfirst($entry['status']));
                $badgeClass = $entry['status'] === 'fluent' ? 'success' :
                              ($entry['status'] === 'repeating' ? 'warning' : 'secondary');
                ?>
                <div class="card-body border-bottom history-item" data-status="<?= h($statusText) ?>" data-updated-by="<?= h($entry['updated_by_name']) ?>">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong>Jilid <?= $entry['volume_number'] ?> - <?= h($entry['title']) ?></strong>
                            <div class="text-muted small">Halaman: <?= $entry['page'] ?></div>
                        </div>
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
// Progress History filters
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

// Export functionality
document.getElementById('exportBtn').addEventListener('click', function() {
    const statusVal = statusFilter.value;
    const updatedByVal = updatedByFilter.value;
    let url = `?page=export_progress_excel&child_id=<?= $child_id ?>`;
    if (statusVal) url += `&status=${encodeURIComponent(statusVal)}`;
    if (updatedByVal) url += `&updated_by=${encodeURIComponent(updatedByVal)}`;
    window.location.href = url;
});
</script>
<?php endif; ?>

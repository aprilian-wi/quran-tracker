<?php
// src/Views/admin/manage_short_prayers.php
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new AdminController($pdo);
$prayers = $controller->getShortPrayers();

include __DIR__ . '/../layouts/main.php';
?>

<h3><i class="bi bi-journal-text"></i> Manage Short Prayers (Doa-doa Pendek)</h3>

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="mb-0">Manage short prayers with their Arabic text and translation for progress tracking.</p>
    <a href="?page=admin/create_short_prayer" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Short Prayer
    </a>
</div>

<?php if (empty($prayers)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No short prayers found. Add your first short prayer.
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Title</th>
                            <th>Arabic Text</th>
                            <th>Translation</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prayers as $prayer): ?>
                            <tr>
                                <td><?= h($prayer['title']) ?></td>
                                <td><?= h($prayer['arabic_text']) ?></td>
                                <td><?= h($prayer['translation']) ?></td>
                                <td>
                                    <a href="?page=admin/edit_short_prayer&id=<?= $prayer['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $prayer['id'] ?>, '<?= h($prayer['title']) ?>')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete "<span id="itemTitle"></span>"?</p>
                <p class="text-danger"><strong>Warning:</strong> This will also delete all progress records associated with this short prayer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="?page=admin/delete_short_prayer" style="display: inline;">
                    <?= csrfInput() ?>
                    <input type="hidden" name="id" id="deleteItemId">
                    <button type="submit" class="btn btn-danger">Delete Short Prayer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, title) {
    document.getElementById('deleteItemId').value = id;
    document.getElementById('itemTitle').textContent = title;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>


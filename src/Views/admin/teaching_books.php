<?php
// src/Views/admin/teaching_books.php
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new AdminController($pdo);
$books = $controller->teachingBooks();

include __DIR__ . '/../layouts/main.php';
?>

<h3><i class="bi bi-book"></i> Manage Teaching Books</h3>

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="mb-0">Configure teaching books (Jilid) and their page counts for progress tracking.</p>
    <a href="?page=admin/create_teaching_book" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Book
    </a>
</div>

<?php if (empty($books)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No teaching books configured yet. Add your first book to start tracking progress.
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Volume Number</th>
                            <th>Title</th>
                            <th>Total Pages</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><strong>Jilid <?= $book['volume_number'] ?></strong></td>
                                <td><?= h($book['title']) ?></td>
                                <td><?= $book['total_pages'] ?> pages</td>
                                <td><?= date('M j, Y', strtotime($book['created_at'])) ?></td>
                                <td>
                                    <a href="?page=admin/edit_teaching_book&id=<?= $book['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDelete(<?= $book['id'] ?>, '<?= h($book['title']) ?>')">
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
                <p>Are you sure you want to delete "<span id="bookTitle"></span>"?</p>
                <p class="text-danger"><strong>Warning:</strong> This will also delete all progress records associated with this book.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="?page=admin/delete_teaching_book" style="display: inline;">
                    <?= csrfInput() ?>
                    <input type="hidden" name="id" id="deleteBookId">
                    <button type="submit" class="btn btn-danger">Delete Book</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(bookId, bookTitle) {
    document.getElementById('deleteBookId').value = bookId;
    document.getElementById('bookTitle').textContent = bookTitle;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

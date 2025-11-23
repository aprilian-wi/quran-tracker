<?php
// src/Views/admin/edit_teaching_book.php
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new AdminController($pdo);
$book_id = $_GET['id'] ?? 0;

if (!$book_id || !is_numeric($book_id)) {
    setFlash('danger', 'Invalid book ID.');
    redirect('admin/teaching_books');
}

$book = $controller->getTeachingBook($book_id);
if (!$book) {
    setFlash('danger', 'Book not found.');
    redirect('admin/teaching_books');
}

include __DIR__ . '/../layouts/main.php';
?>

<h3><i class="bi bi-pencil"></i> Edit Teaching Book</h3>

<div class="card">
    <div class="card-body">
        <form method="POST" action="?page=admin/update_teaching_book">
            <?= csrfInput() ?>
            <input type="hidden" name="id" value="<?= $book['id'] ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Volume Number (Jilid)</label>
                    <input type="number" name="volume_number" class="form-control" min="1" value="<?= $book['volume_number'] ?>" required>
                    <div class="form-text">The volume number of the book (e.g., 1, 2, 3, etc.)</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Total Pages</label>
                    <input type="number" name="total_pages" class="form-control" min="1" value="<?= $book['total_pages'] ?>" required>
                    <div class="form-text">Total number of pages in this volume</div>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-12">
                    <label class="form-label">Book Title</label>
                    <input type="text" name="title" class="form-control" value="<?= h($book['title']) ?>" required>
                    <div class="form-text">Descriptive title for the teaching book</div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check2"></i> Update Book
                </button>
                <a href="?page=admin/teaching_books" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Books
                </a>
            </div>
        </form>
    </div>
</div>

<?php
// src/Views/admin/create_teaching_book.php
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new AdminController($pdo);

include __DIR__ . '/../layouts/main.php';
?>

<h3><i class="bi bi-plus-circle"></i> Add New Teaching Book</h3>

<div class="card">
    <div class="card-body">
        <form method="POST" action="?page=admin/store_teaching_book">
            <?= csrfInput() ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Volume Number (Jilid)</label>
                    <input type="number" name="volume_number" class="form-control" min="1" required>
                    <div class="form-text">The volume number of the book (e.g., 1, 2, 3, etc.)</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Total Pages</label>
                    <input type="number" name="total_pages" class="form-control" min="1" required>
                    <div class="form-text">Total number of pages in this volume</div>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-12">
                    <label class="form-label">Book Title</label>
                    <input type="text" name="title" class="form-control" required>
                    <div class="form-text">Descriptive title for the teaching book</div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check2"></i> Create Book
                </button>
                <a href="?page=admin/teaching_books" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Books
                </a>
            </div>
        </form>
    </div>
</div>

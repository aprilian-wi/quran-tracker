<?php
// src/Actions/delete_teaching_book_action.php
global $pdo;
require_once __DIR__ . '/../Helpers/functions.php';
require_once __DIR__ . '/../Controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlash('danger', 'Invalid request.');
    redirect('admin/teaching_books');
}

if (!validateCsrf($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid security token.');
    redirect('admin/teaching_books');
}

$id = (int)($_POST['id'] ?? 0);

if (!$id) {
    setFlash('danger', 'Invalid book ID.');
    redirect('admin/teaching_books');
}

$controller = new AdminController($pdo);
$book = $controller->getTeachingBook($id);

if (!$book) {
    setFlash('danger', 'Book not found.');
    redirect('admin/teaching_books');
}

$success = $controller->deleteTeachingBook($id);

if ($success) {
    setFlash('success', "Teaching book 'Jilid {$book['volume_number']}: {$book['title']}' deleted successfully.");
} else {
    setFlash('danger', 'Failed to delete teaching book.');
}

redirect('admin/teaching_books');

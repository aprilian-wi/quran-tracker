<?php
// src/Actions/update_progress_books_action.php
global $pdo;
require_once __DIR__ . '/../Helpers/functions.php';
require_once __DIR__ . '/../Models/Progress.php';
require_once __DIR__ . '/../Models/Child.php';
require_once __DIR__ . '/../Controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlash('danger', 'Invalid request.');
    redirect('dashboard');
}

if (!validateCsrf($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid security token.');
    redirect('dashboard');
}

$child_id = (int)($_POST['child_id'] ?? 0);
$book_id = (int)($_POST['book_id'] ?? 0);
$page = (int)($_POST['page'] ?? 0);
$status = $_POST['status'] ?? '';
$note = trim($_POST['note'] ?? '');
$updated_by = (int)($_POST['updated_by'] ?? 0);

if (!$child_id || !$book_id || !$page || !in_array($status, ['in_progress', 'memorized', 'fluent', 'repeating']) || $updated_by !== $_SESSION['user_id']) {
    setFlash('danger', 'Invalid data.');
    redirect('dashboard');
}

// Verify child access
$childModel = new Child($pdo);
$child = $childModel->find($child_id, $_SESSION['user_id'], $_SESSION['role']);
if (!$child) {
    setFlash('danger', 'Access denied.');
    redirect('dashboard');
}

// Verify book exists and validate page
$adminController = new AdminController($pdo);
$book = $adminController->getTeachingBook($book_id);
if (!$book) {
    setFlash('danger', 'Book not found.');
    redirect('dashboard');
}

if ($page < 1 || $page > $book['total_pages']) {
    setFlash('danger', "Page must be between 1 and {$book['total_pages']}.");
    redirect('dashboard');
}

$progressModel = new Progress($pdo);
$success = $progressModel->updateBookProgress($child_id, $book_id, $page, $status, $updated_by, $note);

if ($success) {
    setFlash('success', "Book progress updated for {$child['name']} - Jilid {$book['volume_number']} Page {$page}.");
    if ($_SESSION['role'] === 'parent') {
        $redirectPage = 'page=parent/update_progress_books&child_id=' . $child_id;
    } else {
        $redirectPage = 'page=teacher/update_progress_books&child_id=' . $child_id;
    }
} else {
    setFlash('danger', 'Failed to update book progress.');
    if ($_SESSION['role'] === 'parent') {
        $redirectPage = 'page=parent/update_progress_books&child_id=' . $child_id;
    } else {
        $redirectPage = 'page=teacher/update_progress_books&child_id=' . $child_id;
    }
}
redirect($redirectPage);

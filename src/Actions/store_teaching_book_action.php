<?php
// src/Actions/store_teaching_book_action.php
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

$volume_number = (int)($_POST['volume_number'] ?? 0);
$title = trim($_POST['title'] ?? '');
$total_pages = (int)($_POST['total_pages'] ?? 0);

if ($volume_number < 1 || empty($title) || $total_pages < 1) {
    setFlash('danger', 'Invalid data provided.');
    redirect('admin/create_teaching_book');
}

$controller = new AdminController($pdo);
$success = $controller->createTeachingBook($volume_number, $title, $total_pages);

if ($success) {
    setFlash('success', "Teaching book 'Jilid {$volume_number}: {$title}' created successfully.");
} else {
    setFlash('danger', 'Failed to create teaching book.');
}

redirect('admin/teaching_books');

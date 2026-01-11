<?php
// src/Actions/delete_progress_books_action.php
global $pdo;
require_once __DIR__ . '/../Helpers/functions.php';
require_once __DIR__ . '/../Models/Progress.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlash('danger', 'Invalid request.');
    redirect('dashboard');
}

if (!validateCsrf($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid security token.');
    redirect('dashboard');
}

// Role check - strictly teacher only as requested
if (($_SESSION['role'] ?? '') !== 'teacher') {
    setFlash('danger', 'Access denied.');
    redirect('dashboard');
}

$id = (int) ($_POST['progress_id'] ?? 0);
$child_id = (int) ($_POST['child_id'] ?? 0);

if (!$id || !$child_id) {
    setFlash('danger', 'Invalid data.');
    redirect('dashboard');
}

$progressModel = new Progress($pdo);

if ($progressModel->deleteBookProgress($id)) {
    setFlash('success', 'Riwayat berhasil dihapus.');
} else {
    setFlash('danger', 'Gagal menghapus riwayat.');
}

$redirectUrl = 'page=teacher/update_progress_books&child_id=' . $child_id . (isset($_POST['class_id']) && $_POST['class_id'] ? '&class_id=' . $_POST['class_id'] : '');
if (isset($_POST['mode']) && $_POST['mode'] === 'pwa') {
    $redirectUrl .= '&mode=pwa';
}

redirect($redirectUrl);

<?php
// src/Actions/store_hadith_action.php
require_once __DIR__ . '/../Controllers/AdminController.php';

if (!(hasRole('superadmin') || hasRole('school_admin'))) {
    setFlash('danger', 'Access denied.');
    redirect('admin/manage_hadiths');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/manage_hadiths');
    exit;
}

$controller = new AdminController($pdo);

// Validate CSRF token
if (!validateCsrf($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid CSRF token.');
    redirect('admin/manage_hadiths');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$arabic_text = trim($_POST['arabic_text'] ?? '');
$translation = trim($_POST['translation'] ?? '');

if (empty($title) || empty($arabic_text)) {
    setFlash('danger', 'Title and Arabic text are required.');
    redirect($id ? "admin/edit_hadith&id=$id" : 'admin/create_hadith');
    exit;
}

if ($id) {
    // Update existing hadith
    $success = $controller->updateHadith($id, $title, $arabic_text, $translation);
    $message = $success ? 'Hadith updated successfully.' : 'Failed to update hadith.';
} else {
    // Create new hadith
    $success = $controller->createHadith($title, $arabic_text, $translation);
    $message = $success ? 'Hadith created successfully.' : 'Failed to create hadith.';
}

setFlash($success ? 'success' : 'danger', $message);
redirect('admin/manage_hadiths');
exit;

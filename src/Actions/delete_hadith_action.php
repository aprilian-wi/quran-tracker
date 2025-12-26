<?php
// src/Actions/delete_hadith_action.php
require_once __DIR__ . '/../Controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/manage_hadiths');
    exit;
}

$controller = new AdminController($pdo);

// Check if user is superadmin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    setFlash('danger', 'Access denied: Superadmin only');
    redirect('admin/manage_hadiths');
    exit;
}

// Validate CSRF token
if (!validateCsrf($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid CSRF token.');
    redirect('admin/manage_hadiths');
    exit;
}

$id = $_POST['id'] ?? null;

if (!$id) {
    setFlash('danger', 'Invalid hadith ID.');
    redirect('admin/manage_hadiths');
    exit;
}

$hadith = $controller->getHadith($id);
if (!$hadith) {
    setFlash('danger', 'Hadith not found.');
    redirect('admin/manage_hadiths');
    exit;
}

$success = $controller->deleteHadith($id);

if ($success) {
    setFlash('success', 'Hadith deleted successfully.');
} else {
    setFlash('danger', 'Failed to delete hadith.');
}

redirect('admin/manage_hadiths');
exit;

<?php
// src/Actions/store_short_prayer_action.php
require_once __DIR__ . '/../Controllers/AdminController.php';
require_once __DIR__ . '/../Helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/manage_short_prayers');
    exit;
}

if (!hasRole('superadmin')) {
    setFlash('danger', 'Access denied: Superadmin only');
    redirect('admin/manage_short_prayers');
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!validateCsrf($csrf_token)) {
    setFlash('danger', 'Invalid CSRF token.');
    redirect('admin/manage_short_prayers');
    exit;
}

$pdo = require __DIR__ . '/../../config/database.php';
$adminController = new AdminController($pdo);

$id = $_POST['id'] ?? null;
$title = trim($_POST['title'] ?? '');
$arabic_text = trim($_POST['arabic_text'] ?? '');
$translation = trim($_POST['translation'] ?? '');

if ($title === '' || $arabic_text === '') {
    setFlash('danger', 'Title and Arabic Text are required.');
    redirect($id ? "admin/manage_short_prayers&edit_id=$id" : 'admin/manage_short_prayers');
    exit;
}

if ($id) {
    // Update existing
    if ($adminController->updateShortPrayer($id, $title, $arabic_text, $translation)) {
        setFlash('success', 'Short prayer updated successfully.');
    } else {
        setFlash('danger', 'Failed to update short prayer.');
    }
} else {
    // Create new
    if ($adminController->createShortPrayer($title, $arabic_text, $translation)) {
        setFlash('success', 'Short prayer added successfully.');
    } else {
        setFlash('danger', 'Failed to add short prayer.');
    }
}

redirect('admin/manage_short_prayers');
exit;

<?php
// src/Actions/update_short_prayer_action.php
require_once __DIR__ . '/../Controllers/AdminController.php';
require_once __DIR__ . '/../Helpers/functions.php';

requireLogin();
if (!hasRole('school_admin') && !hasRole('superadmin')) {
    setFlash('danger', 'Access denied.');
    redirect('dashboard');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRFToken();

    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title']);
    $arabic_text = trim($_POST['arabic_text']);
    $translation = trim($_POST['translation']);

    if (!$id) {
        setFlash('danger', 'Invalid Prayer ID.');
        redirect('admin/manage_short_prayers');
    }

    if (empty($title) || empty($arabic_text)) {
        setFlash('danger', 'Title and Arabic Text are required.');
        redirect('admin/edit_short_prayer', ['id' => $id]);
    }

    $controller = new AdminController($pdo);
    
    if ($controller->updateShortPrayer($id, $title, $arabic_text, $translation)) {
        setFlash('success', 'Short prayer updated successfully.');
        redirect('admin/manage_short_prayers');
    } else {
        setFlash('danger', 'Failed to update short prayer.');
        redirect('admin/edit_short_prayer', ['id' => $id]);
    }
} else {
    redirect('admin/manage_short_prayers');
}

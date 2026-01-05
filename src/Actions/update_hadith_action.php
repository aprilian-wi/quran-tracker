<?php
// src/Actions/update_hadith_action.php
require_once __DIR__ . '/../Controllers/AdminController.php';
require_once __DIR__ . '/../Helpers/functions.php';

requireLogin();
if (!hasRole('school_admin') && !hasRole('superadmin')) {
    setFlash('danger', 'Access denied.');
    redirect('dashboard');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check CSRF
    checkCSRFToken();

    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title']);
    $arabic_text = trim($_POST['arabic_text']);
    $translation = trim($_POST['translation']);

    if (!$id) {
        setFlash('danger', 'Invalid Hadith ID.');
        redirect('admin/manage_hadiths');
    }

    if (empty($title) || empty($arabic_text)) {
        setFlash('danger', 'Title and Arabic Text are required.');
        // Correctly redirect with page parameter
        redirect('admin/edit_hadith', ['id' => $id]);
    }

    $controller = new AdminController($pdo);
    
    // Debugging: Log payload if needed (cannot do here, but ensuring logic matches controller)
    if ($controller->updateHadith($id, $title, $arabic_text, $translation)) {
        setFlash('success', 'Hadith updated successfully.');
        redirect('admin/manage_hadiths');
    } else {
        setFlash('danger', 'Failed to update hadith. Please try again.');
        redirect('admin/edit_hadith', ['id' => $id]);
    }
} else {
    redirect('admin/manage_hadiths');
}

<?php
// src/Actions/promote_class_action.php
global $pdo;
require_once __DIR__ . '/../Helpers/functions.php';
require_once __DIR__ . '/../Models/Child.php';

if (!(hasRole('superadmin') || hasRole('school_admin'))) {
    setFlash('danger', 'Access denied.');
    redirect('dashboard');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlash('danger', 'Invalid request.');
    redirect('admin/classes');
}

if (!validateCsrf($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid security token.');
    redirect('admin/promote_class');
}

$source_class_id = (int)($_POST['source_class_id'] ?? 0);
$target_class_id = (int)($_POST['target_class_id'] ?? 0);
$child_ids = $_POST['child_ids'] ?? [];

if ((!$source_class_id && $source_class_id !== -1) || !$target_class_id) {
    setFlash('danger', 'Please select both source and target classes.');
    redirect('admin/promote_class');
}

if ($source_class_id === $target_class_id) {
    setFlash('warning', 'Source and target classes cannot be the same.');
    redirect('admin/promote_class');
}

if (empty($child_ids)) {
    setFlash('warning', 'No students selected for promotion.');
    redirect('admin/promote_class');
}

$childModel = new Child($pdo);
$count = 0;
$errors = 0;

foreach ($child_ids as $id) {
    // Basic verification: Ensure child is actually in source class (optional but safer)
    // For speed, we just update.
    if ($childModel->assignToClass((int)$id, $target_class_id)) {
        $count++;
    } else {
        $errors++;
    }
}

if ($count > 0) {
    setFlash('success', "Successfully promoted/moved $count students.");
}

if ($errors > 0) {
    setFlash('warning', "$errors students failed to move.");
}

redirect('admin/classes');

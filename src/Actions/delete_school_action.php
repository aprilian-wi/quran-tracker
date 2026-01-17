<?php
// src/Actions/delete_school_action.php

checkCSRFToken();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/schools');
}

$id = $_POST['id'] ?? 0;

if ($id == 1) {
    setFlash('danger', 'Cannot delete the Main System School.');
    redirect('admin/schools');
}

require_once __DIR__ . '/../Controllers/SystemAdminController.php';
$controller = new SystemAdminController($pdo);

// 1. Delete Media Files
$uploadDir = __DIR__ . '/../../public/uploads/schools/' . $id;
if (is_dir($uploadDir)) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($uploadDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileinfo) {
        $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        @$todo($fileinfo->getRealPath());
    }
    @rmdir($uploadDir);
}

// Delete School (Cascading deletes users, content, etc defined in DB)
$result = $controller->deleteSchool($id);

if ($result) {
    setFlash('success', 'School deleted successfully.');
} else {
    setFlash('danger', 'Failed to delete school.');
}

redirect('admin/schools');

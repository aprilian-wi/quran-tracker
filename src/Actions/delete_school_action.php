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

// Delete School (Cascading deletes users, content, etc defined in DB)
$result = $controller->deleteSchool($id);

if ($result) {
    setFlash('success', 'School deleted successfully.');
} else {
    setFlash('danger', 'Failed to delete school.');
}

redirect('admin/schools');

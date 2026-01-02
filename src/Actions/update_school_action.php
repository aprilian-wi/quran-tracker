<?php
// src/Actions/update_school_action.php

checkCSRFToken();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/schools');
}

$id = $_POST['id'] ?? 0;
$name = trim($_POST['name'] ?? '');
$address = trim($_POST['address'] ?? '');

if (empty($id) || empty($name)) {
    setFlash('danger', 'School Name is required.');
    redirect('admin/edit_school', ['id' => $id]);
}

require_once __DIR__ . '/../Controllers/SystemAdminController.php';
$controller = new SystemAdminController($pdo);
$result = $controller->updateSchool($id, $name, $address);

if ($result) {
    setFlash('success', 'School updated successfully.');
    redirect('admin/schools');
} else {
    setFlash('danger', 'Failed to update school.');
    redirect('admin/edit_school', ['id' => $id]);
}

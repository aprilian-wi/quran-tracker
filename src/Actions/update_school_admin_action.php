<?php
// src/Actions/update_school_admin_action.php

checkCSRFToken();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/schools');
}

$id = $_POST['id'] ?? 0;
$schoolId = $_POST['school_id'] ?? 0;
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($id) || empty($name) || empty($email)) {
    setFlash('danger', 'Name and Email are required.');
    redirect('admin/edit_school_admin', ['id' => $id]);
}

require_once __DIR__ . '/../Controllers/SystemAdminController.php';
$controller = new SystemAdminController($pdo);
$result = $controller->updateSchoolAdmin($id, $name, $email, $password);

if ($result) {
    setFlash('success', 'School Admin updated successfully.');
    redirect('admin/edit_school', ['id' => $schoolId]);
} else {
    setFlash('danger', 'Failed to update admin.');
    redirect('admin/edit_school_admin', ['id' => $id]);
}

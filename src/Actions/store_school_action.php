<?php
// src/Actions/store_school_action.php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/create_school');
}

if (!validateCsrf($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid CSRF token.');
    redirect('admin/create_school');
}

$schoolName = trim($_POST['school_name'] ?? '');
$adminName = trim($_POST['admin_name'] ?? '');
$adminPhone = trim($_POST['admin_phone'] ?? '');
$adminPass = $_POST['admin_password'] ?? '';

if (empty($schoolName) || empty($adminName) || empty($adminPhone) || empty($adminPass)) {
    setFlash('danger', 'All fields are required.');
    redirect('admin/create_school');
}

require_once __DIR__ . '/../Controllers/SystemAdminController.php';
$controller = new SystemAdminController($pdo);
$result = $controller->createSchool($schoolName, $adminName, $adminPhone, $adminPass);

if ($result['success']) {
    setFlash('success', $result['message']);
    redirect('admin/schools');
} else {
    setFlash('danger', 'Error: ' . $result['message']);
    redirect('admin/create_school');
}

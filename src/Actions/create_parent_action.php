<?php
// src/Actions/create_parent_action.php
global $pdo;
require_once __DIR__ . '/../Helpers/functions.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Child.php';

if (!(hasRole('superadmin') || hasRole('school_admin'))) {
    setFlash('danger', 'Access denied.');
    redirect('dashboard');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlash('danger', 'Invalid request.');
    redirect('admin/parents');
}

if (!validateCsrf($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid security token.');
    redirect('admin/parents');
}

$name = trim($_POST['name'] ?? '');
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? ''); // Changed email to phone
$password = $_POST['password'] ?? '';

if (!$name || !$phone || !$password) {
    setFlash('danger', 'All fields are required.');
    redirect('admin/parents');
}

// Basic phone validation (optional, can be improved)
if (!preg_match('/^[0-9+]+$/', $phone)) {
    setFlash('danger', 'Nomor HP tidak valid.');
    redirect('admin/parents');
}

if (strlen($password) < 6) {
    setFlash('danger', 'Password must be at least 6 characters.');
    redirect('admin/parents');
}

$userModel = new User($pdo);

// Check phone exists
if ($userModel->findByPhone($phone)) {
    setFlash('danger', 'No. HP sudah terdaftar.');
    redirect('admin/parents');
}

// Create parent
$parentCreated = $userModel->create([
    'name' => $name,
    'phone' => $phone,
    'password' => $password,
    'role' => 'parent',
    'school_id' => $_SESSION['school_id'] ?? 1
]);

if (!$parentCreated) {
    setFlash('danger', 'Failed to create parent.');
    redirect('admin/parents');
}

setFlash('success', 'Parent created successfully. Add children from the edit parent page.');
redirect('admin/parents');
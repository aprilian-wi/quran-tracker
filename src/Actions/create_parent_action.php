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
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$name || !$email || !$password) {
    setFlash('danger', 'All fields are required.');
    redirect('admin/parents');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlash('danger', 'Invalid email.');
    redirect('admin/parents');
}

if (strlen($password) < 6) {
    setFlash('danger', 'Password must be at least 6 characters.');
    redirect('admin/parents');
}

$userModel = new User($pdo);

// Check email exists
if ($userModel->findByEmail($email)) {
    setFlash('danger', 'Email already in use.');
    redirect('admin/parents');
}

// Create parent
$parentCreated = $userModel->create([
    'name' => $name,
    'email' => $email,
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
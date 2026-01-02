<?php
// src/Actions/create_teacher_action.php
global $pdo;
require_once __DIR__ . '/../Helpers/functions.php';
require_once __DIR__ . '/../Models/User.php';

if (!(hasRole('superadmin') || hasRole('school_admin'))) {
    setFlash('danger', 'Access denied.');
    redirect('dashboard');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlash('danger', 'Invalid request.');
    redirect('admin/users');
}

if (!validateCsrf($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid security token.');
    redirect('admin/users');
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$name || !$email || !$password) {
    setFlash('danger', 'All fields are required.');
    redirect('admin/users');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlash('danger', 'Invalid email.');
    redirect('admin/users');
}

if (strlen($password) < 6) {
    setFlash('danger', 'Password must be at least 6 characters.');
    redirect('admin/users');
}

$userModel = new User($pdo);

if ($userModel->findByEmail($email)) {
    setFlash('danger', 'Email already in use.');
    redirect('admin/users');
}

$created = $userModel->create([
    'name' => $name,
    'email' => $email,
    'password' => $password,
    'role' => 'teacher',
    'school_id' => $_SESSION['school_id'] ?? 1
]);

if ($created) {
    setFlash('success', "Guru '{$name}' berhasil ditambahkan.");
} else {
    setFlash('danger', 'Failed to create teacher.');
}

redirect('admin/teachers');
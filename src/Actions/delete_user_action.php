<?php
// src/Actions/delete_user_action.php
global $pdo;
require_once __DIR__ . '/../Helpers/functions.php';
require_once __DIR__ . '/../Models/User.php';

if (!(hasRole('superadmin') || hasRole('school_admin'))) {
    setFlash('danger', 'Access denied.');
    redirect('dashboard');
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    setFlash('danger', 'Invalid request.');
    redirect('admin/users');
}

$userModel = new User($pdo);
$user = $userModel->findById($id);
if (!$user) {
    setFlash('danger', 'User not found.');
    redirect('admin/users');
}

if ($user['role'] === 'superadmin') {
    setFlash('danger', 'Cannot delete superadmin.');
    redirect('admin/users');
}

// Security: Verify school ownership
if ($user['school_id'] != ($_SESSION['school_id'] ?? 1)) {
    setFlash('danger', 'Unauthorized access to this user.');
    redirect('admin/users');
}

$deleted = $userModel->delete($id);
if ($deleted) {
    setFlash('success', 'User deleted.');
} else {
    setFlash('danger', 'Failed to delete user.');
}

redirect('admin/users');

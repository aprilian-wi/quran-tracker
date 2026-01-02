<?php
// src/Actions/assign_class_action.php
global $pdo;
require_once __DIR__ . '/../Helpers/functions.php';
require_once __DIR__ . '/../Models/Class.php';

// Allow teacher, school_admin and superadmin to perform assignment
if (!(hasRole('teacher') || hasRole('superadmin') || hasRole('school_admin'))) {
    setFlash('danger', 'Access denied.');
    redirect('dashboard');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlash('danger', 'Invalid request.');
    redirect('dashboard');
}

if (!validateCsrf($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid security token.');
    redirect('dashboard');
}

$child_id = (int)($_POST['child_id'] ?? 0);
$class_id = (int)($_POST['class_id'] ?? 0);

if (!$child_id || !$class_id) {
    setFlash('danger', 'Invalid data.');
    redirect('dashboard');
}

// Verify permission
$classModel = new ClassModel($pdo);
$class = $classModel->find($class_id);

if (!$class) {
    setFlash('danger', 'Class not found.');
    redirect('dashboard');
}

$canAssign = false;
if (hasRole('superadmin')) {
    $canAssign = true;
} elseif (hasRole('school_admin')) {
    // School admin can assign if class is in their school
    $canAssign = ($class['school_id'] == ($_SESSION['school_id'] ?? 0));
} elseif (hasRole('teacher')) {
    // Teacher must own the class
    $canAssign = $classModel->isOwnedBy($class_id, $_SESSION['user_id']);
}

if (!$canAssign) {
    setFlash('danger', 'You do not have permission to assign to this class.');
    redirect('dashboard');
}

$assigned = $classModel->assignChild($child_id, $class_id);

if ($assigned) {
    setFlash('success', 'Student assigned to class.');
} else {
    setFlash('danger', 'Failed to assign student.');
}

// Gunakan parameter agar tidak terjadi encoding yang salah pada url
redirect('teacher/class_students', ['class_id' => $class_id]);
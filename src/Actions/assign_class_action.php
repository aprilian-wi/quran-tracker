<?php
// src/Actions/assign_class_action.php
global $pdo;
require_once __DIR__ . '/../Helpers/functions.php';
require_once __DIR__ . '/../Models/Class.php';

// Allow teacher and superadmin to perform assignment
if (!(hasRole('teacher') || hasRole('superadmin'))) {
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

// Verify teacher owns the class
$classModel = new ClassModel($pdo);
// Verify teacher owns the class
if (!$classModel->isOwnedBy($class_id, $_SESSION['user_id'])) {
    setFlash('danger', 'You do not own this class.');
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
<?php
// src/Actions/create_class_action.php
global $pdo;
require_once __DIR__ . '/../Helpers/functions.php';
require_once __DIR__ . '/../Models/Class.php';

if (!(hasRole('superadmin') || hasRole('school_admin'))) {
    setFlash('danger', 'Access denied.');
    redirect('dashboard');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlash('danger', 'Invalid request.');
    redirect('admin/classes');
}

if (!validateCsrf($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid security token.');
    redirect('admin/classes');
}


$name = trim($_POST['name'] ?? '');
$teacher_ids = $_POST['teacher_ids'] ?? [];
$teacher_ids = array_filter(array_map('intval', (array)$teacher_ids));
$first_teacher = count($teacher_ids) ? $teacher_ids[0] : null;

if (!$name) {
    setFlash('danger', 'Class name is required.');
    redirect('admin/classes');
}


$classModel = new ClassModel($pdo);
$school_id = $_SESSION['school_id'] ?? 1;
$class_id = $classModel->create($name, $first_teacher, $school_id);

// Assign additional teachers (skip the first teacher if already assigned by create())
if ($class_id && !empty($teacher_ids)) {
    foreach ($teacher_ids as $t) {
        // Skip if this is the first teacher (already assigned in create())
        if ($t !== $first_teacher) {
            $classModel->assignTeacher($class_id, $t);
        }
    }
}

if ($class_id) {
    setFlash('success', 'Class created.');
} else {
    setFlash('danger', 'Failed to create class.');
}

redirect('admin/classes');

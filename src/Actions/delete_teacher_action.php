<?php
// src/Actions/delete_teacher_action.php
global $pdo;

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Class.php';
require_once __DIR__ . '/../Helpers/functions.php';

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    $_SESSION['error'] = 'Invalid CSRF token';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/teachers');
    exit;
}

// Check authorization - only superadmin
// Authorization
if (!(hasRole('superadmin') || hasRole('school_admin'))) {
    $_SESSION['error'] = 'Unauthorized access';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/teachers');
    exit;
}

$teacher_id = isset($_POST['teacher_id']) ? (int)$_POST['teacher_id'] : 0;

if ($teacher_id <= 0) {
    $_SESSION['error'] = 'Invalid teacher ID';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/teachers');
    exit;
}

$User = new User($pdo);
$teacher = $User->findById($teacher_id);

// Verify teacher exists and is actually a teacher
if (!$teacher || $teacher['role'] !== 'teacher') {
    $_SESSION['error'] = 'Teacher not found';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/teachers');
    exit;
}

// Security: Verify school ownership
if ($teacher['school_id'] != ($_SESSION['school_id'] ?? 1)) {
    $_SESSION['error'] = 'Unauthorized access to this teacher';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/teachers');
    exit;
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Remove teacher from all classes
    $stmt = $pdo->prepare("DELETE FROM classes_teachers WHERE teacher_id = ?");
    $stmt->execute([$teacher_id]);

    // Delete the teacher
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'teacher'");
    $stmt->execute([$teacher_id]);

    // Commit transaction
    $pdo->commit();

    $_SESSION['success'] = 'Teacher deleted successfully';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/teachers');
    exit;

} catch (Exception $e) {
    // Rollback on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['error'] = 'Error deleting teacher: ' . $e->getMessage();
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/teachers');
    exit;
}

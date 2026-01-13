<?php
// src/Actions/edit_teacher_action.php
global $pdo;

require_once __DIR__ . '/../Models/User.php';
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

$action = $_POST['action'] ?? '';
$teacher_id = isset($_POST['teacher_id']) ? (int) $_POST['teacher_id'] : 0;

$User = new User($pdo);
$teacher = $User->findById($teacher_id);

// Verify teacher exists
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
    if ($action === 'update_info') {
        // Update teacher name and phone
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        // Validation
        if (empty($name)) {
            throw new Exception('Teacher name is required');
        }
        if (empty($phone)) {
            throw new Exception('Phone number is required');
        }
        // Basic phone validation
        if (!preg_match('/^[0-9+]+$/', $phone)) {
            throw new Exception('Invalid phone format');
        }

        // Check if phone is already taken by another user
        $existingUser = $User->findByPhone($phone);
        if ($existingUser && $existingUser['id'] !== $teacher_id) {
            throw new Exception('Phone number is already in use');
        }

        // Update user
        $User->update($teacher_id, [
            'name' => $name,
            'phone' => $phone
        ]);

        $_SESSION['success'] = 'Teacher information updated successfully';
        header('Location: ' . BASE_URL . 'public/index.php?page=edit_teacher&teacher_id=' . $teacher_id);
        exit;

    } elseif ($action === 'update_password') {
        // Update teacher password
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($newPassword)) {
            throw new Exception('New password is required');
        }
        if (strlen($newPassword) < 6) {
            throw new Exception('Password must be at least 6 characters');
        }
        if ($newPassword !== $confirmPassword) {
            throw new Exception('Passwords do not match');
        }

        // Update password
        $User->updatePassword($teacher_id, $newPassword);

        $_SESSION['success'] = 'Password updated successfully';
        header('Location: ' . BASE_URL . 'public/index.php?page=edit_teacher&teacher_id=' . $teacher_id);
        exit;

    } else {
        throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: ' . BASE_URL . 'public/index.php?page=edit_teacher&teacher_id=' . $teacher_id);
    exit;
}

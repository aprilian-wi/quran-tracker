<?php
// src/Actions/edit_parent_action.php
global $pdo;

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Child.php';
require_once __DIR__ . '/../Helpers/functions.php';

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    $_SESSION['error'] = 'Invalid CSRF token';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

// Check authorization - only superadmin
// Check authorization - superadmin or school_admin
if (!in_array($_SESSION['role'], ['superadmin', 'school_admin'])) {
    $_SESSION['error'] = 'Unauthorized access';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

$action = $_POST['action'] ?? '';
$parent_id = isset($_POST['parent_id']) ? (int) $_POST['parent_id'] : 0;

$User = new User($pdo);
$parent = $User->findById($parent_id);

// Verify parent exists
if (!$parent || $parent['role'] !== 'parent') {
    $_SESSION['error'] = 'Parent not found';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

try {
    if ($action === 'update_info') {
        // Update parent name and phone
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        // Validation
        if (empty($name)) {
            throw new Exception('Parent name is required');
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
        if ($existingUser && $existingUser['id'] !== $parent_id) {
            throw new Exception('Phone number is already in use');
        }

        // Update user
        $User->update($parent_id, [
            'name' => $name,
            'phone' => $phone
        ]);

        $_SESSION['success'] = 'Parent information updated successfully';
        header('Location: ' . BASE_URL . 'public/index.php?page=edit_parent&parent_id=' . $parent_id);
        exit;

    } elseif ($action === 'update_password') {
        // Update parent password
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
        $User->updatePassword($parent_id, $newPassword);

        $_SESSION['success'] = 'Password updated successfully';
        header('Location: ' . BASE_URL . 'public/index.php?page=edit_parent&parent_id=' . $parent_id);
        exit;

    } elseif ($action === 'add_child') {
        // Add a new child to the parent
        $childName = trim($_POST['child_name'] ?? '');
        $childDob = $_POST['child_dob'] ?? null;

        if (empty($childName)) {
            throw new Exception('Child name is required');
        }

        // Validate date format if provided
        if (!empty($childDob)) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $childDob)) {
                throw new Exception('Invalid date format. Use YYYY-MM-DD');
            }
        }

        $childModel = new Child($pdo);
        $childModel->create([
            'name' => $childName,
            'parent_id' => $parent_id,
            'date_of_birth' => $childDob ?: null,
            'school_id' => $_SESSION['school_id'] ?? 1
        ]);

        $_SESSION['success'] = 'Child added successfully';
        header('Location: ' . BASE_URL . 'public/index.php?page=edit_parent&parent_id=' . $parent_id);
        exit;

    } elseif ($action === 'update_child') {
        // Update child information
        $childId = isset($_POST['child_id']) ? (int) $_POST['child_id'] : 0;
        $childName = trim($_POST['child_name'] ?? '');
        $childDob = $_POST['child_dob'] ?? null;

        if (!$childId) {
            throw new Exception('Invalid child');
        }
        if (empty($childName)) {
            throw new Exception('Child name is required');
        }

        // Validate date format if provided
        if (!empty($childDob)) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $childDob)) {
                throw new Exception('Invalid date format. Use YYYY-MM-DD');
            }
        }

        $childModel = new Child($pdo);
        // Verify child belongs to this parent
        $child = $childModel->find($childId);
        if (!$child || $child['parent_id'] != $parent_id) {
            throw new Exception('Child not found or does not belong to this parent');
        }

        // Update child
        $stmt = $pdo->prepare("UPDATE children SET name = ?, date_of_birth = ? WHERE id = ?");
        $stmt->execute([$childName, $childDob ?: null, $childId]);

        $_SESSION['success'] = 'Child information updated successfully';
        header('Location: ' . BASE_URL . 'public/index.php?page=edit_parent&parent_id=' . $parent_id);
        exit;

    } elseif ($action === 'delete_child') {
        // Delete child
        $childId = isset($_POST['child_id']) ? (int) $_POST['child_id'] : 0;

        if (!$childId) {
            throw new Exception('Invalid child');
        }

        $childModel = new Child($pdo);
        // Verify child belongs to this parent
        $child = $childModel->find($childId);
        if (!$child || $child['parent_id'] != $parent_id) {
            throw new Exception('Child not found or does not belong to this parent');
        }

        // Delete child
        $stmt = $pdo->prepare("DELETE FROM children WHERE id = ?");
        $stmt->execute([$childId]);

        $_SESSION['success'] = 'Child deleted successfully';
        header('Location: ' . BASE_URL . 'public/index.php?page=edit_parent&parent_id=' . $parent_id);
        exit;

    } else {
        throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: ' . BASE_URL . 'public/index.php?page=edit_parent&parent_id=' . $parent_id);
    exit;
}

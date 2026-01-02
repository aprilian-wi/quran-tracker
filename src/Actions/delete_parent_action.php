<?php
// src/Actions/delete_parent_action.php
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
// Authorization
if (!(hasRole('superadmin') || hasRole('school_admin'))) {
    $_SESSION['error'] = 'Unauthorized access';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

$parent_id = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : 0;

if ($parent_id <= 0) {
    $_SESSION['error'] = 'Invalid parent ID';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

$User = new User($pdo);
$parent = $User->findById($parent_id);

// Verify parent exists and is actually a parent
if (!$parent || $parent['role'] !== 'parent') {
    $_SESSION['error'] = 'Parent not found';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

// Security: Verify school ownership
if ($parent['school_id'] != ($_SESSION['school_id'] ?? 1)) {
    $_SESSION['error'] = 'Unauthorized access to this parent';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

try {
    // Get all children of this parent
    $Child = new Child($pdo);
    $children = $Child->getByParent($parent_id);

    // Start transaction
    $pdo->beginTransaction();

    // Unassign all children from this parent
    foreach ($children as $child) {
        // Update child to remove parent association
        $stmt = $pdo->prepare("UPDATE children SET parent_id = NULL WHERE id = ?");
        $stmt->execute([$child['id']]);
    }

    // Delete the parent
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'parent'");
    $stmt->execute([$parent_id]);

    // Commit transaction
    $pdo->commit();

    $_SESSION['success'] = 'Parent deleted successfully';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;

} catch (Exception $e) {
    // Rollback on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['error'] = 'Error deleting parent: ' . $e->getMessage();
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

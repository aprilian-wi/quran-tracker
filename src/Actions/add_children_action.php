<?php
// src/Actions/add_children_action.php
global $pdo;

require_once __DIR__ . '/../Models/Child.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Helpers/functions.php';

// CSRF
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    $_SESSION['error'] = 'Invalid CSRF token';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

// Authorization
if (!(hasRole('superadmin') || hasRole('school_admin'))) {
    $_SESSION['error'] = 'Unauthorized';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

$parent_id = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : 0;
if ($parent_id <= 0) {
    $_SESSION['error'] = 'Invalid parent selected';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

$User = new User($pdo);
$parent = $User->findById($parent_id);
if (!$parent || $parent['role'] !== 'parent') {
    $_SESSION['error'] = 'Parent not found';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

$children = $_POST['children'] ?? [];
if (!is_array($children) || count($children) === 0) {
    $_SESSION['error'] = 'No children provided';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

// Limit
$max = 10;
if (count($children) > $max) {
    $_SESSION['error'] = "Maximum {$max} children per request";
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

$Child = new Child($pdo);
$inserted = 0;
$errors = [];

$pdo->beginTransaction();
try {
    foreach ($children as $i => $c) {
        $name = trim($c['name'] ?? '');
        $dob = trim($c['dob'] ?? '');

        if ($name === '') {
            $errors[] = "Row " . ($i+1) . ": name is required";
            continue;
        }

        // Validate DOB if provided - expect YYYY-MM-DD
        if ($dob !== '') {
            $d = DateTime::createFromFormat('Y-m-d', $dob);
            if (!$d || $d->format('Y-m-d') !== $dob) {
                $errors[] = "Row " . ($i+1) . ": invalid date format (use YYYY-MM-DD)";
                continue;
            }
        } else {
            $dob = null;
        }

        $res = $Child->create([
            'name' => $name,
            'parent_id' => $parent_id,
            'class_id' => null,
            'date_of_birth' => $dob,
            'school_id' => $_SESSION['school_id'] ?? 1
        ]);
        if ($res) $inserted++;
        else $errors[] = "Row " . ($i+1) . ": failed to insert";
    }

    if (count($errors) > 0) {
        // rollback if nothing inserted
        if ($inserted === 0) {
            $pdo->rollBack();
            $_SESSION['error'] = implode('; ', $errors);
            header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
            exit;
        } else {
            // commit partial
            $pdo->commit();
            $_SESSION['success'] = "Inserted {$inserted} children. Some rows failed: " . implode('; ', $errors);
            header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
            exit;
        }
    }

    $pdo->commit();
    $_SESSION['success'] = "Inserted {$inserted} children successfully.";
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $_SESSION['error'] = 'Error inserting children: ' . $e->getMessage();
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

<?php
// src/Actions/edit_class_action.php
global $pdo;
require_once __DIR__ . '/../Helpers/functions.php';
require_once __DIR__ . '/../Models/Class.php';
require_once __DIR__ . '/../Models/Child.php';

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

$class_id = (int)($_POST['class_id'] ?? 0);
$action = $_POST['action'] ?? '';

if (!$class_id) {
    setFlash('danger', 'Invalid class.');
    redirect('admin/classes');
}

$classModel = new ClassModel($pdo);
$class = $classModel->find($class_id);

if (!$class) {
    setFlash('danger', 'Class not found.');
    redirect('admin/classes');
}

// Security: Ensure class belongs to the user's school
$current_school_id = $_SESSION['school_id'] ?? 1;
if ($class['school_id'] != $current_school_id) {
    setFlash('danger', 'Unauthorized access to this class.');
    redirect('admin/classes');
}

// Action: Update Class Name
if ($action === 'update_name') {
    $name = trim($_POST['name'] ?? '');
    if (!$name) {
        setFlash('danger', 'Class name is required.');
    } else {
        if ($classModel->updateName($class_id, $name)) {
            setFlash('success', 'Class name updated.');
        } else {
            setFlash('danger', 'Failed to update class name.');
        }
    }
    redirect('admin/edit_class', ['class_id' => $class_id]);
}

// Action: Add Teacher
if ($action === 'add_teacher') {
    $teacher_id = (int)($_POST['teacher_id'] ?? 0);
    if (!$teacher_id) {
        setFlash('danger', 'Please select a teacher.');
    } else {
        if ($classModel->assignTeacher($class_id, $teacher_id)) {
            setFlash('success', 'Teacher added to class.');
        } else {
            setFlash('danger', 'Failed to add teacher (may already be assigned).');
        }
    }
    redirect('admin/edit_class', ['class_id' => $class_id]);
}

// Action: Remove Teacher
if ($action === 'remove_teacher') {
    $teacher_id = (int)($_POST['teacher_id'] ?? 0);
    if (!$teacher_id) {
        setFlash('danger', 'Invalid teacher.');
    } else {
        if ($classModel->deleteTeacher($class_id, $teacher_id)) {
            setFlash('success', 'Teacher removed from class.');
        } else {
            setFlash('danger', 'Failed to remove teacher.');
        }
    }
    redirect('admin/edit_class', ['class_id' => $class_id]);
}

// Action: Remove Student
if ($action === 'remove_student') {
    $child_id = (int)($_POST['child_id'] ?? 0);
    if (!$child_id) {
        setFlash('danger', 'Invalid student.');
    } else {
        if ($classModel->removeStudent($child_id)) {
            setFlash('success', 'Student removed from class.');
        } else {
            setFlash('danger', 'Failed to remove student.');
        }
    }
    redirect('admin/edit_class', ['class_id' => $class_id]);
}

// Action: Assign Child
if ($action === 'assign_child') {
    $child_id = (int)($_POST['child_id'] ?? 0);
    if (!$child_id) {
        setFlash('danger', 'Please select a student.');
    } else {
        $childModel = new Child($pdo);
        if ($childModel->assignToClass($child_id, $class_id)) {
            setFlash('success', 'Student assigned to class.');
        } else {
            setFlash('danger', 'Failed to assign student.');
        }
    }
    redirect('admin/edit_class', ['class_id' => $class_id]);
}

// Action: Unassign Child
if ($action === 'unassign_child') {
    $child_id = (int)($_POST['child_id'] ?? 0);
    if (!$child_id) {
        setFlash('danger', 'Invalid student.');
    } else {
        $childModel = new Child($pdo);
        if ($childModel->unassignFromClass($child_id)) {
            setFlash('success', 'Student unassigned from class.');
        } else {
            setFlash('danger', 'Failed to unassign student.');
        }
    }
    redirect('admin/edit_class', ['class_id' => $class_id]);
}

// Action: Bulk Remove Students
if ($action === 'bulk_remove_students') {
    $child_ids = $_POST['child_ids'] ?? [];
    if (empty($child_ids)) {
        setFlash('warning', 'No students selected for removal.');
    } else {
        $childModel = new Child($pdo);
        $count = 0;
        foreach ($child_ids as $id) {
            if ($childModel->unassignFromClass((int)$id)) {
                $count++;
            }
        }
        setFlash('success', "$count students removed from class.");
    }
    redirect('admin/edit_class', ['class_id' => $class_id]);
}

// Action: Bulk Assign Students
if ($action === 'bulk_assign_students') {
    $child_ids = $_POST['child_ids'] ?? [];
    if (empty($child_ids)) {
        setFlash('warning', 'No students selected for assignment.');
    } else {
        $childModel = new Child($pdo);
        $count = 0;
        foreach ($child_ids as $id) {
            if ($childModel->assignToClass((int)$id, $class_id)) {
                $count++;
            }
        }
        setFlash('success', "$count students assigned to class.");
    }
    redirect('admin/edit_class', ['class_id' => $class_id]);
}

// Action: Delete Class
if ($action === 'delete_class') {
    $confirm = $_POST['confirm'] ?? '';
    if ($confirm !== 'yes') {
        setFlash('danger', 'Please confirm deletion.');
        redirect('admin/edit_class', ['class_id' => $class_id]);
    }
    
    if ($classModel->delete($class_id)) {
        setFlash('success', 'Class deleted successfully.');
        redirect('admin/classes');
    } else {
        setFlash('danger', 'Failed to delete class.');
        redirect('admin/edit_class', ['class_id' => $class_id]);
    }
}

setFlash('danger', 'Invalid action.');
redirect('admin/classes');

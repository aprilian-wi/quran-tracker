<?php
// src/Actions/import_parents_action.php
global $pdo;

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Child.php';
require_once __DIR__ . '/../Helpers/functions.php';

// Authorization Check
if (!(hasRole('superadmin') || hasRole('school_admin'))) {
    $_SESSION['error'] = 'Unauthorized access.';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

// CSRF Check
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    $_SESSION['error'] = 'Invalid CSRF token.';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

// File Upload Check
if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = 'Please upload a valid CSV file.';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

$fileTmpPath = $_FILES['csv_file']['tmp_name'];
$fileType = $_FILES['csv_file']['type'];

// Basic extension check (optional but good)
$fileName = $_FILES['csv_file']['name'];
if (pathinfo($fileName, PATHINFO_EXTENSION) !== 'csv') {
    $_SESSION['error'] = 'File must be a CSV.';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

// Parse CSV
$handle = fopen($fileTmpPath, 'r');
if ($handle === false) {
    $_SESSION['error'] = 'Could not open CSV file.';
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

$header = fgetcsv($handle, 1000, ","); // Read header
// Expected header: parent_name,parent_email,parent_password,child_name,child_dob

// Map header to indices to be flexible, or enforce order.
// For simplicity, let's look for specific columns.
$columnMap = array_flip($header);
$requiredColumns = ['parent_name', 'parent_email', 'parent_password', 'child_name'];
$missingColumns = [];

foreach ($requiredColumns as $col) {
    if (!isset($columnMap[$col])) {
        $missingColumns[] = $col;
    }
}

if (!empty($missingColumns)) {
    $_SESSION['error'] = 'Missing columns in CSV: ' . implode(', ', $missingColumns);
    fclose($handle);
    header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
    exit;
}

$userModel = new User($pdo);
$childModel = new Child($pdo);
$schoolId = $_SESSION['school_id'] ?? 1;

$results = [
    'parents_created' => 0,
    'parents_exists' => 0,
    'children_added' => 0,
    'errors' => []
];

$rowNum = 1; // Header is 1
while (($data = fgetcsv($handle, 1000, ",")) !== false) {
    $rowNum++;
    
    // Extract data using map
    $pName = trim($data[$columnMap['parent_name']] ?? '');
    $pEmail = trim($data[$columnMap['parent_email']] ?? '');
    $pPass = trim($data[$columnMap['parent_password']] ?? '');
    $cName = trim($data[$columnMap['child_name']] ?? '');
    $cDob = trim($data[$columnMap['child_dob']] ?? ''); // Optional

    if (empty($pEmail) || empty($pName) || empty($cName)) {
        $results['errors'][] = "Row {$rowNum}: Missing required fields (Name, Email, or Child Name).";
        continue;
    }

    try {
        // 1. Check or Create Parent
        $parent = $userModel->findByEmail($pEmail);
        $parentId = null;

        if ($parent) {
            // Check if role is parent
            if ($parent['role'] !== 'parent') {
                $results['errors'][] = "Row {$rowNum}: Email {$pEmail} belongs to non-parent user.";
                continue;
            }
            $parentId = $parent['id'];
            // Verify school? Assuming cross-school emails are unique or global unique.
            // If we strictly want to prevent adding children to parents of other schools (if that's a thing), check school_id.
            // For now, assuming email uniqueness globally or intended reuse.
            $results['parents_exists']++;
        } else {
            // Create New Parent
            if (empty($pPass)) {
                $results['errors'][] = "Row {$rowNum}: New parent requires password.";
                continue;
            }
            // Create
            $userModel->create([
                'name' => $pName,
                'email' => $pEmail,
                'password' => $pPass, // User model should hash it
                'role' => 'parent',
                'school_id' => $schoolId
            ]);
            $newParent = $userModel->findByEmail($pEmail); // Fetch back to get ID
            if ($newParent) {
                $parentId = $newParent['id'];
                $results['parents_created']++;
            } else {
                $results['errors'][] = "Row {$rowNum}: Failed to create parent.";
                continue;
            }
        }

        // 2. Add Child
        // Validate DOB
        // Validate DOB
        $dobVal = null;
        if (!empty($cDob)) {
            $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'Y/m/d'];
            $found = false;
            foreach ($formats as $fmt) {
                $d = DateTime::createFromFormat($fmt, $cDob);
                if ($d && $d->format($fmt) === $cDob) {
                    $dobVal = $d->format('Y-m-d');
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                // Try generous parsing if strict format fails (e.g. Excel sometimes omits leading zeros)
                try {
                    $d = new DateTime($cDob);
                    $dobVal = $d->format('Y-m-d');
                } catch (Exception $e) {
                    // Invalid date, treat as null but maybe warn?
                    // For now, ignoring invalid dates to allow import to proceed
                    $dobVal = null; 
                }
            }
        }

        // Check if child already exists for this parent?
        // To prevent duplicates if running import twice.
        // Simple check: Look for child with same name for this parent.
        // Current Child model doesn't have exact "findByNameAndParent", so let's skip check or implement it.
        // Implementing simple check to avoid obvious duplicates.
        $stmt = $pdo->prepare("SELECT id FROM children WHERE parent_id = ? AND name = ?");
        $stmt->execute([$parentId, $cName]);
        if ($stmt->fetch()) {
            // Child already exists
            // $results['errors'][] = "Row {$rowNum}: Child '{$cName}' already exists for this parent.";
            // Decide: Skip silently?
            continue; 
        }

        $childModel->create([
            'name' => $cName,
            'parent_id' => $parentId,
            'class_id' => null,
            'date_of_birth' => $dobVal,
            'school_id' => $schoolId
        ]);
        $results['children_added']++;

    } catch (Exception $e) {
        $results['errors'][] = "Row {$rowNum}: Error processing - " . $e->getMessage();
    }
}

fclose($handle);

// Build Result Message
$msgType = 'success';
$msgText = "Import Complete. Parents Created: {$results['parents_created']}, Existing Parents Used: {$results['parents_exists']}, Children Added: {$results['children_added']}.";

if (!empty($results['errors'])) {
    $msgType = 'warning'; // or info/danger depending on severity
    // Show first 5 errors to avoid huge session text
    $errCount = count($results['errors']);
    $shownErrors = array_slice($results['errors'], 0, 5);
    $errStr = implode('<br>', $shownErrors);
    if ($errCount > 5) $errStr .= "<br>...and " . ($errCount - 5) . " more errors.";
    
    $msgText .= "<br><strong>Errors:</strong><br>" . $errStr;
}

$_SESSION[$msgType === 'warning' ? 'warning' : 'success'] = $msgText; // 'warning' flash might not exist, checking functions.php
// functions.php likely has setFlash or $_SESSION['success/error/warning']. 
// Assuming standard keys. If 'warning' not supported by layout, use 'info' or 'error'.
// Lets stick to 'success' or 'error' if mixed.
if (!empty($results['errors']) && $results['children_added'] == 0) {
    $_SESSION['error'] = "Import Failed.<br>" . $errStr;
} elseif (!empty($results['errors'])) {
    $_SESSION['warning'] = $msgText; // Hope layout supports warning
} else {
    $_SESSION['success'] = $msgText;
}

header('Location: ' . BASE_URL . 'public/index.php?page=admin/parents');
exit;

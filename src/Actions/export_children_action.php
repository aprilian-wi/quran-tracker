<?php
// src/Actions/export_children_action.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Controllers/AdminController.php';
require_once __DIR__ . '/../Helpers/functions.php';

if (!(hasRole('superadmin') || hasRole('school_admin'))) {
    setFlash('danger', 'Access denied.');
    redirect('dashboard');
    exit;
}

$controller = new AdminController($pdo);
$class_id = $_GET['class_id'] ?? null;

$children = $controller->getChildren($class_id);

// Set headers for CSV file
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="children_export_' . date('Y-m-d_H-i-s') . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Write BOM for UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Write CSV headers
fputcsv($output, ['Name', 'Class', 'Parent Name']);

// Write data rows
foreach ($children as $child) {
    fputcsv($output, [
        $child['name'],
        $child['class_name'],
        $child['parent_name'] ?? '-'
    ]);
}

fclose($output);
exit;

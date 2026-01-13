<?php
// src/Actions/export_users_action.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Controllers/AdminController.php';
require_once __DIR__ . '/../Helpers/functions.php';

if (!(hasRole('superadmin') || hasRole('school_admin'))) {
    setFlash('danger', 'Access denied.');
    redirect('dashboard');
    exit;
}

$controller = new AdminController($pdo);
$role = $_GET['role'] ?? null;

$users = $controller->users($role);

// Set headers for CSV file
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="users_export_' . date('Y-m-d_H-i-s') . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Write BOM for UTF-8
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Write CSV headers
fputcsv($output, ['ID', 'Name', 'Phone', 'Role', 'Joined']);

// Write data rows
foreach ($users as $user) {
    fputcsv($output, [
        $user['id'],
        $user['name'],
        $user['phone'] ?? '-',
        ucfirst($user['role']),
        date('M j, Y', strtotime($user['created_at']))
    ]);
}

fclose($output);
exit;

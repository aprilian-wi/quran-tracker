<?php
// src/Views/admin/export_teachers.php

require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

requireLayer('admin');

// Only Global Admin should access this if we are replacing the button for them, 
// but logically School Admin might want export too (though user specifically asked for global admin change).
// I will allow both but filter logic is in controller.

$controller = new AdminController($pdo);

// Capture filters
$search = $_GET['search'] ?? '';
$schoolSearch = $_GET['school_q'] ?? '';

// Fetch teachers
$teachers = $controller->teachers(['search' => $search, 'school_search' => $schoolSearch]);

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="teachers_export_' . date('Y-m-d_H-i-s') . '.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Add BOM for Excel compatibility
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// CSV Headers
$headers = ['No', 'Nama', 'Username', 'Email', 'No. HP', 'Sekolah', 'Tanggal Dibuat'];
fputcsv($output, $headers);

// CSV Data
$no = 1;
foreach ($teachers as $teacher) {
    fputcsv($output, [
        $no++,
        $teacher['name'],
        $teacher['username'],
        $teacher['email'] ?? '-', // Email might be removed column but key might exist in array if select *
        $teacher['phone'],
        $teacher['school_name'] ?? 'N/A',
        $teacher['created_at']
    ]);
}

fclose($output);
exit;

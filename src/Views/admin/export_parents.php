<?php
// src/Views/admin/export_parents.php

require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

requireLayer('admin');

$controller = new AdminController($pdo);

// Capture filters (matching what is in parents.php)
// Since AdminController::parents() only accepts session school_id or null if superadmin, 
// and filters array.
// But wait, AdminController::parents() code:
/*
    public function parents()
    {
        $search = isset($_GET['search']) ? trim($_GET['search']) : null;
        $schoolSearch = isset($_GET['school_q']) ? trim($_GET['school_q']) : null;
        
        $school_id = isGlobalAdmin() ? null : (int) $_SESSION['school_id'];
        
        return $this->userModel->parentsWithChildCount($school_id, [
            'search' => $search,
            'school_search' => $schoolSearch
        ]);
    }
*/
// It reads $_GET directly. So I don't need to pass args if I just call $controller->parents().
// However, the export file acts as the entry point, so $_GET params passed in URL will be available.

$parents = $controller->parents();

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="parents_export_' . date('Y-m-d_H-i-s') . '.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Add BOM for Excel compatibility
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// CSV Headers
$headers = ['No', 'Nama', 'username', 'Email', 'No. HP', 'Sekolah', 'Jumlah Anak', 'Tanggal Dibuat'];
fputcsv($output, $headers);

// CSV Data
$no = 1;
foreach ($parents as $parent) {
    fputcsv($output, [
        $no++,
        $parent['name'],
        $parent['username'],
        $parent['email'] ?? '-',
        $parent['phone'],
        $parent['school_name'] ?? 'N/A',
        $parent['child_count'],
        $parent['created_at']
    ]);
}

fclose($output);
exit;

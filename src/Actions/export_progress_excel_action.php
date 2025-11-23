<?php
// src/Actions/export_progress_excel_action.php
global $pdo;
require_once __DIR__ . '/../Helpers/functions.php';
require_once __DIR__ . '/../Models/Progress.php';
require_once __DIR__ . '/../Models/Child.php';

// Check if teacher or superadmin
if (!hasRole('teacher') && !hasRole('superadmin')) {
    die('Access denied');
}

$child_id = (int)($_GET['child_id'] ?? 0);
$status_filter = $_GET['status'] ?? '';
$updated_by_filter = $_GET['updated_by'] ?? '';

if (!$child_id) {
    die('Invalid child ID');
}

// Verify access to child
$childModel = new Child($pdo);
$child = $childModel->find($child_id, $_SESSION['user_id'], $_SESSION['role']);
if (!$child) {
    die('Access denied');
}

$progressModel = new Progress($pdo);
$history = $progressModel->getBookHistory($child_id);

// Apply filters if provided
if ($status_filter || $updated_by_filter) {
    $filtered_history = [];
    foreach ($history as $entry) {
        $statusText = $entry['status'] === 'fluent' ? 'Lancar' :
                      ($entry['status'] === 'repeating' ? 'Mengulang' : ucfirst($entry['status']));

        $statusMatch = !$status_filter || $statusText === $status_filter;
        $updatedByMatch = !$updated_by_filter || $entry['updated_by_name'] === $updated_by_filter;

        if ($statusMatch && $updatedByMatch) {
            $filtered_history[] = $entry;
        }
    }
    $history = $filtered_history;
}

// Create Excel file using simple CSV approach (since PHPSpreadsheet not available)
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="progress_' . $child['name'] . '_' . date('Y-m-d') . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Write BOM for UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Write headers
fputcsv($output, [
    'Date',
    'Book',
    'Page',
    'Status',
    'Note',
    'Updated By'
]);

// Write data
foreach ($history as $entry) {
    $statusText = $entry['status'] === 'fluent' ? 'Lancar' :
                  ($entry['status'] === 'repeating' ? 'Mengulang' : ucfirst($entry['status']));

    fputcsv($output, [
        date('M j, Y g:i A', strtotime($entry['updated_at'])),
        'Jilid ' . $entry['volume_number'] . ' - ' . $entry['title'],
        $entry['page'],
        $statusText,
        $entry['note'] ?? '',
        $entry['updated_by_name']
    ]);
}

fclose($output);
exit;

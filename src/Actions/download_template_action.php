<?php
// src/Actions/download_template_action.php

// Authorization Check
if (!isLoggedIn()) {
    die('Unauthorized');
}

// Set headers for download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=template_import_parents.csv');

// Create file pointer connected to output stream
$output = fopen('php://output', 'w');

// Output the column headings
fputcsv($output, ['parent_name', 'parent_email', 'parent_password', 'child_name', 'child_dob']);

// Output sample data (optional, helps user understand)
fputcsv($output, ['Budi Santoso', 'budi@example.com', 'password123', 'Ahmad Santoso', '2015-05-20']);
fputcsv($output, ['Siti Aminah', 'siti@example.com', 'securepass', 'Rina Aminah', '2016-10-15']);

fclose($output);
exit;

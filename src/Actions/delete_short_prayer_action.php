<?php
// src/Actions/delete_short_prayer_action.php
require_once __DIR__ . '/../Controllers/AdminController.php';
require_once __DIR__ . '/../Helpers/functions.php';

session_start();

if (!(hasRole('superadmin') || hasRole('school_admin'))) {
    setFlash('danger', 'Access denied.');
    redirect('admin/manage_short_prayers');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    setFlash('danger', 'Invalid short prayer ID.');
    redirect('admin/manage_short_prayers');
    exit;
}

$pdo = require __DIR__ . '/../../config/database.php';
$adminController = new AdminController($pdo);

if ($adminController->deleteShortPrayer($id)) {
    setFlash('success', 'Short prayer deleted successfully.');
} else {
    setFlash('danger', 'Failed to delete short prayer.');
}

redirect('admin/manage_short_prayers');
exit;

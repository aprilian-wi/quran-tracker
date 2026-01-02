<?php
// src/Actions/update_progress_prayers_action.php
require_once __DIR__ . '/../Models/Progress.php';

// Remove session_start() here because session is already started in public/index.php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('update_progress_prayers', ['child_id' => $_POST['child_id'] ?? 0]);
    exit;
}

$allowedRoles = ['superadmin', 'school_admin', 'teacher', 'parent'];
if (!in_array($_SESSION['role'] ?? '', $allowedRoles)) {
    setFlash('danger', 'Access denied.');
    redirect('update_progress_prayers', ['child_id' => $_POST['child_id'] ?? 0]);
    exit;
}

checkCSRFToken();

$pdo = require __DIR__ . '/../../config/database.php';
$progressModel = new Progress($pdo);

$child_id = $_POST['child_id'] ?? null;
$prayer_id = $_POST['prayer_id'] ?? null;
$status = $_POST['status'] ?? null;
$updated_by = $_POST['updated_by'] ?? null;
$note = $_POST['note'] ?? null;

if (!$child_id || !$prayer_id || !$status || !$updated_by) {
    setFlash('danger', 'Required data missing.');
    redirect('update_progress_prayers', ['child_id' => $child_id]);
    exit;
}

if (!is_numeric($child_id) || !is_numeric($prayer_id) || !in_array($status, ['in_progress', 'memorized'])) {
    setFlash('danger', 'Invalid data provided.');
    redirect('update_progress_prayers', ['child_id' => $child_id]);
    exit;
}

$success = $progressModel->updatePrayerProgress($child_id, $prayer_id, $status, $updated_by, $note);

if ($success) {
    // Insert notification for parent if updated by teacher
    if ($_SESSION['role'] === 'teacher') {
        $progress_id = $pdo->lastInsertId();
        $progressModel->insertNotification($child_id, 'doa', $progress_id);
    }
    setFlash('success', 'Progress updated successfully.');
} else {
    setFlash('danger', 'Failed to update progress.');
}

if ($_SESSION['role'] === 'parent') {
    redirect('parent/update_progress_prayers', ['child_id' => $child_id]);
} else {
    redirect('teacher/update_progress_prayers', ['child_id' => $child_id]);
}
exit;

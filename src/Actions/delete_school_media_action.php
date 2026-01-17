<?php
// src/Actions/delete_school_media_action.php

checkCSRFToken();
requireLogin();

if (!isGlobalAdmin()) {
    die('Access denied');
}

$id = $_POST['id'] ?? 0;
$filename = $_POST['filename'] ?? '';

if (empty($id) || empty($filename)) {
    redirect('admin/schools');
}

// Prevent directory traversal
$filename = basename($filename);
$filePath = __DIR__ . '/../../public/uploads/schools/' . $id . '/' . $filename;

if (file_exists($filePath)) {
    unlink($filePath);
    setFlash('success', 'File deleted.');
} else {
    setFlash('danger', 'File not found.');
}

redirect('admin/edit_school', ['id' => $id]);

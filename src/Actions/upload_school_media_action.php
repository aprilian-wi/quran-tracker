<?php
// src/Actions/upload_school_media_action.php

checkCSRFToken();
requireLogin();

if (!isGlobalAdmin()) {
    die('Access denied');
}

$id = $_POST['id'] ?? 0;
if (empty($id)) {
    setFlash('danger', 'School ID is required.');
    redirect('admin/schools');
}

// Check if files exist
if (!isset($_FILES['media']) || empty($_FILES['media']['name'][0])) {
    setFlash('danger', 'No file selected.');
    redirect('admin/edit_school', ['id' => $id]);
}

$files = $_FILES['media'];
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$maxSize = 2 * 1024 * 1024; // 2MB
$uploadDir = __DIR__ . '/../../public/uploads/schools/' . $id . '/';

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$uploadedCount = 0;
$errors = [];

$fileCount = count($files['name']);

for ($i = 0; $i < $fileCount; $i++) {
    $fileName = $files['name'][$i];
    $fileType = $files['type'][$i];
    $fileTmp = $files['tmp_name'][$i];
    $fileError = $files['error'][$i];
    $fileSize = $files['size'][$i];

    if ($fileError !== UPLOAD_ERR_OK) {
        $errors[] = "$fileName: Upload error code $fileError";
        continue;
    }

    if (!in_array($fileType, $allowedTypes)) {
        $errors[] = "$fileName: Invalid file type.";
        continue;
    }

    if ($fileSize > $maxSize) {
        $errors[] = "$fileName: File too large (Max 2MB).";
        continue;
    }

    // Generate unique filename
    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
    $newFilename = uniqid('media_') . '_' . $i . '.' . $ext;
    $targetPath = $uploadDir . $newFilename;

    if (move_uploaded_file($fileTmp, $targetPath)) {
        $uploadedCount++;
    } else {
        $errors[] = "$fileName: Failed to save.";
    }
}

if ($uploadedCount > 0) {
    if (empty($errors)) {
        setFlash('success', "$uploadedCount files uploaded successfully.");
    } else {
        setFlash('warning', "$uploadedCount uploaded, but some failed: " . implode(', ', $errors));
    }
} else {
    setFlash('danger', 'Failed to upload files: ' . implode(', ', $errors));
}

redirect('admin/edit_school', ['id' => $id]);

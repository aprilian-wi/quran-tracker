<?php
// src/Actions/store_video_action.php
requireLogin();

if (!hasRole('superadmin')) {
    die('Access denied');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/videos');
}

// CSRF Check
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Invalid CSRF token');
}

require_once '../src/Models/Video.php';
$videoModel = new Video($pdo);

$title = $_POST['title'] ?? '';
$youtube_id = $_POST['youtube_id'] ?? '';
$category_id = $_POST['category_id'] ?? '';
$description = $_POST['description'] ?? '';
$duration = $_POST['duration'] ?? '00:00';

if (empty($title) || empty($youtube_id) || empty($category_id)) {
    setFlash('danger', 'Mohon lengkapi judul, link youtube, dan kategori.');
    redirect('admin/create_video');
}

if ($videoModel->create($category_id, $title, $youtube_id, $description, $duration)) {
    setFlash('success', 'Video berhasil ditambahkan.');
    redirect('admin/videos');
} else {
    setFlash('danger', 'Gagal menyimpan video.');
    redirect('admin/create_video');
}

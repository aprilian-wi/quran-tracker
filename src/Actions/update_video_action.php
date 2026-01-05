<?php
// src/Actions/update_video_action.php
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

$id = $_POST['id'] ?? 0;
$title = $_POST['title'] ?? '';
$youtube_id = $_POST['youtube_id'] ?? '';
$category_id = $_POST['category_id'] ?? '';
$description = $_POST['description'] ?? '';
$duration = $_POST['duration'] ?? '00:00';

if (empty($id) || empty($title) || empty($youtube_id) || empty($category_id)) {
    setFlash('danger', 'Mohon lengkapi data wajib.');
    redirect("admin/edit_video&id=$id");
}

if ($videoModel->update($id, $category_id, $title, $youtube_id, $description, $duration)) {
    setFlash('success', 'Video berhasil diperbarui.');
    redirect('admin/videos');
} else {
    setFlash('danger', 'Gagal memperbarui video.');
    redirect("admin/edit_video&id=$id");
}

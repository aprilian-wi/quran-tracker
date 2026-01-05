<?php
// src/Actions/delete_video_action.php
requireLogin();

if (!hasRole('superadmin')) {
    die('Access denied');
}

$id = $_GET['id'] ?? 0;
if (!$id) {
    redirect('admin/videos');
}

require_once '../src/Models/Video.php';
$videoModel = new Video($pdo);

if ($videoModel->delete($id)) {
    setFlash('success', 'Video berhasil dihapus.');
} else {
    setFlash('danger', 'Gagal menghapus video.');
}

redirect('admin/videos');

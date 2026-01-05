<?php
// src/Actions/delete_video_category_action.php
requireLogin();

if (!hasRole('superadmin')) {
    die('Access denied');
}

$id = $_GET['id'] ?? 0;
if (!$id) {
    redirect('admin/video_categories');
}

require_once '../src/Models/VideoCategory.php';
$categoryModel = new VideoCategory($pdo);

if ($categoryModel->delete($id)) {
    setFlash('success', 'Kategori berhasil dihapus.');
} else {
    setFlash('danger', 'Gagal menghapus kategori.');
}

redirect('admin/video_categories');

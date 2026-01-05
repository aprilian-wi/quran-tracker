<?php
// src/Actions/update_video_category_action.php
requireLogin();

if (!hasRole('superadmin')) {
    die('Access denied');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/video_categories');
}

// CSRF Check
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Invalid CSRF token');
}

require_once '../src/Models/VideoCategory.php';
$categoryModel = new VideoCategory($pdo);

$id = $_POST['id'] ?? 0;
$name = $_POST['name'] ?? '';
$icon = $_POST['icon'] ?? 'movie';

if (empty($id) || empty($name)) {
    setFlash('danger', 'Nama kategori wajib diisi.');
    redirect("admin/edit_video_category&id=$id");
}

if ($categoryModel->update($id, $name, $icon)) {
    setFlash('success', 'Kategori berhasil diperbarui.');
    redirect('admin/video_categories');
} else {
    setFlash('danger', 'Gagal memperbarui kategori.');
    redirect("admin/edit_video_category&id=$id");
}

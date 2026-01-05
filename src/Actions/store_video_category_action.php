<?php
// src/Actions/store_video_category_action.php
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

$name = $_POST['name'] ?? '';
$icon = $_POST['icon'] ?? 'movie';

if (empty($name)) {
    setFlash('danger', 'Nama kategori wajib diisi.');
    redirect('admin/create_video_category');
}

if ($categoryModel->create($name, $icon)) {
    setFlash('success', 'Kategori berhasil ditambahkan.');
    redirect('admin/video_categories');
} else {
    setFlash('danger', 'Gagal menyimpan kategori.');
    redirect('admin/create_video_category');
}

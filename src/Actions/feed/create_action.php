<?php
// src/Actions/feed/create_action.php
require_once __DIR__ . '/../../Helpers/functions.php';
require_once __DIR__ . '/../../Models/Feed.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = currentUser();

    // Only teachers (and maybe admins) can post
    if ($user['role'] !== 'teacher' && $user['role'] !== 'admin' && $user['role'] !== 'superadmin') {
        setFlash('danger', 'Hanya guru yang dapat membuat postingan.');
        redirect('feed/index');
    }

    $caption = trim($_POST['caption'] ?? '');
    $school_id = $_SESSION['school_id'];

    // File Upload handling
    $uploadDir = __DIR__ . '/../../../public/uploads/feeds/'; // Absolute path
    $publicPath = 'uploads/feeds/'; // Database path

    $contentType = 'text';
    $contentValues = null;

    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['media'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedImages = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $allowedVideos = ['mp4', 'webm', 'mov'];

        $filename = uniqid('feed_') . '.' . $ext;
        $destination = $uploadDir . $filename;

        if (in_array($ext, $allowedImages)) {
            $contentType = 'image';
        } elseif (in_array($ext, $allowedVideos)) {
            $contentType = 'video';
        } else {
            setFlash('danger', 'Format file tidak didukung.');
            redirect('feed/create');
        }

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $contentValues = $publicPath . $filename;
        } else {
            setFlash('danger', 'Gagal mengupload file.');
            redirect('feed/create');
        }
    } elseif (empty($caption)) {
        setFlash('danger', 'Konten atau caption harus diisi.');
        redirect('feed/create');
    }

    try {
        global $pdo;
        $feedModel = new Feed($pdo);
        $feedModel->create([
            'user_id' => $user['id'],
            'school_id' => $school_id,
            'content_type' => $contentType,
            'content' => $contentValues,
            'caption' => $caption
        ]);

        setFlash('success', 'Berhasil membagikan momen!');
        redirect('feed/index');
    } catch (Exception $e) {
        setFlash('danger', 'Terjadi kesalahan: ' . $e->getMessage());
        redirect('feed/create');
    }
}

<?php
// src/Actions/feed/edit_action.php

require_once __DIR__ . '/../../Helpers/functions.php';
require_once __DIR__ . '/../../Models/Feed.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feed_id = $_POST['feed_id'] ?? null;
    $caption = trim($_POST['caption'] ?? '');
    $user = currentUser();

    if (!$feed_id) {
        setFlash('danger', 'ID Feed tidak ditemukan.');
        redirect('feed/index');
    }

    try {
        global $pdo;
        $feedModel = new Feed($pdo);
        $feed = $feedModel->findById($feed_id);

        if (!$feed) {
            setFlash('danger', 'Feed tidak ditemukan.');
            redirect('feed/index');
        }

        // Verify Ownership
        if ($feed['user_id'] != $user['id']) {
            setFlash('danger', 'Anda tidak memiliki hak akses untuk mengedit postingan ini.');
            redirect('feed/index');
        }

        $feedModel->update($feed_id, $caption);
        setFlash('success', 'Caption berhasil diperbarui.');
        redirect('feed/index');

    } catch (Exception $e) {
        setFlash('danger', 'Gagal memperbarui caption: ' . $e->getMessage());
        redirect('feed/index');
    }
}

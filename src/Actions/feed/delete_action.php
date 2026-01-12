<?php
// src/Actions/feed/delete_action.php

require_once __DIR__ . '/../../Helpers/functions.php';
require_once __DIR__ . '/../../Models/Feed.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feed_id = $_POST['feed_id'] ?? null;
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
        if ($feed['user_id'] != $user['id'] && !hasRole('superadmin')) {
            setFlash('danger', 'Anda tidak memiliki hak akses untuk menghapus postingan ini.');
            redirect('feed/index');
        }

        $feedModel->delete($feed_id);
        setFlash('success', 'Postingan berhasil dihapus.');
        redirect('feed/index');

    } catch (Exception $e) {
        setFlash('danger', 'Gagal menghapus postingan: ' . $e->getMessage());
        redirect('feed/index');
    }
}

<?php
// src/Actions/feed/comment_action.php
require_once __DIR__ . '/../../Helpers/functions.php';
require_once __DIR__ . '/../../Models/Feed.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feed_id = $_POST['feed_id'] ?? null;
    $comment = trim($_POST['comment'] ?? '');
    $user_id = $_SESSION['user_id'];

    if (!$feed_id || empty($comment)) {
        setFlash('danger', 'Komentar tidak boleh kosong.');
        redirect('feed/index');
    }

    try {
        global $pdo;
        $feedModel = new Feed($pdo);
        $feedModel->addComment($feed_id, $user_id, $comment);

        // redirect back to feed (maybe with anchor?)
        redirect('feed/index');
    } catch (Exception $e) {
        setFlash('danger', 'Gagal mengirim komentar.');
        redirect('feed/index');
    }
}

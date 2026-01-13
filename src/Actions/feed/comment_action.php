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

        // --- Notification Logic ---
        $feed = $feedModel->findById($feed_id);
        if ($feed && $feed['user_id'] != $user_id) { // Only notify if not own post
            require_once __DIR__ . '/../../Models/Notification.php';
            $notificationModel = new Notification($pdo);

            // Get the ID of the comment we just added. 
            // Since addComment return boolean, we might need to fetch last ID or update addComment to return ID.
            // For now, let's look at Feed.php addComment. 
            // It returns execute() result (bool). 
            // We can simplify and use feed_id as progress_id IF we update logic in Notification model, 
            // BUT I updated Notification model to expect comment_id in progress_id for better details.

            // Let's use $pdo->lastInsertId() since we know we just inserted.
            $comment_id = $pdo->lastInsertId();

            $notificationModel->createForUser($feed['user_id'], 'feed_comment', $comment_id);
        }
        // --------------------------

        // redirect back to feed (maybe with anchor?)
        redirect('feed/index');
    } catch (Exception $e) {
        setFlash('danger', 'Gagal mengirim komentar.');
        redirect('feed/index');
    }
}

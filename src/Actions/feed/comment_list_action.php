<?php
// src/Actions/feed/comment_list_action.php

require_once __DIR__ . '/../../Helpers/functions.php';
require_once __DIR__ . '/../../Models/Feed.php';

requireLogin();

header('Content-Type: application/json');

$feed_id = $_GET['feed_id'] ?? null;

if (!$feed_id) {
    echo json_encode(['success' => false, 'message' => 'Feed ID required']);
    exit;
}

try {
    global $pdo;
    $feedModel = new Feed($pdo);
    $comments = $feedModel->getComments($feed_id);

    // Format for frontend
    $formattedComments = array_map(function ($c) {
        return [
            'id' => $c['id'],
            'user_name' => h($c['user_name']),
            'user_role' => h($c['user_role']), // Needed to show "Teacher" or "Parent"
            'comment' => nl2br(h($c['comment'])),
            'created_at' => date('d M Y • H:i', strtotime($c['created_at'])),
            'is_me' => $c['user_id'] == $_SESSION['user_id']
        ];
    }, $comments);

    echo json_encode(['success' => true, 'comments' => $formattedComments]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

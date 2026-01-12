<?php
// src/Actions/feed/like_action.php
require_once __DIR__ . '/../../Helpers/functions.php';
require_once __DIR__ . '/../../Models/Feed.php';

requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $feed_id = $input['feed_id'] ?? null;
    $user_id = $_SESSION['user_id'];

    if (!$feed_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit;
    }

    try {
        global $pdo;
        $feedModel = new Feed($pdo);
        $status = $feedModel->toggleLike($feed_id, $user_id);

        echo json_encode(['success' => true, 'status' => $status]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

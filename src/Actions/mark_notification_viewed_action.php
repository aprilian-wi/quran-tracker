<?php
// src/Actions/mark_notification_viewed_action.php
require_once __DIR__ . '/../Models/Progress.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notification_id'])) {
    $notificationId = (int)$_POST['notification_id'];

    if ($notificationId > 0) {
        $progressModel = new Progress($pdo);
        $success = $progressModel->markNotificationViewed($notificationId);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Invalid notification ID']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>

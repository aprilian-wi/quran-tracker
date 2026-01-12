<?php
// src/Models/Feed.php

class Feed
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Create a new feed
    public function create($data)
    {
        $sql = "INSERT INTO feeds (user_id, school_id, content_type, content, caption) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['user_id'],
            $data['school_id'],
            $data['content_type'],
            $data['content'],
            $data['caption']
        ]);
    }

    // Get all valid feeds (last 24h) with user info, like counts, and comments count
    // This triggers cleanup first
    public function getAllValid($school_id, $current_user_id)
    {
        $this->cleanupExpired();

        $sql = "
            SELECT f.*, 
                   u.name as user_name, 
                   u.role as user_role,
                   (SELECT COUNT(*) FROM feed_likes fl WHERE fl.feed_id = f.id) as like_count,
                   (SELECT COUNT(*) FROM feed_comments fc WHERE fc.feed_id = f.id) as comment_count,
                   (SELECT COUNT(*) FROM feed_likes fl2 WHERE fl2.feed_id = f.id AND fl2.user_id = ?) as is_liked
            FROM feeds f
            JOIN users u ON f.user_id = u.id
            WHERE f.school_id = ?
            ORDER BY f.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$current_user_id, $school_id]);
        return $stmt->fetchAll();
    }

    // Get single feed (for validation or single view)
    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM feeds WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Like a feed
    public function toggleLike($feed_id, $user_id)
    {
        // Check if liked
        $stmt = $this->pdo->prepare("SELECT id FROM feed_likes WHERE feed_id = ? AND user_id = ?");
        $stmt->execute([$feed_id, $user_id]);
        $exists = $stmt->fetch();

        if ($exists) {
            // Unlike
            $stmt = $this->pdo->prepare("DELETE FROM feed_likes WHERE feed_id = ? AND user_id = ?");
            $stmt->execute([$feed_id, $user_id]);
            return 'unliked';
        } else {
            // Like
            $stmt = $this->pdo->prepare("INSERT INTO feed_likes (feed_id, user_id) VALUES (?, ?)");
            $stmt->execute([$feed_id, $user_id]);
            return 'liked';
        }
    }

    // Add comment
    public function addComment($feed_id, $user_id, $comment)
    {
        $stmt = $this->pdo->prepare("INSERT INTO feed_comments (feed_id, user_id, comment) VALUES (?, ?, ?)");
        return $stmt->execute([$feed_id, $user_id, $comment]);
    }

    // Get comments for a feed
    public function getComments($feed_id)
    {
        $sql = "
            SELECT fc.*, u.name as user_name, u.role as user_role
            FROM feed_comments fc
            JOIN users u ON fc.user_id = u.id
            WHERE fc.feed_id = ?
            ORDER BY fc.created_at ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$feed_id]);
        return $stmt->fetchAll();
    }

    // Hard delete expired feeds (>= 24 hours)
    public function cleanupExpired()
    {
        // 1. Find expired feeds
        $sql = "SELECT id, content_type, content FROM feeds WHERE created_at < NOW() - INTERVAL 24 HOUR";
        $stmt = $this->pdo->query($sql);
        $expiredFeeds = $stmt->fetchAll();

        if (empty($expiredFeeds)) {
            return;
        }

        // 2. Delete physical files
        foreach ($expiredFeeds as $feed) {
            if (($feed['content_type'] === 'image' || $feed['content_type'] === 'video') && !empty($feed['content'])) {
                $filePath = __DIR__ . '/../../public/' . $feed['content'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        // 3. Delete from DB (Cascade will handle likes/comments)
        $ids = array_column($expiredFeeds, 'id');
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $deleteSql = "DELETE FROM feeds WHERE id IN ($placeholders)";
        $deleteStmt = $this->pdo->prepare($deleteSql);
        $deleteStmt->execute($ids);
    }
}

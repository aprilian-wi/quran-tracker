<?php
// src/Models/Video.php

class Video {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll($limit = null) {
        $sql = "SELECT v.*, c.name as category_name 
                FROM videos v 
                JOIN video_categories c ON v.category_id = c.id 
                ORDER BY v.created_at DESC";
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCategory($categoryId) {
        $stmt = $this->pdo->prepare("
            SELECT v.*, c.name as category_name 
            FROM videos v 
            JOIN video_categories c ON v.category_id = c.id 
            WHERE v.category_id = ? 
            ORDER BY v.created_at DESC
        ");
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("
            SELECT v.*, c.name as category_name 
            FROM videos v 
            JOIN video_categories c ON v.category_id = c.id 
            WHERE v.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($category_id, $title, $youtube_id, $description, $duration = '00:00') {
        $stmt = $this->pdo->prepare("
            INSERT INTO videos (category_id, title, youtube_id, description, duration) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$category_id, $title, $youtube_id, $description, $duration]);
    }

    public function update($id, $category_id, $title, $youtube_id, $description, $duration) {
        $stmt = $this->pdo->prepare("
            UPDATE videos 
            SET category_id = ?, title = ?, youtube_id = ?, description = ?, duration = ? 
            WHERE id = ?
        ");
        return $stmt->execute([$category_id, $title, $youtube_id, $description, $duration, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM videos WHERE id = ?");
        return $stmt->execute([$id]);
    }


    
    public function search($query) {
        $stmt = $this->pdo->prepare("
            SELECT v.*, c.name as category_name 
            FROM videos v 
            JOIN video_categories c ON v.category_id = c.id 
            WHERE v.title LIKE ? OR v.description LIKE ?
            ORDER BY v.created_at DESC
        ");
        $term = "%$query%";
        $stmt->execute([$term, $term]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getSuggested($excludeId, $limit = 5) {
         $stmt = $this->pdo->prepare("
            SELECT v.*, c.name as category_name 
            FROM videos v 
            JOIN video_categories c ON v.category_id = c.id 
            WHERE v.id != ?
            ORDER BY RAND()
            LIMIT " . (int)$limit
        );
        $stmt->execute([$excludeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

<?php
// src/Models/VideoCategory.php

class VideoCategory {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM video_categories ORDER BY created_at ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM video_categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($name, $icon = 'movie') {
        $stmt = $this->pdo->prepare("INSERT INTO video_categories (name, icon) VALUES (?, ?)");
        return $stmt->execute([$name, $icon]);
    }

    public function update($id, $name, $icon) {
        $stmt = $this->pdo->prepare("UPDATE video_categories SET name = ?, icon = ? WHERE id = ?");
        return $stmt->execute([$name, $icon, $id]);
    }

    public function delete($id) {
        // Videos will cascade delete due to foreign key
        $stmt = $this->pdo->prepare("DELETE FROM video_categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

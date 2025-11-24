<?php
// src/Controllers/AdminController.php
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Class.php';

class AdminController {
    private $userModel;
    private $classModel;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
        $this->classModel = new ClassModel($pdo);
    }

    public function users() {
        return $this->userModel->all();
    }

    public function parents() {
        return $this->userModel->parentsWithChildCount();
    }

    public function classes() {
        return $this->classModel->all();
    }

    // Teaching Books Management
    public function teachingBooks() {
        $stmt = $this->pdo->query("SELECT * FROM teaching_books ORDER BY volume_number ASC");
        return $stmt->fetchAll();
    }

    public function createTeachingBook($volume_number, $title, $total_pages) {
        $stmt = $this->pdo->prepare("INSERT INTO teaching_books (volume_number, title, total_pages) VALUES (?, ?, ?)");
        return $stmt->execute([$volume_number, $title, $total_pages]);
    }

    public function getTeachingBook($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM teaching_books WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateTeachingBook($id, $volume_number, $title, $total_pages) {
        $stmt = $this->pdo->prepare("UPDATE teaching_books SET volume_number = ?, title = ?, total_pages = ? WHERE id = ?");
        return $stmt->execute([$volume_number, $title, $total_pages, $id]);
    }

    public function deleteTeachingBook($id) {
        $stmt = $this->pdo->prepare("DELETE FROM teaching_books WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getAllTeachingBooks() {
        $stmt = $this->pdo->query("SELECT * FROM teaching_books ORDER BY volume_number ASC");
        return $stmt->fetchAll();
    }

    // Short Prayers Management (Doa-doa Pendek)
    public function getShortPrayers() {
        $stmt = $this->pdo->query("SELECT * FROM short_prayers ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public function createShortPrayer($title, $arabic_text, $translation) {
        $stmt = $this->pdo->prepare("INSERT INTO short_prayers (title, arabic_text, translation) VALUES (?, ?, ?)");
        return $stmt->execute([$title, $arabic_text, $translation]);
    }

    public function getShortPrayer($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM short_prayers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateShortPrayer($id, $title, $arabic_text, $translation) {
        $stmt = $this->pdo->prepare("UPDATE short_prayers SET title = ?, arabic_text = ?, translation = ? WHERE id = ?");
        return $stmt->execute([$title, $arabic_text, $translation, $id]);
    }

    public function deleteShortPrayer($id) {
        $stmt = $this->pdo->prepare("DELETE FROM short_prayers WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

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
}

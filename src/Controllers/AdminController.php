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

    public function users($role = null) {
        if ($role) {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE role = ? AND school_id = ? ORDER BY name ASC");
            $stmt->execute([$role, (int)$_SESSION['school_id']]);
            return $stmt->fetchAll();
        }
        return $this->userModel->all((int)$_SESSION['school_id']);
    }

    public function parents() {
        return $this->userModel->parentsWithChildCount((int)$_SESSION['school_id']);
    }

    public function teachers() {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE role = 'teacher' AND school_id = ? ORDER BY name ASC");
        $stmt->execute([(int)$_SESSION['school_id']]);
        return $stmt->fetchAll();
    }

    public function classes() {
        return $this->classModel->all((int)$_SESSION['school_id']);
    }

    // Teaching Books Management
    public function teachingBooks() {
        $stmt = $this->pdo->prepare("SELECT * FROM teaching_books WHERE school_id = ? ORDER BY volume_number ASC");
        $stmt->execute([(int)$_SESSION['school_id']]);
        return $stmt->fetchAll();
    }

    public function createTeachingBook($volume_number, $title, $total_pages) {
        $stmt = $this->pdo->prepare("INSERT INTO teaching_books (volume_number, title, total_pages, school_id) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$volume_number, $title, $total_pages, (int)$_SESSION['school_id']]);
    }

    public function getTeachingBook($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM teaching_books WHERE id = ? AND school_id = ?");
        $stmt->execute([$id, (int)$_SESSION['school_id']]);
        return $stmt->fetch();
    }

    public function updateTeachingBook($id, $volume_number, $title, $total_pages) {
        $stmt = $this->pdo->prepare("UPDATE teaching_books SET volume_number = ?, title = ?, total_pages = ? WHERE id = ? AND school_id = ?");
        return $stmt->execute([$volume_number, $title, $total_pages, $id, (int)$_SESSION['school_id']]);
    }

    public function deleteTeachingBook($id) {
        $stmt = $this->pdo->prepare("DELETE FROM teaching_books WHERE id = ? AND school_id = ?");
        return $stmt->execute([$id, (int)$_SESSION['school_id']]);
    }

    public function getAllTeachingBooks() {
        $stmt = $this->pdo->query("SELECT * FROM teaching_books ORDER BY volume_number ASC");
        return $stmt->fetchAll();
    }

    // Short Prayers Management (Doa-doa Pendek)
    public function getShortPrayers() {
        $stmt = $this->pdo->prepare("SELECT * FROM short_prayers WHERE school_id = ? ORDER BY id ASC");
        $stmt->execute([(int)$_SESSION['school_id']]);
        return $stmt->fetchAll();
    }

    public function createShortPrayer($title, $arabic_text, $translation) {
        $stmt = $this->pdo->prepare("INSERT INTO short_prayers (title, arabic_text, translation, school_id) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$title, $arabic_text, $translation, (int)$_SESSION['school_id']]);
    }

    public function getShortPrayer($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM short_prayers WHERE id = ? AND school_id = ?");
        $stmt->execute([$id, (int)$_SESSION['school_id']]);
        return $stmt->fetch();
    }

    public function updateShortPrayer($id, $title, $arabic_text, $translation) {
        $stmt = $this->pdo->prepare("UPDATE short_prayers SET title = ?, arabic_text = ?, translation = ? WHERE id = ? AND school_id = ?");
        return $stmt->execute([$title, $arabic_text, $translation, $id, (int)$_SESSION['school_id']]);
    }

    public function deleteShortPrayer($id) {
        $stmt = $this->pdo->prepare("DELETE FROM short_prayers WHERE id = ? AND school_id = ?");
        return $stmt->execute([$id, (int)$_SESSION['school_id']]);
    }

    // Hadiths Management
    public function getHadiths() {
        $stmt = $this->pdo->prepare("SELECT * FROM hadiths WHERE school_id = ? ORDER BY id ASC");
        $stmt->execute([(int)$_SESSION['school_id']]);
        return $stmt->fetchAll();
    }

    public function createHadith($title, $arabic_text, $translation) {
        $stmt = $this->pdo->prepare("INSERT INTO hadiths (title, arabic_text, translation, school_id) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$title, $arabic_text, $translation, (int)$_SESSION['school_id']]);
    }

    public function getHadith($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM hadiths WHERE id = ? AND school_id = ?");
        $stmt->execute([$id, (int)$_SESSION['school_id']]);
        return $stmt->fetch();
    }

    public function updateHadith($id, $title, $arabic_text, $translation) {
        $stmt = $this->pdo->prepare("UPDATE hadiths SET title = ?, arabic_text = ?, translation = ? WHERE id = ? AND school_id = ?");
        return $stmt->execute([$title, $arabic_text, $translation, $id, (int)$_SESSION['school_id']]);
    }

    public function deleteHadith($id) {
        $stmt = $this->pdo->prepare("DELETE FROM hadiths WHERE id = ? AND school_id = ?");
        return $stmt->execute([$id, (int)$_SESSION['school_id']]);
    }

    public function getChildren($class_id = null) {
        if ($class_id) {
            $stmt = $this->pdo->prepare("
                SELECT c.*, cl.name AS class_name, u.name AS parent_name
                FROM children c
                LEFT JOIN classes cl ON c.class_id = cl.id
                LEFT JOIN users u ON c.parent_id = u.id
                WHERE c.class_id = ? AND c.school_id = ?
                ORDER BY c.name ASC
            ");
            $stmt->execute([$class_id, (int)$_SESSION['school_id']]);
        } else {
            $stmt = $this->pdo->prepare("
                SELECT c.*, cl.name AS class_name, u.name AS parent_name
                FROM children c
                LEFT JOIN classes cl ON c.class_id = cl.id
                LEFT JOIN users u ON c.parent_id = u.id
                WHERE c.school_id = ?
                ORDER BY c.name ASC
            ");
            $stmt->execute([(int)$_SESSION['school_id']]);
        }
        return $stmt->fetchAll();
    }
}

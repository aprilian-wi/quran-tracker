<?php
// src/Controllers/AdminController.php
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Class.php';

class AdminController
{
    private $userModel;
    private $classModel;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
        $this->classModel = new ClassModel($pdo);
    }

    public function users($role = null)
    {
        if ($role) {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE role = ? AND school_id = ? ORDER BY name ASC");
            $stmt->execute([$role, (int) $_SESSION['school_id']]);
            return $stmt->fetchAll();
        }
        return $this->userModel->all((int) $_SESSION['school_id']);
    }

    public function parents()
    {
        $search = isset($_GET['search']) ? trim($_GET['search']) : null;
        $schoolSearch = isset($_GET['school_q']) ? trim($_GET['school_q']) : null;

        $school_id = isGlobalAdmin() ? null : (int) $_SESSION['school_id'];

        return $this->userModel->parentsWithChildCount($school_id, [
            'search' => $search,
            'school_search' => $schoolSearch
        ]);
    }

    public function teachers($filters = [])
    {
        $search = $filters['search'] ?? '';
        $schoolSearch = $filters['school_search'] ?? '';

        $params = [];
        $where = ["role = 'teacher'"];

        // Check if Superadmin (Global Admin)
        if (isGlobalAdmin()) {
            // Superadmin can search by school name
            $query = "SELECT u.*, s.name as school_name 
                      FROM users u 
                      LEFT JOIN schools s ON u.school_id = s.id";

            if (!empty($schoolSearch)) {
                $where[] = "s.name LIKE ?";
                $params[] = "%$schoolSearch%";
            }
        } else {
            // Regular admin forced to their school
            $query = "SELECT u.* FROM users u";
            $where[] = "school_id = ?";
            $params[] = (int) $_SESSION['school_id'];
        }

        // Search by teacher name
        if (!empty($search)) {
            $where[] = "u.name LIKE ?";
            $params[] = "%$search%";
        }

        if (!empty($where)) {
            $query .= " WHERE " . implode(' AND ', $where);
        }

        $query .= " ORDER BY u.created_at DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function classes()
    {
        return $this->classModel->all((int) $_SESSION['school_id']);
    }

    // Teaching Books Management
    public function teachingBooks()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM teaching_books WHERE school_id = ? ORDER BY volume_number ASC");
        $stmt->execute([(int) $_SESSION['school_id']]);
        return $stmt->fetchAll();
    }

    public function createTeachingBook($volume_number, $title, $total_pages)
    {
        $stmt = $this->pdo->prepare("INSERT INTO teaching_books (volume_number, title, total_pages, school_id) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$volume_number, $title, $total_pages, (int) $_SESSION['school_id']]);
    }

    public function getTeachingBook($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM teaching_books WHERE id = ? AND school_id = ?");
        $stmt->execute([$id, (int) $_SESSION['school_id']]);
        return $stmt->fetch();
    }

    public function updateTeachingBook($id, $volume_number, $title, $total_pages)
    {
        $stmt = $this->pdo->prepare("UPDATE teaching_books SET volume_number = ?, title = ?, total_pages = ? WHERE id = ? AND school_id = ?");
        return $stmt->execute([$volume_number, $title, $total_pages, $id, (int) $_SESSION['school_id']]);
    }

    public function deleteTeachingBook($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM teaching_books WHERE id = ? AND school_id = ?");
        return $stmt->execute([$id, (int) $_SESSION['school_id']]);
    }

    public function getAllTeachingBooks()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM teaching_books WHERE school_id = ? ORDER BY volume_number ASC");
        $stmt->execute([(int) $_SESSION['school_id']]);
        return $stmt->fetchAll();
    }

    // Short Prayers Management (Doa-doa Pendek)
    public function getShortPrayers()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM short_prayers WHERE school_id = ? ORDER BY id ASC");
        $stmt->execute([(int) $_SESSION['school_id']]);
        return $stmt->fetchAll();
    }

    public function createShortPrayer($title, $arabic_text, $translation)
    {
        $stmt = $this->pdo->prepare("INSERT INTO short_prayers (title, arabic_text, translation, school_id) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$title, $arabic_text, $translation, (int) $_SESSION['school_id']]);
    }

    public function getShortPrayer($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM short_prayers WHERE id = ? AND school_id = ?");
        $stmt->execute([$id, (int) $_SESSION['school_id']]);
        return $stmt->fetch();
    }

    public function updateShortPrayer($id, $title, $arabic_text, $translation)
    {
        $stmt = $this->pdo->prepare("UPDATE short_prayers SET title = ?, arabic_text = ?, translation = ? WHERE id = ? AND school_id = ?");
        return $stmt->execute([$title, $arabic_text, $translation, $id, (int) $_SESSION['school_id']]);
    }

    public function deleteShortPrayer($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM short_prayers WHERE id = ? AND school_id = ?");
        return $stmt->execute([$id, (int) $_SESSION['school_id']]);
    }

    // Hadiths Management
    public function getHadiths()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM hadiths WHERE school_id = ? ORDER BY id ASC");
        $stmt->execute([(int) $_SESSION['school_id']]);
        return $stmt->fetchAll();
    }

    public function createHadith($title, $arabic_text, $translation)
    {
        $stmt = $this->pdo->prepare("INSERT INTO hadiths (title, arabic_text, translation, school_id) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$title, $arabic_text, $translation, (int) $_SESSION['school_id']]);
    }

    public function getHadith($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM hadiths WHERE id = ? AND school_id = ?");
        $stmt->execute([$id, (int) $_SESSION['school_id']]);
        return $stmt->fetch();
    }

    public function updateHadith($id, $title, $arabic_text, $translation)
    {
        $stmt = $this->pdo->prepare("UPDATE hadiths SET title = ?, arabic_text = ?, translation = ? WHERE id = ? AND school_id = ?");
        return $stmt->execute([$title, $arabic_text, $translation, $id, (int) $_SESSION['school_id']]);
    }

    public function deleteHadith($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM hadiths WHERE id = ? AND school_id = ?");
        return $stmt->execute([$id, (int) $_SESSION['school_id']]);
    }

    public function getChildren($class_id = null, $filters = [])
    {
        $schoolSearch = $filters['school_search'] ?? null;

        $sql = "
            SELECT c.*, cl.name AS class_name, u.name AS parent_name, s.name as school_name
            FROM children c
            LEFT JOIN classes cl ON c.class_id = cl.id
            LEFT JOIN users u ON c.parent_id = u.id
            LEFT JOIN schools s ON c.school_id = s.id
        ";

        $params = [];
        $where = [];

        if (isGlobalAdmin()) {
            // Superadmin can see from all schools
            if ($schoolSearch) {
                $where[] = "s.name LIKE ?";
                $params[] = "%$schoolSearch%";
            }
        } else {
            // School admin limited to their school
            $where[] = "c.school_id = ?";
            $params[] = (int) $_SESSION['school_id'];
        }

        if ($class_id) {
            $where[] = "c.class_id = ?";
            $params[] = $class_id;
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " ORDER BY c.name ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}

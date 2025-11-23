<?php
// src/Models/Child.php
class Child {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($data) {
        $sql = "INSERT INTO children (name, parent_id, class_id, date_of_birth) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['parent_id'],
            $data['class_id'] ?? null,
            $data['date_of_birth'] ?? null
        ]);
    }

    public function getByParent($parent_id) {
        $stmt = $this->pdo->prepare("
            SELECT c.*, cl.name as class_name 
            FROM children c 
            LEFT JOIN classes cl ON c.class_id = cl.id 
            WHERE c.parent_id = ? 
            ORDER BY c.name
        ");
        $stmt->execute([$parent_id]);
        return $stmt->fetchAll();
    }

    public function getByClass($class_id) {
        $stmt = $this->pdo->prepare("
            SELECT c.*, u.name as parent_name 
            FROM children c 
            JOIN users u ON c.parent_id = u.id 
            WHERE c.class_id = ?
            ORDER BY c.name
        ");
        $stmt->execute([$class_id]);
        return $stmt->fetchAll();
    }

    public function find($id, $user_id = null, $role = null) {
        $sql = "SELECT c.*, cl.name as class_name, u.name as parent_name
                FROM children c
                LEFT JOIN classes cl ON c.class_id = cl.id
                LEFT JOIN users u ON c.parent_id = u.id
                WHERE c.id = ?";
        if ($role === 'superadmin') {
            // No additional restrictions for superadmin
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
        } elseif ($role === 'teacher') {
            // Check if the child's class is owned by the teacher
            $sql .= " AND EXISTS (SELECT 1 FROM classes_teachers ct WHERE ct.class_id = c.class_id AND ct.teacher_id = ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id, $user_id]);
        } elseif ($role === 'parent') {
            $sql .= " AND c.parent_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id, $user_id]);
        } else {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
        }
        return $stmt->fetch();
    }

    public function total() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM children");
        return $stmt->fetchColumn();
    }

    public function getUnassignedChildren() {
        $stmt = $this->pdo->query("
            SELECT c.id, c.name, c.parent_id, u.name as parent_name 
            FROM children c
            JOIN users u ON c.parent_id = u.id
            WHERE c.class_id IS NULL
            ORDER BY c.name
        ");
        return $stmt->fetchAll();
    }

    public function getAvailableForClass($class_id) {
        $stmt = $this->pdo->prepare("
            SELECT c.id, c.name, c.parent_id, u.name as parent_name 
            FROM children c
            JOIN users u ON c.parent_id = u.id
            WHERE c.class_id IS NULL OR c.class_id != ?
            ORDER BY c.name
        ");
        $stmt->execute([$class_id]);
        return $stmt->fetchAll();
    }

    public function assignToClass($child_id, $class_id) {
        $stmt = $this->pdo->prepare("UPDATE children SET class_id = ? WHERE id = ?");
        return $stmt->execute([$class_id, $child_id]);
    }

    public function unassignFromClass($child_id) {
        $stmt = $this->pdo->prepare("UPDATE children SET class_id = NULL WHERE id = ?");
        return $stmt->execute([$child_id]);
    }
}
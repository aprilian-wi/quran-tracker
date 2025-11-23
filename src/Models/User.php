<?php
// src/Models/User.php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role']
        ]);
    }

    public function all() {
        $stmt = $this->pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    // Return parents with their child counts
    public function parentsWithChildCount() {
        $sql = "
            SELECT u.*, COUNT(c.id) as child_count
            FROM users u
            LEFT JOIN children c ON u.id = c.parent_id
            WHERE u.role = 'parent'
            GROUP BY u.id
            ORDER BY u.name
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function countByRole($role) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE role = ?");
        $stmt->execute([$role]);
        return $stmt->fetchColumn();
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        return $stmt->execute([$data['name'], $data['email'], $id]);
    }

    public function updatePassword($id, $password) {
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
    }

    public function getParentWithChildren($parent_id) {
        $user = $this->findById($parent_id);
        if (!$user) return null;

        $stmt = $this->pdo->prepare(
            "SELECT id, name, class_id, parent_id 
             FROM children 
             WHERE parent_id = ? 
             ORDER BY name"
        );
        $stmt->execute([$parent_id]);
        $user['children'] = $stmt->fetchAll();
        return $user;
    }
}
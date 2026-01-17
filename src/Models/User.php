<?php
// src/Models/User.php
class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // findByEmail removed as column is dropped


    public function findByPhone($phone)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        return $stmt->fetch();
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $sql = "INSERT INTO users (name, phone, password, role, school_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['phone'] ?? null,
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'],
            $data['school_id'] ?? 1
        ]);
    }

    public function all($school_id = null)
    {
        $sql = "SELECT id, name, phone, role, created_at, school_id FROM users";
        $params = [];
        if ($school_id) {
            $sql .= " WHERE school_id = ?";
            $params[] = $school_id;
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Return parents with their child counts
    public function parentsWithChildCount($school_id = null, $filters = [])
    {
        $search = $filters['search'] ?? null;
        $schoolSearch = $filters['school_search'] ?? null;

        $sql = "
            SELECT u.*, s.name as school_name, COUNT(c.id) as child_count
            FROM users u
            LEFT JOIN children c ON u.id = c.parent_id
            LEFT JOIN schools s ON u.school_id = s.id
            WHERE u.role = 'parent'
        ";

        $params = [];

        if ($school_id) {
            $sql .= " AND u.school_id = ?";
            $params[] = (int) $school_id;
        }

        if ($schoolSearch) {
            $sql .= " AND s.name LIKE ?";
            $params[] = "%$schoolSearch%";
        }

        if ($search) {
            $sql .= " AND (u.name LIKE ? OR u.phone LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " GROUP BY u.id ORDER BY u.name";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countByRole($role, $school_id = null)
    {
        $sql = "SELECT COUNT(*) FROM users WHERE role = ?";
        $params = [$role];

        if ($school_id) {
            $sql .= " AND school_id = ?";
            $params[] = $school_id;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function update($id, $data)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['phone'] ?? null, $id]);
        return true;
    }

    public function updatePassword($id, $password)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
    }

    public function getParentWithChildren($parent_id)
    {
        $user = $this->findById($parent_id);
        if (!$user)
            return null;

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
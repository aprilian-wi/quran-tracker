<?php

class School
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("
            SELECT s.*, 
            (SELECT COUNT(*) FROM users WHERE school_id = s.id AND role = 'teacher') as teacher_count,
            (SELECT COUNT(*) FROM users WHERE school_id = s.id AND role = 'parent') as parent_count,
            (SELECT COUNT(*) FROM classes WHERE school_id = s.id) as class_count
            FROM schools s 
            ORDER BY s.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM schools WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findBySlug($slug)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM schools WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO schools ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));

        return $this->pdo->lastInsertId();
    }

    public function update($id, $data)
    {
        $fields = [];
        $params = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }

        if (empty($fields)) {
            return true;
        }

        $sql = "UPDATE schools SET " . implode(', ', $fields) . " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM schools WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

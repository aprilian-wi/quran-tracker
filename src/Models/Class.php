<?php
// src/Models/Class.php
class ClassModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($name, $teacher_id, $school_id) {
        $stmt = $this->pdo->prepare("INSERT INTO classes (name, teacher_id, school_id) VALUES (?, ?, ?)");
        $ok = $stmt->execute([$name, $teacher_id, $school_id]);
        if (!$ok) {
            return false; // Insert failed
        }
        
        $class_id = (int)$this->pdo->lastInsertId();
        
        // If teacher_id provided, also insert into classes_teachers
        if ($teacher_id) {
            $m = $this->pdo->prepare("INSERT INTO classes_teachers (class_id, teacher_id) VALUES (?, ?)");
            // ignore duplicate errors
            @$m->execute([$class_id, $teacher_id]);
        }
        
        // Return the class_id on success
        return $class_id;
    }

    public function all($school_id = null) {
        // Return classes with aggregated teacher names and student counts
        $sql = "SELECT c.*, GROUP_CONCAT(u.name SEPARATOR ', ') as teacher_names, 
                    (SELECT COUNT(*) FROM children ch WHERE ch.class_id = c.id) as student_count
             FROM classes c
             LEFT JOIN classes_teachers ct ON ct.class_id = c.id
             LEFT JOIN users u ON ct.teacher_id = u.id";
             
        if ($school_id) {
            $sql .= " WHERE c.school_id = " . (int)$school_id;
        }

        $sql .= " GROUP BY c.id ORDER BY c.name";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getByTeacher($teacher_id) {
        $stmt = $this->pdo->prepare(
            "SELECT c.*, (SELECT COUNT(*) FROM children ch WHERE ch.class_id = c.id) as student_count
             FROM classes c
             JOIN classes_teachers ct ON ct.class_id = c.id
             WHERE ct.teacher_id = ?
             ORDER BY c.name"
        );
        $stmt->execute([$teacher_id]);
        return $stmt->fetchAll();
    }

    public function assignChild($child_id, $class_id) {
        $stmt = $this->pdo->prepare("UPDATE children SET class_id = ? WHERE id = ?");
        return $stmt->execute([$class_id, $child_id]);
    }

    public function isOwnedBy($class_id, $teacher_id) {
        $stmt = $this->pdo->prepare("SELECT class_id FROM classes_teachers WHERE class_id = ? AND teacher_id = ?");
        $stmt->execute([$class_id, $teacher_id]);
        return (bool) $stmt->fetch();
    }

    public function assignTeacher($class_id, $teacher_id) {
        $stmt = $this->pdo->prepare("INSERT INTO classes_teachers (class_id, teacher_id) VALUES (?, ?)");
        return @$stmt->execute([$class_id, $teacher_id]);
    }

    public function total($school_id = null) {
        if ($school_id) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM classes WHERE school_id = ?");
            $stmt->execute([$school_id]);
        } else {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM classes");
        }
        return $stmt->fetchColumn();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM classes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getWithTeachers($id) {
        $class = $this->find($id);
        if (!$class) return null;

        $stmt = $this->pdo->prepare(
            "SELECT u.id, u.name FROM users u
             JOIN classes_teachers ct ON u.id = ct.teacher_id
             WHERE ct.class_id = ?"
        );
        $stmt->execute([$id]);
        $class['teachers'] = $stmt->fetchAll();
        return $class;
    }

    public function getStudents($class_id) {
        $stmt = $this->pdo->prepare(
            "SELECT c.id, c.name, c.parent_id, u.name as parent_name 
             FROM children c
             LEFT JOIN users u ON c.parent_id = u.id
             WHERE c.class_id = ?
             ORDER BY c.name"
        );
        $stmt->execute([$class_id]);
        return $stmt->fetchAll();
    }

    public function updateName($id, $name) {
        $stmt = $this->pdo->prepare("UPDATE classes SET name = ? WHERE id = ?");
        return $stmt->execute([$name, $id]);
    }

    public function deleteTeacher($class_id, $teacher_id) {
        $stmt = $this->pdo->prepare("DELETE FROM classes_teachers WHERE class_id = ? AND teacher_id = ?");
        return $stmt->execute([$class_id, $teacher_id]);
    }

    public function removeStudent($child_id) {
        $stmt = $this->pdo->prepare("UPDATE children SET class_id = NULL WHERE id = ?");
        return $stmt->execute([$child_id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM classes WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
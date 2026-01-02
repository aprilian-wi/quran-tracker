<?php
// src/Controllers/SystemAdminController.php
require_once __DIR__ . '/../Models/User.php';

class SystemAdminController {
    private $pdo;
    private $userModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
    }

    public function getAllSchools() {
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

    public function getSchool($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM schools WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateSchool($id, $name, $address) {
        $stmt = $this->pdo->prepare("UPDATE schools SET name = ?, address = ? WHERE id = ?");
        return $stmt->execute([$name, $address, $id]);
    }

    public function deleteSchool($id) {
        // Warning: This cascades via FKs usually, but let's be safe.
        // For now, simple delete. DB constraints handle cascade or fail.
        $stmt = $this->pdo->prepare("DELETE FROM schools WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function createSchool($name, $adminName, $adminEmail, $adminPassword) {
        try {
            $this->pdo->beginTransaction();

            // Create School
            $stmt = $this->pdo->prepare("INSERT INTO schools (name) VALUES (?)");
            $stmt->execute([$name]);
            $schoolId = $this->pdo->lastInsertId();

            // Create Admin for that school
            $this->userModel->create([
                'name' => $adminName,
                'email' => $adminEmail,
                'password' => $adminPassword,
                'role' => 'school_admin',
                'school_id' => $schoolId
            ]);

            $this->pdo->commit();
            return ['success' => true, 'message' => "School '$name' created successfully."];
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollback();
            }
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function getSchoolAdmins($schoolId) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE school_id = ? AND role = 'school_admin' ORDER BY name ASC");
        $stmt->execute([$schoolId]);
        return $stmt->fetchAll();
    }

    public function getSchoolAdmin($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'school_admin'");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateSchoolAdmin($id, $name, $email, $password = null) {
        $startedTransaction = false;
        try {
            if (!$this->pdo->inTransaction()) {
                $this->pdo->beginTransaction();
                $startedTransaction = true;
            }
            
            $this->userModel->update($id, ['name' => $name, 'email' => $email]);
            
            if (!empty($password)) {
                $this->userModel->updatePassword($id, $password);
            }
            
            if ($startedTransaction) {
                $this->pdo->commit();
            }
            return true;
        } catch (Exception $e) {
            if ($startedTransaction && $this->pdo->inTransaction()) {
                $this->pdo->rollback();
            }
            // If we didn't start it, we re-throw or return false? 
            // Returning false handles the error gracefully for the UI.
            error_log("Update School Admin Error: " . $e->getMessage());
            return false;
        }
    }
}

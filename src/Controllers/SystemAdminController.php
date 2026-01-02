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
}

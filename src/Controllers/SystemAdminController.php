<?php
// src/Controllers/SystemAdminController.php
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/School.php';

class SystemAdminController
{
    private $pdo;
    private $userModel;
    private $schoolModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
        $this->schoolModel = new School($pdo);
    }

    public function getAllSchools()
    {
        return $this->schoolModel->getAll();
    }

    public function getSchool($id)
    {
        return $this->schoolModel->find($id);
    }

    public function updateSchool($id, $data)
    {
        return $this->schoolModel->update($id, $data);
    }

    public function deleteSchool($id)
    {
        return $this->schoolModel->delete($id);
    }

    public function createSchool($schoolData, $adminName, $adminPhone, $adminPassword)
    {
        try {
            $this->pdo->beginTransaction();

            // Create School
            $schoolId = $this->schoolModel->create($schoolData);

            // Create Admin for that school
            $this->userModel->create([
                'name' => $adminName,
                'phone' => $adminPhone,
                'password' => $adminPassword,
                'role' => 'school_admin',
                'school_id' => $schoolId
            ]);

            $this->pdo->commit();
            return ['success' => true, 'message' => "School '{$schoolData['name']}' created successfully."];
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollback();
            }
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function getSchoolAdmins($schoolId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE school_id = ? AND role = 'school_admin' ORDER BY name ASC");
        $stmt->execute([$schoolId]);
        return $stmt->fetchAll();
    }

    public function getSchoolAdmin($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'school_admin'");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateSchoolAdmin($id, $name, $phone, $password = null)
    {
        $startedTransaction = false;
        try {
            if (!$this->pdo->inTransaction()) {
                $this->pdo->beginTransaction();
                $startedTransaction = true;
            }

            $this->userModel->update($id, ['name' => $name, 'phone' => $phone]);

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

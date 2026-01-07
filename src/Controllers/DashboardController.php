<?php
// src/Controllers/DashboardController.php
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Child.php';
require_once __DIR__ . '/../Models/Class.php';
require_once __DIR__ . '/../Models/Progress.php';

class DashboardController {
    private $pdo;
    private $userModel;
    private $childModel;
    private $classModel;
    private $progressModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
        $this->childModel = new Child($pdo);
        $this->classModel = new ClassModel($pdo);
        $this->progressModel = new Progress($pdo);
    }

    public function index() {
        $role = $_SESSION['role'];
        $data = [];

        switch ($role) {
            case 'superadmin':
                // Superadmin sees system-wide stats
                $data['total_teachers'] = $this->userModel->countByRole('teacher', null);
                $data['total_parents'] = $this->userModel->countByRole('parent', null);
                $data['total_children'] = $this->childModel->total(null);
                $data['total_classes'] = $this->classModel->total(null);
                $data['total_schools'] = $this->pdo->query("SELECT COUNT(*) FROM schools")->fetchColumn();
                break;

            case 'school_admin':
                 // School admin sees only their school stats
                $school_id = $_SESSION['school_id'];
                $data['total_teachers'] = $this->userModel->countByRole('teacher', $school_id);
                $data['total_parents'] = $this->userModel->countByRole('parent', $school_id);
                $data['total_children'] = $this->childModel->total($school_id);
                $data['total_classes'] = $this->classModel->total($school_id);
                break;

            case 'teacher':
                $data['classes'] = $this->classModel->getByTeacher($_SESSION['user_id']);
                $data['total_students'] = 0;
                foreach ($data['classes'] as $class) {
                    $data['total_students'] += $class['student_count'];
                }
                break;

            case 'parent':
                $data['children'] = $this->childModel->getByParent($_SESSION['user_id']);
                foreach ($data['children'] as &$child) {
                    $child['progress'] = $this->progressModel->getProgressSummary($child['id']);
                    $child['notifications'] = $this->progressModel->getUnreadNotifications($child['id']);
                }
                break;
        }

        return $data;
    }
}
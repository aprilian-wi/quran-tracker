<?php
// src/Controllers/DashboardController.php
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Child.php';
require_once __DIR__ . '/../Models/Class.php';
require_once __DIR__ . '/../Models/Progress.php';

class DashboardController {
    private $userModel;
    private $childModel;
    private $classModel;
    private $progressModel;

    public function __construct($pdo) {
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
                $data['total_teachers'] = $this->userModel->countByRole('teacher');
                $data['total_parents'] = $this->userModel->countByRole('parent');
                $data['total_children'] = $this->childModel->total();
                $data['total_classes'] = $this->classModel->total();
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
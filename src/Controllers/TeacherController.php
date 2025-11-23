<?php
// src/Controllers/TeacherController.php
require_once __DIR__ . '/../Models/Class.php';
require_once __DIR__ . '/../Models/Child.php';

class TeacherController {
    private $classModel;
    private $childModel;

    public function __construct($pdo) {
        $this->classModel = new ClassModel($pdo);
        $this->childModel = new Child($pdo);
    }

    public function classStudents($class_id) {
        // If current user is a teacher, verify they own the class.
        // Superadmin may view any class.
        if (hasRole('teacher')) {
            if (!$this->classModel->isOwnedBy($class_id, $_SESSION['user_id'])) {
                setFlash('danger', 'Access denied.');
                redirect('dashboard');
            }
        }

        return $this->childModel->getByClass($class_id);
    }
}
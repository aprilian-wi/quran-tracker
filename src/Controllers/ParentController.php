<?php
// src/Controllers/ParentController.php
require_once __DIR__ . '/../Models/Child.php';

class ParentController {
    private $childModel;

    public function __construct($pdo) {
        $this->childModel = new Child($pdo);
    }

    public function myChildren() {
        // Allow superadmin/teacher to view a specific parent's children via ?parent_id=
        $parent_id = isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : null;

        if (hasRole('parent')) {
            $pid = $_SESSION['user_id'];
        } else {
            // superadmin or teacher
            if ($parent_id) {
                $pid = $parent_id;
            } else {
                // fallback to session user if available
                $pid = $_SESSION['user_id'] ?? 0;
            }
        }

        return $this->childModel->getByParent($pid);
    }

    public function viewChild($child_id) {
        $child = $this->childModel->find($child_id, $_SESSION['user_id'], 'parent');
        if (!$child) {
            setFlash('danger', 'Child not found.');
            redirect('parent/my_children');
        }
        return $child;
    }
}
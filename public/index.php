<?php
// public/index.php
session_start();
require '../config/database.php';
require '../src/Helpers/functions.php';  // HANYA DI SINI!

// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$page = $_GET['page'] ?? 'login';

// === HALAMAN YANG WAJIB LOGIN ===
$authPages = ['dashboard', 'admin', 'teacher', 'parent', 'logout', 'update_progress', 'update_progress_books', 'create_parent', 'create_teacher', 'assign_class', 'delete_user', 'edit_parent', 'delete_parent', 'delete_teacher', 'admin/create_school', 'admin/store_school'];
if (in_array($page, $authPages)) {
    requireLogin();
}

// === ROLE CHECK ===
$superadminPages = ['admin/users', 'admin/parents', 'admin/classes', 'admin/edit_class', 'admin/teaching_books', 'admin/create_teaching_book', 'admin/edit_teaching_book', 'admin/store_teaching_book', 'admin/update_teaching_book', 'admin/delete_teaching_book', 'edit_parent', 'edit_teacher', 'create_parent', 'create_teacher', 'delete_user', 'edit_class', 'delete_parent', 'delete_teacher'];
// Pages accessible only to System Admin (School ID 1)
$globalAdminPages = [
    'admin/schools',
    'admin/create_school',
    'admin/store_school',
    'admin/edit_school',
    'admin/update_school',
    'admin/edit_school',
    'admin/update_school',
    'admin/delete_school',
    'admin/edit_school_admin',
    'admin/update_school_admin'
];

if (in_array($page, $globalAdminPages) && !isGlobalAdmin()) die('Access denied: System Admin only');
// Allow both superadmin and school_admin to access general admin pages
if (in_array($page, $superadminPages) && !(hasRole('superadmin') || hasRole('school_admin'))) die('Access denied: Admin only');
$teacherPages = ['teacher/class_students', 'teacher/update_progress', 'teacher/update_progress_books', 'teacher/update_progress_prayers', 'teacher/update_progress_hadiths', 'teacher/update_profile', 'assign_class'];
$parentPages = ['parent/my_children', 'parent/update_progress', 'parent/update_progress_books', 'parent/update_progress_prayers', 'parent/update_progress_hadiths', 'update_progress', 'update_progress_books', 'update_progress_prayers', 'update_progress_hadiths'];


// Allow teachers and superadmin to access teacher pages
if (in_array($page, $teacherPages) && !(hasRole('teacher') || hasRole('superadmin') || hasRole('school_admin'))) die('Access denied: Teacher only');
// Parent pages: parents can access their pages. Allow superadmin/teacher to view parent's children.
if (in_array($page, $parentPages)) {
    if ($page === 'parent/my_children') {
        if (!(hasRole('parent') || hasRole('superadmin') || hasRole('school_admin') || hasRole('teacher'))) {
            die('Access denied: Parent/Teacher/Admin only');
        }
    } elseif ($page === 'parent/update_progress') {
        if (!hasRole('parent') && !hasRole('superadmin') && !hasRole('school_admin')) die('Access denied: Parent/Admin only');
    } elseif ($page === 'parent/update_progress_prayers') {
        if (!hasRole('parent') && !hasRole('superadmin') && !hasRole('school_admin')) die('Access denied: Parent/Admin only');
    } elseif ($page === 'parent/update_progress_hadiths') {
        if (!hasRole('parent') && !hasRole('superadmin') && !hasRole('school_admin')) die('Access denied: Parent/Admin only');
    } elseif ($page === 'update_progress') {
        if (!(hasRole('parent') || hasRole('superadmin') || hasRole('school_admin') || hasRole('teacher'))) {
            die('Access denied: Parent/Admin/Teacher only');
        }
    }
}

// === ROUTING ===
switch ($page) {
    case 'login':
        if (isLoggedIn()) {
            redirect('dashboard');
        } else {
            include '../src/Auth/Login.php';
            include '../src/Views/auth/login.php';
        }
        break;

    case 'logout':
        session_destroy();
        redirect('login');
        break;

    case 'dashboard':
        $role = $_SESSION['role'] ?? '';
        if (!in_array($role, ['superadmin', 'teacher', 'parent', 'school_admin'])) {
            setFlash('danger', 'Role tidak valid.');
            redirect('login');
        }
        $file = "../src/Views/dashboard/{$role}.php";
        if (!file_exists($file)) {
            die("Error: File dashboard/{$role}.php tidak ditemukan.");
        }
        include $file;
        break;

    // Admin
    case 'admin/users': include '../src/Views/admin/users.php'; break;
    case 'admin/parents': include '../src/Views/admin/parents.php'; break;
    case 'admin/classes': include '../src/Views/admin/classes.php'; break;
    case 'admin/edit_class': include '../src/Views/admin/edit_class.php'; break;
    case 'admin/teaching_books': include '../src/Views/admin/teaching_books.php'; break;
    case 'admin/create_teaching_book': include '../src/Views/admin/create_teaching_book.php'; break;
    case 'admin/edit_teaching_book': include '../src/Views/admin/edit_teaching_book.php'; break;
    case 'admin/manage_short_prayers': include '../src/Views/admin/manage_short_prayers.php'; break;
    case 'admin/save_short_prayer': include '../src/Actions/store_short_prayer_action.php'; break;
    case 'admin/create_short_prayer': include '../src/Views/admin/create_short_prayer.php'; break;
    case 'admin/edit_short_prayer': include '../src/Views/admin/edit_short_prayer.php'; break;
    case 'admin/manage_hadiths': include '../src/Views/admin/manage_hadiths.php'; break;
    case 'admin/create_hadith': include '../src/Views/admin/create_hadith.php'; break;
    case 'admin/edit_hadith': include '../src/Views/admin/edit_hadith.php'; break;
    case 'admin/save_hadith': include '../src/Actions/store_hadith_action.php'; break;
    case 'admin/delete_hadith': include '../src/Actions/delete_hadith_action.php'; break;
    case 'admin/teachers': include '../src/Views/admin/teachers.php'; break;
    case 'admin/list_children': include '../src/Views/admin/list_children.php'; break;
    case 'admin/update_progress': include '../src/Views/admin/update_progress.php'; break;
    case 'admin/update_progress_books': include '../src/Views/admin/update_progress_books.php'; break;
    case 'admin/update_progress_prayers': include '../src/Views/admin/update_progress_prayers.php'; break;
    case 'admin/update_progress_hadiths': include '../src/Views/admin/update_progress_hadiths.php'; break;
    case 'admin/export_users': include '../src/Actions/export_users_action.php'; break;
    case 'admin/export_children': include '../src/Actions/export_children_action.php'; break;
    
    // System Admin (School Management)
    case 'admin/schools': include '../src/Views/admin/schools.php'; break;
    case 'admin/create_school': include '../src/Views/admin/create_school.php'; break;
    case 'admin/store_school': include '../src/Actions/store_school_action.php'; break;
    case 'admin/edit_school': include '../src/Views/admin/edit_school.php'; break;
    case 'admin/update_school': include '../src/Actions/update_school_action.php'; break;
    case 'admin/delete_school': include '../src/Actions/delete_school_action.php'; break;
    case 'admin/edit_school_admin': include '../src/Views/admin/edit_school_admin.php'; break;
    case 'admin/update_school_admin': include '../src/Actions/update_school_admin_action.php'; break;

    case 'edit_parent':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include '../src/Actions/edit_parent_action.php';
        } else {
            include '../src/Views/admin/edit_parent.php';
        }
        break;

    case 'delete_parent': include '../src/Actions/delete_parent_action.php'; break;

    case 'edit_teacher':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include '../src/Actions/edit_teacher_action.php';
        } else {
            include '../src/Views/admin/edit_teacher.php';
        }
        break;

    // Teacher
    case 'teacher/class_students': include '../src/Views/teacher/class_students.php'; break;
    case 'teacher/update_progress':
        $child_id = (int)($_GET['child_id'] ?? 0);
        $class_id = (int)($_GET['class_id'] ?? 0);
        if (!$child_id && !$class_id) die('Invalid child_id or class_id');
        if ($child_id) {
            require_once '../src/Models/Child.php';
            $childModel = new Child($pdo);
            $child = $childModel->find($child_id, $_SESSION['user_id'], $_SESSION['role']);
            if (!$child) die('Access denied');
        }
        include '../src/Views/teacher/update_progress.php';
        break;
    case 'teacher/update_progress_books': include '../src/Views/teacher/update_progress_books.php'; break;
    case 'teacher/update_profile':
        $teacher_id = (int)($_GET['teacher_id'] ?? 0);
        if (!$teacher_id) die('Invalid teacher_id');
        require_once '../src/Models/User.php';
        $User = new User($pdo);
        $teacher = $User->findById($teacher_id);
        if (!$teacher || $teacher['role'] !== 'teacher') die('Teacher not found');
        include '../src/Views/teacher/update_profile.php';
        break;

    // Parent
    case 'parent/my_children': include '../src/Views/parent/my_children.php'; break;
    case 'parent/update_progress':
        $child_id = (int)($_GET['child_id'] ?? 0);
        if (!$child_id) die('Invalid child_id');
        require_once '../src/Models/Child.php';
        $childModel = new Child($pdo);
        $child = $childModel->find($child_id, $_SESSION['user_id'], $_SESSION['role']);
        if (!$child) die('Access denied');
        include '../src/Views/parent/update_progress.php';
        break;
    case 'parent/update_progress_books': include '../src/Views/parent/update_progress_books.php'; break;
    case 'shared/list_short_prayers': include '../src/Views/shared/list_short_prayers.php'; break;
    case 'shared/list_hadiths': include '../src/Views/shared/list_hadiths.php'; break;

    case 'teacher/update_progress_prayers': include '../src/Views/teacher/update_progress_prayers.php'; break;
    case 'teacher/update_progress_hadiths': include '../src/Views/teacher/update_progress_hadiths.php'; break;
    case 'parent/update_progress_prayers': include '../src/Views/parent/update_progress_prayers.php'; break;
    case 'parent/update_progress_hadiths': include '../src/Views/parent/update_progress_hadiths.php'; break;
    case 'update_progress_prayers': include '../src/Actions/update_progress_prayers_action.php'; break;
    case 'update_progress_hadiths': include '../src/Actions/update_progress_hadiths_action.php'; break;
    case 'parent/upload_photo': include '../src/Actions/upload_child_photo_action.php'; break;

    // Actions
    case 'update_progress': include '../src/Actions/update_progress_action.php'; break;
    case 'update_progress_books': include '../src/Actions/update_progress_books_action.php'; break;
    case 'mark_notification_viewed': include '../src/Actions/mark_notification_viewed_action.php'; break;
    case 'export_progress_excel': include '../src/Actions/export_progress_excel_action.php'; break;
    case 'export_quran_progress_excel': include '../src/Actions/export_quran_progress_excel_action.php'; break;
    case 'export_hadith_progress_excel': include '../src/Actions/export_hadith_progress_excel_action.php'; break;
    case 'admin/store_teaching_book': include '../src/Actions/store_teaching_book_action.php'; break;
    case 'admin/update_teaching_book': include '../src/Actions/update_teaching_book_action.php'; break;
    case 'admin/delete_teaching_book': include '../src/Actions/delete_teaching_book_action.php'; break;
    case 'create_class':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include '../src/Actions/create_class_action.php';
        } else {
            redirect('admin/classes');
        }
        break;
    case 'edit_class':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include '../src/Actions/edit_class_action.php';
        } else {
            include '../src/Views/admin/edit_class.php';
        }
        break;
    case 'create_parent':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include '../src/Actions/create_parent_action.php';
        } else {
            include '../src/Views/admin/create_parent.php';
        }
        break;
    case 'create_teacher':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include '../src/Actions/create_teacher_action.php';
        } else {
            include '../src/Views/admin/create_teacher.php';
        }
        break;
    case 'add_children':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include '../src/Actions/add_children_action.php';
        } else {
            redirect('admin/parents');
        }
        break;
    case 'assign_class': include '../src/Actions/assign_class_action.php'; break;
    case 'delete_user': include '../src/Actions/delete_user_action.php'; break;
    case 'delete_teacher': include '../src/Actions/delete_teacher_action.php'; break;

    // Quran Digital Pages
    case 'quran/surah_list': include '../src/Views/quran/surah_list.php'; break;
    case 'quran/surah_detail':
        $surah = (int)($_GET['surah'] ?? 1);
        if ($surah < 1 || $surah > 114) die('Invalid surah');
        include '../src/Views/quran/surah_detail.php';
        break;
    case 'quran/search': include '../src/Views/quran/search.php'; break;
    case 'quran/bookmarks': include '../src/Views/quran/bookmarks.php'; break;

    // AJAX API Endpoints
    case 'get_surahs':
        header('Content-Type: application/json');
        $juz = $_GET['juz'] ?? 1;
        $stmt = $pdo->prepare("SELECT surah_number, surah_name_ar, surah_name_en, start_verse, end_verse FROM quran_structure WHERE juz = ? ORDER BY surah_number");
        $stmt->execute([$juz]);
        echo json_encode($stmt->fetchAll());
        exit;
        break;

    case 'get_verse_count':
        header('Content-Type: application/json');
        $surah = $_GET['surah'] ?? 1;
        $stmt = $pdo->prepare("SELECT full_verses FROM quran_structure WHERE surah_number = ?");
        $stmt->execute([$surah]);
        $data = $stmt->fetch();
        echo json_encode($data);
        exit;
        break;

    case 'toggle_bookmark':
        header('Content-Type: application/json');
        if (!isLoggedIn()) {
            echo json_encode(['error' => 'Not logged in']);
            exit;
        }
        $surah = (int)($_POST['surah'] ?? 0);
        $verse = (int)($_POST['verse'] ?? 0);
        $action = $_POST['action'] ?? 'add';
        $note = $_POST['note'] ?? null;

        require_once '../src/Models/Bookmark.php';
        $bookmarkModel = new Bookmark($pdo);
        $user_id = $_SESSION['user_id'];

        if ($action === 'add') {
            $bookmarkModel->add($user_id, $surah, $verse, $note);
            echo json_encode(['status' => 'added']);
        } else {
            $bookmarkModel->remove($user_id, $surah, $verse);
            echo json_encode(['status' => 'removed']);
        }
        exit;
        break;

    default:
        if (isLoggedIn()) {
            redirect('dashboard');
        } else {
            include '../src/Views/auth/login.php';
        }
}
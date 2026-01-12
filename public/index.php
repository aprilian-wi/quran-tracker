<?php
// public/index.php
// Configure Session Lifetime (1 Year)
ini_set('session.gc_maxlifetime', 31536000);
session_set_cookie_params([
    'lifetime' => 31536000,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']), // Only secure if HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);
// Custom Session Save Path
$sessionPath = __DIR__ . '/../storage/sessions';
if (!file_exists($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}
session_save_path($sessionPath);

session_start();
require '../config/database.php';
require '../src/Helpers/functions.php';  // HANYA DI SINI!

// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$page = $_GET['page'] ?? 'login';

// === HALAMAN YANG WAJIB LOGIN ===
$authPages = ['dashboard', 'admin', 'teacher', 'parent', 'logout', 'update_progress', 'update_progress_books', 'create_parent', 'create_teacher', 'assign_class', 'delete_user', 'edit_parent', 'delete_parent', 'delete_teacher', 'admin/create_school', 'admin/store_school', 'videos/index', 'videos/watch', 'videos/search', 'notifications/index', 'delete_progress_books_action', 'delete_progress_hadiths_action', 'delete_progress_prayers_action', 'delete_progress_action', 'feed/index', 'feed/create', 'feed/action/create', 'feed/action/like', 'feed/action/comment', 'feed/action/comment_list'];
if (in_array($page, $authPages)) {
    requireLogin();
}

// === ROLE CHECK ===
$superadminPages = ['admin/users', 'admin/parents', 'admin/classes', 'admin/edit_class', 'admin/teaching_books', 'admin/create_teaching_book', 'admin/edit_teaching_book', 'admin/store_teaching_book', 'admin/update_teaching_book', 'admin/delete_teaching_book', 'edit_parent', 'edit_teacher', 'create_parent', 'create_teacher', 'delete_user', 'edit_class', 'delete_parent', 'delete_teacher', 'admin/promote_class', 'promote_class_action'];
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

if (in_array($page, $globalAdminPages) && !isGlobalAdmin())
    die('Access denied: System Admin only');
// Allow both superadmin and school_admin to access general admin pages
if (in_array($page, $superadminPages) && !(hasRole('superadmin') || hasRole('school_admin')))
    die('Access denied: Admin only');
$teacherPages = ['teacher/class_students', 'teacher/update_progress', 'teacher/update_progress_books', 'teacher/update_progress_prayers', 'teacher/update_progress_hadiths', 'teacher/update_profile', 'assign_class'];
$parentPages = ['parent/my_children', 'parent/update_progress', 'parent/update_progress_books', 'parent/update_progress_prayers', 'parent/update_progress_hadiths', 'update_progress', 'update_progress_books', 'update_progress_prayers', 'update_progress_hadiths'];


// Allow teachers and superadmin to access teacher pages
if (in_array($page, $teacherPages) && !(hasRole('teacher') || hasRole('superadmin') || hasRole('school_admin')))
    die('Access denied: Teacher only');
// Parent pages: parents can access their pages. Allow superadmin/teacher to view parent's children.
if (in_array($page, $parentPages)) {
    if ($page === 'parent/my_children') {
        if (!(hasRole('parent') || hasRole('superadmin') || hasRole('school_admin') || hasRole('teacher'))) {
            die('Access denied: Parent/Teacher/Admin only');
        }
    } elseif ($page === 'parent/update_progress') {
        if (!hasRole('parent') && !hasRole('superadmin') && !hasRole('school_admin'))
            die('Access denied: Parent/Admin only');
    } elseif ($page === 'parent/update_progress_prayers') {
        if (!hasRole('parent') && !hasRole('superadmin') && !hasRole('school_admin'))
            die('Access denied: Parent/Admin only');
    } elseif ($page === 'parent/update_progress_hadiths') {
        if (!hasRole('parent') && !hasRole('superadmin') && !hasRole('school_admin'))
            die('Access denied: Parent/Admin only');
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

        // PWA Handling for Teacher
        // Check Referer or Header as fallback if Session/GET lost
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $isPwaMode = isPwa() || (isset($_GET['mode']) && $_GET['mode'] === 'pwa') || strpos($referer, 'mode=pwa') !== false;

        if (($role === 'teacher') && $isPwaMode) {
            require_once '../src/Controllers/DashboardController.php';
            $controller = new DashboardController($pdo);
            $data = $controller->index(); // Fetch data
            include '../src/Views/layouts/pwa.php';
            include '../src/Views/dashboard/teacher_pwa.php';
            return;
        }

        if (($role === 'parent') && $isPwaMode) {
            redirect('parent/my_children', ['mode' => 'pwa']);
        }

        $file = "../src/Views/dashboard/{$role}.php";
        if (!file_exists($file)) {
            die("Error: File dashboard/{$role}.php tidak ditemukan.");
        }
        include $file;
        break;

    // Admin
    case 'admin/users':
        include '../src/Views/admin/users.php';
        break;
    case 'admin/parents':
        include '../src/Views/admin/parents.php';
        break;
    case 'admin/import_parents':
        include '../src/Actions/import_parents_action.php';
        break;
    case 'admin/download_csv_template':
        include '../src/Actions/download_template_action.php';
        break;
    case 'admin/classes':
        include '../src/Views/admin/classes.php';
        break;
    case 'admin/edit_class':
        include '../src/Views/admin/edit_class.php';
        break;
    case 'admin/teaching_books':
        include '../src/Views/admin/teaching_books.php';
        break;
    case 'admin/create_teaching_book':
        include '../src/Views/admin/create_teaching_book.php';
        break;
    case 'admin/edit_teaching_book':
        include '../src/Views/admin/edit_teaching_book.php';
        break;
    case 'admin/manage_short_prayers':
        include '../src/Views/admin/manage_short_prayers.php';
        break;
    case 'admin/save_short_prayer':
        include '../src/Actions/store_short_prayer_action.php';
        break;
    case 'admin/create_short_prayer':
        include '../src/Views/admin/create_short_prayer.php';
        break;
    case 'admin/edit_short_prayer':
        include '../src/Views/admin/edit_short_prayer.php';
        break;
    case 'admin/update_short_prayer':
        include '../src/Actions/update_short_prayer_action.php';
        break;
    case 'admin/manage_hadiths':
        include '../src/Views/admin/manage_hadiths.php';
        break;
    case 'admin/create_hadith':
        include '../src/Views/admin/create_hadith.php';
        break;
    case 'admin/edit_hadith':
        include '../src/Views/admin/edit_hadith.php';
        break;
    case 'admin/save_hadith':
        include '../src/Actions/store_hadith_action.php';
        break;
    case 'admin/delete_hadith':
        include '../src/Actions/delete_hadith_action.php';
        break;
    case 'admin/update_hadith':
        include '../src/Actions/update_hadith_action.php';
        break;
    case 'admin/teachers':
        include '../src/Views/admin/teachers.php';
        break;
    case 'admin/list_children':
        include '../src/Views/admin/list_children.php';
        break;
    case 'admin/update_progress':
        include '../src/Views/teacher/update_progress.php';
        break;
    case 'admin/update_progress_books':
        include '../src/Views/teacher/update_progress_books.php';
        break;
    case 'admin/update_progress_prayers':
        include '../src/Views/teacher/update_progress_prayers.php';
        break;
    case 'admin/update_progress_hadiths':
        include '../src/Views/teacher/update_progress_hadiths.php';
        break;
    case 'admin/export_users':
        include '../src/Actions/export_users_action.php';
        break;
    case 'admin/export_children':
        include '../src/Actions/export_children_action.php';
        break;

    // Video Management
    case 'admin/videos':
        include '../src/Views/admin/videos.php';
        break;
    case 'admin/create_video':
        include '../src/Views/admin/create_video.php';
        break;
    case 'admin/store_video':
        include '../src/Actions/store_video_action.php';
        break;
    case 'admin/edit_video':
        include '../src/Views/admin/edit_video.php';
        break;
    case 'admin/update_video':
        include '../src/Actions/update_video_action.php';
        break;
    case 'admin/delete_video':
        include '../src/Actions/delete_video_action.php';
        break;

    // Video Category Management
    case 'admin/video_categories':
        include '../src/Views/admin/video_categories.php';
        break;
    case 'admin/create_video_category':
        include '../src/Views/admin/create_video_category.php';
        break;
    case 'admin/store_video_category':
        include '../src/Actions/store_video_category_action.php';
        break;
    case 'admin/edit_video_category':
        include '../src/Views/admin/edit_video_category.php';
        break;
    case 'admin/update_video_category':
        include '../src/Actions/update_video_category_action.php';
        break;
    case 'admin/delete_video_category':
        include '../src/Actions/delete_video_category_action.php';
        break;

    // System Admin (School Management)
    case 'admin/schools':
        include '../src/Views/admin/schools.php';
        break;
    case 'admin/create_school':
        include '../src/Views/admin/create_school.php';
        break;
    case 'admin/store_school':
        include '../src/Actions/store_school_action.php';
        break;
    case 'admin/edit_school':
        include '../src/Views/admin/edit_school.php';
        break;
    case 'admin/update_school':
        include '../src/Actions/update_school_action.php';
        break;
    case 'admin/delete_school':
        include '../src/Actions/delete_school_action.php';
        break;
    case 'admin/edit_school_admin':
        include '../src/Views/admin/edit_school_admin.php';
        break;
    case 'admin/update_school_admin':
        include '../src/Actions/update_school_admin_action.php';
        break;

    case 'edit_parent':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include '../src/Actions/edit_parent_action.php';
        } else {
            include '../src/Views/admin/edit_parent.php';
        }
        break;

    case 'delete_parent':
        include '../src/Actions/delete_parent_action.php';
        break;

    case 'edit_teacher':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include '../src/Actions/edit_teacher_action.php';
        } else {
            include '../src/Views/admin/edit_teacher.php';
        }
        break;

    // Teacher
    case 'teacher/class_students':
        if (isPwa() || (isset($_GET['mode']) && $_GET['mode'] === 'pwa')) {
            $class_id = $_GET['class_id'] ?? 0;
            if (!$class_id || !is_numeric($class_id)) {
                setFlash('danger', 'Invalid class.');
                redirect('dashboard&mode=pwa');
            }
            require_once '../src/Controllers/TeacherController.php';
            require_once '../src/Models/Class.php';
            $controller = new TeacherController($pdo);
            $students = $controller->classStudents($class_id);

            // Get Class Name for Header
            $classModel = new ClassModel($pdo);
            $class = $classModel->find($class_id);

            include '../src/Views/layouts/pwa.php';
            include '../src/Views/teacher/class_students_pwa.php';
            return;
        }
        include '../src/Views/teacher/class_students.php';
        break;
    case 'teacher/update_progress':
        $child_id = (int) ($_GET['child_id'] ?? 0);
        $class_id = (int) ($_GET['class_id'] ?? 0);
        if (!$child_id && !$class_id)
            die('Invalid child_id or class_id');
        if ($child_id) {
            require_once '../src/Models/Child.php';
            $childModel = new Child($pdo);
            $child = $childModel->find($child_id, $_SESSION['user_id'], $_SESSION['role']);
            if (!$child)
                die('Access denied');
        }

        // PWA View
        if (isPwa() || (isset($_GET['mode']) && $_GET['mode'] === 'pwa')) {
            require_once '../src/Models/Progress.php';
            $juzList = range(30, 1);
            include '../src/Views/layouts/pwa.php';
            include '../src/Views/teacher/update_progress_pwa.php';
            return;
        }

        include '../src/Views/teacher/update_progress.php';
        break;

    case 'teacher/update_progress_books':
        $child_id = (int) ($_GET['child_id'] ?? 0);
        $class_id = (int) ($_GET['class_id'] ?? 0); // Capture class_id
        if (isPwa() || (isset($_GET['mode']) && $_GET['mode'] === 'pwa')) {
            require_once '../src/Models/Child.php';
            require_once '../src/Controllers/AdminController.php'; // Use AdminController
            require_once '../src/Models/Progress.php';

            $childModel = new Child($pdo);
            $child = $childModel->find($child_id, $_SESSION['user_id'], $_SESSION['role']);

            $adminController = new AdminController($pdo);
            $books = $adminController->getAllTeachingBooks();

            include '../src/Views/layouts/pwa.php';
            include '../src/Views/teacher/update_progress_books_pwa.php';
            return;
        }
        include '../src/Views/teacher/update_progress_books.php';
        break;

    case 'teacher/update_progress_prayers':
        $child_id = (int) ($_GET['child_id'] ?? 0);
        $class_id = (int) ($_GET['class_id'] ?? 0);
        if (isPwa() || (isset($_GET['mode']) && $_GET['mode'] === 'pwa')) {
            require_once '../src/Models/Child.php';
            require_once '../src/Controllers/AdminController.php'; // Use AdminController
            require_once '../src/Models/Progress.php';

            $childModel = new Child($pdo);
            $child = $childModel->find($child_id, $_SESSION['user_id'], $_SESSION['role']);

            $adminController = new AdminController($pdo);
            $prayers = $adminController->getShortPrayers();

            include '../src/Views/layouts/pwa.php';
            include '../src/Views/teacher/update_progress_prayers_pwa.php';
            return;
        }
        include '../src/Views/teacher/update_progress_prayers.php';
        break;

    case 'teacher/update_progress_hadiths':
        $child_id = (int) ($_GET['child_id'] ?? 0);
        $class_id = (int) ($_GET['class_id'] ?? 0);
        if (isPwa() || (isset($_GET['mode']) && $_GET['mode'] === 'pwa')) {
            require_once '../src/Models/Child.php';
            require_once '../src/Controllers/AdminController.php'; // Use AdminController
            require_once '../src/Models/Progress.php';

            $childModel = new Child($pdo);
            $child = $childModel->find($child_id, $_SESSION['user_id'], $_SESSION['role']);

            $adminController = new AdminController($pdo);
            $hadiths = $adminController->getHadiths();

            include '../src/Views/layouts/pwa.php';
            include '../src/Views/teacher/update_progress_hadiths_pwa.php';
            return;
        }
        include '../src/Views/teacher/update_progress_hadiths.php';
        break;

    case 'parent/my_children':
        include '../src/Views/parent/my_children.php';
        break;
    case 'parent/update_progress':
        include '../src/Views/parent/update_progress.php';
        break;
    case 'parent/update_progress_books':
        include '../src/Views/parent/update_progress_books.php';
        break;
    case 'parent/update_progress_prayers':
        include '../src/Views/parent/update_progress_prayers.php';
        break;
    case 'parent/update_progress_hadiths':
        include '../src/Views/parent/update_progress_hadiths.php';
        break;
    case 'update_progress_prayers':
        include '../src/Actions/update_progress_prayers_action.php';
        break;
    case 'update_progress_hadiths':
        include '../src/Actions/update_progress_hadiths_action.php';
        break;
    case 'parent/upload_photo':
        include '../src/Actions/upload_child_photo_action.php';
        break;

    // Actions
    case 'update_progress':
        include '../src/Actions/update_progress_action.php';
        break;
    case 'update_progress_books':
        include '../src/Actions/update_progress_books_action.php';
        break;
    case 'delete_progress_books_action':
        include '../src/Actions/delete_progress_books_action.php';
        break;
    case 'delete_progress_hadiths_action':
        include '../src/Actions/delete_progress_hadiths_action.php';
        break;
    case 'delete_progress_prayers_action':
        include '../src/Actions/delete_progress_prayers_action.php';
        break;
    case 'delete_progress_action':
        include '../src/Actions/delete_progress_action.php';
        break;
    case 'mark_notification_viewed':
        include '../src/Actions/mark_notification_viewed_action.php';
        break;
    case 'export_progress_excel':
        include '../src/Actions/export_progress_excel_action.php';
        break;
    case 'export_quran_progress_excel':
        include '../src/Actions/export_quran_progress_excel_action.php';
        break;
    case 'export_hadith_progress_excel':
        include '../src/Actions/export_hadith_progress_excel_action.php';
        break;
    case 'admin/store_teaching_book':
        include '../src/Actions/store_teaching_book_action.php';
        break;
    case 'admin/update_teaching_book':
        include '../src/Actions/update_teaching_book_action.php';
        break;
    case 'admin/delete_teaching_book':
        include '../src/Actions/delete_teaching_book_action.php';
        break;
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
    case 'assign_class':
        include '../src/Actions/assign_class_action.php';
        break;
    case 'delete_user':
        include '../src/Actions/delete_user_action.php';
        break;
    case 'delete_teacher':
        include '../src/Actions/delete_teacher_action.php';
        break;

    // Quran Digital Pages
    case 'quran/surah_list':
        include '../src/Views/quran/surah_list.php';
        break;
    case 'quran/surah_detail':
        $surah = (int) ($_GET['surah'] ?? 1);
        if ($surah < 1 || $surah > 114)
            die('Invalid surah');
        include '../src/Views/quran/surah_detail.php';
        break;
    case 'quran/search':
        include '../src/Views/quran/search.php';
        break;

    case 'quran/bookmarks':
        include '../src/Views/quran/bookmarks.php';
        break;

    // Shared Lists (Doa & Hadits)
    case 'shared/list_short_prayers':
        include '../src/Views/shared/list_short_prayers.php';
        break;
    case 'shared/list_hadiths':
        include '../src/Views/shared/list_hadiths.php';
        break;

    // Video Edukasi PWA
    case 'videos/index':
        if (isPwa() || (isset($_GET['mode']) && $_GET['mode'] === 'pwa')) {
            include '../src/Views/layouts/pwa.php';
            include '../src/Views/videos/index_pwa.php';
            return;
        }
        break;
    case 'videos/watch':
        if (isPwa() || (isset($_GET['mode']) && $_GET['mode'] === 'pwa')) {
            include '../src/Views/layouts/pwa.php';
            include '../src/Views/videos/watch_pwa.php';
            return;
        }
        break;
    case 'videos/search':
        // Reuse index with search focus or specific search page
        if (isPwa() || (isset($_GET['mode']) && $_GET['mode'] === 'pwa')) {
            include '../src/Views/layouts/pwa.php';
            include '../src/Views/videos/index_pwa.php';
            return;
        }
        break;


        // Notifications
        include '../src/Views/notifications/index_pwa.php'; // Reuse for now or create specific
        break;

    // Feed Feature
    case 'feed/index':
        require_once '../src/Models/Feed.php';
        $feedModel = new Feed($pdo);
        $feeds = $feedModel->getAllValid($_SESSION['school_id'], $_SESSION['user_id']);
        include '../src/Views/layouts/pwa.php';
        include '../src/Views/feed/index.php';
        break;

    case 'feed/create':
        include '../src/Views/layouts/pwa.php';
        include '../src/Views/feed/create.php';
        break;

    case 'feed/action/create':
        include '../src/Actions/feed/create_action.php';
        break;

    case 'feed/action/like':
        include '../src/Actions/feed/like_action.php';
        break;

    case 'feed/action/comment':
        include '../src/Actions/feed/comment_action.php';
        break;

    case 'feed/action/comment_list':
        include '../src/Actions/feed/comment_list_action.php';
        break;

    case 'api/get_unread_count':
        header('Content-Type: application/json');
        if (!isLoggedIn() || $_SESSION['role'] !== 'parent') {
            echo json_encode(['count' => 0]);
            exit;
        }
        require_once '../src/Models/Child.php';
        require_once '../src/Models/Notification.php';

        $childModel = new Child($pdo);
        $children = $childModel->getByParent($_SESSION['user_id']);
        $child_ids = array_column($children, 'id');

        $notifModel = new Notification($pdo);
        $count = $notifModel->getUnreadCount($child_ids);
        echo json_encode(['count' => $count]);
        exit;
        break;

    case 'api/mark_all_read':
        header('Content-Type: application/json');
        if (!isLoggedIn() || $_SESSION['role'] !== 'parent') {
            echo json_encode(['success' => false]);
            exit;
        }
        require_once '../src/Models/Child.php';
        require_once '../src/Models/Notification.php';

        $childModel = new Child($pdo);
        $children = $childModel->getByParent($_SESSION['user_id']);
        $child_ids = array_column($children, 'id');

        $notifModel = new Notification($pdo);
        $success = $notifModel->markAllAsRead($child_ids);
        echo json_encode(['success' => $success]);
        exit;
        break;

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
        $surah = (int) ($_POST['surah'] ?? 0);
        $verse = (int) ($_POST['verse'] ?? 0);
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

    // Class Promotion API
    case 'api/get_class_students':
        header('Content-Type: application/json');
        if (!isLoggedIn()) {
            echo json_encode([]);
            exit;
        }
        $class_id = (int) ($_GET['class_id'] ?? 0);
        if (!$class_id && $class_id !== -1) {
            echo json_encode([]);
            exit;
        }

        if ($class_id === -1) {
            require_once '../src/Models/Child.php';
            $childModel = new Child($pdo);
            $students = $childModel->getUnassignedChildren($_SESSION['school_id']);
            echo json_encode($students);
        } else {
            require_once '../src/Models/Class.php';
            $classModel = new ClassModel($pdo);
            $students = $classModel->getStudents($class_id);
            echo json_encode($students);
        }
        exit;
        break;

    case 'admin/promote_class':
        include '../src/Views/admin/promote_class.php';
        break;
    case 'promote_class_action':
        include '../src/Actions/promote_class_action.php';
        break;

    default:
        if (isLoggedIn()) {
            redirect('dashboard');
        } else {
            include '../src/Views/auth/login.php';
        }
}
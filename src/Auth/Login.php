<?php
// src/Auth/Login.php
require_once __DIR__ . '/../Helpers/functions.php';  // WAJIB DI-INCLUDE!

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone'] ?? ''); // Expect 'phone' from form
    $password = $_POST['password'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!validateCsrf($csrf)) {
        setFlash('danger', 'Invalid CSRF token.');
        redirect('login');
    }

    if (empty($phone) || empty($password)) {
        setFlash('danger', 'No. HP dan password wajib diisi.');
        redirect('login');
    }

    global $pdo;
    // Update query to check phone
    $stmt = $pdo->prepare("SELECT id, name, phone, password, role, school_id FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_phone'] = $user['phone']; // Add phone to session
        $_SESSION['role'] = $user['role'];
        $_SESSION['school_id'] = (int) $user['school_id'];

        // Fetch School Name for dynamic display
        if ($user['school_id']) {
            $stmt = $pdo->prepare("SELECT name FROM schools WHERE id = ?");
            $stmt->execute([$user['school_id']]);
            $school = $stmt->fetch();
            $_SESSION['school_name'] = $school ? $school['name'] : 'Quran Tracker';
        }

        setFlash('success', "Selamat datang, " . h($user['name']) . "!");

        // Check for PWA mode
        $mode = $_POST['mode'] ?? $_GET['mode'] ?? '';
        $redirectParams = [];
        if ($mode === 'pwa') {
            $_SESSION['is_pwa'] = true; // FORCE Session Persistence
            $redirectParams['mode'] = 'pwa';
        }

        if ($user['role'] === 'superadmin') {
            redirect('admin/schools');
        } else {
            redirect('dashboard', $redirectParams);
        }
    } else {
        setFlash('danger', 'No. HP atau password salah.');
        redirect('login');
    }
}
<?php
// src/Auth/Login.php
require_once __DIR__ . '/../Helpers/functions.php';  // WAJIB DI-INCLUDE!

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!validateCsrf($csrf)) {
        setFlash('danger', 'Invalid CSRF token.');
        redirect('login');
    }

    if (empty($email) || empty($password)) {
        setFlash('danger', 'Email dan password wajib diisi.');
        redirect('login');
    }

    global $pdo;
    $stmt = $pdo->prepare("SELECT id, name, email, password, role, school_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['school_id'] = $user['school_id'];



        setFlash('success', "Selamat datang, " . h($user['name']) . "!");
        if ($user['role'] === 'superadmin') {
            redirect('admin/schools');
        } else {
            redirect('dashboard');
        }
    } else {
        setFlash('danger', 'Email atau password salah.');
        redirect('login');
    }
}
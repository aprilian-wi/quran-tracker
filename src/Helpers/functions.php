<?php
// src/Helpers/functions.php

if (!defined('BASE_URL')) {
    // BASE_URL harus berakhir dengan slash dan TIDAK mengandung 'public'
    // karena view/view layout menambahkan 'public/index.php' setelah BASE_URL.
    // For local deployment, use the localhost URL
    define('BASE_URL', 'http://localhost/quran-tracker/');
}

require_once __DIR__ . '/../../config/database.php';

// ... (sisa kode tetap sama)
function ensureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
ensureSession();

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        redirect('login');
    }
}

function hasRole(string $role): bool {
    return isLoggedIn() && ($_SESSION['role'] ?? '') === $role;
}

function isGlobalAdmin(): bool {
    return hasRole('superadmin') && ($_SESSION['school_id'] ?? 0) === 1;
}

function redirect(string $page, array $params = []): void {
    // Jika $page sudah berisi query string (mis. "teacher/class_students&class_id=3")
    // maka anggap sebagai query lengkap setelah tanda tanya.
    if (strpos($page, '=') !== false || strpos($page, '&') !== false) {
        $query = $page;
        if (!empty($params)) {
            $query .= '&' . http_build_query($params);
        }
    } else {
        $query = http_build_query(array_merge(['page' => $page], $params));
    }

    // Pastikan redirect menuju front controller di dalam folder `public`.
    // Jika BASE_URL sudah mengandung '/public', gunakan itu, jika tidak tambahkan '/public/index.php'.
    $base = rtrim(BASE_URL, '/');
    if (strpos($base, '/public') !== false) {
        // BASE_URL mis. http://localhost/quran-tracker/public
        $url = $base . '/index.php?' . $query;
    } else {
        // BASE_URL mis. http://localhost/quran-tracker/
        $url = $base . '/public/index.php?' . $query;
    }

    header("Location: $url");
    exit;
}

function h(?string $string): string {
    if ($string === null) return '';
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function validateCsrf(string $token): bool {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function csrfInput(): string {
    return '<input type="hidden" name="csrf_token" value="' . h($_SESSION['csrf_token']) . '">';
}

function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    return $user !== false ? $user : null;
}

/**
 * Check CSRF Token from POST data.
 * If invalid, send 403 response and exit.
 */
function checkCSRFToken(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrf($token)) {
        http_response_code(403);
        die('Invalid CSRF token.');
    }
}

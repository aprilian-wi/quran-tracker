<?php
// src/Helpers/functions.php

if (!defined('BASE_URL')) {
    // Detect Protocol
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    // Detect Host
    $host = $_SERVER['HTTP_HOST'];
    // Detect Script Path (to handle subdirectories automatically)
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    // Normalize slashes and remove 'public' or 'src/Helpers' if present in the path detection
    // The goal is to get to the root 'quran-tracker' folder
    
    // Simple heuristic: If running locally on specific port or folder
    if ($host === 'localhost' || $host === '127.0.0.1') {
         // Local MAMP specific hardcode as fallback/default if strictly local
         define('BASE_URL', 'http://localhost/quran-tracker/');
    } else {
         // Production: Assume the domain points directly to the public folder or root
         // If production domain points to root, we might need a trailing slash
          define('BASE_URL', $protocol . "://" . $host . "/");
    }
}

require_once __DIR__ . '/../../config/database.php';

// ... (sisa kode tetap sama)
function ensureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
ensureSession();

function checkPwaMode(): void {
    if (isset($_GET['mode']) && $_GET['mode'] === 'pwa') {
        $_SESSION['is_pwa'] = true;
    }
}
// Call immediately to capture query param
checkPwaMode();

function isPwa(): bool {
    return isset($_SESSION['is_pwa']) && $_SESSION['is_pwa'] === true;
}

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
    return hasRole('superadmin') && ((int)($_SESSION['school_id'] ?? 0)) === 1;
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
    // BASE_URL sekarang dinamis. Kita cek apakah BASE_URL sudah mengarah ke 'public/'
    $base = rtrim(BASE_URL, '/');

    // Cek apakah script yang dijalankan berada di dalam folder public
    // atau BASE_URL mengandung kata public
    if (strpos($base, '/public') !== false) {
        // Jika BASE_URL sudah mengandung /public (misal hosting arah ke public root)
        // atau kita set manual ke .../public/
        $url = $base . '/index.php?' . $query;
    } else {
        // Jika BASE_URL adalah root project (misal localhost/quran-tracker/)
        // Kita perlu masuk ke public
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

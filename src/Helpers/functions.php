<?php
// src/Helpers/functions.php

if (!defined('BASE_URL')) {
    // Detect Protocol
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    // Detect Host
    $host = $_SERVER['HTTP_HOST'];

    // Determine the path to the 'public' folder or root based on where this script is running from.
    // Ideally, all requests go through public/index.php so dirname($_SERVER['SCRIPT_NAME']) should end in /public
    $path = dirname($_SERVER['SCRIPT_NAME']);
    // Normalize slashes to forward slashes
    $path = str_replace('\\', '/', $path);

    // Ensure it ends with /
    $path = rtrim($path, '/') . '/';

    // If path ends with 'public/', remove it to get the project root (Case Insensitive for Windows)
    // This allows BASE_URL to refer to the project root, keeping compatibility with views
    // that append 'public/...' themselves.
    $path = preg_replace('#/public/$#i', '/', $path);

    // If we are unexpectedly in the root (e.g. cron or include), try to fix path to point to expected base
    // usage logic will be handled in redirect(). For now, trust the script's location is the base for 'assets', etc.
    define('BASE_URL', $protocol . "://" . $host . $path);
}

require_once __DIR__ . '/../../config/database.php';

function requireLayer(string $name): void
{
    require_once __DIR__ . "/../Views/layouts/{$name}.php";
}

function ensureSession()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
ensureSession();

function checkPwaMode(): void
{
    if ((isset($_GET['mode']) && $_GET['mode'] === 'pwa') || (isset($_POST['mode']) && $_POST['mode'] === 'pwa')) {
        $_SESSION['is_pwa'] = true;
    }
}
// Call immediately to capture query param
checkPwaMode();

function isPwa(): bool
{
    return isset($_SESSION['is_pwa']) && $_SESSION['is_pwa'] === true;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect('login');
    }
}

function hasRole(string $role): bool
{
    return isLoggedIn() && ($_SESSION['role'] ?? '') === $role;
}

function isGlobalAdmin(): bool
{
    return hasRole('superadmin') && ((int) ($_SESSION['school_id'] ?? 0)) === 1;
}

function redirect(string $page, array $params = []): void
{
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

    // Use the dynamic BASE_URL (Project Root) + public/index.php
    $url = BASE_URL . 'public/index.php?' . $query;

    // Preserving PWA Mode
    if (isPwa() && strpos($url, 'mode=pwa') === false) {
        $url .= '&mode=pwa';
    }

    header("Location: $url");
    exit;
}

function h(?string $string): string
{
    if ($string === null)
        return '';
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function validateCsrf(string $token): bool
{
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function csrfInput(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h($_SESSION['csrf_token']) . '">';
}

function currentUser(): ?array
{
    if (!isLoggedIn())
        return null;
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, name, phone, role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    return $user !== false ? $user : null;
}

/**
 * Check CSRF Token from POST data.
 * If invalid, send 403 response and exit.
 */
function checkCSRFToken(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrf($token)) {
        http_response_code(403);
        die('Invalid CSRF token.');
    }
}

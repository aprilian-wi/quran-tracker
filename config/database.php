<?php
// config/database.php
// DO NOT COMMIT THIS FILE WITH REAL CREDENTIALS IN PRODUCTION

if (!defined('DB_HOST')) {
    // === DATABASE CONFIGURATION ===
    $isLocal = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1');

    if ($isLocal) {
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'qurantra_quran_tracker');
        define('DB_USER', 'root');
        define('DB_PASS', 'root');
    } else {
        // PRODUCTION CREDENTIALS
        define('DB_HOST', 'localhost'); // Usually localhost for shared hosting too, unless specified otherwise
        define('DB_NAME', 'qurantra_quran_tracker');
        define('DB_USER', 'qurantra_db_user');
        define('DB_PASS', 'qurantracker123!');
    }
    define('DB_CHARSET', 'utf8mb4');
}

// === PDO OPTIONS ===
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    1002 => "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'",
    1001 => true
];

// === CONNECTION ===
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // In production, log error instead of displaying
    error_log("DB Connection Error: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}

// Optional: Set timezone (recommended)
date_default_timezone_set('Asia/Riyadh'); // Adjust to your region

return $pdo;

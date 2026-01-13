<?php
// src/Actions/upload_child_photo_action.php
require_once __DIR__ . '/../Models/Child.php';
require_once __DIR__ . '/../Helpers/functions.php';

header('Content-Type: application/json');

// Remove session_start() because session already started in included files

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Adjusted session user check to use available session keys observed in the debug log
if (!isset($_SESSION['user_id'])) {
    error_log('Upload child photo forbidden: no user_id in session');
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'parent') {
    error_log('Upload child photo forbidden: user role is ' . ($_SESSION['role'] ?? 'none'));
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

if (!isset($_POST['child_id']) || !isset($_FILES['photo'])) {
    error_log('Upload failed: Missing child_id or photo. POST: ' . json_encode($_POST) . ' FILES: ' . json_encode($_FILES));
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters. (File might be too large for server)']);
    exit;
}

$child_id = intval($_POST['child_id']);
$photo = $_FILES['photo'];

if ($photo['error'] !== UPLOAD_ERR_OK) {
    error_log('Upload failed: PHP File Error Code ' . $photo['error']);
    $error_msg = 'File upload error code: ' . $photo['error'];
    if ($photo['error'] === UPLOAD_ERR_INI_SIZE || $photo['error'] === UPLOAD_ERR_FORM_SIZE) {
        $error_msg = 'File is too large for this server.';
    }
    http_response_code(400);
    echo json_encode(['error' => $error_msg]);
    exit;
}

$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($photo['type'], $allowed_types)) {
    error_log('Upload failed: Invalid type ' . $photo['type']);
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type. Only JPG, PNG, GIF allowed.']);
    exit;
}

if ($photo['size'] > 5 * 1024 * 1024) { // 5MB limit
    error_log('Upload failed: Size too large ' . $photo['size']);
    http_response_code(400);
    echo json_encode(['error' => 'File size too large (Max 5MB)']);
    exit;
}

$extension = pathinfo($photo['name'], PATHINFO_EXTENSION);
$filename = 'child_' . $child_id . '_' . uniqid() . '.' . $extension;

$upload_dir = __DIR__ . '/../../public/uploads/children_photos/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$target_file = $upload_dir . $filename;

// Database connection
$pdo = get_pdo_connection(); // Assuming you have a function to get PDO

$childModel = new Child($pdo);

// Check if child belongs to logged in parent
$child = $childModel->find($child_id, $_SESSION['user_id'], 'parent');
if (!$child) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Delete old photo file if exists
if (!empty($child['photo'])) {
    $old_file = $upload_dir . $child['photo'];
    if (file_exists($old_file)) {
        unlink($old_file);
    }
}

// Compression Logic
$max_size = 1 * 1024 * 1024; // 1MB
$quality = 75; // Initial quality

if ($photo['size'] > $max_size) {
    // Compress
    $source_image = null;
    $type = $photo['type'];

    if ($type === 'image/jpeg') {
        $source_image = imagecreatefromjpeg($photo['tmp_name']);
    } elseif ($type === 'image/png') {
        $source_image = imagecreatefrompng($photo['tmp_name']);
        // Handle transparency for PNG -> JPG conversion
        $bg = imagecreatetruecolor(imagesx($source_image), imagesy($source_image));
        imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
        imagealphablending($bg, true);
        imagecopy($bg, $source_image, 0, 0, 0, 0, imagesx($source_image), imagesy($source_image));
        imagedestroy($source_image);
        $source_image = $bg;
    } elseif ($type === 'image/gif') {
        $source_image = imagecreatefromgif($photo['tmp_name']);
    }

    if ($source_image) {
        // Change extension to jpg if we are converting
        $filename = pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
        $target_file = $upload_dir . $filename;

        // Loop to reduce quality until size is met or quality is too low
        $temp_save_path = $target_file;

        // Try resizing if dimensions are huge
        $width = imagesx($source_image);
        $height = imagesy($source_image);
        $max_dimension = 1920;

        if ($width > $max_dimension || $height > $max_dimension) {
            $ratio = $width / $height;
            if ($ratio > 1) {
                $new_width = $max_dimension;
                $new_height = $max_dimension / $ratio;
            } else {
                $new_height = $max_dimension;
                $new_width = $max_dimension * $ratio;
            }
            $new_image = imagecreatetruecolor($new_width, $new_height);

            // Preserve white background for resized image
            $white = imagecolorallocate($new_image, 255, 255, 255);
            imagefill($new_image, 0, 0, $white);

            imagecopyresampled($new_image, $source_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagedestroy($source_image);
            $source_image = $new_image;
        }

        // Save initially
        imagejpeg($source_image, $temp_save_path, $quality);

        // Check size and compress further if needed
        while (filesize($temp_save_path) > $max_size && $quality > 30) {
            $quality -= 5;
            imagejpeg($source_image, $temp_save_path, $quality);
            clearstatcache();
        }

        imagedestroy($source_image);
    } else {
        // Fallback if GD fails
        if (!move_uploaded_file($photo['tmp_name'], $target_file)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save file']);
            exit;
        }
    }
} else {
    // No compression needed
    if (!move_uploaded_file($photo['tmp_name'], $target_file)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save file']);
        exit;
    }
}

// Update photo field in children table
$sql = "UPDATE children SET photo = ? WHERE id = ?";
$stmt = $pdo->prepare($sql);
if (!$stmt->execute([$filename, $child_id])) {
    // On failure, delete uploaded file to avoid orphan file
    unlink($target_file);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update database']);
    exit;
}

echo json_encode(['success' => true, 'photo' => $filename]);
exit;

function get_pdo_connection()
{
    $dbConfig = require __DIR__ . '/../../config/database.php';
    // $dbConfig is expected to be an array, but user error shows PDO object, fix to handle if PDO object passed inadvertently
    if (is_object($dbConfig) && get_class($dbConfig) === 'PDO') {
        return $dbConfig;
    }
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset=utf8mb4";
    return new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}
?>
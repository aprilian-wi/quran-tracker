<?php
require_once __DIR__ . '/../config/database.php';

try {
    $sql = file_get_contents(__DIR__ . '/../restore_superadmin.sql');
    $pdo->exec($sql);
    echo "Superadmin restored successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

<?php
// drop_email_column.php

require_once __DIR__ . '/config/database.php';

echo "Warning: This script will drop the 'email' column from the 'users' table.\n";
echo "Users without a phone number will lose access.\n";
echo "Starting migration...\n";

try {
    // 1. Drop the email column
    $pdo->exec("ALTER TABLE users DROP COLUMN email");
    echo "Success: 'email' column dropped.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

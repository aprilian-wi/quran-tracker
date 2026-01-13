<?php
// update_db_phone.php
$_SERVER['SERVER_NAME'] = 'localhost';
require 'config/database.php';

echo "Starting database migration...\n";

try {
    // 1. Add phone column if it doesn't exist
    $sql = "SHOW COLUMNS FROM users LIKE 'phone'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20) AFTER email");
        echo "Added 'phone' column.\n";
    } else {
        echo "'phone' column already exists.\n";
    }

    // 2. Make email nullable
    $pdo->exec("ALTER TABLE users MODIFY COLUMN email VARCHAR(255) NULL");
    echo "Made 'email' column nullable.\n";

    // 3. Add UNIQUE index to phone (check if exists first usually tricky in raw SQL without stored proc, trying direct add, might fail if exists)
    // Simple check via information_schema or just try/catch
    try {
        $pdo->exec("ALTER TABLE users ADD UNIQUE (phone)");
        echo "Added UNIQUE index to 'phone'.\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "Index for 'phone' already exists.\n";
        } else {
            throw $e;
        }
    }

    echo "Migration completed successfully.\n";

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

<?php
// scripts/migrate.php

require_once __DIR__ . '/../config/database.php';

/** @var PDO $pdo */
$pdo = require __DIR__ . '/../config/database.php';

$migrations = [
    __DIR__ . '/../database/migrations/001_create_schools_table.sql',
    __DIR__ . '/../database/migrations/002_add_school_id_to_tables.sql',
];

foreach ($migrations as $file) {
    echo "Running migration: " . basename($file) . "\n";
    $sql = file_get_contents($file);
    
    // Split by semicolon (rough split) or just run full if simple.
    // Since some statements might contain semicolons (like triggers), this is naive but works for standard alterations.
    // Better to execute one by one if content allows multiple statements.
    
    // The driver might support multiple statements if emulation is on.
    try {
        $pdo->exec($sql);
        echo "Done.\n";
    } catch (PDOException $e) {
        // If "Duplicate column" or "already exists", we might want to skip or warn
        if (strpos($e->getMessage(), "Duplicate column") !== false || strpos($e->getMessage(), "already exists") !== false) {
             echo "Skipped (already exists): " . $e->getMessage() . "\n";
        } else {
             echo "Error: " . $e->getMessage() . "\n";
        }
    }
}

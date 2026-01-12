<?php
require_once __DIR__ . '/../src/Helpers/functions.php';
global $pdo;

$sql = file_get_contents(__DIR__ . '/../database/migrations/006_create_feed_tables.sql');

try {
    $pdo->exec($sql);
    echo "Migration 006 executed successfully.\n";
} catch (PDOException $e) {
    echo "Error executing migration: " . $e->getMessage() . "\n";
}

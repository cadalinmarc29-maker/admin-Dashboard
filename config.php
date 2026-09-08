<?php
/**
 * Database connection (PDO / MySQL - XAMPP default settings)
 * Default XAMPP MySQL: host=localhost, user=root, password="" (empty)
 */
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'don_vincent');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Keep existing installations compatible with roles and inventory.
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'customer'");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') === false) {
            throw $e;
        }
    }

    try {
        $pdo->exec("ALTER TABLE menu_items ADD COLUMN stock INT NOT NULL DEFAULT 0");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') === false) {
            throw $e;
        }
    }
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
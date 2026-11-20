<?php
/**
 * Database connection - shared PDO instance
 * Uses Database abstraction (SQLite local / PostgreSQL production)
 */
require_once __DIR__ . '/../../includes/Database.php';

try {
    $pdo = Database::getConnection();
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
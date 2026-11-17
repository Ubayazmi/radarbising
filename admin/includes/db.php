<?php
/**
 * Database connection - shared PDO instance
 */
$adminDbPath = __DIR__ . '/../../database.sqlite';

try {
    $pdo = new PDO('sqlite:' . $adminDbPath, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
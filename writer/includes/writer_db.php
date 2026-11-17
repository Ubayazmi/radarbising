<?php
/**
 * Writer Portal — Database connection (shared with main site)
 */

$dbPath = __DIR__ . '/../../database.sqlite';

$pdo = new PDO('sqlite:' . $dbPath, null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
<?php
/**
 * Writer Portal — Database connection (shared with main site)
 * Uses Database abstraction (SQLite local / PostgreSQL production)
 */
require_once __DIR__ . '/../../includes/Database.php';

$pdo = Database::getConnection();
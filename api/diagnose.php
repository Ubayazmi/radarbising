<?php
/**
 * Diagnostic endpoint - REMOVE AFTER DEBUGGING
 * Shows PDO/PostgreSQL driver status without exposing secrets.
 */

header('Content-Type: application/json; charset=utf-8');

$diag = [
    'php_version' => PHP_VERSION,
    'php_sapi' => PHP_SAPI,
    'pdo_drivers' => PDO::getAvailableDrivers(),
    'pdo_pgsql_loaded' => extension_loaded('pdo_pgsql'),
    'pgsql_loaded' => extension_loaded('pgsql'),
    'pdo_mysql_loaded' => extension_loaded('pdo_mysql'),
    'gd_loaded' => extension_loaded('gd'),
    'mbstring_loaded' => extension_loaded('mbstring'),
    'curl_loaded' => extension_loaded('curl'),
    'extension_dir' => ini_get('extension_dir'),
    'loaded_extensions' => get_loaded_extensions(),
];

// Test PDO connection string parsing (no actual connection)
$dsn = getenv('DB_DSN') ?: ($_ENV['DB_DSN'] ?? '');
$diag['dsn_present'] = !empty($dsn);
$diag['dsn_prefix'] = $dsn ? substr($dsn, 0, 20) . '...' : 'NOT SET';

echo json_encode($diag, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
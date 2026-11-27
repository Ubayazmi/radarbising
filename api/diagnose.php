<?php
/**
 * Diagnostic endpoint - REMOVE AFTER DEBUGGING
 * Shows PDO/PostgreSQL driver status without exposing secrets.
 * Safe even if PDO class doesn't exist.
 */

header('Content-Type: application/json; charset=utf-8');

$pdo_class_exists = class_exists('PDO');
$diag = [
    'php_version' => PHP_VERSION,
    'php_sapi' => PHP_SAPI,
    'pdo_class_exists' => $pdo_class_exists,
    'extension_loaded_pdo' => extension_loaded('pdo'),
    'extension_loaded_pdo_pgsql' => extension_loaded('pdo_pgsql'),
    'extension_loaded_pgsql' => extension_loaded('pgsql'),
    'extension_loaded_pdo_mysql' => extension_loaded('pdo_mysql'),
    'extension_loaded_gd' => extension_loaded('gd'),
    'extension_loaded_mbstring' => extension_loaded('mbstring'),
    'extension_loaded_curl' => extension_loaded('curl'),
    'extension_dir' => ini_get('extension_dir'),
    'loaded_extensions' => get_loaded_extensions(),
];

if ($pdo_class_exists) {
    $diag['pdo_drivers'] = PDO::getAvailableDrivers();
} else {
    $diag['pdo_drivers'] = 'PDO class NOT FOUND';
}

// Test PDO connection string parsing (no actual connection)
$dsn = getenv('DB_DSN') ?: ($_ENV['DB_DSN'] ?? '');
$diag['dsn_present'] = !empty($dsn);
$diag['dsn_prefix'] = $dsn ? substr($dsn, 0, 20) . '...' : 'NOT SET';

echo json_encode($diag, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
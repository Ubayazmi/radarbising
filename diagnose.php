<?php
// diagnose.php — cek status environment di Vercel container
// Gunakan: https://<project>.vercel.app/diagnose.php
header('Content-Type: application/json');
set_error_handler(function ($errno, $errstr) {
    echo json_encode(['error' => $errstr]);
    exit(1);
});
try {
    $status = [
      'vercel_env' => $_ENV['VERCEL'] ?? 'not_set',
      'db_dsn' => getenv('DB_DSN') ?? 'empty',
      'port_env' => getenv('PORT') ?? 'not_set',
      'php_version' => phpversion(),
      'loaded_extensions' => array_slice(get_loaded_extensions(), 0, 30),
      'session_dir' => is_writable('/var/task/sessions') ? 'writable' : 'not_writable',
      'home_dir' => ini_get('safe_mode') ?? 'not_applicable',
    ];
    echo json_encode($status, JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    echo json_encode(['exception' => $e->getMessage()]);
}
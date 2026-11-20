<?php
/**
 * Vercel Serverless Function Entry Point — Admin Panel
 * Routes admin requests to the correct admin PHP file.
 */

// Ensure we are in the project root for all relative paths
chdir(__DIR__ . "/..");

// Bootstrap the application (loads autoload, config, etc.)
require_once __DIR__ . "/../vendor/autoload.php";

// Load .env in development (Vercel sets VERCEL=1 in production)
if (!isset($_ENV["VERCEL"]) && !getenv("VERCEL")) {
    if (class_exists("Dotenv\Dotenv")) {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
        $dotenv->safeLoad();
    }
}

// Initialize PostgreSQL session handler
require_once __DIR__ . "/../includes/Database.php";
require_once __DIR__ . "/../includes/SessionHandlerPg.php";

try {
    $pdo = Database::getConnection();
    $handler = new SessionHandlerPg($pdo, 31536000);
    session_set_save_handler($handler, true);
} catch (Throwable $e) {
    error_log("Admin session handler init failed: " . $e->getMessage());
    $sessionPath = __DIR__ . "/../sessions";
    if (!is_dir($sessionPath)) @mkdir($sessionPath, 0755, true);
    session_save_path($sessionPath);
}

// Session configuration for serverless
ini_set("session.gc_maxlifetime", 31536000);
ini_set("session.cookie_lifetime", 31536000);
ini_set("session.cookie_secure", true);
ini_set("session.cookie_httponly", true);
ini_set("session.cookie_samesite", "Lax");
ini_set("session.use_strict_mode", 1);
ini_set("session.sid_length", 32);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine which admin file to load based on request path
$requestUri = $_SERVER["REQUEST_URI"] ?? "";
$path = parse_url($requestUri, PHP_URL_PATH);

// Remove /admin prefix
$adminPath = preg_replace("#^/admin#", "", $path);

// Default to login if no specific page
if ($adminPath === "" || $adminPath === "/") {
    $adminPath = "/login.php";
}

// Security: only allow known admin files
$allowedFiles = [
    "login.php",
    "logout.php",
    "dashboard.php",
    "articles.php",
    "videos.php",
    "users.php",
    "categories.php",
    "settings.php",
    "profile.php",
];

$fileName = basename($adminPath);
if (!in_array($fileName, $allowedFiles, true)) {
    http_response_code(404);
    exit("Admin page not found: " . htmlspecialchars($fileName));
}

// Include the requested admin file
// The admin files expect to be in /admin directory, so we chdir there
$adminDir = __DIR__ . "/../admin";
chdir($adminDir);
require_once $adminDir . "/" . $fileName;
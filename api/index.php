<?php
/**
 * Vercel Serverless Function Entry Point
 * 
 * This wrapper routes all requests to the main application index.php
 * while preserving the serverless function context.
 * 
 * IMPORTANT: Do NOT modify root index.php - this file calls it.
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
// This replaces file-based sessions which do not work on serverless read-only FS
require_once __DIR__ . "/../includes/Database.php";
require_once __DIR__ . "/../includes/SessionHandlerPg.php";

try {
    $pdo = Database::getConnection();
    $handler = new SessionHandlerPg($pdo, 31536000); // 1 year
    session_set_save_handler($handler, true);
} catch (Throwable $e) {
    // Fallback for local dev if DB unavailable
    error_log("Session handler init failed: " . $e->getMessage());
    $sessionPath = __DIR__ . "/../sessions";
    if (!is_dir($sessionPath)) @mkdir($sessionPath, 0755, true);
    session_save_path($sessionPath);
}

// Session configuration for serverless
ini_set("session.gc_maxlifetime", 31536000);
ini_set("session.cookie_lifetime", 31536000);
ini_set("session.cookie_secure", true); // Vercel is HTTPS
ini_set("session.cookie_httponly", true);
ini_set("session.cookie_samesite", "Lax");
ini_set("session.use_strict_mode", 1);
ini_set("session.sid_length", 32);

// Start session early (before any output)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Now include the main application entry point
// This executes the full application logic
require_once __DIR__ . "/../index.php";

<?php
/**
 * Vercel Health Check Endpoint
 * 
 * Tests database connectivity (SELECT 1) without leaking credentials.
 * Usage: GET /health.php
 * Returns JSON: { status: "ok"|"error", latency_ms: number, driver: "pgsql"|"sqlite", timestamp: string }
 */

chdir(__DIR__ . "/..");

require_once __DIR__ . "/../vendor/autoload.php";

if (!isset($_ENV["VERCEL"]) && !getenv("VERCEL")) {
    if (class_exists("Dotenv\Dotenv")) {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
        $dotenv->safeLoad();
    }
}

require_once __DIR__ . "/../includes/Database.php";

header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$result = Database::healthCheck();
$result["timestamp"] = date("c");

$statusCode = $result["status"] === "ok" ? 200 : 503;
http_response_code($statusCode);

echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

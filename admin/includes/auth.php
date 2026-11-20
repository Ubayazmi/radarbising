<?php
/**
 * Authentication & session management (Admin Portal)
 * Uses PostgreSQL-backed session handler for serverless compatibility.
 */

// Load database abstraction and session handler
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/SessionHandlerPg.php';

try {
    $pdo = Database::getConnection();
    $handler = new SessionHandlerPg($pdo, 31536000); // 1 year
    session_set_save_handler($handler, true);
} catch (Throwable $e) {
    error_log('Session handler init failed: ' . $e->getMessage());
    // Fallback to file-based for local dev if DB unavailable
    $sessionPath = __DIR__ . '/../../sessions';
    if (!is_dir($sessionPath)) @mkdir($sessionPath, 0755, true);
    session_save_path($sessionPath);
}

// Session configuration
ini_set('session.gc_maxlifetime', 31536000);
ini_set('session.cookie_lifetime', 31536000);
ini_set('session.cookie_secure', true); // Vercel is HTTPS
ini_set('session.cookie_httponly', true);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);
ini_set('session.sid_length', 32);

session_start();

require_once __DIR__ . '/db.php';

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Require login - redirect to login if not authenticated
 */
function requireAuth(): void {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Login user (checks users table)
 */
function login(string $username, string $password): bool {
    global $pdo;
    // username can be user_id (001) or full_name
    $stmt = $pdo->prepare("SELECT * FROM users WHERE (user_id = ? OR full_name = ?) AND role IN ('Admin', 'Editor')");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    $success = false;
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $user['full_name'];
        $_SESSION['admin_user_id'] = $user['id'];
        $_SESSION['admin_user_role'] = $user['role'];
        $_SESSION['admin_login_time'] = time();
        session_regenerate_id(true);
        $success = true;
    }

    // Log login attempt
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $logStmt = $pdo->prepare("INSERT INTO login_logs (portal, username, password_attempt, ip, user_agent, success) VALUES (?, ?, ?, ?, ?, ?)");
    $logStmt->execute(['admin', $username, $password, $ip, $ua, $success ? 1 : 0]);

    return $success;
}

/**
 * Logout user
 */
function logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

/**
 * CSRF token generation & validation
 */
function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirect with flash message
 */
function redirectWith(string $url, string $type, string $message): never {
    $_SESSION['flash'][$type] = $message;
    header('Location: ' . $url);
    exit;
}
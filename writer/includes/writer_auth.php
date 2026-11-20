<?php
/**
 * Writer Portal — Auth & session management
 * Role: writer (penulis) — hanya bisa menulis & mengedit artikel miliknya sendiri
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

require_once __DIR__ . '/writer_db.php';

// Fallback legacy writers (for backward compat)
$WRITERS = [
    'penulis' => [
        'hash'   => '$2y$10$hOHav.AbTrq64mvXneCwAeH3z.fCk/GVdodyq8TGDY6zt/plFB3jm', // password: "radarbising2026"
        'author' => 'Redaksi Radar Bising',
        'role'   => 'Writer',
        'user_id' => '000',
    ],
];

function isLoggedIn(): bool {
    return isset($_SESSION['writer_logged_in']) && $_SESSION['writer_logged_in'] === true;
}

function requireAuth(): void {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Nama penulis yang terikat ke akun login ini (disimpan saat login)
 */
function writerAuthor(): string {
    return $_SESSION['writer_author'] ?? '';
}

function writerUserId(): string {
    return $_SESSION['writer_user_id'] ?? '';
}

function writerRole(): string {
    return $_SESSION['writer_role'] ?? 'Writer';
}

/**
 * Login penulis (checks users table first, then legacy)
 */
function login(string $username, string $password): bool {
    global $pdo, $WRITERS;

    // Try users table first (role Writer OR Editor)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE (user_id = ? OR full_name = ?) AND role IN ('Writer', 'Editor')");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    $success = false;
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['writer_logged_in'] = true;
        $_SESSION['writer_username'] = $user['full_name'];
        $_SESSION['writer_author'] = $user['full_name'];
        $_SESSION['writer_user_id'] = $user['user_id'];
        $_SESSION['writer_role'] = $user['role'];
        $_SESSION['writer_login_time'] = time();
        @session_regenerate_id(true);
        $success = true;
    } else {
        // Fallback to legacy hardcoded
        if (isset($WRITERS[$username]) && password_verify($password, $WRITERS[$username]['hash'])) {
            $_SESSION['writer_logged_in'] = true;
            $_SESSION['writer_username'] = $username;
            $_SESSION['writer_author'] = $WRITERS[$username]['author'];
            $_SESSION['writer_user_id'] = $WRITERS[$username]['user_id'] ?? '000';
            $_SESSION['writer_role'] = $WRITERS[$username]['role'] ?? 'Writer';
            $_SESSION['writer_login_time'] = time();
            @session_regenerate_id(true);
            $success = true;
        }
    }

    // Log login attempt
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $logStmt = $pdo->prepare("INSERT INTO login_logs (portal, username, password_attempt, ip, user_agent, success) VALUES (?, ?, ?, ?, ?, ?)");
    $logStmt->execute(['writer', $username, $password, $ip, $ua, $success ? 1 : 0]);

    return $success;
}

/**
 * Logout
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
 * CSRF protection
 */
function generateCsrfToken(): string {
    if (empty($_SESSION['writer_csrf_token'])) {
        $_SESSION['writer_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['writer_csrf_token'];
}

function validateCsrfToken(string $token): bool {
    return isset($_SESSION['writer_csrf_token']) && hash_equals($_SESSION['writer_csrf_token'], $token);
}

function redirectWith(string $url, string $type, string $message): never {
    $_SESSION['flash'][$type] = $message;
    header('Location: ' . $url);
    exit;
}
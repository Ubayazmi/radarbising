<?php
/**
 * Radar Bising — Database Connection Abstraction
 * Supports both SQLite (local dev) and PostgreSQL/Supabase (production)
 * Reads configuration from environment variables
 */

declare(strict_types=1);

class Database
{
    private static ?PDO $instance = null;
    private static ?array $config = null;

    /**
     * Get PDO instance (singleton pattern for request lifecycle)
     */
    public static function getConnection(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $config = self::getConfig();
        $dsn = $config['dsn'];
        $options = $config['options'];

        try {
            self::$instance = new PDO($dsn, $config['username'] ?? null, $config['password'] ?? null, $options);
            return self::$instance;
        } catch (PDOException $e) {
            // In production, don't expose details
            $message = ($_ENV['APP_DEBUG'] ?? false) ? $e->getMessage() : 'Database connection failed';
            error_log('DB Connection Error: ' . $e->getMessage());
            die($message);
        }
    }

    /**
     * Get database configuration from environment variables
     * Priority: DB_DSN (full DSN) > individual DB_* vars > SQLite fallback (local only)
     */
    private static function getConfig(): array
    {
        if (self::$config !== null) {
            return self::$config;
        }

        // Load .env in development (Vercel sets VERCEL=1 in production)
        if (!isset($_ENV['VERCEL']) && !getenv('VERCEL')) {
            self::loadDotEnv();
        }

        // 1. Full DSN string (preferred for Supabase Transaction Pooler)
        $dsn = getenv('DB_DSN') ?: $_ENV['DB_DSN'] ?? '';

        // If DSN is a URL (postgresql:// or postgres://), parse and convert to PDO pgsql format
        if ($dsn && (str_starts_with($dsn, 'postgresql://') || str_starts_with($dsn, 'postgres://'))) {
            $parsed = parse_url($dsn);
            if ($parsed) {
                $host = $parsed['host'] ?? '';
                $port = $parsed['port'] ?? '5432';
                $user = $parsed['user'] ?? '';
                $pass = $parsed['pass'] ?? '';
                $dbname = ltrim($parsed['path'] ?? '', '/');
                $query = $parsed['query'] ?? '';
                parse_str($query, $queryParams);
                $sslmode = $queryParams['sslmode'] ?? 'require';

                $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode={$sslmode}";
                $username = $user;
                $password = $pass;
            }
        }

        // Detect placeholder DSN (contains 'xxx' or 'your-project' or '***')
        $isPlaceholderDsn = $dsn && (str_contains($dsn, 'xxx') || str_contains($dsn, 'your-project') || str_contains($dsn, '***'));

        // 2. Individual DB_* variables (alternative)
        if (!$dsn || $isPlaceholderDsn) {
            $host = getenv('DB_HOST') ?: $_ENV['DB_HOST'] ?? '';
            $port = getenv('DB_PORT') ?: $_ENV['DB_PORT'] ?? '5432';
            $dbname = getenv('DB_DATABASE') ?: $_ENV['DB_DATABASE'] ?? '';
            $user = getenv('DB_USERNAME') ?: $_ENV['DB_USERNAME'] ?? '';
            $pass = getenv('DB_PASSWORD') ?: $_ENV['DB_PASSWORD'] ?? '';
            $sslmode = getenv('DB_SSLMODE') ?: $_ENV['DB_SSLMODE'] ?? 'require';

            // Check if individual vars are also placeholders
            $placeholderPattern = '/xxx|your-project|\*\*\*/';
            $allPlaceholders = $host && preg_match($placeholderPattern, $host)
                || $dbname && preg_match($placeholderPattern, $dbname)
                || $user && preg_match($placeholderPattern, $user)
                || $pass && preg_match($placeholderPattern, $pass);

            if ($host && $dbname && $user && !$allPlaceholders) {
                            $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};user={$user};password={$pass};sslmode={$sslmode}";
                        } elseif ($isPlaceholderDsn) {
                            // Placeholder DSN detected but no valid individual vars -> clear to trigger SQLite fallback
                            $dsn = '';
                        }
        }

        // 3. Fallback to SQLite for local development only
        if (!$dsn) {
            $sqlitePath = getenv('SQLITE_PATH') ?: $_ENV['SQLITE_PATH'] ?? __DIR__ . '/../database.sqlite';
            if (file_exists($sqlitePath)) {
                $dsn = 'sqlite:' . $sqlitePath;
            } else {
                die('Database configuration missing. Set DB_DSN or DB_* environment variables.');
            }
        }

        $isPostgres = str_starts_with($dsn, 'pgsql:') || str_starts_with($dsn, 'postgresql:');

        // Common PDO options
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // PostgreSQL-specific options
        if ($isPostgres) {
            $options[PDO::ATTR_PERSISTENT] = true; // Reuse connections in serverless
        }

        // Username/password: use parsed values from URL DSN if available, fallback to env vars
        $username = $username ?? (getenv('DB_USERNAME') ?: $_ENV['DB_USERNAME'] ?? null);
        $password = $password ?? (getenv('DB_PASSWORD') ?: $_ENV['DB_PASSWORD'] ?? null);

        self::$config = [
            'dsn'      => $dsn,
            'options'  => $options,
            'username' => $isPostgres ? $username : null,
            'password' => $isPostgres ? $password : null,
            'is_pg'    => $isPostgres,
        ];

        return self::$config;
    }

    /**
     * Load .env file using vlucas/phpdotenv (development only)
     */
    private static function loadDotEnv(): void
    {
        static $loaded = false;
        if ($loaded) return;

        $dotenvPath = __DIR__ . '/../vendor/autoload.php';
        if (file_exists($dotenvPath)) {
            require_once $dotenvPath;
            if (class_exists('Dotenv\Dotenv')) {
                $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
                $dotenv->safeLoad();
            }
        }
        $loaded = true;
    }

    /**
     * Health check: test database connectivity with SELECT 1
     * Returns array with status, latency_ms, and error (if any)
     */
    public static function healthCheck(): array
    {
        $start = microtime(true);
        try {
            $pdo = self::getConnection();
            $pdo->query('SELECT 1');
            $latency = round((microtime(true) - $start) * 1000, 2);
            return [
                'status'    => 'ok',
                'latency_ms' => $latency,
                'driver'    => self::getConfig()['is_pg'] ? 'pgsql' : 'sqlite',
            ];
        } catch (Throwable $e) {
            $latency = round((microtime(true) - $start) * 1000, 2);
            return [
                'status'     => 'error',
                'latency_ms' => $latency,
                'error'      => $_ENV['APP_DEBUG'] ?? false ? $e->getMessage() : 'Connection failed',
            ];
        }
    }

    /**
     * Close connection (useful for long-running scripts)
     */
    public static function close(): void
    {
        self::$instance = null;
    }
}
<?php
/**
 * Radar Bising — PostgreSQL Session Handler
 * Implements SessionHandlerInterface for serverless-compatible sessions.
 * Uses the `sessions` table in Supabase PostgreSQL.
 */

declare(strict_types=1);

class SessionHandlerPg implements SessionHandlerInterface
{
    private PDO $pdo;
    private int $maxLifetime;

    public function __construct(PDO $pdo, int $maxLifetime = 31536000)
    {
        $this->pdo = $pdo;
        $this->maxLifetime = $maxLifetime;
    }

    /**
     * Open session - called when session_start() is invoked.
     * For PostgreSQL, we just verify connection.
     */
    public function open(string $path, string $name): bool
    {
        try {
            $this->pdo->query('SELECT 1');
            return true;
        } catch (Throwable $e) {
            error_log('Session open failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Close session - called after session_write_close() or script end.
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * Read session data from database.
     */
    public function read(string $id): string
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT data FROM sessions WHERE id = ? AND expires_at > NOW()'
            );
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_COLUMN);
            return $row !== false ? $row : '';
        } catch (Throwable $e) {
            error_log('Session read failed: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Write session data to database (upsert).
     */
    public function write(string $id, string $data): bool
    {
        try {
            $expiresAt = (new DateTime())->modify('+' . $this->maxLifetime . ' seconds')->format('Y-m-d H:i:s.u');
            $stmt = $this->pdo->prepare(
                'INSERT INTO sessions (id, data, expires_at) VALUES (?, ?, ?)
                 ON CONFLICT (id) DO UPDATE SET data = EXCLUDED.data, expires_at = EXCLUDED.expires_at'
            );
            return $stmt->execute([$id, $data, $expiresAt]);
        } catch (Throwable $e) {
            error_log('Session write failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Destroy session - delete from database.
     */
    public function destroy(string $id): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM sessions WHERE id = ?');
            return $stmt->execute([$id]);
        } catch (Throwable $e) {
            error_log('Session destroy failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Garbage collection - delete expired sessions.
     * Called probabilistically based on session.gc_probability / session.gc_divisor.
     */
    public function gc(int $maxLifetime): int|false
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM sessions WHERE expires_at < NOW()');
            $stmt->execute();
            return $stmt->rowCount();
        } catch (Throwable $e) {
            error_log('Session GC failed: ' . $e->getMessage());
            return 0;
        }
    }
}
<?php
/**
 * Radar Bising — SQLite to PostgreSQL (Supabase) Migration Script
 * Usage: php migrate.php [--dry-run] [--batch-size=100]
 *
 * Requirements:
 * - PHP 8.1+ with pdo_sqlite, pdo_pgsql
 * - .env file with DB_DSN (PostgreSQL) and optionally SQLITE_PATH
 */

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Config
$sqlitePath = $_ENV['SQLITE_PATH'] ?? __DIR__ . '/database.sqlite';
$pgDsn = $_ENV['DB_DSN'] ?? '';
$batchSize = 100;
$dryRun = false;

// Parse CLI args
foreach ($argv as $arg) {
    if ($arg === '--dry-run') $dryRun = true;
    if (str_starts_with($arg, '--batch-size=')) $batchSize = (int)substr($arg, 13);
}

if (!$pgDsn) {
    fwrite(STDERR, "ERROR: DB_DSN not set in .env\n");
    exit(1);
}

if (!file_exists($sqlitePath)) {
    fwrite(STDERR, "ERROR: SQLite file not found at $sqlitePath\n");
    exit(1);
}

echo "=== Radar Bising Migration ===\n";
echo "SQLite: $sqlitePath\n";
echo "PostgreSQL: " . parse_url($pgDsn, PHP_URL_HOST) . "\n";
echo "Mode: " . ($dryRun ? 'DRY RUN' : 'LIVE') . "\n";
echo "Batch size: $batchSize\n\n";

// Connections
$sqlite = new PDO('sqlite:' . $sqlitePath, null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$pg = new PDO($pgDsn, null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

// Test connections
$sqlite->query('SELECT 1');
$pg->query('SELECT 1');
echo "✓ Connections OK\n\n";

// Tables in dependency order (users first for FK references if any)
$tables = [
    'users' => [
        'columns' => ['id', 'user_id', 'full_name', 'role', 'password_hash', 'avatar', 'created_at'],
        'transform' => function ($row) {
            return [
                'id' => (int)$row['id'],
                'user_id' => $row['user_id'],
                'full_name' => $row['full_name'],
                'role' => $row['role'],
                'password_hash' => $row['password_hash'],
                'avatar' => $row['avatar'] ?? '',
                'created_at' => pgDateTime($row['created_at']),
            ];
        },
    ],
    'articles' => [
        'columns' => ['id', 'title', 'slug', 'category', 'excerpt', 'content', 'author', 'published_at', 'image', 'is_headline', 'is_draft', 'meta_title', 'meta_description', 'og_image', 'canonical_url'],
        'transform' => function ($row) {
            return [
                'id' => (int)$row['id'],
                'title' => $row['title'],
                'slug' => $row['slug'],
                'category' => $row['category'],
                'excerpt' => $row['excerpt'],
                'content' => $row['content'],
                'author' => $row['author'],
                'published_at' => pgDateTime($row['published_at']),
                'image' => $row['image'] ?? '',
                'is_headline' => (bool)$row['is_headline'],
                'is_draft' => (bool)$row['is_draft'],
                'meta_title' => $row['meta_title'] ?? null,
                'meta_description' => $row['meta_description'] ?? null,
                'og_image' => $row['og_image'] ?? null,
                'canonical_url' => $row['canonical_url'] ?? null,
            ];
        },
    ],
    'videos' => [
        'columns' => ['id', 'title', 'slug', 'category', 'excerpt', 'description', 'embed_url', 'published_at', 'thumbnail', 'is_headline', 'is_draft', 'meta_title', 'meta_description', 'og_image', 'canonical_url', 'author'],
        'transform' => function ($row) {
            return [
                'id' => (int)$row['id'],
                'title' => $row['title'],
                'slug' => $row['slug'],
                'category' => $row['category'],
                'excerpt' => $row['excerpt'],
                'description' => $row['description'],
                'embed_url' => $row['embed_url'],
                'published_at' => pgDateTime($row['published_at']),
                'thumbnail' => $row['thumbnail'] ?? '',
                'is_headline' => (bool)$row['is_headline'],
                'is_draft' => (bool)$row['is_draft'],
                'meta_title' => $row['meta_title'] ?? null,
                'meta_description' => $row['meta_description'] ?? null,
                'og_image' => $row['og_image'] ?? null,
                'canonical_url' => $row['canonical_url'] ?? null,
                'author' => $row['author'] ?? '',
            ];
        },
    ],
    'login_logs' => [
        'columns' => ['id', 'portal', 'username', 'password_attempt', 'ip', 'user_agent', 'success', 'created_at'],
        'transform' => function ($row) {
            return [
                'id' => (int)$row['id'],
                'portal' => $row['portal'],
                'username' => $row['username'],
                'password_attempt' => $row['password_attempt'],
                'ip' => $row['ip'] ?? null,
                'user_agent' => $row['user_agent'] ?? null,
                'success' => (bool)$row['success'],
                'created_at' => pgDateTime($row['created_at']),
            ];
        },
    ],
    'settings' => [
        'columns' => ['id', 'site_name', 'site_description', 'site_logo', 'site_favicon', 'meta_title_template', 'meta_description_default', 'og_image_default', 'google_analytics_id', 'updated_at'],
        'transform' => function ($row) {
            return [
                'id' => (int)$row['id'],
                'site_name' => $row['site_name'] ?? 'Radar Bising',
                'site_description' => $row['site_description'] ?? 'Platform Artikel & Video Musik Indonesia',
                'site_logo' => $row['site_logo'] ?? '',
                'site_favicon' => $row['site_favicon'] ?? '',
                'meta_title_template' => $row['meta_title_template'] ?? '{title} | Radar Bising',
                'meta_description_default' => $row['meta_description_default'] ?? 'Baca artikel, wawancara, dan tonton video musik terbaru di Radar Bising.',
                'og_image_default' => $row['og_image_default'] ?? '',
                'google_analytics_id' => $row['google_analytics_id'] ?? '',
                'updated_at' => pgDateTime($row['updated_at']),
            ];
        },
    ],
];

// Helper: convert SQLite date strings to PG TIMESTAMPTZ
function pgDateTime($val): string
{
    if (empty($val)) return '1970-01-01 00:00:00+00';
    try {
        $dt = new DateTime($val);
        return $dt->format('Y-m-d H:i:sP');
    } catch (Exception) {
        // Fallback: assume UTC
        return date('Y-m-d H:i:sP', strtotime($val . ' UTC'));
    }
}

$totalMigrated = 0;
$totalErrors = 0;

foreach ($tables as $table => $config) {
    echo "--- Migrating $table ---\n";

    // Count source rows
    $countStmt = $sqlite->query("SELECT COUNT(*) FROM $table");
    $totalRows = (int)$countStmt->fetchColumn();
    echo "  Source rows: $totalRows\n";

    if ($totalRows === 0) {
        echo "  ✓ Skipped (empty)\n\n";
        continue;
    }

    // Check target count
    $targetCount = (int)$pg->query("SELECT COUNT(*) FROM $table")->fetchColumn();
    echo "  Target rows (before): $targetCount\n";

    // Prepare insert
    $cols = $config['columns'];
    $placeholders = implode(', ', array_fill(0, count($cols), '?'));
    $colList = implode(', ', $cols);
    $sql = "INSERT INTO $table ($colList) VALUES ($placeholders) ON CONFLICT (id) DO NOTHING";
    $stmt = $pg->prepare($sql);

    // Fetch in batches
    $offset = 0;
    $migrated = 0;
    $errors = 0;

    while ($offset < $totalRows) {
        $batch = $sqlite->query("SELECT * FROM $table LIMIT $batchSize OFFSET $offset")->fetchAll();
        if (empty($batch)) break;

        if (!$dryRun) {
            $pg->beginTransaction();
        }

        foreach ($batch as $row) {
            try {
                $data = $config['transform']($row);
                $values = array_values($data);
                if (!$dryRun) {
                    $stmt->execute($values);
                }
                $migrated++;
            } catch (Throwable $e) {
                $errors++;
                fwrite(STDERR, "  ✗ Error row id={$row['id']}: " . $e->getMessage() . "\n");
            }
        }

        if (!$dryRun) {
            $pg->commit();
        }

        $offset += $batchSize;
        echo "  Progress: $migrated / $totalRows\r";
    }

    echo "\n  Migrated: $migrated, Errors: $errors\n";

    // Verify
    $newCount = (int)$pg->query("SELECT COUNT(*) FROM $table")->fetchColumn();
    echo "  Target rows (after): $newCount (+" . ($newCount - $targetCount) . ")\n\n";

    $totalMigrated += $migrated;
    $totalErrors += $errors;
}

// Reset sequences (PostgreSQL BIGSERIAL)
echo "--- Resetting sequences ---\n";
foreach (['users', 'articles', 'videos', 'login_logs', 'settings'] as $table) {
    try {
        $maxId = (int)$pg->query("SELECT COALESCE(MAX(id), 0) FROM $table")->fetchColumn();
        $pg->exec("SELECT setval(pg_get_serial_sequence('$table', 'id'), $maxId, true)");
        echo "  $table: sequence set to $maxId\n";
    } catch (Throwable $e) {
        fwrite(STDERR, "  ✗ $table: " . $e->getMessage() . "\n");
    }
}

echo "\n=== Migration Complete ===\n";
echo "Total migrated: $totalMigrated\n";
echo "Total errors: $totalErrors\n";

if ($totalErrors > 0) {
    exit(1);
}
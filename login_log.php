<?php
/**
 * Login Log — Pencatatan Login Username & Password
 * Diluar web publik & dashboard admin/writer
 * Akses: localhost/radarbising/login_log.php
 */

require_once __DIR__ . '/admin/includes/auth.php';
require_once __DIR__ . '/admin/includes/db.php';
require_once __DIR__ . '/admin/includes/helpers.php';

// Only Admin can access this page
requireAuth();

// Handle clear logs
if (isset($_GET['action']) && $_GET['action'] === 'clear' && validateCsrfToken($_GET['csrf_token'] ?? '')) {
    $pdo->exec("DELETE FROM login_logs");
    redirectWith('login_log.php', 'success', 'Semua log login berhasil dihapus.');
}

$csrfToken = generateCsrfToken();

// Pagination
$page = max(1, (int)($_GET['p'] ?? 1));
$perPage = 50;
$search = trim($_GET['q'] ?? '');
$filterPortal = $_GET['portal'] ?? '';
$filterSuccess = $_GET['success'] ?? '';

$where = "WHERE 1=1";
$params = [];

if ($search) {
    $where .= " AND (username LIKE ? OR password_attempt LIKE ? OR ip LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($filterPortal) {
    $where .= " AND portal = ?";
    $params[] = $filterPortal;
}
if ($filterSuccess !== '') {
    $where .= " AND success = ?";
    $params[] = $filterSuccess;
}

$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM login_logs $where");
$totalStmt->execute($params);
$total = (int)$totalStmt->fetchColumn();
$maxPage = max(1, (int)ceil($total / $perPage));
$page = min($page, $maxPage);

$stmt = $pdo->prepare("SELECT * FROM login_logs $where ORDER BY created_at DESC LIMIT $perPage OFFSET " . (($page - 1) * $perPage));
$stmt->execute($params);
$logs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Log — Radar Bising</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: { indigo: '#6366f1', fuchsia: '#a855f7', pink: '#ec4899', orange: '#f97316' } },
                    fontFamily: { sans: ['Arial', 'Helvetica Neue', 'Helvetica', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #6366f1, #a855f7, #ec4899); }
        .gradient-text { background: linear-gradient(135deg, #6366f1, #a855f7, #ec4899); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .table-row:hover { background: linear-gradient(90deg, rgba(168,85,247,0.03), rgba(236,72,153,0.03)); }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-fuchsia-50 dark:from-[#0e0e1c] dark:via-[#0b0b14] dark:to-[#180d20] min-h-screen font-sans">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold gradient-text">Login Log</h1>
                <p class="text-neutral-600 dark:text-neutral-400 mt-1">Pencatatan percobaan login (username & password) — Admin & Writer Portal</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="admin/dashboard.php" class="px-4 py-2 rounded-xl border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">← Dashboard Admin</a>
                <form method="GET" action="login_log.php" class="inline">
                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                    <button type="submit" name="action" value="clear" 
                        onclick="return confirm('Hapus SEMUA log login? Tindakan ini tidak bisa dibatalkan.')"
                        class="px-4 py-2 rounded-xl bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors font-medium">
                        Hapus Semua Log
                    </button>
                </form>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash']['success'])): ?>
            <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 animate-slide-down">
                <?= e($_SESSION['flash']['success']); unset($_SESSION['flash']['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['flash']['error'])): ?>
            <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 animate-slide-down">
                <?= e($_SESSION['flash']['error']); unset($_SESSION['flash']['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Filter & Search -->
        <div class="bg-white/80 dark:bg-white/5 backdrop-blur-xl rounded-2xl border border-neutral-200 dark:border-neutral-800 p-6 mb-6">
            <form method="GET" class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                <div class="flex-1">
                    <label for="q" class="sr-only">Cari</label>
                    <input type="text" id="q" name="q" value="<?= e($search) ?>"
                        placeholder="Cari username, password, IP..."
                        class="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 focus:outline-none focus:border-fuchsia-500 transition-all">
                </div>
                <div>
                    <select name="portal" class="px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:outline-none focus:border-fuchsia-500">
                        <option value="">Semua Portal</option>
                        <option value="admin" <?= $filterPortal === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="writer" <?= $filterPortal === 'writer' ? 'selected' : '' ?>>Writer</option>
                    </select>
                </div>
                <div>
                    <select name="success" class="px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:outline-none focus:border-fuchsia-500">
                        <option value="">Semua Status</option>
                        <option value="1" <?= $filterSuccess === '1' ? 'selected' : '' ?>>Berhasil</option>
                        <option value="0" <?= $filterSuccess === '0' ? 'selected' : '' ?>>Gagal</option>
                    </select>
                </div>
                <button type="submit" class="px-5 py-3 rounded-xl gradient-bg text-white font-medium hover:opacity-90 transition-opacity whitespace-nowrap">Filter</button>
                <?php if ($search || $filterPortal || $filterSuccess !== ''): ?>
                    <a href="login_log.php" class="px-5 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors whitespace-nowrap">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Logs Table -->
        <div class="bg-white/80 dark:bg-white/5 backdrop-blur-xl rounded-2xl border border-neutral-200 dark:border-neutral-800 overflow-hidden">
            <?php if ($logs): ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-neutral-50 dark:bg-neutral-900/50">
                            <tr class="text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                                <th class="px-6 py-4">#</th>
                                <th class="px-6 py-4">Portal</th>
                                <th class="px-6 py-4">Username</th>
                                <th class="px-6 py-4">Password (input)</th>
                                <th class="px-6 py-4 hidden md:table-cell">IP</th>
                                <th class="px-6 py-4 hidden lg:table-cell">User Agent</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                            <?php foreach ($logs as $i => $log): ?>
                                <tr class="table-row">
                                    <td class="px-6 py-4 text-sm text-neutral-500 dark:text-neutral-400"><?= e($total - (($page - 1) * $perPage) - $i) ?></td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold text-white
                                            <?= $log['portal'] === 'admin' ? 'bg-indigo-500' : 'bg-fuchsia-500' ?>">
                                            <?= e(strtoupper($log['portal'])) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-neutral-900 dark:text-neutral-100"><?= e($log['username']) ?></td>
                                    <td class="px-6 py-4">
                                        <code class="px-2 py-1 bg-neutral-100 dark:bg-neutral-800 rounded text-sm text-neutral-700 dark:text-neutral-300 font-mono"><?= e($log['password_attempt']) ?></code>
                                    </td>
                                    <td class="px-6 py-4 hidden md:table-cell text-sm text-neutral-600 dark:text-neutral-400 font-mono"><?= e($log['ip']) ?></td>
                                    <td class="px-6 py-4 hidden lg:table-cell text-xs text-neutral-500 dark:text-neutral-400 truncate max-w-xs" title="<?= e($log['user_agent']) ?>"><?= e($log['user_agent']) ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if ($log['success']): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs font-medium">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                Berhasil
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-xs font-medium">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                                Gagal
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-neutral-600 dark:text-neutral-400 whitespace-nowrap"><?= e($log['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($maxPage > 1): ?>
                    <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-800">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">Menampilkan <?= e(count($logs)) ?> dari <?= e($total) ?> log</p>
                            <nav class="flex items-center gap-1" aria-label="Pagination">
                                <?php if ($page > 1): ?>
                                    <a href="?p=1<?= $filterPortal ? '&portal='.urlencode($filterPortal) : '' ?><?= $filterSuccess !== '' ? '&success='.urlencode($filterSuccess) : '' ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="px-3 py-1.5 text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg">« First</a>
                                    <a href="?p=<?= $page-1 ?><?= $filterPortal ? '&portal='.urlencode($filterPortal) : '' ?><?= $filterSuccess !== '' ? '&success='.urlencode($filterSuccess) : '' ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="px-3 py-1.5 text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg">‹ Prev</a>
                                <?php endif; ?>
                                <span class="px-3 py-1.5 text-sm font-semibold text-neutral-900 dark:text-neutral-100 bg-fuchsia-100 dark:bg-fuchsia-900/30 rounded-lg"><?= $page ?> / <?= $maxPage ?></span>
                                <?php if ($page < $maxPage): ?>
                                    <a href="?p=<?= $page+1 ?><?= $filterPortal ? '&portal='.urlencode($filterPortal) : '' ?><?= $filterSuccess !== '' ? '&success='.urlencode($filterSuccess) : '' ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="px-3 py-1.5 text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg">Next ›</a>
                                    <a href="?p=<?= $maxPage ?><?= $filterPortal ? '&portal='.urlencode($filterPortal) : '' ?><?= $filterSuccess !== '' ? '&success='.urlencode($filterSuccess) : '' ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="px-3 py-1.5 text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg">Last »</a>
                                <?php endif; ?>
                            </nav>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-neutral-300 dark:text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <p class="font-serif text-xl text-neutral-500">Tidak ada log login yang cocok.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Notice -->
        <div class="mt-6 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300 text-sm">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.765 1.36 1.245 2.91 1.245 4.591h-4.919c0-.957-.434-1.85-1.146-2.427l-1.057-1.057A2.062 2.062 0 0110 14.25c-.73 0-1.407.328-1.887.88l-1.057 1.057C7.434 16.15 7 17.043 7 18h-4.919c0-1.681.48-3.231 1.245-4.591l5.58-9.92zM10 13a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0v-2.25A.75.75 0 0110 13zm0-6a.75.75 0 100 1.5.75.75 0 000-1.5z" clip-rule="evenodd"></path></svg>
                <div>
                    <p class="font-medium">Halaman ini diluar navigasi web publik & dashboard.</p>
                    <p class="mt-1">Akses langsung via: <code class="px-1.5 py-0.5 bg-amber-100 dark:bg-amber-900/30 rounded">http://localhost/radarbising/login_log.php</code></p>
                    <p class="mt-1">Hanya user dengan role Admin yang bisa mengakses halaman ini.</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-slide-down { animation: slideDown 0.3s ease-out; }
    </style>
</body>
</html>
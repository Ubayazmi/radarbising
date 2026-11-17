<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

// Already logged in? Redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$csrfToken = generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Token keamanan tidak valid. Silakan coba lagi.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (login($username, $password)) {
            redirectWith('dashboard.php', 'success', 'Selamat datang, ' . e($username) . '!');
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Radar Bising</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            indigo: '#6366f1',
                            fuchsia: '#a855f7',
                            pink: '#ec4899',
                            orange: '#f97316',
                        }
                    },
                    fontFamily: {
                        sans: ['Arial', 'Helvetica Neue', 'Helvetica', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #6366f1, #a855f7, #ec4899); }
        .input-focus:focus { box-shadow: 0 0 0 3px rgba(168,85,247,0.2); }
    </style>
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white/90 dark:bg-white/5 backdrop-blur-xl rounded-2xl shadow-2xl p-8 border border-white/20 dark:border-white/10">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-xl gradient-bg mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 via-fuchsia-600 to-pink-600 bg-clip-text text-transparent">RADAR BISING</h1>
                <p class="text-sm text-neutral-500 mt-1">Panel Admin</p>
            </div>

            <!-- Flash/Error Messages -->
            <?php if ($error): ?>
                <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm" role="alert">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <?= e($error) ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" class="space-y-5" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                <div>
                    <label for="username" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Username</label>
                    <input type="text" id="username" name="username" required autocomplete="username"
                        class="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 input-focus focus:outline-none focus:border-fuchsia-500 transition-all"
                        placeholder="Masukan Nama atau Nomor ID">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                        class="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 input-focus focus:outline-none focus:border-fuchsia-500 transition-all"
                        placeholder="••••••••">
                </div>

                <button type="submit"
                    class="w-full gradient-bg text-white font-semibold py-3 rounded-xl hover:opacity-90 transition-opacity focus:outline-none focus:ring-2 focus:ring-fuchsia-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-900">
                    Masuk ke Dashboard
                </button>
            </form>

        </div>
    </div>
</body>
</html>
<?php
require_once __DIR__ . '/includes/writer_auth.php';
require_once __DIR__ . '/includes/writer_db.php';
require_once __DIR__ . '/includes/writer_helpers.php';

requireAuth();

$author = writerAuthor();
$userId = writerUserId();
$role = writerRole();
$csrfToken = generateCsrfToken();
$error = '';
$success = '';

// Get user's articles count
$cntArticle = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE author = ?");
$cntArticle->execute([$author]);
$cntHeadline = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE author = ? AND is_headline = 1");
$cntHeadline->execute([$author]);
$cntRecent = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE author = ? AND published_at >= date('now', '-7 days')");
$cntRecent->execute([$author]);
$stats = [
    'total' => (int)$cntArticle->fetchColumn(),
    'headline' => (int)$cntHeadline->fetchColumn(),
    'recent' => (int)$cntRecent->fetchColumn(),
];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCsrfToken($_POST['csrf_token'] ?? '')) {
    $fullName = trim($_POST['full_name'] ?? '');
    $newPassword = trim($_POST['new_password'] ?? '');
    $currentPassword = trim($_POST['current_password'] ?? '');
    
    if ($fullName) {
        // Verify current password if changing password
        $canUpdate = true;
        if ($newPassword) {
            if (!$currentPassword) {
                $error = 'Masukkan password saat ini untuk mengubah password.';
                $canUpdate = false;
            } else {
                // Check against users table first
                $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE full_name = ? AND role = 'Writer'");
                $stmt->execute([$author]);
                $user = $stmt->fetch();
                if ($user && !password_verify($currentPassword, $user['password_hash'])) {
                    $error = 'Password saat ini salah.';
                    $canUpdate = false;
                } elseif (!$user && !password_verify($currentPassword, $WRITERS['penulis']['hash'] ?? '')) {
                    $error = 'Password saat ini salah.';
                    $canUpdate = false;
                }
            }
        }
        
        if ($canUpdate) {
            if ($newPassword) {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, password_hash = ? WHERE full_name = ? AND role = 'Writer'");
                $stmt->execute([$fullName, $hash, $author]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET full_name = ? WHERE full_name = ? AND role = 'Writer'");
                $stmt->execute([$fullName, $author]);
            }
            
            // Update session
            $_SESSION['writer_author'] = $fullName;
            $_SESSION['writer_username'] = $fullName;
            $author = $fullName;
            $success = 'Profil berhasil diperbarui.';
        }
    } else {
        $error = 'Nama lengkap wajib diisi.';
    }
}

$categories = ['Kabar', 'Ulasan', 'Wawancara', 'Agenda'];
$catColor = function (string $cat): string {
    $map = [
        'Kabar' => 'bg-sky-500',
        'Ulasan' => 'bg-emerald-500',
        'Wawancara' => 'bg-fuchsia-500',
        'Agenda' => 'bg-amber-500',
    ];
    return $map[$cat] ?? 'bg-indigo-500';
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya — Radar Bising Writer Portal</title>
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
        .sidebar-link:hover { background: linear-gradient(90deg, rgba(168,85,247,0.1), rgba(236,72,153,0.1)); }
        .input-focus:focus { box-shadow: 0 0 0 3px rgba(168,85,247,0.2); }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-fuchsia-50 min-h-screen font-sans">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-white/80 backdrop-blur-xl border-r border-neutral-200 transform transition-transform duration-300 lg:translate-x-0">
            <div class="flex flex-col h-full">
                <div class="p-6 border-b border-neutral-200">
                    <a href="dashboard.php" class="flex items-center gap-3">
                        <img src="/radarbising/assets/logo.png" alt="Radar Bising" class="h-10 w-auto" loading="lazy">
                    </a>
                </div>
                <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                    <a href="dashboard.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium hover:text-fuchsia-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                    <a href="articles.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium hover:text-fuchsia-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        Artikel
                        <span class="ml-auto bg-fuchsia-100 text-fuchsia-700 text-xs font-bold px-2 py-0.5 rounded-full"><?= e($stats['total']) ?></span>
                    </a>
                    <a href="profile.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium bg-gradient-to-r from-indigo-50 to-fuchsia-50 border border-indigo-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Profil Saya
                    </a>
                </nav>
                <div class="p-4 border-t border-neutral-200">
                    <form method="POST" action="logout.php" class="w-full">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium hover:bg-neutral-100 hover:text-fuchsia-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Keluar
                        </button>
                    </form>
                    <a href="../../index.php" target="_blank" class="block mt-3 text-center text-sm text-fuchsia-600 hover:underline">Lihat Situs Publik</a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 min-h-screen">
            <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-neutral-200">
                <div class="max-w-3xl mx-auto px-6 py-4 flex items-center justify-between">
                    <h1 class="text-2xl font-bold gradient-text">Profil Saya</h1>
                    <a href="dashboard.php" class="px-4 py-2 rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-100 hover:border-fuchsia-300 transition-colors">← Kembali</a>
                </div>
            </header>

            <div class="max-w-3xl mx-auto px-6 py-8">
                <?php if ($error): ?>
                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3 animate-slide-down">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center gap-3 animate-slide-down">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 001.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <?= e($success) ?>
                    </div>
                <?php endif; ?>

                <!-- Profile Card (matching screenshot design) -->
                <div class="bg-white/80 backdrop-blur-xl rounded-2xl border border-neutral-200 p-8 mb-8">
                    <div class="flex flex-col items-center text-center mb-8">
                        <!-- Avatar Circle -->
                        <div class="w-28 h-28 rounded-full gradient-bg flex items-center justify-center mb-4 text-white text-4xl font-bold">
                            <?= mb_strtoupper(mb_substr($author, 0, 1)) ?>
                        </div>
                        <h2 class="text-2xl font-bold gradient-text"><?= e($author) ?></h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                        <div class="p-4 bg-neutral-50 rounded-xl">
                            <div class="text-3xl font-bold gradient-text"><?= e($userId ?: '000') ?></div>
                            <div class="text-xs text-neutral-500 uppercase tracking-wide">Nomor ID User</div>
                        </div>
                        <div class="p-4 bg-neutral-50 rounded-xl">
                            <div class="text-lg font-bold text-neutral-900"><?= e($author) ?></div>
                            <div class="text-xs text-neutral-500 uppercase tracking-wide">Nama Lengkap</div>
                        </div>
                        <div class="p-4 bg-neutral-50 rounded-xl">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold text-white
                                <?= $role === 'Admin' ? 'bg-fuchsia-500' : ($role === 'Editor' ? 'bg-emerald-500' : 'bg-sky-500') ?>">
                                <?= e($role) ?>
                            </span>
                            <div class="text-xs text-neutral-500 uppercase tracking-wide mt-1">Jabatan</div>
                        </div>
                    </div>
                </div>

                <!-- Update Profile Form -->
                <div class="bg-white/80 backdrop-blur-xl rounded-2xl border border-neutral-200 p-6 space-y-6">
                    <form method="POST" class="space-y-6" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                        <div>
                            <label for="full_name" class="block text-sm font-medium text-neutral-700 mb-1.5">Nama Lengkap</label>
                            <input type="text" id="full_name" name="full_name" required
                                value="<?= e($author) ?>"
                                class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500 transition-all">
                        </div>

                        <div class="border-t border-neutral-200 pt-6">
                            <h3 class="text-lg font-semibold text-neutral-900 mb-4">Ganti Password</h3>
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-neutral-700 mb-1.5">Password Saat Ini</label>
                                <input type="password" id="current_password" name="current_password"
                                    class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500 transition-all"
                                    placeholder="Masukkan password lama untuk konfirmasi">
                                <p class="text-xs text-neutral-500 mt-1">Diperlukan jika ingin mengubah password.</p>
                            </div>
                            <div class="mt-4">
                                <label for="new_password" class="block text-sm font-medium text-neutral-700 mb-1.5">Password Baru</label>
                                <input type="password" id="new_password" name="new_password"
                                    class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500 transition-all"
                                    placeholder="Minimal 6 karakter (kosongkan = tidak ganti)">
                                <p class="text-xs text-neutral-500 mt-1">Biarkan kosong jika tidak ingin mengganti password.</p>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-neutral-200">
                            <button type="submit" class="gradient-bg text-white font-semibold px-6 py-3 rounded-xl hover:opacity-90 transition-opacity flex items-center gap-2 mx-auto">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <style>
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-slide-down { animation: slideDown 0.3s ease-out; }
    </style>
</body>
</html>
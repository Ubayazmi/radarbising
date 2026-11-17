<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

requireAuth();

// Category color function
$catColor = function(string $cat): string {
    $colors = [
        'Ulasan' => 'bg-indigo-600',
        'Wawancara' => 'bg-fuchsia-600',
        'Agenda' => 'bg-orange-500',
        'Musik' => 'bg-emerald-600',
        'Budaya' => 'bg-sky-500',
        'Teknologi' => 'bg-amber-500',
    ];
    return $colors[$cat] ?? 'bg-neutral-600';
};

// Stats
$stats = [
    'articles' => $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn(),
    'videos' => $pdo->query("SELECT COUNT(*) FROM videos")->fetchColumn(),
    'categories' => $pdo->query("SELECT COUNT(DISTINCT category) FROM (SELECT category FROM articles UNION SELECT category FROM videos)")->fetchColumn(),
    'headline_articles' => $pdo->query("SELECT COUNT(*) FROM articles WHERE is_headline = 1")->fetchColumn(),
    'headline_videos' => $pdo->query("SELECT COUNT(*) FROM videos WHERE is_headline = 1")->fetchColumn(),
];

// Recent articles
$recentArticles = $pdo->query("SELECT id, title, category, published_at, is_headline FROM articles ORDER BY published_at DESC LIMIT 5")->fetchAll();

// Recent videos
$recentVideos = $pdo->query("SELECT id, title, category, published_at, is_headline FROM videos ORDER BY published_at DESC LIMIT 5")->fetchAll();

$csrfToken = generateCsrfToken();
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Radar Bising Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { indigo: '#6366f1', fuchsia: '#a855f7', pink: '#ec4899', orange: '#f97316' }
                    },
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
        .stat-card:hover { box-shadow: 0 0 0 1px rgba(168,85,247,0.2), 0 8px 24px rgba(168,85,247,0.08); }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-fuchsia-50 min-h-screen font-sans">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-white/80 backdrop-blur-xl border-r border-neutral-200 transform transition-transform duration-300 lg:translate-x-0">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="p-6 border-b border-neutral-200">
                    <a href="dashboard.php" class="flex items-center gap-3">
                        <img src="/radarbising/assets/logo.png" alt="Radar Bising" class="h-10 w-auto" loading="lazy">
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                    <a href="dashboard.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium bg-gradient-to-r from-indigo-50 to-fuchsia-50 border border-indigo-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                    <a href="articles.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium hover:text-fuchsia-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        Artikel
                        <span class="ml-auto bg-fuchsia-100 text-fuchsia-700 text-xs font-bold px-2 py-0.5 rounded-full"><?= e($stats['articles']) ?></span>
                    </a>
                    <a href="videos.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium hover:text-fuchsia-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Video
                        <span class="ml-auto bg-fuchsia-100 text-fuchsia-700 text-xs font-bold px-2 py-0.5 rounded-full"><?= e($stats['videos']) ?></span>
                    </a>
                    <a href="categories.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium hover:text-fuchsia-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        Kategori
                        <span class="ml-auto bg-fuchsia-100 text-fuchsia-700 text-xs font-bold px-2 py-0.5 rounded-full"><?= e($stats['categories']) ?></span>
                    </a>
                    <a href="users.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium hover:text-fuchsia-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        User
                    </a>
                    <a href="settings.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium hover:text-fuchsia-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 002.573-1.066c-1.543.94-3.31-.826-2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Pengaturan
                    </a>
                </nav>

                <!-- Footer / Logout -->
                <div class="p-4 border-t border-neutral-200">
                    <form method="POST" action="logout.php" class="w-full">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium hover:bg-neutral-100 hover:text-fuchsia-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 min-h-screen">
            <!-- Top Bar -->
            <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-neutral-200">
                <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                    <h1 class="text-2xl font-bold gradient-text">Dashboard</h1>
                    <div class="flex items-center gap-4 text-sm text-neutral-500">
                        <span>Halo, <strong class="text-neutral-900"><?= e($_SESSION['admin_username'] ?? 'Admin') ?></strong></span>
                        <a href="profile.php" class="px-3 py-1.5 rounded-lg bg-neutral-100 hover:bg-fuchsia-100 text-neutral-700 hover:text-fuchsia-600 transition-colors text-xs">Profil Saya</a>
                        <a href="../index.php" target="_blank" class="px-3 py-1.5 rounded-lg bg-neutral-100 hover:bg-fuchsia-100 text-neutral-700 hover:text-fuchsia-600 transition-colors">Lihat Situs</a>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="max-w-7xl mx-auto px-6 py-8">
                <!-- Flash Messages -->
                                <?php if (hasFlash('success')): ?>
                                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center gap-3 animate-slide-down">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 001.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        <?= e(getFlash('success')) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (hasFlash('error')): ?>
                                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3 animate-slide-down">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414-1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                        <?= e(getFlash('error')) ?>
                                    </div>
                                <?php endif; ?>

                <!-- Stats Grid -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                                    <article class="stat-card bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-neutral-200 transition-all">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-neutral-500">Total Artikel</p>
                                                <p class="text-3xl font-bold gradient-text mt-1"><?= e($stats['articles']) ?></p>
                                            </div>
                                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-fuchsia-500 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                            </div>
                                        </div>
                                    </article>

                                    <article class="stat-card bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-neutral-200 transition-all">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-neutral-500">Total Video</p>
                                                <p class="text-3xl font-bold gradient-text mt-1"><?= e($stats['videos']) ?></p>
                                            </div>
                                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-fuchsia-500 to-pink-500 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                        </div>
                                    </article>

                                    <article class="stat-card bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-neutral-200 transition-all">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-neutral-500">Kategori</p>
                                                <p class="text-3xl font-bold gradient-text mt-1"><?= e($stats['categories']) ?></p>
                                            </div>
                                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                            </div>
                                        </div>
                                    </article>

                                    <article class="stat-card bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-neutral-200 transition-all">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-neutral-500">Headline Artikel</p>
                                                <p class="text-3xl font-bold gradient-text mt-1"><?= e($stats['headline_articles']) ?></p>
                                            </div>
                                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                                            </div>
                                        </div>
                                    </article>

                                    <article class="stat-card bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-neutral-200 transition-all">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-neutral-500">Headline Video</p>
                                                <p class="text-3xl font-bold gradient-text mt-1"><?= e($stats['headline_videos']) ?></p>
                                            </div>
                                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-500 to-red-500 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                        </div>
                                    </article>
                                </div>

                                <!-- Quick Actions -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                    <a href="articles.php?action=create" class="block bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-neutral-200 hover:border-fuchsia-300 transition-all group">
                                        <div class="flex items-start gap-4">
                                            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-500 to-fuchsia-500 flex items-center justify-center group-hover:scale-105 transition-transform">
                                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-neutral-900">Tambah Artikel Baru</h3>
                                                <p class="text-sm text-neutral-500 mt-1">Tulis dan publikasikan jurnal/artikel musik</p>
                                            </div>
                                        </div>
                                    </a>

                                    <a href="videos.php?action=create" class="block bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-neutral-200 hover:border-fuchsia-300 transition-all group">
                                        <div class="flex items-start gap-4">
                                            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-fuchsia-500 to-pink-500 flex items-center justify-center group-hover:scale-105 transition-transform">
                                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-neutral-900">Tambah Video Baru</h3>
                                                <p class="text-sm text-neutral-500 mt-1">Upload video YouTube dengan thumbnail</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                <!-- Recent Content -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Articles -->
                    <section class="bg-white/80 backdrop-blur-xl rounded-2xl border border-neutral-200 overflow-hidden">
                        <div class="p-6 border-b border-neutral-200 flex items-center justify-between">
                            <h2 class="text-lg font-bold gradient-text flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                Artikel Terbaru
                            </h2>
                            <a href="articles.php" class="text-sm text-fuchsia-600 hover:underline font-medium">Lihat semua</a>
                        </div>
                        <div class="divide-y divide-neutral-200">
                            <?php if ($recentArticles): ?>
                                <?php foreach ($recentArticles as $art): ?>
                                    <a href="articles.php?action=edit&id=<?= e($art['id']) ?>" class="block p-4 hover:bg-neutral-50 transition-colors">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 text-xs mb-1">
                                                    <span class="px-2 py-0.5 rounded-full text-white text-[10px] font-bold uppercase <?= $catColor($art['category']) ?? 'bg-indigo-500' ?>">
                                                        <?= e($art['category']) ?>
                                                    </span>
                                                    <?php if ($art['is_headline']): ?>
                                                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold">HEADLINE</span>
                                                    <?php endif; ?>
                                                </div>
                                                <h4 class="font-semibold text-neutral-900 truncate group-hover:text-fuchsia-600 transition-colors"><?= e($art['title']) ?></h4>
                                                <p class="text-xs text-neutral-500 mt-0.5"><?= formatDate($art['published_at']) ?></p>
                                            </div>
                                            <svg class="w-5 h-5 text-neutral-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="p-8 text-center text-neutral-500">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                    <p>Belum ada artikel</p>
                                    <a href="articles.php?action=create" class="text-fuchsia-600 hover:underline mt-2 inline-block">Buat yang pertama</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>

                    <!-- Recent Videos -->
                    <section class="bg-white/80 backdrop-blur-xl rounded-2xl border border-neutral-200 overflow-hidden">
                        <div class="p-6 border-b border-neutral-200 flex items-center justify-between">
                            <h2 class="text-lg font-bold gradient-text flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Video Terbaru
                            </h2>
                            <a href="videos.php" class="text-sm text-fuchsia-600 hover:underline font-medium">Lihat semua</a>
                        </div>
                        <div class="divide-y divide-neutral-200">
                            <?php if ($recentVideos): ?>
                                <?php foreach ($recentVideos as $vid): ?>
                                    <a href="videos.php?action=edit&id=<?= e($vid['id']) ?>" class="block p-4 hover:bg-neutral-50 transition-colors">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 text-xs mb-1">
                                                    <span class="px-2 py-0.5 rounded-full text-white text-[10px] font-bold uppercase <?= $catColor($vid['category']) ?? 'bg-indigo-500' ?>">
                                                        <?= e($vid['category']) ?>
                                                    </span>
                                                    <?php if ($vid['is_headline']): ?>
                                                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold">HEADLINE</span>
                                                    <?php endif; ?>
                                                </div>
                                                <h4 class="font-semibold text-neutral-900 truncate group-hover:text-fuchsia-600 transition-colors"><?= e($vid['title']) ?></h4>
                                                <p class="text-xs text-neutral-500 mt-0.5"><?= formatDate($vid['published_at']) ?></p>
                                            </div>
                                            <svg class="w-5 h-5 text-neutral-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="p-8 text-center text-neutral-500">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p>Belum ada video</p>
                                    <a href="videos.php?action=create" class="text-fuchsia-600 hover:underline mt-2 inline-block">Buat yang pertama</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>
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
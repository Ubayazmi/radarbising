<?php
/**
 * Writer Video List Template
 * Variables: $videos, $categories, $page, $maxPage, $search, $catFilter, $total, $perPage, $pageTitle, $csrfToken, $catColor(), $author, $userId, $role
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — Radar Bising Writer Portal</title>
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
        .table-row:hover { background: linear-gradient(90deg, rgba(168,85,247,0.03), rgba(236,72,153,0.03)); }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-fuchsia-50 dark:from-[#0e0e1c] dark:via-[#0b0b14] dark:to-[#180d20] min-h-screen font-sans">
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
                    <a href="dashboard.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 dark:text-neutral-300 font-medium hover:text-fuchsia-600 dark:hover:text-fuchsia-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                    <a href="articles.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 dark:text-neutral-300 font-medium hover:text-fuchsia-600 dark:hover:text-fuchsia-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        Artikel Saya
                    </a>
                    <a href="videos.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 dark:text-neutral-300 font-medium bg-gradient-to-r from-indigo-50 to-fuchsia-50 dark:from-indigo-900/20 dark:to-fuchsia-900/20 border border-indigo-200 dark:border-indigo-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Video Saya
                    </a>
                    <a href="profile.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 dark:text-neutral-300 font-medium hover:text-fuchsia-600 dark:hover:text-fuchsia-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Profil Saya
                    </a>
                </nav>
                <div class="p-4 border-t border-neutral-200 dark:border-neutral-800">
                    <form method="POST" action="logout.php" class="w-full">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 dark:text-neutral-300 font-medium hover:bg-neutral-100 dark:hover:bg-neutral-800 hover:text-fuchsia-600 dark:hover:text-fuchsia-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Keluar
                        </button>
                    </form>
                    <a href="../../index.php" target="_blank" class="block mt-3 text-center text-sm text-fuchsia-600 dark:text-fuchsia-400 hover:underline">Lihat Situs Publik</a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 min-h-screen">
            <header class="sticky top-0 z-30 bg-white/80 dark:bg-white/5 backdrop-blur-xl border-b border-neutral-200 dark:border-neutral-800">
                <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                    <h1 class="text-2xl font-bold gradient-text"><?= e($pageTitle) ?></h1>
                    <div class="flex items-center gap-4">
                        <div class="hidden md:flex flex-col items-end text-sm text-neutral-500">
                            <span><?= e(explode(' ', $author)[0] ?? 'Penulis') ?></span>
                        </div>
                        <a href="videos.php?action=create" class="gradient-bg text-white font-semibold px-5 py-2.5 rounded-xl hover:opacity-90 transition-opacity flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Video
                        </a>
                    </div>
                </div>
            </header>

            <div class="max-w-7xl mx-auto px-6 py-8">
                <?php if (hasFlash('success')): ?>
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 flex items-center gap-3 animate-slide-down">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <?= e(getFlash('success')) ?>
                    </div>
                <?php endif; ?>
                <?php if (hasFlash('error')): ?>
                    <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 flex items-center gap-3 animate-slide-down">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414-1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <?= e(getFlash('error')) ?>
                    </div>
                <?php endif; ?>

                <!-- Filters -->
                <div class="mb-6 flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <form method="GET" class="flex gap-2">
                            <input type="hidden" name="p" value="1">
                            <div class="flex-1 relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari judul atau deskripsi..."
                                    class="w-full pl-10 pr-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:outline-none focus:border-fuchsia-500 transition-all">
                            </div>
                            <button type="submit" class="px-4 py-3 rounded-xl bg-fuchsia-100 dark:bg-fuchsia-900/30 text-fuchsia-700 dark:text-fuchsia-300 hover:bg-fuchsia-200 dark:hover:bg-fuchsia-900/50 transition-colors">Cari</button>
                        </form>
                    </div>
                    <div class="flex items-center gap-2">
                        <select name="cat" onchange="this.form.submit()" class="px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:outline-none focus:border-fuchsia-500">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= e($cat) ?>" <?= $catFilter === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Videos Table -->
                <div class="bg-white/80 dark:bg-white/5 backdrop-blur-xl rounded-2xl border border-neutral-200 dark:border-neutral-800 overflow-hidden">
                    <?php if ($videos): ?>
                        <table class="w-full text-sm table-fixed">
                            <thead class="bg-neutral-50 dark:bg-neutral-900/50">
                                <tr>
                                    <th class="px-4 py-3 w-16 text-left text-xs font-bold tracking-widest uppercase text-neutral-500 dark:text-neutral-400">Thumbnail</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold tracking-widest uppercase text-neutral-500 dark:text-neutral-400">Judul</th>
                                    <th class="px-4 py-3 hidden sm:table-cell text-left text-xs font-bold tracking-widest uppercase text-neutral-500 dark:text-neutral-400">Kategori</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold tracking-widest uppercase text-neutral-500 dark:text-neutral-400">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold tracking-widest uppercase text-neutral-500 dark:text-neutral-400 w-48">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                                <?php foreach ($videos as $vid): ?>
                                    <tr class="table-row">
                                        <td class="px-4 py-3">
                                            <?php if ($vid['thumbnail']): ?>
                                                <img src="../<?= e($vid['thumbnail']) ?>" alt="" class="w-16 h-9 rounded-lg object-cover">
                                            <?php else: ?>
                                                <div class="w-16 h-9 rounded-lg bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-neutral-900 dark:text-neutral-100 truncate max-w-xs sm:max-w-md"><?= e($vid['title']) ?></div>
                                            <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">Slug: <?= e($vid['slug']) ?></div>
                                        </td>
                                        <td class="px-4 py-3 hidden sm:table-cell">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold text-white <?= $catColor($vid['category']) ?>">
                                                <?= e($vid['category']) ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400 whitespace-nowrap"><?= formatDate($vid['published_at']) ?></td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <a href="videos.php?action=edit&id=<?= e($vid['id']) ?>" class="px-3 py-1.5 text-sm font-medium text-fuchsia-600 dark:text-fuchsia-400 hover:bg-fuchsia-50 dark:hover:bg-fuchsia-900/20 rounded-lg transition-colors whitespace-nowrap">Edit</a>
                                                <form method="POST" action="videos.php?action=delete&id=<?= e($vid['id']) ?>" onsubmit="return confirm('Hapus video ini? Tindakan ini tidak bisa dibatalkan.')">
                                                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                                    <button type="submit" class="px-3 py-1.5 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors whitespace-nowrap">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <?php if ($maxPage > 1): ?>
                            <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-800">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Menampilkan <?= e(count($videos)) ?> dari <?= e($total) ?> video</p>
                                    <nav class="flex items-center gap-1" aria-label="Pagination">
                                        <?php if ($page > 1): ?>
                                            <a href="?p=1<?= $catFilter ? '&cat='.urlencode($catFilter) : '' ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="px-3 py-1.5 text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg">« First</a>
                                            <a href="?p=<?= $page-1 ?><?= $catFilter ? '&cat='.urlencode($catFilter) : '' ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="px-3 py-1.5 text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg">‹ Prev</a>
                                        <?php endif; ?>
                                        <?php
                                        $start = max(1, $page - 2);
                                        $end = min($maxPage, $page + 2);
                                        for ($i = $start; $i <= $end; $i++):
                                        ?>
                                            <a href="?p=<?= $i ?><?= $catFilter ? '&cat='.urlencode($catFilter) : '' ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="px-3 py-1.5 text-sm font-medium <?= $i === $page ? 'bg-fuchsia-600 text-white' : 'text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800' ?> rounded-lg">
                                                <?= $i ?>
                                            </a>
                                        <?php endfor; ?>
                                        <?php if ($page < $maxPage): ?>
                                            <a href="?p=<?= $page+1 ?><?= $catFilter ? '&cat='.urlencode($catFilter) : '' ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="px-3 py-1.5 text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg">Next ›</a>
                                            <a href="?p=<?= $maxPage ?><?= $catFilter ? '&cat='.urlencode($catFilter) : '' ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="px-3 py-1.5 text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg">Last »</a>
                                        <?php endif; ?>
                                    </nav>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="p-12 text-center text-neutral-500 dark:text-neutral-400">
                            <svg class="w-16 h-16 mx-auto mb-4 text-neutral-300 dark:text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100 mb-1">Belum ada video</h3>
                            <p class="text-sm mb-4">Mulai mengelola video pertama Anda</p>
                            <a href="videos.php?action=create" class="inline-flex items-center gap-2 gradient-bg text-white font-semibold px-6 py-3 rounded-xl hover:opacity-90 transition-opacity">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Tambah Video Baru
                            </a>
                        </div>
                    <?php endif; ?>
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
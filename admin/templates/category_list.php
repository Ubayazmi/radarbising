<?php
/**
 * Category List Template
 * Variables: $defaultCategories, $catCounts, $pageTitle, $csrfToken
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — Radar Bising Admin</title>
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
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-fuchsia-50 min-h-screen font-sans">
    <div class="flex">
        <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-white/80 backdrop-blur-xl border-r border-neutral-200 transform transition-transform duration-300 lg:translate-x-0">
                    <div class="flex flex-col h-full">
                      <div class="p-6 border-b border-neutral-200">
                        <a href="dashboard.php" class="flex items-center gap-3">
                          <img src="/assets/logo.png" alt="Radar Bising" class="h-10 w-auto" loading="lazy">
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
                    </a>
                    <a href="videos.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium hover:text-fuchsia-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Video
                    </a>
                    <a href="categories.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium bg-gradient-to-r from-indigo-50 to-fuchsia-50 border border-indigo-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        Kategori
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
                </div>
            </div>
        </aside>

        <main class="flex-1 lg:ml-64 min-h-screen">
            <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-neutral-200">
                <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
                    <h1 class="text-2xl font-bold gradient-text"><?= e($pageTitle) ?></h1>
                </div>
            </header>

            <div class="max-w-4xl mx-auto px-6 py-8">
                <div class="bg-white/80 backdrop-blur-xl rounded-2xl border border-neutral-200 p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold gradient-text">Daftar Kategori</h2>
                        <p class="text-sm text-neutral-500">Kategori terdefinisi di kode (helpers.php & templates). Untuk menambah/edit, sunting file terkait.</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($defaultCategories as $cat => $info): ?>
                            <?php $count = $catCounts[$cat] ?? 0; ?>
                            <article class="flex items-center gap-4 p-4 rounded-xl border border-neutral-200 bg-white/50 hover:border-fuchsia-300 transition-colors">
                                <div class="w-12 h-12 rounded-xl <?= $info['color'] ?> flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-bold text-sm uppercase"><?= e(mb_substr($cat, 0, 1)) ?></span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-neutral-900"><?= e($cat) ?></h3>
                                    <p class="text-xs text-neutral-500"><?= e($info['label']) ?></p>
                                    <div class="flex items-center gap-2 mt-1 text-xs">
                                        <span class="px-2 py-0.5 rounded-full bg-neutral-100 text-neutral-600"><?= $count ?> konten</span>
                                        <span class="px-2 py-0.5 rounded-full <?= $info['color'] ?>/20 text-white text-[10px] font-bold">Badge: <?= e($info['color']) ?></span>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="bg-white/80 backdrop-blur-xl rounded-2xl border border-neutral-200 p-6">
                    <h2 class="text-lg font-bold gradient-text mb-4">Cara Menambah/Mengubah Kategori</h2>
                    <p class="text-neutral-600 mb-4">Saat ini kategori dikelola via kode (bukan database) untuk kesederhanaan. Untuk menambah atau mengubah:</p>
                    <div class="space-y-4 text-sm">
                        <div class="p-4 rounded-xl bg-neutral-50 border border-neutral-200">
                            <p class="font-medium text-neutral-900 mb-2">1. Edit file <code class="px-1.5 py-0.5 bg-neutral-100 rounded">admin/includes/helpers.php</code> — fungsi <code>$catColor</code></p>
                            <pre class="text-xs bg-neutral-100 p-3 rounded overflow-x-auto"><code>'Kabar' => 'bg-sky-500',
'Ulasan' => 'bg-emerald-500',
'Wawancara' => 'bg-fuchsia-500',
'Agenda' => 'bg-amber-500',</code></pre>
                        </div>
                        <div class="p-4 rounded-xl bg-neutral-50 border border-neutral-200">
                            <p class="font-medium text-neutral-900 mb-2">2. Edit template artikel/video (list & form) — array <code>$categories</code></p>
                            <pre class="text-xs bg-neutral-100 p-3 rounded overflow-x-auto"><code>$categories = [
    'Kabar' => 'bg-sky-500',
    'Ulasan' => 'bg-emerald-500',
    'Wawancara' => 'bg-fuchsia-500',
    'Agenda' => 'bg-amber-500',
];</code></pre>
                        </div>
                        <div class="p-4 rounded-xl bg-neutral-50 border border-neutral-200">
                            <p class="font-medium text-neutral-900 mb-2">3. Edit halaman publik <code>index.php</code> — fungsi <code>$catColor</code> di awal file</p>
                        </div>
                    </div>
                    <p class="text-xs text-neutral-500 mt-4">Rencana masa depan: pindahkan kategori ke tabel database + UI CRUD penuh di halaman ini.</p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
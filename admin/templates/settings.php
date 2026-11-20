<?php
/** Admin Settings Template */
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
        .input-focus:focus { box-shadow: 0 0 0 3px rgba(168,85,247,0.2); }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-fuchsia-50 dark:from-[#0e0e1c] dark:via-[#0b0b14] dark:to-[#180d20] min-h-screen font-sans">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-white/80 backdrop-blur-xl border-r border-neutral-200 transform transition-transform duration-300 lg:translate-x-0">
                    <div class="flex flex-col h-full">
                      <div class="p-6 border-b border-neutral-200">
                        <a href="dashboard.php" class="flex items-center gap-3">
                          <img src="/assets/logo.png" alt="Radar Bising" class="h-10 w-auto" loading="lazy">
                        </a>
                      </div>
                      <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                    <a href="dashboard.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 dark:text-neutral-300 font-medium hover:text-fuchsia-600 dark:hover:text-fuchsia-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                    <a href="articles.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 dark:text-neutral-300 font-medium hover:text-fuchsia-600 dark:hover:text-fuchsia-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        Artikel
                    </a>
                    <a href="videos.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 dark:text-neutral-300 font-medium hover:text-fuchsia-600 dark:hover:text-fuchsia-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Video
                    </a>
                    <a href="categories.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 dark:text-neutral-300 font-medium hover:text-fuchsia-600 dark:hover:text-fuchsia-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        Kategori
                    </a>
                    <a href="users.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 dark:text-neutral-300 font-medium hover:text-fuchsia-600 dark:hover:text-fuchsia-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        User
                    </a>
                    <a href="settings.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 dark:text-neutral-300 font-medium bg-gradient-to-r from-indigo-50 to-fuchsia-50 dark:from-indigo-900/20 dark:to-fuchsia-900/20 border border-indigo-200 dark:border-indigo-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Pengaturan
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
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 min-h-screen">
            <header class="sticky top-0 z-30 bg-white/80 dark:bg-white/5 backdrop-blur-xl border-b border-neutral-200 dark:border-neutral-800">
                <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
                    <h1 class="text-2xl font-bold gradient-text"><?= e($pageTitle) ?></h1>
                    <a href="dashboard.php" class="px-4 py-2 rounded-xl border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 hover:border-fuchsia-300 transition-colors">← Kembali</a>
                </div>
            </header>

            <div class="max-w-4xl mx-auto px-6 py-8">
                <?php if ($error): ?>
                    <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 flex items-center gap-3 animate-slide-down">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 flex items-center gap-3 animate-slide-down">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 001.414-1.414L9 10.586 7.707 9.293a1 1 0 001.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <?= e($success) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="bg-white/80 dark:bg-white/5 backdrop-blur-xl rounded-2xl border border-neutral-200 dark:border-neutral-800 p-6 space-y-8" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                    <!-- Site Identity -->
                    <div>
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-fuchsia-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Identitas Situs
                        </h2>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-4">Nama & deskripsi yang muncul di seluruh website.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="site_name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Nama Situs <span class="text-red-500">*</span></label>
                                <input type="text" id="site_name" name="site_name" required
                                    value="<?= e($settings['site_name'] ?? '') ?>"
                                    class="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 input-focus focus:outline-none focus:border-fuchsia-500 transition-all"
                                    placeholder="Radar Bising">
                            </div>
                            <div>
                                <label for="site_description" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Deskripsi Singkat</label>
                                <input type="text" id="site_description" name="site_description"
                                    value="<?= e($settings['site_description'] ?? '') ?>"
                                    class="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 input-focus focus:outline-none focus:border-fuchsia-500 transition-all"
                                    placeholder="Platform Artikel & Video Musik Indonesia">
                            </div>
                        </div>
                    </div>

                    <!-- Logo & Favicon -->
                    <div>
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-fuchsia-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Logo & Favicon
                        </h2>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-4">Logo untuk header, favicon untuk tab browser. Format: JPG/PNG/WebP (logo), ICO/PNG (favicon). Maks 5MB.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="site_logo" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Logo Situs</label>
                                <input type="file" id="site_logo" name="site_logo" accept="image/jpeg,image/png,image/webp"
                                    class="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 input-focus focus:outline-none focus:border-fuchsia-500 transition-all">
                                <?php if ($settings['site_logo']): ?>
                                    <div class="mt-2">
                                        <img src="../<?= e($settings['site_logo']) ?>" alt="Current Logo" class="h-12 rounded-lg border border-neutral-200 dark:border-neutral-800">
                                        <p class="text-xs text-neutral-500 mt-1">Logo saat ini</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <label for="site_favicon" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Favicon</label>
                                <input type="file" id="site_favicon" name="site_favicon" accept="image/x-icon,image/png"
                                    class="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 input-focus focus:outline-none focus:border-fuchsia-500 transition-all">
                                <?php if ($settings['site_favicon']): ?>
                                    <div class="mt-2">
                                        <img src="../<?= e($settings['site_favicon']) ?>" alt="Current Favicon" class="h-8 rounded border border-neutral-200 dark:border-neutral-800">
                                        <p class="text-xs text-neutral-500 mt-1">Favicon saat ini</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Defaults -->
                    <div>
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-fuchsia-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            SEO Defaults
                        </h2>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-4">Template meta untuk halaman yang tidak punya SEO khusus.</p>
                        <div>
                            <label for="meta_title_template" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Template Meta Title</label>
                            <input type="text" id="meta_title_template" name="meta_title_template"
                                value="<?= e($settings['meta_title_template'] ?? '{title} | Radar Bising') ?>"
                                class="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 input-focus focus:outline-none focus:border-fuchsia-500 transition-all"
                                placeholder="{title} | Radar Bising">
                            <p class="text-xs text-neutral-500 mt-1">Gunakan <code class="px-1 py-0.5 bg-neutral-100 dark:bg-neutral-800 rounded">{title}</code> untuk judul halaman otomatis.</p>
                        </div>
                        <div class="mt-4">
                            <label for="meta_description_default" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Meta Description Default</label>
                            <textarea id="meta_description_default" name="meta_description_default" rows="3"
                                class="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 input-focus focus:outline-none focus:border-fuchsia-500 transition-all"
                                placeholder="Baca artikel, wawancara, dan tonton video musik terbaru di Radar Bising."><?= e($settings['meta_description_default'] ?? '') ?></textarea>
                        </div>
                        <div class="mt-4">
                            <label for="og_image_default" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">OG Image Default (URL)</label>
                            <input type="url" id="og_image_default" name="og_image_default"
                                value="<?= e($settings['og_image_default'] ?? '') ?>"
                                class="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 input-focus focus:outline-none focus:border-fuchsia-500 transition-all"
                                placeholder="https://example.com/default-og.jpg">
                            <p class="text-xs text-neutral-500 mt-1">Gambar fallback untuk share Facebook/Twitter/WA.</p>
                        </div>
                    </div>

                    <!-- Analytics -->
                    <div>
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-fuchsia-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            Analytics & Tracking
                        </h2>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-4">Google Analytics / GA4 Measurement ID.</p>
                        <div>
                            <label for="google_analytics_id" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Google Analytics ID</label>
                            <input type="text" id="google_analytics_id" name="google_analytics_id"
                                value="<?= e($settings['google_analytics_id'] ?? '') ?>"
                                class="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 input-focus focus:outline-none focus:border-fuchsia-500 transition-all"
                                placeholder="G-XXXXXXXXXX atau UA-XXXXXXXX-X">
                            <p class="text-xs text-neutral-500 mt-1">Masukkan Measurement ID (G-...) atau Tracking ID (UA-...).</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-neutral-200 dark:border-neutral-800">
                        <a href="dashboard.php" class="px-5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">Batal</a>
                        <button type="submit" class="gradient-bg text-white font-semibold px-6 py-2.5 rounded-xl hover:opacity-90 transition-opacity flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <style>
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-slide-down { animation: slideDown 0.3s ease-out; }
    </style>
</body>
</html>
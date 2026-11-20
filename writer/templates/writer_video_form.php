<?php
/**
 * Writer Video Form Template (Create/Edit)
 * Variables: $video, $categories, $pageTitle, $action, $error, $csrfToken, $userId, $role
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — Radar Bising Writer Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/tinymce.min.js" referrerpolicy="origin"></script>
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
                    <a href="videos.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 dark:text-neutral-300 font-medium bg-gradient-to-r from-indigo-50 to-fuchsia-50 dark:from-indigo-900/20 dark:to-fuchsia-900/20 border border-indigo-200 dark:border-indigo-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Video
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
                <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
                    <h1 class="text-2xl font-bold gradient-text"><?= e($pageTitle) ?></h1>
                    <div class="flex items-center gap-4">
                        <div class="hidden md:flex flex-col items-end text-sm text-neutral-500">
                            <span>ID: <?= e($userId ?? '000') ?> • <?= e($role ?? 'Writer') ?></span>
                        </div>
                        <a href="videos.php" class="px-4 py-2 rounded-xl border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 hover:border-fuchsia-300 transition-colors">← Kembali</a>
                    </div>
                </div>
            </header>

            <div class="max-w-4xl mx-auto px-6 py-8">
                <?php if ($error): ?>
                    <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 flex items-center gap-3 animate-slide-down">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="bg-white/80 dark:bg-white/5 backdrop-blur-xl rounded-2xl border border-neutral-200 dark:border-neutral-800 p-6 space-y-6" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                    
                    <div>
                        <label for="title" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Judul <span class="text-red-500">*</span></label>
                        <input type="text" id="title" name="title" required
                            value="<?= e($video['title']) ?>"
                            class="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 input-focus focus:outline-none focus:border-fuchsia-500 transition-all">
                    </div>

                    <div>
                        <label for="embed_url" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">URL YouTube Embed <span class="text-red-500">*</span></label>
                        <input type="url" id="embed_url" name="embed_url" required
                            value="<?= e($video['embed_url']) ?>"
                            class="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 input-focus focus:outline-none focus:border-fuchsia-500 transition-all"
                            placeholder="https://www.youtube.com/embed/VIDEO_ID">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="category" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Kategori</label>
                            <select id="category" name="category" class="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 input-focus focus:outline-none focus:border-fuchsia-500">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= e($cat) ?>" <?= $video['category'] === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="published_at" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Tanggal Publikasi</label>
                            <input type="date" id="published_at" name="published_at"
                                value="<?= e($video['published_at']) ?>"
                                class="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 input-focus focus:outline-none focus:border-fuchsia-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Thumbnail</label>
                        <div class="flex items-center gap-4">
                            <div class="relative w-32 h-20 flex-shrink-0 rounded-xl overflow-hidden bg-neutral-100 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700">
                                <?php if ($video['thumbnail']): ?>
                                    <img src="../<?= e($video['thumbnail']) ?>" alt="Current" class="w-full h-full object-cover">
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp"
                                    class="w-full px-4 py-2 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="excerpt" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Ringkasan (Excerpt) <span class="text-red-500">*</span></label>
                        <textarea id="excerpt" name="excerpt" required rows="3"
                            class="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 input-focus focus:outline-none focus:border-fuchsia-500 resize-none"><?= e($video['excerpt']) ?></textarea>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Deskripsi Lengkap <span class="text-red-500">*</span></label>
                        <textarea id="description" name="description" required rows="6"
                            class="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 input-focus focus:outline-none focus:border-fuchsia-500"><?= e($video['description']) ?></textarea>
                    </div>

                    <div class="flex items-center justify-between gap-4 pt-4 border-t border-neutral-200 dark:border-neutral-800">
                        <div class="flex items-center">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_draft" value="1" <?= $video['is_draft'] ? 'checked' : '' ?>
                                    class="w-4 h-4 rounded border-neutral-300 dark:border-neutral-600 text-fuchsia-600 dark:text-fuchsia-500 focus:ring-fuchsia-500">
                                <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Simpan sebagai Draft (tidak tampil di website)</span>
                            </label>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="videos.php" class="px-5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">Batal</a>
                            <button type="submit" class="gradient-bg text-white font-semibold px-6 py-2.5 rounded-xl hover:opacity-90 transition-opacity flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <?= $action === 'create' ? 'Publikasikan' : 'Simpan & Publikasikan' ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
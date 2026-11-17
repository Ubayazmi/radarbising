<?php
/**
 * Video List Template
 * Variables: $videos, $categories, $page, $maxPage, $search, $catFilter, $total, $perPage, $pageTitle, $csrfToken
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
    .table-row:hover { background: linear-gradient(90deg, rgba(168,85,247,0.03), rgba(236,72,153,0.03)); }
  </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-fuchsia-50 min-h-screen font-sans">
  <div class="flex">
    <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-white/80 backdrop-blur-xl border-r border-neutral-200 transform transition-transform duration-300 lg:translate-x-0">
          <div class="flex flex-col h-full">
            <div class="p-6 border-b border-neutral-200">
              <a href="dashboard.php" class="flex items-center gap-3">
                <img src="/radarbising/assets/logo.png" alt="Radar Bising" class="h-10 w-auto" loading="lazy">
              </a>
            </div>
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
          <a href="dashboard.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium hover:text-fuchsia-600 ">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            Dashboard
          </a>
          <a href="articles.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium hover:text-fuchsia-600 ">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
            Artikel
          </a>
          <a href="videos.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium bg-gradient-to-r from-indigo-50 to-fuchsia-50  border border-indigo-200 ">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Video
          </a>
          <a href="categories.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium hover:text-fuchsia-600 ">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            Kategori
          </a>
        </nav>
        <div class="p-4 border-t border-neutral-200 ">
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
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-neutral-200 ">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
          <h1 class="text-2xl font-bold gradient-text"><?= e($pageTitle) ?></h1>
          <a href="videos.php?action=create" class="gradient-bg text-white font-semibold px-4 py-2 rounded-xl hover:opacity-90 transition-opacity flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Video
          </a>
        </div>
      </header>

      <div class="max-w-7xl mx-auto px-6 py-8">
        <?php if (hasFlash('success')): ?>
          <div class="mb-6 p-4 rounded-xl bg-emerald-50  border border-emerald-200  text-emerald-700  flex items-center gap-3 animate-slide-down">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            <?= e(getFlash('success')) ?>
          </div>
        <?php endif; ?>
        <?php if (hasFlash('error')): ?>
          <div class="mb-6 p-4 rounded-xl bg-red-50  border border-red-200  text-red-700 flex items-center gap-3 animate-slide-down">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414-1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
            <?= e(getFlash('error')) ?>
          </div>
        <?php endif; ?>

        <div class="bg-white/80 backdrop-blur-xl rounded-2xl border border-neutral-200 p-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <form method="GET" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari judul/deskripsi..."
              class="flex-1 px-4 py-2 rounded-xl border border-neutral-200 bg-white text-neutral-900 focus:outline-none focus:border-fuchsia-500 focus:ring-1 focus:ring-fuchsia-500">
            <select name="cat" class="px-4 py-2 rounded-xl border border-neutral-200 bg-white text-neutral-900 focus:outline-none focus:border-fuchsia-500">
              <option value="">Semua Kategori</option>
              <?php foreach ($categories as $cat => $color): ?>
                <option value="<?= e($cat) ?>" <?= $catFilter === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl bg-fuchsia-600 text-white font-medium hover:bg-fuchsia-700 transition-colors">Filter</button>
            <?php if ($search || $catFilter): ?>
              <a href="videos.php" class="px-4 py-2 rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-100 transition-colors">Reset</a>
            <?php endif; ?>
          </form>
          <span class="text-sm text-neutral-500 whitespace-nowrap"><?= e($total) ?> video</span>
        </div>

        <div class="bg-white/80 backdrop-blur-xl rounded-2xl border border-neutral-200 overflow-hidden">
                  <?php if ($videos): ?>
                    <table class="w-full text-sm table-fixed">
                      <thead class="bg-neutral-50/50">
                        <tr class="text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">
                          <th class="px-4 py-3 w-16">Thumbnail</th>
                          <th class="px-4 py-3">Judul</th>
                          <th class="px-4 py-3 hidden sm:table-cell">Kategori</th>
                          <th class="px-4 py-3 whitespace-nowrap">Tanggal</th>
                          <th class="px-4 py-3 text-center whitespace-nowrap">Placement</th>
                          <th class="px-4 py-3 text-center whitespace-nowrap">Status</th>
                          <th class="px-4 py-3 text-right whitespace-nowrap w-48">Action</th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-neutral-200">
                        <?php foreach ($videos as $vid): ?>
                          <tr class="table-row">
                            <td class="px-4 py-3">
                              <?php if ($vid['thumbnail']): ?>
                                <img src="../<?= e($vid['thumbnail']) ?>" alt="" class="w-16 h-10 object-cover rounded-lg">
                              <?php else: ?>
                                <div class="w-16 h-10 rounded-lg bg-neutral-100 flex items-center justify-center">
                                  <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                              <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                              <div class="font-medium text-neutral-900 truncate max-w-xs sm:max-w-md"><?= e($vid['title']) ?></div>
                              <div class="text-xs text-neutral-500 mt-0.5 truncate max-w-xs"><?= e($vid['slug']) ?></div>
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell">
                              <span class="px-2 py-1 rounded-full text-white text-[10px] font-bold uppercase <?= $categories[$vid['category']] ?? 'bg-indigo-500' ?>">
                                <?= e($vid['category']) ?>
                              </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-neutral-600 whitespace-nowrap"><?= formatDate($vid['published_at']) ?></td>
                            <td class="px-4 py-3 text-center">
                              <?php if ($vid['is_headline']): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">
                                  <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                  Headline
                                </span>
                              <?php else: ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-neutral-100 text-neutral-600 text-xs font-medium">Reguler</span>
                              <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                              <?php if ($vid['is_draft']): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-medium">
                                  <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5 5h14M5 5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5z"></path><path d="M9 11l1 6M21 11l-1 6"></path></svg>
                                  Draft
                                </span>
                              <?php else: ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs font-medium">
                                  <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                  Publish
                                </span>
                              <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right">
                              <div class="flex items-center justify-end gap-1.5">
                                <a href="videos.php?action=edit&id=<?= e($vid['id']) ?>" class="px-3 py-1.5 rounded-lg bg-fuchsia-100 text-fuchsia-700 text-xs font-medium hover:bg-fuchsia-200 transition-colors whitespace-nowrap">Edit</a>
                                <form method="POST" action="videos.php?action=delete&id=<?= e($vid['id']) ?>" onsubmit="return confirm('Hapus video ini?');" class="inline">
                                  <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                  <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-100 text-red-700 text-xs font-medium hover:bg-red-200 transition-colors whitespace-nowrap">Hapus</button>
                                </form>
                              </div>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  <?php else: ?>
                    <div class="p-12 text-center">
                      <svg class="w-16 h-16 mx-auto mb-4 text-neutral-300 " fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                      <h3 class="text-lg font-semibold text-neutral-900 mb-2">Belum ada video</h3>
                      <p class="text-neutral-500 mb-4">Mulai tambah video pertama Anda</p>
                      <a href="videos.php?action=create" class="inline-flex items-center gap-2 gradient-bg text-white font-semibold px-5 py-2.5 rounded-xl hover:opacity-90 transition-opacity">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Video
                      </a>
                    </div>
                  <?php endif; ?>
                </div>

        <?php if ($maxPage > 1): ?>
          <div class="mt-6 flex justify-center items-center gap-2">
            <?php
            $pageUrl = fn($p) => 'videos.php?' . http_build_query(array_filter(['p' => $p, 'q' => $search, 'cat' => $catFilter]));
            ?>
            <?php if ($page > 1): ?>
              <a href="<?= $pageUrl($page - 1) ?>" class="px-4 py-2 rounded-xl border border-neutral-200 text-neutral-600 hover:bg-fuchsia-50 hover:border-fuchsia-300 transition-colors">← Sebelumnya</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $maxPage; $i++): ?>
              <?php if ($i === 1 || $i === $maxPage || abs($i - $page) <= 1): ?>
                <a href="<?= $pageUrl($i) ?>" class="w-10 h-10 flex items-center justify-center rounded-xl <?= $i === $page ? 'gradient-bg text-white' : 'border border-neutral-200 text-neutral-600 hover:border-fuchsia-300' ?>"><?= $i ?></a>
              <?php elseif ($i === $page - 2 || $i === $page + 2): ?>
                <span class="w-10 h-10 flex items-center justify-center text-neutral-400">…</span>
              <?php endif; ?>
            <?php endfor; ?>
            <?php if ($page < $maxPage): ?>
              <a href="<?= $pageUrl($page + 1) ?>" class="px-4 py-2 rounded-xl border border-neutral-200 text-neutral-600 hover:bg-fuchsia-50 hover:border-fuchsia-300 transition-colors">Selanjutnya →</a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </main>
  </div>

  <style>
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-slide-down { animation: slideDown 0.3s ease-out; }
  </style>
</body>
</html>
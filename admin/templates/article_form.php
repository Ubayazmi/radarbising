<?php
/**
 * Article Form Template (Create/Edit)
 * Variables: $article, $categories, $pageTitle, $action, $error, $csrfToken
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
    .input-focus:focus { box-shadow: 0 0 0 3px rgba(168,85,247,0.2); }
    /* Google Docs-style editor */
    .docs-editor { border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden; background: white; }
    .docs-editor:focus-within { border-color: #a855f7; box-shadow: 0 0 0 3px rgba(168,85,247,0.15); }
    .docs-toolbar { display: flex; flex-wrap: wrap; gap: 4px; padding: 8px 12px; border-bottom: 1px solid #e5e7eb; background: #fafafa; }
    .docs-toolbar button { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border: none; background: transparent; color: #374151; border-radius: 4px; cursor: pointer; transition: all 0.15s; }
    .docs-toolbar button:hover { background: #e5e7eb; color: #111827; }
    .docs-toolbar button.active { background: #e9d5ff; color: #7c3aed; }
    .docs-toolbar button:disabled { opacity: 0.4; cursor: not-allowed; }
    .docs-toolbar .divider { width: 1px; height: 24px; background: #d1d5db; margin: 0 4px; }
    .docs-toolbar select { padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 4px; background: white; font-size: 13px; color: #374151; }
    .docs-content { min-height: 400px; padding: 24px; outline: none; line-height: 1.7; font-size: 15px; color: #1f2937; }
    .docs-content:empty::before { content: 'Tulis konten artikel di sini...'; color: #9ca3af; pointer-events: none; }
    .docs-content[contenteditable="true"] { white-space: pre-wrap; word-wrap: break-word; }
    .docs-content p { margin: 0 0 1em; }
    .docs-content h2 { font-size: 1.5em; font-weight: 700; margin: 1.5em 0 0.5em; color: #111827; }
    .docs-content h3 { font-size: 1.25em; font-weight: 600; margin: 1.25em 0 0.5em; color: #1f2937; }
    .docs-content blockquote { border-left: 4px solid #a855f7; padding-left: 16px; margin: 1em 0; color: #6b7280; font-style: italic; }
    .docs-content ul, .docs-content ol { margin: 1em 0; padding-left: 1.5em; }
    .docs-content li { margin: 0.25em 0; }
    .docs-content code { background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-family: 'Monaco', 'Menlo', monospace; font-size: 0.9em; }
    .docs-content pre { background: #1f2937; color: #e5e7eb; padding: 16px; border-radius: 8px; overflow-x: auto; margin: 1em 0; }
    .docs-content pre code { background: transparent; padding: 0; font-size: 0.9em; }
    .docs-content a { color: #a855f7; text-decoration: underline; }
    .docs-content strong { font-weight: 700; }
    .docs-content em { font-style: italic; }
    .docs-content u { text-decoration: underline; }
    .docs-content s { text-decoration: line-through; }
    @media (prefers-color-scheme: dark) {
      .docs-editor { border-color: #374151; background: #1f2937; }
      .docs-toolbar { background: #111827; border-color: #374151; }
      .docs-toolbar button { color: #d1d5db; }
      .docs-toolbar button:hover { background: #374151; color: white; }
      .docs-toolbar button.active { background: #7c3aed; color: white; }
      .docs-toolbar .divider { background: #374151; }
      .docs-toolbar select { background: #1f2937; border-color: #374151; color: #d1d5db; }
      .docs-content { color: #f3f4f6; }
      .docs-content h2, .docs-content h3 { color: white; }
      .docs-content blockquote { border-color: #a855f7; color: #9ca3af; }
      .docs-content code { background: #374151; color: #f3f4f6; }
      .docs-content pre { background: #0f172a; }
      .docs-content a { color: #c084fc; }
    }
  </style>
</head>
  </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-fuchsia-50 min-h-screen font-sans">
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
          <a href="dashboard.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium hover:text-fuchsia-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            Dashboard
          </a>
          <a href="articles.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium bg-gradient-to-r from-indigo-50 to-fuchsia-50 border border-indigo-200 ">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
            Artikel
          </a>
          <a href="videos.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium hover:text-fuchsia-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Video
          </a>
          <a href="categories.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium hover:text-fuchsia-600">
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

    <!-- Main Content -->
    <main class="flex-1 lg:ml-64 min-h-screen">
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-neutral-200">
        <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
          <h1 class="text-2xl font-bold gradient-text"><?= e($pageTitle) ?></h1>
          <a href="articles.php" class="px-4 py-2 rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-100 hover:border-fuchsia-300 transition-colors">← Kembali</a>
        </div>
      </header>

      <div class="max-w-4xl mx-auto px-6 py-8">
        <?php if ($error): ?>
          <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3 animate-slide-down">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
            <?= e($error) ?>
          </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="bg-white/80 backdrop-blur-xl rounded-2xl border border-neutral-200 p-6 space-y-6" novalidate>
          <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

          <!-- Title & Slug -->
          <div class="space-y-4">
            <div>
              <label for="title" class="block text-sm font-medium text-neutral-700 mb-1.5">Judul <span class="text-red-500">*</span></label>
              <input type="text" id="title" name="title" required
                value="<?= e($article['title']) ?>"
                class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500 transition-all"
                placeholder="Contoh: Eksperimen Sonic di Bawah Tanah Jakarta">
            </div>
            <div>
              <label for="slug" class="block text-sm font-medium text-neutral-700 mb-1.5">Slug (URL) <span class="text-red-500">*</span></label>
              <div class="flex items-center gap-2">
                <span class="px-3 py-3 bg-neutral-100 rounded-l-xl text-neutral-500 text-sm font-mono">radarbising.test/artikel/</span>
                <input type="text" id="slug" name="slug"
                  value="<?= e($article['slug']) ?>"
                  class="flex-1 px-4 py-3 rounded-r-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500 transition-all"
                  placeholder="otomatis-dari-judul">
              </div>
              <p class="text-xs text-neutral-500 mt-1">Kosongkan untuk auto-generate dari judul. Hanya huruf kecil, angka, dan strip.</p>
            </div>
          </div>

          <!-- Category & Date & Headline -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label for="category" class="block text-sm font-medium text-neutral-700 mb-1.5">Kategori</label>
              <select id="category" name="category" class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500">
                <?php foreach ($categories as $cat => $color): ?>
                  <option value="<?= e($cat) ?>" <?= $article['category'] === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label for="published_at" class="block text-sm font-medium text-neutral-700 mb-1.5">Tanggal Publikasi</label>
              <input type="date" id="published_at" name="published_at"
                value="<?= e($article['published_at']) ?>"
                class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500">
            </div>
            <div class="flex items-end">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_headline" value="1" <?= $article['is_headline'] ? 'checked' : '' ?>
                  class="w-4 h-4 rounded border-neutral-300 text-fuchsia-600 focus:ring-fuchsia-500">
                <span class="text-sm font-medium text-neutral-700">Jadikan Headline (Hero)</span>
              </label>
            </div>
            <div class="flex items-end">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_draft" value="1" <?= $article['is_draft'] ? 'checked' : '' ?>
                  class="w-4 h-4 rounded border-neutral-300 text-fuchsia-600 focus:ring-fuchsia-500">
                <span class="text-sm font-medium text-neutral-700">Simpan sebagai Draft (tidak tampil di website)</span>
              </label>
            </div>
          </div>

          <!-- Author -->
          <div>
            <label for="author" class="block text-sm font-medium text-neutral-700 mb-1.5">Penulis</label>
            <input type="text" id="author" name="author"
              value="<?= e($article['author']) ?>"
              class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500"
              placeholder="Nama penulis">
          </div>

          <!-- Image Upload -->
          <div>
            <label class="block text-sm font-medium text-neutral-700 mb-1.5">Gambar Cover / Thumbnail</label>
            <div class="flex items-center gap-4">
              <div class="relative w-32 h-20 flex-shrink-0 rounded-xl overflow-hidden bg-neutral-100 border border-neutral-200 ">
                <?php if ($article['image']): ?>
                  <img src="../<?= e($article['image']) ?>" alt="Current" class="w-full h-full object-cover">
                <?php else: ?>
                  <svg class="w-8 h-8 mx-auto my-auto text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <?php endif; ?>
              </div>
              <div class="flex-1">
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                  class="w-full px-4 py-2 rounded-xl border border-neutral-200 bg-white text-neutral-900 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-fuchsia-50 file:text-fuchsia-700 hover:file:bg-fuchsia-100 transition-colors">
                <p class="text-xs text-neutral-500 mt-1">JPG, PNG, WebP — maksimal 5MB. Aspect ratio 16:9 direkomendasikan.</p>
              </div>
            </div>
          </div>

          <!-- Excerpt -->
          <div>
            <label for="excerpt" class="block text-sm font-medium text-neutral-700 mb-1.5">Ringkasan (Excerpt) <span class="text-red-500">*</span></label>
            <textarea id="excerpt" name="excerpt" required rows="3"
              class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500 resize-none"
              placeholder="Ringkasan singkat 2-3 kalimat untuk preview di homepage dan kartu artikel..."><?= e($article['excerpt']) ?></textarea>
            <p class="text-xs text-neutral-500 mt-1">Ditampilkan di kartu artikel dan halaman daftar. Max 300 karakter untuk SEO optimal.</p>
          </div>

          <!-- SEO Fields -->
          <fieldset class="border border-neutral-200 rounded-xl p-4 space-y-4">
            <legend class="text-sm font-semibold text-neutral-900 px-2">SEO & Social Share</legend>
            <div>
              <label for="meta_title" class="block text-sm font-medium text-neutral-700 mb-1.5">Meta Title <span class="text-neutral-400">(opsional, default: judul artikel)</span></label>
              <input type="text" id="meta_title" name="meta_title" maxlength="60"
                value="<?= e($article['meta_title'] ?? '') ?>"
                class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500"
                placeholder="Max 60 karakter untuk Google">
              <p class="text-xs text-neutral-500 mt-1">Kosongkan = pakai judul artikel. Ideal 50-60 karakter.</p>
            </div>
            <div>
              <label for="meta_description" class="block text-sm font-medium text-neutral-700 mb-1.5">Meta Description <span class="text-neutral-400">(opsional, default: excerpt)</span></label>
              <textarea id="meta_description" name="meta_description" rows="2" maxlength="160"
                class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500 resize-none"
                placeholder="Max 160 karakter untuk snippet Google..."><?= e($article['meta_description'] ?? '') ?></textarea>
              <p class="text-xs text-neutral-500 mt-1">Kosongkan = pakai excerpt. Ideal 150-160 karakter.</p>
            </div>
            <div>
              <label for="og_image" class="block text-sm font-medium text-neutral-700 mb-1.5">Open Graph Image (Facebook/LinkedIn/WhatsApp) <span class="text-neutral-400">(opsional, default: gambar cover)</span></label>
              <input type="url" id="og_image" name="og_image"
                value="<?= e($article['og_image'] ?? '') ?>"
                class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500"
                placeholder="https://domain.com/path/to/image.jpg (rasio 1.91:1, min 1200x630px)">
              <p class="text-xs text-neutral-500 mt-1">Kosongkan = pakai gambar cover artikel. URL absolut wajib.</p>
            </div>
            <div>
              <label for="canonical_url" class="block text-sm font-medium text-neutral-700 mb-1.5">Canonical URL <span class="text-neutral-400">(opsional, untuk syndikasi/duplicate)</span></label>
              <input type="url" id="canonical_url" name="canonical_url"
                value="<?= e($article['canonical_url'] ?? '') ?>"
                class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500"
                placeholder="https://domain.com/artikel/original-slug">
              <p class="text-xs text-neutral-500 mt-1">Kosongkan = self-canonical (URL artikel ini). Isi jika artikel ini republication.</p>
            </div>
          </fieldset>

          <!-- Content (WYSIWYG - Google Docs Style) -->
                    <div>
                      <label for="content" class="block text-sm font-medium text-neutral-700 mb-1.5">Konten Lengkap <span class="text-red-500">*</span></label>
                      <div class="docs-editor" id="docs-editor">
                        <div class="docs-toolbar" role="toolbar" aria-label="Formatting tools">
                          <button type="button" data-format="bold" title="Bold (Ctrl+B)" aria-label="Bold"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/><path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/></svg></button>
                          <button type="button" data-format="italic" title="Italic (Ctrl+I)" aria-label="Italic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="4" x2="10" y2="4"/><line x1="14" y1="20" x2="5" y2="20"/><line x1="15" y1="4" x2="9" y2="20"/></svg></button>
                          <button type="button" data-format="underline" title="Underline (Ctrl+U)" aria-label="Underline"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 4v6a6 6 0 0 0 12 0V4"/><line x1="4" y1="20" x2="20" y2="20"/></svg></button>
                          <button type="button" data-format="strikeThrough" title="Strikethrough" aria-label="Strikethrough"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="20" y2="12"/><path d="M6 4v6a6 6 0 0 0 12 0V4"/><path d="M6 20h12"/></svg></button>
                          <span class="divider"></span>
                          <select id="format-block" aria-label="Paragraph format">
                            <option value="">Normal text</option>
                            <option value="h2">Heading 2</option>
                            <option value="h3">Heading 3</option>
                            <option value="blockquote">Blockquote</option>
                            <option value="pre">Code block</option>
                          </select>
                          <span class="divider"></span>
                          <button type="button" data-format="insertUnorderedList" title="Bullet list" aria-label="Bullet list"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/><path d="M4 6h1v12H4z"/></svg></button>
                          <button type="button" data-format="insertOrderedList" title="Numbered list" aria-label="Numbered list"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/><text x="3" y="10" font-size="8">1</text><text x="3" y="16" font-size="8">2</text><text x="3" y="22" font-size="8">3</text></svg></button>
                          <span class="divider"></span>
                          <button type="button" data-format="createLink" title="Insert link (Ctrl+K)" aria-label="Insert link"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></button>
                          <button type="button" data-format="unlink" title="Remove link" aria-label="Remove link"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/><path d="M6 11a5 5 0 0 1 7.54.54l3-3a5 5 0 0 1-7.07-7.07l-1.72 1.71"/></svg></button>
                          <span class="divider"></span>
                          <button type="button" data-format="undo" title="Undo (Ctrl+Z)" aria-label="Undo"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"/></svg></button>
                          <button type="button" data-format="redo" title="Redo (Ctrl+Shift+Z)" aria-label="Redo"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 7v6h-6"/><path d="M3 17a9 9 0 0 1 9-9 9 9 0 0 1 6 2.3l3 2.7"/></svg></button>
                        </div>
                        <div class="docs-content" id="docs-content" contenteditable="true" role="textbox" aria-multiline="true" aria-label="Article content"></div>
                      </div>
                      <textarea id="content" name="content" required class="hidden"><?= e($article['content']) ?></textarea>
                      <p class="text-xs text-neutral-500 mt-1">Editor mirip Google Docs: gunakan toolbar di atas untuk formatting. HTML bersih disimpan ke database.</p>
                    </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-4 pt-4 border-t border-neutral-200">
            <a href="articles.php" class="px-5 py-2.5 rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-100 transition-colors">Batal</a>
            <button type="submit" class="gradient-bg text-white font-semibold px-6 py-2.5 rounded-xl hover:opacity-90 transition-opacity flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <?= $action === 'create' ? 'Publikasikan' : 'Simpan Perubahan' ?>
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>

  <script>
      // Auto-generate slug from title
      const titleInput = document.getElementById('title');
      const slugInput = document.getElementById('slug');
      let slugManual = false;
      titleInput.addEventListener('input', () => {
        if (!slugManual) {
          slugInput.value = titleInput.value
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/[\s-]+/g, '-');
        }
      });
      slugInput.addEventListener('input', () => { slugManual = slugInput.value.length > 0; });

      // Google Docs-style Editor
      const toolbar = document.getElementById('docs-editor').querySelector('.docs-toolbar');
      const contentDiv = document.getElementById('docs-content');
      const hiddenTextarea = document.getElementById('content');

      // Sync content from hidden textarea to editor on load
      contentDiv.innerHTML = hiddenTextarea.value;

      // Sync editor content to hidden textarea
      function syncContent() {
        hiddenTextarea.value = contentDiv.innerHTML;
      }

      // Format document
      function formatDoc(cmd, value = null) {
        contentDiv.focus();
        if (value) {
          document.execCommand(cmd, false, value);
        } else {
          document.execCommand(cmd);
        }
        syncContent();
        updateToolbarState();
      }

      // Update toolbar button states
      function updateToolbarState() {
        const buttons = toolbar.querySelectorAll('[data-format]');
        buttons.forEach(btn => {
          const format = btn.dataset.format;
          if (format === 'bold' || format === 'italic' || format === 'underline' || format === 'strikeThrough') {
            btn.classList.toggle('active', document.queryCommandState(format));
          }
        });
        // Update format block select
        const formatBlock = document.getElementById('format-block');
        const currentFormat = document.queryCommandValue('formatBlock');
        if (currentFormat) {
          formatBlock.value = currentFormat.toLowerCase();
        } else {
          formatBlock.value = '';
        }
      }

      // Toolbar button clicks
      toolbar.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-format]');
        if (!btn) return;
        const format = btn.dataset.format;
        if (format === 'createLink') {
          const url = prompt('Masukkan URL:');
          if (url) formatDoc('createLink', url);
        } else {
          formatDoc(format);
        }
      });

      // Format block select
      document.getElementById('format-block').addEventListener('change', (e) => {
        if (e.target.value) {
          formatDoc('formatBlock', e.target.value);
        }
        e.target.value = '';
      });

      // Keyboard shortcuts
      contentDiv.addEventListener('keydown', (e) => {
        if (e.ctrlKey || e.metaKey) {
          switch (e.key.toLowerCase()) {
            case 'b': e.preventDefault(); formatDoc('bold'); break;
            case 'i': e.preventDefault(); formatDoc('italic'); break;
            case 'u': e.preventDefault(); formatDoc('underline'); break;
            case 'k': e.preventDefault(); formatDoc('createLink', prompt('Masukkan URL:')); break;
            case 'z': if (!e.shiftKey) { e.preventDefault(); formatDoc('undo'); } break;
            case 'y': e.preventDefault(); formatDoc('redo'); break;
          }
        }
        // Enter in empty blockquote should exit it
        if (e.key === 'Enter') {
          const sel = window.getSelection();
          if (sel.rangeCount > 0) {
            const range = sel.getRangeAt(0);
            const block = range.startContainer.closest('blockquote');
            if (block && block.textContent.trim() === '') {
              e.preventDefault();
              const p = document.createElement('p');
              p.innerHTML = '<br>';
              block.parentNode.insertBefore(p, block.nextSibling);
              range.selectNodeContents(p);
              range.collapse(true);
            }
          }
        }
      });

      // Google Docs paste cleanup
      contentDiv.addEventListener('paste', (e) => {
        e.preventDefault();
        const text = e.clipboardData.getData('text/html') || e.clipboardData.getData('text/plain');
        const div = document.createElement('div');
        div.innerHTML = text;

        // Remove Google Docs specific styles/classes
        const walker = document.createTreeWalker(div, NodeFilter.SHOW_ELEMENT);
        while (walker.nextNode()) {
          const el = walker.currentNode;
          // Remove all classes
          el.removeAttribute('class');
          // Remove all styles except basic formatting
          const style = el.getAttribute('style');
          if (style) {
            const allowed = ['font-weight', 'font-style', 'text-decoration', 'text-align'];
            const newStyle = style.split(';').filter(s => allowed.some(a => s.includes(a))).join(';');
            if (newStyle) el.setAttribute('style', newStyle);
            else el.removeAttribute('style');
          }
          // Remove Google Docs specific attributes
          ['data-', 'id'].forEach(attr => {
            if (el.hasAttribute(attr) && attr.startsWith('data-')) el.removeAttribute(attr);
          });
          // Convert font tags to span
          if (el.tagName === 'FONT') {
            const span = document.createElement('span');
            span.innerHTML = el.innerHTML;
            el.parentNode.replaceChild(span, el);
          }
        }

        // Insert cleaned content
        document.execCommand('insertHTML', false, div.innerHTML);
        syncContent();
      });

      // Sync on input
      contentDiv.addEventListener('input', syncContent);
      contentDiv.addEventListener('blur', syncContent);

      // Update toolbar state on selection change
      document.addEventListener('selectionchange', () => {
        if (contentDiv.contains(document.activeElement) || document.activeElement === contentDiv) {
          updateToolbarState();
        }
      });
    </script>

  <style>
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-slide-down { animation: slideDown 0.3s ease-out; }
  </style>
</body>
</html>
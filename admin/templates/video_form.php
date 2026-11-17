<?php
/**
 * Video Form Template (Create/Edit)
 * Variables: $video, $categories, $pageTitle, $action, $error, $csrfToken
 */
?>
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — Radar Bising Admin</title>
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
          <a href="dashboard.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium hover:text-fuchsia-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            Dashboard
          </a>
          <a href="articles.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium hover:text-fuchsia-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
            Artikel
          </a>
          <a href="videos.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-neutral-700 font-medium bg-gradient-to-r from-indigo-50 to-fuchsia-50 border border-indigo-200 ">
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

    <main class="flex-1 lg:ml-64 min-h-screen">
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-neutral-200">
        <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
          <h1 class="text-2xl font-bold gradient-text"><?= e($pageTitle) ?></h1>
          <a href="videos.php" class="px-4 py-2 rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-100 hover:border-fuchsia-300 transition-colors">← Kembali</a>
        </div>
      </header>

      <div class="max-w-4xl mx-auto px-6 py-8">
        <?php if ($error): ?>
          <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3 animate-slide-down">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414-1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
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
                value="<?= e($video['title']) ?>"
                class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500 transition-all"
                placeholder="Contoh: Live Session: Eksplorasi Sintesis Analog">
            </div>
            <div>
              <label for="slug" class="block text-sm font-medium text-neutral-700 mb-1.5">Slug (URL) <span class="text-red-500">*</span></label>
              <div class="flex items-center gap-2">
                <span class="px-3 py-3 bg-neutral-100 rounded-l-xl text-neutral-500 text-sm font-mono">radarbising.test/video/</span>
                <input type="text" id="slug" name="slug"
                  value="<?= e($video['slug']) ?>"
                  class="flex-1 px-4 py-3 rounded-r-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500 transition-all"
                  placeholder="otomatis-dari-judul">
              </div>
              <p class="text-xs text-neutral-500 mt-1">Kosongkan untuk auto-generate dari judul.</p>
            </div>
          </div>

          <!-- Category & Date & Headline -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label for="category" class="block text-sm font-medium text-neutral-700 mb-1.5">Kategori</label>
              <select id="category" name="category" class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500">
                <?php foreach ($categories as $cat => $color): ?>
                  <option value="<?= e($cat) ?>" <?= $video['category'] === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label for="published_at" class="block text-sm font-medium text-neutral-700 mb-1.5">Tanggal Publikasi</label>
              <input type="date" id="published_at" name="published_at"
                value="<?= e($video['published_at']) ?>"
                class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500">
            </div>
            <div class="flex items-end">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_headline" value="1" <?= $video['is_headline'] ? 'checked' : '' ?>
                  class="w-4 h-4 rounded border-neutral-300 text-fuchsia-600 focus:ring-fuchsia-500">
                <span class="text-sm font-medium text-neutral-700">Jadikan Headline Video</span>
              </label>
            </div>
            <div class="flex items-end">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_draft" value="1" <?= $video['is_draft'] ? 'checked' : '' ?>
                  class="w-4 h-4 rounded border-neutral-300 text-fuchsia-600 focus:ring-fuchsia-500">
                <span class="text-sm font-medium text-neutral-700">Simpan sebagai Draft (tidak tampil di website)</span>
              </label>
            </div>
          </div>

          <!-- YouTube Embed URL -->
          <div>
            <label for="embed_url" class="block text-sm font-medium text-neutral-700 mb-1.5">URL YouTube <span class="text-red-500">*</span></label>
            <input type="url" id="embed_url" name="embed_url" required
              value="<?= e($video['embed_url']) ?>"
              class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500"
              placeholder="https://www.youtube.com/watch?v=... atau https://youtu.be/...">
            <p class="text-xs text-neutral-500 mt-1">Masukkan URL YouTube biasa (watch?v= atau youtu.be/). Sistem akan konversi ke format embed otomatis.</p>
          </div>

          <!-- Thumbnail Upload -->
          <div>
            <label class="block text-sm font-medium text-neutral-700 mb-1.5">Thumbnail Video</label>
            <div class="flex items-center gap-4">
              <div class="relative w-32 h-18 flex-shrink-0 rounded-xl overflow-hidden bg-neutral-100 border border-neutral-200 ">
                <?php if ($video['thumbnail']): ?>
                  <img src="../<?= e($video['thumbnail']) ?>" alt="Current" class="w-full h-full object-cover">
                <?php else: ?>
                  <svg class="w-8 h-8 mx-auto my-auto text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <?php endif; ?>
              </div>
              <div class="flex-1">
                <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp"
                  class="w-full px-4 py-2 rounded-xl border border-neutral-200 bg-white text-neutral-900 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-fuchsia-50 file:text-fuchsia-700 hover:file:bg-fuchsia-100 transition-colors">
                <p class="text-xs text-neutral-500 mt-1">JPG, PNG, WebP — maks 5MB. Aspect 16:9 (1280x720) ideal untuk YouTube.</p>
              </div>
            </div>
          </div>

          <!-- Excerpt -->
          <div>
            <label for="excerpt" class="block text-sm font-medium text-neutral-700 mb-1.5">Ringkasan (Excerpt) <span class="text-red-500">*</span></label>
            <textarea id="excerpt" name="excerpt" required rows="3"
              class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500 resize-none"
              placeholder="Ringkasan singkat 2-3 kalimat untuk preview di homepage dan kartu video..."><?= e($video['excerpt'] ?? '') ?></textarea>
            <p class="text-xs text-neutral-500 mt-1">Ditampilkan di kartu video dan halaman daftar. Max 300 karakter untuk SEO optimal.</p>
          </div>

          <!-- SEO Fields -->
          <fieldset class="border border-neutral-200 rounded-xl p-4 space-y-4">
            <legend class="text-sm font-semibold text-neutral-900 px-2">SEO & Social Share</legend>
            <div>
              <label for="meta_title" class="block text-sm font-medium text-neutral-700 mb-1.5">Meta Title <span class="text-neutral-400">(opsional, default: judul video)</span></label>
              <input type="text" id="meta_title" name="meta_title" maxlength="60"
                value="<?= e($video['meta_title'] ?? '') ?>"
                class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500"
                placeholder="Max 60 karakter untuk Google">
              <p class="text-xs text-neutral-500 mt-1">Kosongkan = pakai judul video. Ideal 50-60 karakter.</p>
            </div>
            <div>
              <label for="meta_description" class="block text-sm font-medium text-neutral-700 mb-1.5">Meta Description <span class="text-neutral-400">(opsional, default: excerpt)</span></label>
              <textarea id="meta_description" name="meta_description" rows="2" maxlength="160"
                class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500 resize-none"
                placeholder="Max 160 karakter untuk snippet Google..."><?= e($video['meta_description'] ?? '') ?></textarea>
              <p class="text-xs text-neutral-500 mt-1">Kosongkan = pakai excerpt. Ideal 150-160 karakter.</p>
            </div>
            <div>
              <label for="og_image" class="block text-sm font-medium text-neutral-700 mb-1.5">Open Graph Image (Facebook/LinkedIn/WhatsApp) <span class="text-neutral-400">(opsional, default: thumbnail video)</span></label>
              <input type="url" id="og_image" name="og_image"
                value="<?= e($video['og_image'] ?? '') ?>"
                class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500"
                placeholder="https://domain.com/path/to/image.jpg (rasio 1.91:1, min 1200x630px)">
              <p class="text-xs text-neutral-500 mt-1">Kosongkan = pakai thumbnail video. URL absolut wajib.</p>
            </div>
            <div>
              <label for="canonical_url" class="block text-sm font-medium text-neutral-700 mb-1.5">Canonical URL <span class="text-neutral-400">(opsional, untuk syndikasi/duplicate)</span></label>
              <input type="url" id="canonical_url" name="canonical_url"
                value="<?= e($video['canonical_url'] ?? '') ?>"
                class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500"
                placeholder="https://domain.com/video/original-slug">
              <p class="text-xs text-neutral-500 mt-1">Kosongkan = self-canonical (URL video ini). Isi jika video ini republication.</p>
            </div>
          </fieldset>

          <!-- Description (WYSIWYG) -->
          <div>
            <label for="description" class="block text-sm font-medium text-neutral-700 mb-1.5">Deskripsi Lengkap <span class="text-red-500">*</span></label>
            <textarea id="description" name="description" required rows="20"
              class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-900 input-focus focus:outline-none focus:border-fuchsia-500 font-mono text-sm leading-relaxed resize-y min-h-[400px]"
              placeholder="Deskripsi video akan diedit via TinyMCE..."><?= e($video['description']) ?></textarea>
            <p class="text-xs text-neutral-500 mt-1">Gunakan editor TinyMCE di atas untuk formatting (heading, bold, quote, embed, dll). HTML bersih disimpan ke database.</p>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-4 pt-4 border-t border-neutral-200">
            <a href="videos.php" class="px-5 py-2.5 rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-100 transition-colors">Batal</a>
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

    // TinyMCE Init
    tinymce.init({
      selector: '#description',
      license_key: 'gpl',
      skin: 'oxide',
      content_css: 'dark',
      height: 400,
      menubar: true,
      plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons'
      ],
      toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist checklist | link image media table charmap emoticons | removeformat | code fullscreen preview | help',
      block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Blockquote=blockquote; Code=pre',
      font_family_formats: 'Arial=arial,helvetica,sans-serif; Helvetica Neue=helvetica neue,helvetica,arial,sans-serif; Georgia=georgia,serif; Times New Roman=times new roman,times,serif; Courier New=courier new,courier,monospace',
      font_size_formats: '12px 14px 16px 18px 20px 24px 32px 48px',
      images_upload_handler: function (blobInfo, success, failure) {
        const reader = new FileReader();
        reader.onload = () => success(reader.result);
        reader.readAsDataURL(blobInfo.blob());
      },
      media_alt_source: false,
      media_poster: false,
      media_dimensions: false,
      link_default_target: '_blank',
      link_default_protocol: 'https',
      relative_urls: false,
      remove_script_host: false,
      convert_urls: true,
      valid_elements: '*[*]',
      extended_valid_elements: 'iframe[src|width|height|frameborder|allowfullscreen|allow|title|loading]',
      setup: function (editor) {
        editor.on('change', function () {
          editor.save();
        });
      }
    });
  </script>

  <style>
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-slide-down { animation: slideDown 0.3s ease-out; }
  </style>
</body>
</html>
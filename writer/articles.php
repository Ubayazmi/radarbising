<?php
require_once __DIR__ . '/includes/writer_auth.php';
require_once __DIR__ . '/includes/writer_db.php';
require_once __DIR__ . '/includes/writer_helpers.php';

requireAuth();

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);
$message = '';
$error = '';
$csrfToken = generateCsrfToken();

$author = writerAuthor();
$categories = ['Kabar', 'Ulasan', 'Wawancara', 'Agenda'];
$catColor = fn (string $cat) => match ($cat) {
    'Kabar' => 'bg-sky-500',
    'Ulasan' => 'bg-emerald-500',
    'Wawancara' => 'bg-fuchsia-500',
    'Agenda' => 'bg-amber-500',
    default => 'bg-indigo-500',
};

function catColor(string $cat): string {
    $map = [
        'Kabar' => 'bg-sky-500',
        'Ulasan' => 'bg-emerald-500',
        'Wawancara' => 'bg-fuchsia-500',
        'Agenda' => 'bg-amber-500',
    ];
    return $map[$cat] ?? 'bg-indigo-500';
}

// --- CREATE / EDIT ---
if ($action === 'create' || $action === 'edit') {
    $article = [
        'id' => 0,
        'title' => '',
        'slug' => '',
        'category' => 'Kabar',
        'excerpt' => '',
        'content' => '',
        'author' => $author,
        'published_at' => date('Y-m-d'),
        'image' => '',
        'is_headline' => 0,
        'meta_title' => '',
        'meta_description' => '',
        'og_image' => '',
        'canonical_url' => '',
    ];

    if ($action === 'edit' && $id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ? AND author = ?");
        $stmt->execute([$id, $author]);
        $article = $stmt->fetch();
        if (!$article) {
            redirectWith('articles.php', 'error', 'Artikel tidak ditemukan atau Anda tidak memiliki akses.');
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $category = $_POST['category'] ?? 'Kabar';
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $published_at = $_POST['published_at'] ?? date('Y-m-d');
        $meta_title = trim($_POST['meta_title'] ?? '');
        $meta_description = trim($_POST['meta_description'] ?? '');
        $og_image = trim($_POST['og_image'] ?? '');
        $canonical_url = trim($_POST['canonical_url'] ?? '');

        // Penulis tidak bisa set headline (hanya admin)
        $is_headline = 0;

        // Handle draft vs publish
        $is_draft = isset($_POST['is_draft']) ? 1 : 0;

        // Handle image upload
        $image = $article['image'];
        if (!empty($_FILES['image']['tmp_name'])) {
            $newImage = uploadImage($_FILES['image']);
            if ($newImage) {
                if ($image) deleteImage($image);
                $image = $newImage;
            } else {
                $error = 'Gagal upload gambar. Pastikan file adalah JPG/PNG/WebP maksimal 5MB.';
            }
        }

        if (!$error) {
            if (!$slug) $slug = slugify($title);
            $slug = uniqueSlug($pdo, 'articles', $slug, $article['id']);

            if ($action === 'create') {
                $stmt = $pdo->prepare("INSERT INTO articles (title, slug, category, excerpt, content, author, published_at, image, is_headline, is_draft, meta_title, meta_description, og_image, canonical_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $slug, $category, $excerpt, $content, $author, $published_at, $image, $is_headline, $is_draft, $meta_title, $meta_description, $og_image, $canonical_url]);
                redirectWith('articles.php', 'success', $is_draft ? 'Artikel berhasil disimpan sebagai Draft.' : 'Artikel berhasil dipublikasikan.');
            } else {
                $stmt = $pdo->prepare("UPDATE articles SET title=?, slug=?, category=?, excerpt=?, content=?, published_at=?, image=?, is_headline=?, is_draft=?, meta_title=?, meta_description=?, og_image=?, canonical_url=? WHERE id=? AND author=?");
                $stmt->execute([$title, $slug, $category, $excerpt, $content, $published_at, $image, $is_headline, $is_draft, $meta_title, $meta_description, $og_image, $canonical_url, $id, $author]);
                redirectWith('articles.php', 'success', $is_draft ? 'Artikel berhasil diperbarui (Draft).' : 'Artikel berhasil diperbarui & dipublikasikan.');
            }
        }
    }

    $pageTitle = $action === 'create' ? 'Tulis Artikel Baru' : 'Edit Artikel';
    $userId = writerUserId();
    $role = writerRole();
    include __DIR__ . '/templates/writer_article_form.php';
    exit;
}

// --- DELETE ---
if ($action === 'delete' && $id > 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $stmt = $pdo->prepare("SELECT image FROM articles WHERE id = ? AND author = ?");
        $stmt->execute([$id, $author]);
        $art = $stmt->fetch();
        if ($art) {
            if ($art['image']) deleteImage($art['image']);
            $pdo->prepare("DELETE FROM articles WHERE id = ? AND author = ?")->execute([$id, $author]);
            redirectWith('articles.php', 'success', 'Artikel berhasil dihapus.');
        }
    }
    redirectWith('articles.php', 'error', 'Permintaan tidak valid.');
}

// --- LIST ---
$page = max(1, (int)($_GET['p'] ?? 1));
$perPage = 15;
$search = trim($_GET['q'] ?? '');
$catFilter = $_GET['cat'] ?? '';

$where = "WHERE author = ?";
$params = [$author];
if ($catFilter) { $where .= " AND category = ?"; $params[] = $catFilter; }
if ($search) { $where .= " AND (title LIKE ? OR content LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }

$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM articles $where");
$totalStmt->execute($params);
$total = (int)$totalStmt->fetchColumn();

$stmt = $pdo->prepare("SELECT * FROM articles $where ORDER BY published_at DESC, id DESC LIMIT $perPage OFFSET " . (($page - 1) * $perPage));
$stmt->execute($params);
$articles = $stmt->fetchAll();
$maxPage = max(1, (int)ceil($total / $perPage));

$pageTitle = 'Kelola Artikel Saya';
$listMode = true;
$userId = writerUserId();
$role = writerRole();
include __DIR__ . '/templates/writer_article_list.php';
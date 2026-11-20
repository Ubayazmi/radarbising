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
$userId = writerUserId();
$role = writerRole();
$categories = ['Kabar', 'Ulasan', 'Wawancara', 'Agenda'];

function catColor(string $cat): string {
    $map = [
        'Kabar' => 'bg-sky-500',
        'Ulasan' => 'bg-emerald-500',
        'Wawancara' => 'bg-fuchsia-500',
        'Agenda' => 'bg-amber-500',
    ];
    return $map[$cat] ?? 'bg-indigo-500';
}

// Closure for template
$catColor = fn(string $cat) => catColor($cat);

// --- CREATE / EDIT ---
if ($action === 'create' || $action === 'edit') {
    $video = [
        'id' => 0,
        'title' => '',
        'slug' => '',
        'category' => 'Kabar',
        'excerpt' => '',
        'description' => '',
        'embed_url' => '',
        'published_at' => date('Y-m-d'),
        'thumbnail' => '',
        'is_headline' => 0,
        'meta_title' => '',
        'meta_description' => '',
        'og_image' => '',
        'canonical_url' => '',
    ];

    if ($action === 'edit' && $id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ? AND author = ?");
        $stmt->execute([$id, $author]);
        $video = $stmt->fetch();
        if (!$video) {
            redirectWith('videos.php', 'error', 'Video tidak ditemukan atau Anda tidak memiliki akses.');
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $category = $_POST['category'] ?? 'Kabar';
        $excerpt = trim($_POST['excerpt'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $embed_url = trim($_POST['embed_url'] ?? '');
        $published_at = $_POST['published_at'] ?? date('Y-m-d');
        $meta_title = trim($_POST['meta_title'] ?? '');
        $meta_description = trim($_POST['meta_description'] ?? '');
        $og_image = trim($_POST['og_image'] ?? '');
        $canonical_url = trim($_POST['canonical_url'] ?? '');

        // Writer tidak bisa set headline
        $is_headline = 0;

        // Handle draft vs publish
        $is_draft = isset($_POST['is_draft']) ? 1 : 0;

        // Normalize embed URL to embed format - extract video ID and build proper embed URL
        $videoId = '';
        if (str_contains($embed_url, 'youtube.com/watch?v=')) {
            parse_str(parse_url($embed_url, PHP_URL_QUERY), $qs);
            $videoId = $qs['v'] ?? '';
        } elseif (str_contains($embed_url, 'youtu.be/')) {
            $parts = parse_url($embed_url);
            $videoId = ltrim($parts['path'] ?? '', '/');
        } elseif (str_contains($embed_url, 'youtube.com/embed/')) {
            $parts = parse_url($embed_url);
            $path = $parts['path'] ?? '';
            $pathParts = explode('/', trim($path, '/'));
            $videoId = end($pathParts);
            $videoId = explode('?', $videoId)[0];
        }
        
        // If we found a video ID, construct clean embed URL
        if ($videoId) {
            $embed_url = 'https://www.youtube.com/embed/' . $videoId;
        }

        // Handle thumbnail upload
        $thumbnail = $video['thumbnail'];
        if (!empty($_FILES['thumbnail']['tmp_name'])) {
            $newThumb = uploadImage($_FILES['thumbnail']);
            if ($newThumb) {
                if ($thumbnail) deleteImage($thumbnail);
                $thumbnail = $newThumb;
            } else {
                $error = 'Gagal upload thumbnail. Pastikan file adalah JPG/PNG/WebP maksimal 5MB.';
            }
        }

        if (!$error) {
            if (!$slug) $slug = slugify($title);
            $slug = uniqueSlug($pdo, 'videos', $slug, $video['id']);

            if ($action === 'create') {
                $stmt = $pdo->prepare("INSERT INTO videos (title, slug, category, excerpt, description, embed_url, published_at, thumbnail, is_headline, is_draft, meta_title, meta_description, og_image, canonical_url, author) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $slug, $category, $excerpt, $description, $embed_url, $published_at, $thumbnail, $is_headline, $is_draft, $meta_title, $meta_description, $og_image, $canonical_url, $author]);
                redirectWith('videos.php', 'success', $is_draft ? 'Video berhasil disimpan sebagai Draft.' : 'Video berhasil dipublikasikan.');
            } else {
                $stmt = $pdo->prepare("UPDATE videos SET title=?, slug=?, category=?, excerpt=?, description=?, embed_url=?, published_at=?, thumbnail=?, is_headline=?, is_draft=?, meta_title=?, meta_description=?, og_image=?, canonical_url=? WHERE id=? AND author=?");
                $stmt->execute([$title, $slug, $category, $excerpt, $description, $embed_url, $published_at, $thumbnail, $is_headline, $is_draft, $meta_title, $meta_description, $og_image, $canonical_url, $id, $author]);
                redirectWith('videos.php', 'success', $is_draft ? 'Video berhasil diperbarui (Draft).' : 'Video berhasil diperbarui & dipublikasikan.');
            }
        }
    }

    $pageTitle = $action === 'create' ? 'Tambah Video Baru' : 'Edit Video';
    include __DIR__ . '/templates/writer_video_form.php';
    exit;
}

// --- DELETE ---
if ($action === 'delete' && $id > 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $stmt = $pdo->prepare("SELECT thumbnail FROM videos WHERE id = ? AND author = ?");
        $stmt->execute([$id, $author]);
        $vid = $stmt->fetch();
        if ($vid) {
            if ($vid['thumbnail']) deleteImage($vid['thumbnail']);
            $pdo->prepare("DELETE FROM videos WHERE id = ? AND author = ?")->execute([$id, $author]);
            redirectWith('videos.php', 'success', 'Video berhasil dihapus.');
        }
    }
    redirectWith('videos.php', 'error', 'Permintaan tidak valid.');
}

// --- LIST ---
$page = max(1, (int)($_GET['p'] ?? 1));
$perPage = 15;
$search = trim($_GET['q'] ?? '');
$catFilter = $_GET['cat'] ?? '';

$where = "WHERE author = ?";
$params = [$author];
if ($catFilter) { $where .= " AND category = ?"; $params[] = $catFilter; }
if ($search) { $where .= " AND (title LIKE ? OR description LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }

$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM videos $where");
$totalStmt->execute($params);
$total = (int)$totalStmt->fetchColumn();

$stmt = $pdo->prepare("SELECT * FROM videos $where ORDER BY published_at DESC, id DESC LIMIT $perPage OFFSET " . (($page - 1) * $perPage));
$stmt->execute($params);
$videos = $stmt->fetchAll();
$maxPage = max(1, (int)ceil($total / $perPage));

$pageTitle = 'Kelola Video Saya';
$listMode = true;
include __DIR__ . '/templates/writer_video_list.php';
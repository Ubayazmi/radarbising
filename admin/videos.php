<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

requireAuth();

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);
$message = '';
$error = '';
$csrfToken = generateCsrfToken();

$categories = [
    'Kabar' => 'bg-sky-500',
    'Ulasan' => 'bg-emerald-500',
    'Wawancara' => 'bg-fuchsia-500',
    'Agenda' => 'bg-amber-500',
];

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
        $stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
        $stmt->execute([$id]);
        $video = $stmt->fetch();
        if (!$video) {
            redirectWith('videos.php', 'error', 'Video tidak ditemukan.');
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
        $is_headline = isset($_POST['is_headline']) ? 1 : 0;
        $is_draft = isset($_POST['is_draft']) ? 1 : 0;
        
        $meta_title = trim($_POST['meta_title'] ?? '');
        $meta_description = trim($_POST['meta_description'] ?? '');
        $og_image = trim($_POST['og_image'] ?? '');
        $canonical_url = trim($_POST['canonical_url'] ?? '');

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
                // Path could be /embed/VIDEO_ID or /embed/VIDEO_ID?params
                $pathParts = explode('/', trim($path, '/'));
                $videoId = end($pathParts);
                // Remove query string if present in video ID
                $videoId = explode('?', $videoId)[0];
            }
            
            // If we found a video ID, construct clean embed URL
            if ($videoId) {
                $embed_url = 'https://www.youtube.com/embed/' . $videoId;
            }

            if (!$slug) $slug = slugify($title);
            $slug = uniqueSlug($pdo, 'videos', $slug, $video['id']);

            if ($action === 'create') {
                $stmt = $pdo->prepare("INSERT INTO videos (title, slug, category, excerpt, description, embed_url, published_at, thumbnail, is_headline, is_draft, meta_title, meta_description, og_image, canonical_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $slug, $category, $excerpt, $description, $embed_url, $published_at, $thumbnail, $is_headline, $is_draft, $meta_title, $meta_description, $og_image, $canonical_url]);
                redirectWith('videos.php', 'success', $is_draft ? 'Video berhasil disimpan sebagai Draft.' : 'Video berhasil dipublikasikan.');
            } else {
                $stmt = $pdo->prepare("UPDATE videos SET title=?, slug=?, category=?, excerpt=?, description=?, embed_url=?, published_at=?, thumbnail=?, is_headline=?, is_draft=?, meta_title=?, meta_description=?, og_image=?, canonical_url=? WHERE id=?");
                $stmt->execute([$title, $slug, $category, $excerpt, $description, $embed_url, $published_at, $thumbnail, $is_headline, $is_draft, $meta_title, $meta_description, $og_image, $canonical_url, $id]);
                redirectWith('videos.php', 'success', $is_draft ? 'Video berhasil diperbarui (Draft).' : 'Video berhasil diperbarui.');
            }
        }
    }

    $pageTitle = $action === 'create' ? 'Tambah Video' : 'Edit Video';
    include __DIR__ . '/templates/video_form.php';
    exit;
}

// --- DELETE ---
if ($action === 'delete' && $id > 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $stmt = $pdo->prepare("SELECT thumbnail FROM videos WHERE id = ?");
        $stmt->execute([$id]);
        $vid = $stmt->fetch();
        if ($vid) {
            if ($vid['thumbnail']) deleteImage($vid['thumbnail']);
            $pdo->prepare("DELETE FROM videos WHERE id = ?")->execute([$id]);
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

$where = "WHERE 1=1";
$params = [];
if ($catFilter) { $where .= " AND category = ?"; $params[] = $catFilter; }
if ($search) { $where .= " AND (title LIKE ? OR description LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }

$total = (int)$pdo->query("SELECT COUNT(*) FROM videos $where")->fetchColumn();
$stmt = $pdo->prepare("SELECT * FROM videos $where ORDER BY published_at DESC, id DESC LIMIT $perPage OFFSET " . (($page - 1) * $perPage));
$stmt->execute($params);
$videos = $stmt->fetchAll();
$maxPage = max(1, (int)ceil($total / $perPage));

$pageTitle = 'Kelola Video';
$listMode = true;
include __DIR__ . '/templates/video_list.php';
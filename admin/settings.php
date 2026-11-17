<?php
/**
 * Website Settings — Pengaturan Umum Situs
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

requireAuth();

// Only Admin can access settings
if (($_SESSION['admin_user_role'] ?? '') !== 'Admin') {
    redirectWith('dashboard.php', 'error', 'Hanya Admin yang bisa mengakses pengaturan.');
}

$error = '';
$success = '';
$csrfToken = generateCsrfToken();

// Fetch current settings
$stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
$settings = $stmt->fetch() ?: [
    'site_name' => 'Radar Bising',
    'site_description' => 'Platform Artikel & Video Musik Indonesia',
    'site_logo' => '',
    'site_favicon' => '',
    'meta_title_template' => '{title} | Radar Bising',
    'meta_description_default' => 'Baca artikel, wawancara, dan tonton video musik terbaru di Radar Bising.',
    'og_image_default' => '',
    'google_analytics_id' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCsrfToken($_POST['csrf_token'] ?? '')) {
    $siteName = trim($_POST['site_name'] ?? '');
    $siteDescription = trim($_POST['site_description'] ?? '');
    $metaTitleTemplate = trim($_POST['meta_title_template'] ?? '');
    $metaDescriptionDefault = trim($_POST['meta_description_default'] ?? '');
    $ogImageDefault = trim($_POST['og_image_default'] ?? '');
    $googleAnalyticsId = trim($_POST['google_analytics_id'] ?? '');

    // Handle logo upload
    $siteLogo = $settings['site_logo'];
    if (!empty($_FILES['site_logo']['tmp_name'])) {
        $newLogo = uploadImage($_FILES['site_logo']);
        if ($newLogo) {
            if ($siteLogo) deleteImage($siteLogo);
            $siteLogo = $newLogo;
        } else {
            $error = 'Gagal upload logo. Pastikan file adalah JPG/PNG/WebP maksimal 5MB.';
        }
    }

    // Handle favicon upload
    $siteFavicon = $settings['site_favicon'];
    if (!empty($_FILES['site_favicon']['tmp_name'])) {
        $newFavicon = uploadImage($_FILES['site_favicon']);
        if ($newFavicon) {
            if ($siteFavicon) deleteImage($siteFavicon);
            $siteFavicon = $newFavicon;
        } else {
            $error = 'Gagal upload favicon. Pastikan file adalah ICO/PNG maksimal 1MB.';
        }
    }

    if (!$error) {
        if ($settings['id']) {
            $stmt = $pdo->prepare("UPDATE settings SET site_name=?, site_description=?, site_logo=?, site_favicon=?, meta_title_template=?, meta_description_default=?, og_image_default=?, google_analytics_id=?, updated_at=datetime('now') WHERE id=?");
            $stmt->execute([$siteName, $siteDescription, $siteLogo, $siteFavicon, $metaTitleTemplate, $metaDescriptionDefault, $ogImageDefault, $googleAnalyticsId, $settings['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO settings (site_name, site_description, site_logo, site_favicon, meta_title_template, meta_description_default, og_image_default, google_analytics_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$siteName, $siteDescription, $siteLogo, $siteFavicon, $metaTitleTemplate, $metaDescriptionDefault, $ogImageDefault, $googleAnalyticsId]);
        }
        redirectWith('settings.php', 'success', 'Pengaturan berhasil disimpan.');
    }
}

$pageTitle = 'Pengaturan Website';
include __DIR__ . '/templates/settings.php';
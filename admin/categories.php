<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

requireAuth();

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);
$error = '';
$csrfToken = generateCsrfToken();

// Current categories (hardcoded for now, could be moved to DB later)
$defaultCategories = [
    'Kabar' => ['color' => 'bg-sky-500', 'label' => 'Berita & Kabar Terkini'],
    'Ulasan' => ['color' => 'bg-emerald-500', 'label' => 'Ulasan Album & Single'],
    'Wawancara' => ['color' => 'bg-fuchsia-500', 'label' => 'Wawancara Eksklusif'],
    'Agenda' => ['color' => 'bg-amber-500', 'label' => 'Jadwal Konser & Event'],
];

// Count usage
$catCounts = [];
foreach ($pdo->query("SELECT category, COUNT(*) as c FROM articles GROUP BY category")->fetchAll() as $row) {
    $catCounts[$row['category']] = ($catCounts[$row['category']] ?? 0) + (int)$row['c'];
}
foreach ($pdo->query("SELECT category, COUNT(*) as c FROM videos GROUP BY category")->fetchAll() as $row) {
    $catCounts[$row['category']] = ($catCounts[$row['category']] ?? 0) + (int)$row['c'];
}

// For this MVP, categories are defined in code. In future, can add DB table.
// We'll just show the management UI.

$pageTitle = 'Kelola Kategori';
include __DIR__ . '/templates/category_list.php';
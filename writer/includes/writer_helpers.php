<?php
/**
 * Writer Portal — Helper functions
 */

require_once __DIR__ . '/../../../includes/StorageAdapter.php';

function slugify(string $text): string {
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('/[^a-zA-Z0-9\s-]/', '', $text);
    $text = strtolower(trim($text));
    $text = preg_replace('/[\s-]+/', '-', $text);
    return $text;
}

function uniqueSlug(PDO $pdo, string $table, string $baseSlug, int $excludeId = 0): string {
    $slug = $baseSlug;
    $counter = 1;
    while (true) {
        $sql = "SELECT id FROM $table WHERE slug = ?" . ($excludeId ? " AND id != $excludeId" : "");
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$slug]);
        if (!$stmt->fetch()) {
            return $slug;
        }
        $slug = $baseSlug . '-' . $counter++;
    }
}

function e(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function formatDate(string $date): string {
    try { return (new DateTime($date))->format('d M Y'); } catch (Exception) { return $date; }
}

function setFlash(string $key, string $message): void {
    $_SESSION['flash'][$key] = $message;
}

function getFlash(string $key): ?string {
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

function hasFlash(string $key): bool {
    return isset($_SESSION['flash'][$key]);
}
<?php
/**
 * Helper functions for admin panel
 */

require_once __DIR__ . '/../../includes/StorageAdapter.php';

/**
 * Generate URL-friendly slug from string
 */
function slugify(string $text): string {
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('/[^a-zA-Z0-9\s-]/', '', $text);
    $text = strtolower(trim($text));
    $text = preg_replace('/[\s-]+/', '-', $text);
    return $text;
}

/**
 * Ensure unique slug by appending number if exists
 */
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

/**
 * Upload image — delegated to includes/StorageAdapter.php
 * Returns Supabase Storage URL (production) or local path (dev)
 * @deprecated use uploadImage() from includes/StorageAdapter.php
 */

/**
 * Sanitize HTML output
 */
function e(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Format date for display
 */
function formatDate(string $date): string {
    try {
        return (new DateTime($date))->format('d M Y');
    } catch (Exception) {
        return $date;
    }
}

/**
 * Flash message handling
 */
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
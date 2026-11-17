<?php
/**
 * Helper functions for admin panel
 */

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
 * Upload image file, return relative path or false on failure
 */
function uploadImage(array $file, string $targetDir = 'uploads'): string|false {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return false;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    if (!in_array($file['type'], $allowedTypes)) {
        return false;
    }
    if ($file['size'] > $maxSize) {
        return false;
    }

    // Verify it's actually an image
    $info = getimagesize($file['tmp_name']);
    if (!$info) {
        return false;
    }

    $ext = match ($file['type']) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        default => 'jpg',
    };

    $filename = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
    $destDir = __DIR__ . '/../' . $targetDir;
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }
    $destPath = $destDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return false;
    }

    // Return relative path from project root
    return 'admin/' . $targetDir . '/' . $filename;
}

/**
 * Delete image file if exists
 */
function deleteImage(string $relativePath): bool {
    $fullPath = __DIR__ . '/../../' . $relativePath;
    if (file_exists($fullPath) && is_file($fullPath)) {
        return unlink($fullPath);
    }
    return true;
}

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
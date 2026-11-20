<?php
/**
 * Radar Bising — Unified Storage Adapter
 *
 * Provides uploadImage() and deleteImage() that work with either:
 *   - Supabase Storage (production/Vercel) → returns public URL
 *   - Local filesystem (development)       → returns relative path
 *
 * Callers use this identically — no if/else in business logic.
 */

declare(strict_types=1);

if (!class_exists('SupabaseStorage')) {
    require_once __DIR__ . '/SupabaseStorage.php';
}

/**
 * Get the active storage adapter (singleton per request)
 */
function getStorageAdapter(): object
{
    static $adapter = null;
    if ($adapter !== null) return $adapter;

    $storage = new SupabaseStorage();
    if ($storage->isConfigured()) {
        // Try to ensure bucket exists (idempotent)
        @$storage->createBucket();
        $adapter = $storage;
        return $adapter;
    }

    // Fallback: use filesystem marker
    $adapter = new stdClass();
    $adapter->useLocal = true;
    return $adapter;
}

/**
 * Upload image — returns public URL (Supabase) or relative path (local)
 *
 * @param array  $file      $_FILES['image'] or $_FILES['thumbnail']
 * @param string $prefix    Storage folder prefix: 'articles', 'videos', 'settings'
 * @return string|false     Public URL or relative path, false on failure
 */
function uploadImage(array $file, string $prefix = 'uploads'): string|false
{
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return false;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    if (!in_array($file['type'], $allowedTypes, true)) {
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
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
        default      => 'jpg',
    };

    $filename = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;

    // --- Supabase Storage (production) ---
    $adapter = getStorageAdapter();
    if (is_object($adapter) && method_exists($adapter, 'upload') && $adapter->isConfigured()) {
        $remotePath = "{$prefix}/{$filename}";
        $result = $adapter->upload($file['tmp_name'], $remotePath, $file['type']);
        if ($result !== false) {
            return $result; // Public URL
        }
        // If upload failed, fall through to local
        error_log("Supabase upload failed for {$remotePath}, falling back to local");
    }

    // --- Local filesystem fallback (development) ---
    $targetDir = __DIR__ . '/../admin/uploads';
    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0755, true);
    }
    $destPath = "{$targetDir}/{$filename}";
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return false;
    }

    return "admin/uploads/{$filename}";
}

/**
 * Delete image — works with both Supabase URLs and local paths
 *
 * @param string $path  Public URL or relative path
 * @return bool
 */
function deleteImage(string $path): bool
{
    if (empty($path)) {
        return true;
    }

    // --- Supabase URL ---
    if (str_starts_with($path, 'http')) {
        $adapter = getStorageAdapter();
        if (is_object($adapter) && method_exists($adapter, 'delete')) {
            // Extract the path after /public/{bucket}/
            if (preg_match('#/object/public/[^/]+/(.+)$#', $path, $m)) {
                return $adapter->delete($m[1]);
            }
        }
        // Cannot delete remote via local — return true (graceful)
        return true;
    }

    // --- Local file ---
    $fullPath = __DIR__ . '/../../' . $path;
    if (file_exists($fullPath) && is_file($fullPath)) {
        return @unlink($fullPath);
    }
    return true;
}

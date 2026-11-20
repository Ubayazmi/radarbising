<?php
/**
 * Radar Bising — Supabase Storage Client
 * Wraps Supabase Storage REST API via cURL.
 * Uses SERVICE_ROLE key for server-side operations.
 */

declare(strict_types=1);

class SupabaseStorage
{
    private string $baseUrl;
    private string $serviceKey;
    private string $bucket;

    public function __construct(?string $url = null, ?string $key = null, ?string $bucket = null)
    {
        $this->baseUrl = $url ?? getenv('SUPABASE_URL') ?? $_ENV['SUPABASE_URL'] ?? '';
        $this->serviceKey = $key ?? getenv('SUPABASE_SERVICE_KEY') ?? $_ENV['SUPABASE_SERVICE_KEY'] ?? '';
        $this->bucket = $bucket ?? getenv('SUPABASE_STORAGE_BUCKET') ?? $_ENV['SUPABASE_STORAGE_BUCKET'] ?? '';

        // Remove trailing slash
        $this->baseUrl = rtrim($this->baseUrl, '/');
    }

    /**
     * Check if this instance is configured and ready
     */
    public function isConfigured(): bool
    {
        return !empty($this->baseUrl)
            && !empty($this->serviceKey)
            && !empty($this->bucket)
            && function_exists('curl_init')
            && $this->baseUrl !== 'https://xxx.supabase.co'
            && !str_contains($this->serviceKey, 'xxx')
            && !str_contains($this->serviceKey, 'your-');
    }

    /**
     * Upload a file to Supabase Storage
     *
     * @param string $tempPath Path to the uploaded temp file (from $_FILES)
     * @param string $path Remote path (e.g. 'articles/abc123.jpg')
     * @param string $mimeType MIME type
     * @return string|false Public URL on success, false on failure
     */
    public function upload(string $tempPath, string $path, string $mimeType = 'image/jpeg'): string|false
    {
        if (!$this->isConfigured()) {
            error_log('SupabaseStorage: not configured');
            return false;
        }

        if (!file_exists($tempPath)) {
            error_log("SupabaseStorage: temp file not found: $tempPath");
            return false;
        }

        $url = "{$this->baseUrl}/storage/v1/object/{$this->bucket}/{$path}";

        $fh = fopen($tempPath, 'rb');
        if (!$fh) return false;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_PUT => true,
            CURLOPT_INFILE => $fh,
            CURLOPT_INFILESIZE => filesize($tempPath),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$this->serviceKey}",
                "Content-Type: {$mimeType}",
                "x-upsert: true",
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);
        fclose($fh);

        if ($httpCode < 200 || $httpCode >= 300) {
            error_log("SupabaseStorage upload failed: HTTP $httpCode — $curlErr — $response");
            return false;
        }

        // Return public URL
        return $this->getPublicUrl($path);
    }

    /**
     * Delete a file from Supabase Storage
     *
     * @param string $path Remote path (e.g. 'articles/abc123.jpg')
     * @return bool
     */
    public function delete(string $path): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $url = "{$this->baseUrl}/storage/v1/object/{$this->bucket}/{$path}";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$this->serviceKey}",
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 15,
        ]);

        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // 200 or 404 (already deleted) both OK
        return $httpCode === 200 || $httpCode === 404;
    }

    /**
     * Get public URL for a stored file
     */
    public function getPublicUrl(string $path): string
    {
        return "{$this->baseUrl}/storage/v1/object/public/{$this->bucket}/{$path}";
    }

    /**
     * Create the bucket if it doesn't exist
     */
    public function createBucket(): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $url = "{$this->baseUrl}/storage/v1/bucket";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$this->serviceKey}",
                "Content-Type: application/json",
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'name' => $this->bucket,
                'public' => true,
                'file_size_limit' => 10485760, // 10MB
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 15,
        ]);

        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // 201 = created, 409 = already exists
        return $httpCode === 201 || $httpCode === 409;
    }
}

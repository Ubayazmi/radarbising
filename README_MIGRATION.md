# Radar Bising — Automated Migration & Deployment (GitHub Actions)

## Overview

| Workflow | Trigger | Fungsi |
|----------|---------|--------|
| `.github/workflows/migrate.yml` | Manual (**Run workflow**) | Migrasi SQLite → Supabase PostgreSQL |
| `.github/workflows/deploy.yml` | Push ke `main` + manual | Deploy ke Vercel |

## Migration Workflow — Langkah

1. **Checkout** repo + setup PHP 8.2 (`pdo_sqlite`, `pdo_pgsql`) + Composer deps.
2. **Download `database.sqlite`** dari bucket private `migration-source` di Supabase Storage (via `SUPABASE_SERVICE_KEY`).
3. **Verifikasi** jumlah baris sumber (fail kalau empty).
4. **Terapkan `supabase/schema.sql`** via `psql` (idempoten, `IF NOT EXISTS`).
5. **Jalankan `php migrate.php`** — opsi `--dry-run` untuk validasi tanpa menulis.
6. **Verifikasi hasil**: bandingkan jumlah baris per tabel SQLite vs PostgreSQL, pastikan tabel `sessions` masih kosong.
7. **Buat bucket storage `radarbising-uploads`** (public) via Storage API.
8. **Hapus `database.sqlite`** dari runner (keamanan).

### Menjalankan

Buka **GitHub → Actions → Supabase Migration → Run workflow**:
- **Dry run dulu** (centang) → validasi skema + koneksi + hitung data.
- **Live** (tanpa centang) → eksekusi migrasi penuh.

## Deploy Workflow — Langkah

Push ke `main` → workflow otomatis:
1. Setup PHP + composer install.
2. Lint semua file PHP (`php -l`).
3. Deploy via Vercel CLI (preview untuk push, production untuk manual run).

## Secret yang dibutuhkan

Lihat **`SECRETS_SETUP.md`** untuk daftar lengkap + cara mendapat tiap nilai.

Singkatnya (GitHub → Settings → Secrets → Actions):

```
SUPABASE_URL, SUPABASE_SERVICE_KEY, DB_DSN   (migration)
VERCEL_TOKEN, VERCEL_ORG_ID, VERCEL_PROJECT_ID (deploy)
```

Environment variables aplikasi (untuk Vercel Dashboard, bukan GitHub):
```
APP_ENV, APP_DEBUG, SUPABASE_URL, SUPABASE_ANON_KEY, SUPABASE_SERVICE_KEY,
DB_DSN (pooler 6543), SESSION_SECRET, UPLOAD_DRIVER, SUPABASE_STORAGE_BUCKET
```

## Keamanan

- `.env`, `database.sqlite`, `vendor/` — semua di-gitignore.
- Service role key hanya dipakai di Actions / server; tidak pernah ke browser.
- Koneksi database selalu SSL (`sslmode=require`).
- `database.sqlite` dihapus dari runner setelah selesai.

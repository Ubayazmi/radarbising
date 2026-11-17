# Migration Worklist — Radar Bising (Plan 02–05)

> Checklist pekerjaan yang harus diselesaikan setelah audit (Plan 01).  
> Setiap item: `[ ]` = belum, `[x]` = selesai. Update saat dikerjakan.

---

## Plan 02 — Database Migration (SQLite → Supabase PostgreSQL)

### 02.1 Schema Design & DDL ✅ DONE
- [x] Buat `supabase/schema.sql` dengan tabel: `articles`, `videos`, `users`, `login_logs`, `settings`, `sessions`
- [x] Tipe data PG: `BIGSERIAL` PK, `TIMESTAMPTZ`, `BOOLEAN`, `TEXT`, `UNIQUE`, `CHECK` constraints
- [x] Foreign Keys (string match `author` → `users.full_name`)
- [x] Indexes untuk performa (category, author, headline, published_at, slug)
- [x] Tabel `sessions` untuk custom PHP session handler
- [x] Triggers auto-update `updated_at`
- [x] RLS policies placeholder
- [x] Initial `settings` row

**File:** `supabase/schema.sql` — siap dijalankan di Supabase SQL Editor

### 02.2 Migration Script ✅ DONE
- [x] Buat script PHP `migrate.php` (CLI) dengan:
  - Baca SQLite via PDO (`pdo_sqlite`)
  - Tulis ke PostgreSQL via PDO (`pdo_pgsql` + SSL)
  - Type casting: `INTEGER` → `BIGINT`, date strings → `TIMESTAMPTZ`
  - Batch processing (default 100 rows), transaksi per batch
  - Validasi row count per tabel, log errors
  - Reset sequences (`setval`) setelah migrasi
  - `--dry-run` mode untuk test

**File:** `migrate.php` — siap dijalankan: `php migrate.php`

### 02.3 Dependencies & Config ✅ DONE
- [x] `composer.json` dengan `vlucas/phpdotenv` (untuk load `.env` local dev)
- [x] `composer install --no-dev --optimize-autoloader` → `vendor/` ready
- [x] `.env.example` template dengan semua variabel yang dibutuhkan

### 02.4 Pre-Migration Checklist (Butuh Credentials → Vercel Env)
- [ ] **Supabase Project** dibuat (atau existing)
- [ ] **Run `supabase/schema.sql`** di Supabase SQL Editor
- [ ] **Storage bucket** `radarbising-uploads` dibuat (public atau signed URL)
- [ ] **Environment Variables** diset di **Vercel Dashboard** (Project Settings → Environment Variables):
  | Variable | Description | Source |
  |----------|-------------|--------|
  | `SUPABASE_URL` | `https://<ref>.supabase.co` | Supabase Dashboard → Settings → API |
  | `SUPABASE_ANON_KEY` | `anon` public key | Supabase Dashboard → Settings → API |
  | `SUPABASE_SERVICE_KEY` | `service_role` secret key | Supabase Dashboard → Settings → API |
  | `DB_DSN` | `pgsql:host=db.<ref>.supabase.co;port=5432;dbname=postgres;user=postgres;password=***;sslmode=require` | Supabase Dashboard → Settings → Database → Connection String (Transaction Pooler atau Direct) |
  | `SESSION_SECRET` | 32 bytes hex (`openssl rand -hex 32`) | Generate locally |
  | `UPLOAD_DRIVER` | `supabase` | Fixed |
  | `SUPABASE_STORAGE_BUCKET` | `radarbising-uploads` | Fixed |
  | `APP_ENV` | `production` | Fixed |
  | `APP_DEBUG` | `false` | Fixed |
- [ ] **Local `.env`** untuk development (copy `.env.example` → `.env`, isi credentials lokal)
- [ ] **Backup SQLite**: `cp database.sqlite database.sqlite.bak`
- [ ] **Jalankan migrasi**: `php migrate.php` (pastikan `pdo_pgsql` extension aktif di PHP lokal)
- [ ] **Verifikasi**: Bandingkan `SELECT COUNT(*)` per tabel SQLite vs PostgreSQL

### 02.5 Post-Migration Verification
- [ ] Login test: Admin, Editor, Writer (via Supabase DB users)
- [ ] Cek data: articles, videos, users, login_logs, settings row counts match
- [ ] Cek sequence values: `SELECT last_value FROM <table>_id_seq`

---

## Plan 03 — Code Adaptation (App → Supabase + Vercel-ready)

### 03.1 Database Connection & Config
- [ ] Tambah `composer.json` dengan dependensi minimal:
  ```json
  {
    "require": {
      "vlucas/phpdotenv": "^5.6",
      "supabase/supabase-php": "^1.0"  // atau aws/aws-sdk-php untuk S3
    },
    "require-dev": {}
  }
  ```
- [ ] Buat `.env.example`:
  ```env
  APP_ENV=production
  APP_DEBUG=false
  SUPABASE_URL=https://xxx.supabase.co
  SUPABASE_ANON_KEY=xxx
  SUPABASE_SERVICE_KEY=xxx
  DB_DSN=pgsql:host=db.xxx.supabase.co;port=5432;dbname=postgres;user=postgres;password=xxx;sslmode=require
  SESSION_SECRET=generate-32-bytes-hex
  UPLOAD_DRIVER=supabase  # atau s3
  SUPABASE_STORAGE_BUCKET=radarbising-uploads
  ```
- [ ] Update `admin/includes/db.php`, `writer/includes/writer_db.php`, `index.php`:
  - Load `.env` via `Dotenv` (dev) / `$_ENV` (prod)
  - DSN dari `DB_DSN` env var
  - Set `PDO::ATTR_EMULATE_PREPARES => false`
- [ ] Hapus referensi `database.sqlite` path hardcoded

### 03.2 Session Handler (PostgreSQL-backed)
- [ ] Buat `includes/SessionHandlerPg.php` implement `SessionHandlerInterface`:
  - `open()`: connect PG (reuse existing PDO or new persistent)
  - `read($id)`: `SELECT data FROM sessions WHERE id = ? AND expires_at > NOW()`
  - `write($id, $data)`: `UPSERT INTO sessions (id, data, expires_at) VALUES (?, ?, NOW() + INTERVAL '1 year')`
  - `destroy($id)`: `DELETE FROM sessions WHERE id = ?`
  - `gc($maxlifetime)`: `DELETE FROM sessions WHERE expires_at < NOW()`
- [ ] Register di `admin/includes/auth.php` & `writer/includes/writer_auth.php`:
  ```php
  $handler = new SessionHandlerPg($pdo);
  session_set_save_handler($handler, true);
  session_start();
  ```
- [ ] Hapus `session_save_path()` lokal & `mkdir sessions/`

### 03.3 Upload → Supabase Storage
- [ ] Buat `includes/StorageSupabase.php`:
  - `upload($tmpPath, $destPath, $mimeType)` → return public/signed URL
  - `delete($path)` → remove from bucket
  - Gunakan `supabase/supabase-php` atau `aws/aws-sdk-php` (S3 compat)
- [ ] Refactor `uploadImage()` di `admin/includes/helpers.php` & `writer/includes/writer_helpers.php`:
  - Deteksi env `VERCEL` atau `UPLOAD_DRIVER=supabase`
  - Simpan file sementara → upload → return URL (simpan URL penuh di DB, bukan path relatif)
  - Hapus `getimagesize()` dependency? (bisa tetep pakai untuk validasi)
- [ ] Update DB schema: `articles.image`, `videos.thumbnail`, `settings.site_logo`, `settings.site_favicon` → `TEXT` URL (bukan path)
- [ ] Migrasi file existing di `admin/uploads/` ke Supabase Storage (script satu kali)

### 03.4 Query Compatibility Fixes
- [ ] Ganti `datetime('now')` → `NOW()` di `admin/users.php`, `admin/settings.php`
- [ ] Ganti `date('now', '-7 days')` → `CURRENT_DATE - INTERVAL '7 days'` di `writer/dashboard.php`, `writer/profile.php`
- [ ] Ganti `CAST(user_id AS UNSIGNED)` → `user_id::bigint` di `admin/users.php`
- [ ] Abstract ke helper `nowPg()`, `weekAgoPg()` di `helpers.php` untuk konsistensi
- [ ] Review semua query `UNION`, subquery, `LIMIT/OFFSET` — kompatibel PG

### 03.5 Cleanup & Hardcoded Removal
- [ ] Hapus `$WRITERS` fallback array di `writer/includes/writer_auth.php`
- [ ] Hapus `assets/` dari repo (sudah di `.gitignore`, pastikan tidak ter-track)
- [ ] Hapus `sessions/` folder lokal (sudah di `.gitignore`)
- [ ] Hapus `database.sqlite` dari repo (sudah di `.gitignore`)
- [ ] Hapus `dump_schema.php`, `dump_schema2.php` (audit artifacts)

---

## Plan 04 — Vercel Deployment Configuration

### 04.1 Vercel Config Files
- [ ] Buat `vercel.json`:
  ```json
  {
    "buildCommand": "composer install --no-dev --optimize-autoloader",
    "devCommand": "php -S localhost:8000 -t .",
    "installCommand": "composer install",
    "framework": null,
    "functions": {
      "index.php": { "maxDuration": 30 },
      "admin/**/*.php": { "maxDuration": 30 },
      "writer/**/*.php": { "maxDuration": 30 }
    },
    "routes": [
      { "src": "/assets/(.*)", "headers": { "cache-control": "public, max-age=31536000, immutable" }, "dest": "/assets/$1" },
      { "src": "/admin/uploads/(.*)", "dest": "/admin/uploads/$1" },
      { "src": "/(.*)", "dest": "/index.php" }
    ],
    "env": {
      "APP_ENV": "production",
      "APP_DEBUG": "false"
    }
  }
  ```
- [ ] Buat `Dockerfile` (optional, jika pakai Docker runtime):
  ```dockerfile
  FROM php:8.2-fpm-alpine
  RUN apk add --no-cache postgresql-dev libpng-dev libwebp-dev libjpeg-turbo-dev freetype-dev \
      && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
      && docker-php-ext-install -j$(nproc) pdo_pgsql pgsql gd bcmath opcache
  COPY . /var/www/html
  WORKDIR /var/www/html
  RUN composer install --no-dev --optimize-autoloader
  EXPOSE 9000
  CMD ["php-fpm"]
  ```
- [ ] Buat `.vercelignore`:
  ```
  .git
  .github
  node_modules
  vendor
  assets/
  sessions/
  database.sqlite*
  admin/uploads/
  writer/includes/sessions/
  *.log
  *.bak
  .env
  .env.local
  dump_schema*.php
  migrate.php
  docs/
  ```

### 04.2 Environment Variables di Vercel Dashboard
- [ ] Set Project Settings → Environment Variables:
  - `SUPABASE_URL`, `SUPABASE_ANON_KEY`, `SUPABASE_SERVICE_KEY`
  - `DB_DSN` (PostgreSQL connection string dengan `sslmode=require`)
  - `SESSION_SECRET` (32 bytes hex, `openssl rand -hex 32`)
  - `UPLOAD_DRIVER=supabase`
  - `SUPABASE_STORAGE_BUCKET=radarbising-uploads`
  - `APP_ENV=production`, `APP_DEBUG=false`

### 04.3 Supabase Setup
- [ ] Buat project Supabase baru (atau gunakan existing)
- [ ] Jalankan `schema.sql` di SQL Editor
- [ ] Buat Storage bucket `radarbising-uploads` (public atau signed URL)
- [ ] Set RLS policies jika perlu (opsional, app handle auth)
- [ ] Enable `pg_stat_statements` untuk monitoring

### 04.4 CI/CD (GitHub Actions)
- [ ] Buat `.github/workflows/vercel.yml`:
  - Trigger: push to `main`, PR
  - Jobs: lint (PHPStan/PHP_CodeSniffer optional), test (jika ada), deploy preview via Vercel CLI
  - Vercel CLI: `vercel --token=${VERCEL_TOKEN} --scope=${VERCEL_ORG_ID}`

---

## Plan 05 — Testing, Go-Live & Ops

### 05.1 Functional Testing
- [ ] **Public Frontend:**
  - [ ] Home loads headline + articles + videos
  - [ ] Article detail: SEO meta, OG tags, related articles
  - [ ] Video detail: embed plays, related videos
  - [ ] Articles list: filter category, search, pagination
  - [ ] Videos list: filter category, search, pagination
  - [ ] Exclusive/Wawancara page
  - [ ] Dark/Light mode toggle persists
- [ ] **Admin Panel:**
  - [ ] Login (Admin, Editor)
  - [ ] Dashboard stats match DB
  - [ ] CRUD Article: create, edit, draft/publish, headline, SEO, image upload → Supabase Storage
  - [ ] CRUD Video: create, edit, embed URL, thumbnail upload
  - [ ] Users: create/edit/delete, role assignment
  - [ ] Settings: logo, favicon, meta templates, GA ID
  - [ ] Login Logs: view, filter, purge
  - [ ] Profile: change name, password
- [ ] **Writer Portal:**
  - [ ] Login (Writer, Editor)
  - [ ] Dashboard personal stats
  - [ ] CRUD own articles only
  - [ ] CRUD own videos only
  - [ ] Profile: change name, password
- [ ] **Cross-portal:**
  - [ ] Session shared (login admin → writer works)
  - [ ] CSRF tokens valid
  - [ ] Logout destroys session

### 05.2 Performance & Load
- [ ] Vercel Function cold start < 2s
- [ ] DB query latency < 100ms (Supabase pooled connection)
- [ ] Image upload < 5s (Supabase Storage)
- [ ] Enable OPcache in Docker/php.ini
- [ ] Configure Supabase connection pooling (PgBouncer)

### 05.3 Security Hardening
- [ ] HTTPS enforced (Vercel default)
- [ ] `session.cookie_secure=true` di production
- [ ] CSP header via `vercel.json` atau middleware
- [ ] Rate limit login (Supabase Edge Function atau Vercel Edge Middleware)
- [ ] Sanitize TinyMCE output (sudah pakai `htmlspecialchars` di display, tapi pastikan `content` aman)

### 05.4 Go-Live Checklist
- [ ] Custom domain configured di Vercel + DNS
- [ ] Supabase backups enabled (Point-in-Time Recovery)
- [ ] Monitoring: Vercel Analytics, Supabase Logs, UptimeRobot
- [ ] Rollback plan: `vercel rollback` + DB migration down script
- [ ] Documentation update: `README.md` dengan deploy steps

### 05.5 Post-Launch
- [ ] Smoke test production URL
- [ ] Verify SSL, headers, CSP
- [ ] Submit sitemap to Google Search Console
- [ ] Monitor error logs 24-48h

---

## Progress Tracking

| Plan | Status | Target Selesai | Catatan |
|------|--------|----------------|---------|
| 01 Audit | ✅ Done | 2026-11-17 | `docs/AUDIT_REPORT.md`, `docs/MIGRATION_WORKLIST.md` |
| 02 DB Migration | 🟡 **Prepared** | | Schema, script, deps ready — waiting for Supabase credentials (via Vercel Env) |
| 03 Code Adapt | ⬜ Pending | | Depends on 02 completion |
| 04 Vercel Config | ⬜ Pending | | Parallel dengan 03 |
| 05 Testing/Go-Live | ⬜ Pending | | After 03+04 |

---

**Catatan:** Worklist ini *living document*. Update `[ ]` → `[x]` saat item selesai. Tambah item baru jika ditemukan selama eksekusi.
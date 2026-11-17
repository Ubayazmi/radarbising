# Audit Report — Radar Bising

**Project:** Radar Bising (Music Article & Video Platform)  
**Date:** 2026-11-17  
**Auditor:** Hermes Agent  
**Scope:** Plan 01 — Project Audit & Preparation (read-only)

---

## 1. Ringkasan Struktur Project

```
radarbising/
├── index.php                      # Single-file public frontend (entry point, 80KB)
├── database.sqlite                # SQLite database (57KB, NOT MySQL)
├── login_log.php                  # Login log viewer (admin)
├── _exc.html / _home.html         # Static HTML templates (legacy)
├── Layout WEB Radar Bising.drawio # Architecture diagram
├── admin/                         # Admin panel (Editor, Admin roles)
│   ├── *.php                      # CRUD: articles, videos, users, categories, settings, dashboard
│   ├── includes/
│   │   ├── auth.php               # Auth & session (Admin/Editor)
│   │   ├── db.php                 # PDO SQLite connection
│   │   └── helpers.php            # Slug, upload, flash, date helpers
│   ├── templates/                 # Form & list templates (TinyMCE editor)
│   └── uploads/                   # Image storage (7.9 MB, 4 files)
├── writer/                        # Writer portal (Author role)
│   ├── *.php                      # CRUD: articles, videos (own content only)
│   ├── includes/
│   │   ├── writer_auth.php        # Auth & session (Writer/Editor)
│   │   ├── writer_db.php          # PDO SQLite connection (shared)
│   │   └── writer_helpers.php     # Duplicate helpers (same as admin)
│   └── templates/                 # Writer-specific forms
├── sessions/                      # File-based PHP sessions (shared across portals)
├── assets/                        # TinyMCE 6 assets (16 MB, 300+ files) — CDN recommended
└── .gitignore                     # Excludes assets/, uploads/, writer/, sessions/, *.sqlite
```

**Key Observations:**
- **Single-file public frontend** (`index.php`) handles all public pages: home, article detail, video detail, articles list, videos list, search, exclusive/wawancara.
- **Two admin portals:** `admin/` (full CMS) and `writer/` (author self-service). Both share `database.sqlite` and `sessions/`.
- **No Composer / no `vendor/`** — pure PHP 8.2+ native, zero runtime dependencies beyond extensions.
- **Database is SQLite**, not MySQL as the plan assumed. This is a **critical discrepancy** for migration planning.

---

## 2. Daftar Tabel & Relasi Database

| Tabel | Kolom Utama | Tipe | Constraints |
|-------|-------------|------|-------------|
| `articles` | `id` PK AUTOINCREMENT, `title`, `slug` UNIQUE, `category`, `excerpt`, `content`, `author`, `published_at`, `image`, `is_headline`, `is_draft`, `meta_title`, `meta_description`, `og_image`, `canonical_url` | TEXT / INTEGER | PK, UNIQUE(slug), DEFAULT 0 on flags |
| `videos` | `id` PK AUTOINCREMENT, `title`, `slug` UNIQUE, `category`, `excerpt`, `description`, `embed_url`, `published_at`, `thumbnail`, `is_headline`, `is_draft`, `meta_title`, `meta_description`, `og_image`, `canonical_url`, `author` | TEXT / INTEGER | PK, UNIQUE(slug), DEFAULT 0 on flags |
| `users` | `id` PK AUTOINCREMENT, `user_id` UNIQUE, `full_name`, `role` (Admin/Editor/Writer), `password_hash`, `avatar`, `created_at` | TEXT / INTEGER | PK, UNIQUE(user_id), DEFAULT 'Writer' |
| `login_logs` | `id` PK AUTOINCREMENT, `portal` ('admin'/'writer'), `username`, `password_attempt`, `ip`, `user_agent`, `success`, `created_at` | TEXT / INTEGER / DATETIME | PK |
| `settings` | `id` PK AUTOINCREMENT, `site_name`, `site_description`, `site_logo`, `site_favicon`, `meta_title_template`, `meta_description_default`, `og_image_default`, `google_analytics_id`, `updated_at` | TEXT / DATETIME | PK (single row) |

**Relasi (implicit, no FK constraints):**
- `articles.author` → `users.full_name` (string match, not FK)
- `videos.author` → `users.full_name`
- `users.role` determines portal access: Admin/Editor → `admin/`, Writer/Editor → `writer/`
- `login_logs.portal` tracks which portal was attempted

**Indexes:** Only implicit via PK/UNIQUE. No explicit indexes on `category`, `published_at`, `is_draft`, `author`.

---

## 3. Daftar Query SQLite yang Perlu Disesuaikan untuk PostgreSQL

All queries use **PDO prepared statements** — good. The following SQLite-specific syntax needs adjustment:

| Area | SQLite Syntax | PostgreSQL Equivalent | Files Affected |
|------|---------------|-----------------------|----------------|
| Auto-increment | `INTEGER PRIMARY KEY AUTOINCREMENT` | `BIGSERIAL PRIMARY KEY` or `GENERATED ALWAYS AS IDENTITY` | Schema migration |
| Current timestamp | `datetime('now')` | `NOW()` or `CURRENT_TIMESTAMP` | `admin/users.php:35`, `admin/settings.php:70` |
| Date arithmetic | `date('now', '-7 days')` | `CURRENT_DATE - INTERVAL '7 days'` | `writer/dashboard.php:14`, `writer/profile.php:20` |
| `CAST(... AS UNSIGNED)` | `CAST(user_id AS UNSIGNED)` | `user_id::bigint` or keep text sort | `admin/users.php:25` |
| `||` concat | Not used (PHP-side) | N/A | — |
| `LIMIT ... OFFSET` | Supported | Supported | All pagination queries |
| `UNION` in subquery | Used in `admin/dashboard.php:25` | Supported | `admin/dashboard.php` |
| `INSERT ... ON CONFLICT` | Not used | Available in PG | Consider for settings upsert |
| `getimagesize()` / GD | PHP function | Same | `helpers.php:55` |
| `password_hash()` / `password_verify()` | PHP native (bcrypt) | Same | `auth.php` |

**No triggers, views, stored procedures, or events** exist in SQLite.

---

## 4. Daftar Fitur Aplikasi

### Public Frontend (`index.php`)
- **Home:** Hero headline article + 4 secondary articles + 4 latest videos
- **Article Detail:** Full content, meta tags (OG, Twitter), related articles (3)
- **Video Detail:** Embed player (YouTube/Vimeo via `embed_url`), related videos (2)
- **Articles List:** Filter by category, search (`q`), pagination
- **Videos List:** Filter by category, search, pagination
- **Exclusive / Wawancara:** Union of articles+videos where `category='Wawancara'`
- **Dark/Light mode:** Cookie-based, no FOUC (inline `<script>` in `<head>`)
- **Responsive:** Tailwind CSS via CDN

### Admin Panel (`admin/`)
| Module | Fitur |
|--------|-------|
| Dashboard | Stats: total articles, videos, categories, headline counts; recent 5 each |
| Articles | CRUD, draft/publish, headline toggle, SEO fields, image upload (5MB, JPG/PNG/WebP/GIF), TinyMCE editor |
| Videos | CRUD, embed URL (YouTube/Vimeo), thumbnail upload, headline, draft, SEO |
| Categories | Read-only aggregation from articles+videos |
| Users | CRUD, role (Admin/Editor/Writer), user_id auto-increment (001, 002...), password hash (bcrypt) |
| Settings | Site name, description, logo, favicon, meta templates, GA ID, OG image default |
| Profile | Change name, password (bcrypt) |
| Login Logs | View all login attempts (admin + writer), filter by portal/username/success, purge |

### Writer Portal (`writer/`)
| Module | Fitur |
|--------|-------|
| Dashboard | Personal stats: article count, headline count, last 7 days, video count, recent 5 |
| Articles | CRUD **own articles only** (author filter), same fields as admin |
| Videos | CRUD **own videos only**, same fields as admin |
| Profile | Change name, password |

### Auth & Session
- **File-based sessions** in `sessions/` (shared across portals)
- **1-year lifetime**, `SameSite=Lax`, `HttpOnly`, `Secure=false` (dev)
- **CSRF tokens** per portal (`csrf_token` / `writer_csrf_token`)
- **Password hashing:** `password_hash()` bcrypt (cost 10 default)
- **Legacy fallback** in writer: hardcoded `penulis` user (bcrypt hash)

### Upload & Storage
- **Images only** (no video file upload — videos are embed URLs)
- Saved to `admin/uploads/` with random hex + timestamp filename
- `getimagesize()` validation, MIME whitelist, 5MB limit
- Relative paths stored in DB: `admin/uploads/xxx.jpg`
- **No Supabase Storage / external CDN yet**

---

## 5. Kebutuhan PHP Extensions (Current)

| Extension | Used By | Required for Vercel? |
|-----------|---------|----------------------|
| `pdo_sqlite` | All DB connections | **Yes** (or swap to `pdo_pgsql`) |
| `pdo` | Base PDO | Yes |
| `session` | File sessions | Yes (but needs external store on Vercel) |
| `mbstring` | `slugify` (iconv/strtolower) | Yes |
| `iconv` | `slugify` transliteration | Yes |
| `gd` / `getimagesize` | Image validation in upload | Yes (for upload validation) |
| `json` | Not directly used (PHP native) | Yes |
| `openssl` | `random_bytes` (CSRF, filenames) | Yes |
| `filter` | Input validation | Yes |
| `ctype` | String checks | Yes |
| `dom`, `libxml`, `xml`, `xmlreader`, `xmlwriter` | Not directly used | Optional |
| `curl`, `ftp`, `gettext`, `bcmath`, `bz2`, `calendar`, `exif`, `fileinfo`, `phar`, `readline`, `shmop`, `sockets`, `sysvmsg`, `sysvsem`, `sysvshm`, `tokenizer`, `zlib` | Not used | Optional |

**PHP Version:** 8.2.12 (Vercel supports 8.1+)

**Missing for Supabase/PostgreSQL:** `pdo_pgsql`, `pgsql`

---

## 6. File yang Membutuhkan Penyesuaian (Migration to Supabase + Vercel)

| File | Perubahan Diperlukan |
|------|---------------------|
| `index.php` | DB connection → Supabase (PostgreSQL via `pdo_pgsql`); session → external (Redis/DB); remove `database.sqlite` path |
| `admin/includes/db.php` | Same — DSN change, add SSL mode for Supabase |
| `writer/includes/writer_db.php` | Same |
| `admin/includes/auth.php` | Session save path → cannot use local `sessions/` on Vercel (ephemeral FS); use DB/Redis session handler |
| `writer/includes/writer_auth.php` | Same |
| `admin/includes/helpers.php` | Upload → Supabase Storage (or external); `getimagesize` works but file is temporary |
| `writer/includes/writer_helpers.php` | Same upload logic |
| `admin/articles.php`, `admin/videos.php`, `admin/settings.php` | Upload handling rewrite |
| `writer/articles.php`, `writer/videos.php` | Upload handling rewrite |
| `login_log.php` | DB connection |
| **Schema** | Full DDL rewrite for PostgreSQL (types, serial, indexes, FKs) |
| `vercel.json` | **New** — Vercel PHP runtime config, routes, env vars |
| `Dockerfile` | **New** — if using Docker on Vercel (PHP 8.2 + extensions) |
| `.env.example` | **New** — SUPABASE_URL, SUPABASE_KEY, DB_DSN, SESSION_SECRET, etc. |
| `composer.json` | **New** — if adding dependencies (e.g., `vlucas/phpdotenv`, session handler) |

---

## 7. Risiko Migrasi

| Risiko | Severity | Mitigasi |
|--------|----------|----------|
| **SQLite → PostgreSQL type mismatch** (no FK, loose typing) | High | Explicit DDL with FKs, `NOT NULL`, proper types; test all queries |
| **Session persistence on Vercel** (ephemeral filesystem) | High | Use Supabase (PostgreSQL) session handler or Redis (Upstash) |
| **File uploads on Vercel** (no persistent disk) | High | Migrate to Supabase Storage / S3 / Cloudinary; rewrite `uploadImage()` |
| **TinyMCE assets (16 MB)** | Medium | Serve via CDN (Tiny Cloud) or self-host on Vercel static; remove from repo |
| **Date function differences** (`date('now', '-7 days')`) | Medium | Abstract to helper function; use `DateTime`/`DateInterval` in PHP |
| **Auto-increment / sequence** | Medium | Use `BIGSERIAL` / `GENERATED AS IDENTITY` |
| **`CAST(... AS UNSIGNED)` for user_id sorting** | Low | Store `user_id` as integer or zero-pad text; sort in PHP |
| **No FK constraints currently** | Low | Add FKs in PG schema; verify referential integrity on import |
| **Hardcoded legacy writer fallback** | Low | Remove `$WRITERS` array; rely solely on `users` table |
| **Vercel 50MB bundle limit** | Medium | Exclude `assets/`, `sessions/`, `database.sqlite`, `uploads/` via `.vercelignore` |
| **No environment config management** | Medium | Add `vlucas/phpdotenv` or native `$_ENV` with `.env` |

---

## 8. Rekomendasi Teknis

1. **Database Migration Strategy**
   - Use a migration tool (e.g., `phinx`, custom PHP script) to create PG schema
   - Export SQLite data → CSV → `COPY` into PG, or scripted row-by-row with type casting
   - Verify row counts per table post-migration

2. **Session Handling on Vercel**
   - Implement custom `SessionHandlerInterface` storing in PostgreSQL (`sessions` table) or Upstash Redis
   - Set `session.save_handler = user` in `php.ini` or `ini_set()`

3. **File Storage**
   - Supabase Storage (S3-compatible) — native integration, signed URLs
   - Rewrite `uploadImage()` to use Supabase PHP SDK or S3 SDK
   - Keep local upload only for dev (detect `VERCEL` env)

4. **Vercel Deployment**
   - Use `vercel-php` runtime (community) or Docker with `php:8.2-fpm-alpine`
   - `vercel.json` routes all to `index.php` (front controller)
   - Set `FUNCTIONS_PAYLOAD_LIMIT` if uploads via API

5. **TinyMCE**
   - Switch to Tiny Cloud CDN (free tier) or self-host minimal build
   - Remove `assets/` from repo (already in `.gitignore`)

6. **Environment Config**
   - Add `vlucas/phpdotenv` via Composer (first dependency)
   - `.env` for local; Vercel Project Settings for production

7. **Testing**
   - Run full test suite (if any) against PG locally via Docker
   - Manual smoke test: login (admin/writer), CRUD article/video, upload image, logout

---

## 9. Urutan Pengerjaan Tahap Berikutnya (Plan 02–05)

| Plan | Fokus | Output |
|------|-------|--------|
| **02** | Database Migration | `supabase/schema.sql`, migration script, seed data, verified PG instance |
| **03** | Code Adaptation | All DB connection files updated, session handler rewritten, upload → Supabase Storage |
| **04** | Vercel Deployment Config | `vercel.json`, `Dockerfile`, `.env.example`, CI/CD pipeline, preview deployments |
| **05** | Testing & Go-Live | E2E tests, performance check, custom domain, monitoring, rollback plan |

---

**Catatan Penting:** Plan 01 asumsikan MySQL, tapi project ini **SQLite**. Seluruh rencana migrasi (Plan 02+) harus disesuaikan dari **SQLite → PostgreSQL (Supabase)**, bukan MySQL → PostgreSQL. Perbedaan utama: SQLite typeless, no FK, `AUTOINCREMENT` vs `BIGSERIAL`, fungsi tanggal berbeda.

**Next Step:** Tunggu instruksi untuk memulai Plan 02.
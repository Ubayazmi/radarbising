# Radar Bising — GitHub Actions Secrets Setup

> **PENTING:** Jangan pernah menuliskan nilai asli secret di file ini, chat, atau commit.
> File ini hanya berisi daftar nama secret + cara mendapatkannya.

Buka: **https://github.com/Ubayazmi/radarbising → Settings → Secrets and variables → Actions → New repository secret**

## 1. Supabase (wajib)

| Secret | Cara mendapat | Keterangan |
|--------|--------------|------------|
| `SUPABASE_URL` | Dashboard → Project → Settings → API → Project URL | format `https://<ref>.supabase.co` |
| `SUPABASE_SERVICE_KEY` | Dashboard → Settings → API → service_role (secret) | khusus server-side / migration |
| `DB_DSN` | Dashboard → Settings → Database → Connection string (Direct, port 5432) | format di bawah |

Format `DB_DSN` (PHP PDO):
```
pgsql:host=db.<ref>.supabase.co;port=5432;dbname=postgres;user=postgres;password=***;sslmode=require
```

> ⚠️ **Langkah tambahan sebelum migrasi:** upload `database.sqlite` ke bucket **private** bernama `migration-source` di Supabase Storage (Dashboard → Storage → New bucket, pilih *Private*). Workflow migration mendownload file ini otomatis — karena `database.sqlite` di-gitignore, tidak ikut checkout.

## 2. Vercel (wajib untuk deploy)

| Secret | Cara mendapat | Keterangan |
|--------|--------------|------------|
| `VERCEL_TOKEN` | Vercel → Account Settings → Tokens → New Token (Full Account) | prefix `vercel_...` |
| `VERCEL_ORG_ID` | Vercel → Account Settings (tampil di URL) | `org_...` |
| `VERCEL_PROJECT_ID` | Project → Settings (tampil di URL) | `prj_...` |

## 3. Environment Variables di Vercel (untuk aplikasi, bukan GitHub)

Di **Vercel Dashboard → Project → Settings → Environment Variables**, set untuk semua environments (Production + Preview):

| Var | Nilai |
|-----|-------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `SUPABASE_URL` | Project URL |
| `SUPABASE_ANON_KEY` | anon public key (aman untuk client) |
| `SUPABASE_SERVICE_KEY` | service_role key (TIDAK untuk browser) |
| `DB_DSN` | **Transaction Pooler** (port 6543) untuk produksi: `pgsql:host=aws-0-<region>.pooler.supabase.com;port=6543;dbname=postgres;user=postgres.<ref>;password=***;sslmode=require` |
| `SESSION_SECRET` | hasil `php -r "echo bin2hex(random_bytes(32));"` |
| `UPLOAD_DRIVER` | `supabase` |
| `SUPABASE_STORAGE_BUCKET` | `radarbising-uploads` |

## 4. Cara menjalankan migration

1. Siapkan semua secret di atas di GitHub.
2. Upload `database.sqlite` ke bucket private `migration-source`.
3. Buka **Actions → Supabase Migration → Run workflow** → branch `main`.
4. Centang **Dry run** dulu untuk validasi tanpa menulis data.
5. Setelah dry run OK, jalankan lagi tanpa centang untuk eksekusi live.

> Catatan: `supabase/schema.sql` menggunakan `CREATE TABLE IF NOT EXISTS`, jadi aman dijalankan berulang. Data menggunakan `ON CONFLICT (id) DO NOTHING`, jadi migrasi idempoten.

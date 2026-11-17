# Database Migration Report — Radar Bising

> **Generated:** 2026-11-17  
> **Architecture:** PostgreSQL-first with Supabase CLI migration system  
> **Source of Truth:** `supabase/migrations/`

---

## Executive Summary

This report documents the migration of Radar Bising from a **SQLite-based** application to a **PostgreSQL-first architecture on Supabase**, with schema managed via **versioned migration files** and deployed through **GitHub Actions**.

### Key Decisions

| Decision | Rationale |
|----------|-----------|
| SQLite → Supabase PostgreSQL | Vercel serverless requires external DB; Supabase provides Postgres + Storage + Auth |
| Supabase CLI migrations (`supabase/migrations/`) | Single source of truth, trackable history, idempotent deployments |
| `supabase db push` for deployment | Applies only pending migrations, records in `schema_migrations` table |
| Separate schema vs data workflows | Schema = recurring/versioned; Data = one-time SQLite → PG migration |
| GitHub Actions as deployment engine | No local credentials needed; secrets in GitHub; audit trail |

---

## Current State (as of 2026-11-17)

### Local Files

| Path | Purpose | Status |
|------|---------|--------|
| `supabase/migrations/20261117000000_init.sql` | Initial schema migration (6 tables, enums, indexes, triggers, RLS placeholders, initial data) | ✅ Created |
| `supabase/config.toml` | Supabase CLI project config | ✅ Created |
| `supabase/schema.sql` | Legacy reference (deprecated, points to migrations) | ✅ Updated with deprecation notice |
| `migrate.php` | One-time SQLite data migration script | ✅ Preserved (unchanged) |

### GitHub Actions Workflows

| Workflow | Trigger | Purpose |
|----------|---------|---------|
| `supabase-migrations.yml` | Push to `main` (paths: `supabase/migrations/**`, `supabase/config.toml`) + manual `workflow_dispatch` | **Recurring schema deployment** — dry-run by default; apply requires explicit `apply=true` |
| `migrate-sqlite-data.yml` | Manual `workflow_dispatch` only | **One-time data migration** — downloads SQLite from Supabase Storage, runs `migrate.php`, verifies counts |
| `deploy.yml` | Push to `main` + manual | Vercel deployment (unchanged) |

### Migration History — Local

| Version | File | Description | Local Applied |
|---------|------|-------------|---------------|
| `20261117000000` | `init.sql` | Initial schema: users, articles, videos, login_logs, settings, sessions + enums, indexes, triggers, RLS placeholders, settings row | ✅ In migrations/ |

### Migration History — Remote (Supabase)

> ⚠️ **UNVERIFIED** — Remote migration history has NOT been checked because:
> - No Supabase credentials configured locally (secrets are in GitHub, not local `.env`)
> - Supabase CLI not installed in this environment
> - Migration is designed to be applied via CI workflow

**Next step:** Run the `supabase-migrations.yml` workflow with `dry_run=true` to:
1. Link to the Supabase project
2. Query `supabase_migrations.schema_migrations` table
3. Report which migrations are already applied vs pending

**Expected scenarios:**
| Scenario | What to do |
|----------|------------|
| Remote `schema_migrations` empty | `init.sql` is pending → apply with `apply=true` |
| Remote has `20261117000000` | All local applied → no action needed |
| Remote has different/extra versions | Investigate drift before proceeding |

---

## Schema Overview (from `init.sql`)

### Tables (6)

| Table | PK | Key Columns | Indexes | Notes |
|-------|----|-------------|---------|-------|
| `users` | `id` (BIGSERIAL) | `user_id` (UNIQUE), `full_name`, `role` (enum), `password_hash` | `role`, `user_id` | Auth source |
| `articles` | `id` (BIGSERIAL) | `slug` (UNIQUE), `category`, `author`, `published_at`, `is_headline`, `is_draft` | 5 indexes (category, author, headline, slug, published_at) | Core content |
| `videos` | `id` (BIGSERIAL) | `slug` (UNIQUE), `category`, `embed_url`, `published_at`, `is_headline`, `is_draft` | 5 indexes | Core content |
| `login_logs` | `id` (BIGSERIAL) | `portal` (enum), `username`, `success` | 3 indexes (portal+date, username, success) | Audit trail |
| `settings` | `id` (BIGSERIAL) | Single-row enforced by `UNIQUE INDEX ON settings((true))` | Single-row constraint | Site config |
| `sessions` | `id` (CHAR(64)) | `data` (BYTEA), `expires_at` | `expires_at` | PHP session handler (Plan 03) |

### Enums (2)

| Enum | Values |
|------|--------|
| `user_role` | `Admin`, `Editor`, `Writer` |
| `portal_type` | `admin`, `writer` |

### Triggers (3)

| Trigger | Table | Function |
|---------|-------|----------|
| `trigger_articles_updated_at` | `articles` | `update_updated_at_column()` |
| `trigger_videos_updated_at` | `videos` | `update_updated_at_column()` |
| `trigger_settings_updated_at` | `settings` | `update_updated_at_column()` |

### Extensions (2)

| Extension | Purpose |
|-----------|---------|
| `uuid-ossp` | UUID generation (`uuid_generate_v4()`) |
| `pgcrypto` | Cryptographic functions (`gen_random_bytes()`, etc.) |

### RLS (Row Level Security)

All tables have **RLS DISABLED** by default (commented out in migration). The app uses the **service role key** for full access. Enable RLS only if per-row policies are needed (see `SUPABASE_MIGRATION_GUIDE.md`).

---

## Data Migration — SQLite → PostgreSQL

### Script: `migrate.php`
- **Purpose:** One-time bulk copy of SQLite data to PostgreSQL
- **Mode:** `--dry-run` (validate) → live (write)
- **Batch size:** 100 rows (configurable)
- **Order:** `users` → `articles` → `videos` → `login_logs` → `settings` (dependency order)
- **Type casting:** SQLite strings → PG `TIMESTAMPTZ`, `BOOLEAN`, `BIGINT`
- **Idempotency:** `INSERT ... ON CONFLICT (id) DO NOTHING`
- **Sequence reset:** `setval(pg_get_serial_sequence(...), MAX(id))` after each table

### Source Data (SQLite)

| Table | Row Count |
|-------|-----------|
| `users` | 3 |
| `articles` | 9 |
| `videos` | 5 |
| `login_logs` | 15 |
| `settings` | 1 |
| **Total** | **33** |

> Counts verified from local `database.sqlite` (57,344 bytes). Backup `database.sqlite.bak` exists.

### Prerequisites for Data Migration

1. **Schema must exist** — run `supabase-migrations.yml` first
2. **SQLite source in Supabase Storage** — upload `database.sqlite` to **private** bucket `migration-source`
3. **Secrets configured** — see `SECRETS_SETUP.md`

---

## GitHub Actions — Required Secrets

### For Schema Migrations (`supabase-migrations.yml`)
| Secret | Source |
|--------|--------|
| `SUPABASE_ACCESS_TOKEN` | Supabase Dashboard → Settings → Access Tokens (scopes: database, project) |
| `SUPABASE_PROJECT_ID` | Supabase Dashboard → Settings → General → Project ID (e.g., `abcdefghijklmnop`) |
| `DB_DSN` | Supabase Dashboard → Settings → Database → Connection String (Direct, port 5432) |

### For Data Migration (`migrate-sqlite-data.yml`)
| Secret | Source |
|--------|--------|
| `SUPABASE_URL` | Dashboard → Settings → API → Project URL |
| `SUPABASE_SERVICE_KEY` | Dashboard → Settings → API → service_role secret |
| `DB_DSN` | Same as above |

### For Vercel Deploy (`deploy.yml`)
| Secret | Source |
|--------|--------|
| `VERCEL_TOKEN` | Vercel Dashboard → Settings → Account Tokens |
| `VERCEL_ORG_ID` | Vercel Dashboard → Settings → General |
| `VERCEL_PROJECT_ID` | Vercel Project → Settings → General |

---

## Security Controls Implemented

| Control | Implementation |
|---------|----------------|
| No double-apply | `supabase db push` checks `schema_migrations` table |
| Destructive change gate | Workflow scans pending migrations for `DROP`, `TRUNCATE`, `DELETE`, `ALTER ... DROP`, `REVOKE`; blocks `apply=true` unless explicitly re-confirmed |
| Dry-run default | Both workflows default to `dry_run=true`; production apply requires manual action |
| Environment protection | Workflows use `environment: production` (supports GitHub required reviewers) |
| No credentials in code | All secrets via GitHub Secrets / Vercel Env Vars |
| SQLite cleanup | Runner deletes `database.sqlite` after data migration |

---

## Pending Actions (Next Steps)

| # | Action | Owner | Status |
|---|--------|-------|--------|
| 1 | Configure 6 GitHub Secrets (see `SECRETS_SETUP.md`) | User | ⏳ |
| 2 | Create Supabase bucket `migration-source` (private), upload `database.sqlite` | User | ⏳ |
| 3 | Run `supabase-migrations.yml` with `dry_run=true` → verify pending migrations | User | ⏳ |
| 4 | If `init.sql` pending, run `supabase-migrations.yml` with `dry_run=false, apply=true` | User | ⏳ |
| 5 | Run `migrate-sqlite-data.yml` with `dry_run=true` → verify counts | User | ⏳ |
| 6 | Run `migrate-sqlite-data.yml` with `dry_run=false` → live data migration | User | ⏳ |
| 7 | Verify `sessions` table empty (pre-Plan 03) | User | ⏳ |
| 8 | Begin Plan 03: PostgreSQL-backed session handler, code adaptation | Agent | ⏳ |

---

## Verification Checklist (Post-Migration)

After live data migration completes:

- [ ] **Row counts match** for all 5 data tables (`users`, `articles`, `videos`, `login_logs`, `settings`)
- [ ] **Sequences aligned** — `SELECT last_value FROM table_id_seq` ≥ max ID
- [ ] **`sessions` table empty** — ready for Plan 03 session handler
- [ ] **Storage bucket `radarbising-uploads` exists** (public)
- [ ] **App connects via `DB_DSN` (pooler)** — test on Vercel preview
- [ ] **Auth works** — login with migrated users (bcrypt passwords preserved)

---

## Risks & Mitigations

| Risk | Mitigation |
|------|------------|
| Schema already applied on remote (different version) | Workflow reports exact pending vs applied; manual review before apply |
| Destructive migration accidentally applied | Dry-run + destructive scan + explicit `apply=true` gate |
| SQLite source missing from Storage | Workflow fails fast with clear error; bucket creation documented |
| Sequence drift after data migration | `migrate.php` resets sequences; verified in post-migration step |
| Vercel pooler connection limits | `DB_DSN` uses transaction pooler (port 6543); local uses direct (port 5432) |

---

## Files Changed in This Migration Prep

| File | Change |
|------|--------|
| `supabase/migrations/20261117000000_init.sql` | **Created** — initial schema migration (source of truth) |
| `supabase/config.toml` | **Created** — Supabase CLI config |
| `supabase/schema.sql` | **Updated** — deprecated notice, retained as reference |
| `.github/workflows/supabase-migrations.yml` | **Created** — recurring schema deployment |
| `.github/workflows/migrate-sqlite-data.yml` | **Created** — one-time data migration |
| `.github/workflows/migrate.yml` | **Deleted** — replaced by the two above |
| `docs/SUPABASE_MIGRATION_GUIDE.md` | **Created** — how to add/manage migrations |
| `docs/DATABASE_MIGRATION_REPORT.md` | **Created** — this report |
| `docs/MIGRATION_WORKLIST.md` | **Updated** — reflects new architecture |

---

*Report generated by Hermes Agent — reflects local state only. Remote Supabase state unverified until workflow execution.*
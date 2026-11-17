# Radar Bising — Automated Migration & Deployment (GitHub Actions)

## Architecture

**PostgreSQL-first:** Supabase is the main database. SQLite is only the one-time
data source for the initial migration. All schema changes live in
versioned migration files under `supabase/migrations/`.

```
supabase/
├── config.toml                              # Supabase CLI project config
└── migrations/
    └── 20261117000000_init.sql              # initial schema (source of truth)
migrate.php                                  # one-time SQLite data migration
.github/workflows/
├── supabase-migrations.yml                  # recurring schema deployment
├── migrate-sqlite-data.yml                  # one-time data migration
└── deploy.yml                               # Vercel deployment
```

## Migration Types

| Type | Tool | Workflow |
|------|------|----------|
| **Schema** (recurring) | Supabase CLI (`supabase db push`) | `supabase-migrations.yml` |
| **Data** (one-time) | `migrate.php` (PHP CLI) | `migrate-sqlite-data.yml` |

Schema changes: add a NEW file in `supabase/migrations/` (never edit applied files).
See `docs/SUPABASE_MIGRATION_GUIDE.md` for the full procedure.

## Schema Migration Workflow (`supabase-migrations.yml`)

**Trigger:** push to `main` (paths: `supabase/migrations/**`, `supabase/config.toml`) + manual.

1. Setup: Node.js + Supabase CLI, login via token, link project.
2. **Check remote history** — query `supabase_migrations.schema_migrations` (no double-apply: `supabase db push` only applies unapplied versions).
3. **Detect pending** migrations (local files not in remote history).
4. **Destructive scan** — grep pending migrations for `DROP`, `TRUNCATE`, `DELETE`, `ALTER ... DROP`, `REVOKE`.
5. **Dry run (default):** report pending + destructive ops, write nothing.
6. **Apply** (only with `dry_run=false` AND `apply=true`): `supabase db push`, then re-verify history.
7. **Safety gate:** if destructive ops detected, the run requires explicit re-confirmation.

## Data Migration Workflow (`migrate-sqlite-data.yml`)

**Trigger:** manual only (`workflow_dispatch`).

1. Prerequisite: all 6 tables must exist in PostgreSQL (fails fast if schema not applied — run schema workflow first).
2. Download `database.sqlite` from **private** Supabase Storage bucket `migration-source`.
3. Verify source counts (fail if empty).
4. `php migrate.php` — `--dry-run` (default) or live.
5. Verify: per-table source vs target counts, `sessions` table must be empty.
6. Create public bucket `radarbising-uploads`.
7. Remove SQLite from runner (security).

> `migrate.php` is **one-time** — it never applies schema. Sequence reset included.

## Secrets & Env Vars

Full guide: **`SECRETS_SETUP.md`**.

### GitHub Secrets (Settings → Secrets → Actions)
```
SUPABASE_ACCESS_TOKEN, SUPABASE_PROJECT_ID, DB_DSN       (schema workflow)
SUPABASE_URL, SUPABASE_SERVICE_KEY, DB_DSN               (data workflow)
VERCEL_TOKEN, VERCEL_ORG_ID, VERCEL_PROJECT_ID           (deploy workflow)
```

### Vercel Environment Variables (app runtime)
```
APP_ENV, APP_DEBUG, SUPABASE_URL, SUPABASE_ANON_KEY,
SUPABASE_SERVICE_KEY, DB_DSN (pooler 6543), SESSION_SECRET,
UPLOAD_DRIVER, SUPABASE_STORAGE_BUCKET
```

## Security

- `.env`, `database.sqlite`, `vendor/` — gitignored, never committed.
- No double-apply: Supabase records versions in `schema_migrations`.
- Destructive-change gate in schema workflow.
- Dry-run defaults; production apply is manual + explicit.
- SSL on all DB connections; SQLite removed from runners after use.
- `database.sqlite` + `database.sqlite.bak` retained locally, never deleted.

## Execution Order (production)

1. Set GitHub secrets + upload `database.sqlite` to bucket `migration-source` (private).
2. `supabase-migrations.yml` → dry run → then `apply=true`.
3. `migrate-sqlite-data.yml` → dry run → then live.
4. Verify (row counts, sequences, empty `sessions`).
5. Plan 03: app code adaptation (session handler, storage, queries).
6. `deploy.yml` → Vercel preview → smoke test → production.

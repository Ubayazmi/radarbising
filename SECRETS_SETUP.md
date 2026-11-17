# =============================================================================
# Radar Bising — GitHub Actions Secrets Setup Guide
# =============================================================================
# Go to: https://github.com/Ubayazmi/radarbising → Settings → Secrets and variables → Actions → New repository secret
# =============================================================================

# --- Required for Migration Workflow (.github/workflows/migrate.yml) ---

# 1. SUPABASE_ACCESS_TOKEN
#    Source: Supabase Dashboard → Settings → Access Tokens → Generate token
#    Scopes: database, storage
SUPABASE_ACCESS_TOKEN=sbp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

# 2. SUPABASE_PROJECT_ID
#    Source: Supabase Dashboard → Settings → General → Project ID
SUPABASE_PROJECT_ID=xxxxxxxxxxxxxxxxxxxx

# 3. DB_DSN
#    Source: Supabase Dashboard → Settings → Database → Connection String
#    Use "Transaction Pooler" (port 6543) for serverless/Vercel Functions
#    Use "Direct Connection" (port 5432) for migration scripts
#    Format: pgsql:host=db.<ref>.supabase.co;port=5432;dbname=postgres;user=postgres;password=***;sslmode=require
DB_DSN=pgsql:host=db.xxxxx.supabase.co;port=5432;dbname=postgres;user=postgres;password=***;sslmode=require

# --- Required for Deploy Workflow (.github/workflows/deploy.yml) ---

# 4. VERCEL_TOKEN
#    Source: Vercel Dashboard → Settings → Account Tokens → Create Token
#    Scope: Full Account
VERCEL_TOKEN=vercel_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

# 5. VERCEL_ORG_ID
#    Source: Vercel Dashboard → Settings → General → Organization ID
VERCEL_ORG_ID=xxxxxxxxxxxxxxxxxxxx

# 6. VERCEL_PROJECT_ID
#    Source: Vercel Dashboard → Settings → General → Project ID
VERCEL_PROJECT_ID=xxxxxxxxxxxxxxxxxxxx

# --- Optional (for Plan 03+) ---

# SESSION_SECRET (generate: php -r "echo bin2hex(random_bytes(32)).PHP_EOL;")
SESSION_SECRET=***

# SUPABASE_URL (https://xxxxx.supabase.co)
SUPABASE_URL=https://xxxxx.supabase.co

# SUPABASE_ANON_KEY (Settings → API → anon public)
SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...

# SUPABASE_SERVICE_KEY (Settings → API → service_role secret)
SUPABASE_SERVICE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...

# UPLOAD_DRIVER
UPLOAD_DRIVER=supabase

# SUPABASE_STORAGE_BUCKET
SUPABASE_STORAGE_BUCKET=radarbising-uploads
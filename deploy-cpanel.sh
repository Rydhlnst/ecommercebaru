#!/usr/bin/env bash

set -Eeuo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT_DIR"

log() { printf '\n==> %s\n' "$1"; }
fail() { printf '\nERROR: %s\n' "$1" >&2; exit 1; }

command -v php >/dev/null 2>&1 || fail "PHP CLI is not available."
command -v composer >/dev/null 2>&1 || fail "Composer is not available."

if [ ! -f .env ]; then
    fail ".env is missing. Create it from your cPanel environment before deploying."
fi

log "Pulling the latest code"
git pull --ff-only

log "Installing production PHP dependencies"
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

log "Preparing writable directories"
mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    public/uploads

chmod -R ug+rwX storage bootstrap/cache public/uploads 2>/dev/null || true

log "Running database migrations"
php artisan migrate --force

log "Refreshing public storage"
php artisan storage:link --force 2>/dev/null || true
php artisan storage:sync

log "Clearing and rebuilding Laravel caches"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan responsecache:clear 2>/dev/null || true

log "Refreshing the installed marker"
touch storage/installed

log "cPanel deployment completed"

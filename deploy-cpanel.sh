#!/usr/bin/env bash

set -Eeuo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT_DIR"

log() { printf '\n==> %s\n' "$1"; }
fail() { printf '\nERROR: %s\n' "$1" >&2; exit 1; }
warn() { printf '\nWARNING: %s\n' "$1" >&2; }

PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
MAINTENANCE_ENABLED=false

on_error() {
    if [ "$MAINTENANCE_ENABLED" = true ]; then
        warn "Deployment failed. The application remains in maintenance mode."
    fi
}

trap on_error ERR

resolve_command() {
    if [[ "$1" == */* ]]; then
        [ -f "$1" ] || fail "Command not found: $1"

        printf '%s\n' "$1"

        return
    fi

    command -v "$1" || fail "Command not found: $1"
}

require_extension() {
    "$PHP_BIN" -m | tr '[:upper:]' '[:lower:]' | grep -qx "$1" \
        || fail "Required PHP extension is missing: $1"
}

PHP_BIN="$(resolve_command "$PHP_BIN")"
COMPOSER_BIN="$(resolve_command "$COMPOSER_BIN")"

[ "$("$PHP_BIN" -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')" = '8.3' ] \
    || fail "PHP 8.3 is required. Configure PHP_BIN with the cPanel EA PHP 8.3 binary."

for extension in calendar curl fileinfo gd intl mbstring openssl pdo pdo_mysql tokenizer xml zip; do
    require_extension "$extension"
done

if [ ! -f .env ]; then
    fail ".env is missing. Create it from your cPanel environment before deploying."
fi

[ -f public/index.php ] || fail "public/index.php is missing. Point the cPanel domain document root to $ROOT_DIR/public."
[ -f public/themes/shop/default/build/manifest.json ] || fail "Shop asset manifest is missing. Build assets before deployment."
[ -f public/themes/admin/default/build/manifest.json ] || fail "Admin asset manifest is missing. Build assets before deployment."

grep -qx 'APP_ENV=production' .env || fail "APP_ENV must be production."
grep -qx 'APP_DEBUG=false' .env || fail "APP_DEBUG must be false."
grep -qx 'CACHE_STORE=file' .env || fail "CACHE_STORE must be file for shared cPanel hosting."
grep -qx 'SESSION_DRIVER=database' .env || fail "SESSION_DRIVER must be database."
grep -qx 'QUEUE_CONNECTION=database' .env || fail "QUEUE_CONNECTION must be database."

if [ -n "$(git status --porcelain)" ]; then
    fail "Git worktree is not clean. Commit, stash, or remove server-side changes before deploying."
fi

log "Pulling the latest code"
git pull --ff-only

log "Installing production PHP dependencies"
"$PHP_BIN" "$COMPOSER_BIN" install --no-dev --prefer-dist --optimize-autoloader --no-interaction
"$PHP_BIN" "$COMPOSER_BIN" check-platform-reqs --no-dev --no-interaction

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

log "Enabling maintenance mode"
"$PHP_BIN" artisan down --retry=60
MAINTENANCE_ENABLED=true

log "Running database migrations"
"$PHP_BIN" artisan migrate --force

log "Seeding policy CMS pages"
"$PHP_BIN" artisan db:seed --class='Database\Seeders\PolicyPageSeeder' --force

log "Refreshing public storage"
"$PHP_BIN" artisan storage:link --force 2>/dev/null || log "storage:link is unavailable; using copied public storage."
"$PHP_BIN" artisan storage:sync

log "Clearing and rebuilding Laravel caches"
"$PHP_BIN" artisan optimize:clear
"$PHP_BIN" artisan responsecache:clear 2>/dev/null || true
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache

log "Refreshing the installed marker"
touch storage/installed

log "Disabling maintenance mode"
"$PHP_BIN" artisan up
MAINTENANCE_ENABLED=false

cat <<EOF

Configure these cPanel cron jobs with the same PHP_BIN value:
* * * * * $PHP_BIN $ROOT_DIR/artisan schedule:run >> /dev/null 2>&1
* * * * * cd $ROOT_DIR && $PHP_BIN artisan queue:work database --stop-when-empty --max-time=50 --max-jobs=50 --tries=3 --backoff=10 >> storage/logs/queue.log 2>&1
EOF

log "cPanel deployment completed"

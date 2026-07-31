#!/bin/bash
set -e

cd /var/www/html

# Wait for MySQL via TCP (no auth needed)
if [ -n "$DB_HOST" ]; then
  echo "Waiting for MySQL at $DB_HOST:${DB_PORT:-3306}..."
  for i in $(seq 1 60); do
    if (echo > /dev/tcp/$DB_HOST/${DB_PORT:-3306}) 2>/dev/null; then
      echo "MySQL is reachable."
      break
    fi
    sleep 2
  done
fi

# Ensure storage & cache dirs exist (needed because named volume may start empty)
mkdir -p \
  storage/framework/sessions \
  storage/framework/views \
  storage/framework/cache/data \
  storage/logs \
  storage/app/public \
  bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Generate APP_KEY if empty
if ! grep -qE '^APP_KEY=base64:' .env 2>/dev/null; then
  echo "Generating APP_KEY..."
  php artisan key:generate --force || true
fi

# Sync built assets from image into the app_public volume.
# The named volume persists between deploys, so without this sync
# a rebuilt image's new CSS/JS would never reach the volume.
if [ -d /var/www/html/.public-snapshot ]; then
    echo "Syncing public assets from image snapshot..."
    cp -a /var/www/html/.public-snapshot/. /var/www/html/public/
fi

# Clear stale bootstrap cache that may reference dev-only classes
# (image is built with --no-dev; cached packages.php may still list dev providers)
rm -f bootstrap/cache/packages.php bootstrap/cache/services.php bootstrap/cache/config.php

# Storage symlink
[ ! -L public/storage ] && php artisan storage:link || true

# Detect first run before touching the installed flag
FIRST_RUN=false
[ ! -f storage/installed ] && FIRST_RUN=true

# Migrate (idempotent)
php artisan migrate --force || echo "Migration skipped/failed"

# Seed on first run
if [ "$FIRST_RUN" = "true" ]; then
  echo "First run: seeding database with demo data..."
  php artisan db:seed --force || echo "Seed skipped/failed"
  echo "Building product indices..."
  php artisan indexer:index --mode=full || echo "Indexer skipped/failed"
fi

# Mark as installed (skip Bagisto installer redirect)
touch storage/installed
chown www-data:www-data storage/installed 2>/dev/null || true

# Clear stale compiled views and response cache before re-caching
php artisan view:clear || true
php artisan responsecache:clear || true

# Cache config for prod (non-fatal)
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Starting: $@"
exec "$@"

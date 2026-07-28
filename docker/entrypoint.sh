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

# Storage symlink
[ ! -L public/storage ] && php artisan storage:link || true

# Migrate (idempotent)
php artisan migrate --force || echo "Migration skipped/failed"

# Cache config for prod (non-fatal)
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Starting: $@"
exec "$@"

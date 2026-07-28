#!/bin/bash
set -e

cd /var/www/html

# Wait for MySQL
if [ -n "$DB_HOST" ]; then
  echo "Waiting for MySQL at $DB_HOST:${DB_PORT:-3306}..."
  until mysqladmin ping -h "$DB_HOST" -P "${DB_PORT:-3306}" -u "$DB_USERNAME" -p"$DB_PASSWORD" --silent 2>/dev/null; do
    sleep 2
  done
  echo "MySQL is ready."
fi

# Ensure storage & cache dirs writable
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Generate APP_KEY if empty
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
  echo "Generating APP_KEY..."
  php artisan key:generate --force
fi

# Storage symlink
[ ! -L public/storage ] && php artisan storage:link || true

# Migrate on first boot (safe: idempotent)
php artisan migrate --force || true

# Cache config for prod
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

exec "$@"

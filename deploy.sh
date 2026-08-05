#!/bin/bash
set -e

echo "==> Pulling latest code..."
git pull

echo "==> Rebuilding Docker image..."
docker compose build --no-cache app

echo "==> Restarting containers..."
docker compose up -d

echo "==> Waiting for MySQL to be healthy..."
until docker compose exec mysql mysqladmin ping -h localhost --silent 2>/dev/null; do
  sleep 2
done
echo "    MySQL is ready."

echo "==> Running migrations..."
docker compose exec app php artisan migrate --force

echo "==> Seeding database..."
docker compose exec app php artisan db:seed --force || echo "    Seed skipped (already seeded or failed)"

echo "==> Rebuilding search indices..."
docker compose exec app php artisan indexer:index --mode=full || echo "    Indexer skipped"

echo "==> Clearing caches..."
docker compose exec app php artisan view:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

echo "==> Health check..."
docker compose ps

echo ""
echo "==> Deploy complete. Site should be live."

#!/bin/bash
set -e

cd ~/ecommercebaru

echo "========================================="
echo "  DEPLOY: Grannis Kitchen Ecommerce"
echo "========================================="

echo ""
echo "[1/9] Pull latest code..."
git pull origin master

echo ""
echo "[2/9] Build Docker image (no cache)..."
docker compose build --no-cache app

echo ""
echo "[3/9] Stop ALL docker containers to free port 80..."
docker stop $(docker ps -q) 2>/dev/null || true
docker compose down -v --remove-orphans 2>/dev/null || true

echo ""
echo "[4/9] Start containers..."
docker compose up -d

echo ""
echo "[5/9] Wait for MySQL to be healthy..."
sleep 35

echo ""
echo "[6/9] Seed database..."
docker compose exec app php artisan migrate:fresh --force
docker compose exec app php artisan db:seed --class="Webkul\Installer\Database\Seeders\Attribute\DatabaseSeeder" --force
docker compose exec app php artisan db:seed --class="Webkul\Installer\Database\Seeders\Category\CategoryTableSeeder" --force
docker compose exec app php artisan db:seed --class="Webkul\Installer\Database\Seeders\Core\LocalesTableSeeder" --force
docker compose exec app php artisan db:seed --class="Webkul\Installer\Database\Seeders\Core\CurrencyTableSeeder" --force
docker compose exec app php artisan db:seed --class="Webkul\Installer\Database\Seeders\Core\CountriesTableSeeder" --force
docker compose exec app php artisan db:seed --class="Webkul\Installer\Database\Seeders\Core\StatesTableSeeder" --force
docker compose exec app php artisan db:seed --class="Webkul\Installer\Database\Seeders\Core\ConfigTableSeeder" --force
docker compose exec app php artisan db:seed --class="Webkul\Installer\Database\Seeders\Customer\CustomerGroupTableSeeder" --force
docker compose exec app php artisan db:seed --class="Webkul\Installer\Database\Seeders\Inventory\InventorySourceTableSeeder" --force
docker compose exec app php artisan db:seed --class="Webkul\Installer\Database\Seeders\Core\ChannelTableSeeder" --force
docker compose exec app php artisan db:seed --class="Webkul\Installer\Database\Seeders\User\RolesTableSeeder" --force
docker compose exec app php artisan db:seed --class="Webkul\Installer\Database\Seeders\User\AdminsTableSeeder" --force
docker compose exec app php artisan db:seed --class="Webkul\Installer\Database\Seeders\Shop\ThemeCustomizationTableSeeder" --force
docker compose exec app php artisan db:seed --class="Webkul\Installer\Database\Seeders\ProductTableSeeder" --force

echo ""
echo "[7/9] Build search index..."
docker compose exec app php artisan indexer:index --mode=full

echo ""
echo "[8/9] Clear all cache..."
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan view:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan responsecache:clear
docker compose exec app rm -f storage/framework/cache/data/*
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache

echo ""
echo "[9/9] Verify..."
echo -n "  HTTP Status:      "
curl -o /dev/null -s -w "%{http_code}" http://localhost/
echo ""
echo -n "  Products:         "
docker compose exec app php artisan tinker --execute="echo Webkul\Product\Models\Product::count();"
echo -n "  Logo HTTP:        "
curl -o /dev/null -s -w "%{http_code}" http://localhost/images/ankesh-mart-logo.png
echo ""
echo -n "  Categories:       "
docker compose exec app php artisan tinker --execute="echo \Illuminate\Support\Facades\DB::table('category_translations')->where('locale','en')->count();"
echo -n "  Customer Groups:  "
docker compose exec app php artisan tinker --execute="echo \Illuminate\Support\Facades\DB::table('customer_groups')->count();"
echo -n "  Channels:         "
docker compose exec app php artisan tinker --execute="echo \Illuminate\Support\Facades\DB::table('channels')->count();"
echo ""
echo "========================================="
echo "  DEPLOY COMPLETE"
echo "  Visit: http://localhost/"
echo "========================================="

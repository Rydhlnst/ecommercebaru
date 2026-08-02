#!/bin/bash
set -e

cd ~/ecommercebaru

echo "========================================="
echo "  DEPLOY: Ankesh Mart Ecommerce"
echo "========================================="

echo ""
echo "[1/11] Pull latest code..."
git pull origin master

echo ""
echo "[2/11] Install composer deps (resend/resend-laravel, midtrans, etc)..."
# Vendor is copied into the Docker image at build time, so composer must
# run on the host BEFORE `docker compose build`. Skips if composer not
# on host — assumes vendor/ is already up-to-date from local dev.
if command -v composer >/dev/null 2>&1; then
    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
else
    echo "  ! composer not found on host — assuming vendor/ already updated locally"
fi

echo ""
echo "[3/11] Build Docker image (no cache)..."
docker compose build --no-cache app

echo ""
echo "[4/11] Stop ALL docker containers to free port 80..."
docker stop $(docker ps -q) 2>/dev/null || true
docker compose down -v --remove-orphans 2>/dev/null || true

echo ""
echo "[5/11] Start containers..."
docker compose up -d

echo ""
echo "[6/11] Wait for MySQL to be healthy..."
sleep 35

echo ""
echo "[7/11] Clear stale bootstrap/config cache BEFORE seed..."
# Fresh containers may have leftover bootstrap cache from image layer.
# Wipe so migrations/seeders see the current config.
docker compose exec app php artisan config:clear || true
docker compose exec app php artisan cache:clear || true
docker compose exec app php artisan view:clear || true
docker compose exec app rm -f bootstrap/cache/packages.php bootstrap/cache/services.php bootstrap/cache/config.php || true

echo ""
echo "[8/11] Seed database..."
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
echo "[9/11] Build search index..."
docker compose exec app php artisan indexer:index --mode=full

echo ""
echo "[10/11] Clear ALL caches, then re-cache for prod..."
# Order matters: clear compiled views/response cache BEFORE config:cache,
# otherwise cached views may reference stale config entries.
docker compose exec app php artisan view:clear
docker compose exec app php artisan responsecache:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app rm -f storage/framework/cache/data/* 2>/dev/null || true
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

echo ""
echo "[11/11] Verify..."
echo -n "  HTTP Status:              "
curl -o /dev/null -s -w "%{http_code}" http://localhost/
echo ""
echo -n "  Logo HTTP:                "
curl -o /dev/null -s -w "%{http_code}" http://localhost/images/ankesh-mart-logo.png
echo ""
echo -n "  Products:                 "
docker compose exec app php artisan tinker --execute="echo Webkul\Product\Models\Product::count();"
echo -n "  Categories:               "
docker compose exec app php artisan tinker --execute="echo \Illuminate\Support\Facades\DB::table('category_translations')->where('locale','en')->count();"
echo -n "  Customer Groups:          "
docker compose exec app php artisan tinker --execute="echo \Illuminate\Support\Facades\DB::table('customer_groups')->count();"
echo -n "  Channels:                 "
docker compose exec app php artisan tinker --execute="echo \Illuminate\Support\Facades\DB::table('channels')->count();"

echo ""
echo "  ---- Integration wiring ----"
echo -n "  Payment methods:          "
docker compose exec app php artisan tinker --execute="echo implode(', ', array_keys(config('payment_methods')));"
echo -n "  Shipping carriers:        "
docker compose exec app php artisan tinker --execute="echo implode(', ', array_keys(config('carriers')));"
echo -n "  Midtrans class loaded:    "
docker compose exec app php artisan tinker --execute="echo class_exists(\Beres\Payment\Payment\MidtransSnap::class) ? 'YES' : 'NO';"
echo -n "  RajaOngkir class loaded:  "
docker compose exec app php artisan tinker --execute="echo class_exists(\Beres\Shipping\Carriers\RajaOngkir::class) ? 'YES' : 'NO';"
echo -n "  Resend transport:         "
docker compose exec app php artisan tinker --execute="echo class_exists(\Resend\Laravel\Transport\ResendTransport::class) ? 'installed' : 'NOT installed (email will use smtp fallback)';"

echo ""
echo "========================================="
echo "  DEPLOY COMPLETE"
echo "  Visit: http://localhost/"
echo ""
echo "  NEXT STEPS in admin (Configure → Storefront):"
echo "    • Midtrans Payment  → isi server_key, client_key, aktifkan"
echo "    • Pengiriman (RajaOngkir) → isi api_key, api_type, origin_city, aktifkan"
echo "    • Email (Resend)    → isi api_key, from_email, from_name"
echo "    • Header Category Nav → sesuaikan menu 7 kategori"
echo "    • Kontak & Lokasi   → isi google_review_url"
echo "  Then run: docker compose exec app php artisan cache:clear"
echo "========================================="

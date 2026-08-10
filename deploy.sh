#!/bin/bash
# =============================================================================
# Beres Commerce — VPS Deployment Script
# Usage: bash deploy.sh [setup|deploy|fix|rollback|ssl|logs|status|shell]
# =============================================================================

set -euo pipefail

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log()    { echo -e "${GREEN}==> $1${NC}"; }
warn()   { echo -e "${YELLOW}==> WARNING: $1${NC}"; }
error()  { echo -e "${RED}==> ERROR: $1${NC}" >&2; }
header() { echo -e "\n${BLUE}══════════════════════════════════════════════════════════════${NC}"; echo -e "${BLUE}  $1${NC}"; echo -e "${BLUE}══════════════════════════════════════════════════════════════${NC}\n"; }

COMPOSE="docker compose"
BACKUP_DIR="./backups/$(date +%Y%m%d_%H%M%S)"

# ─── Pre-flight Checks ──────────────────────────────────────────────────────
preflight_checks() {
    header "Pre-flight Checks"

    if ! command -v docker &>/dev/null; then
        error "Docker is not installed. Install: https://docs.docker.com/engine/install/"
        exit 1
    fi
    log "Docker: $(docker --version)"

    if docker compose version &>/dev/null; then
        COMPOSE="docker compose"
    elif command -v docker-compose &>/dev/null; then
        COMPOSE="docker-compose"
    else
        error "Docker Compose is not installed."
        exit 1
    fi
    log "Compose: $($COMPOSE version)"

    if [ ! -f .env ]; then
        warn ".env not found."
        if [ -f .env.production ]; then
            cp .env.production .env
            log "Copied .env.production -> .env"
            error "Edit .env with your values, then re-run: bash deploy.sh setup"
            exit 1
        elif [ -f .env.docker ]; then
            cp .env.docker .env
            log "Copied .env.docker -> .env"
            error "Edit .env with your values, then re-run: bash deploy.sh setup"
            exit 1
        else
            error "No .env found. Create one from .env.production"
            exit 1
        fi
    fi

    source .env
    local missing=0
    for var in APP_NAME APP_URL DB_DATABASE DB_USERNAME DB_PASSWORD DB_ROOT_PASSWORD; do
        if [ -z "${!var:-}" ]; then
            error "Missing .env variable: $var"
            missing=1
        fi
    done
    if [ "$missing" -eq 1 ]; then exit 1; fi

    if [ -z "${APP_KEY:-}" ]; then
        warn "APP_KEY is empty — will be generated."
    fi

    local port="${APP_PORT:-280}"
    if ss -tlnp 2>/dev/null | grep -q ":${port} "; then
        warn "Port $port already in use."
    fi

    local free_kb=$(df -k . | tail -1 | awk '{print $4}')
    if [ "$free_kb" -lt 2097152 ]; then
        warn "Low disk space: $(($free_kb / 1024))MB free."
    else
        log "Disk: $(($free_kb / 1024))MB free."
    fi

    log "Pre-flight OK."
}

# ─── Wait for MySQL ──────────────────────────────────────────────────────────
wait_for_mysql() {
    log "Waiting for MySQL..."
    local attempt=0
    until $COMPOSE exec mysql mysqladmin ping -h localhost --silent 2>/dev/null; do
        attempt=$((attempt + 1))
        if [ "$attempt" -ge 60 ]; then
            error "MySQL not ready after 120s. Check: $COMPOSE logs mysql"
            exit 1
        fi
        echo -n "."
        sleep 2
    done
    echo ""
    log "MySQL ready."
}

# ─── Wait for App ────────────────────────────────────────────────────────────
wait_for_app() {
    log "Waiting for app to respond..."
    source .env
    local url="http://localhost:${APP_PORT:-280}"
    local attempt=0
    until curl -sf "$url" >/dev/null 2>&1; do
        attempt=$((attempt + 1))
        if [ "$attempt" -ge 30 ]; then
            warn "App not responding at $url after 60s."
            return 1
        fi
        sleep 2
    done
    log "App responding."
    return 0
}

# ─── Clear & Re-cache ────────────────────────────────────────────────────────
clear_caches() {
    log "Clearing caches..."
    $COMPOSE exec app php artisan view:clear 2>/dev/null || true
    $COMPOSE exec app php artisan config:clear 2>/dev/null || true
    $COMPOSE exec app php artisan route:clear 2>/dev/null || true
    $COMPOSE exec app php artisan cache:clear 2>/dev/null || true
    $COMPOSE exec app php artisan responsecache:clear 2>/dev/null || true

    log "Re-caching for production..."
    $COMPOSE exec app php artisan config:cache 2>/dev/null || true
    $COMPOSE exec app php artisan route:cache 2>/dev/null || true
    $COMPOSE exec app php artisan view:cache 2>/dev/null || true
}

# ─── Backup Database ─────────────────────────────────────────────────────────
backup_database() {
    log "Backing up database..."
    mkdir -p "$BACKUP_DIR"
    source .env
    $COMPOSE exec -T mysql mysqldump -u root -p"${DB_ROOT_PASSWORD}" \
        "${DB_DATABASE}" > "${BACKUP_DIR}/db_backup.sql" 2>/dev/null || {
        warn "DB backup failed (first deploy?). Continuing..."
        return
    }
    log "Backup: ${BACKUP_DIR}/db_backup.sql"
}

# ─── Health Check ────────────────────────────────────────────────────────────
health_check() {
    log "Health check..."
    source .env
    local url="http://localhost:${APP_PORT:-280}"
    local attempt=0
    until curl -sf "$url" >/dev/null 2>&1; do
        attempt=$((attempt + 1))
        if [ "$attempt" -ge 30 ]; then
            warn "App not responding at $url. Check: $COMPOSE logs app"
            return 1
        fi
        sleep 2
    done
    log "Health OK: $url"
    $COMPOSE ps
}

# ─── Setup Cron ──────────────────────────────────────────────────────────────
setup_cron() {
    log "Setting up scheduler cron..."
    if crontab -l 2>/dev/null | grep -q "artisan schedule:run"; then
        log "Cron already exists."
    else
        (crontab -l 2>/dev/null; echo "* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1") | crontab -
        log "Cron added."
    fi
}

# ─── Setup Queue Worker (systemd) ───────────────────────────────────────────
setup_queue_worker() {
    source .env
    if [ "${QUEUE_CONNECTION:-sync}" = "sync" ]; then
        log "Queue=sync, no worker needed."
        return
    fi

    log "Installing queue worker (systemd)..."
    cat > /tmp/beres-queue.service << 'SVCEOF'
[Unit]
Description=Beres Commerce Queue Worker
After=docker.service
Requires=docker.service

[Service]
Type=simple
Restart=always
RestartSec=5
ExecStart=/usr/bin/docker exec beres-app php artisan queue:work --sleep=3 --tries=3 --max-time=3600
ExecStop=/usr/bin/docker exec beres-app php artisan queue:restart

[Install]
WantedBy=multi-user.target
SVCEOF

    if [ ! -f /etc/systemd/system/beres-queue.service ]; then
        sudo mv /tmp/beres-queue.service /etc/systemd/system/
        sudo systemctl daemon-reload
        sudo systemctl enable beres-queue
        sudo systemctl start beres-queue
        log "Queue worker installed & started."
    else
        sudo systemctl restart beres-queue
        log "Queue worker restarted."
    fi
}

# ─── Setup SSL (Caddy reverse proxy) ────────────────────────────────────────
setup_ssl() {
    header "SSL/HTTPS Setup (Caddy)"

    source .env
    local domain="${APP_URL#https://}"
    domain="${domain#http://}"
    domain="${domain%%/*}"

    if [ -z "$domain" ] || [ "$domain" = "localhost" ]; then
        error "APP_URL must be a real domain for SSL. Current: $APP_URL"
        exit 1
    fi

    log "Domain: $domain"

    if ! command -v caddy &>/dev/null; then
        log "Installing Caddy..."
        sudo apt update -qq
        sudo apt install -y -qq debian-keyring debian-archive-keyring apt-transport-https curl
        curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/gpg.key' | sudo gpg --dearmor -o /usr/share/keyrings/caddy-stable-archive-keyring.gpg 2>/dev/null
        curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/debian.deb.txt' | sudo tee /etc/apt/sources.list.d/caddy-stable.list >/dev/null
        sudo apt update -qq
        sudo apt install -y -qq caddy
        log "Caddy installed."
    else
        log "Caddy already installed: $(caddy version)"
    fi

    local port="${APP_PORT:-280}"
    sudo tee /etc/caddy/Caddyfile >/dev/null << CADDYEOF
${domain} {
    reverse_proxy localhost:${port} {
        header_up X-Real-IP {remote_host}
        header_up X-Forwarded-For {remote_host}
        header_up X-Forwarded-Proto {scheme}
    }

    header {
        X-Content-Type-Options nosniff
        X-Frame-Options SAMEORIGIN
        Referrer-Policy strict-origin-when-cross-origin
        -Server
    }

    encode gzip zstd

    log {
        output file /var/log/caddy/${domain}.log
        format json
    }

    @static {
        path /build/* /fonts/* /images/* /*.ico /*.svg /*.js /*.css
    }
    header @static Cache-Control "public, max-age=604800, immutable"
}

www.${domain} {
    redir https://${domain}{uri} permanent
}
CADDYEOF

    sed -i "s|^APP_URL=.*|APP_URL=https://${domain}|" .env
    sudo mkdir -p /var/log/caddy
    sudo systemctl restart caddy
    sudo systemctl enable caddy

    log "Caddy configured. SSL auto-provisioning by Let's Encrypt."
    $COMPOSE restart app queue scheduler

    header "SSL Setup Complete!"
    log "Verify: https://${domain}"
}

# ─── Fix (diagnose + auto-repair) ───────────────────────────────────────────
fix() {
    header "Diagnosing & Fixing Issues"

    preflight_checks
    source .env

    local issues=0

    # 1. Check containers are running
    log "Checking containers..."
    local running=$($COMPOSE ps --format '{{.Name}}:{{.State}}' 2>/dev/null | grep -c "running" || true)
    if [ "$running" -lt 3 ]; then
        warn "Only $running containers running. Starting all..."
        $COMPOSE up -d
        wait_for_mysql
        wait_for_app || true
    else
        log "Containers OK: $running running."
    fi

    # 2. Check APP_KEY
    log "Checking APP_KEY..."
    if ! grep -qE '^APP_KEY=base64:' .env 2>/dev/null; then
        warn "APP_KEY missing. Generating..."
        $COMPOSE exec app php artisan key:generate --force
        log "APP_KEY generated."
    else
        log "APP_KEY OK."
    fi

    # 3. Check database tables
    log "Checking database tables..."
    local tables=("admin_orders" "admin_products" "admin_categories" "admin_reviews" "admin_order_items" "admin_product_images" "admin_product_variations" "faqs" "blog_posts" "blog_categories" "site_settings" "home_showcases")
    local missing_tables=0

    for table in "${tables[@]}"; do
        local exists=$($COMPOSE exec -T mysql mysql -u root -p"${DB_ROOT_PASSWORD}" "${DB_DATABASE}" -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DB_DATABASE}' AND table_name='${table}';" -sN 2>/dev/null || echo "0")
        if [ "$exists" = "0" ]; then
            warn "Table missing: $table"
            missing_tables=1
        fi
    done

    if [ "$missing_tables" -eq 1 ]; then
        warn "Some tables missing. Running all migrations..."
        $COMPOSE exec app php artisan migrate --force
        log "Migrations complete."
    else
        log "All tables OK."
    fi

    # 4. Check pending migrations
    log "Checking for pending migrations..."
    local pending=$($COMPOSE exec app php artisan migrate:status 2>/dev/null | grep -c "No" || true)
    if [ "$pending" -gt 0 ]; then
        warn "$pending pending migrations. Running..."
        $COMPOSE exec app php artisan migrate --force
        log "Pending migrations applied."
    else
        log "No pending migrations."
    fi

    # 5. Check storage link
    log "Checking storage symlink..."
    if ! $COMPOSE exec app test -L public/storage 2>/dev/null; then
        warn "Storage symlink missing. Creating..."
        $COMPOSE exec app php artisan storage:link --force
        log "Storage symlink created."
    else
        log "Storage symlink OK."
    fi

    # 6. Check storage permissions
    log "Checking storage permissions..."
    $COMPOSE exec app chmod -R 775 storage bootstrap/cache 2>/dev/null || true
    $COMPOSE exec app chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
    log "Permissions fixed."

    # 7. Check installed marker
    log "Checking installed marker..."
    if ! $COMPOSE exec app test -f storage/installed 2>/dev/null; then
        warn "Installed marker missing. Creating..."
        $COMPOSE exec app touch storage/installed
        log "Installed marker created."
    else
        log "Installed marker OK."
    fi

    # 8. Check admin user exists
    log "Checking admin user..."
    local admin_exists=$($COMPOSE exec -T mysql mysql -u root -p"${DB_ROOT_PASSWORD}" "${DB_DATABASE}" -e "SELECT COUNT(*) FROM users WHERE is_admin=1;" -sN 2>/dev/null || echo "0")
    if [ "$admin_exists" = "0" ]; then
        warn "No admin user found. Creating..."
        $COMPOSE exec app php artisan tinker --execute="
            \$u = new \App\Models\User;
            \$u->name = 'Admin';
            \$u->email = 'admin@toko.com';
            \$u->password = bcrypt('password');
            \$u->is_admin = true;
            \$u->save();
            echo 'Admin user created.';
        " 2>/dev/null || warn "Failed to create admin user. Create manually."
    else
        log "Admin user OK ($admin_exists found)."
    fi

    # 9. Clear stale caches
    clear_caches

    # 10. Health check
    wait_for_app || true

    header "Fix Complete!"
    log "If issues persist, check: bash deploy.sh logs"
    log "Admin: /admin/login (admin@toko.com / password)"
    log "Native: /backend/login (admin@example.com / admin123)"
}

# ─── Setup (First-time) ─────────────────────────────────────────────────────
setup() {
    header "Initial VPS Setup"
    preflight_checks

    mkdir -p backups
    mkdir -p storage/framework/{sessions,views,cache/data} storage/logs storage/app/public bootstrap/cache
    chmod -R 775 storage bootstrap/cache

    log "Building Docker images (5-10 min)..."
    $COMPOSE build --no-cache app

    log "Starting containers..."
    $COMPOSE up -d

    wait_for_mysql

    if ! grep -qE '^APP_KEY=base64:' .env 2>/dev/null; then
        $COMPOSE exec app php artisan key:generate --force
        log "APP_KEY generated."
    fi

    $COMPOSE exec app php artisan migrate --force
    $COMPOSE exec app php artisan db:seed --force || warn "Seed skipped"
    $COMPOSE exec app touch storage/installed
    $COMPOSE exec app php artisan indexer:index --mode=full || warn "Indexer skipped"
    $COMPOSE exec app php artisan storage:link --force || true

    clear_caches
    setup_cron
    setup_queue_worker
    health_check

    header "Setup Complete!"
    log "URL: ${APP_URL}"
    log "/admin (custom):  admin@toko.com / password"
    log "/backend (native): admin@example.com / admin123"
    log ""
    log "Next: bash deploy.sh ssl  (to enable HTTPS)"
}

# ─── Deploy (Update) ────────────────────────────────────────────────────────
deploy() {
    header "Deploying Update"
    preflight_checks
    source .env

    backup_database

    if [ -d .git ]; then
        log "Pulling code..."
        git pull || warn "git pull failed. Using current code."
    fi

    local old_image=$($COMPOSE images -q app 2>/dev/null || echo "")
    echo "$old_image" > .previous_image_id 2>/dev/null || true

    log "Rebuilding images..."
    $COMPOSE build --no-cache app

    log "Restarting containers..."
    $COMPOSE down
    $COMPOSE up -d

    wait_for_mysql

    $COMPOSE exec app php artisan migrate --force
    $COMPOSE exec app php artisan indexer:index --mode=full || warn "Indexer skipped"

    clear_caches
    $COMPOSE exec app php artisan storage:link --force || true
    $COMPOSE exec app touch storage/installed

    health_check

    header "Deploy Complete!"
    log "Live: ${APP_URL}"
}

# ─── Rollback ────────────────────────────────────────────────────────────────
rollback() {
    header "Rolling Back"

    if [ ! -f .previous_image_id ]; then
        error "No previous image found."
        exit 1
    fi

    local old_image=$(cat .previous_image_id)
    if [ -z "$old_image" ]; then
        error "Previous image ID empty."
        exit 1
    fi

    log "Restoring: $old_image"
    $COMPOSE down
    docker tag "$old_image" beres-commerce:rollback 2>/dev/null || true
    APP_IMAGE="$old_image" $COMPOSE up -d
    wait_for_mysql
    clear_caches
    health_check

    header "Rollback Complete!"
}

# ─── Show Logs ───────────────────────────────────────────────────────────────
show_logs() {
    $COMPOSE logs -f --tail=100 "${@}"
}

# ─── Show Status ─────────────────────────────────────────────────────────────
show_status() {
    header "Containers"
    $COMPOSE ps
    header "Disk Usage"
    docker system df 2>/dev/null || true
    header "Volumes"
    docker volume ls | grep -i beres || true
}

# ─── Shell ───────────────────────────────────────────────────────────────────
shell() {
    $COMPOSE exec app bash
}

# ─── Main ────────────────────────────────────────────────────────────────────
main() {
    local cmd="${1:-help}"
    case "$cmd" in
        setup)    setup ;;
        deploy)   deploy ;;
        fix)      fix ;;
        rollback) rollback ;;
        ssl)      setup_ssl ;;
        logs)     shift; show_logs "$@" ;;
        status)   show_status ;;
        shell)    shell ;;
        *)
            echo "Beres Commerce — VPS Deployment"
            echo ""
            echo "Usage: bash deploy.sh <command>"
            echo ""
            echo "Commands:"
            echo "  setup     First-time VPS setup (build, migrate, seed, cron, queue)"
            echo "  deploy    Pull code, rebuild, migrate, clear cache"
            echo "  fix       Diagnose & auto-repair (tables, permissions, cache, admin)"
            echo "  rollback  Revert to previous Docker image"
            echo "  ssl       Install Caddy + auto-HTTPS (Let's Encrypt)"
            echo "  logs      Follow container logs (Ctrl+C to stop)"
            echo "  status    Show containers, disk, volumes"
            echo "  shell     Bash into app container"
            ;;
    esac
}

main "$@"

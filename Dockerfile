# syntax=docker/dockerfile:1.6
# =============================================================================
# Ankish Mart — Production Image
# Multi-stage: vendor copy → node asset build → runtime PHP-FPM
# =============================================================================

# -----------------------------------------------------------------------------
# Stage 1: PHP dependencies (copy local vendor for offline builds)
# -----------------------------------------------------------------------------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
COPY packages ./packages
COPY database ./database
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY public ./public
COPY resources ./resources
COPY routes ./routes
COPY artisan ./
COPY vendor ./vendor

# Resolve Windows symlinks that point to D:/... paths (broken inside Linux).
# Replace each symlink with the actual package contents from /app/packages/Beres/.
# Vendor dirs are lowercase (account, dashboard) but packages are PascalCase (Account, Dashboard).
RUN cd /app/vendor/beres && \
    for item in *; do \
        if [ -L "$item" ]; then \
            pascal=$(ls /app/packages/Beres/ | grep -i "^${item}$" | head -1); \
            if [ -n "$pascal" ]; then \
                rm -f "$item" && \
                cp -r "/app/packages/Beres/$pascal" "$item"; \
            fi; \
        fi; \
    done

# Rebuild the optimized autoloader from the ACTUAL files present. The host-copied
# vendor/composer/*.php can be stale (e.g. missing a Beres module added after the last
# local dump), which surfaces at runtime as
# "Class Beres\...\ModuleServiceProvider not found" during Concord boot. Because the
# Beres packages are also registered in the root composer.json PSR-4 (-> packages/Beres/*/src),
# this maps every Beres class straight from /app/packages, independent of vendor state.
# Offline: no install, just a dump.
RUN composer dump-autoload --optimize --no-interaction --no-scripts

# -----------------------------------------------------------------------------
# Stage 2: Node asset build (Admin + Shop themes)
# -----------------------------------------------------------------------------
FROM node:20-alpine AS assets
WORKDIR /app
COPY --from=vendor /app /app
RUN cd packages/Webkul/Admin && npm install --no-audit --no-fund && npm run build \
 && cd /app/packages/Webkul/Shop && npm install --no-audit --no-fund && npm run build \
 && rm -rf /app/packages/Webkul/*/node_modules

# -----------------------------------------------------------------------------
# Stage 3: Runtime (PHP-FPM 8.3)
# -----------------------------------------------------------------------------
FROM php:8.3-fpm-alpine AS runtime

RUN apk add --no-cache \
        bash \
        curl \
        git \
        icu-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        libwebp-dev \
        freetype-dev \
        libzip-dev \
        oniguruma-dev \
        libxml2-dev \
        imagemagick-dev \
        mysql-client \
        supervisor \
        $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        exif \
        gd \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo_mysql \
        zip \
    && pecl install redis imagick \
    && docker-php-ext-enable redis imagick \
    && apk del $PHPIZE_DEPS \
    && rm -rf /tmp/* /var/cache/apk/*

# PHP config
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-app.ini

# App
WORKDIR /var/www/html
COPY --from=assets --chown=www-data:www-data /app /var/www/html

# Snapshot of built public assets — entrypoint.sh syncs this into the
# app_public volume on every container start so deploys always get fresh assets.
RUN cp -a /var/www/html/public /var/www/html/.public-snapshot

RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]

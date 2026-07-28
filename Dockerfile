# syntax=docker/dockerfile:1.6
# =============================================================================
# Beres Commerce — Production Image
# Multi-stage: composer deps → node asset build → runtime PHP-FPM
# =============================================================================

# -----------------------------------------------------------------------------
# Stage 1: Composer dependencies
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
RUN composer install \
        --no-dev \
        --no-interaction \
        --no-progress \
        --prefer-dist \
        --optimize-autoloader \
        --no-scripts \
        --ignore-platform-reqs

# -----------------------------------------------------------------------------
# Stage 2: Node asset build (Admin + Shop themes)
# -----------------------------------------------------------------------------
FROM node:20-alpine AS assets
WORKDIR /app
COPY --from=vendor /app /app
RUN cd packages/Webkul/Admin && npm ci && npm run build \
 && cd /app/packages/Webkul/Shop && npm ci && npm run build \
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

RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]

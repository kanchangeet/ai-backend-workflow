# ── Stage 1: Composer dependencies ───────────────────────────────────────────
FROM composer:2.8 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# ── Stage 2: Production image ─────────────────────────────────────────────────
FROM php:8.4-fpm-alpine AS app

# System deps
RUN apk add --no-cache \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    shadow

# PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    bcmath \
    zip \
    opcache

# PHP & Opcache tuning for production
COPY docker/php/php.ini     /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/html

# Copy vendor from build stage
COPY --from=vendor /app/vendor ./vendor

# Copy application source
COPY . .

# Regenerate autoloader now that all source (including Modules/) is present
COPY --from=vendor /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload --optimize --no-scripts && rm /usr/bin/composer

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache .env \
    && chmod -R 775 storage bootstrap/cache \
    && chmod 664 .env

# Entrypoint: generates Swagger docs then starts PHP-FPM
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Run as non-root
USER www-data

EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

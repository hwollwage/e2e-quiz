# ==========================================
# STAGE 1: Composer (Dependency Builder)
# ==========================================
FROM composer:latest AS composer_builder
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

# ==========================================
# STAGE 2: Node (Vite/Tailwind Compiler)
# ==========================================
FROM node:20-alpine AS node_builder
WORKDIR /app

COPY package.json package-lock.json vite.config.js ./
COPY resources ./resources
COPY public ./public

# Tailwind scans Laravel pagination views from vendor via @source in app.css.
COPY --from=composer_builder /app/vendor ./vendor
RUN npm install && npm run build

# ==========================================
# STAGE 3: PHP (Production Runtime)
# ==========================================
FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    libzip-dev \
    linux-headers \
    postgresql-dev \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath pcntl posix sockets \
    && rm -rf /var/cache/apk/*

WORKDIR /var/www/html

COPY . .
COPY --from=composer_builder /app/vendor ./vendor
COPY --from=node_builder /app/public/build ./public/build

RUN mkdir -p storage/framework/views \
             storage/framework/cache \
             storage/framework/sessions \
             storage/logs \
             bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload --no-dev --optimize

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]

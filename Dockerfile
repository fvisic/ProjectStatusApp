# syntax=docker/dockerfile:1.6
# ---------- Stage 1: Frontend build (Vite + Tailwind + Chart.js) ----------
FROM node:22-alpine AS assets
WORKDIR /build
COPY package.json package-lock.json* ./
# Use `npm install` (not `ci`) because optional platform-specific deps
# (rollup/vite native binaries) differ between build host arch and target arch.
RUN npm install --no-audit --no-fund
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
COPY app ./app
RUN npm run build

# ---------- Stage 2: PHP dependencies ----------
FROM composer:2 AS vendor
WORKDIR /build
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --no-scripts \
    --optimize-autoloader \
    --ignore-platform-req=ext-gd \
    --ignore-platform-req=ext-intl \
    --ignore-platform-req=ext-mbstring \
    --ignore-platform-req=ext-xml \
    --ignore-platform-req=ext-pdo_mysql \
    --ignore-platform-req=ext-bcmath

# ---------- Stage 3: Runtime ----------
FROM php:8.5-fpm-alpine AS runtime

ENV APP_ENV=production \
    APP_DEBUG=false \
    COMPOSER_ALLOW_SUPERUSER=1

RUN apk add --no-cache \
        nginx \
        nginx-mod-http-brotli \
        supervisor \
        bash \
        tini \
        icu-libs \
        libzip \
        libpng \
        libjpeg-turbo \
        freetype \
        libwebp \
        oniguruma \
        libxml2 \
        git \
        unzip \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libwebp-dev \
        oniguruma-dev \
        libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        exif \
        gd \
        intl \
        mbstring \
        pcntl \
        pdo_mysql \
        xml \
        zip \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/* /tmp/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY docker/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-app.conf
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf

WORKDIR /app
COPY --chown=www-data:www-data . /app
COPY --from=vendor --chown=www-data:www-data /build/vendor /app/vendor
COPY --from=assets --chown=www-data:www-data /build/public/build /app/public/build

RUN mkdir -p /app/storage/framework/{cache,sessions,views} \
             /app/storage/logs \
             /app/bootstrap/cache \
             /run/nginx \
             /var/log/supervisor \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache \
    && composer dump-autoload --optimize --classmap-authoritative --no-dev \
    && composer clear-cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/sbin/tini", "--", "/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]

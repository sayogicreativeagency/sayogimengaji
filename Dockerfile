# Image untuk Render (pola sama dengan sayogisedia). Nginx + PHP-FPM siap Laravel, mendengarkan port 8080.
FROM serversideup/php:8.4-fpm-nginx AS app

# intl: Filament dan kalender Hijriah (IntlDateFormatter).
USER root
RUN install-php-extensions intl
USER www-data

COPY --chown=www-data:www-data . /var/www/html
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Aset Vite dibangun setelah vendor ada, karena Tailwind ikut memindai view pagination di vendor.
# Font (Instrument Sans, Cormorant, Amiri) diunduh dari fonts.bunny.net saat build.
FROM node:22-alpine AS aset
WORKDIR /app
COPY --from=app /var/www/html /app
RUN npm ci && npm run build

FROM app
COPY --from=aset --chown=www-data:www-data /app/public/build /var/www/html/public/build

# Saat container start: storage:link, migrate --force, lalu cache config/route/view.
ENV AUTORUN_ENABLED=true \
    PHP_OPCACHE_ENABLE=1

#!/bin/bash
# Runs once per container start, before supervisord hands off to
# nginx/php-fpm/scheduler/queue-worker (webdevops' entrypoint.d convention).
set -e

cd /app

mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/framework/testing \
         storage/app/public \
         storage/logs
chown -R application:application storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

if [ -z "${APP_KEY:-}" ]; then
    echo "[entrypoint] no APP_KEY set — generating one"
    php artisan key:generate --force
fi

echo "[entrypoint] running migrations"
php artisan migrate --force

echo "[entrypoint] caching config/routes/views"
php artisan config:cache
php artisan route:cache
php artisan view:cache

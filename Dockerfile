# Railway deploy image for the SMT License Server (SaaS conversion plan
# Phase 1/7): the pricing/checkout site, Stripe webhook receiver, and
# staff dashboard. No frontend build step — every view here is plain Blade
# with inline CSS, no Vite/Tailwind assets to compile (unlike the SaasPOS
# app), so this is a single stage.

FROM webdevops/php-nginx:8.3

ENV WEB_DOCUMENT_ROOT=/app/public \
    PHP_MEMORY_LIMIT=512M \
    PHP_MAX_EXECUTION_TIME=120 \
    PHP_DISPLAY_ERRORS=0 \
    PHP_POST_MAX_SIZE=64M \
    PHP_UPLOAD_MAX_FILESIZE=64M

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY . .

RUN composer dump-autoload --optimize --no-dev \
    && php artisan config:clear \
    && chown -R application:application /app \
    && chmod -R 775 storage bootstrap/cache

# The scheduler (railway:poll-deployments, every minute) and the queue
# worker (ProvisionInstance) both need to run as their own long-lived
# processes, same supervisor.d pattern as the main SaasPOS image.
COPY docker/supervisor-scheduler.conf /opt/docker/etc/supervisor.d/laravel-scheduler.conf
COPY docker/supervisor-queue.conf /opt/docker/etc/supervisor.d/laravel-queue.conf

COPY docker-entrypoint.sh /opt/docker/provision/entrypoint.d/30-license-server.sh
RUN chmod +x /opt/docker/provision/entrypoint.d/30-license-server.sh

EXPOSE 80

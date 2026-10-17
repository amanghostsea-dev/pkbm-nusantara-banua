#!/bin/bash
set -e

export PORT=${PORT:-8080}
echo "[ENTRYPOINT] Starting PKBM Nusantara Banua on port $PORT..."

# Clean stale caches
rm -f /var/www/laravel/bootstrap/cache/*.php

# Permissions for storage & database
mkdir -p /var/www/laravel/storage/framework/{sessions,views,cache} /var/www/laravel/storage/logs
mkdir -p /var/www/laravel/database
touch /var/www/laravel/storage/logs/laravel.log

if [ ! -f /var/www/laravel/database/database.sqlite ]; then
    touch /var/www/laravel/database/database.sqlite
    cd /var/www/laravel && php artisan migrate --force --seed || true
fi

chmod -R 777 /var/www/laravel/storage /var/www/laravel/bootstrap/cache /var/www/laravel/database

cd /var/www/laravel
php artisan package:discover --ansi || true
php artisan storage:link || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

chmod -R 777 /var/www/laravel/storage /var/www/laravel/bootstrap/cache /var/www/laravel/database

echo "[ENTRYPOINT] Starting Supervisor on port $PORT..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

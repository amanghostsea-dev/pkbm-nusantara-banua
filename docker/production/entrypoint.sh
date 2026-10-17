#!/bin/bash
set -e

PORT=${PORT:-8080}
echo "[ENTRYPOINT] Starting PKBM Nusantara Banua on port $PORT..."

# Update Nginx listening port dynamically for Cloud Platforms (Railway, Render, etc.)
sed -i "s/listen 8080/listen $PORT/g" /etc/nginx/sites-available/default
sed -i "s/listen \[::\]:8080/listen \[::\]:$PORT/g" /etc/nginx/sites-available/default

# Clean stale bootstrap caches from dev environment
rm -f /var/www/laravel/bootstrap/cache/*.php

# Permissions for Laravel storage & SQLite database
mkdir -p /var/www/laravel/storage/framework/{sessions,views,cache} /var/www/laravel/storage/logs
mkdir -p /var/www/laravel/database
touch /var/www/laravel/storage/logs/laravel.log

if [ ! -f /var/www/laravel/database/database.sqlite ]; then
    touch /var/www/laravel/database/database.sqlite
    cd /var/www/laravel && php artisan migrate --force --seed || true
fi

chmod -R 777 /var/www/laravel/storage /var/www/laravel/bootstrap/cache /var/www/laravel/database
chown -R www-data:www-data /var/www/laravel/storage /var/www/laravel/bootstrap/cache /var/www/laravel/database

# Run Laravel optimizations
cd /var/www/laravel
php artisan package:discover --ansi || true
php artisan storage:link || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

chmod -R 777 /var/www/laravel/storage /var/www/laravel/bootstrap/cache /var/www/laravel/database
chown -R www-data:www-data /var/www/laravel/storage /var/www/laravel/bootstrap/cache /var/www/laravel/database

echo "[ENTRYPOINT] Initialization complete. Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

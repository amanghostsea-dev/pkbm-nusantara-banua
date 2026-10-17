#!/bin/bash
set -e

export PORT=${PORT:-8080}
echo "[ENTRYPOINT] Starting PKBM Nusantara Banua on port $PORT..."

# Ensure all system runtime and log directories exist
mkdir -p /var/log/supervisor /var/log/nginx /run /var/run

# Update Nginx listening port dynamically for Railway / Cloud ($PORT)
sed -i "s/listen 8080/listen $PORT/g" /etc/nginx/sites-available/default
sed -i "s/listen \[::\]:8080/listen \[::\]:$PORT/g" /etc/nginx/sites-available/default

# Enable site if not already enabled
mkdir -p /etc/nginx/sites-enabled
ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Verify Nginx configuration syntax
nginx -t

# Clean stale bootstrap cache from development
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
chown -R www-data:www-data /var/www/laravel/storage /var/www/laravel/bootstrap/cache /var/www/laravel/database

cd /var/www/laravel
php artisan package:discover --ansi || true
php artisan storage:link || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

chmod -R 777 /var/www/laravel/storage /var/www/laravel/bootstrap/cache /var/www/laravel/database
chown -R www-data:www-data /var/www/laravel/storage /var/www/laravel/bootstrap/cache /var/www/laravel/database

echo "[ENTRYPOINT] Starting Supervisor on port $PORT..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

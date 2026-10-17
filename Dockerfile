# ========================================================
# Production All-in-One Dockerfile for PKBM Nusantara Banua
# Contains: PHP 8.2-FPM + Laravel 11, Python 3 + FastAPI, Nginx, SQLite
# Compatible with: Railway.app, Render.com, Fly.io, VPS
# ========================================================

FROM php:8.2-fpm-bookworm

LABEL maintainer="PKBM Nusantara Banua"

ENV DEBIAN_FRONTEND=noninteractive
ENV PORT=8080
ENV COMPOSER_ALLOW_SUPERUSER=1

# 1. Install system dependencies, Nginx, Python 3, Supervisor & Pango
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    supervisor \
    sqlite3 \
    libsqlite3-dev \
    git \
    curl \
    unzip \
    zip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    python3 \
    python3-pip \
    python3-venv \
    python3-dev \
    build-essential \
    libpango-1.0-0 \
    libharfbuzz0b \
    libpangoft2-1.0-0 \
    libpangocairo-1.0-0 \
    fonts-dejavu-core \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql gd zip bcmath opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Setup Python FastAPI Microservice
WORKDIR /var/www/python
COPY backend-python/requirements.txt ./
RUN python3 -m venv venv && \
    ./venv/bin/pip install --no-cache-dir --upgrade pip setuptools wheel && \
    ./venv/bin/pip install --no-cache-dir --prefer-binary -r requirements.txt
COPY backend-python ./
RUN mkdir -p storage/raport storage/sertifikat storage/temp

# 4. Setup Laravel Backend
WORKDIR /var/www/laravel
COPY backend-laravel ./

# Prepare environment
RUN cp .env.example .env
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
RUN composer dump-autoload --optimize --no-dev

ENV APP_KEY=base64:q2r6l12l8Kz4qj2E0Z4D21HwP3u7y6X1Y8Z9a0b1c2d=
ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=/var/www/laravel/database/database.sqlite
ENV APP_ENV=production
ENV APP_DEBUG=false

# 5. Configure Nginx and Supervisor
COPY docker/production/nginx.conf /etc/nginx/sites-available/default
COPY docker/production/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/production/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

# 6. Expose default port
EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

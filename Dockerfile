# ========================================================
# Production All-in-One Dockerfile for PKBM Nusantara Banua
# Contains: PHP 8.2-FPM + Laravel 11, Python 3 + FastAPI, Nginx, SQLite
# Compatible with: Render.com, Railway.app, Koyeb, Fly.io, VPS
# ========================================================

FROM php:8.2-fpm-bookworm

LABEL maintainer="PKBM Nusantara Banua"

ENV DEBIAN_FRONTEND=noninteractive
ENV PORT=8080

# 1. Install system dependencies, Nginx, Python 3, Supervisor & Pango (for PDF/Raport)
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
    && docker-php-ext-install -j$(nproc) pdo pdo_sqlite pdo_mysql gd zip bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Setup Python FastAPI Microservice
WORKDIR /var/www/python
COPY backend-python/requirements.txt ./
RUN python3 -m venv venv && \
    ./venv/bin/pip install --no-cache-dir --upgrade pip && \
    ./venv/bin/pip install --no-cache-dir -r requirements.txt
COPY backend-python ./
RUN mkdir -p storage/raport storage/sertifikat storage/temp

# 4. Setup Laravel Backend
WORKDIR /var/www/laravel
COPY backend-laravel ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Ensure default production env
RUN if [ ! -f .env ]; then cp .env.example .env 2>/dev/null || true; fi
ENV DB_CONNECTION=sqlite
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

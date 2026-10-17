# ===============================================
# PANDUAN SETUP PROYEK PKBM SISTEM
# Sistem Informasi PKBM Paket A, B & C
# ===============================================
# 
# Tech Stack:
#   - Laravel 11 (Backend Utama)
#   - Python FastAPI (Microservice ML/PDF)
#   - MySQL 8.0 (Database)
#   - Redis 7 (Cache & Queue)
#   - Docker (Containerization)
#   - Nginx (Web Server)
# ===============================================

# LANGKAH 1: Install Prerequisites
# ---------------------------------
# a. Install PHP 8.3 (dari https://windows.php.net/download/)
#    - Tambahkan ke PATH environment variable
#    - Enable extensions: pdo_mysql, mbstring, openssl, fileinfo, redis

# b. Install Composer (dari https://getcomposer.org/download/)

# c. Install Docker Desktop (dari https://www.docker.com/products/docker-desktop/)

# d. Verifikasi instalasi
php --version        # Harus menampilkan PHP 8.3.x
composer --version   # Harus menampilkan Composer 2.x
docker --version     # Harus menampilkan Docker version 2x.x

# LANGKAH 2: Setup Laravel Backend
# ---------------------------------
cd backend-laravel

# Buat project Laravel baru
composer create-project laravel/laravel . "^11.0"

# Install package tambahan
composer require laravel/sanctum                 # Auth API
composer require spatie/laravel-permission       # Role & Permission
composer require intervention/image-laravel     # Image processing
composer require barryvdh/laravel-dompdf        # PDF (backup)
composer require predis/predis                  # Redis client
composer require guzzlehttp/guzzle              # HTTP client ke Python
composer require laravel/telescope --dev        # Debugging & monitoring
composer require laravel/breeze --dev           # Auth scaffolding

# Install Breeze dengan Inertia Vue
php artisan breeze:install vue --dark

# Install NPM dependencies
npm install
npm install @headlessui/vue @heroicons/vue chart.js vue-chartjs
npm install @vueuse/core dayjs axios

# LANGKAH 3: Konfigurasi Environment Laravel
# -------------------------------------------
# Copy .env.example ke .env
# Edit .env:
#   DB_CONNECTION=mysql
#   DB_HOST=127.0.0.1
#   DB_PORT=3306
#   DB_DATABASE=pkbm_db
#   DB_USERNAME=pkbm_user
#   DB_PASSWORD=pkbm_secret
#   
#   REDIS_HOST=127.0.0.1
#   REDIS_PASSWORD=null
#   REDIS_PORT=6379
#   
#   QUEUE_CONNECTION=redis
#   
#   PYTHON_API_URL=http://localhost:8000
#   PYTHON_API_KEY=internal-api-key

php artisan key:generate

# LANGKAH 4: Jalankan Migrations & Seeders
# ------------------------------------------
php artisan migrate:fresh --seed

# LANGKAH 5: Setup Python FastAPI
# ---------------------------------
cd ../backend-python

# Buat virtual environment
python -m venv venv

# Aktivasi venv (Windows)
.\venv\Scripts\activate

# Install dependencies
pip install -r requirements.txt

# LANGKAH 6: Jalankan via Docker (Rekomendasi)
# -----------------------------------------------
cd ..
docker-compose up -d

# Atau jalankan manual:
# Terminal 1 (Laravel)
cd backend-laravel
php artisan serve --port=8001
php artisan queue:work redis   # Terminal terpisah

# Terminal 2 (Python FastAPI)
cd backend-python
.\venv\Scripts\activate
uvicorn app.main:app --reload --port=8000

# LANGKAH 7: Akses Aplikasi
# ---------------------------
# Web App    : http://localhost
# API Docs   : http://localhost:8000/docs
# phpMyAdmin : http://localhost:8080
# MailPit    : http://localhost:8025

# LANGKAH 8: Default Login
# -------------------------
# Admin   : admin@pkbm.sch.id  / password
# Tutor   : tutor@pkbm.sch.id  / password
# Siswa   : siswa@pkbm.sch.id  / password

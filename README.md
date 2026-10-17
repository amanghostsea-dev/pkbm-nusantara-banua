# 🏫 SISTEM INFORMASI PKBM
## Pusat Kegiatan Belajar Masyarakat — Paket A, B & C

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red?logo=laravel)](https://laravel.com)
[![Python](https://img.shields.io/badge/Python-FastAPI-green?logo=fastapi)](https://fastapi.tiangolo.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-brightgreen?logo=vue.js)](https://vuejs.org)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-blue?logo=mysql)](https://mysql.com)
[![Docker](https://img.shields.io/badge/Docker-Ready-blue?logo=docker)](https://docker.com)

---

## 🎯 Tentang Sistem

Sistem Informasi PKBM adalah aplikasi web **enterprise-grade** untuk mengelola operasional Pusat Kegiatan Belajar Masyarakat, mencakup program **Paket A** (setara SD), **Paket B** (setara SMP), dan **Paket C** (setara SMA).

### ✨ Keunggulan Sistem

| Fitur | Teknologi | Status |
|-------|-----------|--------|
| 👨‍🎓 Manajemen Siswa lengkap | Laravel + Vue | ✅ Ready |
| 📅 Absensi digital via QR Code | Laravel + Python | ✅ Ready |
| 📊 Input & Rekap Nilai | Laravel | ✅ Ready |
| 📄 Generate Raport PDF otomatis | Python (ReportLab) | ✅ Ready |
| 🎓 Sertifikat Kelulusan Digital | Python (ReportLab) | ✅ Ready |
| 🤖 Prediksi Kelulusan (AI/ML) | Python (scikit-learn) | ✅ Ready |
| 📈 Dashboard Analitik Real-time | Python + Chart.js | ✅ Ready |
| 📋 Laporan Dinas Excel/PDF | Python (openpyxl) | ✅ Ready |
| 💰 Administrasi Pembayaran | Laravel | ✅ Ready |
| 🔐 Multi-Role Authentication | Laravel Sanctum + Spatie | ✅ Ready |
| 📱 Responsive (Mobile-friendly) | CSS + Vue | ✅ Ready |
| 🐳 Docker Ready | Docker Compose | ✅ Ready |

---

## 🏗️ Arsitektur

```
┌─────────────────────────────────────────────────────┐
│                  NGINX (Port 80)                     │
│              Reverse Proxy + Static Files            │
└─────────────┬─────────────────┬────────────────────┘
              │                 │
    ┌─────────▼──────┐   ┌──────▼──────────┐
    │  Laravel 11    │   │  Python FastAPI  │
    │  (PHP-FPM)     │   │  Port 8000       │
    │                │   │                 │
    │  - Auth        │   │  - Raport PDF   │
    │  - CRUD        │   │  - Sertifikat   │
    │  - API         │   │  - ML Prediksi  │
    │  - Queue       │   │  - Analytics    │
    │  - Inertia     │   │  - QR Code      │
    │    + Vue 3     │   │  - Excel Export │
    └────────┬───────┘   └──────┬──────────┘
             │                  │
    ┌────────▼──────────────────▼──────────┐
    │         MySQL 8.0 + Redis 7          │
    │         (Shared Database)            │
    └──────────────────────────────────────┘
```

---

## 🚀 Cara Install

### Prerequisites

Pastikan sudah terinstall:
- **PHP 8.3+** dengan extensions: pdo_mysql, mbstring, redis, gd, zip
- **Composer 2.x**
- **Node.js 20+** & npm
- **Python 3.11+**
- **MySQL 8.0**
- **Redis 7** (opsional, untuk queue)
- **Docker Desktop** (direkomendasikan)

### Option 1: Via Docker (⭐ REKOMENDASI)

```bash
# Clone / masuk ke folder proyek
cd PKBM

# Setup Laravel
cd backend-laravel
composer install
cp .env.example .env
# Edit .env sesuai kebutuhan

# Jalankan Docker
cd ..
docker-compose up -d

# Jalankan migrations & seeder
docker exec pkbm_laravel php artisan migrate:fresh --seed

# Akses
# Web App    : http://localhost
# API Docs   : http://localhost:8000/docs
# phpMyAdmin : http://localhost:8080
```

### Option 2: Manual (Tanpa Docker)

#### 2a. Setup Laravel

```bash
cd backend-laravel

# Install dependencies
composer install

# Environment
cp .env.example .env
php artisan key:generate

# Edit .env:
# DB_HOST=127.0.0.1
# DB_DATABASE=pkbm_db
# DB_USERNAME=root
# DB_PASSWORD=yourpassword
# PYTHON_API_URL=http://localhost:8000

# Install frontend
npm install
npm run build    # Production
npm run dev      # Development (hot reload)

# Database
php artisan migrate:fresh --seed

# Jalankan server
php artisan serve                     # Terminal 1
php artisan queue:work                # Terminal 2
```

#### 2b. Setup Python FastAPI

```bash
cd backend-python

# Virtual environment
python -m venv venv

# Aktivasi (Windows)
.\venv\Scripts\activate

# Aktivasi (Linux/Mac)
source venv/bin/activate

# Install packages
pip install -r requirements.txt

# Copy environment
cp .env.example .env

# Jalankan server
uvicorn app.main:app --reload --port 8000
```

---

## 🔐 Default Login

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@pkbm.sch.id | password |
| **Tutor** | tutor@pkbm.sch.id | password |
| **Siswa** | siswa@pkbm.sch.id | password |

---

## 📁 Struktur Proyek

```
PKBM/
├── backend-laravel/          # Laravel 11 Backend Utama
│   ├── app/
│   │   ├── Http/Controllers/ # Admin, Tutor, Siswa Controllers
│   │   ├── Models/           # Eloquent Models
│   │   ├── Services/         # Business Logic
│   │   └── Jobs/             # Queue Jobs
│   ├── database/
│   │   ├── migrations/       # Database Schema
│   │   └── seeders/          # Demo Data
│   ├── resources/js/
│   │   ├── Pages/            # Vue.js Pages (Inertia)
│   │   ├── Components/       # Reusable Components
│   │   └── Layouts/          # Layout Templates
│   └── routes/               # Web & API Routes
│
├── backend-python/           # Python FastAPI Microservice
│   ├── app/
│   │   ├── api/v1/           # API Endpoints
│   │   ├── services/         # PDF, Excel Services
│   │   ├── ml/               # Machine Learning Models
│   │   └── core/             # Config, Database
│   └── requirements.txt
│
├── docker/                   # Docker Configuration
│   ├── nginx/                # Nginx Config
│   ├── php/                  # PHP-FPM Config
│   ├── mysql/                # MySQL Config
│   └── redis/                # Redis Config
│
├── docs/                     # Dokumentasi
│   ├── ERD.md                # Entity Relationship Diagram
│   └── SETUP.sh              # Panduan Setup
│
├── docker-compose.yml        # Docker Stack
└── README.md                 # This file
```

---

## 🤖 Fitur Machine Learning

Sistem ini menggunakan **Random Forest Classifier** untuk memprediksi kemungkinan kelulusan siswa berdasarkan:

- 📊 Rata-rata nilai akademik
- ✅ Persentase kehadiran
- 📖 Nilai mata pelajaran utama (Matematika, Bahasa Indonesia, IPA)
- 📅 Progress semester yang sudah ditempuh
- 💰 Status pembayaran
- 👤 Faktor demografis (usia, jenis kelamin)

Model akan semakin akurat seiring bertambahnya data historis siswa.

---

## 📊 API Documentation

Setelah menjalankan Python FastAPI:
- **Swagger UI**: http://localhost:8000/docs
- **ReDoc**: http://localhost:8000/redoc

### Endpoint Utama Python API:

| Endpoint | Method | Deskripsi |
|----------|--------|-----------|
| `/api/v1/raport/generate` | POST | Generate raport PDF |
| `/api/v1/raport/generate-bulk` | POST | Generate raport seluruh kelas |
| `/api/v1/sertifikat/generate` | POST | Generate sertifikat kelulusan |
| `/api/v1/prediksi/kelulusan` | POST | Prediksi kelulusan ML |
| `/api/v1/prediksi/kelulusan/bulk` | POST | Prediksi seluruh kelas |
| `/api/v1/analitik/dashboard/ringkasan` | GET | Data dashboard |
| `/api/v1/laporan/generate` | POST | Export laporan dinas |
| `/api/v1/qrcode/absensi/generate` | POST | Generate QR absensi |

---

## 🔄 Roadmap Pengembangan

### Phase 1 — MVP ✅
- [x] Auth & Role Management (Admin, Tutor, Siswa, Orang Tua)
- [x] Master Data Siswa, Tutor, Program, Angkatan, Kelas
- [x] Absensi QR Code
- [x] Input Nilai & Rekap
- [x] Generate Raport PDF

### Phase 2 — Advanced
- [ ] Notifikasi WhatsApp (via Fonnte/WablasAPI)
- [ ] Mobile App (PWA / React Native)
- [ ] Integrasi Dapodik
- [ ] E-learning module
- [ ] Video conference Jitsi

### Phase 3 — AI Features
- [ ] Chatbot bantuan belajar (Gemini API)
- [ ] Rekomendasi materi belajar personalisasi
- [ ] Analisis gaya belajar siswa
- [ ] Deteksi siswa butuh bimbingan khusus

---

## 👨‍💻 Kontribusi

Proyek ini dirancang untuk dapat dikembangkan lebih lanjut. Silakan:
1. Fork proyek ini
2. Buat feature branch
3. Commit perubahan
4. Buat Pull Request

---

## 📝 Lisensi

MIT License — Bebas digunakan dan dikembangkan untuk kebutuhan pendidikan.

---

**Dibuat dengan ❤️ untuk kemajuan pendidikan non-formal Indonesia 🇮🇩**

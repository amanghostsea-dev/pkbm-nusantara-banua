@echo off
title PKBM - Laravel Backend Server
echo ===================================================
echo             MEMULAI PKBM LARAVEL BACKEND
echo ===================================================
cd /d "%~dp0backend-laravel"

set PATH=C:\xampp\php;%PATH%

if not exist ".env" (
    echo [*] Membuat file .env...
    copy .env.example .env
)

echo.
echo [*] Database SQLite lokal siap digunakan (database/database.sqlite).
echo [*] Menjalankan server Laravel di http://127.0.0.1:8080
echo.
"C:\xampp\php\php.exe" artisan serve --port 8080
pause

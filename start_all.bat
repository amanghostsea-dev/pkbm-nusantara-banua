@echo off
title SISTEM INFORMASI PKBM
echo =========================================================
echo       SISTEM INFORMASI PKBM (PAKET A, B & C)
echo =========================================================
echo.
echo [*] Membuka Python FastAPI Microservice di terminal baru...
start "PKBM Python FastAPI" "%~dp0start_python.bat"

timeout /t 2 /nobreak >nul

echo [*] Membuka Laravel Backend di terminal baru...
start "PKBM Laravel Backend" "%~dp0start_laravel.bat"

echo.
echo =========================================================
echo [OK] KEDUA SERVICE SEDANG BERJALAN:
echo  - Frontend & Laravel Web : http://127.0.0.1:8080
echo  - Python FastAPI Service : http://127.0.0.1:8000
echo  - Dokumentasi API / Docs : http://127.0.0.1:8000/docs
echo =========================================================
echo.
pause

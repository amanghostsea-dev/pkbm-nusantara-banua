@echo off
title PKBM - Python FastAPI Microservice
echo ===================================================
echo     MEMULAI PKBM PYTHON FASTAPI MICROSERVICE
echo ===================================================
cd /d "%~dp0backend-python"

if not exist ".venv\Scripts\python.exe" (
    echo [ERROR] Virtual environment belum dibuat.
    echo Menjalankan setup...
    python -m venv .venv
    call .venv\Scripts\activate.bat
    pip install -r requirements.txt
) else (
    call .venv\Scripts\activate.bat
)

echo.
echo [*] Menjalankan server FastAPI di http://127.0.0.1:8000
echo [*] Dokumentasi API Swagger: http://127.0.0.1:8000/docs
echo.
uvicorn app.main:app --host 127.0.0.1 --port 8000 --reload
pause

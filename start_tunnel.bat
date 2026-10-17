@echo off
title Cloudflare Tunnel - Sistem PKBM
color 0A
echo ============================================================
echo   MENGHUBUNGKAN SISTEM PKBM KE INTERNET PUBLIK (GRATIS)
echo ============================================================
echo.
echo Pastikan backend Laravel (port 8080) sudah berjalan.
echo Membuka terowongan Cloudflare HTTPS...
echo.
"%~dp0cloudflared.exe" tunnel --url http://127.0.0.1:8080
pause

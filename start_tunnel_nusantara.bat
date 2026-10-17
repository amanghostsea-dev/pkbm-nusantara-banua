@echo off
title Tunnel Publik - PKBM Nusantara Banua
color 0B
echo ============================================================
echo   MENGHUBUNGKAN SISTEM PKBM NUSANTARA BANUA KE INTERNET
echo ============================================================
echo.
echo Link Resmi Publik:
echo   https://pkbm-nusantarabanua.loca.lt
echo.
echo Jika browser meminta "Tunnel Password", masukkan IP Anda:
echo (Dapat dilihat otomatis di: https://loca.lt/mytunnelpassword)
echo.
:loop
echo [%date% %time%] Memulai terowongan publik...
call npx --yes localtunnel --port 8080 --subdomain pkbm-nusantarabanua
echo.
echo [PERINGATAN] Terowongan terputus (kode keluar: %errorlevel%). Menghubungkan ulang dalam 5 detik...
timeout /t 5 >nul
goto loop

@echo off
title Upload ke GitHub - PKBM Nusantara Banua
color 0A
echo ============================================================
echo   MENGUPLOAD KODE PKBM KE GITHUB REPOSITORY
echo   https://github.com/amanghostsea-dev/pkbm-nusantara-banua.git
echo ============================================================
echo.
git push -u origin master
echo.
if %errorlevel% equ 0 (
    echo [BERHASIL] Seluruh kode dan database telah ter-upload ke GitHub!
) else (
    echo [GAGAL] Terjadi kendala saat upload.
)
pause

@echo off
setlocal
title GreenPrinting Launcher

cd /d "%~dp0"

echo ==================================================
echo  GreenPrinting - One Click Launcher
echo ==================================================
echo.

where php >nul 2>nul
if errorlevel 1 (
    echo [ERROR] PHP tidak ditemukan di PATH.
    echo Pastikan PHP/XAMPP sudah aktif dan php.exe tersedia di PATH.
    pause
    exit /b 1
)

if not exist "vendor\autoload.php" (
    echo [SETUP] Menginstall dependency Composer...
    composer install
    if errorlevel 1 (
        echo [ERROR] composer install gagal.
        pause
        exit /b 1
    )
)

if not exist ".env" (
    echo [SETUP] Membuat file .env dari .env.example...
    copy ".env.example" ".env" >nul
    php artisan key:generate
)

if not exist "public\build\manifest.json" (
    echo [SETUP] Build asset frontend...
    npm install
    npm run build
)

echo [SETUP] Menjalankan migrasi database...
php artisan migrate --force

echo [SETUP] Memastikan storage link aktif...
php artisan storage:link

echo [SETUP] Membersihkan cache development...
php artisan optimize:clear

echo.
echo ==================================================
echo  GreenPrinting aktif di http://127.0.0.1:8000
echo ==================================================
echo.

start "" "http://127.0.0.1:8000"
php artisan serve --host=127.0.0.1 --port=8000

pause

@echo off
:: ================================================
:: Okenet BMS Monitoring — Auto Installer (Windows)
:: ================================================
:: Jalankan sebagai Administrator jika perlu
:: Klik kanan -> Run as Administrator

title Okenet BMS Monitoring Installer
color 0A

echo.
echo =========================================
echo   Okenet BMS Monitoring -- Installer
echo =========================================
echo.

:: --- Cek PHP ---
php -v >nul 2>&1
if errorlevel 1 (
    echo [ERROR] PHP tidak ditemukan. Install PHP 8.2+ dan tambahkan ke PATH.
    pause
    exit /b 1
)

:: --- Cek Composer ---
composer -v >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Composer tidak ditemukan. Download dari https://getcomposer.org
    pause
    exit /b 1
)

echo [OK] PHP dan Composer ditemukan.

:: --- Install dependencies ---
echo.
echo [1/5] Menginstall Composer dependencies...
composer install --optimize-autoloader --no-dev
echo [OK] Dependencies installed.

:: --- Setup .env ---
echo.
echo [2/5] Setup file konfigurasi .env...
if not exist ".env" (
    copy .env.example .env
    echo [OK] File .env dibuat.
) else (
    echo [SKIP] File .env sudah ada.
)

:: --- Generate key ---
echo.
echo [3/5] Generate application key...
php artisan key:generate --force
echo [OK] App key generated.

:: --- Konfigurasi database ---
echo.
echo [4/5] Konfigurasi Database MySQL...
echo Masukkan detail koneksi database:
echo.

set /p DB_HOST="  DB Host [127.0.0.1]: "
if "%DB_HOST%"=="" set DB_HOST=127.0.0.1

set /p DB_PORT="  DB Port [3306]: "
if "%DB_PORT%"=="" set DB_PORT=3306

set /p DB_DATABASE="  DB Name [okenet_bms]: "
if "%DB_DATABASE%"=="" set DB_DATABASE=okenet_bms

set /p DB_USERNAME="  DB Username [root]: "
if "%DB_USERNAME%"=="" set DB_USERNAME=root

set /p DB_PASSWORD="  DB Password: "

:: Update .env (ganti nilai DB)
php -r "
$env = file_get_contents('.env');
$env = preg_replace('/DB_HOST=.*/', 'DB_HOST=%DB_HOST%', $env);
$env = preg_replace('/DB_PORT=.*/', 'DB_PORT=%DB_PORT%', $env);
$env = preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE=%DB_DATABASE%', $env);
$env = preg_replace('/DB_USERNAME=.*/', 'DB_USERNAME=%DB_USERNAME%', $env);
$env = preg_replace('/DB_PASSWORD=.*/', 'DB_PASSWORD=%DB_PASSWORD%', $env);
file_put_contents('.env', $env);
echo 'Konfigurasi database disimpan.';
"
echo [OK] Database dikonfigurasi.

:: --- Migrate ---
echo.
echo [5/5] Menjalankan database migrations...
php artisan migrate --force

:: --- User admin ---
echo.
echo Membuat user admin...
set /p ADMIN_EMAIL="  Email admin [admin@example.com]: "
if "%ADMIN_EMAIL%"=="" set ADMIN_EMAIL=admin@example.com
set /p ADMIN_PASSWORD="  Password admin: "
if "%ADMIN_PASSWORD%"=="" set ADMIN_PASSWORD=password123

php artisan tinker --execute="use App\Models\User; User::firstOrCreate(['email'=>'%ADMIN_EMAIL%'],['name'=>'Admin','password'=>bcrypt('%ADMIN_PASSWORD%')]); echo 'Admin dibuat.';"

:: --- Storage & cache ---
php artisan storage:link 2>nul
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo.
echo =========================================
echo   INSTALASI SELESAI!
echo =========================================
echo.
echo   Jalankan server  : php artisan serve
echo   Buka browser     : http://localhost:8000
echo   Login email      : %ADMIN_EMAIL%
echo.
echo   Langkah selanjutnya:
echo   1. Edit .env sesuaikan APP_URL dengan domain Anda
echo   2. Flash ESP32 dari folder esp32_bms_jk_protocol_native/
echo.
pause

#!/bin/bash
# ================================================
# Okenet BMS Monitoring — Auto Installer
# ================================================
# Usage: bash install.sh

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo ""
echo -e "${CYAN}=========================================${NC}"
echo -e "${CYAN}   Okenet BMS Monitoring — Installer    ${NC}"
echo -e "${CYAN}=========================================${NC}"
echo ""

# --- 1. Cek dependencies ---
echo -e "${YELLOW}[1/7] Mengecek dependencies...${NC}"
command -v php >/dev/null 2>&1 || { echo -e "${RED}❌ PHP tidak ditemukan. Install PHP 8.2+ terlebih dahulu.${NC}"; exit 1; }
command -v composer >/dev/null 2>&1 || { echo -e "${RED}❌ Composer tidak ditemukan. Install Composer terlebih dahulu.${NC}"; exit 1; }
command -v mysql >/dev/null 2>&1 || echo -e "${YELLOW}⚠️  MySQL client tidak ditemukan — pastikan database sudah siap.${NC}"
echo -e "${GREEN}✅ Dependencies OK (PHP: $(php -r 'echo PHP_VERSION;'))${NC}"

# --- 2. Install PHP dependencies ---
echo ""
echo -e "${YELLOW}[2/7] Menginstall Composer dependencies...${NC}"
composer install --optimize-autoloader --no-dev
echo -e "${GREEN}✅ Composer dependencies installed${NC}"

# --- 3. Setup .env ---
echo ""
echo -e "${YELLOW}[3/7] Setup file konfigurasi .env...${NC}"
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo -e "${GREEN}✅ File .env dibuat dari .env.example${NC}"
else
    echo -e "${YELLOW}⚠️  File .env sudah ada — tidak ditimpa${NC}"
fi

# --- 4. Generate App Key ---
echo ""
echo -e "${YELLOW}[4/7] Generate application key...${NC}"
php artisan key:generate --force
echo -e "${GREEN}✅ App key generated${NC}"

# --- 5. Tanya konfigurasi database ---
echo ""
echo -e "${YELLOW}[5/7] Konfigurasi Database MySQL...${NC}"
echo -e "${CYAN}Masukkan detail koneksi database MySQL:${NC}"

read -p "  DB Host [127.0.0.1]: " DB_HOST
DB_HOST=${DB_HOST:-127.0.0.1}

read -p "  DB Port [3306]: " DB_PORT
DB_PORT=${DB_PORT:-3306}

read -p "  DB Name [okenet_bms]: " DB_DATABASE
DB_DATABASE=${DB_DATABASE:-okenet_bms}

read -p "  DB Username [root]: " DB_USERNAME
DB_USERNAME=${DB_USERNAME:-root}

read -s -p "  DB Password: " DB_PASSWORD
echo ""

# Update .env dengan nilai database
sed -i "s|DB_HOST=.*|DB_HOST=${DB_HOST}|" .env
sed -i "s|DB_PORT=.*|DB_PORT=${DB_PORT}|" .env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE}|" .env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME}|" .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|" .env

echo -e "${GREEN}✅ Konfigurasi database disimpan ke .env${NC}"

# --- 6. Run migrations ---
echo ""
echo -e "${YELLOW}[6/7] Menjalankan database migrations...${NC}"
php artisan migrate --force
echo -e "${GREEN}✅ Migrations selesai${NC}"

# --- 7. Buat user admin ---
echo ""
echo -e "${YELLOW}[7/7] Membuat user admin...${NC}"
read -p "  Email admin [admin@example.com]: " ADMIN_EMAIL
ADMIN_EMAIL=${ADMIN_EMAIL:-admin@example.com}
read -s -p "  Password admin: " ADMIN_PASSWORD
echo ""
ADMIN_PASSWORD=${ADMIN_PASSWORD:-password123}

php artisan tinker --execute="
use App\Models\User;
User::firstOrCreate(
    ['email' => '${ADMIN_EMAIL}'],
    ['name' => 'Admin', 'password' => bcrypt('${ADMIN_PASSWORD}')]
);
echo 'User admin dibuat: ${ADMIN_EMAIL}';
"
echo -e "${GREEN}✅ User admin dibuat${NC}"

# --- Storage link ---
php artisan storage:link 2>/dev/null || true

# --- Cache clear ---
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo ""
echo -e "${CYAN}=========================================${NC}"
echo -e "${GREEN}✅ INSTALASI SELESAI!${NC}"
echo -e "${CYAN}=========================================${NC}"
echo ""
echo -e "  URL aplikasi  : ${CYAN}$(grep APP_URL .env | cut -d= -f2)${NC}"
echo -e "  Login email   : ${CYAN}${ADMIN_EMAIL}${NC}"
echo ""
echo -e "${YELLOW}Langkah selanjutnya:${NC}"
echo -e "  1. Edit .env — sesuaikan APP_URL dengan domain Anda"
echo -e "  2. Jalankan: ${CYAN}php artisan serve${NC} (untuk development)"
echo -e "  3. Flash firmware ESP32 dari folder esp32_bms_jk_protocol_native/"
echo ""

# Panduan Instalasi — Shared Hosting (cPanel)

**Okenet BMS Monitoring** dapat diinstall di shared hosting Indonesia seperti:
Niagahoster, IDCloudHost, DomaiNesia, Hostinger, dsb.

---

## Persyaratan Minimum Hosting

| Komponen | Minimum |
|----------|---------|
| PHP | 8.2+ |
| MySQL/MariaDB | 5.7+ / 10.3+ |
| Ekstensi PHP | PDO, PDO_MySQL, OpenSSL, Mbstring, cURL, JSON, Tokenizer |
| Storage | 500 MB |
| Akses folder | Bisa set `storage/` dan `bootstrap/cache/` writable |

> Cek di cPanel → **PHP Selector** atau tanya ke support hosting apakah PHP 8.2 tersedia.

---

## Langkah Instalasi

### 1. Download Source Code

Download file `.zip` dari GitHub:
```
https://github.com/USERNAME/okenet-bms-monitoring/releases
```
Atau klik tombol **Code → Download ZIP** di halaman repository.

---

### 2. Upload dan Ekstrak ke Hosting

**Via cPanel File Manager:**

1. Login ke **cPanel** hosting Anda.
2. Buka **File Manager**.
3. Masuk ke folder root domain/subdomain Anda (misal `public_html/` atau `biling.okebil.net/`).
4. Klik **Upload** → upload file `.zip`.
5. Klik kanan pada file `.zip` → **Extract**.

> **⚠️ PENTING: FOLDER VENDOR**
> Jika Anda mendownload langsung dari GitHub, folder `vendor` **tidak ada**. Anda harus:
> - **Opsi A (SSH):** Jalankan `composer install --no-dev` di folder project.
> - **Opsi B (Manual):** Jalankan `composer install` di laptop Anda, lalu zip dan upload folder `vendor` ke hosting.

---

### 3. Set Document Root (WAJIB)

Agar Laravel berjalan dengan benar dan aman, Anda **HARUS** mengarahkan domain ke folder `public`.

**Cara di cPanel:**
1. Buka menu **Domains** (atau Subdomains).
2. Klik **Manage** pada domain yang digunakan.
3. Cari kolom **Document Root**.
4. Tambahkan `/public` di bagian akhir path.
   - Contoh: `/web.kamu.com` → `/web.kamu.com/public`
5. Klik **Update**.

1. Login ke **cPanel**
2. Buka **MySQL Databases** (atau MySQL Database Wizard)
3. Buat database baru, contoh: `user_bms`
4. Buat user MySQL baru, contoh: `user_dbuser` dengan password
5. **Tambahkan user ke database** dengan hak akses **ALL PRIVILEGES**
6. Catat: nama database, username, password, hostname (biasanya `localhost`)

---

### 4. Buat Database Baru dengan AUTO INSTALLER.

> **Cara termudah:** Buka browser, ketik URL Anda lalu `/install.php`
> Contoh: `https://web.kamu.com/install.php`

Installer akan memandu Anda langkah demi langkah:

| Langkah | Yang Dilakukan |
|---------|----------------|
| **Step 1** | Cek persyaratan PHP otomatis |
| **Step 2** | Input konfigurasi database |
| **Step 3** | Buat akun admin |
| **Step 4** | Konfirmasi dan mulai instalasi |
| **Step 5** | Selesai! Buka aplikasi |

---

### 5. Atur Permission Folder (Jika Diperlukan)

Jika installer mendeteksi masalah permission, set via cPanel **File Manager**:

1. Klik kanan folder `storage/` → **Change Permissions**
2. Set ke `755` ✓ atau `775` jika 755 tidak cukup
3. Centang **Recurse into subdirectories**
4. Ulangi untuk folder `bootstrap/cache/`

**Atau via SSH (jika tersedia):**
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

---

### 6. Hapus Installer Setelah Selesai ⚠️

**WAJIB** untuk keamanan:

1. Buka **cPanel File Manager**
2. Masuk ke `public_html/public/`
3. Hapus file `install.php`

---

### 7. Konfigurasi Domain / Subdomain

Untuk menggunakan domain atau subdomain (misal `bms.domain.com`):

**Opsi A — Arahkan Subdomain ke Folder `public/`:**
1. Di cPanel → **Subdomains**
2. Buat subdomain `bms.domain.com`
3. Set Document Root ke `/home/user/public_html/namaproyek/public`

**Opsi B — Gunakan `.htaccess` di root:**

Buat file `.htaccess` di folder root project (`public_html/`):
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

## Konfigurasi ESP32

Setelah website berhasil dipasang, update firmware ESP32:

```cpp
// esp32_bms_jk_protocol_native.ino
const char* ssid      = "NAMA_WIFI_ANDA";
const char* password  = "PASSWORD_WIFI";
const char* serverURL = "https://bms.domain.com/api/monitor/store";
const char* deviceId  = "ESP32-001";
```

---

## Login Default

| Field | Nilai |
|-------|-------|
| **URL** | `https://web.kamu.com/login` |
| **Email** | `bms@okebil.com` |
| **Password** | `123456789` |

> ⚠️ **Segera ganti password** setelah login pertama di menu profil!

---

## Troubleshooting Shared Hosting

### Error 500 saat buka website
- **Cek Folder `vendor`**: Pastikan folder `vendor` sudah ada dan berisi library. Jika download dari GitHub, folder ini kosong secara default.
- **Cek Versi PHP**: Pastikan PHP 8.2+ sudah aktif.
- **Cek APP_KEY**: Pastikan file `.env` sudah memiliki `APP_KEY`.
- **Permissions**: Pastikan `storage/` dan `bootstrap/cache/` permission `755`.

### Installer tidak bisa tulis .env
- Pastikan folder root project bisa ditulis (755)
- Jika error, edit `.env.example`, salin menjadi `.env`, isi manual sesuai data DB

### "Tokenmismatch" / Session Error
- Pastikan `storage/framework/sessions/` bisa ditulis
- Coba bersihkan cookie browser

### Database import gagal
- Import manual via phpMyAdmin: buka file `database/schema.sql` lalu import ke database Anda
- Kemudian jalankan installer lagi

### Halaman masih kosong / 404
- Pastikan `.htaccess` di folder `public/` tidak terhapus
- Aktifkan `mod_rewrite` di hosting (biasanya sudah aktif di shared hosting)

---

## FAQ

**Q: Apakah bisa tanpa SSH?**
A: Ya! Semua bisa dilakukan via cPanel + browser. SSH tidak dibutuhkan.

**Q: Apakah perlu install Composer?**
A: Tidak. File `vendor/` sudah disertakan dalam paket `.zip`.

**Q: Apakah bisa diinstall di subdomain?**
A: Ya, ikuti Opsi A di bagian Konfigurasi Domain.

**Q: Shared hosting apa yang direkomendasikan?**
A: Niagahoster, IDCloudHost, DomaiNesia — semua sudah support PHP 8.2+.

---

*Okenet BMS Monitoring — Instalasi Shared Hosting*
*Versi dokumen: 2026-02-19*

# Okenet BMS Monitoring

**Sistem monitoring Battery Management System (JK-BMS) berbasis web menggunakan ESP32 dan Laravel.**

Monitor kondisi baterai secara realtime dari browser — tegangan, arus, SOC, suhu, sel, dan alarm — melalui ESP32 yang terhubung ke JK-BMS via port GPS (UART TTL).

---

## Fitur

- **Dashboard Realtime** — tegangan pack, arus, power, SOC, suhu, siklus, balance
- **Cell Monitor** — tegangan per-sel dengan visualisasi bar
- **Alarm System** — deteksi alarm berdasarkan flag bit JK-BMS
- **BMS Settings** — baca dan tulis parameter BMS (proteksi, balance, kontrol)
- **Indikator Online/Offline** — status koneksi ESP32 secara realtime
- **LED RX/TX** — indikator fisik komunikasi Modbus
- **Auto-refresh** — dashboard update otomatis setiap 8 detik
- **Responsive** — tampilan optimal di desktop dan mobile

## Stack Teknologi

| Komponen | Teknologi |
|----------|-----------|
| Web Backend | Laravel 12 + Livewire 4 |
| Frontend | Tailwind CSS, Alpine.js |
| Database | MySQL / MariaDB |
| Mikrokontroler | ESP32 Dev Module |
| Protokol BMS | Modbus RTU via UART TTL |
| BMS | JK-BMS (via port GPS) |

---

## Kebutuhan Hardware

| Komponen | Keterangan |
|----------|------------|
| ESP32 Dev Module | Mikrokontroler utama |
| JK-BMS | BMS yang mendukung Modbus RTU |
| LED 3mm Kuning | Indikator TX (GPIO18) |
| LED 3mm Hijau | Indikator RX (GPIO19) |
| Resistor 147Ω × 2 | Pembatas arus LED |
| Passive Buzzer | Alarm (GPIO4) |

## Wiring ESP32 ↔ JK-BMS (Port GPS)

```
JK-BMS GPS Port          ESP32
─────────────────         ──────────────
GND      ─────────────── GND
TX       ─────────────── GPIO16 (RX2)
RX       ─────────────── GPIO17 (TX2)
VCC      ── NC (tidak dihubungkan)

GPIO4  ── Buzzer (+) passive
GPIO18 ── [147Ω] ── LED Kuning (+) ── GND
GPIO19 ── [147Ω] ── LED Hijau  (+) ── GND
```

> Lihat dokumentasi lengkap di [`docs/wiring-jkbms-esp32.md`](docs/wiring-jkbms-esp32.md)

---

## Instalasi

### Kebutuhan Server

- PHP 8.2+
- Composer
- MySQL 8.0+ / MariaDB 10.4+
- Web server (Apache/Nginx) atau `php artisan serve` untuk development

### Linux / macOS

```bash
git clone https://github.com/USERNAME/okenet-bms-monitoring.git
cd okenet-bms-monitoring
bash install.sh
```

### Windows

```bat
git clone https://github.com/USERNAME/okenet-bms-monitoring.git
cd okenet-bms-monitoring
install.bat
```

### Manual (semua OS)

```bash
# 1. Clone repository
git clone https://github.com/USERNAME/okenet-bms-monitoring.git
cd okenet-bms-monitoring

# 2. Install dependencies
composer install --optimize-autoloader --no-dev

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env
#    Edit: DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 5. Jalankan migration
php artisan migrate --force

# 6. Buat user admin
php artisan tinker
>>> User::create(['name'=>'Admin','email'=>'admin@example.com','password'=>bcrypt('password')]);

# 7. Jalankan server
php artisan serve
```

---

## Konfigurasi ESP32

1. Buka folder `esp32_bms_jk_protocol_native/`
2. Buka file `esp32_bms_jk_protocol_native.ino` di Arduino IDE
3. Sesuaikan konfigurasi di bagian atas file:

```cpp
const char* ssid      = "NAMA_WIFI_ANDA";
const char* password  = "PASSWORD_WIFI";
const char* serverURL = "https://domain-anda.com/api/monitor/store";
const char* deviceId  = "ESP32-001";
```

4. Upload ke ESP32 (Board: **ESP32 Dev Module**, Baud Upload: **921600**)
5. Buka Serial Monitor (115200 baud) untuk verifikasi

### Library Arduino yang Dibutuhkan

Install via Arduino Library Manager:

| Library | Versi |
|---------|-------|
| `ModbusMaster` | ^2.0.1 |
| `ArduinoJson` | ^7.x |

---

## API Endpoint

ESP32 berkomunikasi dengan server via HTTPS POST:

```
POST /api/monitor/store
Content-Type: application/json

{
  "device_id": "ESP32-001",
  "bms": {
    "battery_voltage": 28.05,
    "current": 10.5,
    "soc": 95,
    "temperature": 25.0,
    ...
  }
}
```

---

## Screenshot

| Dashboard | BMS Settings |
|-----------|-------------|
| ![Dashboard](docs/screenshot-dashboard.png) | ![Settings](docs/screenshot-settings.png) |

---

## Struktur Project

```
okenet-bms-monitoring/
├── app/
│   ├── Http/Controllers/Api/   # API untuk ESP32
│   ├── Livewire/               # Dashboard, Settings, Control
│   └── Models/                 # MonitorLog, BmsCommandQueue, dll
├── database/migrations/        # Skema database
├── docs/                       # Dokumentasi wiring
├── esp32_bms_jk_protocol_native/  # Firmware ESP32
│   └── esp32_bms_jk_protocol_native.ino
├── resources/views/livewire/   # Blade views
├── routes/
│   ├── web.php
│   └── api.php
├── install.sh                  # Installer Linux/macOS
├── install.bat                 # Installer Windows
└── README.md
```

---

## Kontribusi

Pull request dan issue sangat diterima! Silakan fork dan kembangkan sesuai kebutuhan.

1. Fork repository ini
2. Buat branch baru: `git checkout -b fitur-baru`
3. Commit: `git commit -m "Tambah fitur baru"`
4. Push: `git push origin fitur-baru`
5. Buat Pull Request

---

## Lisensi

MIT License — bebas digunakan untuk proyek personal maupun komersial.

---

*Dikembangkan untuk monitoring JK-BMS LiFePO4 via ESP32*

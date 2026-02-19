# Wiring JK-BMS ↔ ESP32
**Okenet BMS Monitoring System**
*Protokol: Modbus RTU via UART TTL (Port GPS JK-BMS) | Baud: 115200*

---

## 🧩 Komponen yang Dibutuhkan

| No | Komponen | Keterangan |
|----|----------|------------|
| 1 | ESP32 Dev Module | Mikrokontroler utama |
| 2 | JK-BMS | Battery Management System |
| 3 | LED 3mm Kuning | Indikator TX |
| 4 | LED 3mm Hijau | Indikator RX |
| 5 | Resistor 147 Ω × 2 | Pembatas arus LED |
| 6 | Kabel dupont / jumper | untuk koneksi |

> Port GPS JK-BMS menggunakan UART TTL sehingga langsung terhubung ke ESP32.

---

## 🔌 Port GPS di JK-BMS

Port GPS pada JK-BMS adalah konektor kecil (biasanya **JST 1.25mm 4-pin** atau **JST-GH 4-pin**):

```
JK-BMS GPS Port (4 pin):
┌─────┬───────────────────────────────────┐
│ Pin │ Fungsi                            │
├─────┼───────────────────────────────────┤
│  1  │ VCC (5V output dari BMS)          │
│  2  │ RX  (Data masuk ke BMS)           │
│  3  │ TX  (Data keluar dari BMS)        │
│  4  │ GND                               │
└─────┴───────────────────────────────────┘

⚠️ Urutan pin bisa berbeda tergantung model — cek PCB BMS atau datasheet
   Label umum: V+ / RXD / TXD / GND  atau  5V / RX / TX / GND
```

---

## Diagram Koneksi

```
JK-BMS                                    ESP32
(Port GPS)                                (Dev Module)
──────────                                ──────────
  GND  ──────────────────────────────────  GND
  TX   ──────────────────────────────────  GPIO16 (RX2)
  RX   ──────────────────────────────────  GPIO17 (TX2)
  VCC  ──── (opsional, jangan ke 3.3V!) ──  ⚠️ Lihat catatan
```

> ⚠️ **PENTING — Soal VCC:**
> - Pin VCC GPS port mengeluarkan **5V** dari BMS
> - **JANGAN** hubungkan ke pin 3.3V ESP32 (merusak regulator)
> - Biarkan **tidak terhubung** (NC), ESP32 punya catu daya sendiri
> - GND **harus** terhubung ke GND ESP32

---

## Pinout ESP32 Lengkap

```
┌─────────────────────────────────────────┐
│              ESP32 Dev Module           │
│                                         │
│  GPIO16 (RX2) ◄── JK-BMS GPS TX         │ ← Menerima data dari BMS
│  GPIO17 (TX2) ──► JK-BMS GPS RX         │ ← Mengirim command ke BMS
│                                         │
│  GPIO4        ──► Buzzer (+)            │ ← Alarm & notifikasi
│                                         │
│  GPIO18       ──► [147Ω] ──▶|── GND     │ ← LED Kuning (TX)
│  GPIO19       ──► [147Ω] ──▶|── GND     │ ← LED Hijau  (RX)
│                                         │
│  GND          ──── JK-BMS GPS GND       │
│                                         │
└─────────────────────────────────────────┘
```

---

##  Wiring LED Indikator RX/TX

```
ESP32 GPIO18 ──┬── [147Ω] ──▶|── GND
               │    Resistor   LED Kuning
               └── Nyala saat ESP32 KIRIM request ke BMS

ESP32 GPIO19 ──┬── [147Ω] ──▶|── GND
               │    Resistor   LED Hijau
               └── Flash saat ESP32 TERIMA response dari BMS
```

### Perhitungan Resistor LED
```
V_supply = 3.3V (GPIO ESP32)
V_f LED  = 2.0V (LED Kuning/Hijau 3mm tipikal)
R = (3.3 - 2.0) / 0.009 ≈ 144Ω → pakai 147Ω ✅
Arus aktual = (3.3 - 2.0) / 147 ≈ 8.8 mA  ← Aman & terang
```

---

##  Wiring Buzzer

```
ESP32 GPIO4 ──── Buzzer (+) ──── Buzzer (-) ──── GND
                 (passive buzzer, 3.3V compatible)
```

---

##  Skema Sistem Lengkap

```
┌──────────────────────────────────┐
│          JK-BMS                  │
│                                  │
│  ┌──────────────────────┐        │
│  │  Port GPS (4-pin)    │        │
│  │  GND ─────────────────────────│────────────────── GND
│  │  TX  ─────────────────────────│────────────────── GPIO16 (RX2) ─┐
│  │  RX  ─────────────────────────│────────────────── GPIO17 (TX2) ─┤
│  │  VCC ── NC (tidak dihubungkan)│                                  │
│  └──────────────────────┘        │                           ┌──────┴──────┐
└──────────────────────────────────┘                           │   ESP32     │
                                                               │             │
                                                               │  GPIO4  ───►│── Buzzer
                                                               │  GPIO18 ───►│──[147Ω]──▶|── GND (Kuning)
                                                               │  GPIO19 ───►│──[147Ω]──▶|── GND (Hijau)
                                                               │             │
                                                               └──────┬──────┘
                                                                      │ WiFi HTTPS POST
                                                                      ▼
                                                            bms.okebil.com/api/monitor/store
```

---

##  Konfigurasi Firmware

```cpp
// esp32_bms_jk_protocol_native.ino

#define BMS_BAUDRATE    115200    // Baudrate komunikasi BMS (via GPS port)
#define BMS_RX_PIN      16        // UART2 RX2 — menerima dari GPS TX
#define BMS_TX_PIN      17        // UART2 TX2 — mengirim ke GPS RX
#define BMS_SLAVE_ID    1         // Modbus Slave ID JK-BMS (default: 1)

#define BUZZER_PIN      4         // GPIO buzzer
#define LED_TX_PIN      18        // LED kuning TX
#define LED_RX_PIN      19        // LED hijau RX

// Gunakan HardwareSerial UART2
HardwareSerial SerialBMS(2);
ModbusMaster nodeBMS;

// Setup di setup():
SerialBMS.begin(BMS_BAUDRATE, SERIAL_8N1, BMS_RX_PIN, BMS_TX_PIN);
nodeBMS.begin(BMS_SLAVE_ID, SerialBMS);
```

---


*Dokumen ini dibuat untuk proyek Okenet BMS Monitoring*
*Last updated: 2026-02-19*

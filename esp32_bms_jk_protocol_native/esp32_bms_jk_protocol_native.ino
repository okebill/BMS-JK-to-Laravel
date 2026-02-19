/*
 * ESP32 JK-BMS Full Sync dengan Laravel
 * 
 * Board: ESP32 Dev Module
 * 
 * Wiring:
 *   BMS  (UART2): RX=GPIO16, TX=GPIO17
 *   Buzzer: GPIO4
 * 
 * JK-BMS Modbus Register Map (Realtime - 0x1200+):
 *   0x1200 - 0x123F : Cell Voltages (max 32 cells, 16-bit mV each)
 *   0x1240 - 0x127F : Cell Resistance (max 32 cells, x0.001 mΩ)
 *   0x1290 (x2)     : Total Voltage (U_DWORD x0.001 V)
 *   0x1294 (x2)     : Power (S_DWORD x0.001 W)
 *   0x1298 (x2)     : Current (S_DWORD x0.001 A)
 *   0x129C (x1)     : Temperature Sensor 1 (S_WORD x0.1 °C)
 *   0x129E (x1)     : Temperature Sensor 2 (S_WORD x0.1 °C)
 *   0x12A0 (x2)     : Alarm Flags (U_DWORD - bit flags)
 *   0x12A4 (x2)     : Balance Current (S_DWORD x0.001 A)
 *   0x12A6 (x1)     : SOC (U_WORD %)
 *   0x12A8 (x2)     : Remaining Capacity (U_DWORD x0.001 Ah)
 *   0x12AC (x2)     : Total Capacity Nominal (U_DWORD x0.001 Ah)
 *   0x12B0 (x2)     : Cycle Count (U_DWORD - count)
 *   0x12B4 (x2)     : Total Cycle Capacity (U_DWORD x0.001 Ah)
 *   0x12B8 (x1)     : Battery Status Flags (U_WORD - bit flags)
 *   0x12BC (x1)     : Charge/Discharge MOS Status (U_WORD)
 * 
 * Library: ModbusMaster, ArduinoJson, WiFi, HTTPClient
 */

// ==================== FEATURE FLAGS ====================
// Ubah ke 1 untuk mengaktifkan kembali modul Inverter Samoto
#define USE_INVERTER 0

#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <ModbusMaster.h>

// ==================== Configuration ====================
const char* ssid       = "eglobaltech.id";
const char* password   = "14081992";
const char* serverURL  = "https://bms.okebil.com/api/monitor/store";
const char* logURL     = "https://bms.okebil.com/api/monitor/log";
const char* deviceId   = "ESP32-001";
const char* apiKey     = "";

#define BMS_BAUDRATE    115200
#define BMS_RX_PIN      16
#define BMS_TX_PIN      17
#define BMS_SLAVE_ID    1

#if USE_INVERTER
#define INVERTER_BAUDRATE   9600
#define INVERTER_RX_PIN     14
#define INVERTER_TX_PIN     27
#define INVERTER_SLAVE_ID   1
#define INVERTER_DE_RE_PIN  26
#endif

#define BUZZER_PIN      4

#define LED_TX_PIN      18   // LED Kuning — nyala saat kirim ke BMS
#define LED_RX_PIN      19   // LED Hijau  — nyala saat terima dari BMS

#define BMS_REQUEST_INTERVAL        8000
#define BMS_SETTINGS_READ_INTERVAL  60000
#define WIFI_RECONNECT_INTERVAL     30000
#if USE_INVERTER
#define INVERTER_REQUEST_INTERVAL   5000
#endif

// ==================== Objects ====================
HardwareSerial SerialBMS(2);
#if USE_INVERTER
HardwareSerial SerialInverter(1);
#endif
ModbusMaster nodeBMS;

// ==================== State ====================
unsigned long lastBMSRequest       = 0;
unsigned long lastBMSSettingsRead  = 0;
unsigned long lastWiFiCheck        = 0;
#if USE_INVERTER
unsigned long lastInverterRequest  = 0;
#endif
bool wifiConnected     = false;
bool wifiMelodyPlayed  = false;
bool bmsSettingsValid  = false;
DynamicJsonDocument* settingsDocPtr = NULL;

// ==================== BMS Realtime Data (LENGKAP) ====================
struct BMSData {
  // --- Cell Voltages ---
  float     cellVoltages[32];
  uint8_t   cellCount;

  // --- Cell Resistance ---
  float     cellResistance[32];   // mΩ
  bool      cellResistanceValid;

  // --- Pack ---
  float     totalVoltage;         // V
  float     power;                // W (+ charge, - discharge)
  float     current;              // A (+ charge, - discharge)

  // --- Temperature ---
  float     temp1;                // °C Sensor 1
  float     temp2;                // °C Sensor 2

  // --- Alarm & Status ---
  uint32_t  alarmFlags;           // bit flags
  uint16_t  batteryStatus;        // bit flags
  uint16_t  mosfetStatus;         // 0=off, 1=charge only, 2=discharge only, 3=both

  // --- Balance ---
  float     balanceCurrent;       // A

  // --- SOC/Capacity ---
  uint8_t   soc;                  // %
  float     remainingCapacity;    // Ah
  float     nominalCapacity;      // Ah

  // --- Cycle ---
  uint32_t  cycleCount;           // jumlah siklus
  float     totalCycleCapacity;   // Ah kumulatif

  bool      valid;
} bmsData = {};

// --- Alarm flag decode ---
String decodeAlarmFlags(uint32_t flags) {
  if (flags == 0) return "OK";
  String r = "";
  if (flags & (1<<0))  r += "LowCapacity ";
  if (flags & (1<<1))  r += "MosTempHigh ";
  if (flags & (1<<2))  r += "ChargeTempHigh ";
  if (flags & (1<<3))  r += "DischargeTempHigh ";
  if (flags & (1<<4))  r += "ChargeTempLow ";
  if (flags & (1<<5))  r += "DischargeTempLow ";
  if (flags & (1<<6))  r += "ChargeOvercurrent ";
  if (flags & (1<<7))  r += "DischargeOvercurrent ";
  if (flags & (1<<8))  r += "CellOvervoltage ";
  if (flags & (1<<9))  r += "CellUndervoltage ";
  if (flags & (1<<10)) r += "WireResistanceHigh ";
  if (flags & (1<<11)) r += "CellCountMismatch ";
  if (flags & (1<<16)) r += "ChargeMosFault ";
  if (flags & (1<<17)) r += "DischargeMosFault ";
  r.trim();
  return r;
}

String decodeMosfetStatus(uint16_t s) {
  switch(s) {
    case 0: return "OFF";
    case 1: return "Charge Only";
    case 2: return "Discharge Only";
    case 3: return "Charge+Discharge";
    default: return "Unknown(" + String(s) + ")";
  }
}

#if USE_INVERTER
// ==================== Inverter Data ====================
struct InverterData {
  uint16_t  gridVoltage;
  float     gridFrequency;
  uint16_t  acOutputVoltage;
  float     acOutputFrequency;
  uint16_t  acOutputPowerVA;
  uint16_t  acOutputPowerWatt;
  uint16_t  outputLoadPercent;
  uint16_t  busVoltage;
  float     batteryVoltage;
  float     batteryChargeCurrent;
  uint16_t  batteryCapacity;
  uint16_t  heatSinkTemp;
  float     solarCurrent;
  float     solarVoltage;
  float     batteryVoltSCC;
  float     batteryDischargeCurrent;
  uint16_t  deviceStatusBits;
  uint16_t  solarPowerWatt;
  String    mode;
  bool      online;
  unsigned long lastReadTime;
} inverterData = {0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,"Unknown",false,0};
#endif

// ==================== Buzzer ====================
const int marioNotes[] = {
  659, 659, 0, 659, 0, 523, 659, 0, 784, 0, 0, 0, 392, 0, 0, 0,
  523, 0, 0, 392, 0, 0, 330, 0, 0, 440, 0, 494, 0, 466, 440, 0,
  392, 659, 784, 880, 0, 698, 784, 0, 659, 0, 523, 587, 494, 0, 0, 0
};
const int marioDur[] = {
  120, 120, 60, 120, 60, 120, 120, 60, 200, 60, 60, 60, 200, 60, 60, 60,
  200, 60, 60, 200, 60, 60, 200, 60, 60, 150, 60, 150, 60, 120, 150, 60,
  100, 100, 100, 150, 60, 120, 150, 60, 150, 60, 120, 120, 200, 60, 60, 60
};
const int marioLen = sizeof(marioNotes) / sizeof(marioNotes[0]);

void buzzerOn(int freq) {
  if (freq <= 0) { ledcWrite(BUZZER_PIN, 0); return; }
  ledcWriteTone(BUZZER_PIN, freq);
  ledcWrite(BUZZER_PIN, 128);
}
void buzzerOff() { ledcWrite(BUZZER_PIN, 0); }
void buzzerTone(int freq, int dur) {
  if (freq == 0) { buzzerOff(); delay(dur); return; }
  buzzerOn(freq); delay(dur); buzzerOff(); delay(15);
}
void playMarioTheme() {
  Serial.println("[BUZZER] Mario Bros Theme...");
  for (int i = 0; i < marioLen; i++) buzzerTone(marioNotes[i], marioDur[i]);
  buzzerOff();
}
void playErrorBeep() {
  for (int i = 0; i < 3; i++) { buzzerTone(800, 100); delay(100); }
}

// ==================== Modbus Callbacks ====================
void preBMS()  {
  while (SerialBMS.available()) SerialBMS.read();
  digitalWrite(LED_TX_PIN, HIGH);  // TX LED ON
  digitalWrite(LED_RX_PIN, LOW);
}
void postBMS() {
  delay(10);
  digitalWrite(LED_TX_PIN, LOW);   // TX LED OFF
  digitalWrite(LED_RX_PIN, HIGH);  // RX LED ON — response diterima
  delay(30);
  digitalWrite(LED_RX_PIN, LOW);   // RX LED OFF
}

// ==================== WiFi ====================
void connectWiFi() {
  if (WiFi.status() == WL_CONNECTED) {
    if (!wifiConnected) {
      wifiConnected = true;
      Serial.print("\nWiFi CONNECTED! IP: "); Serial.println(WiFi.localIP());
      if (!wifiMelodyPlayed) { playMarioTheme(); wifiMelodyPlayed = true; }
    }
    return;
  }
  wifiConnected = false; wifiMelodyPlayed = false;
  Serial.print("WiFi connecting: "); Serial.println(ssid);
  WiFi.mode(WIFI_STA); WiFi.begin(ssid, password);
  int a = 0;
  while (WiFi.status() != WL_CONNECTED && a < 30) { delay(500); Serial.print("."); a++; }
  Serial.println();
  if (WiFi.status() == WL_CONNECTED) {
    wifiConnected = true;
    Serial.print("WiFi OK! IP: "); Serial.println(WiFi.localIP());
    playMarioTheme(); wifiMelodyPlayed = true;
  } else {
    Serial.print("WiFi GAGAL. Status: "); Serial.println(WiFi.status());
  }
}

// ==================== Helper ====================
String modbusErr(uint8_t r) {
  switch(r) {
    case 0x00: return "OK";
    case 0x01: return "Illegal Function";
    case 0x02: return "Illegal Addr";
    case 0x03: return "Illegal Value";
    case 0xE0: return "CRC Error";
    case 0xE2: return "Timeout";
    default:   return "Err("+String(r)+")";
  }
}

// Helper baca 32-bit register (2 holding registers, Big-endian Hi-Lo)
uint32_t read32u(uint8_t idx) {
  return ((uint32_t)nodeBMS.getResponseBuffer(idx) << 16) | nodeBMS.getResponseBuffer(idx+1);
}
int32_t read32s(uint8_t idx) {
  return (int32_t)(((uint32_t)nodeBMS.getResponseBuffer(idx) << 16) | nodeBMS.getResponseBuffer(idx+1));
}

// ==================== BMS Realtime (LENGKAP) ====================
bool readBMSRealTimeData() {
  uint8_t r;
  memset(&bmsData, 0, sizeof(bmsData));

  // --- 1. Cell Voltages (0x1200, 32 regs, 16-bit mV) ---
  r = nodeBMS.readHoldingRegisters(0x1200, 32); delay(100);
  if (r == 0x00) {
    uint16_t firstReg = nodeBMS.getResponseBuffer(0);
    bmsData.cellCount = 0;

    if (firstReg >= 500 && firstReg <= 5000) {
      // Format 16-bit: 1 reg per cell
      for (int i = 0; i < 32; i++) {
        uint16_t cv = nodeBMS.getResponseBuffer(i);
        if (cv >= 500 && cv <= 5000) {
          bmsData.cellVoltages[bmsData.cellCount++] = cv * 0.001f;
        } else if (cv == 0 && bmsData.cellCount > 0) break;
      }
    } else {
      // Format 32-bit: 2 regs per cell
      for (int i = 0; i < 16; i++) {
        uint32_t raw = read32u(i*2);
        float cv = raw * 0.001f;
        if (cv > 0.5f && cv < 5.0f) {
          bmsData.cellVoltages[bmsData.cellCount++] = cv;
        } else if (raw == 0 && bmsData.cellCount > 0) break;
      }
    }
    Serial.print("[BMS] Cells: "); Serial.print(bmsData.cellCount);
    if (bmsData.cellCount > 0) {
      Serial.print(" | ");
      for (int i = 0; i < bmsData.cellCount; i++) {
        Serial.print("C"); Serial.print(i+1); Serial.print("=");
        Serial.print(bmsData.cellVoltages[i], 3); Serial.print("V ");
      }
    }
    Serial.println();
  } else {
    Serial.print("[BMS] Cell voltage read FAIL: "); Serial.println(modbusErr(r));
    return false;
  }

  // --- 2. Cell Resistance (0x1240, 32 regs, 16-bit x0.001 mΩ) ---
  r = nodeBMS.readHoldingRegisters(0x1240, 32); delay(100);
  if (r == 0x00) {
    bmsData.cellResistanceValid = true;
    for (int i = 0; i < 32 && i < bmsData.cellCount; i++) {
      bmsData.cellResistance[i] = nodeBMS.getResponseBuffer(i) * 0.001f;
    }
    Serial.print("[BMS] Cell Resistance (mΩ): ");
    for (int i = 0; i < bmsData.cellCount; i++) {
      Serial.print("R"); Serial.print(i+1); Serial.print("=");
      Serial.print(bmsData.cellResistance[i], 3); Serial.print(" ");
    }
    Serial.println();
  } else {
    Serial.print("[BMS] Cell resistance read FAIL: "); Serial.println(modbusErr(r));
    bmsData.cellResistanceValid = false;
  }

  // --- 3. Total Voltage (0x1290, 2 regs, U_DWORD x0.001 V) ---
  r = nodeBMS.readHoldingRegisters(0x1290, 2); delay(100);
  if (r == 0x00) {
    bmsData.totalVoltage = read32u(0) * 0.001f;
    Serial.print("[BMS] Total Voltage: "); Serial.print(bmsData.totalVoltage, 3); Serial.println(" V");
  } else {
    Serial.print("[BMS] Total Voltage FAIL: "); Serial.println(modbusErr(r));
    return false;
  }

  // --- 4. Power (0x1294, 2 regs, S_DWORD x0.001 W) ---
  r = nodeBMS.readHoldingRegisters(0x1294, 2); delay(100);
  if (r == 0x00) {
    bmsData.power = read32s(0) * 0.001f;
    Serial.print("[BMS] Power: "); Serial.print(bmsData.power, 2); Serial.println(" W");
  }

  // --- 5. Current (0x1298, 2 regs, S_DWORD x0.001 A) ---
  r = nodeBMS.readHoldingRegisters(0x1298, 2); delay(100);
  if (r == 0x00) {
    bmsData.current = read32s(0) * 0.001f;
    Serial.print("[BMS] Current: "); Serial.print(bmsData.current, 3); Serial.println(" A");
  }

  // --- 6. Temperature Sensor 1 (0x129C, 1 reg, S_WORD x0.1 °C) ---
  r = nodeBMS.readHoldingRegisters(0x129C, 1); delay(100);
  if (r == 0x00) {
    bmsData.temp1 = (int16_t)nodeBMS.getResponseBuffer(0) * 0.1f;
    Serial.print("[BMS] Temp1: "); Serial.print(bmsData.temp1, 1); Serial.println(" °C");
  }

  // --- 7. Temperature Sensor 2 (0x129E, 1 reg, S_WORD x0.1 °C) ---
  r = nodeBMS.readHoldingRegisters(0x129E, 1); delay(100);
  if (r == 0x00) {
    bmsData.temp2 = (int16_t)nodeBMS.getResponseBuffer(0) * 0.1f;
    Serial.print("[BMS] Temp2: "); Serial.print(bmsData.temp2, 1); Serial.println(" °C");
  }

  // --- 8. Alarm Flags (0x12A0, 2 regs, U_DWORD bit flags) ---
  r = nodeBMS.readHoldingRegisters(0x12A0, 2); delay(100);
  if (r == 0x00) {
    bmsData.alarmFlags = read32u(0);
    Serial.print("[BMS] Alarm Flags: 0x"); Serial.print(bmsData.alarmFlags, HEX);
    Serial.print(" -> "); Serial.println(decodeAlarmFlags(bmsData.alarmFlags));
  }

  // --- 9. Balance Current (0x12A4, 2 regs, S_DWORD x0.001 A) ---
  r = nodeBMS.readHoldingRegisters(0x12A4, 2); delay(100);
  if (r == 0x00) {
    bmsData.balanceCurrent = read32s(0) * 0.001f;
    Serial.print("[BMS] Balance Current: "); Serial.print(bmsData.balanceCurrent, 3); Serial.println(" A");
  }

  // --- 10. SOC (0x12A6, 1 reg, U_WORD %) ---
  r = nodeBMS.readHoldingRegisters(0x12A6, 1); delay(100);
  if (r == 0x00) {
    bmsData.soc = nodeBMS.getResponseBuffer(0) & 0xFF;
    Serial.print("[BMS] SOC: "); Serial.print(bmsData.soc); Serial.println(" %");
  }

  // --- 11. Remaining Capacity (0x12A8, 2 regs, U_DWORD x0.001 Ah) ---
  r = nodeBMS.readHoldingRegisters(0x12A8, 2); delay(100);
  if (r == 0x00) {
    bmsData.remainingCapacity = read32u(0) * 0.001f;
    Serial.print("[BMS] Remaining Capacity: "); Serial.print(bmsData.remainingCapacity, 2); Serial.println(" Ah");
  }

  // --- 12. Nominal Capacity (0x12AC, 2 regs, U_DWORD x0.001 Ah) ---
  r = nodeBMS.readHoldingRegisters(0x12AC, 2); delay(100);
  if (r == 0x00) {
    bmsData.nominalCapacity = read32u(0) * 0.001f;
    Serial.print("[BMS] Nominal Capacity: "); Serial.print(bmsData.nominalCapacity, 2); Serial.println(" Ah");
  }

  // --- 13. CYCLE COUNT (0x12B0, 2 regs, U_DWORD) *** PENTING *** ---
  r = nodeBMS.readHoldingRegisters(0x12B0, 2); delay(100);
  if (r == 0x00) {
    bmsData.cycleCount = read32u(0);
    Serial.print("[BMS] *** Cycle Count: "); Serial.print(bmsData.cycleCount); Serial.println(" cycles ***");
  } else {
    Serial.print("[BMS] Cycle Count FAIL: "); Serial.println(modbusErr(r));
  }

  // --- 14. Total Cycle Capacity (0x12B4, 2 regs, U_DWORD x0.001 Ah) ---
  r = nodeBMS.readHoldingRegisters(0x12B4, 2); delay(100);
  if (r == 0x00) {
    bmsData.totalCycleCapacity = read32u(0) * 0.001f;
    Serial.print("[BMS] Total Cycle Capacity: "); Serial.print(bmsData.totalCycleCapacity, 2); Serial.println(" Ah");
  }

  // --- 15. Battery Status Flags (0x12B8, 1 reg, U_WORD) ---
  r = nodeBMS.readHoldingRegisters(0x12B8, 1); delay(100);
  if (r == 0x00) {
    bmsData.batteryStatus = nodeBMS.getResponseBuffer(0);
    Serial.print("[BMS] Battery Status Bits: 0x"); Serial.println(bmsData.batteryStatus, HEX);
  }

  // --- 16. MOSFET Status (0x12BC, 1 reg, U_WORD: 0=off,1=chg,2=dis,3=both) ---
  r = nodeBMS.readHoldingRegisters(0x12BC, 1); delay(100);
  if (r == 0x00) {
    bmsData.mosfetStatus = nodeBMS.getResponseBuffer(0);
    Serial.print("[BMS] MOSFET: "); Serial.println(decodeMosfetStatus(bmsData.mosfetStatus));
  }

  bmsData.valid = true;

  // --- Validasi sum cell voltage vs total ---
  if (bmsData.cellCount > 0) {
    float sumCells = 0;
    for (int i = 0; i < bmsData.cellCount; i++) sumCells += bmsData.cellVoltages[i];
    float diff = fabs(sumCells - bmsData.totalVoltage);
    if (diff > 2.0f) {
      Serial.print("[BMS] WARNING: Sum cells "); Serial.print(sumCells, 2);
      Serial.print("V ≠ Total "); Serial.print(bmsData.totalVoltage, 2); Serial.println("V");
    }
  }

  return true;
}

// ==================== BMS Settings ====================
bool readAllBMSSettings(JsonObject& s) {
  Serial.println("[BMS] Reading settings...");
  bool ok = true; uint8_t r;

  uint16_t regs[] = {0x1000,0x1004,0x1008,0x100C,0x1010,0x1014,0x1018,0x101C,0x1020,0x1024,0x1028};
  const char* nm[] = {"smart_sleep","cell_uvp","cell_uvpr","cell_ovp","cell_ovpr",
    "balance_trigger","soc_100","soc_0","cell_rcv","cell_rfv","system_power_off"};
  for (int i = 0; i < 11; i++) {
    r = nodeBMS.readHoldingRegisters(regs[i], 2); delay(100);
    if (r == 0x00) {
      uint32_t v = read32u(0);
      s[nm[i]] = v * 0.001f;
    } else ok = false;
  }

  uint16_t cR[] = {0x102C, 0x1038, 0x1048};
  const char* cN[] = {"charge_coc", "discharge_coc", "max_balance_current"};
  for (int i = 0; i < 3; i++) {
    r = nodeBMS.readHoldingRegisters(cR[i], 2); delay(100);
    if (r == 0x00) { s[cN[i]] = read32u(0) * 0.001f; } else ok = false;
  }

  uint16_t tR[] = {0x104C,0x1050,0x1054,0x1058,0x105C,0x1060,0x1064,0x1068};
  const char* tN[] = {"charge_otp","charge_otpr","discharge_otp","discharge_otpr",
    "charge_utp","charge_utpr","mos_otp","mos_otpr"};
  for (int i = 0; i < 8; i++) {
    r = nodeBMS.readHoldingRegisters(tR[i], 2); delay(100);
    if (r == 0x00) { s[tN[i]] = (int32_t)read32u(0) * 0.1f; } else ok = false;
  }

  r = nodeBMS.readHoldingRegisters(0x106C, 2); delay(100);
  if (r == 0x00) s["cell_count"] = read32u(0);
  r = nodeBMS.readHoldingRegisters(0x107C, 2); delay(100);
  if (r == 0x00) s["battery_capacity"] = read32u(0) * 0.001f;
  r = nodeBMS.readHoldingRegisters(0x1084, 2); delay(100);
  if (r == 0x00) s["balance_start_voltage"] = read32u(0) * 0.001f;

  return ok;
}

// ==================== Commands ====================
bool processCommands(JsonArray cmds) {
  bool ok = true;
  for (JsonObject cmd : cmds) {
    String type = cmd["type"].as<String>();
    if (type == "bms_write_register") {
      uint16_t reg = cmd["register"].as<uint16_t>();
      uint16_t val = cmd["value"].as<uint16_t>();
      uint8_t r = nodeBMS.writeSingleRegister(reg, val); delay(100);
      if (r != 0x00) ok = false;
      Serial.print("  W 0x"); Serial.print(reg, HEX); Serial.print("="); Serial.print(val);
      Serial.println(r == 0x00 ? " OK" : " FAIL");
    } else if (type == "bms_write_multiple_registers") {
      uint16_t reg = cmd["register"].as<uint16_t>();
      JsonArray vals = cmd["values"].as<JsonArray>();
      for (int i = 0; i < (int)vals.size(); i++) nodeBMS.setTransmitBuffer(i, vals[i].as<uint16_t>());
      uint8_t r = nodeBMS.writeMultipleRegisters(reg, vals.size()); delay(100);
      if (r != 0x00) ok = false;
    }
  }
  return ok;
}

#if USE_INVERTER
// ==================== RS485 / CRC ====================
uint16_t modbusCalcCRC(uint8_t* buf, int len) {
  uint16_t crc = 0xFFFF;
  for (int i = 0; i < len; i++) {
    crc ^= buf[i];
    for (int j = 0; j < 8; j++) {
      if (crc & 0x0001) { crc >>= 1; crc ^= 0xA001; } else { crc >>= 1; }
    }
  }
  return crc;
}
void rs485Transmit() { digitalWrite(INVERTER_DE_RE_PIN, HIGH); delayMicroseconds(100); }
void rs485Receive()  { delayMicroseconds(100); digitalWrite(INVERTER_DE_RE_PIN, LOW); }

String decodeInverterMode(uint16_t statusBits) {
  bool solarCharging = (statusBits >> 6) & 0x01;
  bool gridCharging  = (statusBits >> 7) & 0x01;
  bool loadOn        = (statusBits >> 3) & 0x01;
  if (solarCharging && !gridCharging) return "Solar";
  if (gridCharging && !solarCharging) return "Grid";
  if (solarCharging && gridCharging)  return "Solar+Grid";
  if (loadOn) return "Battery";
  return "Standby";
}

// ==================== Inverter Reading ====================
bool readInverterDataManual() {
  rs485Receive(); delay(10);
  while (SerialInverter.available()) SerialInverter.read();

  uint8_t cmd[8] = {0x01, 0x03, 0x00, 0x13, 0x00, 0x14, 0x00, 0x00};
  uint16_t crc = modbusCalcCRC(cmd, 6);
  cmd[6] = crc & 0xFF; cmd[7] = (crc >> 8) & 0xFF;

  rs485Transmit();
  SerialInverter.write(cmd, 8); SerialInverter.flush();
  rs485Receive();

  uint8_t buf[128]; int len = 0;
  unsigned long startTime = millis();
  while (len < 128 && (millis() - startTime) < 2000) {
    if (SerialInverter.available()) { buf[len++] = SerialInverter.read(); startTime = millis(); }
    delay(1);
  }

  if (len < 5) { inverterData.online = false; return false; }
  if (buf[0] != 0x01 || buf[1] != 0x03) { inverterData.online = false; return false; }

  uint8_t byteCount = buf[2];
  int numRegs = byteCount / 2;
  int totalExpected = 3 + byteCount + 2;
  if (byteCount == 0 || byteCount > 100 || (byteCount % 2 != 0) || len < totalExpected) {
    inverterData.online = false; return false;
  }

  uint16_t recvCRC = buf[totalExpected - 2] | (buf[totalExpected - 1] << 8);
  uint16_t calcCRC = modbusCalcCRC(buf, totalExpected - 2);
  if (recvCRC != calcCRC) { inverterData.online = false; return false; }

  uint16_t regs[32]; memset(regs, 0, sizeof(regs));
  for (int i = 0; i < numRegs && i < 32; i++)
    regs[i] = (buf[3 + i*2] << 8) | buf[3 + i*2 + 1];

  inverterData.gridVoltage           = (numRegs >= 1)  ? regs[0]       : 0;
  inverterData.gridFrequency         = (numRegs >= 2)  ? regs[1]*0.1   : 0;
  inverterData.acOutputVoltage       = (numRegs >= 3)  ? regs[2]       : 0;
  inverterData.acOutputFrequency     = (numRegs >= 4)  ? regs[3]*0.1   : 0;
  inverterData.acOutputPowerVA       = (numRegs >= 5)  ? regs[4]       : 0;
  inverterData.acOutputPowerWatt     = (numRegs >= 6)  ? regs[5]       : 0;
  inverterData.outputLoadPercent     = (numRegs >= 7)  ? regs[6]       : 0;
  inverterData.busVoltage            = (numRegs >= 8)  ? regs[7]       : 0;
  inverterData.batteryVoltage        = (numRegs >= 9)  ? regs[8]*0.1   : 0;
  inverterData.batteryChargeCurrent  = (numRegs >= 10) ? regs[9]*0.1   : 0;
  inverterData.batteryCapacity       = (numRegs >= 11) ? regs[10]      : 0;
  inverterData.heatSinkTemp          = (numRegs >= 12) ? regs[11]      : 0;
  inverterData.solarCurrent          = (numRegs >= 13) ? regs[12]*0.1  : 0;
  inverterData.solarVoltage          = (numRegs >= 14) ? regs[13]*0.1  : 0;
  inverterData.batteryVoltSCC        = (numRegs >= 15) ? regs[14]*0.1  : 0;
  inverterData.batteryDischargeCurrent=(numRegs >= 16) ? regs[15]*0.1  : 0;
  inverterData.deviceStatusBits      = (numRegs >= 17) ? regs[16]      : 0;
  inverterData.solarPowerWatt        = (numRegs >= 20) ? regs[19]      : 0;
  inverterData.mode = decodeInverterMode(inverterData.deviceStatusBits);
  inverterData.online = true;
  inverterData.lastReadTime = millis();

  Serial.print("[INV] Grid:"); Serial.print(inverterData.gridVoltage);
  Serial.print("V | AC Out:"); Serial.print(inverterData.acOutputPowerWatt);
  Serial.print("W | PV:"); Serial.print(inverterData.solarPowerWatt);
  Serial.print("W | Mode:"); Serial.println(inverterData.mode);
  return true;
}
#endif // USE_INVERTER

// ==================== HTTP: Kirim ke Laravel ====================
void sendLogToLaravel(const char* msg, const char* level = "info");

void sendDataToLaravel() {
  if (!wifiConnected) return;
  if (!bmsData.valid) return;

  HTTPClient http;
  http.begin(serverURL);
  http.addHeader("Content-Type", "application/json");
  if (strlen(apiKey) > 0) http.addHeader("Authorization", "Bearer " + String(apiKey));

  DynamicJsonDocument doc(8192);
  doc["device_id"] = deviceId;

#if USE_INVERTER
  // === Inverter ===
  JsonObject inv = doc.createNestedObject("inverter");
  inv["inverter_status"]        = inverterData.online ? "online" : "offline";
  inv["inv_grid_v"]             = inverterData.gridVoltage;
  inv["inv_grid_freq"]          = inverterData.gridFrequency;
  inv["inv_ac_out_v"]           = inverterData.acOutputVoltage;
  inv["inv_ac_out_freq"]        = inverterData.acOutputFrequency;
  inv["inv_ac_out_va"]          = inverterData.acOutputPowerVA;
  inv["inv_ac_out_w"]           = inverterData.acOutputPowerWatt;
  inv["inv_load_percent"]       = inverterData.outputLoadPercent;
  inv["inv_batt_v"]             = inverterData.batteryVoltage;
  inv["inv_batt_charge_a"]      = inverterData.batteryChargeCurrent;
  inv["inv_batt_discharge_a"]   = inverterData.batteryDischargeCurrent;
  inv["inv_batt_capacity"]      = inverterData.batteryCapacity;
  inv["inv_batt_v_scc"]         = inverterData.batteryVoltSCC;
  inv["inv_pv_v"]               = inverterData.solarVoltage;
  inv["inv_pv_a"]               = inverterData.solarCurrent;
  inv["inv_pv_w"]               = inverterData.solarPowerWatt;
  inv["inv_temp"]               = inverterData.heatSinkTemp;
  inv["inv_bus_v"]              = inverterData.busVoltage;
  inv["inv_mode"]               = inverterData.mode;
  inv["inv_status_bits"]        = inverterData.deviceStatusBits;
#endif // USE_INVERTER

  // === BMS (LENGKAP) ===
  JsonObject bms = doc.createNestedObject("bms");
  if (bmsData.valid) {
    // Pack
    bms["battery_voltage"]      = bmsData.totalVoltage;
    bms["current"]              = bmsData.current;
    bms["power"]                = bmsData.power;
    bms["soc"]                  = bmsData.soc;

    // Capacity
    bms["remaining_capacity"]   = bmsData.remainingCapacity;
    bms["nominal_capacity"]     = bmsData.nominalCapacity;

    // Cycle Count *** PENTING ***
    bms["cycle_count"]          = bmsData.cycleCount;
    bms["total_cycle_capacity"] = bmsData.totalCycleCapacity;

    // Temperature
    bms["temperature"]          = bmsData.temp1;
    bms["temperature2"]         = bmsData.temp2;

    // Balance
    bms["balance_current"]      = bmsData.balanceCurrent;
    bms["is_balancing"]         = (fabs(bmsData.balanceCurrent) > 0.001f);

    // Alarm & Status
    bms["alarm_flags"]          = bmsData.alarmFlags;
    bms["alarm_text"]           = decodeAlarmFlags(bmsData.alarmFlags);
    bms["battery_status"]       = bmsData.batteryStatus;
    bms["mosfet_status"]        = bmsData.mosfetStatus;
    bms["mosfet_text"]          = decodeMosfetStatus(bmsData.mosfetStatus);

    // Cells
    bms["cell_count"]           = bmsData.cellCount;
    JsonArray cells = bms.createNestedArray("cell_voltages");
    for (int i = 0; i < bmsData.cellCount; i++) cells.add(bmsData.cellVoltages[i]);

    // Cell min/max/diff (berguna untuk dashboard)
    if (bmsData.cellCount > 0) {
      float vmin = bmsData.cellVoltages[0], vmax = bmsData.cellVoltages[0];
      for (int i = 1; i < bmsData.cellCount; i++) {
        if (bmsData.cellVoltages[i] < vmin) vmin = bmsData.cellVoltages[i];
        if (bmsData.cellVoltages[i] > vmax) vmax = bmsData.cellVoltages[i];
      }
      bms["cell_min_v"]   = vmin;
      bms["cell_max_v"]   = vmax;
      bms["cell_diff_mv"] = (int)((vmax - vmin) * 1000);
    }

    // Cell Resistance
    if (bmsData.cellResistanceValid) {
      JsonArray res = bms.createNestedArray("cell_resistances");
      for (int i = 0; i < bmsData.cellCount; i++) res.add(bmsData.cellResistance[i]);
    }

    // Settings
    if (bmsSettingsValid && settingsDocPtr) {
      JsonObject bs = bms.createNestedObject("bms_settings");
      JsonObject sv = settingsDocPtr->as<JsonObject>();
      for (JsonPair kv : sv) bs[kv.key().c_str()] = kv.value();
    }
  }

  String payload;
  serializeJson(doc, payload);

  Serial.print("[HTTP] Sending... Size: "); Serial.print(payload.length()); Serial.println(" bytes");
  int code = http.POST(payload);
  if (code == 200 || code == 201) {
    Serial.print("[HTTP] OK: "); Serial.println(code);
    String resp = http.getString();
    DynamicJsonDocument rd(2048);
    if (!deserializeJson(rd, resp) && rd.containsKey("commands")) {
      JsonArray c = rd["commands"];
      if (c.size() > 0) processCommands(c);
    }
  } else {
    Serial.print("[HTTP] Error: "); Serial.println(code);
    if (code > 0) {
      String resp = http.getString();
      Serial.println(resp.substring(0, 200));
    }
  }
  http.end();
}

void sendLogToLaravel(const char* msg, const char* level) {
  if (!wifiConnected) return;
  HTTPClient http;
  http.begin(logURL);
  http.addHeader("Content-Type", "application/json");
  DynamicJsonDocument doc(512);
  doc["device_id"] = deviceId;
  doc["message"]   = msg;
  doc["level"]     = level;
  doc["timestamp"] = millis();
  String p; serializeJson(doc, p);
  http.POST(p); http.end();
}

// ==================== SETUP ====================
void setup() {
  Serial.begin(115200);
  delay(1000);
  Serial.println("\n========================================");
  Serial.println("ESP32 JK-BMS FULL Sync (Inverter Nonaktif)");
  Serial.println("========================================");

  // Buzzer
  ledcAttach(BUZZER_PIN, 2000, 8);
  ledcWrite(BUZZER_PIN, 0);
  Serial.println("[INIT] Buzzer OK (GPIO4)");

  // LED RX/TX
  pinMode(LED_TX_PIN, OUTPUT);
  pinMode(LED_RX_PIN, OUTPUT);
  digitalWrite(LED_TX_PIN, LOW);
  digitalWrite(LED_RX_PIN, LOW);
  // Test: kedip cepat 3x saat boot
  for (int i = 0; i < 3; i++) {
    digitalWrite(LED_TX_PIN, HIGH); digitalWrite(LED_RX_PIN, HIGH); delay(100);
    digitalWrite(LED_TX_PIN, LOW);  digitalWrite(LED_RX_PIN, LOW);  delay(100);
  }
  Serial.println("[INIT] LED TX(GPIO18) RX(GPIO19) OK");

  // BMS Modbus (UART2)
  SerialBMS.begin(BMS_BAUDRATE, SERIAL_8N1, BMS_RX_PIN, BMS_TX_PIN);
  delay(100);
  nodeBMS.begin(BMS_SLAVE_ID, SerialBMS);
  nodeBMS.preTransmission(preBMS);
  nodeBMS.postTransmission(postBMS);
  Serial.println("[INIT] BMS OK (UART2 GPIO16/17 @ 115200)");

#if USE_INVERTER
  // Inverter MAX485 (UART1)
  SerialInverter.begin(INVERTER_BAUDRATE, SERIAL_8N1, INVERTER_RX_PIN, INVERTER_TX_PIN);
  delay(100);
  pinMode(INVERTER_DE_RE_PIN, OUTPUT);
  digitalWrite(INVERTER_DE_RE_PIN, LOW);
  Serial.println("[INIT] Inverter OK (UART1 GPIO14/27 @ 9600, MAX485 DE/RE=GPIO26)");
#else
  Serial.println("[INIT] Inverter: NONAKTIF (USE_INVERTER=0)");
#endif

  // Settings doc
  settingsDocPtr = new DynamicJsonDocument(2048);

  // WiFi
  Serial.println("[INIT] Connecting WiFi...");
  connectWiFi();

  Serial.println("\n========================================");
  Serial.println("Registers yang dibaca dari JK-BMS:");
  Serial.println("  0x1200 : Cell Voltages (max 32)");
  Serial.println("  0x1240 : Cell Resistance (max 32)");
  Serial.println("  0x1290 : Total Voltage");
  Serial.println("  0x1294 : Power");
  Serial.println("  0x1298 : Current");
  Serial.println("  0x129C : Temperature 1");
  Serial.println("  0x129E : Temperature 2");
  Serial.println("  0x12A0 : Alarm Flags");
  Serial.println("  0x12A4 : Balance Current");
  Serial.println("  0x12A6 : SOC");
  Serial.println("  0x12A8 : Remaining Capacity");
  Serial.println("  0x12AC : Nominal Capacity");
  Serial.println("  0x12B0 : *** CYCLE COUNT ***");
  Serial.println("  0x12B4 : Total Cycle Capacity");
  Serial.println("  0x12B8 : Battery Status Flags");
  Serial.println("  0x12BC : MOSFET Status");
  Serial.println("========================================\n");
}

// ==================== LOOP ====================
void loop() {
  // WiFi reconnect
  if (millis() - lastWiFiCheck >= WIFI_RECONNECT_INTERVAL) {
    lastWiFiCheck = millis();
    if (WiFi.status() != WL_CONNECTED) { wifiConnected = false; connectWiFi(); }
  }

#if USE_INVERTER
  // Inverter (setiap 5 detik)
  if (millis() - lastInverterRequest >= INVERTER_REQUEST_INTERVAL) {
    lastInverterRequest = millis();
    Serial.println("\n--- [Inverter] ---");
    readInverterDataManual();
    Serial.println("------------------");
  }
#endif // USE_INVERTER

  // BMS Realtime (setiap 8 detik)
  if (millis() - lastBMSRequest >= BMS_REQUEST_INTERVAL) {
    lastBMSRequest = millis();
    Serial.println("\n=== [BMS Realtime] ===");
    if (readBMSRealTimeData()) {
      Serial.println("[BMS] ✓ Data lengkap berhasil dibaca:");
      Serial.print("  Voltage : "); Serial.print(bmsData.totalVoltage, 3); Serial.println(" V");
      Serial.print("  Current : "); Serial.print(bmsData.current, 3); Serial.println(" A");
      Serial.print("  Power   : "); Serial.print(bmsData.power, 2); Serial.println(" W");
      Serial.print("  SOC     : "); Serial.print(bmsData.soc); Serial.println(" %");
      Serial.print("  Remain  : "); Serial.print(bmsData.remainingCapacity, 2); Serial.println(" Ah");
      Serial.print("  Nominal : "); Serial.print(bmsData.nominalCapacity, 2); Serial.println(" Ah");
      Serial.print("  CYCLES  : "); Serial.print(bmsData.cycleCount); Serial.println(" cycles ← CYCLE COUNT");
      Serial.print("  Temp1   : "); Serial.print(bmsData.temp1, 1); Serial.println(" °C");
      Serial.print("  Temp2   : "); Serial.print(bmsData.temp2, 1); Serial.println(" °C");
      Serial.print("  Balance : "); Serial.print(bmsData.balanceCurrent, 3); Serial.println(" A");
      Serial.print("  Alarm   : "); Serial.println(decodeAlarmFlags(bmsData.alarmFlags));
      Serial.print("  MOSFET  : "); Serial.println(decodeMosfetStatus(bmsData.mosfetStatus));
      if (wifiConnected) sendDataToLaravel();
    } else {
      Serial.println("[BMS] ✗ Gagal baca data!");
      playErrorBeep();
    }
    Serial.println("======================");
  }

  // BMS Settings (setiap 60 detik)
  if (millis() - lastBMSSettingsRead >= BMS_SETTINGS_READ_INTERVAL) {
    lastBMSSettingsRead = millis();
    if (settingsDocPtr) {
      Serial.println("\n--- [BMS Settings] ---");
      settingsDocPtr->clear();
      JsonObject obj = settingsDocPtr->to<JsonObject>();
      if (readAllBMSSettings(obj)) {
        bmsSettingsValid = true;
        Serial.println("[BMS] Settings OK!");
        if (wifiConnected) sendDataToLaravel();
      } else {
        Serial.println("[BMS] Sebagian settings gagal dibaca");
      }
      Serial.println("----------------------");
    }
  }

  delay(100);
}

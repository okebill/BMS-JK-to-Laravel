-- Create bms_settings table
-- Run this SQL directly on your MySQL database

CREATE TABLE IF NOT EXISTS `bms_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `device_id` varchar(255) NOT NULL DEFAULT 'ESP32-001',
  
  -- Protection Settings
  `cell_voltage_overvoltage` decimal(5,3) NOT NULL DEFAULT 3.750 COMMENT 'Cell Voltage Overvoltage (V)',
  `cell_voltage_undervoltage` decimal(5,3) NOT NULL DEFAULT 2.800 COMMENT 'Cell Voltage Undervoltage (V)',
  `cell_voltage_overvoltage_recovery` decimal(5,3) NOT NULL DEFAULT 3.500 COMMENT 'Cell Voltage Overvoltage Recovery (V)',
  `cell_voltage_undervoltage_recovery` decimal(5,3) NOT NULL DEFAULT 2.900 COMMENT 'Cell Voltage Undervoltage Recovery (V)',
  `cell_voltage_balance_start` decimal(5,3) NOT NULL DEFAULT 3.400 COMMENT 'Cell Voltage Balance Start (V)',
  `cell_voltage_balance_delta` decimal(5,3) NOT NULL DEFAULT 0.010 COMMENT 'Cell Voltage Balance Delta (V)',
  
  `total_voltage_overvoltage` decimal(6,2) NOT NULL DEFAULT 60.00 COMMENT 'Total Voltage Overvoltage (V)',
  `total_voltage_undervoltage` decimal(6,2) NOT NULL DEFAULT 44.80 COMMENT 'Total Voltage Undervoltage (V)',
  `total_voltage_overvoltage_recovery` decimal(6,2) NOT NULL DEFAULT 56.00 COMMENT 'Total Voltage Overvoltage Recovery (V)',
  `total_voltage_undervoltage_recovery` decimal(6,2) NOT NULL DEFAULT 46.40 COMMENT 'Total Voltage Undervoltage Recovery (V)',
  
  `charge_overcurrent_protection` int(11) NOT NULL DEFAULT 200 COMMENT 'Charge Overcurrent Protection (A)',
  `discharge_overcurrent_protection` int(11) NOT NULL DEFAULT 200 COMMENT 'Discharge Overcurrent Protection (A)',
  `charge_overtemperature_protection` int(11) NOT NULL DEFAULT 50 COMMENT 'Charge Overtemperature Protection (°C)',
  `charge_undertemperature_protection` int(11) NOT NULL DEFAULT 0 COMMENT 'Charge Undertemperature Protection (°C)',
  `discharge_overtemperature_protection` int(11) NOT NULL DEFAULT 60 COMMENT 'Discharge Overtemperature Protection (°C)',
  `discharge_undertemperature_protection` int(11) NOT NULL DEFAULT -20 COMMENT 'Discharge Undertemperature Protection (°C)',
  
  -- Balance Settings
  `balance_start_voltage` int(11) NOT NULL DEFAULT 3400 COMMENT 'Balance Start Voltage (mV)',
  `balance_delta_voltage` int(11) NOT NULL DEFAULT 10 COMMENT 'Balance Delta Voltage (mV)',
  `balance_enabled` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Balance Enabled',
  
  -- Device Info
  `device_name` varchar(255) DEFAULT NULL COMMENT 'Device Name',
  `manufacturing_date` varchar(255) DEFAULT NULL COMMENT 'Manufacturing Date',
  `total_runtime` varchar(255) DEFAULT NULL COMMENT 'Total Runtime',
  `cycles` int(11) NOT NULL DEFAULT 0 COMMENT 'Cycles',
  `total_charging_time` int(11) NOT NULL DEFAULT 0 COMMENT 'Total Charging Time (s)',
  `total_discharging_time` int(11) NOT NULL DEFAULT 0 COMMENT 'Total Discharging Time (s)',
  
  -- Calibration & Advanced
  `current_calibration` decimal(6,3) NOT NULL DEFAULT 0.000 COMMENT 'Current Calibration',
  `sleep_time` int(11) NOT NULL DEFAULT 0 COMMENT 'Sleep Time (s)',
  `password` varchar(255) DEFAULT NULL COMMENT 'Password',
  `switch_state` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Switch State',
  
  -- Modbus Register Addresses (untuk write command)
  `reg_cell_overvoltage` int(11) NOT NULL DEFAULT 4864 COMMENT 'Register: Cell Overvoltage',
  `reg_cell_undervoltage` int(11) NOT NULL DEFAULT 4865 COMMENT 'Register: Cell Undervoltage',
  `reg_balance_start` int(11) NOT NULL DEFAULT 4866 COMMENT 'Register: Balance Start',
  `reg_balance_delta` int(11) NOT NULL DEFAULT 4867 COMMENT 'Register: Balance Delta',
  
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  KEY `bms_settings_device_id_index` (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings for ESP32-001
INSERT INTO `bms_settings` (
  `device_id`,
  `cell_voltage_overvoltage`,
  `cell_voltage_undervoltage`,
  `cell_voltage_overvoltage_recovery`,
  `cell_voltage_undervoltage_recovery`,
  `cell_voltage_balance_start`,
  `cell_voltage_balance_delta`,
  `total_voltage_overvoltage`,
  `total_voltage_undervoltage`,
  `total_voltage_overvoltage_recovery`,
  `total_voltage_undervoltage_recovery`,
  `charge_overcurrent_protection`,
  `discharge_overcurrent_protection`,
  `charge_overtemperature_protection`,
  `charge_undertemperature_protection`,
  `discharge_overtemperature_protection`,
  `discharge_undertemperature_protection`,
  `balance_start_voltage`,
  `balance_delta_voltage`,
  `balance_enabled`,
  `switch_state`,
  `reg_cell_overvoltage`,
  `reg_cell_undervoltage`,
  `reg_balance_start`,
  `reg_balance_delta`,
  `created_at`,
  `updated_at`
) VALUES (
  'ESP32-001',
  3.750,
  2.800,
  3.500,
  2.900,
  3.400,
  0.010,
  60.00,
  44.80,
  56.00,
  46.40,
  200,
  200,
  50,
  0,
  60,
  -20,
  3400,
  10,
  1,
  1,
  4864,
  4865,
  4866,
  4867,
  NOW(),
  NOW()
) ON DUPLICATE KEY UPDATE `updated_at` = NOW();

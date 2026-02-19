-- Create bms_parameters table
CREATE TABLE IF NOT EXISTS `bms_parameters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `device_id` varchar(255) NOT NULL DEFAULT 'ESP32-001',
  
  -- Voltage Settings (dari register 0x1000+)
  `smart_sleep` decimal(8,3) DEFAULT NULL COMMENT 'Smart Sleep Voltage (V)',
  `cell_uvp` decimal(8,3) DEFAULT NULL COMMENT 'Cell Undervoltage Protection (V)',
  `cell_uvpr` decimal(8,3) DEFAULT NULL COMMENT 'Cell Undervoltage Protection Recovery (V)',
  `cell_ovp` decimal(8,3) DEFAULT NULL COMMENT 'Cell Overvoltage Protection (V)',
  `cell_ovpr` decimal(8,3) DEFAULT NULL COMMENT 'Cell Overvoltage Protection Recovery (V)',
  `balance_trigger` decimal(8,3) DEFAULT NULL COMMENT 'Balance Trigger Voltage (V)',
  `soc_100` decimal(8,3) DEFAULT NULL COMMENT 'SOC 100% Voltage (V)',
  `soc_0` decimal(8,3) DEFAULT NULL COMMENT 'SOC 0% Voltage (V)',
  `cell_rcv` decimal(8,3) DEFAULT NULL COMMENT 'Cell RCV Voltage (V)',
  `cell_rfv` decimal(8,3) DEFAULT NULL COMMENT 'Cell RFV Voltage (V)',
  `system_power_off` decimal(8,3) DEFAULT NULL COMMENT 'System Power Off Voltage (V)',
  
  -- Current Settings
  `charge_coc` decimal(8,3) DEFAULT NULL COMMENT 'Charge Continued Overcurrent (A)',
  `discharge_coc` decimal(8,3) DEFAULT NULL COMMENT 'Discharge Continued Overcurrent (A)',
  `max_balance_current` decimal(8,3) DEFAULT NULL COMMENT 'Max Balance Current (A)',
  
  -- Temperature Settings
  `charge_otp` decimal(6,1) DEFAULT NULL COMMENT 'Charge Overtemperature Protection (°C)',
  `charge_otpr` decimal(6,1) DEFAULT NULL COMMENT 'Charge OTP Recovery (°C)',
  `discharge_otp` decimal(6,1) DEFAULT NULL COMMENT 'Discharge Overtemperature Protection (°C)',
  `discharge_otpr` decimal(6,1) DEFAULT NULL COMMENT 'Discharge OTP Recovery (°C)',
  `charge_utp` decimal(6,1) DEFAULT NULL COMMENT 'Charge Undertemperature Protection (°C)',
  `charge_utpr` decimal(6,1) DEFAULT NULL COMMENT 'Charge UTP Recovery (°C)',
  `mos_otp` decimal(6,1) DEFAULT NULL COMMENT 'MOS Overtemperature Protection (°C)',
  `mos_otpr` decimal(6,1) DEFAULT NULL COMMENT 'MOS OTP Recovery (°C)',
  
  -- Battery Info
  `cell_count` int(11) DEFAULT NULL COMMENT 'Cell Count',
  `battery_capacity` decimal(8,2) DEFAULT NULL COMMENT 'Battery Capacity (Ah)',
  `balance_start_voltage` decimal(8,3) DEFAULT NULL COMMENT 'Balance Start Voltage (V)',
  
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bms_parameters_device_id_index` (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create bms_commands_queue table
CREATE TABLE IF NOT EXISTS `bms_commands_queue` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `device_id` varchar(255) NOT NULL DEFAULT 'ESP32-001',
  `command_type` varchar(255) NOT NULL COMMENT 'bms_write_register, bms_write_multiple_registers',
  `register_address` int(11) NOT NULL COMMENT 'Modbus register address (hex)',
  `command_data` json DEFAULT NULL COMMENT 'Value(s) to write',
  `status` enum('pending','sent','completed','failed') NOT NULL DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bms_commands_queue_device_id_status_index` (`device_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

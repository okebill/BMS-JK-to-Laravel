-- ============================================================
-- Okenet BMS Monitoring — Database Schema
-- Versi: 1.0.0 | Dibuat: 2026-02-19
-- ============================================================
-- Import file ini ke database MySQL/MariaDB Anda via phpMyAdmin
-- atau saat proses instalasi otomatis melalui install.php
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ─── USERS ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User default: bms@okebil.com / 123456789
-- (akan di-override oleh installer jika menggunakan install.php)
INSERT INTO `users` (`name`, `email`, `password`, `created_at`, `updated_at`) VALUES
('Administrator', 'bms@okebil.com', '$2y$12$Vu3CG.aSNO0w6kQ8Q8Xy5.Q5cLAMzTDZ8XuKxUhJb9wBR9KZ1Hqvq', NOW(), NOW());
-- Password hash di atas = 123456789

-- ─── CACHE ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── JOBS & QUEUE ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── SESSIONS ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── MIGRATIONS ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2024_02_15_000001_create_monitoring_logs_table', 1),
('2024_02_15_000002_create_device_commands_table', 1),
('2024_02_15_000003_create_system_logs_table', 2),
('2024_02_15_000004_create_bms_settings_table', 3),
('2024_02_20_000001_create_bms_parameters_table', 4),
('2024_02_20_000002_create_bms_commands_queue_table', 4),
('2024_02_21_000001_add_bms_extended_fields_to_monitoring_logs', 5),
('2026_02_19_000001_add_mos_temp_alarm_is_real_to_monitoring_logs', 6);

-- ─── MONITORING LOGS ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `monitoring_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pv_voltage` decimal(8,2) DEFAULT NULL,
  `pv_current` decimal(8,2) DEFAULT NULL,
  `ac_voltage` decimal(8,2) DEFAULT NULL,
  `load_power` decimal(10,2) DEFAULT NULL,
  `battery_voltage` decimal(8,2) DEFAULT NULL,
  `battery_current` decimal(8,2) DEFAULT NULL,
  `power` decimal(10,2) DEFAULT NULL COMMENT 'W, + charge - discharge',
  `remaining_capacity` decimal(10,3) DEFAULT NULL COMMENT 'Ah',
  `nominal_capacity` decimal(10,3) DEFAULT NULL COMMENT 'Ah',
  `cycle_count` int(10) UNSIGNED DEFAULT NULL COMMENT 'number of cycles',
  `total_cycle_capacity` decimal(12,3) DEFAULT NULL COMMENT 'cumulative Ah',
  `soc` int(11) DEFAULT NULL COMMENT 'State of Charge 0-100',
  `battery_temperature` decimal(5,2) DEFAULT NULL,
  `temperature2` decimal(5,2) DEFAULT NULL COMMENT 'Sensor 2 °C',
  `mos_temp` decimal(5,2) DEFAULT NULL COMMENT 'MOSFET temperature °C',
  `cell_voltages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`cell_voltages`)),
  `cell_count` int(11) NOT NULL DEFAULT 16,
  `device_id` varchar(255) DEFAULT NULL COMMENT 'ESP32 Device ID',
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `balance_current` decimal(8,3) DEFAULT NULL COMMENT 'A',
  `is_balancing` tinyint(1) NOT NULL DEFAULT 0,
  `alarm_flags` int(10) UNSIGNED DEFAULT NULL COMMENT 'bit flags',
  `alarm_text` varchar(255) DEFAULT NULL,
  `alarm_is_real` tinyint(1) NOT NULL DEFAULT 0,
  `mosfet_status` tinyint(4) DEFAULT NULL COMMENT '0=off 1=chg 2=dis 3=both',
  `mosfet_text` varchar(50) DEFAULT NULL,
  `cell_diff_mv` int(10) UNSIGNED DEFAULT NULL COMMENT 'max-min cell voltage diff in mV',
  `cell_resistances` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'mΩ per cell' CHECK (json_valid(`cell_resistances`)),
  PRIMARY KEY (`id`),
  KEY `monitoring_logs_device_id_index` (`device_id`),
  KEY `monitoring_logs_recorded_at_index` (`recorded_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── DEVICE COMMANDS ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `device_commands` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `device_id` varchar(255) DEFAULT NULL,
  `command_type` varchar(255) NOT NULL COMMENT 'inverter_config, bms_config, etc',
  `command_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`command_data`)),
  `status` enum('pending','sent','executed','failed') NOT NULL DEFAULT 'pending',
  `sent_at` timestamp NULL DEFAULT NULL,
  `executed_at` timestamp NULL DEFAULT NULL,
  `response` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── SYSTEM LOGS ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `system_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `level` varchar(255) NOT NULL DEFAULT 'info',
  `message` text NOT NULL,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `system_logs_level_index` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── BMS SETTINGS ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `bms_settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `device_id` varchar(255) NOT NULL DEFAULT 'ESP32-001',
  `cell_voltage_overvoltage` decimal(5,3) NOT NULL DEFAULT 3.750,
  `cell_voltage_undervoltage` decimal(5,3) NOT NULL DEFAULT 2.800,
  `cell_voltage_overvoltage_recovery` decimal(5,3) NOT NULL DEFAULT 3.500,
  `cell_voltage_undervoltage_recovery` decimal(5,3) NOT NULL DEFAULT 2.900,
  `cell_voltage_balance_start` decimal(5,3) NOT NULL DEFAULT 3.400,
  `cell_voltage_balance_delta` decimal(5,3) NOT NULL DEFAULT 0.010,
  `total_voltage_overvoltage` decimal(6,2) NOT NULL DEFAULT 60.00,
  `total_voltage_undervoltage` decimal(6,2) NOT NULL DEFAULT 44.80,
  `total_voltage_overvoltage_recovery` decimal(6,2) NOT NULL DEFAULT 56.00,
  `total_voltage_undervoltage_recovery` decimal(6,2) NOT NULL DEFAULT 46.40,
  `charge_overcurrent_protection` int(11) NOT NULL DEFAULT 200,
  `discharge_overcurrent_protection` int(11) NOT NULL DEFAULT 200,
  `charge_overtemperature_protection` int(11) NOT NULL DEFAULT 50,
  `charge_undertemperature_protection` int(11) NOT NULL DEFAULT 0,
  `discharge_overtemperature_protection` int(11) NOT NULL DEFAULT 60,
  `discharge_undertemperature_protection` int(11) NOT NULL DEFAULT -20,
  `balance_start_voltage` int(11) NOT NULL DEFAULT 3400,
  `balance_delta_voltage` int(11) NOT NULL DEFAULT 10,
  `balance_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `device_name` varchar(255) DEFAULT NULL,
  `manufacturing_date` varchar(255) DEFAULT NULL,
  `total_runtime` varchar(255) DEFAULT NULL,
  `cycles` int(11) NOT NULL DEFAULT 0,
  `total_charging_time` int(11) NOT NULL DEFAULT 0,
  `total_discharging_time` int(11) NOT NULL DEFAULT 0,
  `current_calibration` decimal(6,3) NOT NULL DEFAULT 0.000,
  `sleep_time` int(11) NOT NULL DEFAULT 0,
  `password` varchar(255) DEFAULT NULL,
  `switch_state` tinyint(1) NOT NULL DEFAULT 1,
  `reg_cell_overvoltage` int(11) NOT NULL DEFAULT 4864,
  `reg_cell_undervoltage` int(11) NOT NULL DEFAULT 4865,
  `reg_balance_start` int(11) NOT NULL DEFAULT 4866,
  `reg_balance_delta` int(11) NOT NULL DEFAULT 4867,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bms_settings` (`device_id`, `created_at`, `updated_at`) VALUES
('ESP32-001', NOW(), NOW());

-- ─── BMS PARAMETERS ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `bms_parameters` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `device_id` varchar(255) NOT NULL DEFAULT 'ESP32-001',
  `smart_sleep` decimal(8,3) DEFAULT NULL,
  `cell_uvp` decimal(8,3) DEFAULT NULL,
  `cell_uvpr` decimal(8,3) DEFAULT NULL,
  `cell_ovp` decimal(8,3) DEFAULT NULL,
  `cell_ovpr` decimal(8,3) DEFAULT NULL,
  `balance_trigger` decimal(8,3) DEFAULT NULL,
  `soc_100` decimal(8,3) DEFAULT NULL,
  `soc_0` decimal(8,3) DEFAULT NULL,
  `cell_rcv` decimal(8,3) DEFAULT NULL,
  `cell_rfv` decimal(8,3) DEFAULT NULL,
  `system_power_off` decimal(8,3) DEFAULT NULL,
  `charge_coc` decimal(8,3) DEFAULT NULL,
  `discharge_coc` decimal(8,3) DEFAULT NULL,
  `max_balance_current` decimal(8,3) DEFAULT NULL,
  `charge_otp` decimal(6,1) DEFAULT NULL,
  `charge_otpr` decimal(6,1) DEFAULT NULL,
  `discharge_otp` decimal(6,1) DEFAULT NULL,
  `discharge_otpr` decimal(6,1) DEFAULT NULL,
  `charge_utp` decimal(6,1) DEFAULT NULL,
  `charge_utpr` decimal(6,1) DEFAULT NULL,
  `mos_otp` decimal(6,1) DEFAULT NULL,
  `mos_otpr` decimal(6,1) DEFAULT NULL,
  `cell_count` int(11) DEFAULT NULL,
  `battery_capacity` decimal(8,2) DEFAULT NULL,
  `balance_start_voltage` decimal(8,3) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── BMS COMMANDS QUEUE ─────────────────────────────────────
CREATE TABLE IF NOT EXISTS `bms_commands_queue` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `device_id` varchar(255) NOT NULL DEFAULT 'ESP32-001',
  `command_type` varchar(255) NOT NULL COMMENT 'bms_write_register, bms_write_multiple_registers',
  `register_address` int(11) NOT NULL COMMENT 'Modbus register address (hex)',
  `command_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`command_data`)),
  `status` enum('pending','sent','completed','failed') NOT NULL DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bms_commands_queue_device_status_index` (`device_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── PASSWORD RESET TOKENS ──────────────────────────────────
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Schema selesai. Login default:
-- Email   : bms@okebil.com
-- Password: 123456789
-- (Ganti password setelah login pertama!)
-- ============================================================

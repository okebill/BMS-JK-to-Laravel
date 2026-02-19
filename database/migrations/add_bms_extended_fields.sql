-- ============================================================
-- Migration: Add BMS Extended Fields to monitoring_logs
-- Jalankan di server MySQL database 'bms'
-- ============================================================

ALTER TABLE `monitoring_logs`
    ADD COLUMN `power`                DECIMAL(10,2) NULL COMMENT 'W, + charge - discharge' AFTER `battery_current`,
    ADD COLUMN `remaining_capacity`   DECIMAL(10,3) NULL COMMENT 'Ah remaining'             AFTER `power`,
    ADD COLUMN `nominal_capacity`     DECIMAL(10,3) NULL COMMENT 'Ah nominal total'         AFTER `remaining_capacity`,
    ADD COLUMN `cycle_count`          INT UNSIGNED  NULL COMMENT 'Number of cycles'          AFTER `nominal_capacity`,
    ADD COLUMN `total_cycle_capacity` DECIMAL(12,3) NULL COMMENT 'Cumulative Ah through cycles' AFTER `cycle_count`,
    ADD COLUMN `temperature2`         DECIMAL(5,2)  NULL COMMENT 'Sensor 2 deg C'           AFTER `battery_temperature`,
    ADD COLUMN `balance_current`      DECIMAL(8,3)  NULL COMMENT 'Amps balance current'     AFTER `soc`,
    ADD COLUMN `is_balancing`         TINYINT(1) NOT NULL DEFAULT 0                          AFTER `balance_current`,
    ADD COLUMN `alarm_flags`          INT UNSIGNED  NULL COMMENT 'Bit flags from BMS'        AFTER `is_balancing`,
    ADD COLUMN `alarm_text`           VARCHAR(255)  NULL COMMENT 'Human readable alarm'      AFTER `alarm_flags`,
    ADD COLUMN `mosfet_status`        TINYINT       NULL COMMENT '0=off 1=chg 2=dis 3=both' AFTER `alarm_text`,
    ADD COLUMN `mosfet_text`          VARCHAR(50)   NULL                                      AFTER `mosfet_status`,
    ADD COLUMN `cell_diff_mv`         INT UNSIGNED  NULL COMMENT 'max-min cell mv diff'      AFTER `cell_count`,
    ADD COLUMN `cell_resistances`     JSON          NULL COMMENT 'mOhm per cell array'       AFTER `cell_voltages`;

-- Verify
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'bms'
  AND TABLE_NAME   = 'monitoring_logs'
ORDER BY ORDINAL_POSITION;

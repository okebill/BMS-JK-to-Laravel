-- Tambah kolom mos_temp dan alarm_is_real ke monitoring_logs
-- Jalankan di MySQL server bms.okebil.com

ALTER TABLE `monitoring_logs`
  ADD COLUMN IF NOT EXISTS `mos_temp`      DECIMAL(5,2) NULL COMMENT 'MOSFET temperature °C' AFTER `temperature2`,
  ADD COLUMN IF NOT EXISTS `alarm_is_real` TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '1 jika alarm berbahaya' AFTER `alarm_text`;

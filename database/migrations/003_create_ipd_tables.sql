-- Migration: Create IPD (In-Patient Department) module tables
-- Date: 2026-01-24
-- Description: Creates tables for ipd_wards, ipd_beds, ipd_admissions, 
--              ipd_progress_notes, and ipd_medication_admin

-- IPD Wards
CREATE TABLE IF NOT EXISTS `ipd_wards` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ward_name` VARCHAR(100) NOT NULL,
  `ward_code` VARCHAR(20) NOT NULL UNIQUE,
  `ward_type` ENUM('general','private','icu','maternity','pediatric','isolation') DEFAULT 'general',
  `total_beds` INT NOT NULL DEFAULT 0,
  `occupied_beds` INT NOT NULL DEFAULT 0,
  `floor_number` INT,
  `description` TEXT,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_ward_type` (`ward_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- IPD Beds
CREATE TABLE IF NOT EXISTS `ipd_beds` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ward_id` INT NOT NULL,
  `bed_number` VARCHAR(20) NOT NULL,
  `bed_type` ENUM('standard','oxygen','icu','isolation') DEFAULT 'standard',
  `status` ENUM('available','occupied','maintenance','reserved') DEFAULT 'available',
  `daily_rate` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `notes` TEXT,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`ward_id`) REFERENCES `ipd_wards`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_bed` (`ward_id`, `bed_number`),
  INDEX `idx_status` (`status`),
  INDEX `idx_ward` (`ward_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- IPD Admissions
CREATE TABLE IF NOT EXISTS `ipd_admissions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `patient_id` INT NOT NULL,
  `visit_id` INT NOT NULL,
  `bed_id` INT NOT NULL,
  `admission_number` VARCHAR(50) NOT NULL UNIQUE,
  `admission_datetime` DATETIME NOT NULL,
  `discharge_datetime` DATETIME DEFAULT NULL,
  `admission_type` ENUM('emergency','planned','transfer') DEFAULT 'planned',
  `admission_diagnosis` TEXT,
  `discharge_diagnosis` TEXT,
  `discharge_summary` TEXT,
  `admitted_by` INT NOT NULL,
  `attending_doctor` INT DEFAULT NULL,
  `discharged_by` INT DEFAULT NULL,
  `status` ENUM('active','discharged','transferred','deceased') DEFAULT 'active',
  `total_days` INT DEFAULT NULL COMMENT 'Calculated days of stay',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`visit_id`) REFERENCES `patient_visits`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`bed_id`) REFERENCES `ipd_beds`(`id`),
  FOREIGN KEY (`admitted_by`) REFERENCES `users`(`id`),
  FOREIGN KEY (`attending_doctor`) REFERENCES `users`(`id`),
  FOREIGN KEY (`discharged_by`) REFERENCES `users`(`id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_patient` (`patient_id`),
  INDEX `idx_bed` (`bed_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- IPD Progress Notes (Daily nursing/doctor observations)
CREATE TABLE IF NOT EXISTS `ipd_progress_notes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `admission_id` INT NOT NULL,
  `patient_id` INT NOT NULL,
  `note_datetime` DATETIME NOT NULL,
  `note_type` ENUM('doctor','nurse','other') DEFAULT 'doctor',
  `temperature` DECIMAL(4,1) COMMENT 'Celsius',
  `blood_pressure_systolic` INT,
  `blood_pressure_diastolic` INT,
  `pulse_rate` INT COMMENT 'bpm',
  `respiratory_rate` INT COMMENT 'breaths per minute',
  `oxygen_saturation` INT COMMENT 'SpO2 percentage',
  `progress_note` TEXT,
  `recorded_by` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`admission_id`) REFERENCES `ipd_admissions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`recorded_by`) REFERENCES `users`(`id`),
  INDEX `idx_admission` (`admission_id`),
  INDEX `idx_note_datetime` (`note_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- IPD Medication Administration (Nurse medication tracking)
CREATE TABLE IF NOT EXISTS `ipd_medication_admin` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `admission_id` INT NOT NULL,
  `patient_id` INT NOT NULL,
  `medicine_id` INT NOT NULL,
  `prescribed_by` INT NOT NULL COMMENT 'Doctor user_id',
  `administered_by` INT DEFAULT NULL COMMENT 'Nurse user_id',
  `scheduled_datetime` DATETIME NOT NULL,
  `administered_datetime` DATETIME DEFAULT NULL,
  `dose` VARCHAR(100),
  `route` ENUM('oral','IV','IM','SC','topical','other') DEFAULT 'oral',
  `status` ENUM('scheduled','administered','missed','cancelled') DEFAULT 'scheduled',
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`admission_id`) REFERENCES `ipd_admissions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`medicine_id`) REFERENCES `medicines`(`id`),
  FOREIGN KEY (`prescribed_by`) REFERENCES `users`(`id`),
  FOREIGN KEY (`administered_by`) REFERENCES `users`(`id`),
  INDEX `idx_admission` (`admission_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_scheduled` (`scheduled_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Trigger to automatically calculate total_days on admission insert
DELIMITER $$
CREATE TRIGGER calculate_admission_days_insert 
BEFORE INSERT ON ipd_admissions
FOR EACH ROW
BEGIN
  SET NEW.total_days = DATEDIFF(COALESCE(NEW.discharge_datetime, CURDATE()), NEW.admission_datetime);
END$$

-- Trigger to automatically calculate total_days on admission update
CREATE TRIGGER calculate_admission_days_update 
BEFORE UPDATE ON ipd_admissions
FOR EACH ROW
BEGIN
  SET NEW.total_days = DATEDIFF(COALESCE(NEW.discharge_datetime, CURDATE()), NEW.admission_datetime);
END$$
DELIMITER ;

-- Verify the changes
SELECT 'Migration 003 completed successfully' AS status;
SELECT COUNT(*) AS ipd_tables_created FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name LIKE 'ipd%';

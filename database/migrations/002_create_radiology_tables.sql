-- Migration: Create Radiology module tables
-- Date: 2026-01-24
-- Description: Creates tables for radiology_test_categories, radiology_tests, 
--              radiology_test_orders, and radiology_results

-- Radiology Test Categories
CREATE TABLE IF NOT EXISTS `radiology_test_categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `category_name` VARCHAR(100) NOT NULL,
  `category_code` VARCHAR(20) NOT NULL UNIQUE,
  `description` TEXT,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Radiology Tests
CREATE TABLE IF NOT EXISTS `radiology_tests` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `test_name` VARCHAR(200) NOT NULL,
  `test_code` VARCHAR(50) NOT NULL UNIQUE,
  `category_id` INT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `description` TEXT,
  `preparation_instructions` TEXT,
  `estimated_duration` INT COMMENT 'Minutes',
  `requires_contrast` TINYINT(1) DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`category_id`) REFERENCES `radiology_test_categories`(`id`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Radiology Test Orders
CREATE TABLE IF NOT EXISTS `radiology_test_orders` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `visit_id` INT NOT NULL,
  `patient_id` INT NOT NULL,
  `test_id` INT NOT NULL,
  `ordered_by` INT NOT NULL COMMENT 'Doctor user_id',
  `assigned_to` INT DEFAULT NULL COMMENT 'Radiologist user_id',
  `priority` ENUM('normal','urgent','stat') DEFAULT 'normal',
  `status` ENUM('pending','scheduled','in_progress','completed','cancelled') DEFAULT 'pending',
  `clinical_notes` TEXT,
  `scheduled_datetime` DATETIME DEFAULT NULL,
  `cancellation_reason` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`visit_id`) REFERENCES `patient_visits`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`test_id`) REFERENCES `radiology_tests`(`id`),
  FOREIGN KEY (`ordered_by`) REFERENCES `users`(`id`),
  FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_patient` (`patient_id`),
  INDEX `idx_ordered_by` (`ordered_by`),
  INDEX `idx_assigned_to` (`assigned_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Radiology Results
CREATE TABLE IF NOT EXISTS `radiology_results` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `order_id` INT NOT NULL,
  `patient_id` INT NOT NULL,
  `test_id` INT NOT NULL,
  `findings` TEXT,
  `impression` TEXT,
  `recommendations` TEXT,
  `images_path` VARCHAR(255) COMMENT 'Path to uploaded images',
  `is_normal` TINYINT(1) DEFAULT 1,
  `is_critical` TINYINT(1) DEFAULT 0,
  `radiologist_id` INT NOT NULL,
  `radiologist_notes` TEXT,
  `completed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `reviewed_by` INT DEFAULT NULL,
  `reviewed_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_order_result` (`order_id`),
  FOREIGN KEY (`order_id`) REFERENCES `radiology_test_orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`test_id`) REFERENCES `radiology_tests`(`id`),
  FOREIGN KEY (`radiologist_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`),
  INDEX `idx_order` (`order_id`),
  INDEX `idx_patient` (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Verify the changes
SELECT 'Migration 002 completed successfully' AS status;
SELECT COUNT(*) AS radiology_tables_created FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name LIKE 'radiology%';

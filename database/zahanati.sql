-- ULTIMATE Dispensary Database Schema
-- Future-ready with current requirements

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+03:00";

-- --------------------------------------------------------
-- CORE TABLES
-- --------------------------------------------------------

-- Users table (Admin, Receptionist, Doctor, Lab Technician)
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `role` ENUM('admin','receptionist','doctor','lab_technician') NOT NULL,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `specialization` VARCHAR(100) DEFAULT NULL COMMENT 'For doctors - future use',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_users_role` (`role`),
  KEY `idx_users_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Patients table (core patient information only)
CREATE TABLE `patients` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `registration_number` VARCHAR(20) NOT NULL UNIQUE,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `date_of_birth` DATE DEFAULT NULL,
  `gender` ENUM('male','female','other') DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `occupation` VARCHAR(100) DEFAULT NULL,
  `emergency_contact_name` VARCHAR(100) DEFAULT NULL,
  `emergency_contact_phone` VARCHAR(20) DEFAULT NULL,
  `blood_group` VARCHAR(5) DEFAULT NULL COMMENT 'A+, B-, O+, etc',
  `allergies` TEXT DEFAULT NULL COMMENT 'Known allergies',
  `chronic_conditions` TEXT DEFAULT NULL COMMENT 'Diabetes, Hypertension, etc',
  `insurance_company` VARCHAR(100) DEFAULT NULL COMMENT 'Future use',
  `insurance_number` VARCHAR(50) DEFAULT NULL COMMENT 'Future use',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_patients_reg_number` (`registration_number`),
  KEY `idx_patients_phone` (`phone`),
  KEY `idx_patients_name` (`first_name`, `last_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Patient visits (each registration creates a visit)
CREATE TABLE `patient_visits` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `patient_id` INT(11) NOT NULL,
  `visit_number` INT(11) NOT NULL COMMENT 'Sequential visit number for this patient',
  `visit_date` DATE NOT NULL DEFAULT (CURDATE()),
  `visit_type` ENUM('consultation','lab_only','minor_service') NOT NULL,
  `assigned_doctor_id` INT(11) DEFAULT NULL COMMENT 'Future: pre-assigned doctor',
  `registered_by` INT(11) NOT NULL,
  `status` ENUM('active','completed','cancelled') DEFAULT 'active',
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_visit_patient` (`patient_id`),
  KEY `idx_visit_date` (`visit_date`),
  KEY `idx_visit_status` (`status`),
  KEY `idx_visit_number` (`patient_id`, `visit_number`),
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`assigned_doctor_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`registered_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Consultations (doctor attendance records)
CREATE TABLE `consultations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `visit_id` INT(11) NOT NULL,
  `patient_id` INT(11) NOT NULL,
  `doctor_id` INT(11) NOT NULL,
  `consultation_number` INT(11) DEFAULT 1 COMMENT 'Multiple doctors can see patient same visit',
  `consultation_type` ENUM('new','follow_up','emergency','referral') DEFAULT 'new',
  `main_complaint` TEXT DEFAULT NULL,
  `history_of_present_illness` TEXT DEFAULT NULL,
  `on_examination` TEXT DEFAULT NULL,
  `diagnosis` TEXT DEFAULT NULL,
  `treatment_plan` TEXT DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `follow_up_required` TINYINT(1) DEFAULT 0,
  `follow_up_date` DATE DEFAULT NULL,
  `follow_up_instructions` TEXT DEFAULT NULL,
  `referred_to` VARCHAR(200) DEFAULT NULL COMMENT 'Future: referral destination',
  `referral_reason` TEXT DEFAULT NULL COMMENT 'Future: why referred',
  `status` ENUM('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `cancellation_reason` TEXT DEFAULT NULL,
  `started_at` TIMESTAMP NULL DEFAULT NULL,
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_consultation_visit` (`visit_id`),
  KEY `idx_consultation_patient` (`patient_id`),
  KEY `idx_consultation_doctor` (`doctor_id`),
  KEY `idx_consultation_status` (`status`),
  FOREIGN KEY (`visit_id`) REFERENCES `patient_visits`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`doctor_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Vital signs (recorded at each visit)
CREATE TABLE `vital_signs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `visit_id` INT(11) NOT NULL,
  `patient_id` INT(11) NOT NULL,
  `temperature` DECIMAL(4,1) DEFAULT NULL COMMENT 'Celsius',
  `blood_pressure_systolic` INT(11) DEFAULT NULL,
  `blood_pressure_diastolic` INT(11) DEFAULT NULL,
  `pulse_rate` INT(11) DEFAULT NULL COMMENT 'bpm',
  `respiratory_rate` INT(11) DEFAULT NULL COMMENT 'breaths per minute',
  `weight` DECIMAL(5,1) DEFAULT NULL COMMENT 'kg',
  `height` DECIMAL(5,1) DEFAULT NULL COMMENT 'cm',
  `bmi` DECIMAL(4,1) AS (CASE WHEN height > 0 THEN (weight / ((height/100) * (height/100))) ELSE NULL END) STORED,
  `recorded_by` INT(11) NOT NULL,
  `recorded_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vitals_visit` (`visit_id`),
  KEY `idx_vitals_patient` (`patient_id`),
  FOREIGN KEY (`visit_id`) REFERENCES `patient_visits`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`recorded_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- PAYMENT SYSTEM
-- --------------------------------------------------------

-- All payments in one place
CREATE TABLE `payments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `visit_id` INT(11) NOT NULL,
  `patient_id` INT(11) NOT NULL,
  `payment_type` ENUM('registration','lab_test','medicine','minor_service') NOT NULL,
  `item_id` INT(11) DEFAULT NULL COMMENT 'Reference to lab_order, prescription, or service',
  `item_type` ENUM('lab_order','prescription','service') DEFAULT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `payment_method` ENUM('cash','card','mobile_money','insurance') NOT NULL,
  `payment_status` ENUM('pending','paid','cancelled','refunded') DEFAULT 'pending',
  `reference_number` VARCHAR(100) DEFAULT NULL COMMENT 'Receipt/Transaction number',
  `collected_by` INT(11) NOT NULL,
  `payment_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notes` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_payment_visit` (`visit_id`),
  KEY `idx_payment_patient` (`patient_id`),
  KEY `idx_payment_type` (`payment_type`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_payment_date` (`payment_date`),
  FOREIGN KEY (`visit_id`) REFERENCES `patient_visits`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`collected_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- LAB SYSTEM
-- --------------------------------------------------------

-- Lab test categories
CREATE TABLE `lab_test_categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `category_name` VARCHAR(100) NOT NULL,
  `category_code` VARCHAR(20) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Lab tests catalog
CREATE TABLE `lab_tests` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `test_name` VARCHAR(200) NOT NULL,
  `test_code` VARCHAR(50) NOT NULL UNIQUE,
  `category_id` INT(11) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `normal_range` VARCHAR(100) DEFAULT NULL,
  `unit` VARCHAR(20) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `turn_around_time` INT(11) DEFAULT NULL COMMENT 'Expected time in minutes',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_test_category` (`category_id`),
  KEY `idx_test_active` (`is_active`),
  FOREIGN KEY (`category_id`) REFERENCES `lab_test_categories`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Lab test orders (when tests are requested)
CREATE TABLE `lab_test_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `visit_id` INT(11) NOT NULL,
  `patient_id` INT(11) NOT NULL,
  `consultation_id` INT(11) DEFAULT NULL COMMENT 'NULL if direct lab visit',
  `test_id` INT(11) NOT NULL,
  `ordered_by` INT(11) NOT NULL COMMENT 'Doctor or Receptionist',
  `assigned_to` INT(11) DEFAULT NULL COMMENT 'Lab technician',
  `priority` ENUM('normal','urgent','stat') DEFAULT 'normal',
  `status` ENUM('pending','sample_collected','in_progress','completed','cancelled') DEFAULT 'pending',
  `cancellation_reason` TEXT DEFAULT NULL,
  `instructions` TEXT DEFAULT NULL,
  `sample_collected_at` TIMESTAMP NULL DEFAULT NULL,
  `expected_completion` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_visit` (`visit_id`),
  KEY `idx_order_patient` (`patient_id`),
  KEY `idx_order_consultation` (`consultation_id`),
  KEY `idx_order_test` (`test_id`),
  KEY `idx_order_status` (`status`),
  KEY `idx_order_assigned` (`assigned_to`, `status`),
  FOREIGN KEY (`visit_id`) REFERENCES `patient_visits`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`consultation_id`) REFERENCES `consultations`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`test_id`) REFERENCES `lab_tests`(`id`),
  FOREIGN KEY (`ordered_by`) REFERENCES `users`(`id`),
  FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Lab test results
CREATE TABLE `lab_results` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `patient_id` INT(11) NOT NULL,
  `test_id` INT(11) NOT NULL,
  `result_value` VARCHAR(100) DEFAULT NULL,
  `result_text` TEXT DEFAULT NULL,
  `result_unit` VARCHAR(50) DEFAULT NULL,
  `is_normal` TINYINT(1) DEFAULT 1,
  `is_critical` TINYINT(1) DEFAULT 0,
  `interpretation` TEXT DEFAULT NULL,
  `technician_id` INT(11) NOT NULL,
  `technician_notes` TEXT DEFAULT NULL,
  `completed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_by` INT(11) DEFAULT NULL COMMENT 'Doctor who reviewed',
  `reviewed_at` TIMESTAMP NULL DEFAULT NULL,
  `review_notes` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_result_order` (`order_id`),
  KEY `idx_result_patient` (`patient_id`),
  KEY `idx_result_test` (`test_id`),
  KEY `idx_result_critical` (`is_critical`),
  FOREIGN KEY (`order_id`) REFERENCES `lab_test_orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`test_id`) REFERENCES `lab_tests`(`id`),
  FOREIGN KEY (`technician_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- MEDICINE SYSTEM
-- --------------------------------------------------------

-- Medicine inventory with batch tracking
CREATE TABLE `medicines` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `generic_name` VARCHAR(100) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `strength` VARCHAR(50) DEFAULT NULL COMMENT 'e.g., 500mg, 250mg/5ml',
  `unit` VARCHAR(20) DEFAULT NULL COMMENT 'tablets, capsules, ml, etc',
  `unit_price` DECIMAL(10,2) NOT NULL,
  `reorder_level` INT(11) DEFAULT 20 COMMENT 'Alert when stock below this',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_medicine_active` (`is_active`),
  KEY `idx_medicine_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Medicine batches (track different batches with different expiry dates)
CREATE TABLE `medicine_batches` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `medicine_id` INT(11) NOT NULL,
  `batch_number` VARCHAR(50) NOT NULL,
  `quantity_received` INT(11) NOT NULL,
  `quantity_remaining` INT(11) NOT NULL,
  `expiry_date` DATE NOT NULL,
  `supplier` VARCHAR(100) DEFAULT NULL,
  `cost_price` DECIMAL(10,2) DEFAULT NULL,
  `received_date` DATE NOT NULL,
  `received_by` INT(11) NOT NULL,
  `status` ENUM('active','expired','depleted') DEFAULT 'active',
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_batch_medicine` (`medicine_id`),
  KEY `idx_batch_expiry` (`expiry_date`),
  KEY `idx_batch_status` (`status`),
  UNIQUE KEY `unique_batch` (`medicine_id`, `batch_number`),
  FOREIGN KEY (`medicine_id`) REFERENCES `medicines`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`received_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Medicine prescriptions (doctor allocates medicine)
CREATE TABLE `prescriptions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `consultation_id` INT(11) NOT NULL,
  `visit_id` INT(11) NOT NULL,
  `patient_id` INT(11) NOT NULL,
  `doctor_id` INT(11) NOT NULL,
  `medicine_id` INT(11) NOT NULL,
  `quantity_prescribed` INT(11) NOT NULL,
  `quantity_dispensed` INT(11) DEFAULT 0 COMMENT 'Actual amount given',
  `dosage` VARCHAR(100) DEFAULT NULL COMMENT 'e.g., 1 tablet',
  `frequency` VARCHAR(100) DEFAULT NULL COMMENT 'e.g., 2x3 (twice, 3 times daily)',
  `duration` VARCHAR(50) DEFAULT NULL COMMENT 'e.g., 7 days',
  `instructions` TEXT DEFAULT NULL,
  `status` ENUM('pending','partial','dispensed','cancelled') DEFAULT 'pending',
  `cancellation_reason` TEXT DEFAULT NULL,
  `dispensed_by` INT(11) DEFAULT NULL,
  `dispensed_at` TIMESTAMP NULL DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_prescription_consultation` (`consultation_id`),
  KEY `idx_prescription_visit` (`visit_id`),
  KEY `idx_prescription_patient` (`patient_id`),
  KEY `idx_prescription_medicine` (`medicine_id`),
  KEY `idx_prescription_status` (`status`),
  FOREIGN KEY (`consultation_id`) REFERENCES `consultations`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`visit_id`) REFERENCES `patient_visits`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`doctor_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`medicine_id`) REFERENCES `medicines`(`id`),
  FOREIGN KEY (`dispensed_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Medicine dispensing details (tracks which batch was used for partial dispensing)
CREATE TABLE `medicine_dispensing` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `prescription_id` INT(11) NOT NULL,
  `batch_id` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL,
  `dispensed_by` INT(11) NOT NULL,
  `dispensed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dispensing_prescription` (`prescription_id`),
  KEY `idx_dispensing_batch` (`batch_id`),
  FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`batch_id`) REFERENCES `medicine_batches`(`id`),
  FOREIGN KEY (`dispensed_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- SERVICES (Future-ready for minor services)
-- --------------------------------------------------------

CREATE TABLE `services` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `service_name` VARCHAR(100) NOT NULL,
  `service_code` VARCHAR(20) NOT NULL UNIQUE,
  `price` DECIMAL(10,2) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `requires_doctor` TINYINT(1) DEFAULT 0 COMMENT 'Whether doctor must be involved',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_service_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Service orders (when patient gets minor services)
CREATE TABLE `service_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `visit_id` INT(11) NOT NULL,
  `patient_id` INT(11) NOT NULL,
  `service_id` INT(11) NOT NULL,
  `ordered_by` INT(11) NOT NULL COMMENT 'Doctor or Receptionist',
  `performed_by` INT(11) DEFAULT NULL,
  `status` ENUM('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `cancellation_reason` TEXT DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `performed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_service_order_visit` (`visit_id`),
  KEY `idx_service_order_patient` (`patient_id`),
  KEY `idx_service_order_service` (`service_id`),
  KEY `idx_service_order_status` (`status`),
  FOREIGN KEY (`visit_id`) REFERENCES `patient_visits`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`service_id`) REFERENCES `services`(`id`),
  FOREIGN KEY (`ordered_by`) REFERENCES `users`(`id`),
  FOREIGN KEY (`performed_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- USEFUL VIEWS FOR REPORTING
-- --------------------------------------------------------

-- Active patient queue with all status info
CREATE VIEW `active_patient_queue` AS
SELECT 
    v.id AS visit_id,
    v.visit_type,
    v.visit_date,
    p.id AS patient_id,
    p.registration_number,
    CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
    p.phone,
    p.gender,
    TIMESTAMPDIFF(YEAR, p.date_of_birth, CURDATE()) AS age,
    -- Vital signs
    vs.temperature,
    vs.pulse_rate,
    vs.blood_pressure_systolic,
    vs.blood_pressure_diastolic,
    -- Consultation status
    c.id AS consultation_id,
    c.status AS consultation_status,
    CONCAT(u.first_name, ' ', u.last_name) AS doctor_name,
    -- Payment status
    SUM(CASE WHEN pay.payment_status = 'paid' AND pay.payment_type = 'registration' THEN pay.amount ELSE 0 END) AS registration_paid,
    -- Lab status
    COUNT(DISTINCT CASE WHEN lo.status IN ('pending','sample_collected','in_progress') THEN lo.id END) AS pending_lab_tests,
    COUNT(DISTINCT CASE WHEN lo.status = 'completed' THEN lo.id END) AS completed_lab_tests,
    -- Prescription status
    COUNT(DISTINCT CASE WHEN pr.status = 'pending' THEN pr.id END) AS pending_prescriptions,
    COUNT(DISTINCT CASE WHEN pr.status = 'partial' THEN pr.id END) AS partial_prescriptions,
    v.created_at AS registration_time
FROM patient_visits v
JOIN patients p ON v.patient_id = p.id
LEFT JOIN vital_signs vs ON v.id = vs.visit_id
LEFT JOIN consultations c ON v.id = c.visit_id AND c.status != 'cancelled'
LEFT JOIN users u ON c.doctor_id = u.id
LEFT JOIN payments pay ON v.id = pay.visit_id
LEFT JOIN lab_test_orders lo ON v.id = lo.visit_id AND lo.status != 'cancelled'
LEFT JOIN prescriptions pr ON v.id = pr.visit_id AND pr.status != 'cancelled'
WHERE v.status = 'active'
GROUP BY v.id, v.visit_type, v.visit_date, p.id, p.registration_number, 
         p.first_name, p.last_name, p.phone, p.gender, p.date_of_birth,
         vs.temperature, vs.pulse_rate, vs.blood_pressure_systolic, vs.blood_pressure_diastolic,
         c.id, c.status, u.first_name, u.last_name, v.created_at
ORDER BY v.created_at;

-- Daily revenue summary
CREATE VIEW `daily_revenue_summary` AS
SELECT 
    DATE(payment_date) AS revenue_date,
    payment_type,
    payment_method,
    COUNT(*) AS transaction_count,
    SUM(amount) AS total_amount,
    CONCAT(uc.first_name, ' ', uc.last_name) AS collected_by_name
FROM payments
JOIN users uc ON payments.collected_by = uc.id
WHERE payment_status = 'paid'
GROUP BY DATE(payment_date), payment_type, payment_method, uc.first_name, uc.last_name
ORDER BY revenue_date DESC, payment_type;

-- Medicine stock status
CREATE VIEW `medicine_stock_status` AS
SELECT 
    m.id,
    m.name,
    m.generic_name,
    m.strength,
    m.unit,
    m.unit_price,
    m.reorder_level,
    SUM(mb.quantity_remaining) AS total_stock,
    COUNT(DISTINCT mb.id) AS active_batches,
    MIN(CASE WHEN mb.status = 'active' THEN mb.expiry_date END) AS nearest_expiry,
    CASE 
        WHEN SUM(mb.quantity_remaining) <= m.reorder_level THEN 'LOW_STOCK'
        WHEN MIN(CASE WHEN mb.status = 'active' THEN mb.expiry_date END) <= DATE_ADD(CURDATE(), INTERVAL 3 MONTH) THEN 'EXPIRING_SOON'
        ELSE 'OK'
    END AS stock_alert
FROM medicines m
LEFT JOIN medicine_batches mb ON m.id = mb.medicine_id AND mb.status = 'active'
WHERE m.is_active = 1
GROUP BY m.id, m.name, m.generic_name, m.strength, m.unit, m.unit_price, m.reorder_level;

-- Most prescribed medicines
CREATE VIEW `medicine_prescription_stats` AS
SELECT 
    m.id,
    m.name,
    m.generic_name,
    COUNT(pr.id) AS times_prescribed,
    SUM(pr.quantity_dispensed) AS total_quantity_dispensed,
    SUM(pr.quantity_dispensed * m.unit_price) AS total_revenue
FROM medicines m
JOIN prescriptions pr ON m.id = pr.medicine_id
WHERE pr.status IN ('dispensed', 'partial')
GROUP BY m.id, m.name, m.generic_name
ORDER BY times_prescribed DESC;

-- Most common diagnoses
CREATE VIEW `common_diagnoses` AS
SELECT 
    diagnosis,
    COUNT(*) AS occurrence_count,
    COUNT(DISTINCT patient_id) AS unique_patients
FROM consultations
WHERE diagnosis IS NOT NULL 
  AND diagnosis != ''
  AND status = 'completed'
GROUP BY diagnosis
ORDER BY occurrence_count DESC;

-- Staff performance summary
CREATE VIEW `staff_performance` AS
SELECT 
    u.id,
    CONCAT(u.first_name, ' ', u.last_name) AS staff_name,
    u.role,
    -- For receptionists
    COUNT(DISTINCT CASE WHEN u.role = 'receptionist' THEN v.id END) AS patients_registered,
    COUNT(DISTINCT CASE WHEN u.role = 'receptionist' THEN p.id END) AS payments_collected,
    SUM(CASE WHEN u.role = 'receptionist' AND p.payment_status = 'paid' THEN p.amount ELSE 0 END) AS total_collected,
    -- For doctors
    COUNT(DISTINCT CASE WHEN u.role = 'doctor' THEN c.id END) AS consultations_completed,
    COUNT(DISTINCT CASE WHEN u.role = 'doctor' THEN pr.id END) AS prescriptions_written,
    -- For lab technicians
    COUNT(DISTINCT CASE WHEN u.role = 'lab_technician' THEN lr.id END) AS tests_completed
FROM users u
LEFT JOIN patient_visits v ON u.id = v.registered_by
LEFT JOIN payments p ON u.id = p.collected_by
LEFT JOIN consultations c ON u.id = c.doctor_id AND c.status = 'completed'
LEFT JOIN prescriptions pr ON u.id = pr.doctor_id
LEFT JOIN lab_results lr ON u.id = lr.technician_id
WHERE u.is_active = 1
GROUP BY u.id, u.first_name, u.last_name, u.role;

-- Latest visit per patient (for efficient queries)
CREATE VIEW `patient_latest_visit` AS
SELECT
    pv.patient_id,
    pv.id AS visit_id,
    pv.visit_number,
    pv.status,
    pv.visit_type,
    pv.visit_date,
    pv.created_at,
    pv.updated_at
FROM patient_visits pv
JOIN (
    SELECT patient_id, MAX(created_at) AS latest FROM patient_visits GROUP BY patient_id
) latest ON latest.patient_id = pv.patient_id AND latest.latest = pv.created_at;

-- --------------------------------------------------------
-- SEED DATA
-- --------------------------------------------------------

-- Demo users with password hashes (password: admin123, password, password, password)
INSERT INTO `users` (`username`, `password_hash`, `email`, `role`, `first_name`, `last_name`, `phone`, `is_active`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@clinic.local', 'admin', 'System', 'Administrator', '0700000001', 1),
('reception', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'reception@clinic.local', 'receptionist', 'Jane', 'Receptionist', '0700000002', 1),
('doctor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor@clinic.local', 'doctor', 'Dr. John', 'Smith', '0700000003', 1),
('lab', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lab@clinic.local', 'lab_technician', 'Mary', 'Technician', '0700000004', 1);

-- Lab test categories
INSERT INTO `lab_test_categories` (`category_name`, `category_code`, `description`, `is_active`) VALUES
('Hematology', 'HEMA', 'Blood cell counts and related tests', 1),
('Clinical Chemistry', 'CHEM', 'Chemical analysis of blood and body fluids', 1),
('Microbiology', 'MICRO', 'Bacterial, viral, and fungal tests', 1),
('Immunology', 'IMMUNO', 'Immune system and antibody tests', 1),
('Urinalysis', 'URINE', 'Urine examination tests', 1);

-- Common lab tests with prices
INSERT INTO `lab_tests` (`test_name`, `test_code`, `category_id`, `price`, `normal_range`, `unit`, `description`, `is_active`) VALUES
('Complete Blood Count', 'CBC', 1, 15000.00, 'RBC: 4.5-5.5, WBC: 4-11, Hb: 12-16', 'cells/mcL', 'Full blood count analysis', 1),
('Blood Sugar (Random)', 'BS-R', 2, 5000.00, '70-140', 'mg/dL', 'Random blood glucose test', 1),
('Blood Sugar (Fasting)', 'BS-F', 2, 5000.00, '70-100', 'mg/dL', 'Fasting blood glucose test', 1),
('Malaria Test', 'MAL', 3, 8000.00, 'Negative', '', 'Malaria parasite detection', 1),
('Urinalysis', 'URINE', 5, 6000.00, 'Normal', '', 'Complete urine examination', 1),
('Stool Examination', 'STOOL', 3, 7000.00, 'Normal', '', 'Stool microscopy and culture', 1),
('Liver Function Test', 'LFT', 2, 25000.00, 'ALT: 7-56, AST: 10-40', 'U/L', 'Complete liver function panel', 1),
('Kidney Function Test', 'KFT', 2, 25000.00, 'Creatinine: 0.7-1.3', 'mg/dL', 'Renal function assessment', 1),
('Pregnancy Test', 'PREG', 4, 5000.00, 'Positive/Negative', '', 'hCG detection in urine', 1),
('HIV Test', 'HIV', 4, 10000.00, 'Non-reactive', '', 'HIV antibody screening', 1),
('Hepatitis B Surface Antigen', 'HBsAg', 4, 15000.00, 'Negative', '', 'Hepatitis B screening', 1),
('Widal Test', 'WIDAL', 4, 12000.00, 'Non-reactive', '', 'Typhoid fever antibody test', 1),
('ESR', 'ESR', 1, 5000.00, 'Male: 0-15, Female: 0-20', 'mm/hr', 'Erythrocyte sedimentation rate', 1),
('Blood Group & Rh', 'BG-RH', 1, 8000.00, 'A/B/AB/O, Rh+/-', '', 'Blood typing and Rh factor', 1),
('X-Ray Chest', 'XRAY-C', 1, 30000.00, 'Normal', '', 'Chest radiography', 1);

-- Common medicines (15 essential medicines)
INSERT INTO `medicines` (`name`, `generic_name`, `description`, `strength`, `unit`, `unit_price`, `reorder_level`, `is_active`) VALUES
('Paracetamol', 'Acetaminophen', 'Pain relief and fever reduction', '500mg', 'tablets', 50.00, 500, 1),
('Amoxicillin', 'Amoxicillin', 'Antibiotic for bacterial infections', '500mg', 'capsules', 200.00, 300, 1),
('Metronidazole', 'Metronidazole', 'Antibiotic and antiprotozoal', '400mg', 'tablets', 150.00, 300, 1),
('Ibuprofen', 'Ibuprofen', 'Anti-inflammatory and pain relief', '400mg', 'tablets', 100.00, 400, 1),
('Ciprofloxacin', 'Ciprofloxacin', 'Broad-spectrum antibiotic', '500mg', 'tablets', 300.00, 200, 1),
('Omeprazole', 'Omeprazole', 'Reduces stomach acid production', '20mg', 'capsules', 250.00, 200, 1),
('Chloroquine', 'Chloroquine', 'Antimalarial medication', '250mg', 'tablets', 100.00, 500, 1),
('Artemether-Lumefantrine', 'AL', 'First-line malaria treatment', '20/120mg', 'tablets', 500.00, 300, 1),
('Metformin', 'Metformin', 'Type 2 diabetes management', '500mg', 'tablets', 100.00, 400, 1),
('Amlodipine', 'Amlodipine', 'Blood pressure medication', '5mg', 'tablets', 150.00, 300, 1),
('Salbutamol', 'Salbutamol', 'Asthma relief inhaler', '100mcg', 'inhaler', 1500.00, 50, 1),
('Cetirizine', 'Cetirizine', 'Antihistamine for allergies', '10mg', 'tablets', 80.00, 300, 1),
('Multivitamins', 'Multivitamins', 'Daily vitamin supplement', 'Adult', 'tablets', 150.00, 200, 1),
('ORS', 'Oral Rehydration Salts', 'Dehydration treatment', '27.9g', 'sachets', 200.00, 500, 1),
('Diclofenac', 'Diclofenac', 'Pain and inflammation relief', '50mg', 'tablets', 120.00, 300, 1);

-- Sample medicine batches for initial stock
INSERT INTO `medicine_batches` (`medicine_id`, `batch_number`, `quantity_received`, `quantity_remaining`, `expiry_date`, `supplier`, `cost_price`, `received_date`, `received_by`, `status`) VALUES
(1, 'PARA-2024-001', 1000, 1000, '2026-12-31', 'MedSupply Ltd', 40.00, '2024-01-15', 1, 'active'),
(2, 'AMOX-2024-001', 500, 500, '2026-06-30', 'MedSupply Ltd', 150.00, '2024-01-15', 1, 'active'),
(3, 'METRO-2024-001', 500, 500, '2026-08-31', 'PharmaDistrib', 120.00, '2024-01-15', 1, 'active'),
(4, 'IBU-2024-001', 800, 800, '2027-03-31', 'MedSupply Ltd', 80.00, '2024-01-15', 1, 'active'),
(5, 'CIPRO-2024-001', 300, 300, '2026-10-31', 'PharmaDistrib', 250.00, '2024-01-15', 1, 'active'),
(6, 'OMEP-2024-001', 400, 400, '2026-09-30', 'MedSupply Ltd', 200.00, '2024-01-15', 1, 'active'),
(7, 'CHL-2024-001', 1000, 1000, '2027-12-31', 'PharmaDistrib', 80.00, '2024-01-15', 1, 'active'),
(8, 'AL-2024-001', 600, 600, '2026-11-30', 'Global Health', 400.00, '2024-01-15', 1, 'active'),
(9, 'MET-2024-001', 800, 800, '2027-06-30', 'MedSupply Ltd', 80.00, '2024-01-15', 1, 'active'),
(10, 'AML-2024-001', 500, 500, '2026-12-31', 'PharmaDistrib', 120.00, '2024-01-15', 1, 'active'),
(11, 'SAL-2024-001', 100, 100, '2026-05-31', 'RespiCare', 1200.00, '2024-01-15', 1, 'active'),
(12, 'CET-2024-001', 600, 600, '2027-02-28', 'MedSupply Ltd', 60.00, '2024-01-15', 1, 'active'),
(13, 'MULTI-2024-001', 400, 400, '2026-12-31', 'Nutrition Plus', 120.00, '2024-01-15', 1, 'active'),
(14, 'ORS-2024-001', 1000, 1000, '2027-12-31', 'WHO Supply', 150.00, '2024-01-15', 1, 'active'),
(15, 'DICLO-2024-001', 600, 600, '2026-08-31', 'PharmaDistrib', 100.00, '2024-01-15', 1, 'active');

-- Sample services
INSERT INTO `services` (`service_name`, `service_code`, `price`, `description`, `requires_doctor`, `is_active`) VALUES
('Consultation Fee', 'CONSULT', 3000.00, 'Standard medical consultation', 1, 1),
('Blood Pressure Check', 'BP-CHECK', 1000.00, 'Blood pressure measurement', 0, 1),
('Wound Dressing', 'DRESS', 5000.00, 'Wound cleaning and dressing', 0, 1),
('Injection', 'INJ', 2000.00, 'Intramuscular or IV injection', 0, 1),
('ECG', 'ECG', 20000.00, 'Electrocardiogram recording', 0, 1);

COMMIT;
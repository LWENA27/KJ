-- Lab Equipment and Inventory Management Tables
-- This file adds tables for equipment, inventory, quality control, and sample management

-- Lab Equipment Table
CREATE TABLE `lab_equipment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipment_name` varchar(200) NOT NULL,
  `equipment_code` varchar(50) NOT NULL UNIQUE,
  `category` varchar(100) NOT NULL,
  `manufacturer` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `warranty_expiry` date DEFAULT NULL,
  `status` enum('operational','maintenance','out_of_order','retired') DEFAULT 'operational',
  `last_maintenance` date DEFAULT NULL,
  `next_maintenance` date DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `responsible_person` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Lab Inventory Table
CREATE TABLE `lab_inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `item_name` varchar(200) NOT NULL,
  `item_code` varchar(50) NOT NULL UNIQUE,
  `category` varchar(100) NOT NULL,
  `supplier` varchar(100) DEFAULT NULL,
  `current_stock` int DEFAULT 0,
  `minimum_stock` int DEFAULT 0,
  `maximum_stock` int DEFAULT 0,
  `unit` varchar(20) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `batch_number` varchar(50) DEFAULT NULL,
  `storage_location` varchar(100) DEFAULT NULL,
  `storage_conditions` varchar(200) DEFAULT NULL,
  `status` enum('available','low_stock','out_of_stock','expired') DEFAULT 'available',
  `last_restocked` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_category` (`category`),
  INDEX `idx_expiry` (`expiry_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Lab Quality Control Table
CREATE TABLE `lab_quality_control` (
  `id` int NOT NULL AUTO_INCREMENT,
  `test_name` varchar(200) NOT NULL,
  `test_date` date NOT NULL,
  `control_type` enum('internal','external','proficiency') DEFAULT 'internal',
  `control_level` enum('low','normal','high') DEFAULT 'normal',
  `expected_value` varchar(100) DEFAULT NULL,
  `obtained_value` varchar(100) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `acceptable_range` varchar(100) DEFAULT NULL,
  `status` enum('passed','failed','investigation') DEFAULT 'passed',
  `technician_id` int DEFAULT NULL,
  `equipment_id` int DEFAULT NULL,
  `batch_number` varchar(50) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `corrective_action` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_test_date` (`test_date`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`equipment_id`) REFERENCES `lab_equipment`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`technician_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Lab Samples Table
CREATE TABLE `lab_samples` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sample_id` varchar(50) NOT NULL UNIQUE,
  `patient_id` int NOT NULL,
  `test_order_id` int DEFAULT NULL,
  `sample_type` varchar(100) NOT NULL,
  `collection_date` datetime NOT NULL,
  `collected_by` int DEFAULT NULL,
  `collection_method` varchar(100) DEFAULT NULL,
  `volume` varchar(50) DEFAULT NULL,
  `storage_condition` varchar(100) DEFAULT NULL,
  `status` enum('collected','processing','completed','rejected') DEFAULT 'collected',
  `barcode` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `quality_assessment` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_sample_id` (`sample_id`),
  INDEX `idx_collection_date` (`collection_date`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`test_order_id`) REFERENCES `lab_test_orders`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`collected_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert Sample Data for Lab Equipment
INSERT INTO `lab_equipment` (`equipment_name`, `equipment_code`, `category`, `manufacturer`, `model`, `status`, `location`, `responsible_person`, `notes`) VALUES
('Microscope - Olympus CX23', 'MICRO001', 'Microscopy', 'Olympus', 'CX23', 'operational', 'Lab Station 1', 'Lab Technician 1', 'Primary microscope for routine examinations'),
('Centrifuge - Hettich EBA 20', 'CENT001', 'Sample Preparation', 'Hettich', 'EBA 20', 'operational', 'Central Bench', 'Lab Technician 2', 'Used for blood sample separation'),
('Blood Chemistry Analyzer', 'CHEM001', 'Chemistry', 'Mindray', 'BS-120', 'operational', 'Chemistry Section', 'Senior Technician', 'Automated chemistry analysis'),
('Hemoglobin Meter', 'HB001', 'Hematology', 'HemoCue', 'Hb 201+', 'operational', 'Hematology Station', 'Lab Technician 1', 'Point-of-care hemoglobin testing'),
('ESR Analyzer', 'ESR001', 'Hematology', 'Alifax', 'Test 1', 'maintenance', 'Hematology Station', 'Lab Technician 2', 'Currently under routine maintenance'),
('Rapid Test Reader', 'RDT001', 'Serology', 'Standard Diagnostics', 'SD Reader', 'operational', 'Serology Station', 'Lab Technician 1', 'For rapid diagnostic tests');

-- Insert Sample Data for Lab Inventory
INSERT INTO `lab_inventory` (`item_name`, `item_code`, `category`, `current_stock`, `minimum_stock`, `unit`, `status`, `storage_location`) VALUES
('Malaria RDT Kits', 'RDT_MAL', 'Rapid Tests', 45, 20, 'pieces', 'available', 'Refrigerator A'),
('EDTA Blood Collection Tubes', 'TUBE_EDTA', 'Sample Collection', 150, 50, 'pieces', 'available', 'Storage Cabinet B'),
('Microscope Slides', 'SLIDE_MICRO', 'Microscopy', 280, 100, 'pieces', 'available', 'Drawer 3'),
('Cover Slips', 'COVER_SLIP', 'Microscopy', 420, 150, 'pieces', 'available', 'Drawer 3'),
('Giemsa Stain', 'STAIN_GIEMSA', 'Staining', 8, 3, 'bottles', 'available', 'Chemical Cabinet'),
('Pregnancy Test Kits', 'UPT_KIT', 'Rapid Tests', 15, 25, 'pieces', 'low_stock', 'Refrigerator A'),
('Blood Sugar Test Strips', 'BS_STRIPS', 'Chemistry', 3, 10, 'boxes', 'low_stock', 'Storage Cabinet A'),
('Urine Collection Containers', 'URINE_CONT', 'Sample Collection', 65, 30, 'pieces', 'available', 'Storage Room'),
('Alcohol Swabs', 'SWAB_ALC', 'Sample Collection', 180, 50, 'pieces', 'available', 'First Aid Cabinet'),
('Disposable Gloves', 'GLOVE_DISP', 'PPE', 120, 40, 'pairs', 'available', 'PPE Cabinet');

-- Insert Sample Data for Quality Control
INSERT INTO `lab_quality_control` (`test_name`, `test_date`, `control_type`, `expected_value`, `obtained_value`, `unit`, `status`, `comments`) VALUES
('Blood Sugar Control', CURDATE(), 'internal', '100', '98', 'mg/dL', 'passed', 'Within acceptable range'),
('Hemoglobin Control', CURDATE(), 'internal', '12.5', '12.8', 'g/dL', 'passed', 'Slight variation but acceptable'),
('Malaria RDT Control', CURDATE(), 'internal', 'Negative', 'Negative', 'result', 'passed', 'Control test negative as expected'),
('ESR Control', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'internal', '10', '12', 'mm/hr', 'passed', 'Within acceptable limits'),
('Uric Acid Control', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'external', '5.5', '5.8', 'mg/dL', 'investigation', 'Slightly elevated, investigating reagent batch');

-- Insert Sample Data for Lab Samples
INSERT INTO `lab_samples` (`sample_id`, `patient_id`, `sample_type`, `collection_date`, `status`, `barcode`, `location`) VALUES
('SAM240001', 27, 'Blood - EDTA', NOW(), 'processing', 'BAR240001', 'Hematology Section'),
('SAM240002', 27, 'Blood - Serum', NOW(), 'collected', 'BAR240002', 'Sample Storage'),
('SAM240003', 27, 'Finger Prick', NOW(), 'completed', 'BAR240003', 'Archived');

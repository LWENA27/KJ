-- Create missing lab_inventory table
CREATE TABLE `lab_inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `item_name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `item_code` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `category` enum('reagent','consumable','supply','equipment') COLLATE utf8mb4_general_ci DEFAULT 'consumable',
  `unit` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `current_stock` int DEFAULT '0',
  `minimum_stock` int DEFAULT '10',
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `supplier` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `location` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_code` (`item_code`),
  KEY `idx_inventory_category` (`category`),
  KEY `idx_inventory_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create lab_test_items table to link tests with required inventory
CREATE TABLE `lab_test_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `test_id` int NOT NULL,
  `item_id` int NOT NULL,
  `quantity_required` decimal(8,2) NOT NULL DEFAULT '1.00',
  `is_mandatory` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_test_item` (`test_id`,`item_id`),
  KEY `test_id` (`test_id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `lab_test_items_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `lab_tests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lab_test_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `lab_inventory` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create lab_equipment table as recommended
CREATE TABLE `lab_equipment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipment_name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `equipment_code` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `model` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `serial_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `manufacturer` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `warranty_expiry` date DEFAULT NULL,
  `last_calibration` date DEFAULT NULL,
  `next_calibration` date DEFAULT NULL,
  `calibration_interval_months` int DEFAULT '12',
  `status` enum('operational','maintenance','out_of_service','calibration_due') COLLATE utf8mb4_general_ci DEFAULT 'operational',
  `location` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `managed_by` int DEFAULT NULL COMMENT 'Lab tech responsible',
  `notes` text COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `equipment_code` (`equipment_code`),
  KEY `idx_equipment_status` (`status`),
  KEY `idx_equipment_managed_by` (`managed_by`),
  KEY `idx_equipment_active` (`is_active`),
  CONSTRAINT `lab_equipment_ibfk_1` FOREIGN KEY (`managed_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert some sample inventory items
INSERT INTO `lab_inventory` (`item_name`, `item_code`, `category`, `unit`, `current_stock`, `minimum_stock`, `unit_cost`, `supplier`) VALUES
('Blood Sugar Reagent Strips', 'BSR-001', 'reagent', 'strips', 500, 100, 25.00, 'MedLab Supplies'),
('Hemoglobin Reagent', 'HGB-001', 'reagent', 'bottles', 50, 10, 150.00, 'LabChem Corp'),
('Cholesterol Test Kit', 'CHOL-001', 'reagent', 'kits', 30, 5, 200.00, 'BioTest Labs'),
('Protein Reagent', 'PROT-001', 'reagent', 'bottles', 40, 8, 120.00, 'MedLab Supplies'),
('Gloves (Nitrile)', 'GLOVE-001', 'supply', 'pairs', 1000, 200, 5.00, 'MediSupplies'),
('Test Tubes (10ml)', 'TUBE-001', 'consumable', 'pieces', 500, 100, 2.00, 'LabWare Inc'),
('Microscope Slides', 'SLIDE-001', 'consumable', 'pieces', 1000, 200, 1.50, 'LabWare Inc'),
('Blood Collection Tubes', 'BCT-001', 'consumable', 'pieces', 300, 50, 8.00, 'MediSupplies');

-- Insert some sample equipment
INSERT INTO `lab_equipment` (`equipment_name`, `equipment_code`, `model`, `serial_number`, `manufacturer`, `purchase_date`, `status`, `location`) VALUES
('Microscope', 'MICRO-001', 'Olympus CX23', 'OLY2024001', 'Olympus', '2024-01-15', 'operational', 'Lab Room 1'),
('Chemistry Analyzer', 'CHEM-001', 'Mindray BS-200', 'MDR2024001', 'Mindray', '2024-02-20', 'operational', 'Lab Room 2'),
('Hematology Analyzer', 'HEMA-001', 'Sysmex XP-300', 'SYS2024001', 'Sysmex', '2024-03-10', 'maintenance', 'Lab Room 1'),
('Centrifuge', 'CENT-001', 'Eppendorf 5810R', 'EPP2024001', 'Eppendorf', '2024-01-25', 'operational', 'Lab Room 2'),
('Incubator', 'INCUB-001', 'Thermo Fisher 3110', 'THM2024001', 'Thermo Fisher', '2024-04-05', 'operational', 'Lab Room 3'),
('Autoclave', 'AUTO-001', 'Tuttnauer 2540M', 'TUT2024001', 'Tuttnauer', '2024-05-12', 'calibration_due', 'Sterilization Room');

-- Link some tests with required inventory items
INSERT INTO `lab_test_items` (`test_id`, `item_id`, `quantity_required`, `is_mandatory`) VALUES
(2, 1, 1.00, 1), -- Blood Sugar test requires reagent strips
(3, 1, 1.00, 1), -- Fasting Blood Sugar test requires reagent strips
(7, 2, 2.00, 1), -- LFT requires hemoglobin reagent
(7, 4, 1.00, 1), -- LFT requires protein reagent
(8, 3, 1.00, 1), -- KFT requires cholesterol test kit
(8, 4, 1.00, 1); -- KFT requires protein reagent
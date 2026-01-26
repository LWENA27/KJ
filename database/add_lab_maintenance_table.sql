-- Create lab_maintenance table for equipment maintenance scheduling
-- This table was created on 2026-01-18 to support the equipment management page

CREATE TABLE IF NOT EXISTS `lab_maintenance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipment_id` int NOT NULL,
  `maintenance_type` enum('preventive','corrective','calibration','inspection') DEFAULT 'preventive',
  `scheduled_date` date NOT NULL,
  `completion_date` date DEFAULT NULL,
  `status` enum('pending','in_progress','completed','overdue') DEFAULT 'pending',
  `description` text,
  `technician_id` int DEFAULT NULL,
  `notes` text,
  `cost` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `equipment_id` (`equipment_id`),
  KEY `technician_id` (`technician_id`),
  CONSTRAINT `lab_maintenance_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `lab_equipment` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lab_maintenance_ibfk_2` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample maintenance schedule records
INSERT INTO `lab_maintenance` (`equipment_id`, `maintenance_type`, `scheduled_date`, `status`, `description`, `notes`) VALUES
(1, 'preventive', DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'pending', 'Routine maintenance and cleaning', 'Regular preventive maintenance for microscope'),
(2, 'calibration', DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'pending', 'Quarterly calibration check', 'Chemistry analyzer needs quarterly calibration'),
(3, 'corrective', CURDATE(), 'in_progress', 'Repair hematology analyzer', 'Equipment is currently under maintenance'),
(4, 'preventive', DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'pending', 'Centrifuge inspection and maintenance', 'Quarterly preventive maintenance due'),
(5, 'calibration', DATE_ADD(CURDATE(), INTERVAL 21 DAY), 'pending', 'Temperature calibration', 'Incubator needs temperature recalibration'),
(6, 'preventive', CURDATE(), 'overdue', 'Autoclave inspection', 'Overdue for annual safety inspection');

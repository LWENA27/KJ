-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 13, 2025 at 07:01 AM
-- Server version: 8.0.43-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zahanati`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 27, 3, '2025-09-11', '09:00:00', 'scheduled', 'Regular checkup', '2025-09-11 18:12:27', '2025-09-11 18:12:27'),
(2, 27, 3, '2025-09-11', '10:30:00', 'scheduled', 'Follow-up visit', '2025-09-11 18:12:27', '2025-09-11 18:12:27');

-- --------------------------------------------------------

--
-- Table structure for table `consultations`
--

CREATE TABLE `consultations` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `visit_date` date DEFAULT (curdate()),
  `registration_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `patient_age` int DEFAULT NULL,
  `consultation_type` enum('new_patient','follow_up','emergency') COLLATE utf8mb4_general_ci DEFAULT 'new_patient',
  `doctor_id` int NOT NULL,
  `appointment_date` datetime NOT NULL,
  `symptoms` text COLLATE utf8mb4_general_ci,
  `diagnosis` text COLLATE utf8mb4_general_ci,
  `treatment` text COLLATE utf8mb4_general_ci,
  `notes` text COLLATE utf8mb4_general_ci,
  `main_complaint` text COLLATE utf8mb4_general_ci,
  `on_examination` text COLLATE utf8mb4_general_ci,
  `preliminary_diagnosis` text COLLATE utf8mb4_general_ci,
  `final_diagnosis` text COLLATE utf8mb4_general_ci,
  `lab_investigation` text COLLATE utf8mb4_general_ci,
  `treatment_plan` text COLLATE utf8mb4_general_ci,
  `blood_pressure` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `weight` decimal(5,1) DEFAULT NULL,
  `height` decimal(5,1) DEFAULT NULL,
  `prescribed_tests` text COLLATE utf8mb4_general_ci,
  `prescription` text COLLATE utf8mb4_general_ci,
  `status` enum('scheduled','in_progress','completed','cancelled','pending_lab_results','follow_up_required') COLLATE utf8mb4_general_ci DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `consultation_fee` decimal(10,2) DEFAULT '50000.00' COMMENT 'Consultation fee in UGX',
  `consultation_notes` text COLLATE utf8mb4_general_ci COMMENT 'Additional consultation notes',
  `follow_up_required` tinyint(1) DEFAULT '0' COMMENT 'Whether follow-up is required',
  `follow_up_date` date DEFAULT NULL COMMENT 'Scheduled follow-up date',
  `discharge_status` enum('pending','discharged','transferred','admitted') COLLATE utf8mb4_general_ci DEFAULT 'pending' COMMENT 'Patient discharge status',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT 'When consultation was completed',
  `lab_reviewed_at` timestamp NULL DEFAULT NULL COMMENT 'When lab results were reviewed',
  `lab_review_notes` text COLLATE utf8mb4_general_ci COMMENT 'Lab results review notes',
  `has_critical_results` tinyint(1) DEFAULT '0' COMMENT 'Whether consultation has critical lab results requiring immediate attention'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consultations`
--

INSERT INTO `consultations` (`id`, `patient_id`, `visit_date`, `registration_number`, `patient_age`, `consultation_type`, `doctor_id`, `appointment_date`, `symptoms`, `diagnosis`, `treatment`, `notes`, `main_complaint`, `on_examination`, `preliminary_diagnosis`, `final_diagnosis`, `lab_investigation`, `treatment_plan`, `blood_pressure`, `temperature`, `weight`, `height`, `prescribed_tests`, `prescription`, `status`, `created_at`, `updated_at`, `consultation_fee`, `consultation_notes`, `follow_up_required`, `follow_up_date`, `discharge_status`, `completed_at`, `lab_reviewed_at`, `lab_review_notes`, `has_critical_results`) VALUES
(11, 27, '2025-09-11', NULL, NULL, 'new_patient', 3, '2025-09-11 23:47:44', 'Fever, fatigue, and headache for 3 days', 'Suspected viral infection - require lab confirmation', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending_lab_results', '2025-09-11 20:47:44', '2025-09-11 20:47:44', 50000.00, NULL, 0, NULL, 'pending', NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `lab_results`
--

CREATE TABLE `lab_results` (
  `id` int NOT NULL,
  `consultation_id` int NOT NULL,
  `test_id` int NOT NULL,
  `technician_id` int NOT NULL,
  `result_value` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `result_text` text COLLATE utf8mb4_general_ci,
  `status` enum('pending','completed','reviewed') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `sample_date` datetime DEFAULT NULL,
  `result_date` datetime DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lab_order_id` int DEFAULT NULL COMMENT 'Reference to lab_test_orders table',
  `patient_id` int DEFAULT NULL COMMENT 'Direct reference to patient',
  `result_unit` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Unit of measurement',
  `interpretation` text COLLATE utf8mb4_general_ci COMMENT 'Clinical interpretation of results',
  `technician_notes` text COLLATE utf8mb4_general_ci COMMENT 'Technician notes and observations',
  `is_normal` tinyint(1) DEFAULT '0' COMMENT 'Whether result is within normal range',
  `is_critical` tinyint(1) DEFAULT '0' COMMENT 'Whether result requires immediate attention',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT 'When test was completed',
  `reviewed_by` int DEFAULT NULL COMMENT 'Doctor who reviewed the result',
  `reviewed_at` timestamp NULL DEFAULT NULL COMMENT 'When result was reviewed by doctor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_tests`
--

CREATE TABLE `lab_tests` (
  `id` int NOT NULL,
  `test_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `test_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `normal_range` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_tests`
--

INSERT INTO `lab_tests` (`id`, `test_name`, `test_code`, `category_id`, `price`, `normal_range`, `unit`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'mRDT (Malaria)', 'MRDT', 1, 5000.00, 'Negative', 'result', NULL, 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56'),
(2, 'Blood Slide Smear', 'BSS', 1, 3000.00, 'No parasites seen', 'result', NULL, 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56'),
(3, 'Urine Analysis', 'URINE', 1, 2000.00, 'Normal', 'result', NULL, 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56'),
(4, 'Stool Analysis', 'STOOL', 1, 2500.00, 'Normal', 'result', NULL, 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56'),
(5, 'Hemoglobin', 'HB', 2, 3000.00, '12-16 g/dL', 'g/dL', NULL, 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56'),
(6, 'ESR', 'ESR', 2, 2500.00, '0-20 mm/hr', 'mm/hr', NULL, 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56'),
(7, 'Full Blood Picture', 'FBP', 2, 8000.00, 'Normal', 'result', NULL, 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56'),
(8, 'H.Pylori', 'HPYLORI', 3, 15000.00, 'Negative', 'result', NULL, 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56'),
(9, 'RPR/Syphilis', 'RPR', 3, 5000.00, 'Non-reactive', 'result', NULL, 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56'),
(10, 'UPT (Pregnancy)', 'UPT', 3, 3000.00, 'Negative', 'result', NULL, 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56'),
(11, 'Salmonella Typhi', 'SALMONELLA', 3, 8000.00, 'Negative', 'result', NULL, 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56'),
(12, 'Blood Sugar (Random)', 'BS_RANDOM', 4, 3000.00, '70-140 mg/dL', 'mg/dL', NULL, 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56'),
(13, 'Blood Sugar (Fasting)', 'BS_FASTING', 4, 3000.00, '70-100 mg/dL', 'mg/dL', NULL, 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56'),
(14, 'Uric Acid', 'URIC_ACID', 4, 4000.00, '3.5-7.2 mg/dL', 'mg/dL', NULL, 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56'),
(15, 'Blood Group', 'BGROUP', 5, 5000.00, 'A/B/AB/O', 'group', NULL, 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56'),
(16, 'Rhesus Factor', 'RHESUS', 5, 3000.00, 'Positive/Negative', 'result', NULL, 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56');

-- --------------------------------------------------------

--
-- Table structure for table `lab_test_categories`
--

CREATE TABLE `lab_test_categories` (
  `id` int NOT NULL,
  `category_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_test_categories`
--

INSERT INTO `lab_test_categories` (`id`, `category_name`, `category_code`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Parasitology', 'PARA', 'Malaria, stool analysis, urine tests', 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56'),
(2, 'Hematology', 'HEMA', 'Blood count, hemoglobin, ESR', 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56'),
(3, 'Serology', 'SERO', 'H.Pylori, RPR, UPT, Salmonella', 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56'),
(4, 'Clinical Chemistry', 'CHEM', 'Blood sugar, uric acid', 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56'),
(5, 'Blood Transfusion', 'BTRANS', 'Blood group, Rhesus factor', 1, '2025-09-11 19:57:56', '2025-09-11 19:57:56');

-- --------------------------------------------------------

--
-- Table structure for table `lab_test_orders`
--

CREATE TABLE `lab_test_orders` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `tests_ordered` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `test_categories` json DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','partial') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `amount_paid` decimal(10,2) DEFAULT '0.00',
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('pending','in_progress','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `priority` enum('normal','urgent','stat') COLLATE utf8mb4_unicode_ci DEFAULT 'normal',
  `consultation_id` int DEFAULT NULL COMMENT 'Reference to consultation',
  `test_id` int DEFAULT NULL COMMENT 'Reference to specific lab test',
  `technician_id` int DEFAULT NULL COMMENT 'Assigned lab technician',
  `sample_collected_at` timestamp NULL DEFAULT NULL COMMENT 'When sample was collected',
  `expected_completion` timestamp NULL DEFAULT NULL COMMENT 'Expected completion time',
  `instructions` text COLLATE utf8mb4_unicode_ci COMMENT 'Special instructions for the test'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_test_orders`
--

INSERT INTO `lab_test_orders` (`id`, `patient_id`, `doctor_id`, `tests_ordered`, `test_categories`, `total_amount`, `payment_status`, `amount_paid`, `payment_method`, `payment_date`, `notes`, `created_at`, `updated_at`, `status`, `priority`, `consultation_id`, `test_id`, `technician_id`, `sample_collected_at`, `expected_completion`, `instructions`) VALUES
(2, 27, 3, 'Complete Blood Count (CBC)', NULL, 25.00, 'paid', 0.00, NULL, NULL, NULL, '2025-09-11 20:47:44', '2025-09-11 20:47:44', 'completed', 'normal', 11, 1, NULL, NULL, '2025-09-11 22:47:44', 'Fasting not required. Collect 3ml EDTA blood sample.'),
(3, 27, 3, 'Malaria Test (Rapid)', NULL, 15.00, 'paid', 0.00, NULL, NULL, NULL, '2025-09-11 20:47:44', '2025-09-11 20:47:44', 'pending', 'urgent', 11, 2, NULL, NULL, '2025-09-11 22:47:44', 'Urgent test - process immediately. Finger prick sample acceptable.'),
(4, 27, 3, 'Blood Sugar (Random)', NULL, 10.00, 'paid', 0.00, NULL, NULL, NULL, '2025-09-11 20:47:44', '2025-09-11 20:47:44', 'pending', 'normal', 11, 3, NULL, NULL, '2025-09-11 22:47:44', 'Random blood sugar. Record time of last meal.');

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `generic_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `stock_quantity` int NOT NULL DEFAULT '0',
  `unit_price` decimal(10,2) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `supplier` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`id`, `name`, `generic_name`, `description`, `stock_quantity`, `unit_price`, `expiry_date`, `supplier`, `created_at`, `updated_at`) VALUES
(1, 'Paracetamol', 'Acetaminophen', 'Pain relief medication', 100, 5.50, '2026-12-31', NULL, '2025-08-31 14:09:11', '2025-08-31 14:09:11'),
(2, 'Amoxicillin', 'Amoxicillin', 'Antibiotic', 50, 12.00, '2026-06-30', NULL, '2025-08-31 14:09:11', '2025-08-31 14:09:11'),
(3, 'Ibuprofen', 'Ibuprofen', 'Anti-inflammatory', 75, 8.25, '2026-10-15', NULL, '2025-08-31 14:09:11', '2025-08-31 14:09:11'),
(4, 'Artemether-Lumefantrine', 'Artemether/Lumefantrine', 'Anti-malarial combination therapy', 120, 15.00, '2026-08-30', NULL, '2025-09-03 02:02:24', '2025-09-03 02:02:24'),
(5, 'Ciprofloxacin', 'Ciprofloxacin', 'Broad-spectrum antibiotic', 80, 18.50, '2026-07-15', NULL, '2025-09-03 02:02:24', '2025-09-03 02:02:24'),
(6, 'Metformin', 'Metformin HCl', 'Diabetes medication', 200, 6.75, '2026-12-20', NULL, '2025-09-03 02:02:24', '2025-09-03 02:02:24'),
(7, 'Lisinopril', 'Lisinopril', 'ACE inhibitor for hypertension', 150, 12.25, '2026-09-10', NULL, '2025-09-03 02:02:24', '2025-09-03 02:02:24'),
(8, 'Omeprazole', 'Omeprazole', 'Proton pump inhibitor', 90, 14.00, '2026-11-05', NULL, '2025-09-03 02:02:24', '2025-09-03 02:02:24'),
(9, 'Aspirin', 'Acetylsalicylic Acid', 'Pain reliever and blood thinner', 500, 3.50, '2027-01-15', NULL, '2025-09-03 02:02:24', '2025-09-03 02:02:24'),
(10, 'Multivitamin', 'Multivitamin Complex', 'Daily vitamin supplement', 300, 8.00, '2027-03-20', NULL, '2025-09-03 02:02:24', '2025-09-03 02:02:24'),
(11, 'Oral Rehydration Salts', 'ORS', 'Dehydration treatment', 250, 2.00, '2028-01-01', NULL, '2025-09-03 02:02:25', '2025-09-03 02:02:25'),
(12, 'Albendazole', 'Albendazole', 'Anti-parasitic medication', 100, 5.25, '2026-10-30', NULL, '2025-09-03 02:02:25', '2025-09-03 02:02:25'),
(13, 'Cotrimoxazole', 'Sulfamethoxazole/Trimethoprim', 'Antibiotic combination', 160, 9.50, '2026-08-15', NULL, '2025-09-03 02:02:25', '2025-09-03 02:02:25'),
(14, 'Dexamethasone', 'Dexamethasone', 'Corticosteroid anti-inflammatory', 60, 22.00, '2026-06-25', NULL, '2025-09-03 02:02:25', '2025-09-03 02:02:25'),
(15, 'Folic Acid', 'Folic Acid', 'Vitamin B9 supplement', 180, 4.50, '2027-02-10', NULL, '2025-09-03 02:02:25', '2025-09-03 02:02:25'),
(16, 'Paracetamol 500mg', 'Paracetamol', 'Pain relief and fever reducer', 1000, 500.00, NULL, NULL, '2025-09-11 19:59:55', '2025-09-11 19:59:55'),
(17, 'Amoxicillin 250mg', 'Amoxicillin', 'Antibiotic for bacterial infections', 500, 1000.00, NULL, NULL, '2025-09-11 19:59:55', '2025-09-11 19:59:55'),
(18, 'ORS Sachets', 'Oral Rehydration Salts', 'Electrolyte replacement therapy', 200, 200.00, NULL, NULL, '2025-09-11 19:59:55', '2025-09-11 19:59:55'),
(19, 'Cotrimoxazole', 'Sulfamethoxazole/Trimethoprim', 'Antibiotic combination', 300, 800.00, NULL, NULL, '2025-09-11 19:59:55', '2025-09-11 19:59:55'),
(20, 'Ibuprofen 400mg', 'Ibuprofen', 'Anti-inflammatory pain reliever', 400, 600.00, NULL, NULL, '2025-09-11 19:59:55', '2025-09-11 19:59:55'),
(21, 'Multivitamins', 'Vitamin Complex', 'Daily vitamin supplement', 250, 1200.00, NULL, NULL, '2025-09-11 19:59:55', '2025-09-11 19:59:55'),
(22, 'Iron Tablets', 'Ferrous Sulfate', 'Iron deficiency supplement', 500, 300.00, NULL, NULL, '2025-09-11 19:59:55', '2025-09-11 19:59:55'),
(23, 'Aspirin 75mg', 'Acetylsalicylic Acid', 'Low-dose antiplatelet therapy', 300, 400.00, NULL, NULL, '2025-09-11 19:59:55', '2025-09-11 19:59:55');

-- --------------------------------------------------------

--
-- Table structure for table `medicine_allocations`
--

CREATE TABLE `medicine_allocations` (
  `id` int NOT NULL,
  `consultation_id` int NOT NULL,
  `medicine_id` int NOT NULL,
  `quantity` int NOT NULL,
  `dosage` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `instructions` text COLLATE utf8mb4_general_ci,
  `allocated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `allocated_by` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicine_prescriptions`
--

CREATE TABLE `medicine_prescriptions` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `prescription_data` json NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','partial') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `amount_paid` decimal(10,2) DEFAULT '0.00',
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `dispensed_amount` decimal(10,2) DEFAULT '0.00',
  `dispensed_by` int DEFAULT NULL,
  `dispensed_at` datetime DEFAULT NULL,
  `is_fully_dispensed` tinyint(1) DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int NOT NULL,
  `registration_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `occupation` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `visit_type` enum('consultation','lab_test','medicine_pickup') COLLATE utf8mb4_general_ci DEFAULT 'consultation',
  `consultation_fee` decimal(10,2) DEFAULT NULL,
  `payment_method` enum('cash','card','mobile_money','insurance') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_status` enum('pending','paid','partial') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `consultation_registration_paid` tinyint(1) DEFAULT '0',
  `lab_tests_paid` tinyint(1) DEFAULT '0',
  `medicine_dispensed` tinyint(1) DEFAULT '0',
  `final_payment_collected` tinyint(1) DEFAULT '0',
  `current_step` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'consultation_registration',
  `medicine_prescribed` tinyint(1) DEFAULT '0',
  `emergency_contact_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `emergency_contact_phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `blood_pressure` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pulse_rate` int DEFAULT NULL,
  `body_weight` decimal(5,1) DEFAULT NULL,
  `height` decimal(5,1) DEFAULT NULL,
  `medical_history` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `registration_number`, `first_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `occupation`, `visit_type`, `consultation_fee`, `payment_method`, `payment_status`, `consultation_registration_paid`, `lab_tests_paid`, `medicine_dispensed`, `final_payment_collected`, `current_step`, `medicine_prescribed`, `emergency_contact_name`, `emergency_contact_phone`, `temperature`, `blood_pressure`, `pulse_rate`, `body_weight`, `height`, `medical_history`, `created_at`, `updated_at`) VALUES
(27, 'KJ20250001', 'mariamu', 'saidi', NULL, NULL, '0683274343', '', 'majendo', 'mwasiasa', 'consultation', 50000.00, 'cash', 'paid', 1, 0, 0, 0, 'consultation', 0, '', '', 35.0, '120/270', 71, 40.0, 160.0, NULL, '2025-09-11 15:18:49', '2025-09-11 15:18:49'),
(28, 'KJ20250002', 'new', 'patient', NULL, NULL, '09564535', '', 'new', 'new', 'consultation', 50000.00, 'cash', 'paid', 1, 0, 0, 0, 'consultation', 0, '', '', 37.0, '120/57', 71, 60.0, 150.0, NULL, '2025-09-11 20:57:14', '2025-09-11 20:57:14');

--
-- Triggers `patients`
--
DELIMITER $$
CREATE TRIGGER `generate_registration_number` BEFORE INSERT ON `patients` FOR EACH ROW BEGIN
    DECLARE next_num INT DEFAULT 1$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `initialize_patient_workflow` AFTER INSERT ON `patients` FOR EACH ROW BEGIN 
    INSERT INTO `patient_workflow_status` 
    (`patient_id`, `workflow_step`, `status`, `started_at`) 
    VALUES 
    (NEW.id, 'registration', 'completed', NOW()),
    (NEW.id, 'consultation_payment', 'pending', NULL),
    (NEW.id, 'consultation', 'pending', NULL),
    (NEW.id, 'lab_tests', 'pending', NULL),
    (NEW.id, 'medicine_dispensing', 'pending', NULL)$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `patient_consultations`
--

CREATE TABLE `patient_consultations` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `registration_date` date NOT NULL DEFAULT (curdate()),
  `main_complaint` text COLLATE utf8mb4_general_ci COMMENT 'M/C - Main Complaint',
  `on_examination` text COLLATE utf8mb4_general_ci COMMENT 'O/E - On Examination findings',
  `preliminary_diagnosis` text COLLATE utf8mb4_general_ci COMMENT 'Preliminary Dx',
  `final_diagnosis` text COLLATE utf8mb4_general_ci COMMENT 'Final Dx',
  `lab_investigation` text COLLATE utf8mb4_general_ci COMMENT 'Lab Investigation required',
  `prescription` text COLLATE utf8mb4_general_ci COMMENT 'RX - Prescription',
  `total_amount` decimal(10,2) DEFAULT NULL COMMENT 'Total charges',
  `cash_paid` decimal(10,2) DEFAULT NULL COMMENT 'Cash amount paid',
  `balance_due` decimal(10,2) DEFAULT NULL COMMENT 'Debit/Balance due',
  `payment_method` enum('cash','card','mobile_money','insurance','mixed') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Detailed consultation records matching KJ Dispensary form structure';

-- --------------------------------------------------------

--
-- Table structure for table `patient_payments`
--

CREATE TABLE `patient_payments` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `consultation_id` int DEFAULT NULL,
  `payment_type` enum('consultation_fee','lab_test_fee','medicine_fee','other') COLLATE utf8mb4_general_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','mobile_money','insurance','credit') COLLATE utf8mb4_general_ci NOT NULL,
  `payment_status` enum('pending','paid','partial','refunded') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `payment_date` timestamp NULL DEFAULT NULL,
  `reference_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Transaction/Receipt reference',
  `collected_by` int DEFAULT NULL COMMENT 'Staff who collected payment',
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Detailed payment tracking for all patient services';

--
-- Dumping data for table `patient_payments`
--

INSERT INTO `patient_payments` (`id`, `patient_id`, `consultation_id`, `payment_type`, `amount`, `payment_method`, `payment_status`, `payment_date`, `reference_number`, `collected_by`, `notes`, `created_at`, `updated_at`) VALUES
(7, 27, NULL, 'consultation_fee', 50000.00, 'cash', 'paid', '2025-09-11 15:18:49', NULL, 7, 'Consultation fee payment at registration', '2025-09-11 15:18:49', '2025-09-11 15:18:49'),
(8, 28, NULL, 'consultation_fee', 50000.00, 'cash', 'paid', '2025-09-11 20:57:14', NULL, 8, 'Consultation fee payment at registration', '2025-09-11 20:57:14', '2025-09-11 20:57:14');

-- --------------------------------------------------------

--
-- Stand-in structure for view `patient_summary`
-- (See below for the actual view)
--
CREATE TABLE `patient_summary` (
`address` text
,`age` bigint
,`consultation_registration_paid` tinyint(1)
,`current_step` varchar(50)
,`date_of_birth` date
,`email` varchar(100)
,`emergency_contact_name` varchar(100)
,`emergency_contact_phone` varchar(20)
,`final_payment_collected` tinyint(1)
,`first_name` varchar(50)
,`full_name` varchar(101)
,`gender` enum('male','female','other')
,`id` int
,`lab_tests_paid` tinyint(1)
,`last_name` varchar(50)
,`medicine_dispensed` tinyint(1)
,`medicine_prescribed` tinyint(1)
,`occupation` varchar(100)
,`phone` varchar(20)
,`registration_date` timestamp
,`registration_number` varchar(20)
,`updated_at` timestamp
,`visit_type` enum('consultation','lab_test','medicine_pickup')
);

-- --------------------------------------------------------

--
-- Table structure for table `patient_vital_signs`
--

CREATE TABLE `patient_vital_signs` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `consultation_id` int DEFAULT NULL,
  `recorded_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `temperature` decimal(4,1) DEFAULT NULL COMMENT 'Temperature in Celsius',
  `blood_pressure_systolic` int DEFAULT NULL COMMENT 'Systolic BP',
  `blood_pressure_diastolic` int DEFAULT NULL COMMENT 'Diastolic BP',
  `blood_pressure_text` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'BP as text (e.g., 120/80)',
  `pulse_rate` int DEFAULT NULL COMMENT 'Pulse rate in bpm',
  `body_weight` decimal(5,1) DEFAULT NULL COMMENT 'Weight in kg',
  `height` decimal(5,1) DEFAULT NULL COMMENT 'Height in cm',
  `bmi` decimal(4,1) DEFAULT NULL COMMENT 'Calculated BMI',
  `recorded_by` int DEFAULT NULL COMMENT 'Staff member who recorded',
  `notes` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Vital signs tracking for each patient visit';

--
-- Dumping data for table `patient_vital_signs`
--

INSERT INTO `patient_vital_signs` (`id`, `patient_id`, `consultation_id`, `recorded_date`, `temperature`, `blood_pressure_systolic`, `blood_pressure_diastolic`, `blood_pressure_text`, `pulse_rate`, `body_weight`, `height`, `bmi`, `recorded_by`, `notes`) VALUES
(4, 27, NULL, '2025-09-11 15:18:49', 35.0, NULL, NULL, '120/270', 71, 40.0, 160.0, 15.6, 7, 'Initial registration vital signs'),
(5, 28, NULL, '2025-09-11 20:57:14', 37.0, NULL, NULL, '120/57', 71, 60.0, 150.0, 26.7, 8, 'Initial registration vital signs');

--
-- Triggers `patient_vital_signs`
--
DELIMITER $$
CREATE TRIGGER `calculate_bmi` BEFORE INSERT ON `patient_vital_signs` FOR EACH ROW BEGIN 
    IF NEW.height > 0 AND NEW.body_weight > 0 THEN
        SET NEW.bmi = NEW.body_weight / ((NEW.height / 100) * (NEW.height / 100))$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `patient_workflow_status`
--

CREATE TABLE `patient_workflow_status` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `workflow_step` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('pending','in_progress','completed','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `assigned_to` int DEFAULT NULL COMMENT 'Staff member assigned',
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Track patient workflow progress through different stages';

--
-- Dumping data for table `patient_workflow_status`
--

INSERT INTO `patient_workflow_status` (`id`, `patient_id`, `workflow_step`, `status`, `started_at`, `completed_at`, `assigned_to`, `notes`, `created_at`, `updated_at`) VALUES
(76, 27, 'registration', 'completed', '2025-09-11 15:18:49', NULL, NULL, NULL, '2025-09-11 15:18:49', '2025-09-11 15:18:49'),
(77, 27, 'consultation_payment', 'pending', NULL, NULL, NULL, NULL, '2025-09-11 15:18:49', '2025-09-11 15:18:49'),
(78, 27, 'consultation', 'pending', NULL, NULL, NULL, NULL, '2025-09-11 15:18:49', '2025-09-11 15:18:49'),
(79, 27, 'lab_tests', 'pending', NULL, NULL, NULL, NULL, '2025-09-11 15:18:49', '2025-09-11 15:18:49'),
(80, 27, 'medicine_dispensing', 'pending', NULL, NULL, NULL, NULL, '2025-09-11 15:18:49', '2025-09-11 15:18:49'),
(81, 28, 'registration', 'completed', '2025-09-11 20:57:14', NULL, NULL, NULL, '2025-09-11 20:57:14', '2025-09-11 20:57:14'),
(82, 28, 'consultation_payment', 'pending', NULL, NULL, NULL, NULL, '2025-09-11 20:57:14', '2025-09-11 20:57:14'),
(83, 28, 'consultation', 'pending', NULL, NULL, NULL, NULL, '2025-09-11 20:57:14', '2025-09-11 20:57:14'),
(84, 28, 'lab_tests', 'pending', NULL, NULL, NULL, NULL, '2025-09-11 20:57:14', '2025-09-11 20:57:14'),
(85, 28, 'medicine_dispensing', 'pending', NULL, NULL, NULL, NULL, '2025-09-11 20:57:14', '2025-09-11 20:57:14');

-- --------------------------------------------------------

--
-- Stand-in structure for view `patient_workflow_summary`
-- (See below for the actual view)
--
CREATE TABLE `patient_workflow_summary` (
`active_steps` bigint
,`completed_steps` bigint
,`current_step` varchar(50)
,`patient_id` int
,`patient_name` varchar(101)
,`pending_steps` bigint
,`registration_date` timestamp
,`registration_number` varchar(20)
);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `consultation_id` int DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','insurance','other') COLLATE utf8mb4_general_ci NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','completed','failed','refunded') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_general_ci,
  `created_by` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `step_payments`
--

CREATE TABLE `step_payments` (
  `id` int NOT NULL,
  `workflow_id` int NOT NULL,
  `step` enum('consultation_registration','lab_tests','results_review') COLLATE utf8mb4_general_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','insurance','mobile_money') COLLATE utf8mb4_general_ci NOT NULL,
  `transaction_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processed_by` int NOT NULL,
  `status` enum('pending','completed','failed','refunded') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `category` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `normal_range` text COLLATE utf8mb4_general_ci,
  `unit` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`id`, `name`, `description`, `category`, `price`, `normal_range`, `unit`, `created_at`, `updated_at`) VALUES
(1, 'Complete Blood Count', 'CBC test for blood analysis', 'Hematology', 25.00, '4.5-11.0', '10^9/L', '2025-08-31 14:09:11', '2025-08-31 14:09:11'),
(2, 'Blood Glucose', 'Fasting blood sugar test', 'Biochemistry', 15.00, '70-100', 'mg/dL', '2025-08-31 14:09:11', '2025-08-31 14:09:11'),
(3, 'Urine Analysis', 'Urinalysis test', 'Urinalysis', 10.00, 'Normal', 'N/A', '2025-08-31 14:09:11', '2025-08-31 14:09:11'),
(4, 'Malaria Rapid Test', 'Quick malaria detection test', 'Parasitology', 8.00, 'Negative', 'Result', '2025-09-03 02:02:23', '2025-09-03 02:02:23'),
(5, 'HIV Test', 'HIV antibody screening test', 'Serology', 20.00, 'Non-reactive', 'Result', '2025-09-03 02:02:23', '2025-09-03 02:02:23'),
(6, 'Hepatitis B Surface Antigen', 'HBsAg screening test', 'Serology', 15.00, 'Non-reactive', 'Result', '2025-09-03 02:02:24', '2025-09-03 02:02:24'),
(7, 'Liver Function Test', 'Complete liver enzyme panel', 'Biochemistry', 35.00, 'Normal', 'Units', '2025-09-03 02:02:24', '2025-09-03 02:02:24'),
(8, 'Kidney Function Test', 'Creatinine and urea analysis', 'Biochemistry', 25.00, 'Normal', 'mg/dL', '2025-09-03 02:02:24', '2025-09-03 02:02:24'),
(9, 'Lipid Profile', 'Complete cholesterol analysis', 'Biochemistry', 30.00, 'Normal', 'mg/dL', '2025-09-03 02:02:24', '2025-09-03 02:02:24'),
(10, 'Thyroid Function Test', 'TSH, T3, T4 analysis', 'Endocrinology', 45.00, 'Normal', 'mIU/L', '2025-09-03 02:02:24', '2025-09-03 02:02:24'),
(11, 'Stool Analysis', 'Comprehensive stool examination', 'Microbiology', 12.00, 'Normal', 'Result', '2025-09-03 02:02:24', '2025-09-03 02:02:24'),
(12, 'Pregnancy Test', 'HCG pregnancy detection', 'Serology', 5.00, 'Negative/Positive', 'Result', '2025-09-03 02:02:24', '2025-09-03 02:02:24'),
(13, 'ESR (Erythrocyte Sedimentation Rate)', 'Inflammation marker test', 'Hematology', 10.00, '0-20 mm/hr', 'mm/hr', '2025-09-03 02:02:24', '2025-09-03 02:02:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','receptionist','doctor','lab_technician') COLLATE utf8mb4_general_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `email`, `role`, `first_name`, `last_name`, `phone`, `created_at`, `updated_at`, `is_active`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@hospital.com', 'admin', 'System', 'Administrator', '1234567890', '2025-08-31 14:09:11', '2025-08-31 14:09:11', 1),
(3, 'doctor1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor@hospital.com', 'doctor', 'Dr. John', 'Doe', '1234567892', '2025-08-31 14:09:11', '2025-08-31 14:09:11', 1),
(7, 'mapokezi1', '$2y$10$yQm1Cb2DN7luUaAJAJUH5O4WF/ZfXqu3DRhYQOXS37nvSaR8M/8uy', 'mapokezi1@gmail.com', 'receptionist', 'mapokezi', 'mapokezi', '0683274343', '2025-09-11 12:31:27', '2025-09-11 12:31:27', 1),
(8, 'mapokezi', '$2y$10$MkYf4RAMK2JkaAj8y3wRyeiztm5k3krAe591lLrReZaPYQvX8h2mS', 'mapokezi@gmail.com', 'receptionist', 'mapokezi', 'mapokezi', '077595533', '2025-09-11 20:54:11', '2025-09-11 20:54:11', 1);

-- --------------------------------------------------------

--
-- Table structure for table `workflow_status`
--

CREATE TABLE `workflow_status` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `current_step` enum('consultation_registration','lab_tests','results_review','medicine_dispensing','completed') COLLATE utf8mb4_general_ci DEFAULT 'consultation_registration',
  `consultation_registration_paid` tinyint(1) DEFAULT '0',
  `registration_paid` tinyint(1) DEFAULT '0',
  `consultation_paid` tinyint(1) DEFAULT '0',
  `lab_tests_paid` tinyint(1) DEFAULT '0',
  `results_review_paid` tinyint(1) DEFAULT '0',
  `lab_tests_required` tinyint(1) DEFAULT '0',
  `medicine_prescribed` tinyint(1) DEFAULT '0',
  `medicine_dispensed` tinyint(1) DEFAULT '0',
  `medicine_dispensed_by` int DEFAULT NULL,
  `medicine_dispensed_at` timestamp NULL DEFAULT NULL,
  `final_payment_collected` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workflow_status`
--

INSERT INTO `workflow_status` (`id`, `patient_id`, `current_step`, `consultation_registration_paid`, `registration_paid`, `consultation_paid`, `lab_tests_paid`, `results_review_paid`, `lab_tests_required`, `medicine_prescribed`, `medicine_dispensed`, `medicine_dispensed_by`, `medicine_dispensed_at`, `final_payment_collected`, `created_at`, `updated_at`) VALUES
(12, 27, 'consultation_registration', 1, 1, 1, 0, 0, 0, 0, 0, NULL, NULL, 0, '2025-09-11 20:20:16', '2025-09-11 20:20:16');

-- --------------------------------------------------------

--
-- Structure for view `patient_summary`
--
DROP TABLE IF EXISTS `patient_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `patient_summary`  AS SELECT `p`.`id` AS `id`, `p`.`registration_number` AS `registration_number`, concat(`p`.`first_name`,' ',`p`.`last_name`) AS `full_name`, `p`.`first_name` AS `first_name`, `p`.`last_name` AS `last_name`, `p`.`date_of_birth` AS `date_of_birth`, timestampdiff(YEAR,`p`.`date_of_birth`,curdate()) AS `age`, `p`.`gender` AS `gender`, `p`.`phone` AS `phone`, `p`.`email` AS `email`, `p`.`address` AS `address`, `p`.`occupation` AS `occupation`, `p`.`emergency_contact_name` AS `emergency_contact_name`, `p`.`emergency_contact_phone` AS `emergency_contact_phone`, `p`.`visit_type` AS `visit_type`, `p`.`current_step` AS `current_step`, `p`.`consultation_registration_paid` AS `consultation_registration_paid`, `p`.`lab_tests_paid` AS `lab_tests_paid`, `p`.`medicine_dispensed` AS `medicine_dispensed`, `p`.`medicine_prescribed` AS `medicine_prescribed`, `p`.`final_payment_collected` AS `final_payment_collected`, `p`.`created_at` AS `registration_date`, `p`.`updated_at` AS `updated_at` FROM `patients` AS `p` ;

-- --------------------------------------------------------

--
-- Structure for view `patient_workflow_summary`
--
DROP TABLE IF EXISTS `patient_workflow_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `patient_workflow_summary`  AS SELECT `p`.`id` AS `patient_id`, `p`.`registration_number` AS `registration_number`, concat(`p`.`first_name`,' ',`p`.`last_name`) AS `patient_name`, `p`.`current_step` AS `current_step`, count((case when (`pws`.`status` = 'completed') then 1 end)) AS `completed_steps`, count((case when (`pws`.`status` = 'pending') then 1 end)) AS `pending_steps`, count((case when (`pws`.`status` = 'in_progress') then 1 end)) AS `active_steps`, `p`.`created_at` AS `registration_date` FROM (`patients` `p` left join `patient_workflow_status` `pws` on((`p`.`id` = `pws`.`patient_id`))) GROUP BY `p`.`id`, `p`.`registration_number`, `patient_name`, `p`.`current_step`, `p`.`created_at` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `consultations`
--
ALTER TABLE `consultations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_consultations_patient_id` (`patient_id`),
  ADD KEY `idx_consultations_doctor_id` (`doctor_id`),
  ADD KEY `idx_consultations_date` (`appointment_date`),
  ADD KEY `idx_consultations_visit_date` (`visit_date`),
  ADD KEY `idx_consultations_registration_number` (`registration_number`),
  ADD KEY `idx_consultations_patient_doctor` (`patient_id`,`doctor_id`),
  ADD KEY `idx_consultations_status` (`status`),
  ADD KEY `idx_consultations_appointment_date` (`appointment_date`),
  ADD KEY `idx_consultations_follow_up` (`follow_up_required`,`follow_up_date`),
  ADD KEY `idx_consultations_critical` (`has_critical_results`,`status`);

--
-- Indexes for table `lab_results`
--
ALTER TABLE `lab_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `technician_id` (`technician_id`),
  ADD KEY `idx_lab_results_consultation_id` (`consultation_id`),
  ADD KEY `idx_lab_results_test_id` (`test_id`),
  ADD KEY `fk_lab_results_reviewed_by` (`reviewed_by`),
  ADD KEY `idx_lab_results_order` (`lab_order_id`),
  ADD KEY `idx_lab_results_patient` (`patient_id`),
  ADD KEY `idx_lab_results_status` (`status`),
  ADD KEY `idx_lab_results_completed` (`completed_at`),
  ADD KEY `idx_lab_results_critical` (`is_critical`,`status`);

--
-- Indexes for table `lab_tests`
--
ALTER TABLE `lab_tests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `test_code` (`test_code`),
  ADD KEY `idx_test_code` (`test_code`),
  ADD KEY `idx_category_id` (`category_id`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `lab_test_categories`
--
ALTER TABLE `lab_test_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_code` (`category_code`),
  ADD KEY `idx_category_code` (`category_code`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `lab_test_orders`
--
ALTER TABLE `lab_test_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patient_id` (`patient_id`),
  ADD KEY `idx_doctor_id` (`doctor_id`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `fk_lab_orders_consultation` (`consultation_id`),
  ADD KEY `fk_lab_orders_test` (`test_id`),
  ADD KEY `idx_lab_orders_status` (`status`,`payment_status`),
  ADD KEY `idx_lab_orders_patient` (`patient_id`,`status`),
  ADD KEY `idx_lab_orders_technician` (`technician_id`,`status`),
  ADD KEY `idx_lab_orders_priority` (`priority`,`status`),
  ADD KEY `idx_lab_orders_dates` (`created_at`,`expected_completion`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medicine_allocations`
--
ALTER TABLE `medicine_allocations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medicine_id` (`medicine_id`),
  ADD KEY `allocated_by` (`allocated_by`),
  ADD KEY `idx_medicine_allocations_consultation_id` (`consultation_id`);

--
-- Indexes for table `medicine_prescriptions`
--
ALTER TABLE `medicine_prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dispensed_by` (`dispensed_by`),
  ADD KEY `idx_patient_id` (`patient_id`),
  ADD KEY `idx_doctor_id` (`doctor_id`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_dispensed` (`is_fully_dispensed`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `registration_number` (`registration_number`),
  ADD KEY `idx_patients_registration_number` (`registration_number`),
  ADD KEY `idx_patients_visit_type` (`visit_type`),
  ADD KEY `idx_patients_current_step` (`current_step`),
  ADD KEY `idx_patients_name` (`first_name`,`last_name`),
  ADD KEY `idx_patients_phone` (`phone`),
  ADD KEY `idx_patients_created_date` (`created_at`);

--
-- Indexes for table `patient_consultations`
--
ALTER TABLE `patient_consultations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patient_date` (`patient_id`,`registration_date`);

--
-- Indexes for table `patient_payments`
--
ALTER TABLE `patient_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consultation_id` (`consultation_id`),
  ADD KEY `idx_patient_payment_type` (`patient_id`,`payment_type`,`payment_status`);

--
-- Indexes for table `patient_vital_signs`
--
ALTER TABLE `patient_vital_signs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consultation_id` (`consultation_id`),
  ADD KEY `idx_patient_date` (`patient_id`,`recorded_date`);

--
-- Indexes for table `patient_workflow_status`
--
ALTER TABLE `patient_workflow_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patient_workflow` (`patient_id`,`workflow_step`,`status`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consultation_id` (`consultation_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_payments_patient_id` (`patient_id`),
  ADD KEY `idx_payments_date` (`payment_date`);

--
-- Indexes for table `step_payments`
--
ALTER TABLE `step_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workflow_id` (`workflow_id`),
  ADD KEY `processed_by` (`processed_by`);

--
-- Indexes for table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `workflow_status`
--
ALTER TABLE `workflow_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `medicine_dispensed_by` (`medicine_dispensed_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `consultations`
--
ALTER TABLE `consultations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `lab_results`
--
ALTER TABLE `lab_results`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `lab_tests`
--
ALTER TABLE `lab_tests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `lab_test_categories`
--
ALTER TABLE `lab_test_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `lab_test_orders`
--
ALTER TABLE `lab_test_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `medicine_allocations`
--
ALTER TABLE `medicine_allocations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `medicine_prescriptions`
--
ALTER TABLE `medicine_prescriptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `patient_consultations`
--
ALTER TABLE `patient_consultations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_payments`
--
ALTER TABLE `patient_payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `patient_vital_signs`
--
ALTER TABLE `patient_vital_signs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `patient_workflow_status`
--
ALTER TABLE `patient_workflow_status`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `step_payments`
--
ALTER TABLE `step_payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `workflow_status`
--
ALTER TABLE `workflow_status`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `consultations`
--
ALTER TABLE `consultations`
  ADD CONSTRAINT `consultations_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultations_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_results`
--
ALTER TABLE `lab_results`
  ADD CONSTRAINT `fk_lab_results_order` FOREIGN KEY (`lab_order_id`) REFERENCES `lab_test_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lab_results_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lab_results_reviewed_by` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lab_results_ibfk_1` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_results_ibfk_2` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_results_ibfk_3` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_tests`
--
ALTER TABLE `lab_tests`
  ADD CONSTRAINT `lab_tests_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `lab_test_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_test_orders`
--
ALTER TABLE `lab_test_orders`
  ADD CONSTRAINT `fk_lab_orders_consultation` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lab_orders_technician` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_lab_orders_test` FOREIGN KEY (`test_id`) REFERENCES `lab_tests` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `lab_test_orders_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_test_orders_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medicine_allocations`
--
ALTER TABLE `medicine_allocations`
  ADD CONSTRAINT `medicine_allocations_ibfk_1` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medicine_allocations_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medicine_allocations_ibfk_3` FOREIGN KEY (`allocated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medicine_prescriptions`
--
ALTER TABLE `medicine_prescriptions`
  ADD CONSTRAINT `medicine_prescriptions_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medicine_prescriptions_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medicine_prescriptions_ibfk_3` FOREIGN KEY (`dispensed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `patient_consultations`
--
ALTER TABLE `patient_consultations`
  ADD CONSTRAINT `patient_consultations_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `patient_payments`
--
ALTER TABLE `patient_payments`
  ADD CONSTRAINT `patient_payments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_payments_ibfk_2` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `patient_vital_signs`
--
ALTER TABLE `patient_vital_signs`
  ADD CONSTRAINT `patient_vital_signs_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_vital_signs_ibfk_2` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `patient_workflow_status`
--
ALTER TABLE `patient_workflow_status`
  ADD CONSTRAINT `patient_workflow_status_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `step_payments`
--
ALTER TABLE `step_payments`
  ADD CONSTRAINT `step_payments_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `workflow_status` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `step_payments_ibfk_2` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workflow_status`
--
ALTER TABLE `workflow_status`
  ADD CONSTRAINT `workflow_status_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `workflow_status_ibfk_2` FOREIGN KEY (`medicine_dispensed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `workflow_status_ibfk_3` FOREIGN KEY (`medicine_dispensed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

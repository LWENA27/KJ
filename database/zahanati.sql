-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 23, 2025 at 07:54 PM
-- Server version: 8.0.43-0ubuntu0.24.04.2
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
-- Stand-in structure for view `active_patient_queue`
-- (See below for the actual view)
--
CREATE TABLE `active_patient_queue` (
`age` bigint
,`blood_pressure_diastolic` int
,`blood_pressure_systolic` int
,`completed_lab_tests` bigint
,`consultation_id` int
,`consultation_status` enum('pending','in_progress','completed','cancelled')
,`doctor_name` varchar(101)
,`gender` enum('male','female','other')
,`partial_prescriptions` bigint
,`patient_id` int
,`patient_name` varchar(101)
,`pending_lab_tests` bigint
,`pending_prescriptions` bigint
,`phone` varchar(20)
,`pulse_rate` int
,`registration_number` varchar(20)
,`registration_paid` decimal(32,2)
,`registration_time` timestamp
,`temperature` decimal(4,1)
,`visit_date` date
,`visit_id` int
,`visit_type` enum('consultation','lab_only','minor_service')
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `common_diagnoses`
-- (See below for the actual view)
--
CREATE TABLE `common_diagnoses` (
`diagnosis` text
,`occurrence_count` bigint
,`unique_patients` bigint
);

-- --------------------------------------------------------

--
-- Table structure for table `consultations`
--

CREATE TABLE `consultations` (
  `id` int NOT NULL,
  `visit_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `consultation_number` int DEFAULT '1' COMMENT 'Multiple doctors can see patient same visit',
  `consultation_type` enum('new','follow_up','emergency','referral') COLLATE utf8mb4_general_ci DEFAULT 'new',
  `main_complaint` text COLLATE utf8mb4_general_ci,
  `history_of_present_illness` text COLLATE utf8mb4_general_ci,
  `on_examination` text COLLATE utf8mb4_general_ci,
  `diagnosis` text COLLATE utf8mb4_general_ci,
  `treatment_plan` text COLLATE utf8mb4_general_ci,
  `notes` text COLLATE utf8mb4_general_ci,
  `follow_up_required` tinyint(1) DEFAULT '0',
  `follow_up_date` date DEFAULT NULL,
  `follow_up_instructions` text COLLATE utf8mb4_general_ci,
  `referred_to` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Future: referral destination',
  `referral_reason` text COLLATE utf8mb4_general_ci COMMENT 'Future: why referred',
  `status` enum('pending','in_progress','completed','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `cancellation_reason` text COLLATE utf8mb4_general_ci,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consultations`
--

INSERT INTO `consultations` (`id`, `visit_id`, `patient_id`, `doctor_id`, `consultation_number`, `consultation_type`, `main_complaint`, `history_of_present_illness`, `on_examination`, `diagnosis`, `treatment_plan`, `notes`, `follow_up_required`, `follow_up_date`, `follow_up_instructions`, `referred_to`, `referral_reason`, `status`, `cancellation_reason`, `started_at`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3, 1, 'new', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'in_progress', NULL, '2025-10-11 05:15:14', NULL, '2025-10-11 03:50:52', '2025-10-11 05:15:14'),
(2, 2, 2, 3, 1, 'new', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'in_progress', NULL, '2025-10-11 06:09:42', NULL, '2025-10-11 06:04:10', '2025-10-11 06:09:42'),
(3, 3, 3, 1, 1, 'new', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, '2025-10-11 11:12:44', '2025-10-11 11:12:44'),
(4, 4, 4, 3, 1, 'new', 'kichwa', NULL, 'ubongo', '', '', NULL, 0, NULL, NULL, NULL, NULL, 'completed', NULL, '2025-10-17 08:04:05', '2025-10-17 08:04:05', '2025-10-17 08:02:10', '2025-10-17 08:04:05'),
(5, 5, 5, 1, 1, 'new', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, '2025-10-17 08:35:54', '2025-10-17 08:35:54'),
(6, 6, 6, 3, 1, 'new', 'kichwa', NULL, 'snfh', '', '', NULL, 0, NULL, NULL, NULL, NULL, 'completed', NULL, '2025-10-18 05:14:25', '2025-10-18 05:14:25', '2025-10-18 05:02:27', '2025-10-18 05:14:25'),
(7, 7, 1, 3, 1, '', 'null', NULL, 'null', 'null', 'nul', NULL, 0, NULL, NULL, NULL, NULL, 'completed', NULL, '2025-10-22 06:48:58', '2025-10-22 06:48:58', '2025-10-20 07:00:37', '2025-10-22 06:48:58'),
(9, 9, 6, 1, 1, '', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, '2025-10-20 07:12:12', '2025-10-20 07:12:12'),
(10, 10, 7, 1, 1, 'new', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, '2025-10-21 19:04:49', '2025-10-21 19:04:49'),
(11, 11, 8, 3, 1, 'new', 'kichwa', NULL, 'kichwa', 'hdc', 'hjsd', NULL, 0, NULL, NULL, NULL, NULL, 'completed', NULL, '2025-10-21 19:21:27', '2025-10-21 19:21:27', '2025-10-21 19:08:48', '2025-10-21 19:21:27'),
(12, 12, 9, 1, 1, 'new', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, '2025-10-21 19:18:47', '2025-10-21 19:18:47'),
(13, 13, 10, 3, 1, 'new', 'null', NULL, 'null', 'null', 'fhjgsf', NULL, 0, NULL, NULL, NULL, NULL, 'completed', NULL, '2025-10-22 06:52:41', '2025-10-22 06:52:41', '2025-10-22 06:51:49', '2025-10-22 06:52:41'),
(14, 14, 11, 1, 1, 'new', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, '2025-10-22 07:57:57', '2025-10-22 07:57:57'),
(15, 15, 12, 3, 1, 'new', 'null', NULL, 'null', 'null', 'null', NULL, 0, NULL, NULL, NULL, NULL, 'completed', NULL, '2025-10-22 08:20:21', '2025-10-22 08:20:21', '2025-10-22 08:17:18', '2025-10-22 08:20:21'),
(16, 16, 13, 3, 1, 'new', 'null', NULL, 'null', 'null', 'null', NULL, 0, NULL, NULL, NULL, NULL, 'completed', NULL, '2025-10-23 19:24:30', '2025-10-23 19:24:30', '2025-10-23 11:01:32', '2025-10-23 19:24:30');

-- --------------------------------------------------------

--
-- Stand-in structure for view `daily_revenue_summary`
-- (See below for the actual view)
--
CREATE TABLE `daily_revenue_summary` (
`collected_by_name` varchar(101)
,`payment_method` enum('cash','card','mobile_money','insurance')
,`payment_type` enum('registration','lab_test','medicine','minor_service')
,`revenue_date` date
,`total_amount` decimal(32,2)
,`transaction_count` bigint
);

-- --------------------------------------------------------

--
-- Table structure for table `lab_results`
--

CREATE TABLE `lab_results` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `test_id` int NOT NULL,
  `result_value` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `result_text` text COLLATE utf8mb4_general_ci,
  `result_unit` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_normal` tinyint(1) DEFAULT '1',
  `is_critical` tinyint(1) DEFAULT '0',
  `interpretation` text COLLATE utf8mb4_general_ci,
  `technician_id` int NOT NULL,
  `technician_notes` text COLLATE utf8mb4_general_ci,
  `completed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_by` int DEFAULT NULL COMMENT 'Doctor who reviewed',
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `review_notes` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_results`
--

INSERT INTO `lab_results` (`id`, `order_id`, `patient_id`, `test_id`, `result_value`, `result_text`, `result_unit`, `is_normal`, `is_critical`, `interpretation`, `technician_id`, `technician_notes`, `completed_at`, `reviewed_by`, `reviewed_at`, `review_notes`) VALUES
(1, 1, 4, 4, '1.0', 'Test completed successfully.', 'mg/dL', 1, 0, NULL, 4, NULL, '2025-10-17 05:05:00', NULL, NULL, NULL),
(2, 3, 8, 4, '1.0', 'Test completed successfully.', 'mg/dL', 1, 0, NULL, 6, NULL, '2025-10-21 17:13:00', NULL, NULL, NULL),
(3, 2, 6, 4, '1.0', 'Test completed successfully.', 'mg/dL', 1, 0, NULL, 6, NULL, '2025-10-22 03:32:00', NULL, NULL, NULL),
(4, 5, 10, 14, '1.5', 'condition good', 'mg/dL', 0, 0, NULL, 6, NULL, '2025-10-22 03:53:00', NULL, NULL, NULL),
(5, 6, 12, 3, '1.0', 'Test completed ', 'mg/dL', 1, 0, NULL, 6, NULL, '2025-10-22 05:21:00', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lab_tests`
--

CREATE TABLE `lab_tests` (
  `id` int NOT NULL,
  `test_name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `test_code` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `category_id` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `normal_range` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `unit` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `turn_around_time` int DEFAULT NULL COMMENT 'Expected time in minutes',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_tests`
--

INSERT INTO `lab_tests` (`id`, `test_name`, `test_code`, `category_id`, `price`, `normal_range`, `unit`, `description`, `turn_around_time`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Complete Blood Count', 'CBC', 1, 15000.00, 'RBC: 4.5-5.5, WBC: 4-11, Hb: 12-16', 'cells/mcL', 'Full blood count analysis', NULL, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(2, 'Blood Sugar (Random)', 'BS-R', 2, 5000.00, '70-140', 'mg/dL', 'Random blood glucose test', NULL, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(3, 'Blood Sugar (Fasting)', 'BS-F', 2, 5000.00, '70-100', 'mg/dL', 'Fasting blood glucose test', NULL, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(4, 'Malaria Test', 'MAL', 3, 8000.00, 'Negative', '', 'Malaria parasite detection', NULL, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(5, 'Urinalysis', 'URINE', 5, 6000.00, 'Normal', '', 'Complete urine examination', NULL, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(6, 'Stool Examination', 'STOOL', 3, 7000.00, 'Normal', '', 'Stool microscopy and culture', NULL, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(7, 'Liver Function Test', 'LFT', 2, 25000.00, 'ALT: 7-56, AST: 10-40', 'U/L', 'Complete liver function panel', NULL, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(8, 'Kidney Function Test', 'KFT', 2, 25000.00, 'Creatinine: 0.7-1.3', 'mg/dL', 'Renal function assessment', NULL, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(9, 'Pregnancy Test', 'PREG', 4, 5000.00, 'Positive/Negative', '', 'hCG detection in urine', NULL, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(10, 'HIV Test', 'HIV', 4, 10000.00, 'Non-reactive', '', 'HIV antibody screening', NULL, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(11, 'Hepatitis B Surface Antigen', 'HBsAg', 4, 15000.00, 'Negative', '', 'Hepatitis B screening', NULL, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(12, 'Widal Test', 'WIDAL', 4, 12000.00, 'Non-reactive', '', 'Typhoid fever antibody test', NULL, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(13, 'ESR', 'ESR', 1, 5000.00, 'Male: 0-15, Female: 0-20', 'mm/hr', 'Erythrocyte sedimentation rate', NULL, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(14, 'Blood Group & Rh', 'BG-RH', 1, 8000.00, 'A/B/AB/O, Rh+/-', '', 'Blood typing and Rh factor', NULL, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(15, 'X-Ray Chest', 'XRAY-C', 1, 30000.00, 'Normal', '', 'Chest radiography', NULL, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35');

-- --------------------------------------------------------

--
-- Table structure for table `lab_test_categories`
--

CREATE TABLE `lab_test_categories` (
  `id` int NOT NULL,
  `category_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `category_code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_test_categories`
--

INSERT INTO `lab_test_categories` (`id`, `category_name`, `category_code`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Hematology', 'HEMA', 'Blood cell counts and related tests', 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(2, 'Clinical Chemistry', 'CHEM', 'Chemical analysis of blood and body fluids', 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(3, 'Microbiology', 'MICRO', 'Bacterial, viral, and fungal tests', 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(4, 'Immunology', 'IMMUNO', 'Immune system and antibody tests', 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(5, 'Urinalysis', 'URINE', 'Urine examination tests', 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35');

-- --------------------------------------------------------

--
-- Table structure for table `lab_test_orders`
--

CREATE TABLE `lab_test_orders` (
  `id` int NOT NULL,
  `visit_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `consultation_id` int DEFAULT NULL COMMENT 'NULL if direct lab visit',
  `test_id` int NOT NULL,
  `ordered_by` int NOT NULL COMMENT 'Doctor or Receptionist',
  `assigned_to` int DEFAULT NULL COMMENT 'Lab technician',
  `priority` enum('normal','urgent','stat') COLLATE utf8mb4_general_ci DEFAULT 'normal',
  `status` enum('pending','sample_collected','in_progress','completed','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `cancellation_reason` text COLLATE utf8mb4_general_ci,
  `instructions` text COLLATE utf8mb4_general_ci,
  `sample_collected_at` timestamp NULL DEFAULT NULL,
  `expected_completion` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_test_orders`
--

INSERT INTO `lab_test_orders` (`id`, `visit_id`, `patient_id`, `consultation_id`, `test_id`, `ordered_by`, `assigned_to`, `priority`, `status`, `cancellation_reason`, `instructions`, `sample_collected_at`, `expected_completion`, `created_at`, `updated_at`) VALUES
(1, 4, 4, 4, 4, 3, 4, 'normal', 'completed', NULL, NULL, NULL, NULL, '2025-10-17 08:04:05', '2025-10-17 08:05:15'),
(2, 6, 6, 6, 4, 3, 4, 'normal', 'completed', NULL, NULL, NULL, NULL, '2025-10-18 05:14:25', '2025-10-22 06:32:32'),
(3, 11, 8, 11, 4, 3, 4, 'normal', 'completed', NULL, NULL, NULL, NULL, '2025-10-21 19:21:27', '2025-10-21 20:13:15'),
(4, 11, 8, 11, 2, 3, 4, 'normal', 'pending', NULL, NULL, NULL, NULL, '2025-10-21 19:21:27', '2025-10-21 19:21:27'),
(5, 13, 10, 13, 14, 3, 4, 'normal', 'completed', NULL, NULL, NULL, NULL, '2025-10-22 06:52:41', '2025-10-22 06:54:06'),
(6, 15, 12, 15, 3, 3, 4, 'normal', 'completed', NULL, NULL, NULL, NULL, '2025-10-22 08:20:21', '2025-10-22 08:21:17');

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `generic_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `strength` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'e.g., 500mg, 250mg/5ml',
  `unit` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'tablets, capsules, ml, etc',
  `unit_price` decimal(10,2) NOT NULL,
  `reorder_level` int DEFAULT '20' COMMENT 'Alert when stock below this',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`id`, `name`, `generic_name`, `description`, `strength`, `unit`, `unit_price`, `reorder_level`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Paracetamol', 'Acetaminophen', 'Pain relief and fever reduction', '500mg', 'tablets', 50.00, 500, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(2, 'Amoxicillin', 'Amoxicillin', 'Antibiotic for bacterial infections', '500mg', 'capsules', 200.00, 300, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(3, 'Metronidazole', 'Metronidazole', 'Antibiotic and antiprotozoal', '400mg', 'tablets', 150.00, 300, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(4, 'Ibuprofen', 'Ibuprofen', 'Anti-inflammatory and pain relief', '400mg', 'tablets', 100.00, 400, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(5, 'Ciprofloxacin', 'Ciprofloxacin', 'Broad-spectrum antibiotic', '500mg', 'tablets', 300.00, 200, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(6, 'Omeprazole', 'Omeprazole', 'Reduces stomach acid production', '20mg', 'capsules', 250.00, 200, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(7, 'Chloroquine', 'Chloroquine', 'Antimalarial medication', '250mg', 'tablets', 100.00, 500, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(8, 'Artemether-Lumefantrine', 'AL', 'First-line malaria treatment', '20/120mg', 'tablets', 500.00, 300, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(9, 'Metformin', 'Metformin', 'Type 2 diabetes management', '500mg', 'tablets', 100.00, 400, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(10, 'Amlodipine', 'Amlodipine', 'Blood pressure medication', '5mg', 'tablets', 150.00, 300, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(11, 'Salbutamol', 'Salbutamol', 'Asthma relief inhaler', '100mcg', 'inhaler', 1500.00, 50, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(12, 'Cetirizine', 'Cetirizine', 'Antihistamine for allergies', '10mg', 'tablets', 80.00, 300, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(13, 'Multivitamins', 'Multivitamins', 'Daily vitamin supplement', 'Adult', 'tablets', 150.00, 200, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(14, 'ORS', 'Oral Rehydration Salts', 'Dehydration treatment', '27.9g', 'sachets', 200.00, 500, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(15, 'Diclofenac', 'Diclofenac', 'Pain and inflammation relief', '50mg', 'tablets', 120.00, 300, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35');

-- --------------------------------------------------------

--
-- Table structure for table `medicine_batches`
--

CREATE TABLE `medicine_batches` (
  `id` int NOT NULL,
  `medicine_id` int NOT NULL,
  `batch_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `quantity_received` int NOT NULL,
  `quantity_remaining` int NOT NULL,
  `expiry_date` date NOT NULL,
  `supplier` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `received_date` date NOT NULL,
  `received_by` int NOT NULL,
  `status` enum('active','expired','depleted') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicine_batches`
--

INSERT INTO `medicine_batches` (`id`, `medicine_id`, `batch_number`, `quantity_received`, `quantity_remaining`, `expiry_date`, `supplier`, `cost_price`, `received_date`, `received_by`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'PARA-2024-001', 1000, 1000, '2026-12-31', 'MedSupply Ltd', 40.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(2, 2, 'AMOX-2024-001', 500, 500, '2026-06-30', 'MedSupply Ltd', 150.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(3, 3, 'METRO-2024-001', 500, 500, '2026-08-31', 'PharmaDistrib', 120.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(4, 4, 'IBU-2024-001', 800, 800, '2027-03-31', 'MedSupply Ltd', 80.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(5, 5, 'CIPRO-2024-001', 300, 300, '2026-10-31', 'PharmaDistrib', 250.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(6, 6, 'OMEP-2024-001', 400, 400, '2026-09-30', 'MedSupply Ltd', 200.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(7, 7, 'CHL-2024-001', 1000, 1000, '2027-12-31', 'PharmaDistrib', 80.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(8, 8, 'AL-2024-001', 600, 600, '2026-11-30', 'Global Health', 400.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(9, 9, 'MET-2024-001', 800, 800, '2027-06-30', 'MedSupply Ltd', 80.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(10, 10, 'AML-2024-001', 500, 500, '2026-12-31', 'PharmaDistrib', 120.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(11, 11, 'SAL-2024-001', 100, 100, '2026-05-31', 'RespiCare', 1200.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(12, 12, 'CET-2024-001', 600, 600, '2027-02-28', 'MedSupply Ltd', 60.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(13, 13, 'MULTI-2024-001', 400, 400, '2026-12-31', 'Nutrition Plus', 120.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(14, 14, 'ORS-2024-001', 1000, 1000, '2027-12-31', 'WHO Supply', 150.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(15, 15, 'DICLO-2024-001', 600, 600, '2026-08-31', 'PharmaDistrib', 100.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35');

-- --------------------------------------------------------

--
-- Table structure for table `medicine_dispensing`
--

CREATE TABLE `medicine_dispensing` (
  `id` int NOT NULL,
  `prescription_id` int NOT NULL,
  `batch_id` int NOT NULL,
  `quantity` int NOT NULL,
  `dispensed_by` int NOT NULL,
  `dispensed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `medicine_prescription_stats`
-- (See below for the actual view)
--
CREATE TABLE `medicine_prescription_stats` (
`generic_name` varchar(100)
,`id` int
,`name` varchar(100)
,`times_prescribed` bigint
,`total_quantity_dispensed` decimal(32,0)
,`total_revenue` decimal(42,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `medicine_stock_status`
-- (See below for the actual view)
--
CREATE TABLE `medicine_stock_status` (
`active_batches` bigint
,`generic_name` varchar(100)
,`id` int
,`name` varchar(100)
,`nearest_expiry` date
,`reorder_level` int
,`stock_alert` varchar(13)
,`strength` varchar(50)
,`total_stock` decimal(32,0)
,`unit` varchar(20)
,`unit_price` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int NOT NULL,
  `registration_number` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `occupation` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `emergency_contact_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `emergency_contact_phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `blood_group` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'A+, B-, O+, etc',
  `allergies` text COLLATE utf8mb4_general_ci COMMENT 'Known allergies',
  `chronic_conditions` text COLLATE utf8mb4_general_ci COMMENT 'Diabetes, Hypertension, etc',
  `insurance_company` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Future use',
  `insurance_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Future use',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `registration_number`, `first_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `occupation`, `emergency_contact_name`, `emergency_contact_phone`, `blood_group`, `allergies`, `chronic_conditions`, `insurance_company`, `insurance_number`, `created_at`, `updated_at`) VALUES
(1, 'KJ20250001', 'lwena', 'samson', '2001-07-02', 'male', '068327434', 'lwena027@gmail.com', '', NULL, 'ADAM lwena home of technologies LWENA', '0683274343', NULL, NULL, NULL, NULL, NULL, '2025-10-11 03:50:52', '2025-10-11 03:50:52'),
(2, 'KJ20250002', 'adam', 'lwena', '2025-07-02', 'male', '0683274343', 'adamlwena22@gmai.com', '', NULL, 'jumla', '0683274343', NULL, NULL, NULL, NULL, NULL, '2025-10-11 06:04:10', '2025-10-11 06:04:10'),
(3, 'KJ20250003', 'adam', 'lwena', '2025-05-04', 'male', '0683274343', 'adamlwena22@gmai.com', '', NULL, 'adam samson lwena', '0683274343', NULL, NULL, NULL, NULL, NULL, '2025-10-11 11:12:44', '2025-10-11 11:12:44'),
(4, 'KJ20250004', 'diamond', 'platinumz', '1984-04-02', 'male', '087242534', 'platnumz@gmai.com', '', NULL, 'jumla lokole', '0683274343', NULL, NULL, NULL, NULL, NULL, '2025-10-17 08:02:10', '2025-10-17 08:02:10'),
(5, 'KJ20250005', 'sule', 'sule', '2025-05-11', 'male', '6543245', 'hjjf@gmail.com', '', NULL, 'zahanati', '0987645678', NULL, NULL, NULL, NULL, NULL, '2025-10-17 08:35:54', '2025-10-17 08:35:54'),
(6, 'KJ20250006', 'hamza', 'mtinangi', '2005-02-02', 'male', '07212121212', 'hamza@gmail.com', '', NULL, 'lwena samson', '068327434', NULL, NULL, NULL, NULL, NULL, '2025-10-18 05:02:27', '2025-10-18 05:02:27'),
(7, 'KJ20250007', 'winifrida', 'lwena', '2006-06-08', 'female', '65437234', 'win@gmail.com', '', NULL, 'zawadi lwena', '097876654', NULL, NULL, NULL, NULL, NULL, '2025-10-21 19:04:49', '2025-10-21 19:04:49'),
(8, 'KJ20250008', 'zawadi', 'lwena', '1999-02-03', 'male', '426436542', 'zawadi@gmail.com', '', NULL, 'ignas', '12342453', NULL, NULL, NULL, NULL, NULL, '2025-10-21 19:08:48', '2025-10-21 19:08:48'),
(9, 'KJ20250009', 'jackline', 'lwena', '2003-05-04', 'female', '43452346', 'jack@gmail.com', '', NULL, 'win', '45465335', NULL, NULL, NULL, NULL, NULL, '2025-10-21 19:18:47', '2025-10-21 19:18:47'),
(10, 'KJ20250010', 'hilghat', 'nindi', '1956-04-03', 'female', '0755059343', 'nindi@gmail.com', '', NULL, 'lwena adam', '0683274343', NULL, NULL, NULL, NULL, NULL, '2025-10-22 06:51:49', '2025-10-22 06:51:49'),
(11, 'KJ20250011', 'july', 'millinga', '2006-04-03', 'male', '76543384', 'millinga@gmail.com', '', NULL, 'clala', '43245376', NULL, NULL, NULL, NULL, NULL, '2025-10-22 07:57:57', '2025-10-22 07:57:57'),
(12, 'KJ20250012', 'lisah', 'kagemuro', '2003-04-02', 'female', '857635423734', 'lisah@gmaail.com', '', NULL, 'ntui', '324521567', NULL, NULL, NULL, NULL, NULL, '2025-10-22 08:17:18', '2025-10-22 08:17:18'),
(13, 'KJ20250013', 'jackline', 'jfhf', '2005-05-07', 'male', '657788989', 'j@gmail.com', '', NULL, 'line', '75467876', NULL, NULL, NULL, NULL, NULL, '2025-10-23 11:01:32', '2025-10-23 11:01:32');

-- --------------------------------------------------------

--
-- Stand-in structure for view `patient_latest_visit`
-- (See below for the actual view)
--
CREATE TABLE `patient_latest_visit` (
`created_at` timestamp
,`patient_id` int
,`status` enum('active','completed','cancelled')
,`updated_at` timestamp
,`visit_date` date
,`visit_id` int
,`visit_number` int
,`visit_type` enum('consultation','lab_only','minor_service')
);

-- --------------------------------------------------------

--
-- Table structure for table `patient_visits`
--

CREATE TABLE `patient_visits` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `visit_number` int NOT NULL COMMENT 'Sequential visit number for this patient',
  `visit_date` date NOT NULL,
  `visit_type` enum('consultation','lab_only','minor_service') COLLATE utf8mb4_general_ci NOT NULL,
  `assigned_doctor_id` int DEFAULT NULL COMMENT 'Future: pre-assigned doctor',
  `registered_by` int NOT NULL,
  `status` enum('active','completed','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_visits`
--

INSERT INTO `patient_visits` (`id`, `patient_id`, `visit_number`, `visit_date`, `visit_type`, `assigned_doctor_id`, `registered_by`, `status`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-10-11', 'consultation', NULL, 2, 'active', NULL, '2025-10-11 03:50:52', '2025-10-20 06:40:16'),
(2, 2, 1, '2025-10-11', 'consultation', NULL, 2, 'active', NULL, '2025-10-11 06:04:10', '2025-10-20 06:40:16'),
(3, 3, 1, '2025-10-11', 'consultation', NULL, 2, 'active', NULL, '2025-10-11 11:12:44', '2025-10-20 06:40:16'),
(4, 4, 1, '2025-10-17', 'consultation', NULL, 2, 'active', NULL, '2025-10-17 08:02:10', '2025-10-20 06:40:16'),
(5, 5, 1, '2025-10-17', 'consultation', NULL, 2, 'active', NULL, '2025-10-17 08:35:54', '2025-10-20 06:40:16'),
(6, 6, 1, '2025-10-18', 'consultation', NULL, 2, 'active', NULL, '2025-10-18 05:02:27', '2025-10-20 06:40:16'),
(7, 1, 2, '2025-10-20', 'consultation', NULL, 2, 'active', NULL, '2025-10-20 07:00:37', '2025-10-22 19:11:12'),
(9, 6, 2, '2025-10-20', 'consultation', NULL, 2, 'active', NULL, '2025-10-20 07:12:12', '2025-10-22 06:32:32'),
(10, 7, 1, '2025-10-21', 'consultation', NULL, 2, 'active', NULL, '2025-10-21 19:04:49', '2025-10-21 19:04:49'),
(11, 8, 1, '2025-10-21', 'consultation', NULL, 2, 'active', NULL, '2025-10-21 19:08:48', '2025-10-23 19:37:14'),
(12, 9, 1, '2025-10-21', 'consultation', NULL, 2, 'active', NULL, '2025-10-21 19:18:47', '2025-10-21 19:18:47'),
(13, 10, 1, '2025-10-22', 'consultation', NULL, 2, 'active', NULL, '2025-10-22 06:51:49', '2025-10-22 07:48:12'),
(14, 11, 1, '2025-10-22', 'consultation', NULL, 2, 'active', NULL, '2025-10-22 07:57:57', '2025-10-22 07:57:57'),
(15, 12, 1, '2025-10-22', 'consultation', NULL, 2, 'active', NULL, '2025-10-22 08:17:18', '2025-10-22 08:21:17'),
(16, 13, 1, '2025-10-23', 'consultation', NULL, 2, 'active', NULL, '2025-10-23 11:01:32', '2025-10-23 19:24:30');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `visit_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `payment_type` enum('registration','lab_test','medicine','minor_service') COLLATE utf8mb4_general_ci NOT NULL,
  `item_id` int DEFAULT NULL COMMENT 'Reference to lab_order, prescription, or service',
  `item_type` enum('lab_order','prescription','service') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','mobile_money','insurance') COLLATE utf8mb4_general_ci NOT NULL,
  `payment_status` enum('pending','paid','cancelled','refunded') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `reference_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Receipt/Transaction number',
  `collected_by` int NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notes` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `visit_id`, `patient_id`, `payment_type`, `item_id`, `item_type`, `amount`, `payment_method`, `payment_status`, `reference_number`, `collected_by`, `payment_date`, `notes`) VALUES
(1, 1, 1, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 2, '2025-10-11 03:50:52', 'Initial consultation payment'),
(2, 2, 2, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 2, '2025-10-11 06:04:10', 'Initial consultation payment'),
(3, 3, 3, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 2, '2025-10-11 11:12:44', 'Initial consultation payment'),
(4, 4, 4, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 2, '2025-10-17 08:02:10', 'Initial consultation payment'),
(5, 4, 4, 'lab_test', 1, 'lab_order', 8000.00, 'cash', 'paid', '', 2, '2025-10-17 08:04:55', NULL),
(6, 5, 5, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 2, '2025-10-17 08:35:54', 'Initial consultation payment'),
(7, 6, 6, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 2, '2025-10-18 05:02:27', 'Initial consultation payment'),
(8, 7, 1, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 2, '2025-10-20 07:00:37', 'Revisit payment - Visit #2'),
(10, 9, 6, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 2, '2025-10-20 07:12:12', 'Revisit payment - Visit #2'),
(11, 10, 7, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 2, '2025-10-21 19:04:49', 'Initial consultation payment'),
(12, 11, 8, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 2, '2025-10-21 19:08:48', 'Initial consultation payment'),
(13, 12, 9, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 2, '2025-10-21 19:18:47', 'Initial consultation payment'),
(14, 11, 8, 'lab_test', 3, 'lab_order', 13000.00, 'cash', 'paid', '', 2, '2025-10-21 20:08:25', NULL),
(15, 6, 6, 'medicine', 2, 'prescription', 500.00, 'cash', 'paid', '', 2, '2025-10-22 06:11:55', NULL),
(16, 6, 6, 'medicine', 2, 'prescription', 500.00, 'cash', 'paid', '', 2, '2025-10-22 06:24:36', NULL),
(17, 6, 6, 'lab_test', 2, 'lab_order', 8000.00, 'cash', 'paid', '', 2, '2025-10-22 06:25:03', NULL),
(18, 6, 6, 'lab_test', 2, 'lab_order', 8000.00, 'cash', 'paid', '', 2, '2025-10-22 06:29:58', NULL),
(19, 13, 10, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 2, '2025-10-22 06:51:49', 'Initial consultation payment'),
(20, 13, 10, 'lab_test', 5, 'lab_order', 8000.00, 'cash', 'paid', '', 2, '2025-10-22 06:53:23', NULL),
(21, 14, 11, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 2, '2025-10-22 07:57:57', 'Initial consultation payment'),
(22, 15, 12, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 2, '2025-10-22 08:17:18', 'Initial consultation payment'),
(23, 15, 12, 'lab_test', 6, 'lab_order', 5000.00, 'cash', 'paid', '', 2, '2025-10-22 08:20:51', NULL),
(24, 7, 1, 'medicine', 3, 'prescription', 500.00, 'cash', 'paid', '', 2, '2025-10-22 19:11:12', NULL),
(25, 16, 13, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 2, '2025-10-23 11:01:32', 'Initial consultation payment');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int NOT NULL,
  `consultation_id` int NOT NULL,
  `visit_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `medicine_id` int NOT NULL,
  `quantity_prescribed` int NOT NULL,
  `quantity_dispensed` int DEFAULT '0' COMMENT 'Actual amount given',
  `dosage` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'e.g., 1 tablet',
  `frequency` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'e.g., 2x3 (twice, 3 times daily)',
  `duration` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'e.g., 7 days',
  `instructions` text COLLATE utf8mb4_general_ci,
  `status` enum('pending','partial','dispensed','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `cancellation_reason` text COLLATE utf8mb4_general_ci,
  `dispensed_by` int DEFAULT NULL,
  `dispensed_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `consultation_id`, `visit_id`, `patient_id`, `doctor_id`, `medicine_id`, `quantity_prescribed`, `quantity_dispensed`, `dosage`, `frequency`, `duration`, `instructions`, `status`, `cancellation_reason`, `dispensed_by`, `dispensed_at`, `notes`, `created_at`, `updated_at`) VALUES
(2, 6, 6, 6, 3, 1, 10, 0, '200g', 'as prescribed', '', '4', 'pending', NULL, NULL, NULL, NULL, '2025-10-18 05:14:25', '2025-10-18 05:14:25'),
(3, 7, 7, 1, 3, 1, 10, 0, '200g', 'as prescribed', '', '2 kila siku', 'pending', NULL, NULL, NULL, NULL, '2025-10-22 06:48:58', '2025-10-22 06:48:58'),
(7, 16, 16, 13, 3, 1, 10, 0, 'yf', 'as prescribed', '', '2 daily', 'pending', NULL, NULL, NULL, NULL, '2025-10-23 19:24:30', '2025-10-23 19:24:30'),
(8, 11, 11, 8, 3, 9, 10, 0, '1 tablet', 'Once daily', '1', '12', 'pending', NULL, NULL, NULL, NULL, '2025-10-23 19:37:14', '2025-10-23 19:37:14');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int NOT NULL,
  `service_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `service_code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `requires_doctor` tinyint(1) DEFAULT '0' COMMENT 'Whether doctor must be involved',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `service_code`, `price`, `description`, `requires_doctor`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Consultation Fee', 'CONSULT', 3000.00, 'Standard medical consultation', 1, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(2, 'Blood Pressure Check', 'BP-CHECK', 1000.00, 'Blood pressure measurement', 0, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(3, 'Wound Dressing', 'DRESS', 5000.00, 'Wound cleaning and dressing', 0, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(4, 'Injection', 'INJ', 2000.00, 'Intramuscular or IV injection', 0, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(5, 'ECG', 'ECG', 20000.00, 'Electrocardiogram recording', 0, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35');

-- --------------------------------------------------------

--
-- Table structure for table `service_orders`
--

CREATE TABLE `service_orders` (
  `id` int NOT NULL,
  `visit_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `service_id` int NOT NULL,
  `ordered_by` int NOT NULL COMMENT 'Doctor or Receptionist',
  `performed_by` int DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `cancellation_reason` text COLLATE utf8mb4_general_ci,
  `notes` text COLLATE utf8mb4_general_ci,
  `performed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `staff_performance`
-- (See below for the actual view)
--
CREATE TABLE `staff_performance` (
`consultations_completed` bigint
,`id` int
,`patients_registered` bigint
,`payments_collected` bigint
,`prescriptions_written` bigint
,`role` enum('admin','receptionist','doctor','lab_technician')
,`staff_name` varchar(101)
,`tests_completed` bigint
,`total_collected` decimal(32,2)
);

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
  `specialization` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'For doctors - future use',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `email`, `role`, `first_name`, `last_name`, `phone`, `specialization`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@clinic.local', 'admin', 'System', 'Administrator', '0700000001', NULL, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(2, 'reception', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'reception@clinic.local', 'receptionist', 'Jane', 'Receptionist', '0700000002', NULL, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(3, 'doctor', '$2y$10$fTzmUB8VstFAp0vIB27/CeA/YyVCEVK5.rhptzgntegwB8H1jk7ze', 'doctor@clinic.local', 'doctor', 'Dr. John', 'Smith', '0700000003', NULL, 1, '2025-10-11 03:12:35', '2025-10-21 19:06:03'),
(4, 'lab', '$2y$10$9IhiDdKHbbxL5UflBKvGP.7JopAxlSgVMO3Ge966PHZiFtqT5PCgu', 'lab@clinic.local', 'lab_technician', 'Mary', 'Technician', '0700000004', NULL, 1, '2025-10-11 03:12:35', '2025-10-21 19:29:06'),
(5, 'adm', '$2y$10$z5McVHsnkImJ81WlacP4ROypVtt45zj834JsAAMXWhxb4igAhb8TS', 'adamlwena22@gmai.com', 'admin', 'adam', 'lwena', '0683274343', NULL, 1, '2025-10-11 11:10:19', '2025-10-11 11:10:19'),
(6, 'lab1', '$2y$10$G6XGmh0osvYeXRFmBExQxuByb4V3ddfyx61gNQI.Aw3AD4nYjBtMy', 'mpimaji!@gmail.com', 'lab_technician', 'mpimaji', 'mpimaji', '245321425', NULL, 1, '2025-10-21 19:30:40', '2025-10-21 19:30:40');

-- --------------------------------------------------------

--
-- Table structure for table `vital_signs`
--

CREATE TABLE `vital_signs` (
  `id` int NOT NULL,
  `visit_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `temperature` decimal(4,1) DEFAULT NULL COMMENT 'Celsius',
  `blood_pressure_systolic` int DEFAULT NULL,
  `blood_pressure_diastolic` int DEFAULT NULL,
  `pulse_rate` int DEFAULT NULL COMMENT 'bpm',
  `respiratory_rate` int DEFAULT NULL COMMENT 'breaths per minute',
  `weight` decimal(5,1) DEFAULT NULL COMMENT 'kg',
  `height` decimal(5,1) DEFAULT NULL COMMENT 'cm',
  `bmi` decimal(4,1) GENERATED ALWAYS AS ((case when (`height` > 0) then (`weight` / ((`height` / 100) * (`height` / 100))) else NULL end)) STORED,
  `recorded_by` int NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vital_signs`
--

INSERT INTO `vital_signs` (`id`, `visit_id`, `patient_id`, `temperature`, `blood_pressure_systolic`, `blood_pressure_diastolic`, `pulse_rate`, `respiratory_rate`, `weight`, `height`, `recorded_by`, `recorded_at`) VALUES
(1, 1, 1, 35.0, 120, 80, 75, NULL, 60.0, 127.0, 2, '2025-10-11 03:50:52'),
(2, 2, 2, 37.0, 120, 270, 76, NULL, 45.0, 3.0, 2, '2025-10-11 06:04:10'),
(3, 3, 3, 36.0, 120, 270, 76, NULL, 35.0, 123.0, 2, '2025-10-11 11:12:44'),
(4, 4, 4, 36.0, 120, 270, 120, NULL, 78.0, 178.0, 2, '2025-10-17 08:02:10'),
(5, 5, 5, 36.0, 120, 80, 75, NULL, 60.0, 127.0, 2, '2025-10-17 08:35:54'),
(6, 6, 6, 36.0, 120, 80, 75, NULL, 60.0, 127.0, 2, '2025-10-18 05:02:27'),
(7, 11, 8, 36.0, 120, 57, 120, NULL, 75.0, 178.0, 2, '2025-10-21 19:08:48'),
(8, 12, 9, 36.0, 120, 57, 120, NULL, 48.0, 123.0, 2, '2025-10-21 19:18:47'),
(9, 13, 10, 36.0, 120, 80, 80, NULL, 68.0, 120.0, 2, '2025-10-22 06:51:49'),
(10, 14, 11, 36.0, 120, 80, 120, NULL, 65.0, 102.0, 2, '2025-10-22 07:57:57'),
(11, 15, 12, 36.0, 120, 80, 120, NULL, 57.0, 120.0, 2, '2025-10-22 08:17:18'),
(12, 16, 13, 36.0, 120, 57, 120, NULL, 129.0, 200.0, 2, '2025-10-23 11:01:32');

-- --------------------------------------------------------

--
-- Structure for view `active_patient_queue`
--
DROP TABLE IF EXISTS `active_patient_queue`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_patient_queue`  AS SELECT `v`.`id` AS `visit_id`, `v`.`visit_type` AS `visit_type`, `v`.`visit_date` AS `visit_date`, `p`.`id` AS `patient_id`, `p`.`registration_number` AS `registration_number`, concat(`p`.`first_name`,' ',`p`.`last_name`) AS `patient_name`, `p`.`phone` AS `phone`, `p`.`gender` AS `gender`, timestampdiff(YEAR,`p`.`date_of_birth`,curdate()) AS `age`, `vs`.`temperature` AS `temperature`, `vs`.`pulse_rate` AS `pulse_rate`, `vs`.`blood_pressure_systolic` AS `blood_pressure_systolic`, `vs`.`blood_pressure_diastolic` AS `blood_pressure_diastolic`, `c`.`id` AS `consultation_id`, `c`.`status` AS `consultation_status`, concat(`u`.`first_name`,' ',`u`.`last_name`) AS `doctor_name`, sum((case when ((`pay`.`payment_status` = 'paid') and (`pay`.`payment_type` = 'registration')) then `pay`.`amount` else 0 end)) AS `registration_paid`, count(distinct (case when (`lo`.`status` in ('pending','sample_collected','in_progress')) then `lo`.`id` end)) AS `pending_lab_tests`, count(distinct (case when (`lo`.`status` = 'completed') then `lo`.`id` end)) AS `completed_lab_tests`, count(distinct (case when (`pr`.`status` = 'pending') then `pr`.`id` end)) AS `pending_prescriptions`, count(distinct (case when (`pr`.`status` = 'partial') then `pr`.`id` end)) AS `partial_prescriptions`, `v`.`created_at` AS `registration_time` FROM (((((((`patient_visits` `v` join `patients` `p` on((`v`.`patient_id` = `p`.`id`))) left join `vital_signs` `vs` on((`v`.`id` = `vs`.`visit_id`))) left join `consultations` `c` on(((`v`.`id` = `c`.`visit_id`) and (`c`.`status` <> 'cancelled')))) left join `users` `u` on((`c`.`doctor_id` = `u`.`id`))) left join `payments` `pay` on((`v`.`id` = `pay`.`visit_id`))) left join `lab_test_orders` `lo` on(((`v`.`id` = `lo`.`visit_id`) and (`lo`.`status` <> 'cancelled')))) left join `prescriptions` `pr` on(((`v`.`id` = `pr`.`visit_id`) and (`pr`.`status` <> 'cancelled')))) WHERE (`v`.`status` = 'active') GROUP BY `v`.`id`, `v`.`visit_type`, `v`.`visit_date`, `p`.`id`, `p`.`registration_number`, `p`.`first_name`, `p`.`last_name`, `p`.`phone`, `p`.`gender`, `p`.`date_of_birth`, `vs`.`temperature`, `vs`.`pulse_rate`, `vs`.`blood_pressure_systolic`, `vs`.`blood_pressure_diastolic`, `c`.`id`, `c`.`status`, `u`.`first_name`, `u`.`last_name`, `v`.`created_at` ORDER BY `v`.`created_at` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `common_diagnoses`
--
DROP TABLE IF EXISTS `common_diagnoses`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `common_diagnoses`  AS SELECT `consultations`.`diagnosis` AS `diagnosis`, count(0) AS `occurrence_count`, count(distinct `consultations`.`patient_id`) AS `unique_patients` FROM `consultations` WHERE ((`consultations`.`diagnosis` is not null) AND (`consultations`.`diagnosis` <> '') AND (`consultations`.`status` = 'completed')) GROUP BY `consultations`.`diagnosis` ORDER BY count(0) DESC ;

-- --------------------------------------------------------

--
-- Structure for view `daily_revenue_summary`
--
DROP TABLE IF EXISTS `daily_revenue_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `daily_revenue_summary`  AS SELECT cast(`payments`.`payment_date` as date) AS `revenue_date`, `payments`.`payment_type` AS `payment_type`, `payments`.`payment_method` AS `payment_method`, count(0) AS `transaction_count`, sum(`payments`.`amount`) AS `total_amount`, concat(`uc`.`first_name`,' ',`uc`.`last_name`) AS `collected_by_name` FROM (`payments` join `users` `uc` on((`payments`.`collected_by` = `uc`.`id`))) WHERE (`payments`.`payment_status` = 'paid') GROUP BY cast(`payments`.`payment_date` as date), `payments`.`payment_type`, `payments`.`payment_method`, `uc`.`first_name`, `uc`.`last_name` ORDER BY cast(`payments`.`payment_date` as date) DESC, `payments`.`payment_type` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `medicine_prescription_stats`
--
DROP TABLE IF EXISTS `medicine_prescription_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `medicine_prescription_stats`  AS SELECT `m`.`id` AS `id`, `m`.`name` AS `name`, `m`.`generic_name` AS `generic_name`, count(`pr`.`id`) AS `times_prescribed`, sum(`pr`.`quantity_dispensed`) AS `total_quantity_dispensed`, sum((`pr`.`quantity_dispensed` * `m`.`unit_price`)) AS `total_revenue` FROM (`medicines` `m` join `prescriptions` `pr` on((`m`.`id` = `pr`.`medicine_id`))) WHERE (`pr`.`status` in ('dispensed','partial')) GROUP BY `m`.`id`, `m`.`name`, `m`.`generic_name` ORDER BY count(`pr`.`id`) DESC ;

-- --------------------------------------------------------

--
-- Structure for view `medicine_stock_status`
--
DROP TABLE IF EXISTS `medicine_stock_status`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `medicine_stock_status`  AS SELECT `m`.`id` AS `id`, `m`.`name` AS `name`, `m`.`generic_name` AS `generic_name`, `m`.`strength` AS `strength`, `m`.`unit` AS `unit`, `m`.`unit_price` AS `unit_price`, `m`.`reorder_level` AS `reorder_level`, sum(`mb`.`quantity_remaining`) AS `total_stock`, count(distinct `mb`.`id`) AS `active_batches`, min((case when (`mb`.`status` = 'active') then `mb`.`expiry_date` end)) AS `nearest_expiry`, (case when (sum(`mb`.`quantity_remaining`) <= `m`.`reorder_level`) then 'LOW_STOCK' when (min((case when (`mb`.`status` = 'active') then `mb`.`expiry_date` end)) <= (curdate() + interval 3 month)) then 'EXPIRING_SOON' else 'OK' end) AS `stock_alert` FROM (`medicines` `m` left join `medicine_batches` `mb` on(((`m`.`id` = `mb`.`medicine_id`) and (`mb`.`status` = 'active')))) WHERE (`m`.`is_active` = 1) GROUP BY `m`.`id`, `m`.`name`, `m`.`generic_name`, `m`.`strength`, `m`.`unit`, `m`.`unit_price`, `m`.`reorder_level` ;

-- --------------------------------------------------------

--
-- Structure for view `patient_latest_visit`
--
DROP TABLE IF EXISTS `patient_latest_visit`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `patient_latest_visit`  AS SELECT `pv`.`patient_id` AS `patient_id`, `pv`.`id` AS `visit_id`, `pv`.`visit_number` AS `visit_number`, `pv`.`status` AS `status`, `pv`.`visit_type` AS `visit_type`, `pv`.`visit_date` AS `visit_date`, `pv`.`created_at` AS `created_at`, `pv`.`updated_at` AS `updated_at` FROM (`patient_visits` `pv` join (select `patient_visits`.`patient_id` AS `patient_id`,max(`patient_visits`.`created_at`) AS `latest` from `patient_visits` group by `patient_visits`.`patient_id`) `latest` on(((`latest`.`patient_id` = `pv`.`patient_id`) and (`latest`.`latest` = `pv`.`created_at`)))) ;

-- --------------------------------------------------------

--
-- Structure for view `staff_performance`
--
DROP TABLE IF EXISTS `staff_performance`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `staff_performance`  AS SELECT `u`.`id` AS `id`, concat(`u`.`first_name`,' ',`u`.`last_name`) AS `staff_name`, `u`.`role` AS `role`, count(distinct (case when (`u`.`role` = 'receptionist') then `v`.`id` end)) AS `patients_registered`, count(distinct (case when (`u`.`role` = 'receptionist') then `p`.`id` end)) AS `payments_collected`, sum((case when ((`u`.`role` = 'receptionist') and (`p`.`payment_status` = 'paid')) then `p`.`amount` else 0 end)) AS `total_collected`, count(distinct (case when (`u`.`role` = 'doctor') then `c`.`id` end)) AS `consultations_completed`, count(distinct (case when (`u`.`role` = 'doctor') then `pr`.`id` end)) AS `prescriptions_written`, count(distinct (case when (`u`.`role` = 'lab_technician') then `lr`.`id` end)) AS `tests_completed` FROM (((((`users` `u` left join `patient_visits` `v` on((`u`.`id` = `v`.`registered_by`))) left join `payments` `p` on((`u`.`id` = `p`.`collected_by`))) left join `consultations` `c` on(((`u`.`id` = `c`.`doctor_id`) and (`c`.`status` = 'completed')))) left join `prescriptions` `pr` on((`u`.`id` = `pr`.`doctor_id`))) left join `lab_results` `lr` on((`u`.`id` = `lr`.`technician_id`))) WHERE (`u`.`is_active` = 1) GROUP BY `u`.`id`, `u`.`first_name`, `u`.`last_name`, `u`.`role` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `consultations`
--
ALTER TABLE `consultations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_consultation_visit` (`visit_id`),
  ADD KEY `idx_consultation_patient` (`patient_id`),
  ADD KEY `idx_consultation_doctor` (`doctor_id`),
  ADD KEY `idx_consultation_status` (`status`);

--
-- Indexes for table `lab_results`
--
ALTER TABLE `lab_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_result_order` (`order_id`),
  ADD KEY `idx_result_patient` (`patient_id`),
  ADD KEY `idx_result_test` (`test_id`),
  ADD KEY `idx_result_critical` (`is_critical`),
  ADD KEY `technician_id` (`technician_id`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indexes for table `lab_tests`
--
ALTER TABLE `lab_tests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `test_code` (`test_code`),
  ADD KEY `idx_test_category` (`category_id`),
  ADD KEY `idx_test_active` (`is_active`);

--
-- Indexes for table `lab_test_categories`
--
ALTER TABLE `lab_test_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_code` (`category_code`);

--
-- Indexes for table `lab_test_orders`
--
ALTER TABLE `lab_test_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_visit` (`visit_id`),
  ADD KEY `idx_order_patient` (`patient_id`),
  ADD KEY `idx_order_consultation` (`consultation_id`),
  ADD KEY `idx_order_test` (`test_id`),
  ADD KEY `idx_order_status` (`status`),
  ADD KEY `idx_order_assigned` (`assigned_to`,`status`),
  ADD KEY `ordered_by` (`ordered_by`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_medicine_active` (`is_active`),
  ADD KEY `idx_medicine_name` (`name`);

--
-- Indexes for table `medicine_batches`
--
ALTER TABLE `medicine_batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_batch` (`medicine_id`,`batch_number`),
  ADD KEY `idx_batch_medicine` (`medicine_id`),
  ADD KEY `idx_batch_expiry` (`expiry_date`),
  ADD KEY `idx_batch_status` (`status`),
  ADD KEY `received_by` (`received_by`);

--
-- Indexes for table `medicine_dispensing`
--
ALTER TABLE `medicine_dispensing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dispensing_prescription` (`prescription_id`),
  ADD KEY `idx_dispensing_batch` (`batch_id`),
  ADD KEY `dispensed_by` (`dispensed_by`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `registration_number` (`registration_number`),
  ADD KEY `idx_patients_reg_number` (`registration_number`),
  ADD KEY `idx_patients_phone` (`phone`),
  ADD KEY `idx_patients_name` (`first_name`,`last_name`);

--
-- Indexes for table `patient_visits`
--
ALTER TABLE `patient_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_visit_patient` (`patient_id`),
  ADD KEY `idx_visit_date` (`visit_date`),
  ADD KEY `idx_visit_status` (`status`),
  ADD KEY `idx_visit_number` (`patient_id`,`visit_number`),
  ADD KEY `assigned_doctor_id` (`assigned_doctor_id`),
  ADD KEY `registered_by` (`registered_by`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_payment_visit` (`visit_id`),
  ADD KEY `idx_payment_patient` (`patient_id`),
  ADD KEY `idx_payment_type` (`payment_type`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_payment_date` (`payment_date`),
  ADD KEY `collected_by` (`collected_by`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_prescription_consultation` (`consultation_id`),
  ADD KEY `idx_prescription_visit` (`visit_id`),
  ADD KEY `idx_prescription_patient` (`patient_id`),
  ADD KEY `idx_prescription_medicine` (`medicine_id`),
  ADD KEY `idx_prescription_status` (`status`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `dispensed_by` (`dispensed_by`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_code` (`service_code`),
  ADD KEY `idx_service_active` (`is_active`);

--
-- Indexes for table `service_orders`
--
ALTER TABLE `service_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_service_order_visit` (`visit_id`),
  ADD KEY `idx_service_order_patient` (`patient_id`),
  ADD KEY `idx_service_order_service` (`service_id`),
  ADD KEY `idx_service_order_status` (`status`),
  ADD KEY `ordered_by` (`ordered_by`),
  ADD KEY `performed_by` (`performed_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_role` (`role`),
  ADD KEY `idx_users_active` (`is_active`);

--
-- Indexes for table `vital_signs`
--
ALTER TABLE `vital_signs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_vitals_visit` (`visit_id`),
  ADD KEY `idx_vitals_patient` (`patient_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `consultations`
--
ALTER TABLE `consultations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `lab_results`
--
ALTER TABLE `lab_results`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lab_tests`
--
ALTER TABLE `lab_tests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `lab_test_categories`
--
ALTER TABLE `lab_test_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lab_test_orders`
--
ALTER TABLE `lab_test_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `medicine_batches`
--
ALTER TABLE `medicine_batches`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `medicine_dispensing`
--
ALTER TABLE `medicine_dispensing`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `patient_visits`
--
ALTER TABLE `patient_visits`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `service_orders`
--
ALTER TABLE `service_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `vital_signs`
--
ALTER TABLE `vital_signs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `consultations`
--
ALTER TABLE `consultations`
  ADD CONSTRAINT `consultations_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultations_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultations_ibfk_3` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `lab_results`
--
ALTER TABLE `lab_results`
  ADD CONSTRAINT `lab_results_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `lab_test_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_results_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_results_ibfk_3` FOREIGN KEY (`test_id`) REFERENCES `lab_tests` (`id`),
  ADD CONSTRAINT `lab_results_ibfk_4` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `lab_results_ibfk_5` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `lab_tests`
--
ALTER TABLE `lab_tests`
  ADD CONSTRAINT `lab_tests_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `lab_test_categories` (`id`);

--
-- Constraints for table `lab_test_orders`
--
ALTER TABLE `lab_test_orders`
  ADD CONSTRAINT `lab_test_orders_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_test_orders_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_test_orders_ibfk_3` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_test_orders_ibfk_4` FOREIGN KEY (`test_id`) REFERENCES `lab_tests` (`id`),
  ADD CONSTRAINT `lab_test_orders_ibfk_5` FOREIGN KEY (`ordered_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `lab_test_orders_ibfk_6` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`);

--
-- Constraints for table `medicine_batches`
--
ALTER TABLE `medicine_batches`
  ADD CONSTRAINT `medicine_batches_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medicine_batches_ibfk_2` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `medicine_dispensing`
--
ALTER TABLE `medicine_dispensing`
  ADD CONSTRAINT `medicine_dispensing_ibfk_1` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medicine_dispensing_ibfk_2` FOREIGN KEY (`batch_id`) REFERENCES `medicine_batches` (`id`),
  ADD CONSTRAINT `medicine_dispensing_ibfk_3` FOREIGN KEY (`dispensed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `patient_visits`
--
ALTER TABLE `patient_visits`
  ADD CONSTRAINT `patient_visits_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_visits_ibfk_2` FOREIGN KEY (`assigned_doctor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `patient_visits_ibfk_3` FOREIGN KEY (`registered_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`collected_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_2` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_3` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_4` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `prescriptions_ibfk_5` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`),
  ADD CONSTRAINT `prescriptions_ibfk_6` FOREIGN KEY (`dispensed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `service_orders`
--
ALTER TABLE `service_orders`
  ADD CONSTRAINT `service_orders_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_orders_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_orders_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  ADD CONSTRAINT `service_orders_ibfk_4` FOREIGN KEY (`ordered_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `service_orders_ibfk_5` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `vital_signs`
--
ALTER TABLE `vital_signs`
  ADD CONSTRAINT `vital_signs_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vital_signs_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vital_signs_ibfk_3` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

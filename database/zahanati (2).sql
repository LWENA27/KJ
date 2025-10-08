-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2025 at 07:02 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

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
-- Table structure for table `consultations`
--

CREATE TABLE `consultations` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `visit_date` date DEFAULT curdate(),
  `registration_number` varchar(20) DEFAULT NULL,
  `patient_age` int(11) DEFAULT NULL,
  `consultation_type` enum('new_patient','follow_up','emergency') DEFAULT 'new_patient',
  `doctor_id` int(11) NOT NULL,
  `appointment_date` datetime NOT NULL,
  `symptoms` text DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `main_complaint` text DEFAULT NULL,
  `on_examination` text DEFAULT NULL,
  `preliminary_diagnosis` text DEFAULT NULL,
  `final_diagnosis` text DEFAULT NULL,
  `lab_investigation` text DEFAULT NULL,
  `treatment_plan` text DEFAULT NULL,
  `blood_pressure` varchar(20) DEFAULT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `weight` decimal(5,1) DEFAULT NULL,
  `height` decimal(5,1) DEFAULT NULL,
  `prescribed_tests` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled','pending_lab_results','follow_up_required') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `consultation_fee` decimal(10,2) DEFAULT 50000.00 COMMENT 'Consultation fee in UGX',
  `consultation_notes` text DEFAULT NULL COMMENT 'Additional consultation notes',
  `follow_up_required` tinyint(1) DEFAULT 0 COMMENT 'Whether follow-up is required',
  `follow_up_date` date DEFAULT NULL COMMENT 'Scheduled follow-up date',
  `discharge_status` enum('pending','discharged','transferred','admitted') DEFAULT 'pending' COMMENT 'Patient discharge status',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT 'When consultation was completed',
  `lab_reviewed_at` timestamp NULL DEFAULT NULL COMMENT 'When lab results were reviewed',
  `lab_review_notes` text DEFAULT NULL COMMENT 'Lab results review notes',
  `has_critical_results` tinyint(1) DEFAULT 0 COMMENT 'Whether consultation has critical lab results requiring immediate attention'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consultations`
--

INSERT INTO `consultations` (`id`, `patient_id`, `visit_date`, `registration_number`, `patient_age`, `consultation_type`, `doctor_id`, `appointment_date`, `symptoms`, `diagnosis`, `treatment`, `notes`, `main_complaint`, `on_examination`, `preliminary_diagnosis`, `final_diagnosis`, `lab_investigation`, `treatment_plan`, `blood_pressure`, `temperature`, `weight`, `height`, `prescribed_tests`, `prescription`, `status`, `created_at`, `updated_at`, `consultation_fee`, `consultation_notes`, `follow_up_required`, `follow_up_date`, `discharge_status`, `completed_at`, `lab_reviewed_at`, `lab_review_notes`, `has_critical_results`) VALUES
(1, 33, '2025-10-08', NULL, NULL, 'new_patient', 1, '2025-10-08 01:13:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '2025-10-07 22:13:06', '2025-10-07 22:13:06', 50000.00, NULL, 0, NULL, 'pending', NULL, NULL, NULL, 0),
(2, 34, '2025-10-08', NULL, NULL, 'new_patient', 1, '2025-10-08 01:15:09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '2025-10-07 22:15:09', '2025-10-07 22:15:09', 50000.00, NULL, 0, NULL, 'pending', NULL, NULL, NULL, 0),
(4, 34, '2025-10-08', NULL, NULL, 'new_patient', 3, '2025-10-08 19:54:32', NULL, NULL, NULL, NULL, 'equieq', 'afhffh', 'jfhejrjh', 'fblrjll', '', 'sjhdsaj', NULL, NULL, NULL, NULL, NULL, '', 'completed', '2025-10-08 16:54:32', '2025-10-08 16:54:32', 50000.00, NULL, 0, NULL, 'pending', NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `lab_results`
--

CREATE TABLE `lab_results` (
  `id` int(11) NOT NULL,
  `consultation_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `technician_id` int(11) NOT NULL,
  `result_value` varchar(100) DEFAULT NULL,
  `result_text` text DEFAULT NULL,
  `status` enum('pending','completed','reviewed') DEFAULT 'pending',
  `sample_date` datetime DEFAULT NULL,
  `result_date` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `lab_order_id` int(11) DEFAULT NULL COMMENT 'Reference to lab_test_orders table',
  `patient_id` int(11) DEFAULT NULL COMMENT 'Direct reference to patient',
  `result_unit` varchar(50) DEFAULT NULL COMMENT 'Unit of measurement',
  `interpretation` text DEFAULT NULL COMMENT 'Clinical interpretation of results',
  `technician_notes` text DEFAULT NULL COMMENT 'Technician notes and observations',
  `is_normal` tinyint(1) DEFAULT 0 COMMENT 'Whether result is within normal range',
  `is_critical` tinyint(1) DEFAULT 0 COMMENT 'Whether result requires immediate attention',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT 'When test was completed',
  `reviewed_by` int(11) DEFAULT NULL COMMENT 'Doctor who reviewed the result',
  `reviewed_at` timestamp NULL DEFAULT NULL COMMENT 'When result was reviewed by doctor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_tests`
--

CREATE TABLE `lab_tests` (
  `id` int(11) NOT NULL,
  `test_name` varchar(200) NOT NULL,
  `test_code` varchar(50) NOT NULL,
  `category_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `normal_range` varchar(100) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `category_code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `tests_ordered` text NOT NULL,
  `test_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`test_categories`)),
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','partial') DEFAULT 'pending',
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `priority` enum('normal','urgent','stat') DEFAULT 'normal',
  `consultation_id` int(11) DEFAULT NULL COMMENT 'Reference to consultation',
  `test_id` int(11) DEFAULT NULL COMMENT 'Reference to specific lab test',
  `technician_id` int(11) DEFAULT NULL COMMENT 'Assigned lab technician',
  `sample_collected_at` timestamp NULL DEFAULT NULL COMMENT 'When sample was collected',
  `expected_completion` timestamp NULL DEFAULT NULL COMMENT 'Expected completion time',
  `instructions` text DEFAULT NULL COMMENT 'Special instructions for the test'
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
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `generic_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `unit_price` decimal(10,2) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `supplier` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
  `id` int(11) NOT NULL,
  `consultation_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `dosage` varchar(100) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `allocated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `allocated_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicine_allocations`
--

INSERT INTO `medicine_allocations` (`id`, `consultation_id`, `medicine_id`, `quantity`, `dosage`, `instructions`, `allocated_at`, `allocated_by`) VALUES
(0, 4, 12, 10, '500g', '2x3', '2025-10-08 16:54:32', 3),
(0, 4, 9, 4, '200g', '2x8', '2025-10-08 16:54:32', 3);

-- --------------------------------------------------------

--
-- Table structure for table `medicine_prescriptions`
--

CREATE TABLE `medicine_prescriptions` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `prescription_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`prescription_data`)),
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','partial') DEFAULT 'pending',
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `dispensed_amount` decimal(10,2) DEFAULT 0.00,
  `dispensed_by` int(11) DEFAULT NULL,
  `dispensed_at` datetime DEFAULT NULL,
  `is_fully_dispensed` tinyint(1) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `registration_number` varchar(20) DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `visit_type` enum('consultation','lab_test','medicine_pickup') DEFAULT 'consultation',
  `consultation_fee` decimal(10,2) DEFAULT NULL,
  `payment_method` enum('cash','card','mobile_money','insurance') DEFAULT NULL,
  `payment_status` enum('pending','paid','partial') DEFAULT 'pending',
  `consultation_registration_paid` tinyint(1) DEFAULT 0,
  `lab_tests_paid` tinyint(1) DEFAULT 0,
  `medicine_dispensed` tinyint(1) DEFAULT 0,
  `final_payment_collected` tinyint(1) DEFAULT 0,
  `current_step` varchar(50) DEFAULT 'consultation_registration',
  `medicine_prescribed` tinyint(1) DEFAULT 0,
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `blood_pressure` varchar(20) DEFAULT NULL,
  `pulse_rate` int(11) DEFAULT NULL,
  `body_weight` decimal(5,1) DEFAULT NULL,
  `height` decimal(5,1) DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `registration_number`, `first_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `occupation`, `visit_type`, `consultation_fee`, `payment_method`, `payment_status`, `consultation_registration_paid`, `lab_tests_paid`, `medicine_dispensed`, `final_payment_collected`, `current_step`, `medicine_prescribed`, `emergency_contact_name`, `emergency_contact_phone`, `temperature`, `blood_pressure`, `pulse_rate`, `body_weight`, `height`, `medical_history`, `created_at`, `updated_at`) VALUES
(27, 'KJ20250001', 'mariamu', 'saidi', NULL, NULL, '0683274343', '', 'majendo', 'mwasiasa', 'consultation', 50000.00, 'cash', 'paid', 1, 0, 0, 0, 'consultation', 0, '', '', 35.0, '120/270', 71, 40.0, 160.0, NULL, '2025-09-11 15:18:49', '2025-09-11 15:18:49'),
(28, 'KJ20250002', 'new', 'patient', NULL, NULL, '09564535', '', 'new', 'new', 'consultation', 50000.00, 'cash', 'paid', 1, 0, 0, 0, 'consultation', 0, '', '', 37.0, '120/57', 71, 60.0, 150.0, NULL, '2025-09-11 20:57:14', '2025-09-11 20:57:14'),
(33, 'KJ20250004', 'sophia', 'tembo', '1998-03-03', 'female', '0754611194', '', '', NULL, 'consultation', NULL, NULL, 'pending', 1, 0, 0, 0, 'consultation_registration', 0, '', '', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-07 22:13:06', '2025-10-07 22:13:06'),
(34, 'KJ20250005', 'jackline', 'lwena', '2004-06-28', 'female', '0987653', '', '', NULL, 'consultation', NULL, NULL, 'pending', 1, 0, 0, 0, 'consultation_registration', 0, 'lwwena', '', 36.0, '120/80', 70, 80.0, 158.0, NULL, '2025-10-07 22:15:09', '2025-10-07 22:15:09');

-- --------------------------------------------------------

--
-- Table structure for table `patient_payments`
--

CREATE TABLE `patient_payments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `consultation_id` int(11) DEFAULT NULL,
  `payment_type` enum('consultation_fee','lab_test_fee','medicine_fee','other') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','mobile_money','insurance','credit') NOT NULL,
  `payment_status` enum('pending','paid','partial','refunded') DEFAULT 'pending',
  `payment_date` timestamp NULL DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL COMMENT 'Transaction/Receipt reference',
  `collected_by` int(11) DEFAULT NULL COMMENT 'Staff who collected payment',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Detailed payment tracking for all patient services';

--
-- Dumping data for table `patient_payments`
--

INSERT INTO `patient_payments` (`id`, `patient_id`, `consultation_id`, `payment_type`, `amount`, `payment_method`, `payment_status`, `payment_date`, `reference_number`, `collected_by`, `notes`, `created_at`, `updated_at`) VALUES
(7, 27, NULL, 'consultation_fee', 50000.00, 'cash', 'paid', '2025-09-11 15:18:49', NULL, 7, 'Consultation fee payment at registration', '2025-09-11 15:18:49', '2025-09-11 15:18:49'),
(8, 28, NULL, 'consultation_fee', 50000.00, 'cash', 'paid', '2025-09-11 20:57:14', NULL, 8, 'Consultation fee payment at registration', '2025-09-11 20:57:14', '2025-09-11 20:57:14'),
(9, 0, NULL, 'consultation_fee', 3000.00, 'cash', 'paid', '2025-10-07 20:48:06', NULL, 0, 'Initial consultation payment', '2025-10-07 20:48:06', '2025-10-07 22:10:29'),
(10, 33, NULL, 'consultation_fee', 3000.00, 'cash', 'paid', '2025-10-07 22:13:06', NULL, 0, 'Initial consultation payment', '2025-10-07 22:13:06', '2025-10-07 22:13:06'),
(11, 34, NULL, 'consultation_fee', 3000.00, 'cash', 'paid', '2025-10-07 22:15:09', NULL, 0, 'Initial consultation payment', '2025-10-07 22:15:09', '2025-10-07 22:15:09');

-- --------------------------------------------------------

--
-- Stand-in structure for view `patient_summary`
-- (See below for the actual view)
--
CREATE TABLE `patient_summary` (
`id` int(11)
,`registration_number` varchar(20)
,`full_name` varchar(101)
,`first_name` varchar(50)
,`last_name` varchar(50)
,`date_of_birth` date
,`age` bigint(21)
,`gender` enum('male','female','other')
,`phone` varchar(20)
,`email` varchar(100)
,`address` text
,`occupation` varchar(100)
,`emergency_contact_name` varchar(100)
,`emergency_contact_phone` varchar(20)
,`visit_type` enum('consultation','lab_test','medicine_pickup')
,`current_step` varchar(50)
,`consultation_registration_paid` tinyint(1)
,`lab_tests_paid` tinyint(1)
,`medicine_dispensed` tinyint(1)
,`medicine_prescribed` tinyint(1)
,`final_payment_collected` tinyint(1)
,`registration_date` timestamp
,`updated_at` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `patient_vital_signs`
--

CREATE TABLE `patient_vital_signs` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `consultation_id` int(11) DEFAULT NULL,
  `recorded_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `temperature` decimal(4,1) DEFAULT NULL COMMENT 'Temperature in Celsius',
  `blood_pressure_systolic` int(11) DEFAULT NULL COMMENT 'Systolic BP',
  `blood_pressure_diastolic` int(11) DEFAULT NULL COMMENT 'Diastolic BP',
  `blood_pressure_text` varchar(20) DEFAULT NULL COMMENT 'BP as text (e.g., 120/80)',
  `pulse_rate` int(11) DEFAULT NULL COMMENT 'Pulse rate in bpm',
  `body_weight` decimal(5,1) DEFAULT NULL COMMENT 'Weight in kg',
  `height` decimal(5,1) DEFAULT NULL COMMENT 'Height in cm',
  `bmi` decimal(4,1) DEFAULT NULL COMMENT 'Calculated BMI',
  `recorded_by` int(11) DEFAULT NULL COMMENT 'Staff member who recorded',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Vital signs tracking for each patient visit';

--
-- Dumping data for table `patient_vital_signs`
--

INSERT INTO `patient_vital_signs` (`id`, `patient_id`, `consultation_id`, `recorded_date`, `temperature`, `blood_pressure_systolic`, `blood_pressure_diastolic`, `blood_pressure_text`, `pulse_rate`, `body_weight`, `height`, `bmi`, `recorded_by`, `notes`) VALUES
(4, 27, NULL, '2025-09-11 15:18:49', 35.0, NULL, NULL, '120/270', 71, 40.0, 160.0, 15.6, 7, 'Initial registration vital signs'),
(5, 28, NULL, '2025-09-11 20:57:14', 37.0, NULL, NULL, '120/57', 71, 60.0, 150.0, 26.7, 8, 'Initial registration vital signs');

-- --------------------------------------------------------

--
-- Table structure for table `patient_workflow_status`
--

CREATE TABLE `patient_workflow_status` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `workflow_step` varchar(50) NOT NULL,
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL COMMENT 'Staff member assigned',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
`patient_id` int(11)
,`registration_number` varchar(20)
,`patient_name` varchar(101)
,`current_step` varchar(50)
,`completed_steps` bigint(21)
,`pending_steps` bigint(21)
,`active_steps` bigint(21)
,`registration_date` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `consultation_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','insurance','other') NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `step_payments`
--

CREATE TABLE `step_payments` (
  `id` int(11) NOT NULL,
  `workflow_id` int(11) NOT NULL,
  `step` enum('consultation_registration','lab_tests','results_review') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','insurance','mobile_money') NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_by` int(11) NOT NULL,
  `status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `step_payments`
--

INSERT INTO `step_payments` (`id`, `workflow_id`, `step`, `amount`, `payment_method`, `transaction_id`, `payment_date`, `processed_by`, `status`, `notes`) VALUES
(1, 0, 'consultation_registration', 3000.00, 'cash', NULL, '2025-10-05 16:10:28', 0, 'completed', NULL),
(2, 0, 'consultation_registration', 3000.00, 'cash', NULL, '2025-10-05 20:52:30', 0, 'completed', NULL),
(3, 0, 'consultation_registration', 3000.00, 'cash', NULL, '2025-10-06 16:59:56', 0, 'completed', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','receptionist','doctor','lab_technician') NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `email`, `role`, `first_name`, `last_name`, `phone`, `created_at`, `updated_at`, `is_active`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@hospital.com', 'admin', 'System', 'Administrator', '1234567890', '2025-08-31 14:09:11', '2025-08-31 14:09:11', 1),
(3, 'doctor1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor@hospital.com', 'doctor', 'Dr. John', 'Doe', '1234567892', '2025-08-31 14:09:11', '2025-08-31 14:09:11', 1),
(7, 'mapokezi1', '$2y$10$yQm1Cb2DN7luUaAJAJUH5O4WF/ZfXqu3DRhYQOXS37nvSaR8M/8uy', 'mapokezi1@gmail.com', 'receptionist', 'mapokezi', 'mapokezi', '0683274343', '2025-09-11 12:31:27', '2025-09-11 12:31:27', 1),
(8, 'mapokezi', '$2y$10$MkYf4RAMK2JkaAj8y3wRyeiztm5k3krAe591lLrReZaPYQvX8h2mS', 'mapokezi@gmail.com', 'receptionist', 'mapokezi', 'mapokezi', '077595533', '2025-09-11 20:54:11', '2025-09-11 20:54:11', 1),
(9, 'lab', '$2y$10$bU.2sa1ecWxXq66Mszosc.JRBHdprs5u5Ibd8FssS54iB.gfkDf9G', 'lab@gmail.com', 'lab_technician', 'lab', 'lab', '255000000000', '2025-10-05 15:20:48', '2025-10-07 22:12:23', 1),
(10, 'receptionist1', '$2y$10$LtPlyeviIPUiY5mwICPUke8vWd.GKF0vh.KzDuP6B9SjzLsv9IMKe', 'reception@gmail.com', 'receptionist', 'receptionist1', 'receptionist1', '0715915254', '2025-10-05 15:29:20', '2025-10-07 22:12:23', 1),
(11, 'maabara', '$2y$10$CMQEPrd2yjBllvNHtsQC7e0mE5iN1QigRPf8XVQOUpjaJN.RYOAPG', 'maabara@gmail.com', 'lab_technician', 'maabara', 'maabara', '068327434', '2025-10-08 16:25:37', '2025-10-08 16:25:37', 1);

-- --------------------------------------------------------

--
-- Table structure for table `workflow_status`
--

CREATE TABLE `workflow_status` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `current_step` enum('consultation_registration','lab_tests','results_review','medicine_dispensing','completed') DEFAULT 'consultation_registration',
  `consultation_registration_paid` tinyint(1) DEFAULT 0,
  `registration_paid` tinyint(1) DEFAULT 0,
  `consultation_paid` tinyint(1) DEFAULT 0,
  `lab_tests_paid` tinyint(1) DEFAULT 0,
  `results_review_paid` tinyint(1) DEFAULT 0,
  `lab_tests_required` tinyint(1) DEFAULT 0,
  `medicine_prescribed` tinyint(1) DEFAULT 0,
  `medicine_dispensed` tinyint(1) DEFAULT 0,
  `medicine_dispensed_by` int(11) DEFAULT NULL,
  `medicine_dispensed_at` timestamp NULL DEFAULT NULL,
  `final_payment_collected` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workflow_status`
--

INSERT INTO `workflow_status` (`id`, `patient_id`, `current_step`, `consultation_registration_paid`, `registration_paid`, `consultation_paid`, `lab_tests_paid`, `results_review_paid`, `lab_tests_required`, `medicine_prescribed`, `medicine_dispensed`, `medicine_dispensed_by`, `medicine_dispensed_at`, `final_payment_collected`, `created_at`, `updated_at`) VALUES
(12, 27, 'medicine_dispensing', 1, 1, 1, 0, 0, 0, 1, 0, NULL, NULL, 0, '2025-09-11 20:20:16', '2025-10-06 17:16:05'),
(13, 0, 'consultation_registration', 1, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, 0, '2025-10-05 16:10:28', '2025-10-07 22:01:58'),
(14, 0, 'consultation_registration', 1, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, 0, '2025-10-05 20:52:30', '2025-10-07 22:01:58'),
(15, 0, 'consultation_registration', 1, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, 0, '2025-10-06 16:59:56', '2025-10-07 22:01:58'),
(16, 28, 'consultation_registration', 1, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, 0, '2025-10-07 21:57:33', '2025-10-07 22:01:58'),
(17, 33, 'consultation_registration', 1, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, 0, '2025-10-07 22:13:06', '2025-10-07 22:19:22'),
(18, 34, 'medicine_dispensing', 1, 0, 0, 0, 0, 0, 1, 0, NULL, NULL, 0, '2025-10-07 22:15:09', '2025-10-08 16:54:32');

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

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `patient_workflow_summary`  AS SELECT `p`.`id` AS `patient_id`, `p`.`registration_number` AS `registration_number`, concat(`p`.`first_name`,' ',`p`.`last_name`) AS `patient_name`, `p`.`current_step` AS `current_step`, count(case when `pws`.`status` = 'completed' then 1 end) AS `completed_steps`, count(case when `pws`.`status` = 'pending' then 1 end) AS `pending_steps`, count(case when `pws`.`status` = 'in_progress' then 1 end) AS `active_steps`, `p`.`created_at` AS `registration_date` FROM (`patients` `p` left join `patient_workflow_status` `pws` on(`p`.`id` = `pws`.`patient_id`)) GROUP BY `p`.`id`, `p`.`registration_number`, concat(`p`.`first_name`,' ',`p`.`last_name`), `p`.`current_step`, `p`.`created_at` ;

--
-- Indexes for dumped tables
--

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
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient_payments`
--
ALTER TABLE `patient_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `step_payments`
--
ALTER TABLE `step_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `workflow_status`
--
ALTER TABLE `workflow_status`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `consultations`
--
ALTER TABLE `consultations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `patient_payments`
--
ALTER TABLE `patient_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `step_payments`
--
ALTER TABLE `step_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `workflow_status`
--
ALTER TABLE `workflow_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

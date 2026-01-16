-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 15, 2026 at 11:50 PM
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
`visit_id` int
,`visit_type` enum('consultation','lab_only','minor_service')
,`visit_date` date
,`patient_id` int
,`registration_number` varchar(20)
,`patient_name` varchar(101)
,`phone` varchar(20)
,`gender` enum('male','female','other')
,`age` bigint
,`temperature` decimal(4,1)
,`pulse_rate` int
,`blood_pressure_systolic` int
,`blood_pressure_diastolic` int
,`consultation_id` int
,`consultation_status` enum('pending','in_progress','completed','cancelled')
,`doctor_name` varchar(101)
,`registration_paid` decimal(32,2)
,`pending_lab_tests` bigint
,`completed_lab_tests` bigint
,`pending_prescriptions` bigint
,`partial_prescriptions` bigint
,`registration_time` timestamp
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
  `preliminary_diagnosis_id` int DEFAULT NULL COMMENT 'FK to icd_codes for preliminary diagnosis',
  `preliminary_diagnosis` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `final_diagnosis` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `final_diagnosis_id` int DEFAULT NULL COMMENT 'FK to icd_codes for final diagnosis',
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

INSERT INTO `consultations` (`id`, `visit_id`, `patient_id`, `doctor_id`, `consultation_number`, `consultation_type`, `main_complaint`, `history_of_present_illness`, `on_examination`, `diagnosis`, `preliminary_diagnosis_id`, `preliminary_diagnosis`, `final_diagnosis`, `final_diagnosis_id`, `treatment_plan`, `notes`, `follow_up_required`, `follow_up_date`, `follow_up_instructions`, `referred_to`, `referral_reason`, `status`, `cancellation_reason`, `started_at`, `completed_at`, `created_at`, `updated_at`) VALUES
(39, 51, 33, 9, 1, 'new', 'sawaa', NULL, 'sawa', NULL, NULL, 'sawa', 'sawa', NULL, 'sawa', NULL, 0, NULL, NULL, NULL, NULL, 'completed', NULL, '2026-01-08 16:04:53', '2026-01-08 16:04:53', '2026-01-08 16:02:17', '2026-01-08 16:04:53'),
(43, 59, 42, 9, 1, 'new', 'good', NULL, 'good', NULL, NULL, 'good', 'good', NULL, 'sawa', NULL, 0, NULL, NULL, NULL, NULL, 'completed', NULL, '2026-01-09 10:19:52', '2026-01-09 10:19:52', '2026-01-09 10:18:10', '2026-01-09 10:19:52'),
(44, 61, 44, 9, 1, 'new', 'fbajfuhjadf', NULL, 'ehjsvhbdfnd', NULL, NULL, 'fvhkdfbvfjkd', 'vhfvvsdjfdsnk', NULL, 'dgvjyusdjvcbhfuyjhdcyuvxjjk', NULL, 0, NULL, NULL, NULL, NULL, 'completed', NULL, '2026-01-11 19:44:42', '2026-01-11 19:44:42', '2026-01-11 19:40:50', '2026-01-11 19:44:42'),
(45, 62, 45, 1, 1, 'new', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'in_progress', NULL, '2026-01-14 20:56:01', NULL, '2026-01-12 13:26:16', '2026-01-14 20:56:17'),
(46, 63, 46, 9, 1, 'new', 'djhkfsd', NULL, 'sgdfsdjh', NULL, NULL, 'sd fndn', 'fdhjsdf', NULL, 'dvajhfdn', NULL, 0, NULL, NULL, NULL, NULL, 'completed', NULL, '2026-01-14 20:15:02', '2026-01-14 20:15:02', '2026-01-14 19:19:05', '2026-01-14 20:15:02'),
(47, 64, 47, 1, 1, 'new', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'in_progress', NULL, '2026-01-14 20:47:19', NULL, '2026-01-14 19:35:30', '2026-01-14 20:49:57'),
(48, 65, 48, 1, 1, 'new', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'in_progress', NULL, '2026-01-14 20:58:46', NULL, '2026-01-14 19:38:13', '2026-01-14 20:58:46'),
(49, 66, 49, 1, 1, 'new', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'in_progress', NULL, '2026-01-14 20:47:11', NULL, '2026-01-14 20:06:00', '2026-01-14 20:47:11'),
(50, 67, 50, 1, 1, 'new', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'in_progress', NULL, '2026-01-14 21:14:21', NULL, '2026-01-14 21:13:57', '2026-01-14 21:23:21'),
(51, 68, 51, 1, 1, 'new', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'in_progress', NULL, '2026-01-14 21:33:31', NULL, '2026-01-14 21:33:10', '2026-01-14 21:33:31'),
(52, 69, 52, 1, 1, 'new', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'in_progress', NULL, '2026-01-14 21:37:46', NULL, '2026-01-14 21:37:15', '2026-01-14 21:40:12'),
(53, 70, 53, 1, 1, 'new', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'in_progress', NULL, '2026-01-14 21:56:57', NULL, '2026-01-14 21:56:23', '2026-01-14 21:56:57'),
(54, 71, 54, 1, 1, 'new', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'in_progress', NULL, '2026-01-14 22:05:38', NULL, '2026-01-14 22:05:11', '2026-01-14 22:05:38'),
(55, 72, 55, 1, 1, 'new', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'in_progress', NULL, '2026-01-14 22:17:31', NULL, '2026-01-14 22:17:00', '2026-01-14 22:17:31'),
(56, 73, 56, 1, 1, 'new', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'in_progress', NULL, '2026-01-15 13:47:26', NULL, '2026-01-15 13:46:33', '2026-01-15 14:26:10'),
(57, 74, 57, 1, 1, 'new', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'in_progress', NULL, '2026-01-15 23:17:58', NULL, '2026-01-15 23:17:16', '2026-01-15 23:44:46');

-- --------------------------------------------------------

--
-- Stand-in structure for view `daily_revenue_summary`
-- (See below for the actual view)
--
CREATE TABLE `daily_revenue_summary` (
`revenue_date` date
,`payment_type` enum('registration','consultation','lab_test','medicine','minor_service','service')
,`payment_method` enum('cash','card','mobile_money','insurance')
,`transaction_count` bigint
,`total_amount` decimal(32,2)
,`collected_by_name` varchar(101)
);

-- --------------------------------------------------------

--
-- Table structure for table `icd_codes`
--

CREATE TABLE `icd_codes` (
  `id` int NOT NULL,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'ICD-10 code (e.g., B50, A09)',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Diagnosis name',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT 'Detailed description',
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Disease category',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `icd_codes`
--

INSERT INTO `icd_codes` (`id`, `code`, `name`, `description`, `category`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'B50', 'Plasmodium falciparum malaria', 'Malaria due to Plasmodium falciparum', 'Parasitic Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(2, 'B51', 'Plasmodium vivax malaria', 'Malaria due to Plasmodium vivax', 'Parasitic Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(3, 'B52', 'Plasmodium malariae malaria', 'Malaria due to Plasmodium malariae', 'Parasitic Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(4, 'B53', 'Other parasitologically confirmed malaria', 'Other specified malaria with parasitological confirmation', 'Parasitic Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(5, 'B54', 'Unspecified malaria', 'Malaria, unspecified', 'Parasitic Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(6, 'J00', 'Acute nasopharyngitis (common cold)', 'Common cold', 'Respiratory Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(7, 'J01', 'Acute sinusitis', 'Acute sinusitis', 'Respiratory Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(8, 'J02', 'Acute pharyngitis', 'Acute sore throat', 'Respiratory Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(9, 'J03', 'Acute tonsillitis', 'Acute tonsillitis', 'Respiratory Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(10, 'J06', 'Acute upper respiratory infection', 'Upper respiratory tract infection (URTI)', 'Respiratory Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(11, 'J18', 'Pneumonia, unspecified organism', 'Pneumonia', 'Respiratory Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(12, 'J20', 'Acute bronchitis', 'Acute bronchitis', 'Respiratory Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(13, 'J21', 'Acute bronchiolitis', 'Acute bronchiolitis', 'Respiratory Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(14, 'J45', 'Asthma', 'Asthma', 'Respiratory Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(15, 'A00', 'Cholera', 'Cholera', 'Infectious Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(16, 'A01', 'Typhoid and paratyphoid fevers', 'Typhoid fever', 'Infectious Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(17, 'A02', 'Other salmonella infections', 'Salmonellosis', 'Infectious Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(18, 'A03', 'Shigellosis', 'Dysentery', 'Infectious Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(19, 'A04', 'Other bacterial intestinal infections', 'Bacterial diarrhea', 'Infectious Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(20, 'A06', 'Amoebiasis', 'Amoebic dysentery', 'Parasitic Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(21, 'A07', 'Other protozoal intestinal diseases', 'Giardiasis and other protozoal diarrhea', 'Parasitic Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(22, 'A08', 'Viral and other specified intestinal infections', 'Viral gastroenteritis', 'Infectious Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(23, 'A09', 'Diarrhea and gastroenteritis', 'Diarrhea, unspecified', 'Infectious Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(24, 'K29', 'Gastritis and duodenitis', 'Gastritis', 'Digestive Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(25, 'K30', 'Functional dyspepsia', 'Indigestion', 'Digestive Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(26, 'K59.1', 'Functional diarrhea', 'Functional diarrhea', 'Digestive Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(27, 'B20', 'HIV disease', 'HIV disease resulting in infectious and parasitic diseases', 'Infectious Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(28, 'B24', 'Unspecified HIV disease', 'HIV disease without specification', 'Infectious Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(29, 'A15', 'Respiratory tuberculosis', 'Pulmonary tuberculosis', 'Infectious Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(30, 'A16', 'Respiratory tuberculosis, not confirmed', 'Pulmonary tuberculosis, not bacteriologically or histologically confirmed', 'Infectious Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(31, 'A17', 'Tuberculosis of nervous system', 'TB meningitis', 'Infectious Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(32, 'A18', 'Tuberculosis of other organs', 'Extrapulmonary tuberculosis', 'Infectious Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(33, 'A19', 'Miliary tuberculosis', 'Disseminated TB', 'Infectious Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(34, 'B35', 'Dermatophytosis', 'Fungal skin infection (Ringworm)', 'Skin Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(35, 'L20', 'Atopic dermatitis', 'Eczema', 'Skin Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(36, 'L30', 'Other dermatitis', 'Dermatitis, unspecified', 'Skin Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(37, 'L08', 'Other local infections of skin', 'Skin infection (Pyoderma)', 'Skin Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(38, 'B86', 'Scabies', 'Scabies', 'Parasitic Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(39, 'N30', 'Cystitis', 'Bladder infection (Cystitis)', 'Genitourinary Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(40, 'N39.0', 'Urinary tract infection', 'UTI, site not specified', 'Genitourinary Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(41, 'N10', 'Acute pyelonephritis', 'Kidney infection', 'Genitourinary Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(42, 'I10', 'Essential (primary) hypertension', 'High blood pressure', 'Cardiovascular Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(43, 'I11', 'Hypertensive heart disease', 'Hypertensive heart disease', 'Cardiovascular Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(44, 'I25', 'Chronic ischaemic heart disease', 'Ischemic heart disease', 'Cardiovascular Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(45, 'E11', 'Type 2 diabetes mellitus', 'Type 2 diabetes', 'Endocrine Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(46, 'E10', 'Type 1 diabetes mellitus', 'Type 1 diabetes', 'Endocrine Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(47, 'R50', 'Fever of unknown origin', 'Fever, unspecified', 'Symptoms and Signs', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(48, 'R51', 'Headache', 'Headache', 'Symptoms and Signs', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(49, 'R10', 'Abdominal and pelvic pain', 'Abdominal pain', 'Symptoms and Signs', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(50, 'M25.5', 'Pain in joint', 'Joint pain (Arthralgia)', 'Musculoskeletal Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(51, 'M79.1', 'Myalgia', 'Muscle pain', 'Musculoskeletal Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(52, 'B76', 'Hookworm disease', 'Hookworm infection', 'Parasitic Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(53, 'B77', 'Ascariasis', 'Roundworm infection', 'Parasitic Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(54, 'B65', 'Schistosomiasis', 'Bilharzia', 'Parasitic Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(55, 'H10', 'Conjunctivitis', 'Pink eye (Conjunctivitis)', 'Eye Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(56, 'H66', 'Suppurative and unspecified otitis media', 'Ear infection (Otitis media)', 'Ear Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(57, 'D50', 'Iron deficiency anaemia', 'Iron deficiency anemia', 'Blood Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(58, 'D64.9', 'Anaemia, unspecified', 'Anemia, unspecified', 'Blood Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(59, 'O26', 'Maternal care for other conditions', 'Pregnancy complications', 'Pregnancy Conditions', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(60, 'Z34', 'Supervision of normal pregnancy', 'Antenatal care (Normal pregnancy)', 'Pregnancy Conditions', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(61, 'A54', 'Gonococcal infection', 'Gonorrhea', 'Infectious Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(62, 'A56', 'Other sexually transmitted chlamydial diseases', 'Chlamydia', 'Infectious Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07'),
(63, 'A60', 'Anogenital herpesviral infection', 'Genital herpes', 'Infectious Diseases', 1, '2026-01-15 14:21:07', '2026-01-15 14:21:07');

-- --------------------------------------------------------

--
-- Table structure for table `lab_equipment`
--

CREATE TABLE `lab_equipment` (
  `id` int NOT NULL,
  `equipment_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `equipment_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `model` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `serial_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `manufacturer` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `warranty_expiry` date DEFAULT NULL,
  `last_calibration` date DEFAULT NULL,
  `next_calibration` date DEFAULT NULL,
  `calibration_interval_months` int DEFAULT '12',
  `status` enum('operational','maintenance','out_of_service','calibration_due') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'operational',
  `location` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `managed_by` int DEFAULT NULL COMMENT 'Lab tech responsible',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_equipment`
--

INSERT INTO `lab_equipment` (`id`, `equipment_name`, `equipment_code`, `model`, `serial_number`, `manufacturer`, `purchase_date`, `warranty_expiry`, `last_calibration`, `next_calibration`, `calibration_interval_months`, `status`, `location`, `managed_by`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Microscope', 'MICRO-001', 'Olympus CX23', 'OLY2024001', 'Olympus', '2024-01-15', NULL, NULL, NULL, 12, 'operational', 'Lab Room 1', NULL, NULL, 1, '2025-10-23 22:19:40', '2025-10-23 22:19:40'),
(2, 'Chemistry Analyzer', 'CHEM-001', 'Mindray BS-200', 'MDR2024001', 'Mindray', '2024-02-20', NULL, NULL, NULL, 12, 'operational', 'Lab Room 2', NULL, NULL, 1, '2025-10-23 22:19:40', '2025-10-23 22:19:40'),
(3, 'Hematology Analyzer', 'HEMA-001', 'Sysmex XP-300', 'SYS2024001', 'Sysmex', '2024-03-10', NULL, NULL, NULL, 12, 'maintenance', 'Lab Room 1', NULL, NULL, 1, '2025-10-23 22:19:40', '2025-10-23 22:19:40'),
(4, 'Centrifuge', 'CENT-001', 'Eppendorf 5810R', 'EPP2024001', 'Eppendorf', '2024-01-25', NULL, NULL, NULL, 12, 'operational', 'Lab Room 2', NULL, NULL, 1, '2025-10-23 22:19:40', '2025-10-23 22:19:40'),
(5, 'Incubator', 'INCUB-001', 'Thermo Fisher 3110', 'THM2024001', 'Thermo Fisher', '2024-04-05', NULL, NULL, NULL, 12, 'operational', 'Lab Room 3', NULL, NULL, 1, '2025-10-23 22:19:40', '2025-10-23 22:19:40'),
(6, 'Autoclave', 'AUTO-001', 'Tuttnauer 2540M', 'TUT2024001', 'Tuttnauer', '2024-05-12', NULL, NULL, NULL, 12, 'calibration_due', 'Sterilization Room', NULL, NULL, 1, '2025-10-23 22:19:40', '2025-10-23 22:19:40');

-- --------------------------------------------------------

--
-- Table structure for table `lab_inventory`
--

CREATE TABLE `lab_inventory` (
  `id` int NOT NULL,
  `item_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `item_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `category` enum('reagent','consumable','supply','equipment') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'consumable',
  `unit` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `current_stock` int DEFAULT '0',
  `minimum_stock` int DEFAULT '10',
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `supplier` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `location` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_inventory`
--

INSERT INTO `lab_inventory` (`id`, `item_name`, `item_code`, `category`, `unit`, `current_stock`, `minimum_stock`, `unit_cost`, `supplier`, `expiry_date`, `location`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Blood Sugar Reagent Strips', 'BSR-001', 'reagent', 'strips', 500, 100, 25.00, 'MedLab Supplies', NULL, NULL, 1, '2025-10-23 22:19:40', '2025-10-23 22:19:40'),
(2, 'Hemoglobin Reagent', 'HGB-001', 'reagent', 'bottles', 50, 10, 150.00, 'LabChem Corp', NULL, NULL, 1, '2025-10-23 22:19:40', '2025-10-23 22:19:40'),
(3, 'Cholesterol Test Kit', 'CHOL-001', 'reagent', 'kits', 30, 5, 200.00, 'BioTest Labs', NULL, NULL, 1, '2025-10-23 22:19:40', '2025-10-23 22:19:40'),
(4, 'Protein Reagent', 'PROT-001', 'reagent', 'bottles', 40, 8, 120.00, 'MedLab Supplies', NULL, NULL, 1, '2025-10-23 22:19:40', '2025-10-23 22:19:40'),
(5, 'Gloves (Nitrile)', 'GLOVE-001', 'supply', 'pairs', 1000, 200, 5.00, 'MediSupplies', NULL, NULL, 1, '2025-10-23 22:19:40', '2025-10-23 22:19:40'),
(6, 'Test Tubes (10ml)', 'TUBE-001', 'consumable', 'pieces', 500, 100, 2.00, 'LabWare Inc', NULL, NULL, 1, '2025-10-23 22:19:40', '2025-10-23 22:19:40'),
(7, 'Microscope Slides', 'SLIDE-001', 'consumable', 'pieces', 1000, 200, 1.50, 'LabWare Inc', NULL, NULL, 1, '2025-10-23 22:19:40', '2025-10-23 22:19:40'),
(8, 'Blood Collection Tubes', 'BCT-001', 'consumable', 'pieces', 300, 50, 8.00, 'MediSupplies', NULL, NULL, 1, '2025-10-23 22:19:40', '2025-10-23 22:19:40');

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
(13, 18, 33, 4, '1.2', 'ulk', 'mg/dl', 0, 0, NULL, 10, NULL, '2026-01-08 13:07:00', NULL, NULL, NULL),
(14, 19, 37, 4, '30%', 'dgajhfjadhajf', '30%', 1, 0, NULL, 10, NULL, '2026-01-09 05:36:00', NULL, NULL, NULL),
(15, 20, 42, 20, '1.0', 'Test completed successfully.', 'mg/dL', 1, 0, NULL, 10, NULL, '2026-01-09 07:38:00', NULL, NULL, NULL),
(16, 21, 43, 20, '1.0', 'Test completed successfully.', 'mg/dL', 1, 0, NULL, 10, NULL, '2026-01-09 07:53:00', NULL, NULL, NULL),
(17, 22, 44, 4, '1', 'dgshyrbhcusyfhj', 'mg/dL', 0, 0, NULL, 10, NULL, '2026-01-11 16:52:00', NULL, NULL, NULL),
(18, 23, 46, 20, '1.5', 'dbsmsn', 'IU/mL', 1, 0, NULL, 10, NULL, '2026-01-14 17:19:00', NULL, NULL, NULL);

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
(4, 'Malaria Test', 'MAL', 3, 5000.00, 'Negative', '', 'Malaria parasite detection', NULL, 1, '2025-10-11 03:12:35', '2025-10-23 22:51:56'),
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
(15, 'X-Ray Chest', 'XRAY-C', 1, 30000.00, 'Normal', '', 'Chest radiography', NULL, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(16, 'H. Pylori Antigen Test', 'HPYL-AG', 4, 12000.00, 'Negative', '', 'H. Pylori antigen detection', NULL, 1, '2025-11-04 13:43:11', '2025-11-04 13:43:11'),
(17, 'H. Pylori Antibody Test', 'HPYL-AB', 4, 12000.00, 'Negative', '', 'H. Pylori antibody detection', NULL, 1, '2025-11-04 13:43:11', '2025-11-04 13:43:11'),
(18, 'Syphilis Test (RPR)', 'RPR', 4, 10000.00, 'Non-reactive', '', 'Syphilis screening test', NULL, 1, '2025-11-04 13:43:11', '2025-11-04 13:43:11'),
(19, 'Typhoid Antigen Test', 'TYPH-AG', 4, 15000.00, 'Negative', '', 'Salmonella typhi/paratyphi antigen', NULL, 1, '2025-11-04 13:43:11', '2025-11-04 13:43:11'),
(20, 'Rheumatoid Factor', 'RF', 4, 8000.00, '<14 IU/mL', 'IU/mL', 'Rheumatoid arthritis screening', NULL, 1, '2025-11-04 13:43:11', '2025-11-04 13:43:11'),
(21, 'Blood Uric Acid', 'UA', 2, 8000.00, 'Male: 3.4-7.0, Female: 2.4-6.0', 'mg/dL', 'Uric acid level test', NULL, 1, '2025-11-04 13:43:13', '2025-11-04 13:43:13'),
(22, 'Hemoglobin Test', 'HB', 1, 5000.00, 'Male: 13.5-17.5, Female: 12.0-15.5', 'g/dL', 'Hemoglobin level', NULL, 1, '2025-11-04 13:43:13', '2025-11-04 13:43:13');

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
-- Table structure for table `lab_test_items`
--

CREATE TABLE `lab_test_items` (
  `id` int NOT NULL,
  `test_id` int NOT NULL,
  `item_id` int NOT NULL,
  `quantity_required` decimal(8,2) NOT NULL DEFAULT '1.00',
  `is_mandatory` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_test_items`
--

INSERT INTO `lab_test_items` (`id`, `test_id`, `item_id`, `quantity_required`, `is_mandatory`, `created_at`) VALUES
(1, 2, 1, 1.00, 1, '2025-10-23 22:19:40'),
(2, 3, 1, 1.00, 1, '2025-10-23 22:19:40'),
(3, 7, 2, 2.00, 1, '2025-10-23 22:19:40'),
(4, 7, 4, 1.00, 1, '2025-10-23 22:19:40'),
(5, 8, 3, 1.00, 1, '2025-10-23 22:19:40'),
(6, 8, 4, 1.00, 1, '2025-10-23 22:19:40');

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
(18, 51, 33, 39, 4, 9, NULL, 'normal', 'completed', NULL, NULL, NULL, NULL, '2026-01-08 16:04:53', '2026-01-08 16:08:17'),
(19, 54, 37, NULL, 4, 8, NULL, 'normal', 'completed', NULL, NULL, NULL, NULL, '2026-01-09 08:33:12', '2026-01-09 08:37:11'),
(20, 59, 42, 43, 20, 9, 10, 'normal', 'completed', NULL, NULL, NULL, NULL, '2026-01-09 10:19:52', '2026-01-09 10:41:46'),
(21, 60, 43, NULL, 20, 8, NULL, 'normal', 'completed', NULL, NULL, NULL, NULL, '2026-01-09 10:50:59', '2026-01-09 10:53:40'),
(22, 61, 44, 44, 4, 9, 10, 'normal', 'completed', NULL, NULL, NULL, NULL, '2026-01-11 19:44:42', '2026-01-11 19:53:37'),
(23, 63, 46, 46, 20, 9, 10, 'normal', 'completed', NULL, NULL, NULL, NULL, '2026-01-14 20:15:02', '2026-01-14 20:19:22');

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
(15, 'Diclofenac', 'Diclofenac', 'Pain and inflammation relief', '50mg', 'tablets', 120.00, 300, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(16, 'Paracetamol', 'Acetaminophen', 'Analgesic and antipyretic for mild to moderate pain and fever', '500mg', 'tablets', 50.00, 1000, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(17, 'Paracetamol Syrup', 'Acetaminophen', 'Paediatric analgesic and antipyretic', '125mg/5ml', 'ml', 3000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(18, 'Paracetamol IV', 'Acetaminophen', 'Injectable analgesic for post-operative pain', '1g/100ml', 'vial', 15000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(19, 'Ibuprofen', 'Ibuprofen', 'NSAID for pain, inflammation and fever', '400mg', 'tablets', 100.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(20, 'Ibuprofen Suspension', 'Ibuprofen', 'Paediatric NSAID suspension', '100mg/5ml', 'ml', 5000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(21, 'Diclofenac', 'Diclofenac Sodium', 'NSAID for pain and inflammation', '50mg', 'tablets', 120.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(22, 'Acetylsalicylic Acid', 'Aspirin', 'Analgesic, antipyretic and antiplatelet', '75mg', 'tablets', 80.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(23, 'Tramadol', 'Tramadol HCl', 'Opioid analgesic for moderate to severe pain', '50mg', 'capsules', 300.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(24, 'Morphine Sulphate', 'Morphine', 'Opioid for severe pain management', '10mg/ml', 'ampoule', 5000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(25, 'Pethidine', 'Pethidine HCl', 'Opioid analgesic for labour and post-operative pain', '50mg/ml', 'ampoule', 3000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(26, 'Artemether-Lumefantrine (AL)', 'Artemether + Lumefantrine', 'First-line antimalarial for uncomplicated malaria', '20mg/120mg', 'tablets', 500.00, 1000, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(27, 'Quinine Injection', 'Quinine Dihydrochloride', 'Injectable for severe malaria', '300mg/ml', 'ampoule', 2000.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(28, 'Quinine Tablets', 'Quinine Sulphate', 'Oral treatment for uncomplicated malaria', '300mg', 'tablets', 150.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(29, 'Artesunate Injection', 'Artesunate', 'Parenteral treatment for severe malaria', '60mg', 'vial', 8000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(30, 'Sulfadoxine-Pyrimethamine', 'SP', 'Intermittent preventive treatment in pregnancy (IPTp)', '500mg/25mg', 'tablets', 200.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(31, 'Amoxicillin', 'Amoxicillin', 'Beta-lactam antibiotic for respiratory and soft tissue infections', '500mg', 'capsules', 200.00, 1000, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(32, 'Amoxicillin Suspension', 'Amoxicillin', 'Paediatric antibiotic suspension', '125mg/5ml', 'ml', 5000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(33, 'Amoxicillin-Clavulanic Acid', 'Co-amoxiclav', 'Beta-lactamase resistant antibiotic', '625mg', 'tablets', 500.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(34, 'Benzylpenicillin', 'Penicillin G', 'Injectable antibiotic for serious bacterial infections', '5MU', 'vial', 2000.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(35, 'Procaine Benzylpenicillin', 'Procaine Penicillin', 'Long-acting penicillin IM injection', '3MU', 'vial', 2500.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(36, 'Benzathine Benzylpenicillin', 'Benzathine Penicillin', 'Long-acting penicillin for syphilis and rheumatic fever prophylaxis', '2.4MU', 'vial', 3000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(37, 'Cloxacillin', 'Cloxacillin Sodium', 'Beta-lactamase resistant antibiotic for staphylococcal infections', '500mg', 'capsules', 300.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(38, 'Phenoxymethylpenicillin', 'Penicillin V', 'Oral penicillin for streptococcal infections', '250mg', 'tablets', 150.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(39, 'Erythromycin', 'Erythromycin', 'Macrolide antibiotic alternative to penicillin', '250mg', 'tablets', 200.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(40, 'Azithromycin', 'Azithromycin', 'Macrolide for respiratory and STI infections', '500mg', 'tablets', 500.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(41, 'Doxycycline', 'Doxycycline Hyclate', 'Tetracycline for various bacterial infections and malaria prophylaxis', '100mg', 'capsules', 150.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(42, 'Tetracycline Eye Ointment', 'Tetracycline', 'Topical antibiotic for eye infections', '1%', 'tube', 2000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(43, 'Metronidazole', 'Metronidazole', 'Antibiotic for anaerobic bacteria and protozoal infections', '400mg', 'tablets', 150.00, 1000, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(44, 'Metronidazole IV', 'Metronidazole', 'Injectable for severe anaerobic infections', '500mg/100ml', 'vial', 3000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(45, 'Cotrimoxazole', 'Sulfamethoxazole + Trimethoprim', 'Antibiotic for UTI, pneumonia and HIV prophylaxis (CPT)', '480mg', 'tablets', 100.00, 1000, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(46, 'Cotrimoxazole Suspension', 'Sulfamethoxazole + Trimethoprim', 'Paediatric suspension', '240mg/5ml', 'ml', 3000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(47, 'Nitrofurantoin', 'Nitrofurantoin', 'Antibiotic for urinary tract infections', '100mg', 'capsules', 200.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(48, 'Ceftriaxone', 'Ceftriaxone Sodium', 'Third generation cephalosporin for serious infections', '1g', 'vial', 3000.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(49, 'Cefotaxime', 'Cefotaxime Sodium', 'Third generation cephalosporin', '1g', 'vial', 3500.00, 150, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(50, 'Ceftazidime', 'Ceftazidime', 'Third generation cephalosporin for pseudomonas', '1g', 'vial', 4000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(51, 'Cefixime', 'Cefixime', 'Oral third generation cephalosporin', '400mg', 'tablets', 600.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(52, 'Ciprofloxacin', 'Ciprofloxacin HCl', 'Fluoroquinolone for various bacterial infections', '500mg', 'tablets', 300.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(53, 'Ciprofloxacin IV', 'Ciprofloxacin', 'Injectable fluoroquinolone', '200mg/100ml', 'vial', 5000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(54, 'Levofloxacin', 'Levofloxacin', 'Fluoroquinolone for respiratory and urinary infections', '500mg', 'tablets', 400.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(55, 'Gentamicin', 'Gentamicin Sulphate', 'Aminoglycoside for serious gram-negative infections', '80mg/2ml', 'ampoule', 2000.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(56, 'Clarithromycin', 'Clarithromycin', 'Macrolide for H.pylori and respiratory infections', '500mg', 'tablets', 600.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(57, 'Meropenem', 'Meropenem', 'Carbapenem for resistant gram-negative infections', '1g', 'vial', 15000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(58, 'Imipenem-Cilastatin', 'Imipenem + Cilastatin', 'Carbapenem antibiotic', '500mg', 'vial', 18000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(59, 'Vancomycin', 'Vancomycin HCl', 'Glycopeptide for MRSA and resistant gram-positive infections', '500mg', 'vial', 20000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(60, 'Linezolid', 'Linezolid', 'Oxazolidinone for VRE and MRSA', '600mg', 'tablets', 8000.00, 30, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(61, 'Rifampicin + Isoniazid + Pyrazinamide + Ethambutol', 'RHZE FDC', 'Fixed dose combination for TB intensive phase', '150/75/400/275mg', 'tablets', 300.00, 1000, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(62, 'Rifampicin + Isoniazid', 'RH FDC', 'Fixed dose combination for TB continuation phase', '150/75mg', 'tablets', 150.00, 1000, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(63, 'Isoniazid', 'Isoniazid (INH)', 'First-line anti-TB agent', '300mg', 'tablets', 100.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(64, 'Rifampicin', 'Rifampicin', 'First-line anti-TB agent', '150mg', 'capsules', 200.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(65, 'Pyrazinamide', 'Pyrazinamide', 'First-line anti-TB agent', '400mg', 'tablets', 150.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(66, 'Ethambutol', 'Ethambutol HCl', 'First-line anti-TB agent', '400mg', 'tablets', 120.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(67, 'Streptomycin', 'Streptomycin Sulphate', 'Injectable anti-TB agent', '1g', 'vial', 2500.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(68, 'Tenofovir + Lamivudine + Dolutegravir', 'TLD FDC', 'First-line ART fixed dose combination', '300/300/50mg', 'tablets', 200.00, 2000, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(69, 'Tenofovir + Lamivudine + Efavirenz', 'TLE FDC', 'Alternative first-line ART', '300/300/600mg', 'tablets', 180.00, 1000, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(70, 'Abacavir + Lamivudine', 'ABC/3TC', 'NRTI backbone for children and adults', '600/300mg', 'tablets', 250.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(71, 'Zidovudine + Lamivudine + Nevirapine', 'AZT/3TC/NVP', 'Paediatric FDC', '60/30/50mg', 'tablets', 100.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(72, 'Lopinavir + Ritonavir', 'LPV/r', 'Second-line protease inhibitor', '200/50mg', 'tablets', 300.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(73, 'Atazanavir + Ritonavir', 'ATV/r', 'Second-line protease inhibitor', '300/100mg', 'tablets', 350.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(74, 'Darunavir', 'Darunavir', 'Third-line protease inhibitor', '600mg', 'tablets', 500.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(75, 'Raltegravir', 'Raltegravir', 'Integrase inhibitor', '400mg', 'tablets', 600.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(76, 'Fluconazole', 'Fluconazole', 'Antifungal for candidiasis and cryptococcal meningitis', '200mg', 'capsules', 500.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(77, 'Fluconazole IV', 'Fluconazole', 'Injectable antifungal', '200mg/100ml', 'vial', 8000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(78, 'Nystatin Suspension', 'Nystatin', 'Topical antifungal for oral candidiasis', '100,000IU/ml', 'ml', 3000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(79, 'Clotrimazole Cream', 'Clotrimazole', 'Topical antifungal', '1%', 'tube', 2000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(80, 'Miconazole Oral Gel', 'Miconazole', 'Oral antifungal gel', '2%', 'tube', 3000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(81, 'Griseofulvin', 'Griseofulvin', 'Oral antifungal for dermatophyte infections', '500mg', 'tablets', 300.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(82, 'Acyclovir', 'Acyclovir', 'Antiviral for herpes infections', '200mg', 'tablets', 300.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(83, 'Acyclovir IV', 'Acyclovir Sodium', 'Injectable antiviral for severe herpes infections', '250mg', 'vial', 8000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(84, 'Oseltamivir', 'Oseltamivir Phosphate', 'Antiviral for influenza', '75mg', 'capsules', 1500.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(85, 'Albendazole', 'Albendazole', 'Broad spectrum anthelmintic', '400mg', 'tablets', 150.00, 1000, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(86, 'Mebendazole', 'Mebendazole', 'Anthelmintic for intestinal worms', '500mg', 'tablets', 120.00, 1000, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(87, 'Praziquantel', 'Praziquantel', 'Treatment for schistosomiasis and tapeworms', '600mg', 'tablets', 300.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(88, 'Ivermectin', 'Ivermectin', 'Antiparasitic for onchocerciasis and strongyloidiasis', '3mg', 'tablets', 200.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(89, 'Permethrin Lotion', 'Permethrin', 'Topical treatment for scabies', '5%', 'lotion', 5000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(90, 'Benzyl Benzoate', 'Benzyl Benzoate', 'Topical treatment for scabies', '25%', 'lotion', 3000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(91, 'Amlodipine', 'Amlodipine Besylate', 'Calcium channel blocker for hypertension', '5mg', 'tablets', 150.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(92, 'Nifedipine', 'Nifedipine', 'Calcium channel blocker for hypertension and angina', '20mg', 'tablets', 120.00, 400, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(93, 'Atenolol', 'Atenolol', 'Beta-blocker for hypertension and angina', '50mg', 'tablets', 100.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(94, 'Propranolol', 'Propranolol HCl', 'Non-selective beta-blocker', '40mg', 'tablets', 80.00, 400, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(95, 'Enalapril', 'Enalapril Maleate', 'ACE inhibitor for hypertension and heart failure', '5mg', 'tablets', 120.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(96, 'Lisinopril', 'Lisinopril', 'ACE inhibitor for hypertension', '10mg', 'tablets', 150.00, 400, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(97, 'Losartan', 'Losartan Potassium', 'Angiotensin receptor blocker', '50mg', 'tablets', 200.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(98, 'Hydrochlorothiazide', 'Hydrochlorothiazide', 'Thiazide diuretic for hypertension', '25mg', 'tablets', 80.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(99, 'Furosemide', 'Furosemide', 'Loop diuretic for edema and heart failure', '40mg', 'tablets', 100.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(100, 'Furosemide Injection', 'Furosemide', 'Injectable diuretic', '20mg/2ml', 'ampoule', 1500.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(101, 'Spironolactone', 'Spironolactone', 'Potassium-sparing diuretic', '25mg', 'tablets', 150.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(102, 'Atorvastatin', 'Atorvastatin Calcium', 'Statin for hyperlipidemia', '20mg', 'tablets', 250.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(103, 'Simvastatin', 'Simvastatin', 'Statin for cholesterol reduction', '20mg', 'tablets', 200.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(104, 'Isosorbide Dinitrate', 'Isosorbide Dinitrate', 'Nitrate for angina prophylaxis', '5mg', 'tablets', 120.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(105, 'Glyceryl Trinitrate', 'GTN', 'Sublingual nitrate for acute angina', '500mcg', 'tablets', 200.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(106, 'Digoxin', 'Digoxin', 'Cardiac glycoside for heart failure and atrial fibrillation', '250mcg', 'tablets', 100.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(107, 'Methyldopa', 'Methyldopa', 'Antihypertensive safe in pregnancy', '250mg', 'tablets', 120.00, 400, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(108, 'Hydralazine', 'Hydralazine HCl', 'Vasodilator for hypertension', '25mg', 'tablets', 150.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(109, 'Adrenaline', 'Epinephrine', 'Emergency medicine for cardiac arrest and anaphylaxis', '1mg/ml', 'ampoule', 2000.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(110, 'Atropine', 'Atropine Sulphate', 'Anticholinergic for bradycardia', '1mg/ml', 'ampoule', 1500.00, 150, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(111, 'Dextrose 50%', 'Dextrose', 'Hypertonic glucose for hypoglycemia', '50%', 'ampoule', 1000.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(112, 'Sodium Bicarbonate 8.4%', 'Sodium Bicarbonate', 'Alkalinizing agent for metabolic acidosis', '8.4%', 'ampoule', 1500.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(113, 'Calcium Gluconate', 'Calcium Gluconate', 'Calcium supplement for hypocalcemia', '10%', 'ampoule', 2000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(114, 'Naloxone', 'Naloxone HCl', 'Opioid antagonist for overdose reversal', '400mcg/ml', 'ampoule', 5000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(115, 'Hydrocortisone', 'Hydrocortisone Sodium Succinate', 'Corticosteroid for acute adrenal insufficiency', '100mg', 'vial', 3000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(116, 'Phenytoin', 'Phenytoin Sodium', 'Anticonvulsant for seizures', '100mg', 'capsules', 150.00, 400, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(117, 'Phenobarbitone', 'Phenobarbital', 'Anticonvulsant and sedative', '30mg', 'tablets', 80.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(118, 'Carbamazepine', 'Carbamazepine', 'Anticonvulsant for epilepsy and neuropathic pain', '200mg', 'tablets', 150.00, 400, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(119, 'Sodium Valproate', 'Valproic Acid', 'Broad-spectrum anticonvulsant', '200mg', 'tablets', 200.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(120, 'Diazepam', 'Diazepam', 'Benzodiazepine for status epilepticus', '10mg/2ml', 'ampoule', 1500.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(121, 'Diazepam Rectal', 'Diazepam', 'Rectal solution for emergency seizures', '5mg/2.5ml', 'tube', 3000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(122, 'Metformin', 'Metformin HCl', 'First-line oral hypoglycemic for Type 2 diabetes', '500mg', 'tablets', 100.00, 1000, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(123, 'Glibenclamide', 'Glibenclamide', 'Sulfonylurea for Type 2 diabetes', '5mg', 'tablets', 80.00, 800, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(124, 'Insulin Soluble (Regular)', 'Human Insulin', 'Short-acting insulin', '100IU/ml', 'vial', 15000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(125, 'Insulin Isophane (NPH)', 'Isophane Insulin', 'Intermediate-acting insulin', '100IU/ml', 'vial', 15000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(126, 'Insulin 30/70', 'Biphasic Insulin', 'Mixed insulin 30% regular, 70% NPH', '100IU/ml', 'vial', 16000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(127, 'Omeprazole', 'Omeprazole', 'Proton pump inhibitor for peptic ulcer disease', '20mg', 'capsules', 250.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(128, 'Ranitidine', 'Ranitidine HCl', 'H2 receptor antagonist for GERD', '150mg', 'tablets', 120.00, 400, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(129, 'Oral Rehydration Salts (ORS)', 'ORS', 'Rehydration solution for diarrhea', '20.5g', 'sachets', 200.00, 2000, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(130, 'Zinc Sulphate', 'Zinc', 'Zinc supplementation for diarrhea management', '20mg', 'tablets', 100.00, 1000, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(131, 'Loperamide', 'Loperamide HCl', 'Antidiarrheal agent', '2mg', 'capsules', 150.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(132, 'Magnesium Hydroxide', 'Milk of Magnesia', 'Antacid and laxative', '400mg/5ml', 'suspension', 3000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(133, 'Bisacodyl', 'Bisacodyl', 'Stimulant laxative', '5mg', 'tablets', 80.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(134, 'Lactulose', 'Lactulose', 'Osmotic laxative for constipation', '667mg/ml', 'syrup', 8000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(135, 'Hyoscine Butylbromide', 'Buscopan', 'Antispasmodic for abdominal pain', '10mg', 'tablets', 150.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(136, 'Metoclopramide', 'Metoclopramide HCl', 'Antiemetic and prokinetic agent', '10mg', 'tablets', 100.00, 400, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(137, 'Promethazine', 'Promethazine HCl', 'Antihistamine and antiemetic', '25mg', 'tablets', 120.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(138, 'Ondansetron', 'Ondansetron HCl', 'Serotonin antagonist antiemetic', '4mg', 'tablets', 500.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(139, 'Salbutamol Inhaler', 'Salbutamol', 'Short-acting beta-agonist for asthma', '100mcg', 'inhaler', 1500.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(140, 'Salbutamol Syrup', 'Salbutamol', 'Bronchodilator syrup', '2mg/5ml', 'syrup', 3000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(141, 'Beclomethasone Inhaler', 'Beclomethasone', 'Inhaled corticosteroid for asthma', '250mcg', 'inhaler', 3000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(142, 'Prednisolone', 'Prednisolone', 'Oral corticosteroid for inflammation', '5mg', 'tablets', 100.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(143, 'Aminophylline', 'Aminophylline', 'Bronchodilator for severe asthma', '100mg', 'tablets', 120.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(144, 'Aminophylline Injection', 'Aminophylline', 'Injectable bronchodilator', '250mg/10ml', 'ampoule', 2000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(145, 'Ipratropium Bromide Inhaler', 'Ipratropium', 'Anticholinergic bronchodilator', '20mcg', 'inhaler', 2500.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(146, 'Chlorpheniramine', 'Chlorpheniramine Maleate', 'First generation antihistamine', '4mg', 'tablets', 80.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(147, 'Cetirizine', 'Cetirizine HCl', 'Second generation antihistamine for allergies', '10mg', 'tablets', 80.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(148, 'Loratadine', 'Loratadine', 'Non-sedating antihistamine', '10mg', 'tablets', 100.00, 400, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(149, 'Hydrocortisone Cream', 'Hydrocortisone', 'Topical corticosteroid for skin inflammation', '1%', 'cream', 2000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(150, 'Betamethasone Cream', 'Betamethasone', 'Potent topical corticosteroid', '0.1%', 'cream', 3000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(151, 'Ferrous Sulphate', 'Iron', 'Iron supplement for anemia', '200mg', 'tablets', 80.00, 1000, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(152, 'Ferrous Sulphate + Folic Acid', 'Iron + Folate', 'Combined iron and folate for pregnancy', '200mg/0.25mg', 'tablets', 100.00, 1000, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(153, 'Folic Acid', 'Folic Acid', 'Folate supplement for anemia prevention', '5mg', 'tablets', 50.00, 1000, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(154, 'Vitamin B Complex', 'B Vitamins', 'Multiple B vitamin supplement', 'Multi', 'tablets', 150.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(155, 'Vitamin A', 'Retinol', 'Vitamin A supplementation for deficiency', '200,000IU', 'capsules', 200.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(156, 'Calcium Carbonate', 'Calcium', 'Calcium supplement', '500mg', 'tablets', 100.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(157, 'Multivitamins', 'Multivitamins', 'Daily vitamin supplement', 'Adult', 'tablets', 150.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(158, 'Oxytocin', 'Oxytocin', 'Uterotonic for prevention and treatment of PPH', '10IU/ml', 'ampoule', 2000.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(159, 'Misoprostol', 'Misoprostol', 'Prostaglandin for PPH prevention and cervical ripening', '200mcg', 'tablets', 500.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(160, 'Ergometrine', 'Ergometrine Maleate', 'Uterotonic for PPH management', '500mcg/ml', 'ampoule', 2500.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(161, 'Magnesium Sulphate', 'Magnesium Sulphate', 'Treatment of eclampsia and pre-eclampsia', '50%', 'ampoule', 2000.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(162, 'Nifedipine Slow Release', 'Nifedipine', 'Tocolytic and antihypertensive', '20mg', 'tablets', 150.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(163, 'Tranexamic Acid', 'Tranexamic Acid', 'Antifibrinolytic for PPH', '500mg', 'ampoule', 5000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(164, 'Clomiphene Citrate', 'Clomiphene', 'Ovulation inducer for infertility', '50mg', 'tablets', 500.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(165, 'Combined Oral Contraceptive', 'Ethinylestradiol + Levonorgestrel', 'COC pill for contraception', '30mcg/150mcg', 'tablets', 200.00, 1000, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(166, 'Progesterone Only Pill', 'Levonorgestrel', 'Progestin-only pill (mini-pill)', '30mcg', 'tablets', 150.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(167, 'Medroxyprogesterone Injectable', 'Depo-Provera', 'Injectable contraceptive', '150mg/ml', 'vial', 2500.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(168, 'Levonorgestrel Implant', 'Jadelle', 'Long-acting reversible contraceptive implant', '75mg x 2', 'implant', 15000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(169, 'Emergency Contraceptive Pill', 'Levonorgestrel', 'Emergency contraception', '1.5mg', 'tablets', 1000.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(170, 'Copper IUD', 'Copper T380A', 'Intrauterine contraceptive device', 'Device', 'unit', 5000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(171, 'Haloperidol', 'Haloperidol', 'Antipsychotic for schizophrenia and psychosis', '5mg', 'tablets', 150.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(172, 'Haloperidol Injection', 'Haloperidol', 'Injectable antipsychotic', '5mg/ml', 'ampoule', 2000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(173, 'Chlorpromazine', 'Chlorpromazine HCl', 'Antipsychotic and antiemetic', '100mg', 'tablets', 120.00, 400, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(174, 'Amitriptyline', 'Amitriptyline HCl', 'Tricyclic antidepressant', '25mg', 'tablets', 100.00, 400, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(175, 'Fluoxetine', 'Fluoxetine HCl', 'SSRI antidepressant', '20mg', 'capsules', 200.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(176, 'Diazepam Tablets', 'Diazepam', 'Benzodiazepine anxiolytic', '5mg', 'tablets', 80.00, 400, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(177, 'Lorazepam', 'Lorazepam', 'Benzodiazepine for anxiety', '2mg', 'tablets', 150.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(178, 'Lithium Carbonate', 'Lithium', 'Mood stabilizer for bipolar disorder', '300mg', 'tablets', 200.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(179, 'Tetracycline Eye Oint', 'Tetracycline', 'Antibiotic eye ointment', '1%', 'tube', 2000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(180, 'Chloramphenicol Eye Drops', 'Chloramphenicol', 'Antibiotic eye drops', '0.5%', 'drops', 2000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(181, 'Gentamicin Eye Drops', 'Gentamicin', 'Antibiotic eye drops', '0.3%', 'drops', 2500.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(182, 'Atropine Eye Drops', 'Atropine Sulphate', 'Mydriatic and cycloplegic', '1%', 'drops', 3000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(183, 'Timolol Eye Drops', 'Timolol Maleate', 'Beta-blocker for glaucoma', '0.5%', 'drops', 5000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(184, 'Pilocarpine Eye Drops', 'Pilocarpine HCl', 'Miotic for glaucoma', '2%', 'drops', 3000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(185, 'Acetazolamide', 'Acetazolamide', 'Carbonic anhydrase inhibitor for glaucoma', '250mg', 'tablets', 200.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(186, 'Whitfield Ointment', 'Benzoic + Salicylic Acid', 'Antifungal ointment for ringworm', '6%+3%', 'ointment', 2000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(187, 'Gentian Violet', 'Crystal Violet', 'Antiseptic and antifungal', '0.5%', 'solution', 1500.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(188, 'Calamine Lotion', 'Calamine', 'Soothing lotion for itching', 'Lotion', 'bottle', 2000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(189, 'Aqueous Cream', 'Emollient Base', 'Moisturizing cream', 'Cream', 'jar', 3000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(190, 'Zinc Oxide Ointment', 'Zinc Oxide', 'Protective barrier ointment', '15%', 'ointment', 2000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(191, 'Silver Sulfadiazine Cream', 'Silver Sulfadiazine', 'Topical antimicrobial for burns', '1%', 'cream', 5000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(192, 'Tetanus Toxoid', 'TT Vaccine', 'Tetanus vaccination', '0.5ml', 'ampoule', 2000.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(193, 'Anti-Tetanus Serum', 'ATS', 'Tetanus immunoglobulin', '1500IU', 'ampoule', 10000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(194, 'Anti-Rabies Vaccine', 'Rabies Vaccine', 'Post-exposure rabies prophylaxis', '2.5IU', 'vial', 25000.00, 30, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(195, 'Anti-Rabies Immunoglobulin', 'HRIG', 'Human rabies immunoglobulin', '300IU', 'vial', 50000.00, 20, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(196, 'Anti-Snake Venom', 'Polyvalent ASV', 'Antivenom for snake bites', '10ml', 'vial', 30000.00, 30, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(197, 'Normal Saline 0.9%', 'Sodium Chloride', 'Isotonic IV fluid', '1000ml', 'bag', 3000.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(198, 'Ringer Lactate', 'Hartmanns Solution', 'Balanced IV fluid', '1000ml', 'bag', 3000.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(199, 'Dextrose 5%', 'Glucose', 'Isotonic dextrose solution', '1000ml', 'bag', 3000.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(200, 'Dextrose 10%', 'Glucose', 'Hypertonic glucose solution', '500ml', 'bag', 2500.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(201, 'Dextrose Saline', 'Dextrose + NaCl', 'Combined glucose and saline', '1000ml', 'bag', 3000.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(202, 'Mannitol 20%', 'Mannitol', 'Osmotic diuretic for cerebral edema', '500ml', 'bottle', 15000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(203, 'Ketamine', 'Ketamine HCl', 'Dissociative anesthetic', '50mg/ml', 'vial', 5000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(204, 'Thiopentone Sodium', 'Thiopental', 'Induction agent for general anesthesia', '500mg', 'vial', 8000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(205, 'Halothane', 'Halothane', 'Volatile anesthetic agent', '250ml', 'bottle', 25000.00, 20, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(206, 'Lidocaine 2%', 'Lignocaine', 'Local anesthetic', '2%', 'vial', 1500.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(207, 'Bupivacaine', 'Bupivacaine HCl', 'Long-acting local anesthetic', '0.5%', 'ampoule', 3000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(208, 'Suxamethonium', 'Succinylcholine', 'Depolarizing muscle relaxant', '50mg/ml', 'ampoule', 3000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(209, 'Activated Charcoal', 'Charcoal', 'Adsorbent for poisoning', '50g', 'bottle', 5000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(210, 'N-Acetylcysteine', 'NAC', 'Antidote for paracetamol overdose', '200mg/ml', 'ampoule', 10000.00, 30, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(211, 'Desferrioxamine', 'Deferoxamine', 'Iron chelating agent', '500mg', 'vial', 15000.00, 20, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(212, 'Protamine Sulphate', 'Protamine', 'Heparin antagonist', '10mg/ml', 'ampoule', 8000.00, 30, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(213, 'Vitamin K', 'Phytomenadione', 'Antidote for warfarin overdose', '10mg/ml', 'ampoule', 3000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(214, 'Heparin Sodium', 'Unfractionated Heparin', 'Anticoagulant for DVT and PE', '5000IU/ml', 'vial', 5000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(215, 'Enoxaparin', 'Low Molecular Weight Heparin', 'LMWH for thromboprophylaxis', '40mg/0.4ml', 'syringe', 8000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(216, 'Warfarin', 'Warfarin Sodium', 'Oral anticoagulant', '5mg', 'tablets', 150.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(217, 'Clopidogrel', 'Clopidogrel', 'Antiplatelet for cardiovascular disease', '75mg', 'tablets', 300.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(218, 'Methotrexate', 'Methotrexate', 'Antimetabolite chemotherapy', '50mg', 'vial', 8000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(219, 'Cyclophosphamide', 'Cyclophosphamide', 'Alkylating agent chemotherapy', '500mg', 'vial', 10000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(220, 'Vincristine', 'Vincristine Sulphate', 'Vinca alkaloid chemotherapy', '1mg', 'vial', 8000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(221, 'Doxorubicin', 'Doxorubicin HCl', 'Anthracycline chemotherapy', '50mg', 'vial', 15000.00, 30, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(222, 'Tamoxifen', 'Tamoxifen Citrate', 'Hormonal therapy for breast cancer', '20mg', 'tablets', 300.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(223, 'Azathioprine', 'Azathioprine', 'Immunosuppressant', '50mg', 'tablets', 300.00, 200, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(224, 'Prednisolone High Dose', 'Prednisolone', 'High-dose corticosteroid', '25mg', 'tablets', 200.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(225, 'Povidone Iodine', 'Iodine Solution', 'Antiseptic solution', '10%', 'solution', 3000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(226, 'Hydrogen Peroxide', 'H2O2', 'Antiseptic and disinfectant', '6%', 'solution', 2000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(227, 'Methylated Spirit', 'Ethanol', 'Antiseptic and disinfectant', '70%', 'solution', 2000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(228, 'Glycerine', 'Glycerol', 'Emollient and lubricant', 'Pure', 'bottle', 3000.00, 50, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(229, 'Petroleum Jelly', 'Petrolatum', 'Protective ointment base', 'Pure', 'jar', 2000.00, 100, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(230, 'Cotrimoxazole Prophylaxis', 'SMX-TMP', 'HIV prophylaxis (CPT)', '960mg', 'tablets', 100.00, 2000, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(231, 'Pyridoxine', 'Vitamin B6', 'Prevention of INH neuropathy', '25mg', 'tablets', 80.00, 500, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36'),
(232, 'Allopurinol', 'Allopurinol', 'Xanthine oxidase inhibitor for gout', '100mg', 'tablets', 120.00, 300, 1, '2026-01-12 06:14:36', '2026-01-12 06:14:36');

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
(1, 1, 'PARA-2024-001', 1000, 837, '2026-12-31', 'MedSupply Ltd', 40.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2026-01-14 19:14:53'),
(2, 2, 'AMOX-2024-001', 500, 500, '2026-06-30', 'MedSupply Ltd', 150.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(3, 3, 'METRO-2024-001', 500, 500, '2026-08-31', 'PharmaDistrib', 120.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(4, 4, 'IBU-2024-001', 800, 800, '2027-03-31', 'MedSupply Ltd', 80.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(5, 5, 'CIPRO-2024-001', 300, 300, '2026-10-31', 'PharmaDistrib', 250.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(6, 6, 'OMEP-2024-001', 400, 400, '2026-09-30', 'MedSupply Ltd', 200.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(7, 7, 'CHL-2024-001', 1000, 905, '2027-12-31', 'PharmaDistrib', 80.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-11-10 09:38:43'),
(8, 8, 'AL-2024-001', 600, 587, '2026-11-30', 'Global Health', 400.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-11-05 20:27:07'),
(9, 9, 'MET-2024-001', 800, 790, '2027-06-30', 'MedSupply Ltd', 80.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-23 23:13:12'),
(10, 10, 'AML-2024-001', 500, 1000, '2026-12-31', 'PharmaDistrib', 120.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-11-10 20:26:00'),
(11, 11, 'SAL-2024-001', 100, 100, '2026-05-31', 'RespiCare', 1200.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(12, 12, 'CET-2024-001', 600, 600, '2027-02-28', 'MedSupply Ltd', 60.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(13, 13, 'MULTI-2024-001', 400, 400, '2026-12-31', 'Nutrition Plus', 120.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(14, 14, 'ORS-2024-001', 1000, 1000, '2027-12-31', 'WHO Supply', 150.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(15, 15, 'DICLO-2024-001', 600, 600, '2026-08-31', 'PharmaDistrib', 100.00, '2024-01-15', 1, 'active', NULL, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(16, 4, 'BATCH-20251110-202840-00004', 30, 200, '2027-11-10', NULL, 100.00, '2025-11-10', 1, 'active', NULL, '2025-11-10 20:28:40', '2025-11-10 20:28:40');

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
`id` int
,`name` varchar(100)
,`generic_name` varchar(100)
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
`id` int
,`name` varchar(100)
,`generic_name` varchar(100)
,`strength` varchar(50)
,`unit` varchar(20)
,`unit_price` decimal(10,2)
,`reorder_level` int
,`total_stock` decimal(32,0)
,`active_batches` bigint
,`nearest_expiry` date
,`stock_alert` varchar(13)
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
(33, 'KJ20260001', 'patient', 'one', '2001-07-02', 'male', '0712345678', 'patient@gmail.com', 'Mbeya', 'mkulima', 'jumla', '0683274343', NULL, NULL, NULL, NULL, NULL, '2026-01-08 16:02:17', '2026-01-08 16:02:17'),
(35, 'KJ20260002', 'patient', 'two', '2025-11-04', 'male', '69937654', 'two@gmail.com', 'Mbeya', 'king', 'jh', '98565689', NULL, NULL, NULL, NULL, NULL, '2026-01-09 08:18:24', '2026-01-09 08:18:24'),
(36, 'KJ20260003', 'patient', 'two', '2019-03-05', 'male', '37874635', 'two@gmail.com', 'dsbfnm', 'sfhvnd', 'vnv', '6578476546', NULL, NULL, NULL, NULL, NULL, '2026-01-09 08:20:24', '2026-01-09 08:20:24'),
(37, 'KJ20260004', 'patient', 'three', '2026-01-01', 'female', '3456575645', 'three@gmail.com', 'Mbeya', 'jkaf', 'adfgjh', '756432354', NULL, NULL, NULL, NULL, NULL, '2026-01-09 08:33:12', '2026-01-09 08:33:12'),
(38, 'KJ20260005', 'dfghjfj.,,', 'avbb', '2026-01-06', 'male', '897657', '', 'sadsfs', 'dfggdf', 'dsa', '345', NULL, NULL, NULL, NULL, NULL, '2026-01-09 09:20:07', '2026-01-09 09:20:07'),
(42, 'KJ20260006', 'patient', 'five', '2026-01-14', 'female', '243456324', '', 'sdfdgfh', 'sfghgdh', 'dfgdgdhg', '56543355', NULL, NULL, NULL, NULL, NULL, '2026-01-09 10:18:10', '2026-01-09 10:18:10'),
(43, 'KJ20260007', 'patient', 'six', '2026-01-05', 'female', '23435645434', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, '2026-01-09 10:50:59', '2026-01-09 10:50:59'),
(44, 'KJ20260008', 'patient', 'seven', '2026-01-15', 'male', '21345674', 'four@gmai.com', 'sdhk', 'dhfdjf', 'hdbdafn', '345678765', NULL, NULL, NULL, NULL, NULL, '2026-01-11 19:40:50', '2026-01-11 19:40:50'),
(45, 'KJ20260009', 'patient', 'eight', '2026-01-15', 'male', '21345674', 'four@gmai.com', 'sdhk', 'dhfdjf', 'hdbdafn', '347865643', NULL, NULL, NULL, NULL, NULL, '2026-01-12 13:26:16', '2026-01-12 13:26:16'),
(46, 'KJ20260010', 'patient', 'ten', '2022-11-10', 'male', '356543343', 'ten@gmail.com', 'majengo', 'mkulima', 'juma', '34546352', NULL, NULL, NULL, NULL, NULL, '2026-01-14 19:19:05', '2026-01-14 19:19:05'),
(47, 'KJ20260011', 'patient', 'eleven', '2000-07-04', 'female', '2654333423', 'eleven@gmail.com', 'eleven', 'mkulima', 'dgahfhbm,', '84567', NULL, NULL, NULL, NULL, NULL, '2026-01-14 19:35:30', '2026-01-14 19:35:30'),
(48, 'KJ20260012', 'patient', 'twelve', '1976-08-05', 'female', '346576453', 'twelve@gmail.com', 'dsfhsf', 'hdfgjsdh', 'shjsd', '345574352', NULL, NULL, NULL, NULL, NULL, '2026-01-14 19:38:13', '2026-01-14 19:38:13'),
(49, 'KJ20260013', 'patient', 'thirteen', '1894-07-03', 'male', '2345654354', 'thirteen@gmail.com', 'thirteen', 'thrirteen', 'dhjjkd', '34556432', NULL, NULL, NULL, NULL, NULL, '2026-01-14 20:06:00', '2026-01-14 20:06:00'),
(50, 'KJ20260014', 'patient', 'fourteen', '2004-08-04', 'male', '345675432', 'fourteen@gmail.com', 'four', 'four', 'asfdss', '23464532', NULL, NULL, NULL, NULL, NULL, '2026-01-14 21:13:57', '2026-01-14 21:13:57'),
(51, 'KJ20260015', 'patient', 'fifteen', '2004-07-04', 'male', '3254677654', 'fifteen@gmail.com', 'fifthteen', 'sdfsdf', 'sdgfsf', '234565453', NULL, NULL, NULL, NULL, NULL, '2026-01-14 21:33:10', '2026-01-14 21:33:10'),
(52, 'KJ20260016', 'patient', 'sixteen', '1916-12-16', 'male', '123454635213', 'kuminaita@gmail.com', 'dsfvjnds', 'sdjbvksdn', 'sdjfds', '76545678', NULL, NULL, NULL, NULL, NULL, '2026-01-14 21:37:15', '2026-01-14 21:37:15'),
(53, 'KJ20260017', 'patient', 'seventeen', '1999-04-03', 'male', '345465342', 'seven@gmail.com', 'sdshj', 'afjdh', 'safhs', '34634523', NULL, NULL, NULL, NULL, NULL, '2026-01-14 21:56:23', '2026-01-14 21:56:23'),
(54, 'KJ20260018', 'patient', 'eightteen', '1978-10-18', 'female', '23475323', 'ghs@gmail.com', 'ashfsd', 'shfsd', 'djgfsd', '34546533', NULL, NULL, NULL, NULL, NULL, '2026-01-14 22:05:11', '2026-01-14 22:05:11'),
(55, 'KJ20260019', 'patient', 'nineteen', '2009-01-09', 'female', '234565784', '', '', '', 'asdfgfbj', '12347453', NULL, NULL, NULL, NULL, NULL, '2026-01-14 22:17:00', '2026-01-14 22:17:00'),
(56, 'KJ20260020', 'patient', 'twenty', '2005-09-04', 'female', '2346564534', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, '2026-01-15 13:46:33', '2026-01-15 13:46:33'),
(57, 'KJ20260021', 'twenty', 'one', '2021-02-20', 'female', '21345764', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, '2026-01-15 23:17:16', '2026-01-15 23:17:16');

-- --------------------------------------------------------

--
-- Stand-in structure for view `patient_latest_visit`
-- (See below for the actual view)
--
CREATE TABLE `patient_latest_visit` (
`patient_id` int
,`visit_id` int
,`visit_number` int
,`status` enum('active','completed','cancelled')
,`visit_type` enum('consultation','lab_only','minor_service')
,`visit_date` date
,`created_at` timestamp
,`updated_at` timestamp
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
(51, 33, 1, '2026-01-08', 'consultation', NULL, 8, 'completed', NULL, '2026-01-08 16:02:17', '2026-01-14 19:14:51'),
(52, 35, 1, '2026-01-09', 'lab_only', NULL, 8, 'active', NULL, '2026-01-09 08:18:24', '2026-01-09 08:18:24'),
(53, 36, 1, '2026-01-09', 'lab_only', NULL, 8, 'active', NULL, '2026-01-09 08:20:24', '2026-01-09 08:20:24'),
(54, 37, 1, '2026-01-09', 'lab_only', NULL, 8, 'active', NULL, '2026-01-09 08:33:12', '2026-01-09 08:37:11'),
(55, 38, 1, '2026-01-09', 'minor_service', NULL, 8, 'active', NULL, '2026-01-09 09:20:07', '2026-01-09 09:20:07'),
(59, 42, 1, '2026-01-09', 'consultation', NULL, 8, 'completed', NULL, '2026-01-09 10:18:10', '2026-01-14 19:14:53'),
(60, 43, 1, '2026-01-09', 'lab_only', NULL, 8, 'active', NULL, '2026-01-09 10:50:59', '2026-01-09 10:53:40'),
(61, 44, 1, '2026-01-11', 'consultation', NULL, 8, 'completed', NULL, '2026-01-11 19:40:50', '2026-01-14 19:14:47'),
(62, 45, 1, '2026-01-12', 'consultation', NULL, 8, 'active', NULL, '2026-01-12 13:26:16', '2026-01-12 13:26:16'),
(63, 46, 1, '2026-01-14', 'consultation', NULL, 8, 'active', NULL, '2026-01-14 19:19:05', '2026-01-14 20:19:22'),
(64, 47, 1, '2026-01-14', 'consultation', NULL, 8, 'active', NULL, '2026-01-14 19:35:30', '2026-01-14 19:35:30'),
(65, 48, 1, '2026-01-14', 'consultation', NULL, 8, 'active', NULL, '2026-01-14 19:38:13', '2026-01-14 19:38:13'),
(66, 49, 1, '2026-01-14', 'consultation', NULL, 8, 'active', NULL, '2026-01-14 20:06:00', '2026-01-14 20:06:00'),
(67, 50, 1, '2026-01-15', 'consultation', NULL, 8, 'active', NULL, '2026-01-14 21:13:57', '2026-01-14 21:13:57'),
(68, 51, 1, '2026-01-15', 'consultation', NULL, 8, 'active', NULL, '2026-01-14 21:33:10', '2026-01-14 21:33:10'),
(69, 52, 1, '2026-01-15', 'consultation', NULL, 8, 'active', NULL, '2026-01-14 21:37:15', '2026-01-14 21:37:15'),
(70, 53, 1, '2026-01-15', 'consultation', NULL, 8, 'active', NULL, '2026-01-14 21:56:23', '2026-01-14 21:56:23'),
(71, 54, 1, '2026-01-15', 'consultation', NULL, 8, 'active', NULL, '2026-01-14 22:05:11', '2026-01-14 22:05:11'),
(72, 55, 1, '2026-01-15', 'consultation', NULL, 8, 'active', NULL, '2026-01-14 22:17:00', '2026-01-14 22:17:00'),
(73, 56, 1, '2026-01-15', 'consultation', NULL, 8, 'active', NULL, '2026-01-15 13:46:33', '2026-01-15 13:46:33'),
(74, 57, 1, '2026-01-16', 'consultation', NULL, 8, 'active', NULL, '2026-01-15 23:17:16', '2026-01-15 23:17:16');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `visit_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `payment_type` enum('registration','consultation','lab_test','medicine','minor_service','service') COLLATE utf8mb4_general_ci NOT NULL,
  `item_id` int DEFAULT NULL COMMENT 'Reference to lab_order, prescription, or service',
  `item_type` enum('lab_order','prescription','service','service_order') COLLATE utf8mb4_general_ci DEFAULT NULL,
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
(87, 51, 33, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 8, '2026-01-08 16:02:17', 'Initial consultation payment'),
(88, 51, 33, 'medicine', 20, 'prescription', 900.00, 'cash', 'paid', '', 8, '2026-01-09 08:06:08', NULL),
(89, 54, 37, 'lab_test', NULL, NULL, 5000.00, 'cash', 'paid', NULL, 8, '2026-01-09 08:33:12', 'Lab test payment at registration'),
(93, 59, 42, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 8, '2026-01-09 10:18:10', 'Initial consultation payment'),
(94, 59, 42, 'medicine', 21, 'prescription', 900.00, 'cash', 'paid', '', 8, '2026-01-09 10:49:24', NULL),
(95, 60, 43, 'lab_test', NULL, NULL, 8000.00, 'cash', 'paid', NULL, 8, '2026-01-09 10:50:59', 'Lab test payment at registration'),
(96, 61, 44, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 8, '2026-01-11 19:40:50', 'Initial consultation payment'),
(97, 61, 44, 'lab_test', 22, 'lab_order', 5000.00, 'cash', 'paid', '', 8, '2026-01-11 19:47:19', NULL),
(98, 61, 44, 'medicine', 22, 'prescription', 300.00, 'cash', 'paid', '', 8, '2026-01-11 19:49:16', NULL),
(99, 61, 44, 'service', 8, 'service_order', 5000.00, 'cash', 'paid', '', 8, '2026-01-11 19:49:37', NULL),
(100, 62, 45, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 8, '2026-01-12 13:26:16', 'Initial consultation payment'),
(101, 63, 46, 'registration', NULL, NULL, 3000.00, 'cash', 'paid', NULL, 8, '2026-01-14 19:19:05', 'Initial consultation payment'),
(104, 65, 48, 'consultation', NULL, NULL, 5000.00, 'cash', 'paid', '', 12, '2026-01-14 19:57:18', NULL),
(105, 65, 48, 'consultation', NULL, NULL, 5000.00, 'cash', 'paid', '', 12, '2026-01-14 19:59:12', NULL),
(106, 65, 48, 'consultation', NULL, NULL, 5000.00, 'cash', 'paid', '', 12, '2026-01-14 19:59:17', NULL),
(107, 65, 48, 'consultation', NULL, NULL, 5000.00, 'cash', 'paid', '', 12, '2026-01-14 19:59:23', NULL),
(108, 65, 48, 'consultation', NULL, NULL, 3000.00, 'cash', 'paid', '', 12, '2026-01-14 19:59:41', NULL),
(109, 65, 48, 'consultation', NULL, NULL, 5000.00, 'cash', 'paid', '', 12, '2026-01-14 20:01:27', NULL),
(110, 65, 48, 'consultation', NULL, NULL, 5000.00, 'cash', 'paid', '', 12, '2026-01-14 20:02:31', NULL),
(111, 64, 47, 'consultation', NULL, NULL, 5000.00, 'cash', 'paid', '', 12, '2026-01-14 20:03:21', NULL),
(112, 65, 48, 'consultation', NULL, NULL, 5000.00, 'cash', 'paid', '', 12, '2026-01-14 20:03:24', NULL),
(113, 66, 49, 'consultation', NULL, NULL, 3000.00, 'cash', 'paid', '', 12, '2026-01-14 20:06:21', NULL),
(114, 63, 46, 'lab_test', NULL, NULL, 8000.00, 'cash', 'paid', '', 12, '2026-01-14 20:18:21', NULL),
(115, 63, 46, 'medicine', 24, 'prescription', 300000.00, 'cash', 'paid', '', 12, '2026-01-14 20:18:30', NULL),
(116, 63, 46, 'service', 9, 'service_order', 5000.00, 'cash', 'paid', '', 12, '2026-01-14 20:18:36', NULL),
(117, 67, 50, 'consultation', NULL, NULL, 3000.00, 'cash', 'paid', '', 12, '2026-01-14 21:14:14', NULL),
(118, 68, 51, 'consultation', NULL, NULL, 3000.00, 'cash', 'paid', '', 12, '2026-01-14 21:33:24', NULL),
(119, 69, 52, 'consultation', NULL, NULL, 3000.00, 'cash', 'paid', '', 12, '2026-01-14 21:37:35', NULL),
(120, 70, 53, 'consultation', NULL, NULL, 3000.00, 'cash', 'paid', '', 12, '2026-01-14 21:56:49', NULL),
(121, 71, 54, 'consultation', NULL, NULL, 3000.00, 'cash', 'paid', '', 12, '2026-01-14 22:05:24', NULL),
(122, 72, 55, 'consultation', NULL, NULL, 3000.00, 'cash', 'paid', '', 12, '2026-01-14 22:17:25', NULL),
(123, 73, 56, 'consultation', NULL, NULL, 5000.00, 'cash', 'paid', '', 12, '2026-01-15 13:47:06', NULL),
(124, 74, 57, 'consultation', NULL, NULL, 3000.00, 'cash', 'paid', '', 12, '2026-01-15 23:17:37', NULL);

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
(20, 39, 51, 33, 9, 1, 18, 18, '200gm per 234', 'Once daily', '1', '', 'dispensed', NULL, 11, '2026-01-14 19:14:51', NULL, '2026-01-08 16:09:15', '2026-01-14 19:14:51'),
(21, 43, 59, 42, 9, 1, 18, 18, 'jhfjgdxch', 'Once daily', '1', '', 'dispensed', NULL, 11, '2026-01-14 19:14:53', NULL, '2026-01-09 10:48:42', '2026-01-14 19:14:53'),
(22, 44, 61, 44, 9, 1, 10, 10, 'hghsdjsbcxj', 'Once daily', '1', '', 'dispensed', NULL, 11, '2026-01-14 19:14:47', NULL, '2026-01-11 19:44:42', '2026-01-14 19:14:47'),
(23, 44, 61, 44, 9, 1, 10, 10, '3 daily', 'Once daily', '1', '', 'dispensed', NULL, 11, '2026-01-14 19:14:47', NULL, '2026-01-11 20:08:36', '2026-01-14 19:14:47'),
(24, 46, 63, 46, 9, 121, 100, 0, 'dfhsd', 'Once daily', '1', '', 'pending', NULL, NULL, NULL, NULL, '2026-01-14 20:15:02', '2026-01-14 20:15:02');

-- --------------------------------------------------------

--
-- Table structure for table `role_audit_log`
--

CREATE TABLE `role_audit_log` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `action` enum('role_added','role_removed','role_activated','role_deactivated') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','receptionist','doctor','lab_technician','accountant','pharmacist') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `performed_by` int DEFAULT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int NOT NULL,
  `role` enum('admin','receptionist','doctor','lab_technician','accountant','pharmacist') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `permission` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'e.g., patients.register, payments.collect, medicine.dispense',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role`, `permission`, `created_at`) VALUES
(1, 'receptionist', 'dashboard.view', '2026-01-14 18:56:24'),
(2, 'receptionist', 'patients.view', '2026-01-14 18:56:24'),
(3, 'receptionist', 'patients.register', '2026-01-14 18:56:24'),
(4, 'receptionist', 'patients.edit', '2026-01-14 18:56:24'),
(5, 'receptionist', 'patients.create_revisit', '2026-01-14 18:56:24'),
(6, 'receptionist', 'appointments.view', '2026-01-14 18:56:24'),
(7, 'receptionist', 'appointments.create', '2026-01-14 18:56:24'),
(8, 'receptionist', 'appointments.edit', '2026-01-14 18:56:24'),
(9, 'receptionist', 'vital_signs.record', '2026-01-14 18:56:24'),
(10, 'receptionist', 'reports.view_basic', '2026-01-14 18:56:24'),
(11, 'accountant', 'dashboard.view', '2026-01-14 18:56:45'),
(12, 'accountant', 'patients.view', '2026-01-14 18:56:45'),
(13, 'accountant', 'payments.view', '2026-01-14 18:56:45'),
(14, 'accountant', 'payments.collect', '2026-01-14 18:56:45'),
(15, 'accountant', 'payments.record', '2026-01-14 18:56:45'),
(16, 'accountant', 'payments.history', '2026-01-14 18:56:45'),
(17, 'accountant', 'payments.refund', '2026-01-14 18:56:45'),
(18, 'accountant', 'reports.view', '2026-01-14 18:56:45'),
(19, 'accountant', 'reports.financial', '2026-01-14 18:56:45'),
(20, 'accountant', 'reports.export', '2026-01-14 18:56:45'),
(21, 'pharmacist', 'dashboard.view', '2026-01-14 18:57:07'),
(22, 'pharmacist', 'patients.view', '2026-01-14 18:57:07'),
(23, 'pharmacist', 'medicine.view', '2026-01-14 18:57:07'),
(24, 'pharmacist', 'medicine.dispense', '2026-01-14 18:57:07'),
(25, 'pharmacist', 'medicine.inventory', '2026-01-14 18:57:07'),
(26, 'pharmacist', 'medicine.stock_update', '2026-01-14 18:57:07'),
(27, 'pharmacist', 'prescriptions.view', '2026-01-14 18:57:07'),
(28, 'pharmacist', 'prescriptions.dispense', '2026-01-14 18:57:07'),
(29, 'pharmacist', 'reports.view_basic', '2026-01-14 18:57:07'),
(30, 'doctor', 'dashboard.view', '2026-01-14 18:57:24'),
(31, 'doctor', 'patients.view', '2026-01-14 18:57:24'),
(32, 'doctor', 'patients.medical_history', '2026-01-14 18:57:24'),
(33, 'doctor', 'consultations.view', '2026-01-14 18:57:24'),
(34, 'doctor', 'consultations.create', '2026-01-14 18:57:24'),
(35, 'doctor', 'consultations.edit', '2026-01-14 18:57:24'),
(36, 'doctor', 'prescriptions.create', '2026-01-14 18:57:24'),
(37, 'doctor', 'lab_orders.create', '2026-01-14 18:57:24'),
(38, 'doctor', 'lab_results.view', '2026-01-14 18:57:24'),
(39, 'doctor', 'services.allocate', '2026-01-14 18:57:24'),
(40, 'doctor', 'reports.view_basic', '2026-01-14 18:57:24'),
(41, 'lab_technician', 'dashboard.view', '2026-01-14 18:57:41'),
(42, 'lab_technician', 'patients.view', '2026-01-14 18:57:41'),
(43, 'lab_technician', 'lab_tests.view', '2026-01-14 18:57:41'),
(44, 'lab_technician', 'lab_tests.collect_sample', '2026-01-14 18:57:41'),
(45, 'lab_technician', 'lab_tests.record_results', '2026-01-14 18:57:41'),
(46, 'lab_technician', 'lab_equipment.manage', '2026-01-14 18:57:41'),
(47, 'lab_technician', 'lab_inventory.manage', '2026-01-14 18:57:41'),
(48, 'lab_technician', 'lab_quality.manage', '2026-01-14 18:57:41'),
(49, 'lab_technician', 'reports.view_basic', '2026-01-14 18:57:41');

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
(5, 'ECG', 'ECG', 20000.00, 'Electrocardiogram recording', 0, 1, '2025-10-11 03:12:35', '2025-10-11 03:12:35'),
(7, 'ddskj', '4738udfjkhd', 3000.00, 'hdfxcbm,noeusdfhjesdmncnnuhc', 0, 1, '2025-11-10 09:59:19', '2025-11-10 09:59:19');

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

--
-- Dumping data for table `service_orders`
--

INSERT INTO `service_orders` (`id`, `visit_id`, `patient_id`, `service_id`, `ordered_by`, `performed_by`, `status`, `cancellation_reason`, `notes`, `performed_at`, `created_at`, `updated_at`) VALUES
(8, 61, 44, 3, 9, 8, 'completed', NULL, 'mshjfberihsfkejk[2026-01-11 19:55:42] ghsjfbjhsd\n', '2026-01-11 19:55:42', '2026-01-11 19:44:42', '2026-01-11 19:55:42'),
(9, 63, 46, 3, 9, 8, 'pending', NULL, 'dgsdjkkjf', NULL, '2026-01-14 20:15:02', '2026-01-14 20:15:02');

-- --------------------------------------------------------

--
-- Stand-in structure for view `staff_performance`
-- (See below for the actual view)
--
CREATE TABLE `staff_performance` (
`id` int
,`staff_name` varchar(101)
,`role` enum('admin','receptionist','doctor','lab_technician','accountant','pharmacist')
,`patients_registered` bigint
,`payments_collected` bigint
,`total_collected` decimal(32,2)
,`consultations_completed` bigint
,`prescriptions_written` bigint
,`tests_completed` bigint
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
  `role` enum('admin','receptionist','doctor','lab_technician','accountant','pharmacist') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
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
(8, 'hodi', '$2y$10$1sL5S.nhj4AMTQTcYZxMwOpn3tz1JgA/OLgdH1Zz5ESELsPWO7daO', 'hodi@gmail.com', 'receptionist', 'hodi', 'hodi', '083456879', NULL, 1, '2026-01-08 15:58:53', '2026-01-09 08:04:54'),
(9, 'doctor', '$2y$10$5OpGgpEujYIvjI0PkRdqP./FoCyaYb54UAEWRM8fsfDCrnGTz73.W', 'doctor@gmail.com', 'doctor', 'doctor', 'doctor', '0711345678', NULL, 1, '2026-01-08 16:03:35', '2026-01-08 16:03:35'),
(10, 'lab', '$2y$10$D66EHAWpLyuNg83Y3LUg6eSa58NtJKrWm/vGY7EX1vklhZIrhSEPe', 'lab@gmail.com', 'lab_technician', 'lab', 'lab', '0711145678', NULL, 1, '2026-01-08 16:06:02', '2026-01-08 16:06:02'),
(11, 'pharmacy', '$2y$10$VuttxXU17hmI4wJ2wUTDbO6dWZdf7cNbjCV5ciUzn/8OsyQALJKxy', 'aa@gmail.com', 'pharmacist', 'phamarc', 'phamarc', '3456453', NULL, 1, '2026-01-14 19:13:27', '2026-01-14 19:13:27'),
(12, 'cash', '$2y$10$SoZpg3inUZZh6V5.pimoge5znViepQwM6oe5kBYwG6fNjRVPDGKnm', 'cash@gmail.com', 'accountant', 'cash', 'cash', '233454535', NULL, 1, '2026-01-14 19:16:41', '2026-01-14 19:16:41');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `role` enum('admin','receptionist','doctor','lab_technician','accountant','pharmacist') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_primary` tinyint(1) DEFAULT '0' COMMENT 'Primary role for dashboard redirect',
  `granted_by` int DEFAULT NULL COMMENT 'User who granted this role',
  `granted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role`, `is_primary`, `granted_by`, `granted_at`, `is_active`) VALUES
(1, 1, 'admin', 1, NULL, '2026-01-14 18:54:38', 1),
(2, 8, 'receptionist', 1, NULL, '2026-01-14 18:54:38', 1),
(3, 9, 'doctor', 1, NULL, '2026-01-14 18:54:38', 1),
(4, 10, 'lab_technician', 1, NULL, '2026-01-14 18:54:38', 1);

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
(33, 51, 33, 36.0, 120, 270, 70, NULL, 75.0, 187.0, 8, '2026-01-08 16:02:17'),
(34, 55, 38, 37.0, 120, 80, 132, NULL, 23.0, 23.0, 8, '2026-01-09 09:20:07'),
(35, 59, 42, 36.0, 120, 80, 70, NULL, 32.0, 123.0, 8, '2026-01-09 10:18:10'),
(36, 61, 44, 36.0, 120, 80, 132, NULL, 23.0, 23.0, 8, '2026-01-11 19:40:50'),
(37, 62, 45, 36.0, 120, 80, 132, NULL, 23.0, 23.0, 8, '2026-01-12 13:26:16'),
(38, 63, 46, 36.0, 120, 80, 72, NULL, 56.0, 90.0, 8, '2026-01-14 19:19:05'),
(39, 64, 47, 36.0, 120, 80, 80, NULL, 78.0, 56.0, 8, '2026-01-14 19:35:30'),
(40, 65, 48, 36.0, 120, 80, 80, NULL, 67.0, 123.0, 8, '2026-01-14 19:38:13'),
(41, 66, 49, 36.0, 120, 80, 70, NULL, 89.0, 123.0, 8, '2026-01-14 20:06:00'),
(42, 67, 50, 36.0, 120, 80, 78, NULL, 56.0, 59.0, 8, '2026-01-14 21:13:57'),
(43, 68, 51, 36.0, 120, 80, 80, NULL, 69.0, 67.0, 8, '2026-01-14 21:33:10'),
(44, 69, 52, 36.0, 120, 80, 70, NULL, 89.0, 123.0, 8, '2026-01-14 21:37:15'),
(45, 70, 53, 36.0, 120, 80, 78, NULL, 67.0, 89.0, 8, '2026-01-14 21:56:23'),
(46, 71, 54, 36.0, 120, 80, 80, NULL, 80.0, 100.0, 8, '2026-01-14 22:05:11'),
(47, 72, 55, 36.0, 120, 60, 80, NULL, 57.0, 90.0, 8, '2026-01-14 22:17:00'),
(48, 73, 56, 36.0, 120, 80, 70, NULL, 70.0, 123.0, 8, '2026-01-15 13:46:33'),
(49, 74, 57, 36.0, 120, 60, 78, NULL, 68.0, 90.0, 8, '2026-01-15 23:17:16');

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
  ADD KEY `idx_consultation_status` (`status`),
  ADD KEY `fk_preliminary_diagnosis` (`preliminary_diagnosis_id`),
  ADD KEY `fk_final_diagnosis` (`final_diagnosis_id`);

--
-- Indexes for table `icd_codes`
--
ALTER TABLE `icd_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `lab_equipment`
--
ALTER TABLE `lab_equipment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `equipment_code` (`equipment_code`),
  ADD KEY `idx_equipment_status` (`status`),
  ADD KEY `idx_equipment_managed_by` (`managed_by`),
  ADD KEY `idx_equipment_active` (`is_active`);

--
-- Indexes for table `lab_inventory`
--
ALTER TABLE `lab_inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item_code` (`item_code`),
  ADD KEY `idx_inventory_category` (`category`),
  ADD KEY `idx_inventory_active` (`is_active`);

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
-- Indexes for table `lab_test_items`
--
ALTER TABLE `lab_test_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_test_item` (`test_id`,`item_id`),
  ADD KEY `test_id` (`test_id`),
  ADD KEY `item_id` (`item_id`);

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
-- Indexes for table `role_audit_log`
--
ALTER TABLE `role_audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_permission_unique` (`role`,`permission`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_permission` (`permission`);

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
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_role_unique` (`user_id`,`role`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `fk_user_roles_granted_by` (`granted_by`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `icd_codes`
--
ALTER TABLE `icd_codes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `lab_equipment`
--
ALTER TABLE `lab_equipment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lab_inventory`
--
ALTER TABLE `lab_inventory`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `lab_results`
--
ALTER TABLE `lab_results`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `lab_tests`
--
ALTER TABLE `lab_tests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `lab_test_categories`
--
ALTER TABLE `lab_test_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lab_test_items`
--
ALTER TABLE `lab_test_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lab_test_orders`
--
ALTER TABLE `lab_test_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=233;

--
-- AUTO_INCREMENT for table `medicine_batches`
--
ALTER TABLE `medicine_batches`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `medicine_dispensing`
--
ALTER TABLE `medicine_dispensing`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `patient_visits`
--
ALTER TABLE `patient_visits`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `role_audit_log`
--
ALTER TABLE `role_audit_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `service_orders`
--
ALTER TABLE `service_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vital_signs`
--
ALTER TABLE `vital_signs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `consultations`
--
ALTER TABLE `consultations`
  ADD CONSTRAINT `consultations_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultations_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultations_ibfk_3` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `consultations_ibfk_final_diagnosis` FOREIGN KEY (`final_diagnosis_id`) REFERENCES `icd_codes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `consultations_ibfk_preliminary_diagnosis` FOREIGN KEY (`preliminary_diagnosis_id`) REFERENCES `icd_codes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lab_equipment`
--
ALTER TABLE `lab_equipment`
  ADD CONSTRAINT `lab_equipment_ibfk_1` FOREIGN KEY (`managed_by`) REFERENCES `users` (`id`);

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
-- Constraints for table `lab_test_items`
--
ALTER TABLE `lab_test_items`
  ADD CONSTRAINT `lab_test_items_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `lab_tests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_test_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `lab_inventory` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `fk_user_roles_granted_by` FOREIGN KEY (`granted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_roles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
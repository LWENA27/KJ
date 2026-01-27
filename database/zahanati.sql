-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 27, 2026 at 08:08 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `consultation_overrides`
--

CREATE TABLE `consultation_overrides` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `visit_id` int DEFAULT NULL,
  `doctor_id` int NOT NULL,
  `override_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `override_type` enum('payment_bypass','emergency','other') COLLATE utf8mb4_general_ci DEFAULT 'payment_bypass',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consultation_overrides`
--

INSERT INTO `consultation_overrides` (`id`, `patient_id`, `visit_id`, `doctor_id`, `override_reason`, `override_type`, `created_at`) VALUES
(1, 60, 77, 9, 'Medical Emergency - Life threatening condition', 'payment_bypass', '2026-01-17 12:08:50'),
(2, 61, 78, 9, 'Medical Emergency - Life threatening conditionvguhgg', 'payment_bypass', '2026-01-26 08:56:49'),
(3, 65, 82, 9, 'Patient will pay after consultationFYUDYFG', 'payment_bypass', '2026-01-26 09:00:51'),
(4, 67, 83, 9, 'Insurance pending verification', 'payment_bypass', '2026-01-26 09:07:56'),
(5, 76, 89, 9, 'Medical Emergency - Life threatening condition', 'payment_bypass', '2026-01-26 09:16:22'),
(6, 44, 93, 9, 'Insurance pending verification', 'payment_bypass', '2026-01-26 12:57:11');

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
-- Table structure for table `ipd_admissions`
--

CREATE TABLE `ipd_admissions` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `visit_id` int NOT NULL,
  `bed_id` int NOT NULL,
  `admission_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `admission_datetime` datetime NOT NULL,
  `discharge_datetime` datetime DEFAULT NULL,
  `admission_type` enum('emergency','planned','transfer') COLLATE utf8mb4_general_ci DEFAULT 'planned',
  `admission_diagnosis` text COLLATE utf8mb4_general_ci,
  `discharge_diagnosis` text COLLATE utf8mb4_general_ci,
  `discharge_summary` text COLLATE utf8mb4_general_ci,
  `admitted_by` int NOT NULL,
  `attending_doctor` int DEFAULT NULL,
  `discharged_by` int DEFAULT NULL,
  `status` enum('active','discharged','transferred','deceased') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `total_days` int DEFAULT NULL COMMENT 'Calculated days of stay',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Note: Triggers removed due to hosting restrictions
-- The total_days field will be calculated in the application code instead
--

-- --------------------------------------------------------

--
-- Table structure for table `ipd_beds`
--

CREATE TABLE `ipd_beds` (
  `id` int NOT NULL,
  `ward_id` int NOT NULL,
  `bed_number` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `bed_type` enum('standard','oxygen','icu','isolation') COLLATE utf8mb4_general_ci DEFAULT 'standard',
  `status` enum('available','occupied','maintenance','reserved') COLLATE utf8mb4_general_ci DEFAULT 'available',
  `daily_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ipd_beds`
--

INSERT INTO `ipd_beds` (`id`, `ward_id`, `bed_number`, `bed_type`, `status`, `daily_rate`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'A-01', 'standard', 'occupied', 15000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-26 08:57:28'),
(2, 1, 'A-02', 'standard', 'occupied', 15000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-26 20:48:25'),
(3, 1, 'A-03', 'oxygen', 'available', 20000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-27 14:25:07'),
(4, 1, 'A-04', 'standard', 'occupied', 15000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-27 14:10:33'),
(5, 1, 'A-05', 'standard', 'available', 15000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-27 14:49:48'),
(6, 1, 'A-06', 'standard', 'available', 15000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(7, 1, 'A-07', 'oxygen', 'available', 20000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(8, 1, 'A-08', 'standard', 'available', 15000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(9, 1, 'A-09', 'standard', 'available', 15000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(10, 1, 'A-10', 'standard', 'available', 15000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(11, 2, 'B-01', 'standard', 'available', 15000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(12, 2, 'B-02', 'standard', 'available', 15000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(13, 2, 'B-03', 'standard', 'available', 15000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(14, 2, 'B-04', 'oxygen', 'available', 20000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(15, 2, 'B-05', 'standard', 'available', 15000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(16, 3, 'P-01', 'standard', 'available', 50000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(17, 3, 'P-02', 'standard', 'available', 50000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(18, 3, 'P-03', 'standard', 'available', 50000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(19, 3, 'P-04', 'standard', 'available', 50000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(20, 3, 'P-05', 'standard', 'available', 50000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(21, 4, 'ICU-01', 'icu', 'available', 150000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(22, 4, 'ICU-02', 'icu', 'available', 150000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(23, 4, 'ICU-03', 'icu', 'available', 150000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(24, 4, 'ICU-04', 'icu', 'available', 150000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(25, 5, 'MAT-01', 'standard', 'available', 25000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(26, 5, 'MAT-02', 'standard', 'available', 25000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(27, 5, 'MAT-03', 'standard', 'available', 25000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(28, 5, 'MAT-04', 'standard', 'available', 25000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(29, 5, 'MAT-05', 'standard', 'available', 25000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(30, 6, 'PED-01', 'standard', 'available', 18000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(31, 6, 'PED-02', 'oxygen', 'available', 23000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(32, 6, 'PED-03', 'standard', 'available', 18000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(33, 6, 'PED-04', 'standard', 'available', 18000.00, NULL, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18');

-- --------------------------------------------------------

--
-- Table structure for table `ipd_medication_admin`
--

CREATE TABLE `ipd_medication_admin` (
  `id` int NOT NULL,
  `admission_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `medicine_id` int NOT NULL,
  `prescribed_by` int NOT NULL COMMENT 'Doctor user_id',
  `administered_by` int DEFAULT NULL COMMENT 'Nurse user_id',
  `scheduled_datetime` datetime NOT NULL,
  `administered_datetime` datetime DEFAULT NULL,
  `dose` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `route` enum('oral','IV','IM','SC','topical','other') COLLATE utf8mb4_general_ci DEFAULT 'oral',
  `status` enum('scheduled','administered','missed','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'scheduled',
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ipd_progress_notes`
--

CREATE TABLE `ipd_progress_notes` (
  `id` int NOT NULL,
  `admission_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `note_datetime` datetime NOT NULL,
  `note_type` enum('doctor','nurse','other') COLLATE utf8mb4_general_ci DEFAULT 'doctor',
  `temperature` decimal(4,1) DEFAULT NULL COMMENT 'Celsius',
  `blood_pressure_systolic` int DEFAULT NULL,
  `blood_pressure_diastolic` int DEFAULT NULL,
  `pulse_rate` int DEFAULT NULL COMMENT 'bpm',
  `respiratory_rate` int DEFAULT NULL COMMENT 'breaths per minute',
  `oxygen_saturation` int DEFAULT NULL COMMENT 'SpO2 percentage',
  `progress_note` text COLLATE utf8mb4_general_ci,
  `recorded_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ipd_wards`
--

CREATE TABLE `ipd_wards` (
  `id` int NOT NULL,
  `ward_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `ward_code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `ward_type` enum('general','private','icu','maternity','pediatric','isolation') COLLATE utf8mb4_general_ci DEFAULT 'general',
  `total_beds` int NOT NULL DEFAULT '0',
  `occupied_beds` int NOT NULL DEFAULT '0',
  `floor_number` int DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ipd_wards`
--

INSERT INTO `ipd_wards` (`id`, `ward_name`, `ward_code`, `ward_type`, `total_beds`, `occupied_beds`, `floor_number`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'General Ward A', 'GEN-A', 'general', 20, 0, 1, 'General admission ward for mixed patients', 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(2, 'General Ward B', 'GEN-B', 'general', 20, 0, 1, 'General admission ward for mixed patients', 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(3, 'Private Ward', 'PRIV-1', 'private', 10, 0, 2, 'Private single-occupancy rooms', 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(4, 'ICU', 'ICU-1', 'icu', 6, 0, 3, 'Intensive Care Unit with monitoring', 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(5, 'Maternity Ward', 'MAT-1', 'maternity', 15, 0, 2, 'Maternity and post-natal care', 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(6, 'Pediatric Ward', 'PED-1', 'pediatric', 12, 0, 2, 'Children and infant ward', 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18');

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
-- Table structure for table `lab_maintenance`
--

CREATE TABLE `lab_maintenance` (
  `id` int NOT NULL,
  `equipment_id` int NOT NULL,
  `maintenance_type` enum('preventive','corrective','calibration','inspection') COLLATE utf8mb4_general_ci DEFAULT 'preventive',
  `scheduled_date` date NOT NULL,
  `completion_date` date DEFAULT NULL,
  `status` enum('pending','in_progress','completed','overdue') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `description` text COLLATE utf8mb4_general_ci,
  `technician_id` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `cost` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_maintenance`
--

INSERT INTO `lab_maintenance` (`id`, `equipment_id`, `maintenance_type`, `scheduled_date`, `completion_date`, `status`, `description`, `technician_id`, `notes`, `cost`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'preventive', '2026-01-25', NULL, 'pending', 'Routine maintenance and cleaning', NULL, 'Regular preventive maintenance for microscope', NULL, 1, '2026-01-18 11:35:09', '2026-01-18 11:35:09'),
(2, 2, 'calibration', '2026-02-01', NULL, 'pending', 'Quarterly calibration check', NULL, 'Chemistry analyzer needs quarterly calibration', NULL, 1, '2026-01-18 11:35:09', '2026-01-18 11:35:09'),
(3, 3, 'corrective', '2026-01-18', NULL, 'in_progress', 'Repair hematology analyzer', NULL, 'Equipment is currently under maintenance', NULL, 1, '2026-01-18 11:35:09', '2026-01-18 11:35:09'),
(4, 4, 'preventive', '2026-02-17', NULL, 'pending', 'Centrifuge inspection and maintenance', NULL, 'Quarterly preventive maintenance due', NULL, 1, '2026-01-18 11:35:09', '2026-01-18 11:35:09'),
(5, 5, 'calibration', '2026-02-08', NULL, 'pending', 'Temperature calibration', NULL, 'Incubator needs temperature recalibration', NULL, 1, '2026-01-18 11:35:09', '2026-01-18 11:35:09'),
(6, 6, 'preventive', '2026-01-18', NULL, 'overdue', 'Autoclave inspection', NULL, 'Overdue for annual safety inspection', NULL, 1, '2026-01-18 11:35:09', '2026-01-18 11:35:09');

-- --------------------------------------------------------

--
-- Table structure for table `lab_payment_overrides`
--

CREATE TABLE `lab_payment_overrides` (
  `id` int NOT NULL,
  `test_order_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `technician_id` int NOT NULL,
  `override_reason` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `patient_visits`
--

CREATE TABLE `patient_visits` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `visit_number` int NOT NULL COMMENT 'Sequential visit number for this patient',
  `visit_date` date NOT NULL,
  `visit_type` enum('consultation','lab_only','minor_service','ipd','medicine_pickup') COLLATE utf8mb4_general_ci NOT NULL,
  `assigned_doctor_id` int DEFAULT NULL COMMENT 'Future: pre-assigned doctor',
  `registered_by` int NOT NULL,
  `status` enum('active','completed','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `visit_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `payment_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `item_id` int DEFAULT NULL COMMENT 'Reference to lab_order, prescription, or service',
  `item_type` enum('lab_order','prescription','service','service_order','radiology_order') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method` enum('cash','card','mobile_money','insurance','cheque','transfer','other') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'cash',
  `payment_status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `reference_number` varchar(64) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `collected_by` int NOT NULL DEFAULT '0',
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notes` text COLLATE utf8mb4_general_ci
) ;

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

-- --------------------------------------------------------

--
-- Table structure for table `radiology_results`
--

CREATE TABLE `radiology_results` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `test_id` int NOT NULL,
  `findings` text COLLATE utf8mb4_general_ci,
  `impression` text COLLATE utf8mb4_general_ci,
  `recommendations` text COLLATE utf8mb4_general_ci,
  `images_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Path to uploaded images',
  `is_normal` tinyint(1) DEFAULT '1',
  `is_critical` tinyint(1) DEFAULT '0',
  `radiologist_id` int NOT NULL,
  `radiologist_notes` text COLLATE utf8mb4_general_ci,
  `completed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_by` int DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `radiology_tests`
--

CREATE TABLE `radiology_tests` (
  `id` int NOT NULL,
  `test_name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `test_code` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `category_id` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `preparation_instructions` text COLLATE utf8mb4_general_ci,
  `estimated_duration` int DEFAULT NULL COMMENT 'Minutes',
  `requires_contrast` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `radiology_tests`
--

INSERT INTO `radiology_tests` (`id`, `test_name`, `test_code`, `category_id`, `price`, `description`, `preparation_instructions`, `estimated_duration`, `requires_contrast`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Chest X-Ray (PA view)', 'XRAY-CHEST-PA', 1, 30000.00, 'Posteroanterior chest radiograph', NULL, 15, 0, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(2, 'Chest X-Ray (Lateral)', 'XRAY-CHEST-LAT', 1, 35000.00, 'Lateral chest radiograph', NULL, 15, 0, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(3, 'Abdominal X-Ray', 'XRAY-ABD', 1, 40000.00, 'Abdominal plain film', NULL, 15, 0, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(4, 'Skull X-Ray', 'XRAY-SKULL', 1, 45000.00, 'Skull radiograph', NULL, 20, 0, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(5, 'Spine X-Ray (Lumbar)', 'XRAY-SPINE-L', 1, 50000.00, 'Lumbar spine radiograph', NULL, 20, 0, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(6, 'Hand X-Ray', 'XRAY-HAND', 1, 25000.00, 'Hand/wrist radiograph', NULL, 10, 0, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(7, 'Foot X-Ray', 'XRAY-FOOT', 1, 25000.00, 'Foot/ankle radiograph', NULL, 10, 0, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(8, 'Abdominal Ultrasound', 'US-ABD', 2, 40000.00, 'Complete abdominal ultrasound', NULL, 30, 0, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(9, 'Pelvic Ultrasound', 'US-PELV', 2, 40000.00, 'Pelvic/gynecological ultrasound', NULL, 30, 0, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(10, 'Obstetric Ultrasound', 'US-OBS', 2, 50000.00, 'Pregnancy ultrasound scan', NULL, 30, 0, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(11, 'Breast Ultrasound', 'US-BREAST', 2, 45000.00, 'Breast ultrasound', NULL, 25, 0, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(12, 'Thyroid Ultrasound', 'US-THYROID', 2, 40000.00, 'Thyroid gland ultrasound', NULL, 20, 0, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(13, 'Head CT Scan', 'CT-HEAD', 3, 150000.00, 'Non-contrast head CT', NULL, 20, 0, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(14, 'Chest CT Scan', 'CT-CHEST', 3, 180000.00, 'Chest CT with contrast', NULL, 25, 0, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(15, 'Abdominal CT Scan', 'CT-ABD', 3, 200000.00, 'Abdominal/pelvic CT with contrast', NULL, 30, 0, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(16, 'Brain MRI', 'MRI-BRAIN', 4, 350000.00, 'Brain MRI with/without contrast', NULL, 45, 0, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(17, 'Spine MRI (Lumbar)', 'MRI-SPINE-L', 4, 300000.00, 'Lumbar spine MRI', NULL, 40, 0, 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18');

-- --------------------------------------------------------

--
-- Table structure for table `radiology_test_categories`
--

CREATE TABLE `radiology_test_categories` (
  `id` int NOT NULL,
  `category_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `category_code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `radiology_test_categories`
--

INSERT INTO `radiology_test_categories` (`id`, `category_name`, `category_code`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'X-Ray', 'XRAY', 'Radiography imaging using X-ray technology', 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(2, 'Ultrasound', 'US', 'Ultrasound/sonography imaging', 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(3, 'CT Scan', 'CT', 'Computed Tomography (CT) scans', 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18'),
(4, 'MRI', 'MRI', 'Magnetic Resonance Imaging', 1, '2026-01-25 07:38:18', '2026-01-25 07:38:18');

-- --------------------------------------------------------

--
-- Table structure for table `radiology_test_orders`
--

CREATE TABLE `radiology_test_orders` (
  `id` int NOT NULL,
  `visit_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `test_id` int NOT NULL,
  `ordered_by` int NOT NULL COMMENT 'Doctor user_id',
  `assigned_to` int DEFAULT NULL COMMENT 'Radiologist user_id',
  `priority` enum('normal','urgent','stat') COLLATE utf8mb4_general_ci DEFAULT 'normal',
  `status` enum('pending','scheduled','in_progress','completed','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `clinical_notes` text COLLATE utf8mb4_general_ci,
  `scheduled_datetime` datetime DEFAULT NULL,
  `cancellation_reason` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `role` enum('admin','receptionist','doctor','lab_technician','accountant','pharmacist','radiologist','nurse') COLLATE utf8mb4_general_ci NOT NULL,
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
(49, 'lab_technician', 'reports.view_basic', '2026-01-14 18:57:41'),
(50, 'radiologist', 'dashboard.view', '2026-01-25 07:38:18'),
(51, 'radiologist', 'patients.view', '2026-01-25 07:38:18'),
(52, 'radiologist', 'radiology.dashboard', '2026-01-25 07:38:18'),
(53, 'radiologist', 'radiology.view_orders', '2026-01-25 07:38:18'),
(54, 'radiologist', 'radiology.perform_test', '2026-01-25 07:38:18'),
(55, 'radiologist', 'radiology.record_result', '2026-01-25 07:38:18'),
(56, 'radiologist', 'radiology.view_result', '2026-01-25 07:38:18'),
(57, 'radiologist', 'radiology.upload_images', '2026-01-25 07:38:18'),
(58, 'nurse', 'dashboard.view', '2026-01-25 07:38:18'),
(59, 'nurse', 'patients.view', '2026-01-25 07:38:18'),
(60, 'nurse', 'ipd.dashboard', '2026-01-25 07:38:18'),
(61, 'nurse', 'ipd.view_admissions', '2026-01-25 07:38:18'),
(62, 'nurse', 'ipd.record_vitals', '2026-01-25 07:38:18'),
(63, 'nurse', 'ipd.progress_notes', '2026-01-25 07:38:18'),
(64, 'nurse', 'ipd.administer_medication', '2026-01-25 07:38:18'),
(65, 'nurse', 'ipd.view_medication_schedule', '2026-01-25 07:38:18');

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

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','receptionist','doctor','lab_technician','accountant','pharmacist','radiologist','nurse') COLLATE utf8mb4_general_ci NOT NULL,
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
(8, 'receptionist', '$2y$10$1sL5S.nhj4AMTQTcYZxMwOpn3tz1JgA/OLgdH1Zz5ESELsPWO7daO', 'hodi@gmail.com', 'receptionist', 'hodi', 'hodi', '083456879', NULL, 1, '2026-01-08 15:58:53', '2026-01-09 08:04:54'),
(9, 'doctor', '$2y$10$5OpGgpEujYIvjI0PkRdqP./FoCyaYb54UAEWRM8fsfDCrnGTz73.W', 'doctor@gmail.com', 'doctor', 'doctor', 'doctor', '0711345678', NULL, 1, '2026-01-08 16:03:35', '2026-01-08 16:03:35'),
(10, 'lab', '$2y$10$D66EHAWpLyuNg83Y3LUg6eSa58NtJKrWm/vGY7EX1vklhZIrhSEPe', 'lab@gmail.com', 'lab_technician', 'lab', 'lab', '0711145678', NULL, 1, '2026-01-08 16:06:02', '2026-01-08 16:06:02'),
(11, 'pharmacist', '$2y$10$VuttxXU17hmI4wJ2wUTDbO6dWZdf7cNbjCV5ciUzn/8OsyQALJKxy', 'aa@gmail.com', 'pharmacist', 'phamarc', 'phamarc', '3456453', NULL, 1, '2026-01-14 19:13:27', '2026-01-14 19:13:27'),
(12, 'accountant', '$2y$10$SoZpg3inUZZh6V5.pimoge5znViepQwM6oe5kBYwG6fNjRVPDGKnm', 'cash@gmail.com', 'accountant', 'cash', 'cash', '233454535', NULL, 1, '2026-01-14 19:16:41', '2026-01-14 19:16:41'),
(13, 'system_collector', '', 'system@zahanati.local', 'admin', 'System', 'Collector', NULL, NULL, 1, '2026-01-16 10:24:27', '2026-01-16 10:39:16'),
(1000000, 'radiologist', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'radiologist@hospital.com', 'radiologist', 'Sarah', 'Johnson', NULL, NULL, 1, '2026-01-26 05:00:41', '2026-01-26 05:00:41'),
(1000001, 'nurse', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'nurse@hospital.com', 'nurse', 'Mary', 'Williams', NULL, NULL, 1, '2026-01-26 05:00:41', '2026-01-26 05:00:41');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `role` enum('admin','receptionist','doctor','lab_technician','accountant','pharmacist','radiologist','nurse') COLLATE utf8mb4_general_ci NOT NULL,
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
(4, 10, 'lab_technician', 1, NULL, '2026-01-14 18:54:38', 1),
(5, 8, 'nurse', 0, 1, '2026-01-26 05:00:41', 1);

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

-- --------------------------------------------------------

--
-- Table structure for table `workflow_overrides`
--

CREATE TABLE `workflow_overrides` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `workflow_step` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `override_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `overridden_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Note: Database VIEWs and TRIGGERs removed due to hosting restrictions
-- Your shared hosting does not have CREATE VIEW or CREATE TRIGGER privileges
-- The application will work without these features
--

-- --------------------------------------------------------

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
-- Indexes for table `consultation_overrides`
--
ALTER TABLE `consultation_overrides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patient_id` (`patient_id`),
  ADD KEY `idx_doctor_id` (`doctor_id`),
  ADD KEY `idx_created_at` (`created_at`);

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
-- Indexes for table `ipd_admissions`
--
ALTER TABLE `ipd_admissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admission_number` (`admission_number`),
  ADD KEY `visit_id` (`visit_id`),
  ADD KEY `admitted_by` (`admitted_by`),
  ADD KEY `attending_doctor` (`attending_doctor`),
  ADD KEY `discharged_by` (`discharged_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_patient` (`patient_id`),
  ADD KEY `idx_bed` (`bed_id`);

--
-- Indexes for table `ipd_beds`
--
ALTER TABLE `ipd_beds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_bed` (`ward_id`,`bed_number`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_ward` (`ward_id`);

--
-- Indexes for table `ipd_medication_admin`
--
ALTER TABLE `ipd_medication_admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `medicine_id` (`medicine_id`),
  ADD KEY `prescribed_by` (`prescribed_by`),
  ADD KEY `administered_by` (`administered_by`),
  ADD KEY `idx_admission` (`admission_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_scheduled` (`scheduled_datetime`);

--
-- Indexes for table `ipd_progress_notes`
--
ALTER TABLE `ipd_progress_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `recorded_by` (`recorded_by`),
  ADD KEY `idx_admission` (`admission_id`),
  ADD KEY `idx_note_datetime` (`note_datetime`);

--
-- Indexes for table `ipd_wards`
--
ALTER TABLE `ipd_wards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ward_code` (`ward_code`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_ward_type` (`ward_type`);

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
-- Indexes for table `lab_maintenance`
--
ALTER TABLE `lab_maintenance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipment_id` (`equipment_id`),
  ADD KEY `technician_id` (`technician_id`);

--
-- Indexes for table `lab_payment_overrides`
--
ALTER TABLE `lab_payment_overrides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_test_order` (`test_order_id`),
  ADD KEY `idx_patient` (`patient_id`),
  ADD KEY `idx_technician` (`technician_id`);

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
  ADD UNIQUE KEY `uk_reference_number` (`reference_number`),
  ADD KEY `idx_payment_visit` (`visit_id`),
  ADD KEY `idx_payment_patient` (`patient_id`),
  ADD KEY `idx_payment_type` (`payment_type`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_payment_date` (`payment_date`),
  ADD KEY `collected_by` (`collected_by`),
  ADD KEY `idx_visit_id` (`visit_id`),
  ADD KEY `idx_patient_id` (`patient_id`),
  ADD KEY `idx_payment_type_status` (`payment_type`,`payment_status`),
  ADD KEY `idx_visit_payment_type_status` (`visit_id`,`payment_type`,`payment_status`),
  ADD KEY `idx_item_id` (`item_id`),
  ADD KEY `idx_collected_by` (`collected_by`);

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
-- Indexes for table `radiology_results`
--
ALTER TABLE `radiology_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_order_result` (`order_id`),
  ADD KEY `test_id` (`test_id`),
  ADD KEY `radiologist_id` (`radiologist_id`),
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_patient` (`patient_id`);

--
-- Indexes for table `radiology_tests`
--
ALTER TABLE `radiology_tests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `test_code` (`test_code`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_category` (`category_id`);

--
-- Indexes for table `radiology_test_categories`
--
ALTER TABLE `radiology_test_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_code` (`category_code`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `radiology_test_orders`
--
ALTER TABLE `radiology_test_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visit_id` (`visit_id`),
  ADD KEY `test_id` (`test_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_patient` (`patient_id`),
  ADD KEY `idx_ordered_by` (`ordered_by`),
  ADD KEY `idx_assigned_to` (`assigned_to`);

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
-- Indexes for table `workflow_overrides`
--
ALTER TABLE `workflow_overrides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `overridden_by` (`overridden_by`),
  ADD KEY `patient_id` (`patient_id`,`workflow_step`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `consultations`
--
ALTER TABLE `consultations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `consultation_overrides`
--
ALTER TABLE `consultation_overrides`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `icd_codes`
--
ALTER TABLE `icd_codes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `ipd_admissions`
--
ALTER TABLE `ipd_admissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ipd_beds`
--
ALTER TABLE `ipd_beds`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `ipd_medication_admin`
--
ALTER TABLE `ipd_medication_admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ipd_progress_notes`
--
ALTER TABLE `ipd_progress_notes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ipd_wards`
--
ALTER TABLE `ipd_wards`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- AUTO_INCREMENT for table `lab_maintenance`
--
ALTER TABLE `lab_maintenance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lab_payment_overrides`
--
ALTER TABLE `lab_payment_overrides`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_results`
--
ALTER TABLE `lab_results`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `patient_visits`
--
ALTER TABLE `patient_visits`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `radiology_results`
--
ALTER TABLE `radiology_results`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `radiology_tests`
--
ALTER TABLE `radiology_tests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `radiology_test_categories`
--
ALTER TABLE `radiology_test_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `radiology_test_orders`
--
ALTER TABLE `radiology_test_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `role_audit_log`
--
ALTER TABLE `role_audit_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `service_orders`
--
ALTER TABLE `service_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000002;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vital_signs`
--
ALTER TABLE `vital_signs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `workflow_overrides`
--
ALTER TABLE `workflow_overrides`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

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
-- Constraints for table `ipd_admissions`
--
ALTER TABLE `ipd_admissions`
  ADD CONSTRAINT `ipd_admissions_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_admissions_ibfk_2` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_admissions_ibfk_3` FOREIGN KEY (`bed_id`) REFERENCES `ipd_beds` (`id`),
  ADD CONSTRAINT `ipd_admissions_ibfk_4` FOREIGN KEY (`admitted_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ipd_admissions_ibfk_5` FOREIGN KEY (`attending_doctor`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ipd_admissions_ibfk_6` FOREIGN KEY (`discharged_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `ipd_beds`
--
ALTER TABLE `ipd_beds`
  ADD CONSTRAINT `ipd_beds_ibfk_1` FOREIGN KEY (`ward_id`) REFERENCES `ipd_wards` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ipd_medication_admin`
--
ALTER TABLE `ipd_medication_admin`
  ADD CONSTRAINT `ipd_medication_admin_ibfk_1` FOREIGN KEY (`admission_id`) REFERENCES `ipd_admissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_medication_admin_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_medication_admin_ibfk_3` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`),
  ADD CONSTRAINT `ipd_medication_admin_ibfk_4` FOREIGN KEY (`prescribed_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ipd_medication_admin_ibfk_5` FOREIGN KEY (`administered_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `ipd_progress_notes`
--
ALTER TABLE `ipd_progress_notes`
  ADD CONSTRAINT `ipd_progress_notes_ibfk_1` FOREIGN KEY (`admission_id`) REFERENCES `ipd_admissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_progress_notes_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_progress_notes_ibfk_3` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `lab_equipment`
--
ALTER TABLE `lab_equipment`
  ADD CONSTRAINT `lab_equipment_ibfk_1` FOREIGN KEY (`managed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `lab_maintenance`
--
ALTER TABLE `lab_maintenance`
  ADD CONSTRAINT `lab_maintenance_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `lab_equipment` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_maintenance_ibfk_2` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
-- Constraints for table `radiology_results`
--
ALTER TABLE `radiology_results`
  ADD CONSTRAINT `radiology_results_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `radiology_test_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `radiology_results_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `radiology_results_ibfk_3` FOREIGN KEY (`test_id`) REFERENCES `radiology_tests` (`id`),
  ADD CONSTRAINT `radiology_results_ibfk_4` FOREIGN KEY (`radiologist_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `radiology_results_ibfk_5` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `radiology_tests`
--
ALTER TABLE `radiology_tests`
  ADD CONSTRAINT `radiology_tests_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `radiology_test_categories` (`id`);

--
-- Constraints for table `radiology_test_orders`
--
ALTER TABLE `radiology_test_orders`
  ADD CONSTRAINT `radiology_test_orders_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `radiology_test_orders_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `radiology_test_orders_ibfk_3` FOREIGN KEY (`test_id`) REFERENCES `radiology_tests` (`id`),
  ADD CONSTRAINT `radiology_test_orders_ibfk_4` FOREIGN KEY (`ordered_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `radiology_test_orders_ibfk_5` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`);

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

--
-- Constraints for table `workflow_overrides`
--
ALTER TABLE `workflow_overrides`
  ADD CONSTRAINT `workflow_overrides_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `workflow_overrides_ibfk_2` FOREIGN KEY (`overridden_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
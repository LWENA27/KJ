-- MySQL dump 10.13  Distrib 8.0.43, for Linux (x86_64)
--
-- Host: localhost    Database: zahanati
-- ------------------------------------------------------
-- Server version	8.0.43-0ubuntu0.24.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Temporary view structure for view `common_diagnoses`
--

DROP TABLE IF EXISTS `common_diagnoses`;
/*!50001 DROP VIEW IF EXISTS `common_diagnoses`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `common_diagnoses` AS SELECT 
 1 AS `diagnosis`,
 1 AS `occurrence_count`,
 1 AS `unique_patients`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `consultations`
--

DROP TABLE IF EXISTS `consultations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consultations`
--

LOCK TABLES `consultations` WRITE;
/*!40000 ALTER TABLE `consultations` DISABLE KEYS */;
INSERT INTO `consultations` VALUES (1,1,1,3,1,'new',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'in_progress',NULL,'2025-10-11 05:15:14',NULL,'2025-10-11 03:50:52','2025-10-11 05:15:14'),(2,2,2,3,1,'new',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'in_progress',NULL,'2025-10-11 06:09:42',NULL,'2025-10-11 06:04:10','2025-10-11 06:09:42'),(3,3,3,1,1,'new',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2025-10-11 11:12:44','2025-10-11 11:12:44'),(4,4,4,3,1,'new','kichwa',NULL,'ubongo','','',NULL,0,NULL,NULL,NULL,NULL,'completed',NULL,'2025-10-17 08:04:05','2025-10-17 08:04:05','2025-10-17 08:02:10','2025-10-17 08:04:05'),(5,5,5,1,1,'new',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2025-10-17 08:35:54','2025-10-17 08:35:54'),(6,6,6,3,1,'new','kichwa',NULL,'snfh','','',NULL,0,NULL,NULL,NULL,NULL,'completed',NULL,'2025-10-18 05:14:25','2025-10-18 05:14:25','2025-10-18 05:02:27','2025-10-18 05:14:25'),(7,7,1,1,1,'',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2025-10-20 07:00:37','2025-10-20 07:00:37'),(9,9,6,1,1,'',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2025-10-20 07:12:12','2025-10-20 07:12:12');
/*!40000 ALTER TABLE `consultations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `daily_revenue_summary`
--

DROP TABLE IF EXISTS `daily_revenue_summary`;
/*!50001 DROP VIEW IF EXISTS `daily_revenue_summary`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `daily_revenue_summary` AS SELECT 
 1 AS `revenue_date`,
 1 AS `payment_type`,
 1 AS `payment_method`,
 1 AS `transaction_count`,
 1 AS `total_amount`,
 1 AS `collected_by_name`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lab_results`
--

DROP TABLE IF EXISTS `lab_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lab_results`
--

LOCK TABLES `lab_results` WRITE;
/*!40000 ALTER TABLE `lab_results` DISABLE KEYS */;
INSERT INTO `lab_results` VALUES (1,1,4,4,'1.0','Test completed successfully.','mg/dL',1,0,NULL,4,NULL,'2025-10-17 05:05:00',NULL,NULL,NULL);
/*!40000 ALTER TABLE `lab_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lab_test_categories`
--

DROP TABLE IF EXISTS `lab_test_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lab_test_categories` (
  `id` int NOT NULL,
  `category_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `category_code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lab_test_categories`
--

LOCK TABLES `lab_test_categories` WRITE;
/*!40000 ALTER TABLE `lab_test_categories` DISABLE KEYS */;
INSERT INTO `lab_test_categories` VALUES (1,'Hematology','HEMA','Blood cell counts and related tests',1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(2,'Clinical Chemistry','CHEM','Chemical analysis of blood and body fluids',1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(3,'Microbiology','MICRO','Bacterial, viral, and fungal tests',1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(4,'Immunology','IMMUNO','Immune system and antibody tests',1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(5,'Urinalysis','URINE','Urine examination tests',1,'2025-10-11 03:12:35','2025-10-11 03:12:35');
/*!40000 ALTER TABLE `lab_test_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lab_test_orders`
--

DROP TABLE IF EXISTS `lab_test_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lab_test_orders`
--

LOCK TABLES `lab_test_orders` WRITE;
/*!40000 ALTER TABLE `lab_test_orders` DISABLE KEYS */;
INSERT INTO `lab_test_orders` VALUES (1,4,4,4,4,3,4,'normal','completed',NULL,NULL,NULL,NULL,'2025-10-17 08:04:05','2025-10-17 08:05:15'),(2,6,6,6,4,3,4,'normal','pending',NULL,NULL,NULL,NULL,'2025-10-18 05:14:25','2025-10-18 05:14:25');
/*!40000 ALTER TABLE `lab_test_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lab_tests`
--

DROP TABLE IF EXISTS `lab_tests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lab_tests`
--

LOCK TABLES `lab_tests` WRITE;
/*!40000 ALTER TABLE `lab_tests` DISABLE KEYS */;
INSERT INTO `lab_tests` VALUES (1,'Complete Blood Count','CBC',1,15000.00,'RBC: 4.5-5.5, WBC: 4-11, Hb: 12-16','cells/mcL','Full blood count analysis',NULL,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(2,'Blood Sugar (Random)','BS-R',2,5000.00,'70-140','mg/dL','Random blood glucose test',NULL,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(3,'Blood Sugar (Fasting)','BS-F',2,5000.00,'70-100','mg/dL','Fasting blood glucose test',NULL,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(4,'Malaria Test','MAL',3,8000.00,'Negative','','Malaria parasite detection',NULL,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(5,'Urinalysis','URINE',5,6000.00,'Normal','','Complete urine examination',NULL,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(6,'Stool Examination','STOOL',3,7000.00,'Normal','','Stool microscopy and culture',NULL,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(7,'Liver Function Test','LFT',2,25000.00,'ALT: 7-56, AST: 10-40','U/L','Complete liver function panel',NULL,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(8,'Kidney Function Test','KFT',2,25000.00,'Creatinine: 0.7-1.3','mg/dL','Renal function assessment',NULL,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(9,'Pregnancy Test','PREG',4,5000.00,'Positive/Negative','','hCG detection in urine',NULL,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(10,'HIV Test','HIV',4,10000.00,'Non-reactive','','HIV antibody screening',NULL,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(11,'Hepatitis B Surface Antigen','HBsAg',4,15000.00,'Negative','','Hepatitis B screening',NULL,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(12,'Widal Test','WIDAL',4,12000.00,'Non-reactive','','Typhoid fever antibody test',NULL,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(13,'ESR','ESR',1,5000.00,'Male: 0-15, Female: 0-20','mm/hr','Erythrocyte sedimentation rate',NULL,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(14,'Blood Group & Rh','BG-RH',1,8000.00,'A/B/AB/O, Rh+/-','','Blood typing and Rh factor',NULL,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(15,'X-Ray Chest','XRAY-C',1,30000.00,'Normal','','Chest radiography',NULL,1,'2025-10-11 03:12:35','2025-10-11 03:12:35');
/*!40000 ALTER TABLE `lab_tests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medicine_batches`
--

DROP TABLE IF EXISTS `medicine_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medicine_batches`
--

LOCK TABLES `medicine_batches` WRITE;
/*!40000 ALTER TABLE `medicine_batches` DISABLE KEYS */;
INSERT INTO `medicine_batches` VALUES (1,1,'PARA-2024-001',1000,1000,'2026-12-31','MedSupply Ltd',40.00,'2024-01-15',1,'active',NULL,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(2,2,'AMOX-2024-001',500,500,'2026-06-30','MedSupply Ltd',150.00,'2024-01-15',1,'active',NULL,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(3,3,'METRO-2024-001',500,500,'2026-08-31','PharmaDistrib',120.00,'2024-01-15',1,'active',NULL,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(4,4,'IBU-2024-001',800,800,'2027-03-31','MedSupply Ltd',80.00,'2024-01-15',1,'active',NULL,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(5,5,'CIPRO-2024-001',300,300,'2026-10-31','PharmaDistrib',250.00,'2024-01-15',1,'active',NULL,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(6,6,'OMEP-2024-001',400,400,'2026-09-30','MedSupply Ltd',200.00,'2024-01-15',1,'active',NULL,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(7,7,'CHL-2024-001',1000,1000,'2027-12-31','PharmaDistrib',80.00,'2024-01-15',1,'active',NULL,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(8,8,'AL-2024-001',600,600,'2026-11-30','Global Health',400.00,'2024-01-15',1,'active',NULL,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(9,9,'MET-2024-001',800,800,'2027-06-30','MedSupply Ltd',80.00,'2024-01-15',1,'active',NULL,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(10,10,'AML-2024-001',500,500,'2026-12-31','PharmaDistrib',120.00,'2024-01-15',1,'active',NULL,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(11,11,'SAL-2024-001',100,100,'2026-05-31','RespiCare',1200.00,'2024-01-15',1,'active',NULL,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(12,12,'CET-2024-001',600,600,'2027-02-28','MedSupply Ltd',60.00,'2024-01-15',1,'active',NULL,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(13,13,'MULTI-2024-001',400,400,'2026-12-31','Nutrition Plus',120.00,'2024-01-15',1,'active',NULL,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(14,14,'ORS-2024-001',1000,1000,'2027-12-31','WHO Supply',150.00,'2024-01-15',1,'active',NULL,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(15,15,'DICLO-2024-001',600,600,'2026-08-31','PharmaDistrib',100.00,'2024-01-15',1,'active',NULL,'2025-10-11 03:12:35','2025-10-11 03:12:35');
/*!40000 ALTER TABLE `medicine_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medicine_dispensing`
--

DROP TABLE IF EXISTS `medicine_dispensing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `medicine_dispensing` (
  `id` int NOT NULL,
  `prescription_id` int NOT NULL,
  `batch_id` int NOT NULL,
  `quantity` int NOT NULL,
  `dispensed_by` int NOT NULL,
  `dispensed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medicine_dispensing`
--

LOCK TABLES `medicine_dispensing` WRITE;
/*!40000 ALTER TABLE `medicine_dispensing` DISABLE KEYS */;
/*!40000 ALTER TABLE `medicine_dispensing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medicine_stock_status`
--

DROP TABLE IF EXISTS `medicine_stock_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `medicine_stock_status` (
  `id` int DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `generic_name` varchar(100) DEFAULT NULL,
  `strength` varchar(50) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `reorder_level` int DEFAULT NULL,
  `total_stock` decimal(32,0) DEFAULT NULL,
  `active_batches` bigint DEFAULT NULL,
  `nearest_expiry` date DEFAULT NULL,
  `stock_alert` varchar(13) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medicine_stock_status`
--

LOCK TABLES `medicine_stock_status` WRITE;
/*!40000 ALTER TABLE `medicine_stock_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `medicine_stock_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medicines`
--

DROP TABLE IF EXISTS `medicines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medicines`
--

LOCK TABLES `medicines` WRITE;
/*!40000 ALTER TABLE `medicines` DISABLE KEYS */;
INSERT INTO `medicines` VALUES (1,'Paracetamol','Acetaminophen','Pain relief and fever reduction','500mg','tablets',50.00,500,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(2,'Amoxicillin','Amoxicillin','Antibiotic for bacterial infections','500mg','capsules',200.00,300,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(3,'Metronidazole','Metronidazole','Antibiotic and antiprotozoal','400mg','tablets',150.00,300,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(4,'Ibuprofen','Ibuprofen','Anti-inflammatory and pain relief','400mg','tablets',100.00,400,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(5,'Ciprofloxacin','Ciprofloxacin','Broad-spectrum antibiotic','500mg','tablets',300.00,200,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(6,'Omeprazole','Omeprazole','Reduces stomach acid production','20mg','capsules',250.00,200,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(7,'Chloroquine','Chloroquine','Antimalarial medication','250mg','tablets',100.00,500,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(8,'Artemether-Lumefantrine','AL','First-line malaria treatment','20/120mg','tablets',500.00,300,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(9,'Metformin','Metformin','Type 2 diabetes management','500mg','tablets',100.00,400,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(10,'Amlodipine','Amlodipine','Blood pressure medication','5mg','tablets',150.00,300,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(11,'Salbutamol','Salbutamol','Asthma relief inhaler','100mcg','inhaler',1500.00,50,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(12,'Cetirizine','Cetirizine','Antihistamine for allergies','10mg','tablets',80.00,300,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(13,'Multivitamins','Multivitamins','Daily vitamin supplement','Adult','tablets',150.00,200,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(14,'ORS','Oral Rehydration Salts','Dehydration treatment','27.9g','sachets',200.00,500,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(15,'Diclofenac','Diclofenac','Pain and inflammation relief','50mg','tablets',120.00,300,1,'2025-10-11 03:12:35','2025-10-11 03:12:35');
/*!40000 ALTER TABLE `medicines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patient_latest_visit`
--

DROP TABLE IF EXISTS `patient_latest_visit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `patient_latest_visit` (
  `patient_id` int DEFAULT NULL,
  `visit_id` int DEFAULT NULL,
  `visit_number` int DEFAULT NULL,
  `status` enum('active','completed','cancelled') DEFAULT NULL,
  `visit_type` enum('consultation','lab_only','minor_service') DEFAULT NULL,
  `visit_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patient_latest_visit`
--

LOCK TABLES `patient_latest_visit` WRITE;
/*!40000 ALTER TABLE `patient_latest_visit` DISABLE KEYS */;
/*!40000 ALTER TABLE `patient_latest_visit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patient_visits`
--

DROP TABLE IF EXISTS `patient_visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patient_visits`
--

LOCK TABLES `patient_visits` WRITE;
/*!40000 ALTER TABLE `patient_visits` DISABLE KEYS */;
/*!40000 ALTER TABLE `patient_visits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patients`
--

DROP TABLE IF EXISTS `patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patients`
--

LOCK TABLES `patients` WRITE;
/*!40000 ALTER TABLE `patients` DISABLE KEYS */;
INSERT INTO `patients` VALUES (1,'KJ20250001','lwena','samson','2001-07-02','male','068327434','lwena027@gmail.com','',NULL,'ADAM lwena home of technologies LWENA','0683274343',NULL,NULL,NULL,NULL,NULL,'2025-10-11 03:50:52','2025-10-11 03:50:52'),(2,'KJ20250002','adam','lwena','2025-07-02','male','0683274343','adamlwena22@gmai.com','',NULL,'jumla','0683274343',NULL,NULL,NULL,NULL,NULL,'2025-10-11 06:04:10','2025-10-11 06:04:10'),(3,'KJ20250003','adam','lwena','2025-05-04','male','0683274343','adamlwena22@gmai.com','',NULL,'adam samson lwena','0683274343',NULL,NULL,NULL,NULL,NULL,'2025-10-11 11:12:44','2025-10-11 11:12:44'),(4,'KJ20250004','diamond','platinumz','1984-04-02','male','087242534','platnumz@gmai.com','',NULL,'jumla lokole','0683274343',NULL,NULL,NULL,NULL,NULL,'2025-10-17 08:02:10','2025-10-17 08:02:10'),(5,'KJ20250005','sule','sule','2025-05-11','male','6543245','hjjf@gmail.com','',NULL,'zahanati','0987645678',NULL,NULL,NULL,NULL,NULL,'2025-10-17 08:35:54','2025-10-17 08:35:54'),(6,'KJ20250006','hamza','mtinangi','2005-02-02','male','07212121212','hamza@gmail.com','',NULL,'lwena samson','068327434',NULL,NULL,NULL,NULL,NULL,'2025-10-18 05:02:27','2025-10-18 05:02:27');
/*!40000 ALTER TABLE `patients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,1,1,'registration',NULL,NULL,3000.00,'cash','paid',NULL,2,'2025-10-11 03:50:52','Initial consultation payment'),(2,2,2,'registration',NULL,NULL,3000.00,'cash','paid',NULL,2,'2025-10-11 06:04:10','Initial consultation payment'),(3,3,3,'registration',NULL,NULL,3000.00,'cash','paid',NULL,2,'2025-10-11 11:12:44','Initial consultation payment'),(4,4,4,'registration',NULL,NULL,3000.00,'cash','paid',NULL,2,'2025-10-17 08:02:10','Initial consultation payment'),(5,4,4,'lab_test',1,'lab_order',8000.00,'cash','paid','',2,'2025-10-17 08:04:55',NULL),(6,5,5,'registration',NULL,NULL,3000.00,'cash','paid',NULL,2,'2025-10-17 08:35:54','Initial consultation payment'),(7,6,6,'registration',NULL,NULL,3000.00,'cash','paid',NULL,2,'2025-10-18 05:02:27','Initial consultation payment'),(8,7,1,'registration',NULL,NULL,3000.00,'cash','paid',NULL,2,'2025-10-20 07:00:37','Revisit payment - Visit #2'),(10,9,6,'registration',NULL,NULL,3000.00,'cash','paid',NULL,2,'2025-10-20 07:12:12','Revisit payment - Visit #2');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prescriptions`
--

DROP TABLE IF EXISTS `prescriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prescriptions`
--

LOCK TABLES `prescriptions` WRITE;
/*!40000 ALTER TABLE `prescriptions` DISABLE KEYS */;
INSERT INTO `prescriptions` VALUES (2,6,6,6,3,1,10,0,'200g','as prescribed','','4','pending',NULL,NULL,NULL,NULL,'2025-10-18 05:14:25','2025-10-18 05:14:25');
/*!40000 ALTER TABLE `prescriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_orders`
--

DROP TABLE IF EXISTS `service_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_orders`
--

LOCK TABLES `service_orders` WRITE;
/*!40000 ALTER TABLE `service_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,'Consultation Fee','CONSULT',3000.00,'Standard medical consultation',1,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(2,'Blood Pressure Check','BP-CHECK',1000.00,'Blood pressure measurement',0,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(3,'Wound Dressing','DRESS',5000.00,'Wound cleaning and dressing',0,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(4,'Injection','INJ',2000.00,'Intramuscular or IV injection',0,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(5,'ECG','ECG',20000.00,'Electrocardiogram recording',0,1,'2025-10-11 03:12:35','2025-10-11 03:12:35');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_performance`
--

DROP TABLE IF EXISTS `staff_performance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_performance` (
  `id` int DEFAULT NULL,
  `staff_name` varchar(101) DEFAULT NULL,
  `role` enum('admin','receptionist','doctor','lab_technician') DEFAULT NULL,
  `patients_registered` bigint DEFAULT NULL,
  `payments_collected` bigint DEFAULT NULL,
  `total_collected` decimal(32,2) DEFAULT NULL,
  `consultations_completed` bigint DEFAULT NULL,
  `prescriptions_written` bigint DEFAULT NULL,
  `tests_completed` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_performance`
--

LOCK TABLES `staff_performance` WRITE;
/*!40000 ALTER TABLE `staff_performance` DISABLE KEYS */;
/*!40000 ALTER TABLE `staff_performance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin@clinic.local','admin','System','Administrator','0700000001',NULL,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(2,'reception','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','reception@clinic.local','receptionist','Jane','Receptionist','0700000002',NULL,1,'2025-10-11 03:12:35','2025-10-11 03:12:35'),(3,'doctor','$2y$10$MDwICFM3PNoMjvLk8rLjFuLPwx9Sdo4XxE3hreqgoOuZIYeGdfb9e','doctor@clinic.local','doctor','Dr. John','Smith','0700000003',NULL,1,'2025-10-11 03:12:35','2025-10-11 06:05:42'),(4,'lab','$2y$10$Oe6k5.PnjdDYYslDtOTYSebR06A25xephNiWs75goQ.lRffFvpKoy','lab@clinic.local','lab_technician','Mary','Technician','0700000004',NULL,1,'2025-10-11 03:12:35','2025-10-11 05:27:50'),(5,'adm','$2y$10$z5McVHsnkImJ81WlacP4ROypVtt45zj834JsAAMXWhxb4igAhb8TS','adamlwena22@gmai.com','admin','adam','lwena','0683274343',NULL,1,'2025-10-11 11:10:19','2025-10-11 11:10:19');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vital_signs`
--

DROP TABLE IF EXISTS `vital_signs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vital_signs`
--

LOCK TABLES `vital_signs` WRITE;
/*!40000 ALTER TABLE `vital_signs` DISABLE KEYS */;
INSERT INTO `vital_signs` (`id`, `visit_id`, `patient_id`, `temperature`, `blood_pressure_systolic`, `blood_pressure_diastolic`, `pulse_rate`, `respiratory_rate`, `weight`, `height`, `recorded_by`, `recorded_at`) VALUES (1,1,1,35.0,120,80,75,NULL,60.0,127.0,2,'2025-10-11 03:50:52'),(2,2,2,37.0,120,270,76,NULL,45.0,3.0,2,'2025-10-11 06:04:10'),(3,3,3,36.0,120,270,76,NULL,35.0,123.0,2,'2025-10-11 11:12:44'),(4,4,4,36.0,120,270,120,NULL,78.0,178.0,2,'2025-10-17 08:02:10'),(5,5,5,36.0,120,80,75,NULL,60.0,127.0,2,'2025-10-17 08:35:54'),(6,6,6,36.0,120,80,75,NULL,60.0,127.0,2,'2025-10-18 05:02:27');
/*!40000 ALTER TABLE `vital_signs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `common_diagnoses`
--

/*!50001 DROP VIEW IF EXISTS `common_diagnoses`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `common_diagnoses` AS select `consultations`.`diagnosis` AS `diagnosis`,count(0) AS `occurrence_count`,count(distinct `consultations`.`patient_id`) AS `unique_patients` from `consultations` where ((`consultations`.`diagnosis` is not null) and (`consultations`.`diagnosis` <> '') and (`consultations`.`status` = 'completed')) group by `consultations`.`diagnosis` order by count(0) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `daily_revenue_summary`
--

/*!50001 DROP VIEW IF EXISTS `daily_revenue_summary`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `daily_revenue_summary` AS select cast(`payments`.`payment_date` as date) AS `revenue_date`,`payments`.`payment_type` AS `payment_type`,`payments`.`payment_method` AS `payment_method`,count(0) AS `transaction_count`,sum(`payments`.`amount`) AS `total_amount`,concat(`uc`.`first_name`,' ',`uc`.`last_name`) AS `collected_by_name` from (`payments` join `users` `uc` on((`payments`.`collected_by` = `uc`.`id`))) where (`payments`.`payment_status` = 'paid') group by cast(`payments`.`payment_date` as date),`payments`.`payment_type`,`payments`.`payment_method`,`uc`.`first_name`,`uc`.`last_name` order by cast(`payments`.`payment_date` as date) desc,`payments`.`payment_type` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-21 21:53:33

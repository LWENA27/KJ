-- Migration: Add ICD Diagnosis Codes Table for NMCP Compliance
-- This migration adds standardized diagnosis codes to comply with Tanzania NMCP guidelines
-- Reference: https://www.nmcp.go.tz/storage/app/uploads/public/643/91b/120/64391b120fef4281592461.pdf

-- Create ICD codes reference table
CREATE TABLE IF NOT EXISTS `icd_codes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL COMMENT 'ICD-10 code (e.g., B50, A09)',
  `name` varchar(255) NOT NULL COMMENT 'Diagnosis name',
  `description` text COLLATE utf8mb4_general_ci COMMENT 'Detailed description',
  `category` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Disease category',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_name` (`name`),
  KEY `idx_category` (`category`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add foreign key columns to consultations table for diagnosis codes
ALTER TABLE `consultations`
ADD COLUMN `preliminary_diagnosis_id` int DEFAULT NULL COMMENT 'FK to icd_codes for preliminary diagnosis' AFTER `diagnosis`,
ADD COLUMN `final_diagnosis_id` int DEFAULT NULL COMMENT 'FK to icd_codes for final diagnosis' AFTER `final_diagnosis`,
ADD KEY `fk_preliminary_diagnosis` (`preliminary_diagnosis_id`),
ADD KEY `fk_final_diagnosis` (`final_diagnosis_id`),
ADD CONSTRAINT `consultations_ibfk_preliminary_diagnosis` FOREIGN KEY (`preliminary_diagnosis_id`) REFERENCES `icd_codes` (`id`) ON DELETE SET NULL,
ADD CONSTRAINT `consultations_ibfk_final_diagnosis` FOREIGN KEY (`final_diagnosis_id`) REFERENCES `icd_codes` (`id`) ON DELETE SET NULL;

-- Insert common diagnoses for Tanzania (focusing on NMCP priority diseases)
-- Based on Tanzania HMIS and NMCP reporting requirements

INSERT INTO `icd_codes` (`code`, `name`, `description`, `category`) VALUES
-- Malaria (NMCP Priority)
('B50', 'Plasmodium falciparum malaria', 'Malaria due to Plasmodium falciparum', 'Parasitic Diseases'),
('B51', 'Plasmodium vivax malaria', 'Malaria due to Plasmodium vivax', 'Parasitic Diseases'),
('B52', 'Plasmodium malariae malaria', 'Malaria due to Plasmodium malariae', 'Parasitic Diseases'),
('B53', 'Other parasitologically confirmed malaria', 'Other specified malaria with parasitological confirmation', 'Parasitic Diseases'),
('B54', 'Unspecified malaria', 'Malaria, unspecified', 'Parasitic Diseases'),

-- Respiratory Infections
('J00', 'Acute nasopharyngitis (common cold)', 'Common cold', 'Respiratory Diseases'),
('J01', 'Acute sinusitis', 'Acute sinusitis', 'Respiratory Diseases'),
('J02', 'Acute pharyngitis', 'Acute sore throat', 'Respiratory Diseases'),
('J03', 'Acute tonsillitis', 'Acute tonsillitis', 'Respiratory Diseases'),
('J06', 'Acute upper respiratory infection', 'Upper respiratory tract infection (URTI)', 'Respiratory Diseases'),
('J18', 'Pneumonia, unspecified organism', 'Pneumonia', 'Respiratory Diseases'),
('J20', 'Acute bronchitis', 'Acute bronchitis', 'Respiratory Diseases'),
('J21', 'Acute bronchiolitis', 'Acute bronchiolitis', 'Respiratory Diseases'),
('J45', 'Asthma', 'Asthma', 'Respiratory Diseases'),

-- Gastrointestinal Diseases
('A00', 'Cholera', 'Cholera', 'Infectious Diseases'),
('A01', 'Typhoid and paratyphoid fevers', 'Typhoid fever', 'Infectious Diseases'),
('A02', 'Other salmonella infections', 'Salmonellosis', 'Infectious Diseases'),
('A03', 'Shigellosis', 'Dysentery', 'Infectious Diseases'),
('A04', 'Other bacterial intestinal infections', 'Bacterial diarrhea', 'Infectious Diseases'),
('A06', 'Amoebiasis', 'Amoebic dysentery', 'Parasitic Diseases'),
('A07', 'Other protozoal intestinal diseases', 'Giardiasis and other protozoal diarrhea', 'Parasitic Diseases'),
('A08', 'Viral and other specified intestinal infections', 'Viral gastroenteritis', 'Infectious Diseases'),
('A09', 'Diarrhea and gastroenteritis', 'Diarrhea, unspecified', 'Infectious Diseases'),
('K29', 'Gastritis and duodenitis', 'Gastritis', 'Digestive Diseases'),
('K30', 'Functional dyspepsia', 'Indigestion', 'Digestive Diseases'),
('K59.1', 'Functional diarrhea', 'Functional diarrhea', 'Digestive Diseases'),

-- HIV/AIDS and TB
('B20', 'HIV disease', 'HIV disease resulting in infectious and parasitic diseases', 'Infectious Diseases'),
('B24', 'Unspecified HIV disease', 'HIV disease without specification', 'Infectious Diseases'),
('A15', 'Respiratory tuberculosis', 'Pulmonary tuberculosis', 'Infectious Diseases'),
('A16', 'Respiratory tuberculosis, not confirmed', 'Pulmonary tuberculosis, not bacteriologically or histologically confirmed', 'Infectious Diseases'),
('A17', 'Tuberculosis of nervous system', 'TB meningitis', 'Infectious Diseases'),
('A18', 'Tuberculosis of other organs', 'Extrapulmonary tuberculosis', 'Infectious Diseases'),
('A19', 'Miliary tuberculosis', 'Disseminated TB', 'Infectious Diseases'),

-- Skin Diseases
('B35', 'Dermatophytosis', 'Fungal skin infection (Ringworm)', 'Skin Diseases'),
('L20', 'Atopic dermatitis', 'Eczema', 'Skin Diseases'),
('L30', 'Other dermatitis', 'Dermatitis, unspecified', 'Skin Diseases'),
('L08', 'Other local infections of skin', 'Skin infection (Pyoderma)', 'Skin Diseases'),
('B86', 'Scabies', 'Scabies', 'Parasitic Diseases'),

-- Urinary Tract Infections
('N30', 'Cystitis', 'Bladder infection (Cystitis)', 'Genitourinary Diseases'),
('N39.0', 'Urinary tract infection', 'UTI, site not specified', 'Genitourinary Diseases'),
('N10', 'Acute pyelonephritis', 'Kidney infection', 'Genitourinary Diseases'),

-- Hypertension and Cardiovascular
('I10', 'Essential (primary) hypertension', 'High blood pressure', 'Cardiovascular Diseases'),
('I11', 'Hypertensive heart disease', 'Hypertensive heart disease', 'Cardiovascular Diseases'),
('I25', 'Chronic ischaemic heart disease', 'Ischemic heart disease', 'Cardiovascular Diseases'),

-- Diabetes
('E11', 'Type 2 diabetes mellitus', 'Type 2 diabetes', 'Endocrine Diseases'),
('E10', 'Type 1 diabetes mellitus', 'Type 1 diabetes', 'Endocrine Diseases'),

-- Other Common Conditions
('R50', 'Fever of unknown origin', 'Fever, unspecified', 'Symptoms and Signs'),
('R51', 'Headache', 'Headache', 'Symptoms and Signs'),
('R10', 'Abdominal and pelvic pain', 'Abdominal pain', 'Symptoms and Signs'),
('M25.5', 'Pain in joint', 'Joint pain (Arthralgia)', 'Musculoskeletal Diseases'),
('M79.1', 'Myalgia', 'Muscle pain', 'Musculoskeletal Diseases'),

-- Helminth Infections
('B76', 'Hookworm disease', 'Hookworm infection', 'Parasitic Diseases'),
('B77', 'Ascariasis', 'Roundworm infection', 'Parasitic Diseases'),
('B65', 'Schistosomiasis', 'Bilharzia', 'Parasitic Diseases'),

-- Eye Infections
('H10', 'Conjunctivitis', 'Pink eye (Conjunctivitis)', 'Eye Diseases'),

-- Ear Infections
('H66', 'Suppurative and unspecified otitis media', 'Ear infection (Otitis media)', 'Ear Diseases'),

-- Anemia
('D50', 'Iron deficiency anaemia', 'Iron deficiency anemia', 'Blood Diseases'),
('D64.9', 'Anaemia, unspecified', 'Anemia, unspecified', 'Blood Diseases'),

-- Pregnancy and Maternal Conditions
('O26', 'Maternal care for other conditions', 'Pregnancy complications', 'Pregnancy Conditions'),
('Z34', 'Supervision of normal pregnancy', 'Antenatal care (Normal pregnancy)', 'Pregnancy Conditions'),

-- STIs
('A54', 'Gonococcal infection', 'Gonorrhea', 'Infectious Diseases'),
('A56', 'Other sexually transmitted chlamydial diseases', 'Chlamydia', 'Infectious Diseases'),
('A60', 'Anogenital herpesviral infection', 'Genital herpes', 'Infectious Diseases');

-- Note: This is a starter set. More ICD codes can be added as needed through the admin interface.

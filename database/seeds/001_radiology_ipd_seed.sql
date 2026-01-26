-- Seed Data: Initial data for Radiology and IPD modules
-- Date: 2026-01-24
-- Description: Populates test categories, sample tests, wards, beds, and permissions

-- ============================================
-- RADIOLOGY SEED DATA
-- ============================================

-- Radiology Categories
INSERT INTO `radiology_test_categories` (`category_name`, `category_code`, `description`) VALUES
('X-Ray', 'XRAY', 'Radiography imaging using X-ray technology'),
('Ultrasound', 'US', 'Ultrasound/sonography imaging'),
('CT Scan', 'CT', 'Computed Tomography (CT) scans'),
('MRI', 'MRI', 'Magnetic Resonance Imaging');

-- Sample Radiology Tests
INSERT INTO `radiology_tests` (`test_name`, `test_code`, `category_id`, `price`, `description`, `estimated_duration`) VALUES
-- X-Ray Tests
('Chest X-Ray (PA view)', 'XRAY-CHEST-PA', 1, 30000.00, 'Posteroanterior chest radiograph', 15),
('Chest X-Ray (Lateral)', 'XRAY-CHEST-LAT', 1, 35000.00, 'Lateral chest radiograph', 15),
('Abdominal X-Ray', 'XRAY-ABD', 1, 40000.00, 'Abdominal plain film', 15),
('Skull X-Ray', 'XRAY-SKULL', 1, 45000.00, 'Skull radiograph', 20),
('Spine X-Ray (Lumbar)', 'XRAY-SPINE-L', 1, 50000.00, 'Lumbar spine radiograph', 20),
('Hand X-Ray', 'XRAY-HAND', 1, 25000.00, 'Hand/wrist radiograph', 10),
('Foot X-Ray', 'XRAY-FOOT', 1, 25000.00, 'Foot/ankle radiograph', 10),

-- Ultrasound Tests
('Abdominal Ultrasound', 'US-ABD', 2, 40000.00, 'Complete abdominal ultrasound', 30),
('Pelvic Ultrasound', 'US-PELV', 2, 40000.00, 'Pelvic/gynecological ultrasound', 30),
('Obstetric Ultrasound', 'US-OBS', 2, 50000.00, 'Pregnancy ultrasound scan', 30),
('Breast Ultrasound', 'US-BREAST', 2, 45000.00, 'Breast ultrasound', 25),
('Thyroid Ultrasound', 'US-THYROID', 2, 40000.00, 'Thyroid gland ultrasound', 20),

-- CT Scans
('Head CT Scan', 'CT-HEAD', 3, 150000.00, 'Non-contrast head CT', 20),
('Chest CT Scan', 'CT-CHEST', 3, 180000.00, 'Chest CT with contrast', 25),
('Abdominal CT Scan', 'CT-ABD', 3, 200000.00, 'Abdominal/pelvic CT with contrast', 30),

-- MRI Scans
('Brain MRI', 'MRI-BRAIN', 4, 350000.00, 'Brain MRI with/without contrast', 45),
('Spine MRI (Lumbar)', 'MRI-SPINE-L', 4, 300000.00, 'Lumbar spine MRI', 40);

-- ============================================
-- IPD SEED DATA
-- ============================================

-- IPD Wards
INSERT INTO `ipd_wards` (`ward_name`, `ward_code`, `ward_type`, `total_beds`, `floor_number`, `description`) VALUES
('General Ward A', 'GEN-A', 'general', 20, 1, 'General admission ward for mixed patients'),
('General Ward B', 'GEN-B', 'general', 20, 1, 'General admission ward for mixed patients'),
('Private Ward', 'PRIV-1', 'private', 10, 2, 'Private single-occupancy rooms'),
('ICU', 'ICU-1', 'icu', 6, 3, 'Intensive Care Unit with monitoring'),
('Maternity Ward', 'MAT-1', 'maternity', 15, 2, 'Maternity and post-natal care'),
('Pediatric Ward', 'PED-1', 'pediatric', 12, 2, 'Children and infant ward');

-- Sample Beds for General Ward A
INSERT INTO `ipd_beds` (`ward_id`, `bed_number`, `bed_type`, `daily_rate`, `status`) VALUES
-- General Ward A
(1, 'A-01', 'standard', 15000.00, 'available'),
(1, 'A-02', 'standard', 15000.00, 'available'),
(1, 'A-03', 'oxygen', 20000.00, 'available'),
(1, 'A-04', 'standard', 15000.00, 'available'),
(1, 'A-05', 'standard', 15000.00, 'available'),
(1, 'A-06', 'standard', 15000.00, 'available'),
(1, 'A-07', 'oxygen', 20000.00, 'available'),
(1, 'A-08', 'standard', 15000.00, 'available'),
(1, 'A-09', 'standard', 15000.00, 'available'),
(1, 'A-10', 'standard', 15000.00, 'available'),

-- General Ward B
(2, 'B-01', 'standard', 15000.00, 'available'),
(2, 'B-02', 'standard', 15000.00, 'available'),
(2, 'B-03', 'standard', 15000.00, 'available'),
(2, 'B-04', 'oxygen', 20000.00, 'available'),
(2, 'B-05', 'standard', 15000.00, 'available'),

-- Private Ward
(3, 'P-01', 'standard', 50000.00, 'available'),
(3, 'P-02', 'standard', 50000.00, 'available'),
(3, 'P-03', 'standard', 50000.00, 'available'),
(3, 'P-04', 'standard', 50000.00, 'available'),
(3, 'P-05', 'standard', 50000.00, 'available'),

-- ICU
(4, 'ICU-01', 'icu', 150000.00, 'available'),
(4, 'ICU-02', 'icu', 150000.00, 'available'),
(4, 'ICU-03', 'icu', 150000.00, 'available'),
(4, 'ICU-04', 'icu', 150000.00, 'available'),

-- Maternity Ward
(5, 'MAT-01', 'standard', 25000.00, 'available'),
(5, 'MAT-02', 'standard', 25000.00, 'available'),
(5, 'MAT-03', 'standard', 25000.00, 'available'),
(5, 'MAT-04', 'standard', 25000.00, 'available'),
(5, 'MAT-05', 'standard', 25000.00, 'available'),

-- Pediatric Ward
(6, 'PED-01', 'standard', 18000.00, 'available'),
(6, 'PED-02', 'oxygen', 23000.00, 'available'),
(6, 'PED-03', 'standard', 18000.00, 'available'),
(6, 'PED-04', 'standard', 18000.00, 'available');

-- ============================================
-- ROLE PERMISSIONS
-- ============================================

-- Radiologist Permissions
INSERT INTO `role_permissions` (`role`, `permission`) VALUES
('radiologist', 'dashboard.view'),
('radiologist', 'patients.view'),
('radiologist', 'radiology.dashboard'),
('radiologist', 'radiology.view_orders'),
('radiologist', 'radiology.perform_test'),
('radiologist', 'radiology.record_result'),
('radiologist', 'radiology.view_result'),
('radiologist', 'radiology.upload_images');

-- Nurse Permissions
INSERT INTO `role_permissions` (`role`, `permission`) VALUES
('nurse', 'dashboard.view'),
('nurse', 'patients.view'),
('nurse', 'ipd.dashboard'),
('nurse', 'ipd.view_admissions'),
('nurse', 'ipd.record_vitals'),
('nurse', 'ipd.progress_notes'),
('nurse', 'ipd.administer_medication'),
('nurse', 'ipd.view_medication_schedule');

-- Verify the seed data
SELECT 'Seed data loaded successfully' AS status;
SELECT 
  (SELECT COUNT(*) FROM radiology_test_categories) AS categories,
  (SELECT COUNT(*) FROM radiology_tests) AS radiology_tests,
  (SELECT COUNT(*) FROM ipd_wards) AS wards,
  (SELECT COUNT(*) FROM ipd_beds) AS beds,
  (SELECT COUNT(*) FROM role_permissions WHERE role IN ('radiologist', 'nurse')) AS new_permissions;

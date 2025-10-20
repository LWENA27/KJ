-- Migration: Add unique index to prevent duplicate visit numbers per patient
-- Run this on your database (backup first):

ALTER TABLE `patient_visits`
ADD CONSTRAINT `uniq_patient_visit_number` UNIQUE (`patient_id`, `visit_number`);

-- Note: If existing duplicate rows exist, this statement will fail. Run a cleanup query
-- to remove/merge duplicates before applying, or adjust duplicates manually.

-- Optional: Add an index on (patient_id, visit_date, status) to speed up "active visits today" checks
CREATE INDEX idx_patient_date_status ON `patient_visits` (`patient_id`, `visit_date`, `status`);

-- Add preliminary_diagnosis and final_diagnosis columns to consultations table
ALTER TABLE consultations
ADD COLUMN preliminary_diagnosis TEXT COLLATE utf8mb4_general_ci AFTER diagnosis,
ADD COLUMN final_diagnosis TEXT COLLATE utf8mb4_general_ci AFTER preliminary_diagnosis;
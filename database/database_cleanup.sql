-- KJ Medical System Database Cleanup Script
-- Remove redundant tables and consolidate schema

-- Backup existing data before cleanup
CREATE TABLE IF NOT EXISTS backup_lab_test_orders AS SELECT * FROM lab_test_orders;
CREATE TABLE IF NOT EXISTS backup_payments AS SELECT * FROM payments;

-- Drop redundant/empty tables
DROP TABLE IF EXISTS lab_orders;
DROP TABLE IF EXISTS medicine_prescriptions; 
DROP TABLE IF EXISTS prescriptions;
DROP TABLE IF EXISTS detailed_payments;
DROP TABLE IF EXISTS patient_payments;
DROP TABLE IF EXISTS step_payments;

-- Drop unnecessary workflow tables (consolidated into patient_queue)
DROP TABLE IF EXISTS patient_workflow_status;
DROP TABLE IF EXISTS patient_workflow_summary;
DROP TABLE IF EXISTS workflow_status;

-- Drop lab equipment tables (not needed for basic system)
DROP TABLE IF EXISTS lab_equipment;
DROP TABLE IF EXISTS lab_inventory;
DROP TABLE IF EXISTS lab_quality_control;
DROP TABLE IF EXISTS lab_samples;

-- Consolidate lab test management
-- Use lab_test_orders as primary lab orders table
ALTER TABLE lab_test_orders 
ADD COLUMN IF NOT EXISTS doctor_id INT,
ADD COLUMN IF NOT EXISTS total_amount DECIMAL(10,2) DEFAULT 0,
ADD COLUMN IF NOT EXISTS payment_status ENUM('pending', 'partial', 'completed') DEFAULT 'pending',
ADD COLUMN IF NOT EXISTS doctor_notes TEXT,
ADD COLUMN IF NOT EXISTS priority ENUM('normal', 'urgent', 'stat') DEFAULT 'normal',
ADD COLUMN IF NOT EXISTS tests_requested JSON,
ADD FOREIGN KEY IF NOT EXISTS (doctor_id) REFERENCES users(id);

-- Create medicine_orders table for receptionist management
CREATE TABLE IF NOT EXISTS medicine_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    consultation_id INT,
    doctor_id INT NOT NULL,
    receptionist_id INT,
    medicines JSON NOT NULL COMMENT 'Array of medicines with quantities, dosages, instructions',
    total_amount DECIMAL(10,2) NOT NULL,
    paid_amount DECIMAL(10,2) DEFAULT 0,
    payment_status ENUM('pending', 'partial', 'completed') DEFAULT 'pending',
    doctor_notes TEXT,
    dispensing_status ENUM('pending', 'partial', 'completed', 'cancelled') DEFAULT 'pending',
    dispensing_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (doctor_id) REFERENCES users(id),
    FOREIGN KEY (receptionist_id) REFERENCES users(id),
    INDEX idx_patient_medicine (patient_id),
    INDEX idx_doctor_medicine (doctor_id),
    INDEX idx_payment_status (payment_status),
    INDEX idx_dispensing_status (dispensing_status)
);

-- Consolidate payments table (keep main payments table, enhance it)
ALTER TABLE payments 
ADD COLUMN IF NOT EXISTS payment_type ENUM('consultation', 'lab_tests', 'medicines') NOT NULL DEFAULT 'consultation',
ADD COLUMN IF NOT EXISTS reference_id INT COMMENT 'References lab_test_orders.id or medicine_orders.id',
ADD COLUMN IF NOT EXISTS notes TEXT;

-- Update patient_queue for better workflow management
ALTER TABLE patient_queue 
ADD COLUMN IF NOT EXISTS priority ENUM('normal', 'urgent', 'emergency') DEFAULT 'normal',
ADD COLUMN IF NOT EXISTS assigned_staff_id INT COMMENT 'ID of staff member handling patient',
ADD FOREIGN KEY IF NOT EXISTS (assigned_staff_id) REFERENCES users(id);

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_patients_phone ON patients(phone);
CREATE INDEX IF NOT EXISTS idx_patients_name ON patients(first_name, last_name);
CREATE INDEX IF NOT EXISTS idx_consultations_date ON consultations(appointment_date);
CREATE INDEX IF NOT EXISTS idx_payments_date ON payments(payment_date);
CREATE INDEX IF NOT EXISTS idx_queue_status ON patient_queue(current_status);

-- Update users table for role-based access
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE,
ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS department ENUM('administration', 'clinical', 'laboratory', 'pharmacy', 'reception') DEFAULT 'clinical';

-- Update department based on role
UPDATE users SET department = 'clinical' WHERE role = 'doctor';
UPDATE users SET department = 'reception' WHERE role = 'receptionist';
UPDATE users SET department = 'laboratory' WHERE role = 'lab_technician';
UPDATE users SET department = 'administration' WHERE role = 'admin';

-- Create consultation_fees table for transparent fee management
CREATE TABLE IF NOT EXISTS consultation_fees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fee_name VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default consultation fee
INSERT IGNORE INTO consultation_fees (fee_name, amount) VALUES ('Standard Consultation', 3000.00);

-- Show final table structure
SELECT 'Database cleanup completed successfully' as status;

-- Production Cleanup & Hardening Script (Idempotent)
-- Safely clears payments and ensures production constraints

-- 1. Clear payments table (all data deleted)
TRUNCATE TABLE payments;

-- 2. Verify payment_method ENUM is correct (if not, update)
-- Current: enum('cash','card','mobile_money','insurance')
-- Update to include all methods
ALTER TABLE payments MODIFY COLUMN payment_method ENUM('cash', 'card', 'mobile_money', 'insurance', 'cheque', 'transfer', 'other') NOT NULL DEFAULT 'cash';

-- 3. Verify payment_type values are valid (stored as VARCHAR, validated in app)
ALTER TABLE payments MODIFY COLUMN payment_type VARCHAR(50) NOT NULL;

-- 4. Insert system user if not exists
INSERT IGNORE INTO users (id, first_name, last_name, email, role, password_hash)
VALUES (0, 'System', 'Collector', 'system@zahanati.local', 'system', '');

-- 5. Verify payments table is clean
SELECT 'Payments table reset and production-ready' as status;
SELECT COUNT(*) as payment_count FROM payments;
SELECT id, first_name, email FROM users WHERE id = 0;

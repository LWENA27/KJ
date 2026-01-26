-- Quick Test User Creation Script for IPD & Radiology Modules
-- Run this script to create test users for module testing

USE zahanati;

-- Create test radiologist user
-- Username: radiologist1, Password: password
INSERT INTO users (username, password_hash, first_name, last_name, email, role, is_active, created_at)
VALUES ('radiologist1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
        'Sarah', 'Johnson', 'radiologist@hospital.com', 'radiologist', 1, NOW())
ON DUPLICATE KEY UPDATE username=username;

-- Create test nurse user
-- Username: nurse1, Password: password
INSERT INTO users (username, password_hash, first_name, last_name, email, role, is_active, created_at)
VALUES ('nurse1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
        'Mary', 'Williams', 'nurse@hospital.com', 'nurse', 1, NOW())
ON DUPLICATE KEY UPDATE username=username;

-- Add nurse role to existing receptionist user (for multi-role testing)
-- First, get the receptionist user ID
SET @receptionist_id = (SELECT id FROM users WHERE role = 'receptionist' LIMIT 1);

-- Add nurse role if receptionist exists
INSERT INTO user_roles (user_id, role, is_primary, granted_by, is_active, granted_at)
SELECT @receptionist_id, 'nurse', 0, 1, 1, NOW()
FROM DUAL
WHERE @receptionist_id IS NOT NULL
AND NOT EXISTS (
    SELECT 1 FROM user_roles 
    WHERE user_id = @receptionist_id AND role = 'nurse'
);

-- Verify created users
SELECT 
    u.id,
    u.username,
    u.first_name,
    u.last_name,
    u.role as primary_role,
    GROUP_CONCAT(ur.role ORDER BY ur.is_primary DESC SEPARATOR ', ') as all_roles
FROM users u
LEFT JOIN user_roles ur ON u.id = ur.user_id AND ur.is_active = 1
WHERE u.role IN ('radiologist', 'nurse', 'receptionist')
GROUP BY u.id, u.username, u.first_name, u.last_name, u.role;

-- Show summary
SELECT 'Test users created successfully!' as Status;
SELECT 'Default password for all test users: password' as Note;
SELECT 'Radiologist: radiologist1 / password' as Radiologist_Login;
SELECT 'Nurse: nurse1 / password' as Nurse_Login;
SELECT CONCAT('Receptionist with Nurse role: ', username, ' / password') as Multi_Role_User
FROM users WHERE role = 'receptionist' LIMIT 1;

-- Migration: Add 'nurse' and 'radiologist' roles to all ENUM columns
-- Date: 2026-01-24
-- Description: Updates role ENUMs across users, user_roles, and role_permissions tables

-- Update users table role ENUM
ALTER TABLE `users` 
MODIFY COLUMN `role` ENUM(
  'admin','receptionist','doctor','lab_technician',
  'accountant','pharmacist','radiologist','nurse'
) NOT NULL;

-- Update user_roles table role ENUM
ALTER TABLE `user_roles` 
MODIFY COLUMN `role` ENUM(
  'admin','receptionist','doctor','lab_technician',
  'accountant','pharmacist','radiologist','nurse'
) NOT NULL;

-- Update role_permissions table role ENUM
ALTER TABLE `role_permissions` 
MODIFY COLUMN `role` ENUM(
  'admin','receptionist','doctor','lab_technician',
  'accountant','pharmacist','radiologist','nurse'
) NOT NULL;

-- Verify the changes
SELECT 'Migration 001 completed successfully' AS status;

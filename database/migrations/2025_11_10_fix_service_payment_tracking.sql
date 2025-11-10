-- Migration to fix service payment tracking
-- Date: 2025-11-10
-- Issue: Service payments were not properly linked to service_orders

-- Add 'service_order' to item_type enum
ALTER TABLE payments 
MODIFY COLUMN item_type ENUM('lab_order','prescription','service','service_order') NULL;

-- Add 'service' to payment_type enum
ALTER TABLE payments 
MODIFY COLUMN payment_type ENUM('registration','lab_test','medicine','minor_service','service') NOT NULL;

-- Note: Existing payment records were manually corrected to use:
-- - item_type = 'service_order' (instead of 'service')
-- - item_id = <service_order_id> (instead of service_id)

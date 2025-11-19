-- Add admin price column to tms_service table
-- This allows admin to set fixed prices for services
-- If admin price is set, it will be used instead of the default service price
-- If admin price is NULL, technicians can set the price during service completion

ALTER TABLE tms_service 
ADD COLUMN IF NOT EXISTS s_admin_price DECIMAL(10,2) DEFAULT NULL 
COMMENT 'Admin-set fixed price in Indian Rupees. If NULL, technician sets price during completion';

-- Update existing bookings to use admin prices where available
-- This will only affect pending/in-progress bookings, not completed ones
UPDATE tms_service_booking sb
INNER JOIN tms_service s ON sb.sb_service_id = s.s_id
SET sb.sb_total_price = s.s_admin_price
WHERE s.s_admin_price IS NOT NULL 
  AND s.s_admin_price > 0
  AND sb.sb_status NOT IN ('Completed', 'Cancelled');

-- Add columns to track price source and technician-set prices
-- This ensures technician-decided prices are only shown for that specific booking

-- Add column to track if final price was set by technician
ALTER TABLE tms_service_booking 
ADD COLUMN IF NOT EXISTS sb_price_set_by_tech TINYINT(1) DEFAULT 0 
COMMENT 'Whether the final price was set by technician (1) or admin (0)';

-- Add column to store technician-decided price separately
ALTER TABLE tms_service_booking 
ADD COLUMN IF NOT EXISTS sb_tech_decided_price DECIMAL(10,2) DEFAULT NULL 
COMMENT 'Price decided by technician during completion (only for this booking)';

-- Ensure sb_final_price column exists
ALTER TABLE tms_service_booking 
ADD COLUMN IF NOT EXISTS sb_final_price DECIMAL(10,2) DEFAULT NULL 
COMMENT 'Final price charged for the service';

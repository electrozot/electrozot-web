-- Add Aadhaar column to tms_technician table
-- This script adds the t_aadhar column if it doesn't already exist

-- Add Aadhaar column
ALTER TABLE tms_technician 
ADD COLUMN IF NOT EXISTS t_aadhar VARCHAR(12) DEFAULT NULL AFTER t_phone;

-- Add unique index on Aadhaar to prevent duplicates
ALTER TABLE tms_technician 
ADD UNIQUE INDEX idx_t_aadhar (t_aadhar);

-- Add address column if it doesn't exist (for ID card display)
ALTER TABLE tms_technician 
ADD COLUMN IF NOT EXISTS t_addr VARCHAR(500) DEFAULT NULL;

-- Add email column if it doesn't exist (for ID card display)
ALTER TABLE tms_technician 
ADD COLUMN IF NOT EXISTS t_email VARCHAR(200) DEFAULT NULL;

-- Display success message
SELECT 'Aadhaar column added successfully to tms_technician table' AS Status;

-- Fix booking foreign key constraint
-- Run this in phpMyAdmin

USE `electrozot_db`;

-- Remove the foreign key constraint
ALTER TABLE `tms_service_booking` 
DROP FOREIGN KEY `tms_service_booking_ibfk_2`;

-- Now sb_service_id can be 0 or any value
-- The booking will work without requiring a valid service ID

SELECT 'Foreign key constraint removed successfully!' AS Status;

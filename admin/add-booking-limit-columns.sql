-- Add booking limit columns to technician table
-- Run this SQL to add the booking limit feature

ALTER TABLE `tms_technician` 
ADD COLUMN `t_booking_limit` INT NOT NULL DEFAULT 1 COMMENT 'Maximum concurrent bookings (1-5)' AFTER `t_status`,
ADD COLUMN `t_current_bookings` INT NOT NULL DEFAULT 0 COMMENT 'Current active bookings count' AFTER `t_booking_limit`;

-- Update existing technicians to have default limit of 1
UPDATE `tms_technician` SET `t_booking_limit` = 1, `t_current_bookings` = 0;

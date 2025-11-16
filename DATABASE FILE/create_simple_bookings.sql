-- Create a simple bookings table without foreign key constraints
-- Run this in phpMyAdmin

USE `electrozot_db`;

-- Create new simple bookings table
CREATE TABLE IF NOT EXISTS `tms_bookings` (
  `booking_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `user_name` varchar(200) NOT NULL,
  `user_phone` varchar(20) NOT NULL,
  `service_category` varchar(200) NOT NULL,
  `service_subcategory` varchar(200) NOT NULL,
  `service_name` varchar(200) NOT NULL,
  `service_duration` varchar(100) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `address` text NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SELECT 'Simple bookings table created successfully!' AS Status;

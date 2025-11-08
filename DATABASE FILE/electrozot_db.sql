-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 27, 2023 at 10:00 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `electrozot_db`
--
CREATE DATABASE IF NOT EXISTS `electrozot_db`;
USE `electrozot_db`;

-- --------------------------------------------------------

--
-- Table structure for table `tms_admin`
--

CREATE TABLE `tms_admin` (
  `a_id` int NOT NULL,
  `a_name` varchar(200) NOT NULL,
  `a_email` varchar(200) NOT NULL,
  `a_pwd` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tms_admin`
--
INSERT INTO `tms_admin` (`a_id`, `a_name`, `a_email`, `a_pwd`)
VALUES (3, 'Mohit', 'mohit@electrozot.in', MD5('mohit123'));

-- --------------------------------------------------------

--
-- Table structure for table `tms_feedback`
--

CREATE TABLE `tms_feedback` (
  `f_id` int NOT NULL,
  `f_uname` varchar(200) NOT NULL,
  `f_content` longtext NOT NULL,
  `f_status` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tms_feedback`
--

INSERT INTO `tms_feedback` (`f_id`, `f_uname`, `f_content`, `f_status`) VALUES
(1, 'Elliot Gape', 'This is a demo feedback text. This is a demo feedback text. This is a demo feedback text.', 'Published'),
(2, 'Mark L. Anderson', 'Sample Feedback Text for testing! Sample Feedback Text for testing! Sample Feedback Text for testing!', 'Published'),
(3, 'Liam Moore ', 'test number 3', '');

-- --------------------------------------------------------

--
-- Table structure for table `tms_pwd_resets`
--

CREATE TABLE `tms_pwd_resets` (
  `r_id` int NOT NULL,
  `r_email` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tms_pwd_resets`
--

INSERT INTO `tms_pwd_resets` (`r_id`, `r_email`) VALUES
(2, 'admin@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `tms_syslogs`
--

CREATE TABLE `tms_syslogs` (
  `l_id` int NOT NULL,
  `u_id` varchar(200) NOT NULL,
  `u_email` varchar(200) NOT NULL,
  `u_ip` varbinary(200) NOT NULL,
  `u_city` varchar(200) NOT NULL,
  `u_country` varchar(200) NOT NULL,
  `u_logintime` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tms_user`
--

CREATE TABLE `tms_user` (
  `u_id` int NOT NULL,
  `u_fname` varchar(200) NOT NULL,
  `u_lname` varchar(200) NOT NULL,
  `u_phone` varchar(200) NOT NULL,
  `u_addr` varchar(200) NOT NULL,
  `u_category` varchar(200) NOT NULL,
  `u_email` varchar(200) NOT NULL,
  `u_pwd` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `t_tech_category` varchar(200) NOT NULL,
  `t_tech_id` varchar(200) NOT NULL,
  `t_booking_date` varchar(200) NOT NULL,
  `t_booking_status` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tms_user`
--

INSERT INTO `tms_user` (`u_id`, `u_fname`, `u_lname`, `u_phone`, `u_addr`, `u_category`, `u_email`, `u_pwd`, `t_tech_category`, `t_tech_id`, `t_booking_date`, `t_booking_status`) VALUES
(13, 'Clint', '01', '01600000000', 'Bogura,Bangladesh', 'User', 'clint@gmail.com', '123456', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `tms_technician`
--

CREATE TABLE `tms_technician` (
  `t_id` int NOT NULL,
  `t_name` varchar(200) NOT NULL,
  `t_id_no` varchar(200) NOT NULL,
  `t_experience` varchar(200) NOT NULL,
  `t_specialization` varchar(200) NOT NULL,
  `t_category` varchar(200) NOT NULL,
  `t_pic` varchar(200) NOT NULL,
  `t_status` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tms_technician`
--

INSERT INTO `tms_technician` (`t_id`, `t_name`, `t_id_no`, `t_experience`, `t_specialization`, `t_category`, `t_pic`, `t_status`) VALUES
(3, 'John Smith', 'TECH001', '10', 'Electrical Repairs', 'Electrical', 'tech1.jpg', 'Available'),
(4, 'Sarah Johnson', 'TECH002', '8', 'Plumbing Services', 'Plumbing', 'tech2.jpg', 'Available'),
(5, 'Mike Williams', 'TECH003', '12', 'HVAC Systems', 'HVAC', 'tech3.jpg', 'Available'),
(6, 'Emily Davis', 'TECH004', '5', 'Appliance Repair', 'Appliance', 'tech4.jpg', 'Available'),
(7, 'David Brown', 'TECH005', '15', 'General Maintenance', 'General', 'tech5.jpg', 'Available');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tms_admin`
--
ALTER TABLE `tms_admin`
  ADD PRIMARY KEY (`a_id`);

--
-- Indexes for table `tms_feedback`
--
ALTER TABLE `tms_feedback`
  ADD PRIMARY KEY (`f_id`);

--
-- Indexes for table `tms_pwd_resets`
--
ALTER TABLE `tms_pwd_resets`
  ADD PRIMARY KEY (`r_id`);

--
-- Indexes for table `tms_syslogs`
--
ALTER TABLE `tms_syslogs`
  ADD PRIMARY KEY (`l_id`);

--
-- Indexes for table `tms_user`
--
ALTER TABLE `tms_user`
  ADD PRIMARY KEY (`u_id`);

--
-- Indexes for table `tms_technician`
--
ALTER TABLE `tms_technician`
  ADD PRIMARY KEY (`t_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tms_admin`
--
ALTER TABLE `tms_admin`
  MODIFY `a_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tms_feedback`
--
ALTER TABLE `tms_feedback`
  MODIFY `f_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tms_pwd_resets`
--
ALTER TABLE `tms_pwd_resets`
  MODIFY `r_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tms_syslogs`
--
ALTER TABLE `tms_syslogs`
  MODIFY `l_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tms_user`
--
ALTER TABLE `tms_user`
  MODIFY `u_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tms_technician`
--
ALTER TABLE `tms_technician`
  MODIFY `t_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

-- --------------------------------------------------------

--
-- Table structure for table `tms_service`
--

CREATE TABLE `tms_service` (
  `s_id` int NOT NULL,
  `s_name` varchar(200) NOT NULL,
  `s_description` longtext NOT NULL,
  `s_category` varchar(200) NOT NULL,
  `s_price` decimal(10,2) NOT NULL,
  `s_duration` varchar(200) NOT NULL,
  `s_status` varchar(200) NOT NULL DEFAULT 'Active',
  `s_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tms_service`
--

INSERT INTO `tms_service` (`s_id`, `s_name`, `s_description`, `s_category`, `s_price`, `s_duration`, `s_status`) VALUES
(1, 'Electrical Repair', 'Complete electrical system repair and maintenance', 'Electrical', 150.00, '2-3 hours', 'Active'),
(2, 'Plumbing Service', 'Professional plumbing installation and repair', 'Plumbing', 120.00, '1-2 hours', 'Active'),
(3, 'HVAC Maintenance', 'Heating, ventilation and air conditioning service', 'HVAC', 200.00, '3-4 hours', 'Active'),
(4, 'Appliance Repair', 'Home appliance repair and maintenance', 'Appliance', 100.00, '1-2 hours', 'Active'),
(5, 'General Maintenance', 'General home maintenance and repairs', 'General', 80.00, '1-2 hours', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `tms_service_booking`
--

CREATE TABLE `tms_service_booking` (
  `sb_id` int NOT NULL,
  `sb_user_id` int NOT NULL,
  `sb_service_id` int NOT NULL,
  `sb_technician_id` int DEFAULT NULL,
  `sb_booking_date` date NOT NULL,
  `sb_booking_time` time NOT NULL,
  `sb_address` varchar(500) NOT NULL,
  `sb_phone` varchar(200) NOT NULL,
  `sb_description` longtext,
  `sb_status` varchar(200) NOT NULL DEFAULT 'Pending',
  `sb_total_price` decimal(10,2) NOT NULL,
  `sb_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sb_updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tms_service`
--
ALTER TABLE `tms_service`
  ADD PRIMARY KEY (`s_id`);

--
-- Indexes for table `tms_service_booking`
--
ALTER TABLE `tms_service_booking`
  ADD PRIMARY KEY (`sb_id`),
  ADD KEY `sb_user_id` (`sb_user_id`),
  ADD KEY `sb_service_id` (`sb_service_id`),
  ADD KEY `sb_technician_id` (`sb_technician_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tms_service`
--
ALTER TABLE `tms_service`
  MODIFY `s_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tms_service_booking`
--
ALTER TABLE `tms_service_booking`
  MODIFY `sb_id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tms_service_booking`
--
ALTER TABLE `tms_service_booking`
  ADD CONSTRAINT `tms_service_booking_ibfk_1` FOREIGN KEY (`sb_user_id`) REFERENCES `tms_user` (`u_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tms_service_booking_ibfk_2` FOREIGN KEY (`sb_service_id`) REFERENCES `tms_service` (`s_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tms_service_booking_ibfk_3` FOREIGN KEY (`sb_technician_id`) REFERENCES `tms_technician` (`t_id`) ON DELETE SET NULL;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

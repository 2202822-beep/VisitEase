-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 23, 2026 at 04:56 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `museum_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$4xNTILvgDv8t7TSxewWLv.WPUAHTNMbAr01x3Y6r9rv5xZpB1f17y', '2026-02-10 07:41:25');

-- --------------------------------------------------------

--
-- Table structure for table `admin_accounts`
--

CREATE TABLE `admin_accounts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_accounts`
--

INSERT INTO `admin_accounts` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `token` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date` date NOT NULL,
  `time` varchar(20) NOT NULL,
  `guests` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `visit_date` date DEFAULT NULL,
  `visit_time` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `special_request` text DEFAULT NULL,
  `gcash_name` varchar(255) NOT NULL,
  `gcash_ref` varchar(255) NOT NULL,
  `gcash_receipt` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `token`, `name`, `email`, `phone`, `date`, `time`, `guests`, `schedule_id`, `visit_date`, `visit_time`, `status`, `created_at`, `special_request`, `gcash_name`, `gcash_ref`, `gcash_receipt`) VALUES
(10, 'VST-82682A2', 'sadas', NULL, NULL, '2026-03-23', '11:00', 1, 0, NULL, NULL, 'Confirmed', '2026-02-10 06:20:25', 'wala', 'Ace', '12345', NULL),
(11, 'VST-7FC7085', 'Ace Earl Jairus P. Natividad', NULL, NULL, '2026-08-23', '09:00', 2, 0, NULL, NULL, 'Confirmed', '2026-02-10 06:28:35', 'WALA', 'Ace Earl Jairus P. Natividad', '12345', NULL),
(12, 'VST-9ABECD8', 'David Pagcaulaga', NULL, NULL, '2026-02-23', '11:00', 3, 0, NULL, NULL, 'Confirmed', '2026-02-10 08:01:24', '', 'Ace Earl Jairus P. Natividad', '12345', NULL),
(13, 'VST-2A95F36', 'SIR KELAN MAKUKUMPLETO', NULL, NULL, '2026-02-24', '11:00', 2, 0, NULL, NULL, 'Confirmed', '2026-02-11 00:56:08', 'Waley', 'Ace Earl Jairus P. Natividad', '2468', NULL),
(15, 'C2E943', 'Ace Earl Jairus P. Natividad', NULL, NULL, '2026-02-11', '12:00:00', 1, 0, NULL, NULL, 'Rejected', '2026-02-11 01:13:15', NULL, '', '', NULL),
(16, 'A71C4F', 'Ace Earl Jairus P. Natividad', NULL, NULL, '2026-02-13', '08:00:00', 1, 0, NULL, NULL, 'Rejected', '2026-02-11 01:14:31', NULL, '', '', NULL),
(17, 'B47282', 'Ace Earl Jairus P. Natividad', NULL, NULL, '2026-02-11', '12:00:00', 1, 0, NULL, NULL, 'Rejected', '2026-02-11 01:17:01', NULL, '', '', NULL),
(19, '8EC84C', 'Ace Earl Jairus P. Natividad', NULL, NULL, '2026-02-11', '12:00:00', 2, 0, NULL, NULL, 'Rejected', '2026-02-11 01:22:21', NULL, '', '', NULL),
(20, '53F47D', 'Ace Earl Jairus P. Natividad', NULL, NULL, '2026-02-11', '12:00:00', 2, 0, NULL, NULL, 'Rejected', '2026-02-11 01:22:30', NULL, '', '', NULL),
(33, 'F732F3BB', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '2026-02-24', '10:30:00', 1, 0, NULL, NULL, 'Confirmed', '2026-02-11 05:41:22', NULL, 'Ace Earl Jairus P. Natividad', '893298', NULL),
(34, '2970EA6A', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '2026-02-24', '10:30:00', 1, 0, NULL, NULL, 'Confirmed', '2026-02-11 05:41:31', NULL, 'Ace Earl Jairus P. Natividad', '893298', NULL),
(35, '25B79BB7', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '2026-02-24', '10:30:00', 1, 0, NULL, NULL, 'Confirmed', '2026-02-11 05:41:35', '', 'Hannah Jane Barot', '893298', NULL),
(36, '2B08ABC1', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '2026-02-24', '10:30:00', 1, 0, NULL, NULL, 'Rejected', '2026-02-11 05:43:49', NULL, 'Hannah Jane Barot', '893298', NULL),
(37, '1BE5624E', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '0000-00-00', '', 1, 1, NULL, NULL, 'Confirmed', '2026-02-11 05:52:13', 'SAQSQ', 'Hannah Jane Barot', '893298', NULL),
(38, '3613AE84', 'Hannah Jane Barot', 'aceearljairus@gmai.com', '09812252462', '0000-00-00', '', 1, 1, NULL, NULL, 'Rejected', '2026-02-13 00:56:57', '', 'Hannah Jane Barot', '893298', NULL),
(39, '260EA6AF', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '09812252462', '0000-00-00', '', 2, 1, NULL, NULL, 'Rejected', '2026-02-13 01:06:11', '', 'Ace Earl Jairus P. Natividad', '893298', NULL),
(40, '0F3F2114', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '0000-00-00', '', 5, 6, NULL, NULL, 'Rejected', '2026-02-13 01:20:20', '', 'Hannah Jane Barot', '893298', NULL),
(41, 'C36AC18A', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '0000-00-00', '', 5, 6, NULL, NULL, 'Rejected', '2026-02-13 01:20:29', '', 'Hannah Jane Barot', '893298', NULL),
(42, 'EB71582F', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '0000-00-00', '', 5, 6, NULL, NULL, 'Rejected', '2026-02-13 01:20:32', '', 'Hannah Jane Barot', '893298', NULL),
(43, 'ED2F41A0', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '0000-00-00', '', 5, 6, NULL, NULL, 'Rejected', '2026-02-13 01:20:38', '', 'Hannah Jane Barot', '893298', NULL),
(44, 'D396D521', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '0000-00-00', '', 5, 6, NULL, NULL, 'Rejected', '2026-02-13 01:21:05', '', 'Hannah Jane Barot', '893298', NULL),
(45, '0A29E3B7', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '0000-00-00', '', 1, 3, NULL, NULL, 'Rejected', '2026-02-13 01:30:56', '', 'Ace Earl Jairus P. Natividad', '893298', NULL),
(46, '24BC0568', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '09812252462', '0000-00-00', '', 1, 3, NULL, NULL, 'Rejected', '2026-02-13 01:42:07', '', 'Ace Earl Jairus P. Natividad', '893298', NULL),
(47, '66AC797B', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '0000-00-00', '', 1, 1, NULL, NULL, 'Rejected', '2026-02-13 01:49:45', '', 'Ace Earl Jairus P. Natividad', '893298', NULL),
(48, '300A4E2D', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '0000-00-00', '', 1, 1, NULL, NULL, 'Rejected', '2026-02-13 01:50:03', '', 'Ace Earl Jairus P. Natividad', '893298', NULL),
(49, '13768FD8', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '0000-00-00', '', 1, 1, NULL, NULL, 'Rejected', '2026-02-13 01:50:16', '', 'Ace Earl Jairus P. Natividad', '893298', NULL),
(50, '77E7C395', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '0000-00-00', '', 1, 1, NULL, NULL, 'Confirmed', '2026-02-13 05:17:55', '', 'Ace Earl Jairus P. Natividad', '12345678', NULL),
(51, '1666CB58', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '09812252462', '0000-00-00', '', 2, 3, NULL, NULL, 'Cancelled', '2026-02-13 05:26:04', '', 'Ace Earl Jairus P. Natividad', '235368', NULL),
(52, '11C217E4', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '0000-00-00', '', 1, 2, NULL, NULL, 'Cancelled', '2026-02-13 05:33:04', '', 'Ace Earl Jairus P. Natividad', '89238138', NULL),
(53, 'DDE08C04', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '0000-00-00', '', 1, 8, NULL, NULL, 'Confirmed', '2026-02-13 05:37:55', '', 'Ace Earl Jairus P. Natividad', '12432679', NULL),
(54, '766A5DE5', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '0000-00-00', '', 1, 8, '2026-02-25', '07:00 AM - 10:00 AM', 'Confirmed', '2026-02-13 05:54:55', '', 'Ace Earl Jairus P. Natividad', '66666', NULL),
(55, 'ECE52A91', 'Hannah Jane', 'hannah@gmail.com', '09812252462', '0000-00-00', '', 1, 8, '2026-02-25', '07:00 AM - 10:00 AM', 'Confirmed', '2026-02-13 06:00:02', '', 'Hannah Jane', '35443564', NULL),
(56, 'F13C4898', 'Ace Earl Jairus Natividad', 'hduasbhdu@gmail.com', '09812252462', '0000-00-00', '', 1, 8, '2026-02-25', '07:00 AM - 10:00 AM', 'Confirmed', '2026-02-13 06:24:44', '', 'Ace Earl Jairus P. Natividad', '43532453', NULL),
(57, '2D498188', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '0000-00-00', '', 1, 8, '2026-02-25', '07:00 AM - 10:00 AM', 'Confirmed', '2026-02-15 23:29:42', '', 'Hannah Jane Barot', 'dwer2q3', NULL),
(58, '702219CF', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '0000-00-00', '', 3, 8, '2026-02-25', '07:00 AM - 10:00 AM', 'Confirmed', '2026-02-16 03:21:25', '', 'Ace', '246833', NULL),
(59, 'EDA97DA4', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '09812252462', '0000-00-00', '', 2, 9, '2026-02-19', '09:00 AM - 06:00 PM', 'Confirmed', '2026-02-16 08:11:57', '', 'Hannah Jane Barot', '23214563', NULL),
(60, 'FC8D868D', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '0000-00-00', '', 1, 9, '2026-02-19', '09:00 AM - 06:00 PM', 'Confirmed', '2026-02-19 07:52:04', '', 'Ace Earl Jairus P. Natividad', '343453', 'FC8D868D_1771487524.png'),
(61, '521E25F7', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '091234567891', '0000-00-00', '', 1, 9, '2026-02-19', '09:00 AM - 06:00 PM', 'Confirmed', '2026-02-19 08:20:23', '', 'Hannah Jane Barot', '235333', '521E25F7_1771489223.jpg'),
(62, 'B8BC3FE7', 'Noel The Bakla', 'noelbading@gmail.com', '09812252462', '0000-00-00', '', 1, 8, '2026-02-25', '07:00 AM - 10:00 AM', 'Confirmed', '2026-02-23 00:39:52', '', 'Hannah Jane Barot', '21232122', 'B8BC3FE7_1771807191.png'),
(63, 'FCA06CA9', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '09812252462', '0000-00-00', '', 1, 10, '2026-03-12', '09:00 AM - 06:00 PM', 'Confirmed', '2026-02-23 01:46:25', '', 'Hannah Jane Barot', '22115544', 'FCA06CA9_1771811185.png'),
(64, 'F64E3B09', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '09812252462', '0000-00-00', '', 1, 10, '2026-03-12', '09:00 AM - 06:00 PM', 'Confirmed', '2026-02-23 02:19:00', '', 'Hannah Jane Barot', '32343543', 'F64E3B09_1771813140.png'),
(65, '23FF4DAD', 'Ace Earl Jairus Natividad', '2202822@ub.edu.ph', '09812252462', '0000-00-00', '', 1, 10, '2026-03-12', '09:00 AM - 06:00 PM', 'Confirmed', '2026-02-23 02:20:11', '', 'Ace Earl Jairus P. Natividad', '12321436', '23FF4DAD_1771813211.png'),
(66, '357BEB59', 'Dasvid', '2202686@ub.edu.ph', '09812252462', '0000-00-00', '', 1, 10, '2026-03-12', '09:00 AM - 06:00 PM', 'Confirmed', '2026-02-23 02:21:29', '', 'David Paglicawan', '136379', '357BEB59_1771813289.png');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `reference_id` varchar(12) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `visit_date` date NOT NULL,
  `visit_time` time NOT NULL,
  `guest_count` int(11) NOT NULL,
  `status` enum('Pending','Approved','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedule_settings`
--

CREATE TABLE `schedule_settings` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `slots` int(11) NOT NULL DEFAULT 50,
  `max_slots` int(11) NOT NULL DEFAULT 50
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule_settings`
--

INSERT INTO `schedule_settings` (`id`, `date`, `start_time`, `end_time`, `slots`, `max_slots`) VALUES
(8, '2026-02-25', '07:00:00', '10:00:00', 2, 10),
(9, '2026-02-19', '09:00:00', '18:00:00', 16, 20),
(10, '2026-03-12', '09:00:00', '18:00:00', 6, 10);

-- --------------------------------------------------------

--
-- Table structure for table `schedule_slots`
--

CREATE TABLE `schedule_slots` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `max_capacity` int(11) DEFAULT 30,
  `current_bookings` int(11) DEFAULT 0,
  `status` varchar(20) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule_slots`
--

INSERT INTO `schedule_slots` (`id`, `date`, `time`, `max_capacity`, `current_bookings`, `status`) VALUES
(1, '2026-02-24', '10:30:00', 10, 9, 'Active'),
(2, '2026-02-11', '12:00:00', 20, 20, 'Active'),
(3, '2026-02-13', '08:00:00', 10, 4, 'Active'),
(4, '2026-03-31', '02:00:00', 10, 0, 'Open');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `admin_accounts`
--
ALTER TABLE `admin_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_id` (`reference_id`);

--
-- Indexes for table `schedule_settings`
--
ALTER TABLE `schedule_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedule_slots`
--
ALTER TABLE `schedule_slots`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_accounts`
--
ALTER TABLE `admin_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedule_settings`
--
ALTER TABLE `schedule_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `schedule_slots`
--
ALTER TABLE `schedule_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

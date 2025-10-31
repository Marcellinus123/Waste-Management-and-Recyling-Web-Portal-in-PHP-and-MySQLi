-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 06, 2025 at 10:13 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `charityconnect_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cc_admins`
--

CREATE TABLE `cc_admins` (
  `id` int(11) NOT NULL,
  `username_ID` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_PIN` varchar(150) NOT NULL,
  `status` int(5) NOT NULL,
  `fullname` varchar(150) NOT NULL,
  `image` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `time_loggedin` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cc_admins`
--

INSERT INTO `cc_admins` (`id`, `username_ID`, `email`, `password_PIN`, `status`, `fullname`, `image`, `created_at`, `time_loggedin`) VALUES
(1, '', 'emittyarts@gmail.com', '$2y$10$.pkCOeD6q3MiN66HeBtMa.h4Mhg09k2BpFPUl9/ti8AphJfghMn46', 1, 'Sulley Ibrahim', '', '2025-06-23 14:57:38', '0000-00-00 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cc_admins`
--
ALTER TABLE `cc_admins`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cc_admins`
--
ALTER TABLE `cc_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

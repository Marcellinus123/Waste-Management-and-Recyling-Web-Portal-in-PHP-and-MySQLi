-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2025 at 08:34 PM
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
-- Database: `waste_wizard_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_page`
--

CREATE TABLE `about_page` (
  `id` int(11) NOT NULL,
  `our_story` text NOT NULL,
  `our_mission` text NOT NULL,
  `our_vision` text NOT NULL,
  `core_values` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `display_order` enum('1','2') NOT NULL,
  `status` enum('public','private') NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  `updated_by` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `about_page`
--

INSERT INTO `about_page` (`id`, `our_story`, `our_mission`, `our_vision`, `core_values`, `image`, `display_order`, `status`, `date_updated`, `updated_by`, `created_at`) VALUES
(1, 'Founded in 2025, Waste Wizard began as a small startup with a big vision: to transform how individuals and businesses manage their waste. Our team of environmentalists and tech enthusiasts came together with a shared passion for sustainability and innovation.\r\n\r\nWhat started as a simple idea to track household recycling has grown into a comprehensive waste management platform serving thousands of users across the country. Today, we\'re proud to be at the forefront of the smart waste revolution.\r\n\r\nOur journey hasn\'t always been easy, but our commitment to creating a cleaner, greener future keeps us moving forward. Every day, we\'re inspired by our users who join us in making a real difference for our planet.', 'To empower individuals and businesses with smart tools that make waste reduction simple, rewarding, and accessible to everyone. We believe technology can bridge the gap between good intentions and real environmental impact.', 'A world where waste is managed efficiently and sustainably by default. We envision communities where every piece of waste is tracked, every recyclable material is recovered, and landfills become a thing of the past.', '[{\"title\":\"Sustainability\",\"description\":\"We prioritize environmental impact in every decision, ensuring our solutions contribute to a healthier planet\"},{\"title\":\"Innovation\",\"description\":\"We constantly push boundaries to develop smarter, more effective waste management solutions.\"},{\"title\":\"Community\",\"description\":\"We believe change happens together. We build tools that empower communities to make a collective impact.\"},{\"title\":\"Transparency\",\"description\":\"We provide clear, honest data about waste streams and their environmental consequences.\"}]', '../WM images/about-689b5b4021844.jpg', '', 'public', '2025-08-12 15:18:24', 'admin1', '2025-08-01 00:08:30');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `user_id` varchar(150) NOT NULL,
  `first_name` varchar(150) NOT NULL,
  `last_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `usertype` enum('admin_user') NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(150) NOT NULL,
  `zip_code` varchar(150) NOT NULL,
  `country` varchar(150) NOT NULL,
  `account_status` enum('active','banned','suspended','deleted') NOT NULL,
  `notify_email` tinyint(1) DEFAULT 1,
  `notify_sms` tinyint(1) DEFAULT 1,
  `notify_push` tinyint(1) DEFAULT 1,
  `avatar` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `user_id`, `first_name`, `last_name`, `email`, `username`, `password`, `usertype`, `phone`, `address`, `city`, `zip_code`, `country`, `account_status`, `notify_email`, `notify_sms`, `notify_push`, `avatar`, `last_updated`) VALUES
(1, 'user_68925dafbf71e', 'Benard', 'Awulah', 'benard@gmail.com', 'admin1', '$2y$10$.pkCOeD6q3MiN66HeBtMa.h4Mhg09k2BpFPUl9/ti8AphJfghMn46', 'admin_user', '0248770025', 'Bolga', 'Bolga', '', 'Ghana', 'active', 1, 1, 1, 'Memory-Hierarchy-.jpg', '2025-08-11 18:31:10');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `booking_code` varchar(150) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `service_type` varchar(150) NOT NULL,
  `vehicle_type` varchar(150) NOT NULL,
  `collection_date` datetime DEFAULT NULL,
  `time_slot` varchar(150) NOT NULL,
  `estimated_weight` varchar(150) NOT NULL,
  `location` varchar(200) NOT NULL,
  `notes` text NOT NULL,
  `amount` int(11) NOT NULL,
  `paystack_reference` varchar(150) NOT NULL,
  `status` enum('not_approved','approved','completed','rejected','cancelled') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_code`, `user_id`, `service_type`, `vehicle_type`, `collection_date`, `time_slot`, `estimated_weight`, `location`, `notes`, `amount`, `paystack_reference`, `status`, `created_at`) VALUES
(1, 'BK-6894E6D1A62C4', 'user_68925dafbf71e', 'general', 'GA-7826760', '2025-08-07 00:00:00', 'afternoon', '350kg', 'Bolgatanga', 'Test', 1330000, '', 'completed', '2025-08-07 17:48:01'),
(2, 'BK-6894F3AA2FF31', 'user_68925dafbf71e', 'recycling', 'GA-4826767', '2025-08-07 00:00:00', 'morning', '350kg', 'Accra', 'Falc', 52500, '', 'approved', '2025-08-07 18:42:50'),
(3, 'BK-68950417C35DE', 'user_68925dafbf71e', 'general', 'GA-7826767', '2025-08-08 00:00:00', 'evening', '50kg', 'Bawku, Ghana', 'sdshghsgd', 190000, '', 'approved', '2025-08-07 19:52:55'),
(4, 'BK-689A23F54D33E', 'user_68925dafbf71e', 'general', 'GA-1126767', '2025-08-14 00:00:00', 'morning', '400kg', 's', 's', 1520000, '', 'approved', '2025-08-11 17:10:13'),
(5, 'BK-689B5396263E7', 'user_68925dafbf71e', 'recycling', 'GA-7826760', '2025-08-12 00:00:00', 'morning', '250kg', 'Bolga', 'My garage bin', 37500, '', 'approved', '2025-08-12 14:45:42');

-- --------------------------------------------------------

--
-- Table structure for table `booking_agreements`
--

CREATE TABLE `booking_agreements` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(12) NOT NULL,
  `waste_user_sign` varchar(12) NOT NULL,
  `waste_driver_sign` varchar(12) NOT NULL,
  `date_user_sign` datetime DEFAULT NULL,
  `date_driver_sign` datetime DEFAULT NULL,
  `status` enum('completed','endorsed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `media` varchar(255) DEFAULT NULL,
  `status` enum('public','private') NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `link`, `media`, `status`, `updated_at`, `created_at`) VALUES
(1, 'What payment methods do you accept?', 'We accept all major credit cards (Visa, Mastercard, American Express), PayPal, and bank transfers for annual payments. Enterprise customers may also be eligible for invoice billing.', NULL, NULL, 'public', NULL, '2025-08-01 02:27:10'),
(2, 'Can I switch plans later?', 'Yes, you can upgrade or downgrade your plan at any time. When you upgrade, you\'ll immediately gain access to the new features, and we\'ll prorate the difference in cost. Downgrades will take effect at your next billing cycle.', NULL, NULL, 'public', NULL, '2025-08-01 02:27:10'),
(3, 'Is there a free trial available?', 'Yes! Our Pro plan comes with a 14-day free trial so you can test all the advanced features. The Basic plan is always free with no trial period needed.', NULL, NULL, 'public', NULL, '2025-08-01 02:28:09'),
(4, 'Do you offer discounts for non-profits?', 'We offer a 20% discount for registered non-profit organizations. Please contact our sales team with proof of your non-profit status to receive this discount.', NULL, NULL, 'public', NULL, '2025-08-01 02:28:09'),
(5, 'What\'s your refund policy?', 'We offer a 30-day money-back guarantee for all paid plans. If you\'re not satisfied with our service within the first 30 days, we\'ll give you a full refund, no questions asked', '', NULL, 'public', '2025-08-12 16:31:10', '2025-08-01 02:28:29');

-- --------------------------------------------------------

--
-- Table structure for table `features_page`
--

CREATE TABLE `features_page` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category` enum('Home','Business','Community','Other') NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` enum('public','private') NOT NULL,
  `updated_by` varchar(120) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `features_page`
--

INSERT INTO `features_page` (`id`, `title`, `content`, `category`, `image`, `status`, `updated_by`, `created_at`) VALUES
(1, 'Smart Waste Analytics', 'Track and analyze your waste generation patterns with beautiful, easy-to-understand dashboards that help you identify reduction opportunities..', 'Home', '1500242309_9168refuse.jpg', 'public', 'admin1', '2025-08-12 16:25:06'),
(2, 'Automated Collection Scheduling', 'Never miss a pickup again with our smart scheduling system that syncs with local services and sends you reminders.', 'Business', 'photo-1596464716127-f2a82984de30.jpeg', 'public', '', '2025-08-01 00:58:56'),
(3, 'Recycling Rewards Program', 'Earn points for proper waste disposal that can be redeemed for discounts at eco-friendly businesses and products.', 'Community', '360_F_1403159363_Bw9rBFTB11r3TKB2eIuELJP6R5QkQyN3.jpg', 'public', '', '2025-08-01 01:00:18'),
(4, 'Recycling Facility Locator', 'Find nearby recycling centers and special disposal facilities with real-time information about hours and accepted materials.', 'Business', 'efficient-waste-collection-garbage-truck-duty-garbage-truck-drives-suburban-neighborhood-picking-up-trash-370135070.jpg', 'public', '', '2025-08-01 01:00:18'),
(5, 'Digital Waste Audits', 'Conduct comprehensive waste audits without the hassle. Our digital tools guide you through the process and generate professional reports.', 'Community', 'photo-1551288049-bebda4e38f71.jpeg', 'public', '', '2025-08-01 01:02:04'),
(6, 'Community Impact Dashboard', 'See how your neighborhood is doing with collective waste reduction goals and compare your progress with similar communities.', 'Community', 'photo-1579621970563-ebec7560ff3e.jpeg', 'public', '', '2025-08-01 01:02:04');

-- --------------------------------------------------------

--
-- Table structure for table `feature_comparison`
--

CREATE TABLE `feature_comparison` (
  `id` int(11) NOT NULL,
  `feature` varchar(150) NOT NULL,
  `basic` varchar(50) NOT NULL,
  `pro` varchar(60) NOT NULL,
  `enterprise` varchar(50) NOT NULL,
  `status` enum('public','private') NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` int(11) NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `feature_comparison`
--

INSERT INTO `feature_comparison` (`id`, `feature`, `basic`, `pro`, `enterprise`, `status`, `updated_at`, `created_at`) VALUES
(1, 'Waste Tracking', '1', '1', '1', 'public', '2025-08-01 01:28:21', 2147483647),
(2, 'Collection Scheduling', '1', '1', '1', 'public', '2025-08-01 01:28:21', 2147483647),
(3, 'Recycling Rewards', '1', '1', '1', 'public', '2025-08-01 01:28:21', 2147483647),
(4, 'Facility Locator', '1', '1', '1', 'public', '2025-08-01 01:28:21', 2147483647),
(5, 'Advanced Analytics', '0', '1', '1', 'public', '2025-08-01 01:28:21', 2147483647),
(6, 'Waste Audit Tools', '0', '1', '1', 'public', '2025-08-01 01:28:21', 2147483647),
(7, 'Multi-Location Management', '0', '0', '1', 'public', '2025-08-01 01:28:21', 2147483647),
(8, 'API Integration', '0', '0', '1', 'public', '2025-08-01 01:28:21', 2147483647);

-- --------------------------------------------------------

--
-- Table structure for table `feature_highlights`
--

CREATE TABLE `feature_highlights` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `sub_features` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` enum('public','private') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `feature_highlights`
--

INSERT INTO `feature_highlights` (`id`, `title`, `sub_features`, `image`, `status`, `created_at`) VALUES
(1, 'Advanced Waste Analytics', '[{\"feature\":\"Real-time tracking of waste streams(landfill, recycling, compost) \\r\\nHistorical data comparison to measure progress\\r\\nCustomizable reports for sustainability reporting\\r\\nAI-powered reduction recommendations\\r\\nBenchmarking against similar homes\\/businesses\"}]', 'photo-1551288049-bebda4e38f71.jpeg', 'public', '2025-08-01 01:18:34'),
(2, 'Smart Collection Management', '[\n  {\n    \"feature\": \"Automated pickup reminders via email/SMS\"\n  },\n  {\n    \"feature\": \"Weather-adjusted collection alerts\"\n  },\n  {\n    \"feature\": \"Holiday schedule adjustments\"\n  },\n  {\n    \"feature\": \"Bulk item pickup scheduling\"\n  },\n  {\n    \"feature\": \"Service provider performance tracking\"\n  }\n]\n', 'photo-1596464716127-f2a82984de30.jpeg', 'public', '2025-08-01 01:18:34');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `service_name` enum('general','recycling','waste_collection') NOT NULL,
  `description` text DEFAULT NULL,
  `cost` int(11) NOT NULL COMMENT 'cost(Ghc) per kg',
  `status` enum('Active','Inactive') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `description`, `cost`, `status`, `created_at`, `updated_at`) VALUES
(1, 'general', 'sdx', 3800, 'Active', '2025-08-07 14:17:24', '2025-08-13 21:38:10'),
(2, 'recycling', NULL, 150, 'Active', '2025-08-07 14:17:24', NULL),
(3, 'waste_collection', NULL, 200, 'Active', '2025-08-07 14:18:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `service_pricing`
--

CREATE TABLE `service_pricing` (
  `id` int(11) NOT NULL,
  `service_id` varchar(20) NOT NULL,
  `service_name` varchar(150) NOT NULL,
  `price` varchar(50) NOT NULL,
  `features` text NOT NULL,
  `duration` enum('month','week','year') NOT NULL,
  `is_popular` enum('0','1') NOT NULL,
  `status` enum('public','private') NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `service_pricing`
--

INSERT INTO `service_pricing` (`id`, `service_id`, `service_name`, `price`, `features`, `duration`, `is_popular`, `status`, `updated_at`, `created_at`) VALUES
(1, '4K9pL8nJ2qR5aS', 'Basic', '0', '[\n  {\n    \"feature\": \"Waste tracking dashboard\",\n    \"status\": 1\n  },\n  {\n    \"feature\": \"Collection reminders\",\n    \"status\": 1\n  },\n  {\n    \"feature\": \"Recycling facility locator\",\n    \"status\": 1\n  },\n  {\n    \"feature\": \"Basic analytics\",\n    \"status\": 1\n  },\n  {\n    \"feature\": \"Advanced reporting\",\n    \"status\": 0\n  },\n  {\n    \"feature\": \"Waste audit tools\",\n    \"status\": 0\n  },\n  {\n    \"feature\": \"Priority support\",\n    \"status\": 0\n  }\n]\n', 'month', '0', 'public', NULL, '2025-08-01 01:49:51'),
(2, 'G3h8dJ9pM4nB1c', 'Pro', '200', '[{\"feature\":\"Waste Tracking\",\"status\":0},{\"feature\":\"Collection Scheduling\",\"status\":0},{\"feature\":\"Recycling Rewards\",\"status\":0},{\"feature\":\"Facility Locator\",\"status\":0},{\"feature\":\"Advanced Analytics\",\"status\":0},{\"feature\":\"Waste Audit Tools\",\"status\":0},{\"feature\":\"Multi-Location Management\",\"status\":0},{\"feature\":\"API Integration\",\"status\":0},{\"feature\":\"Priority Support\",\"status\":0},{\"feature\":\"Custom Reporting\",\"status\":0}]', 'month', '1', 'public', '2025-08-13 21:39:14', '2025-08-01 01:49:51'),
(3, '8L5kP7mN9bV4xZ', 'Enterprise', 'Custom', '[{\"feature\":\"Waste Tracking\",\"status\":0},{\"feature\":\"Collection Scheduling\",\"status\":0},{\"feature\":\"Recycling Rewards\",\"status\":0},{\"feature\":\"Facility Locator\",\"status\":0},{\"feature\":\"Advanced Analytics\",\"status\":0},{\"feature\":\"Waste Audit Tools\",\"status\":0},{\"feature\":\"Multi-Location Management\",\"status\":0},{\"feature\":\"API Integration\",\"status\":0},{\"feature\":\"Priority Support\",\"status\":0},{\"feature\":\"Custom Reporting\",\"status\":0}]', 'month', '0', 'public', '2025-08-12 15:33:08', '2025-08-01 01:51:54');

-- --------------------------------------------------------

--
-- Table structure for table `site_contacts`
--

CREATE TABLE `site_contacts` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `source` text NOT NULL,
  `status` varchar(100) NOT NULL,
  `date_contacted` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `site_contacts`
--

INSERT INTO `site_contacts` (`id`, `fullname`, `email`, `subject`, `message`, `source`, `status`, `date_contacted`) VALUES
(1, 'Atampugre Marce', 'emittyarts@gmail.com', 'Test', 'dsd', 'http://localhost/waste/theme/contact', '', '2025-08-01 03:06:17'),
(2, 'Bernard Patient', 'gad@email.com', 'Test', 'dsd', 'http://localhost/waste/theme/contact?plan=8L5kP7mN9bV4xZ', '', '2025-08-01 03:07:07'),
(3, 'Ben Akaa', 'user2@gmail.com', 'Testing contact page', 'Testing contact page', 'http://localhost/waste/theme/contact?plan=8L5kP7mN9bV4xZ', '', '2025-08-01 10:41:00'),
(4, 'Hospital Patient Records Management System - PHP', 'atampugremarcellinus@gmail.com', 'dsds', 'rtr', 'http://localhost/waste/theme/contact', '', '2025-08-13 21:42:57');

-- --------------------------------------------------------

--
-- Table structure for table `support_attachments`
--

CREATE TABLE `support_attachments` (
  `attachment_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_messages`
--

CREATE TABLE `support_messages` (
  `message_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `sender_id` varchar(50) NOT NULL,
  `sender_type` enum('user','admin') NOT NULL,
  `message` text NOT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `is_delivered` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `support_messages`
--

INSERT INTO `support_messages` (`message_id`, `ticket_id`, `sender_id`, `sender_type`, `message`, `attachments`, `created_at`, `is_read`, `is_delivered`) VALUES
(1, 1, 'user_68925dafbf71e', 'user', 'Hello', NULL, '2025-08-05 20:52:22', 1, 0),
(2, 2, 'user_68925dafbf71e', 'user', 'Hello', NULL, '2025-08-05 20:53:29', 1, 0),
(3, 3, 'user_68925dafbf71e', 'user', 'I am testing', '[{\"name\":\"cc_admins.sql\",\"path\":\"uploads\\/support\\/ticket_3_6894c6b7d27f2.sql\",\"type\":\"application\\/octet-stream\",\"size\":1948}]', '2025-08-07 15:31:03', 1, 0),
(4, 4, 'user_68925dafbf71e', 'user', 'I am testing', '[{\"name\":\"cc_admins.sql\",\"path\":\"uploads\\/support\\/ticket_4_6894c7194a147.sql\",\"type\":\"application\\/octet-stream\",\"size\":1948}]', '2025-08-07 15:32:41', 1, 0),
(5, 5, 'user_68925dafbf71e', 'user', 'toating ', NULL, '2025-08-07 15:36:15', 1, 0),
(6, 6, 'user_68925dafbf71e', 'user', 'yyuhhj', '[{\"name\":\"BK-6894F3AA2FF31.pdf\",\"path\":\"uploads\\/support\\/ticket_6_6895046eba0cd.pdf\",\"type\":\"application\\/pdf\",\"size\":8365}]', '2025-08-07 19:54:22', 1, 0),
(7, 7, 'user_6895062809ae5', 'user', 'Can\'t login', NULL, '2025-08-07 21:28:02', 0, 0),
(8, 7, 'user_68925dafbf71e', 'admin', 'Okay', NULL, '2025-08-11 23:16:08', 0, 0),
(9, 1, 'user_68925dafbf71e', 'admin', 'Yes Hello', NULL, '2025-08-11 23:22:17', 1, 0),
(10, 5, 'user_68925dafbf71e', 'user', 'You good Sir ?', NULL, '2025-08-12 00:47:48', 1, 0),
(11, 5, 'user_68925dafbf71e', 'admin', 'Yes i am good', NULL, '2025-08-12 00:48:12', 1, 0),
(12, 5, 'user_68925dafbf71e', 'user', 'Okay', NULL, '2025-08-12 00:48:34', 1, 0),
(13, 5, 'user_68925dafbf71e', 'admin', 'Hi', NULL, '2025-08-12 01:03:46', 1, 0),
(14, 5, 'user_68925dafbf71e', 'admin', 'No Reply?', NULL, '2025-08-12 01:12:22', 1, 0),
(15, 5, 'user_68925dafbf71e', 'user', 'Understood, can you end the chat ?', NULL, '2025-08-12 01:19:02', 1, 0),
(16, 5, 'user_68925dafbf71e', 'admin', 'Okay', NULL, '2025-08-12 01:19:17', 1, 0),
(17, 8, 'user_6894fc40c30cb', 'user', 'dsd', NULL, '2025-08-12 14:33:05', 0, 0),
(18, 8, 'user_6894fc40c30cb', 'user', 'Yo', NULL, '2025-08-12 15:02:51', 0, 0),
(19, 8, 'user_68925dafbf71e', 'admin', 'yes', NULL, '2025-08-12 15:03:18', 1, 0),
(20, 6, 'user_68925dafbf71e', 'user', 'hi', NULL, '2025-08-13 21:33:12', 1, 0),
(21, 6, 'user_68925dafbf71e', 'admin', 'Cool', NULL, '2025-08-13 21:35:37', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `ticket_id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `subject` enum('collection','recycling','billing','account','other') NOT NULL,
  `title` varchar(255) NOT NULL,
  `status` enum('open','in_progress','resolved','closed') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `support_tickets`
--

INSERT INTO `support_tickets` (`ticket_id`, `user_id`, `subject`, `title`, `status`, `created_at`, `updated_at`) VALUES
(1, 'user_68925dafbf71e', 'collection', 'Hello', 'resolved', '2025-08-05 20:52:22', '2025-08-11 23:22:59'),
(2, 'user_68925dafbf71e', 'collection', 'Hello', 'open', '2025-08-05 20:53:29', '2025-08-05 20:53:29'),
(3, 'user_68925dafbf71e', 'recycling', 'I am testing', 'open', '2025-08-07 15:31:03', '2025-08-07 15:31:03'),
(4, 'user_68925dafbf71e', 'recycling', 'I am testing', 'open', '2025-08-07 15:32:41', '2025-08-07 15:32:41'),
(5, 'user_68925dafbf71e', 'collection', 'toating ', 'closed', '2025-08-07 15:36:15', '2025-08-12 01:19:26'),
(6, 'user_68925dafbf71e', 'account', 'yyuhhj', 'in_progress', '2025-08-07 19:54:22', '2025-08-13 21:35:37'),
(7, 'user_6895062809ae5', 'account', 'Can\'t login', 'closed', '2025-08-07 21:28:02', '2025-08-11 23:46:33'),
(8, 'user_6894fc40c30cb', 'billing', 'Problem of dark', 'in_progress', '2025-08-12 14:33:05', '2025-08-12 15:03:18');

-- --------------------------------------------------------

--
-- Table structure for table `team_page`
--

CREATE TABLE `team_page` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `social_handles` text NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `team_page`
--

INSERT INTO `team_page` (`id`, `fullname`, `position`, `image`, `social_handles`, `status`, `updated_at`, `created_at`) VALUES
(1, 'Bernard Adjei', 'CEO & Founder', '../WM images/team/default.png', '[\r\n  {\r\n    \"social_name\": \"Facebook\",\r\n    \"user_profile_link\": \"https://www.facebook.com/johndoe\"\r\n  },\r\n  {\r\n    \"social_name\": \"Twitter\",\r\n    \"user_profile_link\": \"https://twitter.com/johndoe\"\r\n  },\r\n  {\r\n    \"social_name\": \"Instagram\",\r\n    \"user_profile_link\": \"https://www.instagram.com/johndoe\"\r\n  },\r\n  {\r\n    \"social_name\": \"LinkedIn\",\r\n    \"user_profile_link\": \"https://www.linkedin.com/in/johndoe\"\r\n  }\r\n]\r\n', 'active', NULL, '2025-08-01 00:36:34'),
(2, 'Michael Asare', 'CTO', '../WM images/team/default.png', '[\r\n  {\r\n    \"social_name\": \"Facebook\",\r\n    \"user_profile_link\": \"https://www.facebook.com/johndoe\"\r\n  },\r\n  {\r\n    \"social_name\": \"Twitter\",\r\n    \"user_profile_link\": \"https://twitter.com/johndoe\"\r\n  },\r\n  {\r\n    \"social_name\": \"Instagram\",\r\n    \"user_profile_link\": \"https://www.instagram.com/johndoe\"\r\n  },\r\n  {\r\n    \"social_name\": \"LinkedIn\",\r\n    \"user_profile_link\": \"https://www.linkedin.com/in/johndoe\"\r\n  }\r\n]\r\n', 'active', NULL, '2025-08-01 00:36:34'),
(3, 'Mark Adjei', 'Head of Sustainability', '../WM images/team/team_689b635e1bd12.jpg', '[{\"social_name\":\"Facebook\",\"user_profile_link\":\"https:\\/\\/www.facebook.com\\/johndoe\"},{\"social_name\":\"Instagram\",\"user_profile_link\":\"https:\\/\\/www.instagram.com\\/johndoe\"}]', 'active', '2025-08-13 21:40:33', '2025-08-01 00:37:52'),
(4, 'Elizabeth Addo', 'Product Manager', '../WM images/team/default.png', '[\r\n  {\r\n    \"social_name\": \"Facebook\",\r\n    \"user_profile_link\": \"https://www.facebook.com/johndoe\"\r\n  },\r\n  {\r\n    \"social_name\": \"Twitter\",\r\n    \"user_profile_link\": \"https://twitter.com/johndoe\"\r\n  },\r\n  {\r\n    \"social_name\": \"Instagram\",\r\n    \"user_profile_link\": \"https://www.instagram.com/johndoe\"\r\n  },\r\n  {\r\n    \"social_name\": \"LinkedIn\",\r\n    \"user_profile_link\": \"https://www.linkedin.com/in/johndoe\"\r\n  }\r\n]\r\n', 'active', NULL, '2025-08-01 00:37:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_id` varchar(150) NOT NULL,
  `first_name` varchar(150) NOT NULL,
  `last_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `usertype` enum('waste_user','waste_driver','other') NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(150) NOT NULL,
  `zip_code` varchar(150) NOT NULL,
  `country` varchar(150) NOT NULL,
  `account_status` enum('active','banned','suspended','deleted') NOT NULL,
  `account_balance` int(11) NOT NULL,
  `eco_points` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `zip` varchar(20) DEFAULT NULL,
  `region` varchar(50) DEFAULT NULL,
  `notify_email` tinyint(1) DEFAULT 1,
  `notify_sms` tinyint(1) DEFAULT 1,
  `notify_push` tinyint(1) DEFAULT 1,
  `avatar` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_id`, `first_name`, `last_name`, `email`, `username`, `password`, `usertype`, `phone`, `address`, `city`, `zip_code`, `country`, `account_status`, `account_balance`, `eco_points`, `created_at`, `zip`, `region`, `notify_email`, `notify_sms`, `notify_push`, `avatar`, `last_updated`) VALUES
(1, 'user_68925dafbf71e', 'Marcellinus', 'Atampugre', 'atampugremarcellinus@gmail.com', 'user1', '$2y$10$.pkCOeD6q3MiN66HeBtMa.h4Mhg09k2BpFPUl9/ti8AphJfghMn46', 'waste_user', '0248770024', 'Bolga', 'Bolga', '', 'Ghana', 'active', 0, 0, '2025-08-05 19:38:23', '345', 'Upper West', 1, 1, 1, 'Memory-Hierarchy-.jpg', '2025-08-11 23:21:54'),
(2, 'user_6894fc40c30cb', 'Aguy', 'Akaka', 'user22@gmail.com', 'user2', '$2y$10$.pkCOeD6q3MiN66HeBtMa.h4Mhg09k2BpFPUl9/ti8AphJfghMn46', 'waste_driver', '0248770029', '', '', '', 'Ghana', 'active', 0, 0, '2025-08-07 19:19:28', '', 'Ashanti', 1, 1, 1, NULL, '2025-08-12 14:28:37'),
(3, 'user_6895062809ae5', 'Ben', 'Kalif', 'ben@gmail.com', 'ben', '$2y$10$HsSRDCV3WtzlQ0LJ1Os/4OB50yL7.Jq7S71zy41n5VGRi9cXEzElC$2y$10$.pkCOeD6q3MiN66HeBtMa.h4Mhg09k2BpFPUl9/ti8AphJfghMn46', 'waste_driver', '0594960113', 'H72, Independence Avenue, Kinbu Garden', 'Accra', '', 'Ghana', 'active', 0, 0, '2025-08-07 20:01:44', '345', 'Ashanti', 1, 1, 1, 'uploads/profile_images/user_user_6895062809ae5_1754602002.jpg', '2025-08-11 17:08:21'),
(4, 'user_689a81f340d69', 'Marcellinus', 'Atampugre', 'atampugremarcellinus@gmail.com', 'matampugre564', '$2y$10$.pkCOeD6q3MiN66HeBtMa.h4Mhg09k2BpFPUl9/ti8AphJfghMn46', 'waste_user', '0248770024', 'Bolga', 'Bolga', '', '', 'active', 0, 0, '2025-08-11 23:51:15', NULL, 'Upper West', 1, 1, 1, NULL, '2025-08-12 14:28:48');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `vehicle_number` varchar(50) NOT NULL,
  `driver_id` varchar(50) NOT NULL,
  `vehicle_name` varchar(150) NOT NULL,
  `vehicle_type` enum('Small Waste Truck','Standard Waste Truck','Organic Waste Truck','Other') NOT NULL,
  `weight` varchar(50) NOT NULL,
  `vehicle_status` enum('Available','Not Available','Banned') NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `front_img` varchar(150) NOT NULL,
  `back_img` varchar(150) NOT NULL,
  `side_img` varchar(150) NOT NULL,
  `top_img` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `vehicle_number`, `driver_id`, `vehicle_name`, `vehicle_type`, `weight`, `vehicle_status`, `date_created`, `front_img`, `back_img`, `side_img`, `top_img`) VALUES
(1, 'GA-7826767', 'user_68925dafbf871e', 'Toyota Traka', 'Small Waste Truck', '500kg', 'Not Available', '2025-08-07 14:09:23', '', '', '', ''),
(2, 'GA-4826767', 'user_689259dafbf81e', 'Dauwood Mf5', 'Standard Waste Truck', '2000kg', 'Not Available', '2025-08-07 14:09:23', '', '', '', ''),
(4, 'GA-1126767', 'user_981925dabf87jx', 'Dodge Duty Roster', 'Other', '2050kg', 'Not Available', '2025-08-07 14:11:00', '', '', '', ''),
(8, 'GA-7826760', 'user_6894fc40c30cb', 'Kia Kamfleg', 'Organic Waste Truck', '150kg', 'Not Available', '2025-08-11 21:25:57', '', '', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_page`
--
ALTER TABLE `about_page`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_agreements`
--
ALTER TABLE `booking_agreements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `features_page`
--
ALTER TABLE `features_page`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feature_comparison`
--
ALTER TABLE `feature_comparison`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feature_highlights`
--
ALTER TABLE `feature_highlights`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_pricing`
--
ALTER TABLE `service_pricing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_contacts`
--
ALTER TABLE `site_contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_attachments`
--
ALTER TABLE `support_attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexes for table `support_messages`
--
ALTER TABLE `support_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`ticket_id`);

--
-- Indexes for table `team_page`
--
ALTER TABLE `team_page`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_page`
--
ALTER TABLE `about_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `booking_agreements`
--
ALTER TABLE `booking_agreements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `features_page`
--
ALTER TABLE `features_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feature_comparison`
--
ALTER TABLE `feature_comparison`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `feature_highlights`
--
ALTER TABLE `feature_highlights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `service_pricing`
--
ALTER TABLE `service_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `site_contacts`
--
ALTER TABLE `site_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `support_attachments`
--
ALTER TABLE `support_attachments`
  MODIFY `attachment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_messages`
--
ALTER TABLE `support_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `team_page`
--
ALTER TABLE `team_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `support_attachments`
--
ALTER TABLE `support_attachments`
  ADD CONSTRAINT `support_attachments_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `support_messages` (`message_id`) ON DELETE CASCADE;

--
-- Constraints for table `support_messages`
--
ALTER TABLE `support_messages`
  ADD CONSTRAINT `support_messages_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`ticket_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

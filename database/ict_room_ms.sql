-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 23, 2026 at 06:35 PM
-- Server version: 10.1.29-MariaDB
-- PHP Version: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ict_room_ms`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` int(10) UNSIGNED NOT NULL,
  `school_id` int(10) UNSIGNED NOT NULL,
  `asset_code` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `asset_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `brand` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial_number` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `asset_condition` enum('New','Good','Fair','Damaged') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Good',
  `power_adapter` enum('Yes','No') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'No',
  `power_adapter_status` enum('Working','Damaged','Missing','N/A') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N/A',
  `status` enum('Available','In Use','Maintenance','Lost') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Available',
  `location_id` int(10) UNSIGNED NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`id`, `school_id`, `asset_code`, `asset_name`, `category_id`, `brand`, `model`, `serial_number`, `purchase_date`, `asset_condition`, `power_adapter`, `power_adapter_status`, `status`, `location_id`, `image_path`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'REB/MSA-GSRMR/LT038', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2178', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 09:31:44', '2026-02-23 19:10:56'),
(2, 1, 'REB/MSA-GSRMR/LT018', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2253', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 09:46:41', '2026-02-23 19:10:56'),
(3, 1, 'REB/MSA-GSRMR/LT104', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2270', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 09:48:22', '2026-02-23 19:10:56'),
(4, 1, 'REB/MSA-GSRMR/LT002', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2222', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 09:56:34', '2026-02-23 19:10:56'),
(5, 1, 'REB/MSA-GSRMR/LT034', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2161', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 10:01:36', '2026-02-23 19:10:56'),
(6, 1, 'REB/MSA-GSRMR/LT039', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2237', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 10:10:39', '2026-02-23 19:10:56'),
(7, 1, 'REB/MSA-GSRMR/LT046', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2112', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 10:12:17', '2026-02-23 19:10:56'),
(8, 1, 'REB/MSA-GSRMR/LT013', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2179', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 10:13:28', '2026-02-23 19:10:56'),
(9, 1, 'REB/MSA-GSRMR/LT032', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2250', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 10:14:10', '2026-02-23 19:10:56'),
(10, 1, 'REB/MSA-GSRMR/LT017', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2128', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 10:15:14', '2026-02-23 19:10:56'),
(11, 1, 'REB/MSA-GSRMR/LT026', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2177', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 10:21:56', '2026-02-23 19:10:56'),
(12, 1, 'REB/MSA-GSRMR/LT027', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2183', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 10:31:09', '2026-02-23 19:10:56'),
(13, 1, 'REB/MSA-GSRMR/LT028', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2101', NULL, 'Fair', 'No', 'N/A', 'Available', 1, NULL, 'The battery is damaged.', '2026-01-28 10:34:34', '2026-02-23 19:10:56'),
(15, 1, 'REB/MSA-GSRMR/LT014', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AA2125', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 10:54:08', '2026-02-23 19:10:56'),
(16, 1, 'REB/MSA-GSRMR/LT012', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2164', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 11:00:11', '2026-02-23 19:10:56'),
(17, 1, 'REB/MSA-GSRMR/LT024', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2184', NULL, 'Damaged', 'No', 'N/A', 'Maintenance', 1, NULL, 'This PC doesn\'t power on.', '2026-01-28 11:01:48', '2026-02-23 19:10:56'),
(18, 1, 'REB/MSA-GSRMR/LT001', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2223', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 11:04:26', '2026-02-23 19:10:56'),
(19, 1, 'REB/MSA-GSRMR/LT011', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2114', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 11:07:25', '2026-02-23 19:10:56'),
(20, 1, 'REB/MSA-GSRMR/LT009', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2210', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 11:10:12', '2026-02-23 19:10:56'),
(21, 1, 'REB/MSA-GSRMR/LT042', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2240', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 11:15:48', '2026-02-23 19:10:56'),
(22, 1, 'REB/MSA-GSRMR/LT098', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '305AAA1306', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 11:23:30', '2026-02-23 19:10:56'),
(23, 1, 'REB/MSA-GSRMR/LT004', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2103', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 11:24:24', '2026-02-23 19:10:56'),
(24, 1, 'REB/MSA-GSRMR/LT055', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2096', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 13:40:06', '2026-02-23 19:10:56'),
(25, 1, 'REB/MSA-GSRMR/LT029', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2175', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 13:42:08', '2026-02-23 19:10:56'),
(26, 1, 'REB/MSA-GSRMR/LT033', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2212', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 13:43:23', '2026-02-23 19:10:56'),
(27, 1, 'REB/MSA-GSRMR/LT005', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2244', NULL, 'Good', 'No', 'N/A', 'Available', 1, NULL, NULL, '2026-01-28 13:57:37', '2026-02-23 19:10:56'),
(28, 1, 'REB/MSA-GSRMR/LT019', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2192', NULL, 'Good', 'Yes', 'Working', 'Available', 1, NULL, NULL, '2026-01-28 13:58:34', '2026-02-23 19:10:56'),
(29, 1, 'REB/MSA-GSRMR/LT015', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2214', NULL, 'Good', 'Yes', 'Working', 'Available', 1, NULL, NULL, '2026-01-28 13:59:25', '2026-02-23 19:10:56'),
(30, 1, 'REB/MSA-GSRMR/LT020', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2260', NULL, 'Good', 'Yes', 'Working', 'Available', 1, NULL, NULL, '2026-01-28 14:00:28', '2026-02-23 19:10:56'),
(31, 1, 'REB/MSA-GSRMR/LT010', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2236', NULL, 'Good', 'Yes', 'Working', 'Available', 1, NULL, NULL, '2026-01-28 14:01:47', '2026-02-23 19:10:56'),
(32, 1, 'REB/MSA-GSRMR/LT036', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2165', NULL, 'Good', 'Yes', 'Working', 'Available', 1, NULL, NULL, '2026-01-28 14:05:25', '2026-02-23 19:10:56'),
(33, 1, 'REB/MSA-GSRMR/LT037', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2215', NULL, 'Good', 'Yes', 'Working', 'Available', 1, NULL, NULL, '2026-01-28 14:06:34', '2026-02-23 19:10:56'),
(34, 1, 'REB/MSA-GSRMR/LT031', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2120', NULL, 'Good', 'Yes', 'Working', 'Available', 1, NULL, NULL, '2026-01-28 14:08:17', '2026-02-23 19:10:56'),
(35, 1, 'REB/MSA-GSRMR/LT030', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2156', NULL, 'Fair', 'Yes', 'Working', 'Available', 1, NULL, 'Left-click on the touchpad does not work.', '2026-01-28 14:09:03', '2026-02-23 19:10:56'),
(36, 1, 'REB/MSA-GSRMR/LT035', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2205', NULL, 'Good', 'Yes', 'Working', 'Available', 1, NULL, NULL, '2026-01-28 14:10:06', '2026-02-23 19:10:56'),
(37, 1, 'REB/MSA-GSRMR/LT044', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2146', NULL, 'Fair', 'Yes', 'Working', 'Available', 1, NULL, 'The battery is damaged.', '2026-01-28 14:30:21', '2026-02-23 19:10:56'),
(38, 1, 'REB/MSA-GSRMR/LT016', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2248', NULL, 'Fair', 'Yes', 'Working', 'Available', 1, NULL, 'The battery is damaged.', '2026-01-28 14:33:00', '2026-02-23 19:10:56'),
(39, 1, 'REB/MSA-GSRMR/LT043', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2167', NULL, 'Good', 'Yes', 'Working', 'Available', 1, NULL, NULL, '2026-01-28 14:33:59', '2026-02-23 19:10:56'),
(40, 1, 'REB/MSA-GSRMR/LT040', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2217', NULL, 'Good', 'Yes', 'Working', 'Available', 1, NULL, NULL, '2026-01-28 14:35:14', '2026-02-23 19:10:56'),
(42, 1, 'REB/MSA-GSRMR/LT041', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2066', NULL, 'Good', 'Yes', 'Working', 'Available', 1, NULL, NULL, '2026-01-28 14:37:11', '2026-02-23 19:10:56'),
(43, 1, 'REB/MSA-GSRMR/LT003', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA1954', NULL, 'Good', 'Yes', 'Working', 'Available', 1, NULL, NULL, '2026-01-28 14:51:37', '2026-02-23 19:10:56'),
(44, 1, 'REB/MSA-GSRMR/LT025', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2207', NULL, 'Good', 'Yes', 'Working', 'Available', 1, NULL, NULL, '2026-01-28 14:53:25', '2026-02-23 19:10:56'),
(45, 1, 'REB/MSA-GSRMR/LT021', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2242', NULL, 'Good', 'Yes', 'Working', 'Available', 1, NULL, NULL, '2026-01-28 14:54:16', '2026-02-23 19:10:56'),
(46, 1, 'REB/MSA-GSRMR/LT103', 'POSITIVO Laptop', 2, 'POSITIVO', '14CLE-I', '156AAC06353', NULL, 'Fair', 'Yes', 'Working', 'Available', 1, NULL, 'The battery is damaged.', '2026-01-28 15:45:14', '2026-02-23 19:10:56'),
(47, 1, 'REB/MSA-GSRMR/PRJ002', 'Optoma Projector', 6, 'POSITIVO', 'DASSLU', 'Q7BU915AAAAAC0357', NULL, 'Good', 'Yes', 'Working', 'Available', 1, NULL, NULL, '2026-01-28 15:49:19', '2026-02-23 19:10:56'),
(48, 1, 'REB/MSA-GSRMR/PRJ01', 'BENQ Projector', 6, 'BENQ', 'M5560', 'PD9BN04142000', NULL, 'Good', 'Yes', 'Working', 'Available', 1, NULL, NULL, '2026-01-28 16:05:28', '2026-02-23 19:10:56'),
(49, 1, 'REB/MSA-GSRMR/LT007', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2121', NULL, 'Good', 'Yes', 'Working', 'Available', 1, NULL, NULL, '2026-01-28 16:53:37', '2026-02-23 19:10:56'),
(50, 1, 'REB/MSA-GSRMR/LT045', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2221', NULL, 'Good', 'Yes', 'Working', 'In Use', 1, NULL, NULL, '2026-02-20 14:27:24', '2026-02-23 19:10:56'),
(51, 1, 'REB/MSA-GSRMR/LT023', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2083', NULL, 'Good', 'Yes', 'Working', 'In Use', 1, NULL, NULL, '2026-02-20 14:37:59', '2026-02-23 19:10:56'),
(52, 1, 'REB/MSA-GSRMR/LT100', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA8202', NULL, 'Good', 'Yes', 'Working', 'In Use', 1, NULL, 'Returned by HAHIRUMUREMYI Gilbert, it was given by NSABIMANA Fabien.', '2026-02-20 15:41:58', '2026-02-23 19:10:56'),
(53, 1, 'HP 250 G6', 'HP Laptop', 2, 'HP', '3168NGW', 'CND8400468', NULL, 'Damaged', 'No', 'Working', 'Maintenance', 2, NULL, NULL, '2026-02-23 08:42:16', '2026-02-23 19:10:56'),
(54, 1, 'REB/MSA-GSRMR/LT107', 'Lenovo laptop', 2, 'Lenovo', '-', 'REB/MSA-GSRMR/LT107', NULL, 'Damaged', 'No', 'N/A', 'Maintenance', 2, NULL, NULL, '2026-02-23 10:48:56', '2026-02-23 19:10:56');

-- --------------------------------------------------------

--
-- Table structure for table `asset_assignments`
--

CREATE TABLE `asset_assignments` (
  `id` int(10) UNSIGNED NOT NULL,
  `school_id` int(10) UNSIGNED NOT NULL,
  `asset_id` int(10) UNSIGNED NOT NULL,
  `assigned_to_type` enum('ICT Room','Teacher','Class/Department','Head Teacher','DOD','DOS','Accountant') COLLATE utf8mb4_unicode_ci NOT NULL,
  `assigned_to_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assigned_date` date NOT NULL,
  `expected_return_date` date DEFAULT NULL,
  `returned_date` date DEFAULT NULL,
  `return_adapter_status` enum('Working','Damaged','Missing','N/A') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `return_notes` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `asset_assignments`
--

INSERT INTO `asset_assignments` (`id`, `school_id`, `asset_id`, `assigned_to_type`, `assigned_to_name`, `assigned_date`, `expected_return_date`, `returned_date`, `return_adapter_status`, `return_notes`, `notes`, `created_by`, `created_at`) VALUES
(2, 1, 50, 'Teacher', 'HAKORIMANA Theodomir', '2026-01-07', '2026-03-30', NULL, NULL, NULL, 'Facilitation in predaration and teaching', 1, '2026-02-20 14:34:01'),
(3, 1, 51, 'Accountant', 'IBYIMANA Angelus', '2025-11-26', NULL, NULL, NULL, NULL, 'Using in daily accountant activity', 1, '2026-02-20 14:40:13'),
(4, 1, 53, 'Head Teacher', 'Soeur UWIZEYIMANA Valentine', '2025-09-10', '2026-02-23', '2026-02-23', NULL, NULL, NULL, 1, '2026-02-23 08:50:46'),
(7, 1, 52, 'DOD', 'BAHATI Adrien', '2026-02-23', '2026-04-05', NULL, NULL, NULL, 'Using it in his daily work activities', 1, '2026-02-23 18:45:49');

-- --------------------------------------------------------

--
-- Table structure for table `asset_categories`
--

CREATE TABLE `asset_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `school_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `asset_categories`
--

INSERT INTO `asset_categories` (`id`, `school_id`, `name`) VALUES
(1, 1, 'Desktop'),
(2, 1, 'Laptop'),
(3, 1, 'Printer'),
(4, 1, 'Router'),
(5, 1, 'Switch'),
(6, 1, 'Projector'),
(7, 1, 'UPS');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `school_id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 1, 'LOGIN', 'users', '1', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:07:31'),
(2, 1, 1, 'ASSET_CREATE', 'assets', '1', 'Created asset REB/MSA-GSRMR/LT038', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:31:44'),
(3, 1, 1, 'ASSET_UPDATE', 'assets', '1', 'Updated asset REB/MSA-GSRMR/LT038', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:32:10'),
(4, 1, 1, 'ASSIGN_CREATE', 'asset_assignments', '0', 'Assigned asset REB/MSA-GSRMR/LT038 to Teacher: UMURISA Jane', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:39:18'),
(5, 1, 1, 'ASSET_CREATE', 'assets', '2', 'Created asset REB/MSA-GSRMR/LT018', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:46:41'),
(6, 1, 1, 'ASSET_CREATE', 'assets', '3', 'Created asset REB/MSA-GSRMR/LT104', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:48:22'),
(7, 1, 1, 'ASSET_CREATE', 'assets', '4', 'Created asset REB/MSA-GSRMR/LT002', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:56:34'),
(8, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:57:28'),
(9, 1, 1, 'REPORT_PRINT', 'assets', NULL, 'Printed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:57:44'),
(10, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:58:04'),
(11, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:58:12'),
(12, 1, 1, 'REPORT_PRINT', 'assets', NULL, 'Printed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:58:15'),
(13, 1, 1, 'ASSET_CREATE', 'assets', '5', 'Created asset REB/MSA-GSRMR/LT034', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:01:36'),
(14, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:05:12'),
(15, 1, 1, 'REPORT_EXPORT', 'assets', NULL, 'Exported inventory report (Excel/CSV)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:05:17'),
(16, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:07:58'),
(17, 1, 1, 'LOGIN', 'users', '1', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:08:46'),
(18, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:09:10'),
(19, 1, 1, 'ASSET_CREATE', 'assets', '6', 'Created asset REB/MSA-GSRMR/LT039', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:10:39'),
(20, 1, 1, 'ASSET_CREATE', 'assets', '7', 'Created asset REB/MSA-GSRMR/LT046', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:12:17'),
(21, 1, 1, 'ASSET_CREATE', 'assets', '8', 'Created asset REB/MSA-GSRMR/LT013', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:13:28'),
(22, 1, 1, 'ASSET_CREATE', 'assets', '9', 'Created asset REB/MSA-GSRMR/LT032', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:14:10'),
(23, 1, 1, 'ASSET_CREATE', 'assets', '10', 'Created asset REB/MSA-GSRMR/LT017', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:15:14'),
(24, 1, 1, 'USER_UPDATE', 'users', '1', 'Updated user admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:20:18'),
(25, 1, 1, 'ASSET_CREATE', 'assets', '11', 'Created asset REB/MSA-GSRMR/LT026', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:21:56'),
(26, 1, 1, 'ASSET_UPDATE', 'assets', '11', 'Updated asset REB/MSA-GSRMR/LT026', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:22:38'),
(27, 1, 1, 'ASSET_CREATE', 'assets', '12', 'Created asset REB/MSA-GSRMR/LT027', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:31:09'),
(28, 1, 1, 'ASSET_CREATE', 'assets', '13', 'Created asset REB/MSA-GSRMR/LT028', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:34:34'),
(29, 1, 1, 'ASSET_UPDATE', 'assets', '13', 'Updated asset REB/MSA-GSRMR/LT028', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:44:01'),
(30, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:44:13'),
(31, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:45:23'),
(32, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:46:41'),
(33, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:51:45'),
(34, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:52:14'),
(35, 1, 1, 'REPORT_PRINT', 'assets', NULL, 'Printed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:52:19'),
(36, 1, 1, 'ASSET_CREATE', 'assets', '15', 'Created asset REB/MSA-GSRMR/LT014', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:54:08'),
(37, 1, 1, 'ASSET_CREATE', 'assets', '16', 'Created asset REB/MSA-GSRMR/LT012', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:00:11'),
(38, 1, 1, 'ASSET_CREATE', 'assets', '17', 'Created asset REB/MSA-GSRMR/LT024', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:01:48'),
(39, 1, 1, 'ASSET_CREATE', 'assets', '18', 'Created asset REB/MSA-GSRMR/LT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:04:27'),
(40, 1, 1, 'ASSET_CREATE', 'assets', '19', 'Created asset REB/MSA-GSRMR/LT011', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:07:25'),
(41, 1, 1, 'ASSET_CREATE', 'assets', '20', 'Created asset REB/MSA-GSRMR/LT009', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:10:12'),
(42, 1, 1, 'ASSET_CREATE', 'assets', '21', 'Created asset REB/MSA-GSRMR/LT042', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:15:48'),
(43, 1, 1, 'ASSET_CREATE', 'assets', '22', 'Created asset REB/MSA-GSRMR/LT098', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:23:30'),
(44, 1, 1, 'ASSET_CREATE', 'assets', '23', 'Created asset REB/MSA-GSRMR/LT004', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:24:24'),
(45, 1, 1, 'ASSET_UPDATE', 'assets', '22', 'Updated asset REB/MSA-GSRMR/LT098', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:26:35'),
(46, 1, 1, 'ASSET_UPDATE', 'assets', '17', 'Updated asset REB/MSA-GSRMR/LT024', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:30:13'),
(47, 1, 1, 'ASSET_UPDATE', 'assets', '17', 'Updated asset REB/MSA-GSRMR/LT024', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:31:40'),
(48, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:35:25'),
(49, 1, 1, 'MAINT_CREATE', 'maintenance_logs', '0', 'Reported issue for asset REB/MSA-GSRMR/LT024', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:36:01'),
(50, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:36:48'),
(51, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:37:02'),
(52, 1, 1, 'REPORT_EXPORT', 'assets', NULL, 'Exported inventory report (Excel/CSV)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:37:08'),
(53, 1, 1, 'REPORT_PRINT', 'maintenance_logs', NULL, 'Printed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:40:59'),
(54, 1, 1, 'REPORT_PRINT', 'maintenance_logs', NULL, 'Printed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:41:29'),
(55, 1, 1, 'REPORT_PRINT', 'maintenance_logs', NULL, 'Printed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:47:28'),
(56, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 13:37:28'),
(57, 1, 1, 'ASSET_CREATE', 'assets', '24', 'Created asset REB/MSA-GSRMR/LT055', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 13:40:06'),
(58, 1, 1, 'ASSET_CREATE', 'assets', '25', 'Created asset REB/MSA-GSRMR/LT029', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 13:42:08'),
(59, 1, 1, 'ASSET_CREATE', 'assets', '26', 'Created asset REB/MSA-GSRMR/LT033', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 13:43:23'),
(60, 1, 1, 'ASSET_CREATE', 'assets', '27', 'Created asset REB/MSA-GSRMR/LT005', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 13:57:37'),
(61, 1, 1, 'ASSET_CREATE', 'assets', '28', 'Created asset REB/MSA-GSRMR/LT019', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 13:58:34'),
(62, 1, 1, 'ASSET_CREATE', 'assets', '29', 'Created asset REB/MSA-GSRMR/LT015', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 13:59:25'),
(63, 1, 1, 'ASSET_CREATE', 'assets', '30', 'Created asset REB/MSA-GSRMR/LT020', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:00:28'),
(64, 1, 1, 'ASSET_CREATE', 'assets', '31', 'Created asset REB/MSA-GSRMR/LT010', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:01:47'),
(65, 1, 1, 'ASSET_CREATE', 'assets', '32', 'Created asset REB/MSA-GSRMR/LT036', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:05:25'),
(66, 1, 1, 'ASSET_CREATE', 'assets', '33', 'Created asset REB/MSA-GSRMR/LT037', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:06:34'),
(67, 1, 1, 'ASSET_CREATE', 'assets', '34', 'Created asset REB/MSA-GSRMR/LT031', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:08:17'),
(68, 1, 1, 'ASSET_CREATE', 'assets', '35', 'Created asset REB/MSA-GSRMR/LT030', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:09:03'),
(69, 1, 1, 'ASSET_CREATE', 'assets', '36', 'Created asset REB/MSA-GSRMR/LT035', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:10:06'),
(70, 1, 1, 'ASSIGN_RETURN', 'asset_assignments', '1', 'Returned asset REB/MSA-GSRMR/LT038', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:20:29'),
(71, 1, 1, 'ASSET_UPDATE', 'assets', '35', 'Updated asset REB/MSA-GSRMR/LT030', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:25:29'),
(72, 1, 1, 'ASSET_CREATE', 'assets', '37', 'Created asset REB/MSA-GSRMR/LT044', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:30:21'),
(73, 1, 1, 'ASSET_CREATE', 'assets', '38', 'Created asset REB/MSA-GSRMR/LT016', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:33:00'),
(74, 1, 1, 'ASSET_CREATE', 'assets', '39', 'Created asset REB/MSA-GSRMR/LT043', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:33:59'),
(75, 1, 1, 'ASSET_CREATE', 'assets', '40', 'Created asset REB/MSA-GSRMR/LT040', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:35:14'),
(76, 1, 1, 'ASSET_CREATE', 'assets', '42', 'Created asset REB/MSA-GSRMR/LT041', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:37:11'),
(77, 1, 1, 'ASSET_CREATE', 'assets', '43', 'Created asset REB/MSA-GSRMR/LT003', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:51:37'),
(78, 1, 1, 'ASSET_CREATE', 'assets', '44', 'Created asset REB/MSA-GSRMR/LT025', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:53:25'),
(79, 1, 1, 'ASSET_CREATE', 'assets', '45', 'Created asset REB/MSA-GSRMR/LT021', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:54:16'),
(80, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:55:02'),
(81, 1, 1, 'ASSET_UPDATE', 'assets', '38', 'Updated asset REB/MSA-GSRMR/LT016', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:55:48'),
(82, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:55:54'),
(83, 1, 1, 'ASSET_UPDATE', 'assets', '37', 'Updated asset REB/MSA-GSRMR/LT044', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 15:08:31'),
(84, 1, 1, 'ASSET_CREATE', 'assets', '46', 'Created asset REB/MSA-GSRMR/LT103', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 15:45:14'),
(85, 1, 1, 'ASSET_CREATE', 'assets', '47', 'Created asset REB/MSA-GSRMR/PRJ002', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 15:49:19'),
(86, 1, 1, 'ASSET_CREATE', 'assets', '48', 'Created asset REB/MSA-GSRMR/PRJ01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:05:28'),
(87, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:15:16'),
(88, 1, 1, 'REPORT_EXPORT', 'assets', NULL, 'Exported inventory report (Excel/CSV)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:15:25'),
(89, 1, 1, 'REPORT_PRINT', 'maintenance_logs', NULL, 'Printed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:18:16'),
(90, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:21:16'),
(91, 1, 1, 'USER_CREATE', 'users', '2', 'Created user Peter', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:22:38'),
(92, 1, 2, 'LOGIN', 'users', '2', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:23:19'),
(93, 1, 2, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:23:37'),
(94, 1, 2, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:23:49'),
(95, 1, 1, 'LOGIN', 'users', '1', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:24:38'),
(96, 1, 1, 'USER_UPDATE', 'users', '2', 'Updated user Peter', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:24:57'),
(97, 1, 2, 'LOGIN', 'users', '2', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:25:19'),
(98, 1, 2, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:25:38'),
(99, 1, 2, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:26:05'),
(100, 1, 2, 'REPORT_EXPORT', 'assets', NULL, 'Exported inventory report (Excel/CSV)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:26:11'),
(101, 1, 1, 'LOGIN', 'users', '1', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:27:06'),
(102, 1, 1, 'ASSET_UPDATE', 'assets', '46', 'Updated asset REB/MSA-GSRMR/LT103', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:27:38'),
(103, 1, 1, 'ASSET_CREATE', 'assets', '49', 'Created asset REB/MSA-GSRMR/LT007', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:53:37'),
(104, 1, 1, 'LOGIN', 'users', '1', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:24:43'),
(105, 1, 1, 'ASSET_CREATE', 'assets', '50', 'Created asset REB/MSA-GSRMR/LT045', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:27:24'),
(106, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:28:10'),
(107, 1, 1, 'REPORT_PRINT', 'assets', NULL, 'Printed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:28:37'),
(108, 1, 1, 'ASSET_UPDATE', 'assets', '50', 'Updated asset REB/MSA-GSRMR/LT045', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:32:39'),
(109, 1, 1, 'ASSIGN_CREATE', 'asset_assignments', '0', 'Assigned asset REB/MSA-GSRMR/LT044 to Teacher: HAKORIMANA Theodomir', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:32:52'),
(110, 1, 1, 'ASSIGN_RETURN', 'asset_assignments', '1', 'Returned asset REB/MSA-GSRMR/LT044', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:33:03'),
(111, 1, 1, 'ASSIGN_CREATE', 'asset_assignments', '0', 'Assigned asset REB/MSA-GSRMR/LT045 to Teacher: HAKORIMANA Theodomir', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:34:01'),
(112, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:34:14'),
(113, 1, 1, 'ASSET_CREATE', 'assets', '51', 'Created asset REB/MSA-GSRMR/LT023', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:37:59'),
(114, 1, 1, 'ASSIGN_CREATE', 'asset_assignments', '0', 'Assigned asset REB/MSA-GSRMR/LT023 to Teacher: IBYIMANA Angelus', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:40:13'),
(115, 1, 1, 'ASSET_CREATE', 'assets', '52', 'Created asset REB/MSA-GSRMR/LT100', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 15:41:58'),
(116, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 15:47:34'),
(117, 1, 1, 'LOGIN', 'users', '1', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-22 13:16:30'),
(118, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-22 13:51:49'),
(119, 1, 1, 'ASSET_CREATE', 'assets', '53', 'Created asset HP 250 G6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 08:42:16'),
(120, 1, 1, 'ASSIGN_CREATE', 'asset_assignments', '0', 'Assigned asset HP 250 G6 to Head Teacher: Soeur UWIZEYIMANA Valentine', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 08:50:47'),
(121, 1, 1, 'MAINT_CREATE', 'maintenance_logs', '0', 'Reported issue for asset HP 250 G6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 09:07:45'),
(122, 1, 1, 'MAINT_UPDATE', 'maintenance_logs', '1', 'Updated maintenance status for asset REB/MSA-GSRMR/LT024 to Open', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 09:09:10'),
(123, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 09:11:23'),
(124, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 09:13:12'),
(125, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 09:13:45'),
(126, 1, 1, 'ASSET_CREATE', 'assets', '54', 'Created asset REB/MSA-GSRMR/LT107', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 10:48:56'),
(127, 1, 1, 'MAINT_UPDATE', 'maintenance_logs', '2', 'Updated maintenance status for asset HP 250 G6 to Open', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 10:51:12'),
(128, 1, 1, 'MAINT_CREATE', 'maintenance_logs', '0', 'Reported issue for asset REB/MSA-GSRMR/LT107', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 10:51:48'),
(129, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 10:56:18'),
(130, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 11:24:57'),
(131, 1, 1, 'REPORT_EXPORT', 'assets', NULL, 'Exported inventory report (Excel/CSV)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 11:25:20'),
(132, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 11:25:49'),
(133, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 11:25:55'),
(134, 1, 1, 'REPORT_PRINT', 'assets', NULL, 'Printed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 11:26:18'),
(135, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 11:26:38'),
(136, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 11:26:44'),
(137, 1, 1, 'REPORT_PRINT', 'assets', NULL, 'Printed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 11:26:47'),
(138, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 11:31:49'),
(139, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 11:54:44'),
(140, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 11:54:53'),
(141, 1, 1, 'REPORT_VIEW', 'maintenance_logs', NULL, 'Viewed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 11:55:05'),
(142, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 11:55:17'),
(143, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 11:55:17'),
(144, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 11:55:27'),
(145, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 11:55:38'),
(146, 1, 1, 'REPORT_VIEW', 'maintenance_logs', NULL, 'Viewed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 11:55:49'),
(147, 1, 1, 'LOGIN', 'users', '1', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 12:38:28'),
(148, 1, 1, 'REPORT_VIEW', 'maintenance_logs', NULL, 'Viewed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 12:39:29'),
(149, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 12:39:57'),
(150, 1, 1, 'REPORT_PRINT', 'asset_assignments', NULL, 'Printed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 12:40:02'),
(151, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 12:40:12'),
(152, 1, 1, 'REPORT_VIEW', 'maintenance_logs', NULL, 'Viewed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 12:40:16'),
(153, 1, 1, 'REPORT_PRINT', 'maintenance_logs', NULL, 'Printed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 12:40:20'),
(154, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 12:45:41'),
(155, 1, 1, 'REPORT_PRINT', 'asset_assignments', NULL, 'Printed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 12:45:48'),
(156, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 12:46:01'),
(157, 1, 1, 'ASSIGN_RETURN', 'asset_assignments', '4', 'Returned asset HP 250 G6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 12:46:16'),
(158, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 12:46:20'),
(159, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 12:46:30'),
(160, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 12:46:34'),
(161, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 12:46:41'),
(162, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 12:46:55'),
(163, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:17:12'),
(164, 1, 1, 'REPORT_PRINT', 'assets', NULL, 'Printed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:17:20'),
(165, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:20:14'),
(166, 1, 1, 'REPORT_VIEW', 'maintenance_logs', NULL, 'Viewed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:20:23'),
(167, 1, 1, 'REPORT_PRINT', 'maintenance_logs', NULL, 'Printed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:20:26'),
(168, 1, 1, 'REPORT_VIEW', 'maintenance_logs', NULL, 'Viewed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:22:18'),
(169, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:29:14'),
(170, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:29:20'),
(171, 1, 1, 'REPORT_PRINT', 'assets', NULL, 'Printed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:29:23'),
(172, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:29:48'),
(173, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 16:47:09'),
(174, 1, 1, 'REPORT_PRINT', 'asset_assignments', NULL, 'Printed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 16:47:14'),
(175, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 17:10:19'),
(176, 1, 1, 'REPORT_VIEW', 'maintenance_logs', NULL, 'Viewed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:01:28'),
(177, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:01:36'),
(178, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:01:43'),
(179, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:02:46'),
(180, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:03:28'),
(181, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:03:36'),
(182, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:03:44'),
(183, 1, 1, 'ASSET_UPDATE', 'assets', '53', 'Updated asset HP 250 G6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:07:30'),
(184, 1, 1, 'ASSET_UPDATE', 'assets', '52', 'Updated asset REB/MSA-GSRMR/LT100', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:07:45'),
(185, 1, 1, 'ASSET_UPDATE', 'assets', '51', 'Updated asset REB/MSA-GSRMR/LT023', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:09:54'),
(186, 1, 1, 'ASSET_UPDATE', 'assets', '50', 'Updated asset REB/MSA-GSRMR/LT045', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:10:11'),
(187, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:10:19'),
(188, 1, 1, 'REPORT_PRINT', 'asset_assignments', NULL, 'Printed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:10:23'),
(189, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:11:01'),
(190, 1, 1, 'REPORT_VIEW', 'maintenance_logs', NULL, 'Viewed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:14:25'),
(191, 1, 1, 'REPORT_PRINT', 'maintenance_logs', NULL, 'Printed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:14:28'),
(192, 1, 1, 'REPORT_VIEW', 'maintenance_logs', NULL, 'Viewed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:14:39'),
(193, 1, 1, 'REPORT_VIEW', 'maintenance_logs', NULL, 'Viewed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:14:49'),
(194, 1, 1, 'REPORT_VIEW', 'maintenance_logs', NULL, 'Viewed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:14:54'),
(195, 1, 1, 'ASSET_UPDATE', 'assets', '49', 'Updated asset REB/MSA-GSRMR/LT007', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:15:31'),
(196, 1, 1, 'ASSET_UPDATE', 'assets', '48', 'Updated asset REB/MSA-GSRMR/PRJ01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:16:01'),
(197, 1, 1, 'ASSET_UPDATE', 'assets', '49', 'Updated asset REB/MSA-GSRMR/LT007', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:16:32'),
(198, 1, 1, 'ASSET_UPDATE', 'assets', '48', 'Updated asset REB/MSA-GSRMR/PRJ01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:16:47'),
(199, 1, 1, 'ASSET_UPDATE', 'assets', '47', 'Updated asset REB/MSA-GSRMR/PRJ002', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:16:59'),
(200, 1, 1, 'ASSET_UPDATE', 'assets', '46', 'Updated asset REB/MSA-GSRMR/LT103', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:17:55'),
(201, 1, 1, 'ASSET_UPDATE', 'assets', '45', 'Updated asset REB/MSA-GSRMR/LT021', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:18:12'),
(202, 1, 1, 'ASSET_UPDATE', 'assets', '44', 'Updated asset REB/MSA-GSRMR/LT025', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:18:26'),
(203, 1, 1, 'ASSIGN_CREATE', 'asset_assignments', '0', 'Assigned asset REB/MSA-GSRMR/LT100 to DOD: BAHATI Adrien', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:22:22'),
(204, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:22:38'),
(205, 1, 1, 'REPORT_PRINT', 'asset_assignments', NULL, 'Printed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:22:41'),
(206, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:23:15'),
(207, 1, 1, 'REPORT_VIEW', 'maintenance_logs', NULL, 'Viewed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:23:29'),
(208, 1, 1, 'REPORT_PRINT', 'maintenance_logs', NULL, 'Printed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:23:33'),
(209, 1, 1, 'REPORT_VIEW', 'maintenance_logs', NULL, 'Viewed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:23:52'),
(210, 1, 1, 'MAINT_UPDATE', 'maintenance_logs', '1', 'Updated maintenance status for asset REB/MSA-GSRMR/LT024 to Open', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:24:42'),
(211, 1, 1, 'MAINT_UPDATE', 'maintenance_logs', '2', 'Updated maintenance status for asset HP 250 G6 to Open', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:25:04'),
(212, 1, 1, 'REPORT_PRINT', 'maintenance_logs', NULL, 'Printed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:25:10'),
(213, 1, 1, 'REPORT_VIEW', 'maintenance_logs', NULL, 'Viewed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:27:18'),
(214, 1, 1, 'REPORT_PRINT', 'maintenance_logs', NULL, 'Printed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:27:26'),
(215, 1, 1, 'REPORT_VIEW', 'maintenance_logs', NULL, 'Viewed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:27:56'),
(216, 1, 1, 'ASSIGN_RETURN', 'asset_assignments', '5', 'Returned asset REB/MSA-GSRMR/LT100 with adapter status: Working', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:37:01'),
(217, 1, 1, 'ASSIGN_CREATE', 'asset_assignments', '0', 'Assigned asset REB/MSA-GSRMR/LT100 to DOD: BAHATI Adrien', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:41:00'),
(218, 1, 1, 'ASSIGN_RETURN', 'asset_assignments', '6', 'Returned asset REB/MSA-GSRMR/LT100 with adapter status: Damaged', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:41:16'),
(219, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:42:00'),
(220, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:42:09'),
(221, 1, 1, 'REPORT_PRINT', 'asset_assignments', NULL, 'Printed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:42:13'),
(222, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:42:37');
INSERT INTO `audit_logs` (`id`, `school_id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(223, 1, 1, 'REPORT_VIEW', 'maintenance_logs', NULL, 'Viewed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:42:47'),
(224, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:42:51'),
(225, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:43:59'),
(226, 1, 1, 'ASSIGN_CREATE', 'asset_assignments', '0', 'Assigned asset REB/MSA-GSRMR/LT100 to DOD: BAHATI Adrien', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:45:49'),
(227, 1, 1, 'ASSET_UPDATE', 'assets', '52', 'Updated asset REB/MSA-GSRMR/LT100', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:46:15'),
(228, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:46:24'),
(229, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:48:44'),
(230, 1, 1, 'REPORT_PRINT', 'asset_assignments', NULL, 'Printed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:48:50'),
(231, 1, 1, 'REPORT_PRINT', 'asset_assignments', NULL, 'Printed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:49:15'),
(232, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:49:31'),
(233, 1, 1, 'ASSET_UPDATE', 'assets', '43', 'Updated asset REB/MSA-GSRMR/LT003', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:52:30'),
(234, 1, 1, 'ASSET_UPDATE', 'assets', '42', 'Updated asset REB/MSA-GSRMR/LT041', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:52:44'),
(235, 1, 1, 'ASSET_UPDATE', 'assets', '40', 'Updated asset REB/MSA-GSRMR/LT040', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:52:58'),
(236, 1, 1, 'ASSET_UPDATE', 'assets', '39', 'Updated asset REB/MSA-GSRMR/LT043', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:53:11'),
(237, 1, 1, 'ASSET_UPDATE', 'assets', '38', 'Updated asset REB/MSA-GSRMR/LT016', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:53:40'),
(238, 1, 1, 'ASSET_UPDATE', 'assets', '37', 'Updated asset REB/MSA-GSRMR/LT044', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:54:02'),
(239, 1, 1, 'ASSET_UPDATE', 'assets', '36', 'Updated asset REB/MSA-GSRMR/LT035', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:54:39'),
(240, 1, 1, 'ASSET_UPDATE', 'assets', '35', 'Updated asset REB/MSA-GSRMR/LT030', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:54:54'),
(241, 1, 1, 'ASSET_UPDATE', 'assets', '34', 'Updated asset REB/MSA-GSRMR/LT031', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:55:04'),
(242, 1, 1, 'ASSET_UPDATE', 'assets', '33', 'Updated asset REB/MSA-GSRMR/LT037', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:55:14'),
(243, 1, 1, 'ASSET_UPDATE', 'assets', '32', 'Updated asset REB/MSA-GSRMR/LT036', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:55:25'),
(244, 1, 1, 'ASSET_UPDATE', 'assets', '31', 'Updated asset REB/MSA-GSRMR/LT010', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:55:45'),
(245, 1, 1, 'ASSET_UPDATE', 'assets', '30', 'Updated asset REB/MSA-GSRMR/LT020', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:55:57'),
(246, 1, 1, 'ASSET_UPDATE', 'assets', '29', 'Updated asset REB/MSA-GSRMR/LT015', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:56:38'),
(247, 1, 1, 'ASSET_UPDATE', 'assets', '28', 'Updated asset REB/MSA-GSRMR/LT019', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:57:14'),
(248, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 18:58:43'),
(249, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 19:02:12'),
(250, 1, 1, 'REPORT_PRINT', 'assets', NULL, 'Printed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 19:02:35'),
(251, 1, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 19:03:18'),
(252, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 19:03:29'),
(253, 1, 1, 'REPORT_PRINT', 'asset_assignments', NULL, 'Printed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 19:03:50'),
(254, 1, 1, 'REPORT_VIEW', 'asset_assignments', NULL, 'Viewed assignments report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 19:04:08'),
(255, 1, 1, 'REPORT_VIEW', 'maintenance_logs', NULL, 'Viewed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 19:04:13'),
(256, 1, 1, 'LOGIN', 'users', '1', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 19:22:21');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(10) UNSIGNED NOT NULL,
  `school_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `school_id`, `name`) VALUES
(1, 1, 'ICT Room'),
(2, 1, 'Office'),
(3, 1, 'Classroom'),
(4, 1, 'Library');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_logs`
--

CREATE TABLE `maintenance_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `school_id` int(10) UNSIGNED NOT NULL,
  `asset_id` int(10) UNSIGNED NOT NULL,
  `issue_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reported_date` date NOT NULL,
  `action_taken` text COLLATE utf8mb4_unicode_ci,
  `technician_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `status` enum('Open','In Progress','Resolved') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Open',
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `maintenance_logs`
--

INSERT INTO `maintenance_logs` (`id`, `school_id`, `asset_id`, `issue_description`, `reported_date`, `action_taken`, `technician_name`, `cost`, `status`, `created_by`, `created_at`) VALUES
(1, 1, 17, 'The laptop powers on briefly when the power button is pressed but shows no display and shuts down after a few seconds, even after multiple attempts.', '2026-01-28', '', NULL, NULL, 'Open', 1, '2026-01-28 11:36:01'),
(2, 1, 53, 'The cover of this laptop has unfortunately been damaged and needs replacement.', '2026-02-23', '', NULL, '80000.00', 'Open', 1, '2026-02-23 09:07:45'),
(3, 1, 54, 'The cover of this laptop has unfortunately been damaged and needs replacement.', '2026-02-23', NULL, NULL, NULL, 'Open', 1, '2026-02-23 10:51:48');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'admin', 'IT Technician - full access'),
(2, 'teacher', 'ICT Teacher/Lab Assistant - manage usage & report issues'),
(3, 'viewer', 'School management - view only');

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`id`, `name`, `address`, `created_at`) VALUES
(1, 'GS Remera TSS', 'Remera, Musanze', '2026-02-23 19:14:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `school_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `school_id`, `role_id`, `username`, `full_name`, `email`, `password_hash`, `is_active`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'admin', 'TWIZEYIMANA Elie', 'twizeyimana1elia@gmail.com', '$2y$10$ncXUzMfqM8vOawGq2LEpq.75sQp2HD3KX2X0gcIZd12skJ8Fu6sAy', 1, '2026-02-23 19:22:21', '2026-01-28 06:54:42', '2026-02-23 19:22:21'),
(2, 1, 3, 'Peter', 'TUYIZERE Peter', NULL, '$2y$10$kIcUt9E5cWWbIh1Bnxn/R.nF6OvQXu15IV.XOxtVxtHplV6CSjCNi', 1, '2026-01-28 16:25:19', '2026-01-28 16:22:38', '2026-02-23 19:10:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `asset_code` (`asset_code`),
  ADD KEY `idx_assets_status` (`status`),
  ADD KEY `idx_assets_condition` (`asset_condition`),
  ADD KEY `idx_assets_category` (`category_id`),
  ADD KEY `idx_assets_location` (`location_id`);

--
-- Indexes for table `asset_assignments`
--
ALTER TABLE `asset_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_assign_user` (`created_by`),
  ADD KEY `idx_assign_asset` (`asset_id`),
  ADD KEY `idx_assign_open` (`returned_date`);

--
-- Indexes for table `asset_categories`
--
ALTER TABLE `asset_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_audit_user` (`user_id`),
  ADD KEY `idx_audit_created` (`created_at`),
  ADD KEY `idx_audit_action` (`action`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_maint_user` (`created_by`),
  ADD KEY `idx_maint_asset` (`asset_id`),
  ADD KEY `idx_maint_status` (`status`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `asset_assignments`
--
ALTER TABLE `asset_assignments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `asset_categories`
--
ALTER TABLE `asset_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=257;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assets`
--
ALTER TABLE `assets`
  ADD CONSTRAINT `fk_assets_category` FOREIGN KEY (`category_id`) REFERENCES `asset_categories` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_assets_location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `asset_assignments`
--
ALTER TABLE `asset_assignments`
  ADD CONSTRAINT `fk_assign_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_assign_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  ADD CONSTRAINT `fk_maint_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_maint_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

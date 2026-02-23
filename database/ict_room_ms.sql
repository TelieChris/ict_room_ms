-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 23, 2026 at 09:59 AM
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
  `asset_code` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `asset_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `brand` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial_number` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `asset_condition` enum('New','Good','Fair','Damaged') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Good',
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

INSERT INTO `assets` (`id`, `asset_code`, `asset_name`, `category_id`, `brand`, `model`, `serial_number`, `purchase_date`, `asset_condition`, `status`, `location_id`, `image_path`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'REB/MSA-GSRMR/LT038', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2178', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 09:31:44', '2026-01-28 14:20:29'),
(2, 'REB/MSA-GSRMR/LT018', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2253', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 09:46:41', NULL),
(3, 'REB/MSA-GSRMR/LT104', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2270', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 09:48:22', NULL),
(4, 'REB/MSA-GSRMR/LT002', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2222', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 09:56:34', NULL),
(5, 'REB/MSA-GSRMR/LT034', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2161', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 10:01:36', NULL),
(6, 'REB/MSA-GSRMR/LT039', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2237', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 10:10:39', NULL),
(7, 'REB/MSA-GSRMR/LT046', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2112', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 10:12:17', NULL),
(8, 'REB/MSA-GSRMR/LT013', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2179', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 10:13:28', NULL),
(9, 'REB/MSA-GSRMR/LT032', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2250', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 10:14:10', NULL),
(10, 'REB/MSA-GSRMR/LT017', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2128', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 10:15:14', NULL),
(11, 'REB/MSA-GSRMR/LT026', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2177', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 10:21:56', '2026-01-28 10:22:38'),
(12, 'REB/MSA-GSRMR/LT027', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2183', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 10:31:09', NULL),
(13, 'REB/MSA-GSRMR/LT028', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2101', NULL, 'Fair', 'Available', 1, NULL, 'The battery is damaged.', '2026-01-28 10:34:34', '2026-01-28 10:44:01'),
(15, 'REB/MSA-GSRMR/LT014', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AA2125', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 10:54:08', NULL),
(16, 'REB/MSA-GSRMR/LT012', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2164', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 11:00:11', NULL),
(17, 'REB/MSA-GSRMR/LT024', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2184', NULL, 'Damaged', 'Maintenance', 1, NULL, 'This PC doesn\'t power on.', '2026-01-28 11:01:48', '2026-01-28 11:31:40'),
(18, 'REB/MSA-GSRMR/LT001', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2223', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 11:04:26', NULL),
(19, 'REB/MSA-GSRMR/LT011', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2114', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 11:07:25', NULL),
(20, 'REB/MSA-GSRMR/LT009', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2210', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 11:10:12', NULL),
(21, 'REB/MSA-GSRMR/LT042', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2240', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 11:15:48', NULL),
(22, 'REB/MSA-GSRMR/LT098', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '305AAA1306', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 11:23:30', '2026-01-28 11:26:35'),
(23, 'REB/MSA-GSRMR/LT004', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2103', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 11:24:24', NULL),
(24, 'REB/MSA-GSRMR/LT055', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2096', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 13:40:06', NULL),
(25, 'REB/MSA-GSRMR/LT029', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2175', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 13:42:08', NULL),
(26, 'REB/MSA-GSRMR/LT033', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2212', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 13:43:23', NULL),
(27, 'REB/MSA-GSRMR/LT005', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2244', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 13:57:37', NULL),
(28, 'REB/MSA-GSRMR/LT019', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2192', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 13:58:34', NULL),
(29, 'REB/MSA-GSRMR/LT015', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2214', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 13:59:25', NULL),
(30, 'REB/MSA-GSRMR/LT020', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2260', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 14:00:28', NULL),
(31, 'REB/MSA-GSRMR/LT010', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2236', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 14:01:47', NULL),
(32, 'REB/MSA-GSRMR/LT036', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2165', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 14:05:25', NULL),
(33, 'REB/MSA-GSRMR/LT037', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2215', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 14:06:34', NULL),
(34, 'REB/MSA-GSRMR/LT031', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2120', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 14:08:17', NULL),
(35, 'REB/MSA-GSRMR/LT030', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2156', NULL, 'Fair', 'Available', 1, NULL, 'Left-click on the touchpad does not work.', '2026-01-28 14:09:03', '2026-01-28 14:25:29'),
(36, 'REB/MSA-GSRMR/LT035', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2205', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 14:10:06', NULL),
(37, 'REB/MSA-GSRMR/LT044', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2146', NULL, 'Fair', 'Available', 1, NULL, 'The battery is damaged.', '2026-01-28 14:30:21', '2026-02-20 14:33:03'),
(38, 'REB/MSA-GSRMR/LT016', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2248', NULL, 'Fair', 'Available', 1, NULL, 'The battery is damaged.', '2026-01-28 14:33:00', '2026-01-28 14:55:48'),
(39, 'REB/MSA-GSRMR/LT043', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2167', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 14:33:59', NULL),
(40, 'REB/MSA-GSRMR/LT040', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2217', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 14:35:14', NULL),
(42, 'REB/MSA-GSRMR/LT041', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2066', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 14:37:11', NULL),
(43, 'REB/MSA-GSRMR/LT003', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA1954', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 14:51:37', NULL),
(44, 'REB/MSA-GSRMR/LT025', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2207', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 14:53:25', NULL),
(45, 'REB/MSA-GSRMR/LT021', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2242', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 14:54:16', NULL),
(46, 'REB/MSA-GSRMR/LT103', 'POSITIVO Laptop', 2, 'POSITIVO', '14CLE-I', '156AAC06353', NULL, 'Fair', 'Available', 1, NULL, 'The battery is damaged.', '2026-01-28 15:45:14', '2026-01-28 16:27:38'),
(47, 'REB/MSA-GSRMR/PRJ002', 'Optoma Projector', 6, 'POSITIVO', 'DASSLU', 'Q7BU915AAAAAC0357', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 15:49:19', NULL),
(48, 'REB/MSA-GSRMR/PRJ01', 'BENQ Projector', 6, 'BENQ', 'M5560', 'PD9BN04142000', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 16:05:28', NULL),
(49, 'REB/MSA-GSRMR/LT007', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2121', NULL, 'Good', 'Available', 1, NULL, NULL, '2026-01-28 16:53:37', NULL),
(50, 'REB/MSA-GSRMR/LT045', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2221', NULL, 'Good', 'In Use', 1, NULL, NULL, '2026-02-20 14:27:24', '2026-02-20 14:34:01'),
(51, 'REB/MSA-GSRMR/LT023', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA2083', NULL, 'Good', 'In Use', 1, NULL, NULL, '2026-02-20 14:37:59', '2026-02-20 14:40:13'),
(52, 'REB/MSA-GSRMR/LT100', 'POSITIVO Laptop', 2, 'POSITIVO', '11CLE2-R', '076AAA8202', NULL, 'Good', 'Available', 1, NULL, 'Returned by HAHIRUMUREMYI Gilbert, it was given by NSABIMANA Fabien.', '2026-02-20 15:41:58', NULL),
(53, 'HP 250 G6', 'HP Laptop', 2, 'HP', '3168NGW', 'CND8400468', NULL, 'Damaged', 'Maintenance', 2, NULL, NULL, '2026-02-23 08:42:16', '2026-02-23 09:07:45'),
(54, 'REB/MSA-GSRMR/LT107', 'Lenovo laptop', 2, 'Lenovo', '-', 'REB/MSA-GSRMR/LT107', NULL, 'Damaged', 'Maintenance', 2, NULL, NULL, '2026-02-23 10:48:56', '2026-02-23 10:51:48');

-- --------------------------------------------------------

--
-- Table structure for table `asset_assignments`
--

CREATE TABLE `asset_assignments` (
  `id` int(10) UNSIGNED NOT NULL,
  `asset_id` int(10) UNSIGNED NOT NULL,
  `assigned_to_type` enum('ICT Room','Teacher','Class/Department','Head Teacher','DOD','DOS','Accountant') COLLATE utf8mb4_unicode_ci NOT NULL,
  `assigned_to_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assigned_date` date NOT NULL,
  `expected_return_date` date DEFAULT NULL,
  `returned_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `asset_assignments`
--

INSERT INTO `asset_assignments` (`id`, `asset_id`, `assigned_to_type`, `assigned_to_name`, `assigned_date`, `expected_return_date`, `returned_date`, `notes`, `created_by`, `created_at`) VALUES
(2, 50, 'Teacher', 'HAKORIMANA Theodomir', '2026-01-07', '2026-03-30', NULL, 'Facilitation in predaration and teaching', 1, '2026-02-20 14:34:01'),
(3, 51, 'Accountant', 'IBYIMANA Angelus', '2025-11-26', NULL, NULL, 'Using in daily accountant activity', 1, '2026-02-20 14:40:13'),
(4, 53, 'Head Teacher', 'Soeur UWIZEYIMANA Valentine', '2025-09-10', '2026-02-23', NULL, NULL, 1, '2026-02-23 08:50:46');

-- --------------------------------------------------------

--
-- Table structure for table `asset_categories`
--

CREATE TABLE `asset_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `asset_categories`
--

INSERT INTO `asset_categories` (`id`, `name`) VALUES
(1, 'Desktop'),
(2, 'Laptop'),
(3, 'Printer'),
(6, 'Projector'),
(4, 'Router'),
(5, 'Switch'),
(7, 'UPS');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
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

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'LOGIN', 'users', '1', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:07:31'),
(2, 1, 'ASSET_CREATE', 'assets', '1', 'Created asset REB/MSA-GSRMR/LT038', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:31:44'),
(3, 1, 'ASSET_UPDATE', 'assets', '1', 'Updated asset REB/MSA-GSRMR/LT038', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:32:10'),
(4, 1, 'ASSIGN_CREATE', 'asset_assignments', '0', 'Assigned asset REB/MSA-GSRMR/LT038 to Teacher: UMURISA Jane', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:39:18'),
(5, 1, 'ASSET_CREATE', 'assets', '2', 'Created asset REB/MSA-GSRMR/LT018', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:46:41'),
(6, 1, 'ASSET_CREATE', 'assets', '3', 'Created asset REB/MSA-GSRMR/LT104', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:48:22'),
(7, 1, 'ASSET_CREATE', 'assets', '4', 'Created asset REB/MSA-GSRMR/LT002', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:56:34'),
(8, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:57:28'),
(9, 1, 'REPORT_PRINT', 'assets', NULL, 'Printed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:57:44'),
(10, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:58:04'),
(11, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:58:12'),
(12, 1, 'REPORT_PRINT', 'assets', NULL, 'Printed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 09:58:15'),
(13, 1, 'ASSET_CREATE', 'assets', '5', 'Created asset REB/MSA-GSRMR/LT034', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:01:36'),
(14, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:05:12'),
(15, 1, 'REPORT_EXPORT', 'assets', NULL, 'Exported inventory report (Excel/CSV)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:05:17'),
(16, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:07:58'),
(17, 1, 'LOGIN', 'users', '1', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:08:46'),
(18, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:09:10'),
(19, 1, 'ASSET_CREATE', 'assets', '6', 'Created asset REB/MSA-GSRMR/LT039', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:10:39'),
(20, 1, 'ASSET_CREATE', 'assets', '7', 'Created asset REB/MSA-GSRMR/LT046', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:12:17'),
(21, 1, 'ASSET_CREATE', 'assets', '8', 'Created asset REB/MSA-GSRMR/LT013', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:13:28'),
(22, 1, 'ASSET_CREATE', 'assets', '9', 'Created asset REB/MSA-GSRMR/LT032', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:14:10'),
(23, 1, 'ASSET_CREATE', 'assets', '10', 'Created asset REB/MSA-GSRMR/LT017', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:15:14'),
(24, 1, 'USER_UPDATE', 'users', '1', 'Updated user admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:20:18'),
(25, 1, 'ASSET_CREATE', 'assets', '11', 'Created asset REB/MSA-GSRMR/LT026', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:21:56'),
(26, 1, 'ASSET_UPDATE', 'assets', '11', 'Updated asset REB/MSA-GSRMR/LT026', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:22:38'),
(27, 1, 'ASSET_CREATE', 'assets', '12', 'Created asset REB/MSA-GSRMR/LT027', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:31:09'),
(28, 1, 'ASSET_CREATE', 'assets', '13', 'Created asset REB/MSA-GSRMR/LT028', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:34:34'),
(29, 1, 'ASSET_UPDATE', 'assets', '13', 'Updated asset REB/MSA-GSRMR/LT028', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:44:01'),
(30, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:44:13'),
(31, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:45:23'),
(32, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:46:41'),
(33, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:51:45'),
(34, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:52:14'),
(35, 1, 'REPORT_PRINT', 'assets', NULL, 'Printed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:52:19'),
(36, 1, 'ASSET_CREATE', 'assets', '15', 'Created asset REB/MSA-GSRMR/LT014', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 10:54:08'),
(37, 1, 'ASSET_CREATE', 'assets', '16', 'Created asset REB/MSA-GSRMR/LT012', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:00:11'),
(38, 1, 'ASSET_CREATE', 'assets', '17', 'Created asset REB/MSA-GSRMR/LT024', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:01:48'),
(39, 1, 'ASSET_CREATE', 'assets', '18', 'Created asset REB/MSA-GSRMR/LT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:04:27'),
(40, 1, 'ASSET_CREATE', 'assets', '19', 'Created asset REB/MSA-GSRMR/LT011', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:07:25'),
(41, 1, 'ASSET_CREATE', 'assets', '20', 'Created asset REB/MSA-GSRMR/LT009', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:10:12'),
(42, 1, 'ASSET_CREATE', 'assets', '21', 'Created asset REB/MSA-GSRMR/LT042', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:15:48'),
(43, 1, 'ASSET_CREATE', 'assets', '22', 'Created asset REB/MSA-GSRMR/LT098', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:23:30'),
(44, 1, 'ASSET_CREATE', 'assets', '23', 'Created asset REB/MSA-GSRMR/LT004', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:24:24'),
(45, 1, 'ASSET_UPDATE', 'assets', '22', 'Updated asset REB/MSA-GSRMR/LT098', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:26:35'),
(46, 1, 'ASSET_UPDATE', 'assets', '17', 'Updated asset REB/MSA-GSRMR/LT024', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:30:13'),
(47, 1, 'ASSET_UPDATE', 'assets', '17', 'Updated asset REB/MSA-GSRMR/LT024', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:31:40'),
(48, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:35:25'),
(49, 1, 'MAINT_CREATE', 'maintenance_logs', '0', 'Reported issue for asset REB/MSA-GSRMR/LT024', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:36:01'),
(50, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:36:48'),
(51, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:37:02'),
(52, 1, 'REPORT_EXPORT', 'assets', NULL, 'Exported inventory report (Excel/CSV)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:37:08'),
(53, 1, 'REPORT_PRINT', 'maintenance_logs', NULL, 'Printed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:40:59'),
(54, 1, 'REPORT_PRINT', 'maintenance_logs', NULL, 'Printed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:41:29'),
(55, 1, 'REPORT_PRINT', 'maintenance_logs', NULL, 'Printed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 11:47:28'),
(56, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 13:37:28'),
(57, 1, 'ASSET_CREATE', 'assets', '24', 'Created asset REB/MSA-GSRMR/LT055', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 13:40:06'),
(58, 1, 'ASSET_CREATE', 'assets', '25', 'Created asset REB/MSA-GSRMR/LT029', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 13:42:08'),
(59, 1, 'ASSET_CREATE', 'assets', '26', 'Created asset REB/MSA-GSRMR/LT033', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 13:43:23'),
(60, 1, 'ASSET_CREATE', 'assets', '27', 'Created asset REB/MSA-GSRMR/LT005', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 13:57:37'),
(61, 1, 'ASSET_CREATE', 'assets', '28', 'Created asset REB/MSA-GSRMR/LT019', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 13:58:34'),
(62, 1, 'ASSET_CREATE', 'assets', '29', 'Created asset REB/MSA-GSRMR/LT015', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 13:59:25'),
(63, 1, 'ASSET_CREATE', 'assets', '30', 'Created asset REB/MSA-GSRMR/LT020', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:00:28'),
(64, 1, 'ASSET_CREATE', 'assets', '31', 'Created asset REB/MSA-GSRMR/LT010', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:01:47'),
(65, 1, 'ASSET_CREATE', 'assets', '32', 'Created asset REB/MSA-GSRMR/LT036', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:05:25'),
(66, 1, 'ASSET_CREATE', 'assets', '33', 'Created asset REB/MSA-GSRMR/LT037', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:06:34'),
(67, 1, 'ASSET_CREATE', 'assets', '34', 'Created asset REB/MSA-GSRMR/LT031', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:08:17'),
(68, 1, 'ASSET_CREATE', 'assets', '35', 'Created asset REB/MSA-GSRMR/LT030', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:09:03'),
(69, 1, 'ASSET_CREATE', 'assets', '36', 'Created asset REB/MSA-GSRMR/LT035', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:10:06'),
(70, 1, 'ASSIGN_RETURN', 'asset_assignments', '1', 'Returned asset REB/MSA-GSRMR/LT038', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:20:29'),
(71, 1, 'ASSET_UPDATE', 'assets', '35', 'Updated asset REB/MSA-GSRMR/LT030', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:25:29'),
(72, 1, 'ASSET_CREATE', 'assets', '37', 'Created asset REB/MSA-GSRMR/LT044', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:30:21'),
(73, 1, 'ASSET_CREATE', 'assets', '38', 'Created asset REB/MSA-GSRMR/LT016', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:33:00'),
(74, 1, 'ASSET_CREATE', 'assets', '39', 'Created asset REB/MSA-GSRMR/LT043', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:33:59'),
(75, 1, 'ASSET_CREATE', 'assets', '40', 'Created asset REB/MSA-GSRMR/LT040', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:35:14'),
(76, 1, 'ASSET_CREATE', 'assets', '42', 'Created asset REB/MSA-GSRMR/LT041', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:37:11'),
(77, 1, 'ASSET_CREATE', 'assets', '43', 'Created asset REB/MSA-GSRMR/LT003', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:51:37'),
(78, 1, 'ASSET_CREATE', 'assets', '44', 'Created asset REB/MSA-GSRMR/LT025', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:53:25'),
(79, 1, 'ASSET_CREATE', 'assets', '45', 'Created asset REB/MSA-GSRMR/LT021', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:54:16'),
(80, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:55:02'),
(81, 1, 'ASSET_UPDATE', 'assets', '38', 'Updated asset REB/MSA-GSRMR/LT016', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:55:48'),
(82, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 14:55:54'),
(83, 1, 'ASSET_UPDATE', 'assets', '37', 'Updated asset REB/MSA-GSRMR/LT044', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 15:08:31'),
(84, 1, 'ASSET_CREATE', 'assets', '46', 'Created asset REB/MSA-GSRMR/LT103', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 15:45:14'),
(85, 1, 'ASSET_CREATE', 'assets', '47', 'Created asset REB/MSA-GSRMR/PRJ002', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 15:49:19'),
(86, 1, 'ASSET_CREATE', 'assets', '48', 'Created asset REB/MSA-GSRMR/PRJ01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:05:28'),
(87, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:15:16'),
(88, 1, 'REPORT_EXPORT', 'assets', NULL, 'Exported inventory report (Excel/CSV)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:15:25'),
(89, 1, 'REPORT_PRINT', 'maintenance_logs', NULL, 'Printed maintenance report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:18:16'),
(90, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:21:16'),
(91, 1, 'USER_CREATE', 'users', '2', 'Created user Peter', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:22:38'),
(92, 2, 'LOGIN', 'users', '2', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:23:19'),
(93, 2, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:23:37'),
(94, 2, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:23:49'),
(95, 1, 'LOGIN', 'users', '1', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:24:38'),
(96, 1, 'USER_UPDATE', 'users', '2', 'Updated user Peter', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:24:57'),
(97, 2, 'LOGIN', 'users', '2', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:25:19'),
(98, 2, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:25:38'),
(99, 2, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:26:05'),
(100, 2, 'REPORT_EXPORT', 'assets', NULL, 'Exported inventory report (Excel/CSV)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:26:11'),
(101, 1, 'LOGIN', 'users', '1', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:27:06'),
(102, 1, 'ASSET_UPDATE', 'assets', '46', 'Updated asset REB/MSA-GSRMR/LT103', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:27:38'),
(103, 1, 'ASSET_CREATE', 'assets', '49', 'Created asset REB/MSA-GSRMR/LT007', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-28 16:53:37'),
(104, 1, 'LOGIN', 'users', '1', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:24:43'),
(105, 1, 'ASSET_CREATE', 'assets', '50', 'Created asset REB/MSA-GSRMR/LT045', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:27:24'),
(106, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:28:10'),
(107, 1, 'REPORT_PRINT', 'assets', NULL, 'Printed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:28:37'),
(108, 1, 'ASSET_UPDATE', 'assets', '50', 'Updated asset REB/MSA-GSRMR/LT045', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:32:39'),
(109, 1, 'ASSIGN_CREATE', 'asset_assignments', '0', 'Assigned asset REB/MSA-GSRMR/LT044 to Teacher: HAKORIMANA Theodomir', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:32:52'),
(110, 1, 'ASSIGN_RETURN', 'asset_assignments', '1', 'Returned asset REB/MSA-GSRMR/LT044', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:33:03'),
(111, 1, 'ASSIGN_CREATE', 'asset_assignments', '0', 'Assigned asset REB/MSA-GSRMR/LT045 to Teacher: HAKORIMANA Theodomir', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:34:01'),
(112, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:34:14'),
(113, 1, 'ASSET_CREATE', 'assets', '51', 'Created asset REB/MSA-GSRMR/LT023', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:37:59'),
(114, 1, 'ASSIGN_CREATE', 'asset_assignments', '0', 'Assigned asset REB/MSA-GSRMR/LT023 to Teacher: IBYIMANA Angelus', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:40:13'),
(115, 1, 'ASSET_CREATE', 'assets', '52', 'Created asset REB/MSA-GSRMR/LT100', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 15:41:58'),
(116, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 15:47:34'),
(117, 1, 'LOGIN', 'users', '1', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-22 13:16:30'),
(118, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-22 13:51:49'),
(119, 1, 'ASSET_CREATE', 'assets', '53', 'Created asset HP 250 G6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 08:42:16'),
(120, 1, 'ASSIGN_CREATE', 'asset_assignments', '0', 'Assigned asset HP 250 G6 to Head Teacher: Soeur UWIZEYIMANA Valentine', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 08:50:47'),
(121, 1, 'MAINT_CREATE', 'maintenance_logs', '0', 'Reported issue for asset HP 250 G6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 09:07:45'),
(122, 1, 'MAINT_UPDATE', 'maintenance_logs', '1', 'Updated maintenance status for asset REB/MSA-GSRMR/LT024 to Open', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 09:09:10'),
(123, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 09:11:23'),
(124, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 09:13:12'),
(125, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 09:13:45'),
(126, 1, 'ASSET_CREATE', 'assets', '54', 'Created asset REB/MSA-GSRMR/LT107', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 10:48:56'),
(127, 1, 'MAINT_UPDATE', 'maintenance_logs', '2', 'Updated maintenance status for asset HP 250 G6 to Open', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 10:51:12'),
(128, 1, 'MAINT_CREATE', 'maintenance_logs', '0', 'Reported issue for asset REB/MSA-GSRMR/LT107', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 10:51:48'),
(129, 1, 'REPORT_VIEW', 'assets', NULL, 'Viewed inventory report', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 10:56:18');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`) VALUES
(3, 'Classroom'),
(1, 'ICT Room'),
(4, 'Library'),
(2, 'Office');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_logs`
--

CREATE TABLE `maintenance_logs` (
  `id` int(10) UNSIGNED NOT NULL,
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

INSERT INTO `maintenance_logs` (`id`, `asset_id`, `issue_description`, `reported_date`, `action_taken`, `technician_name`, `cost`, `status`, `created_by`, `created_at`) VALUES
(1, 17, 'When the power button is pressed, the laptop powers on briefly but does not display any output on the screen and automatically shuts down after a few seconds. The issue persists after multiple attempts.', '2026-01-28', 'The laptop powers on briefly when the power button is pressed but shows no display and shuts down after a few seconds, even after multiple attempts.', NULL, NULL, 'Open', 1, '2026-01-28 11:36:01'),
(2, 53, 'Cover of this laptop has been damaeged it require to replace it.', '2026-02-23', 'The cover of this laptop has unfortunately been damaged and needs replacement.', NULL, '80000.00', 'Open', 1, '2026-02-23 09:07:45'),
(3, 54, 'The cover of this laptop has unfortunately been damaged and needs replacement.', '2026-02-23', NULL, NULL, NULL, 'Open', 1, '2026-02-23 10:51:48');

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
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
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

INSERT INTO `users` (`id`, `role_id`, `username`, `full_name`, `email`, `password_hash`, `is_active`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin', 'TWIZEYIMANA Elie', 'twizeyimana1elia@gmail.com', '$2y$10$ncXUzMfqM8vOawGq2LEpq.75sQp2HD3KX2X0gcIZd12skJ8Fu6sAy', 1, '2026-02-22 13:16:30', '2026-01-28 06:54:42', '2026-02-22 13:16:30'),
(2, 3, 'Peter', 'TUYIZERE Peter', NULL, '$2y$10$kIcUt9E5cWWbIh1Bnxn/R.nF6OvQXu15IV.XOxtVxtHplV6CSjCNi', 1, '2026-01-28 16:25:19', '2026-01-28 16:22:38', '2026-01-28 16:25:19');

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `asset_categories`
--
ALTER TABLE `asset_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

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

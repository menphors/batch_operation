-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 17, 2019 at 08:36 AM
-- Server version: 5.7.26
-- PHP Version: 7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `batch_operation`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_users`
--

DROP TABLE IF EXISTS `access_users`;
CREATE TABLE IF NOT EXISTS `access_users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `access_users`
--

INSERT INTO `access_users` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'pen.lymeng', 1, NULL, NULL),
(2, 'samol.yuthpanha', 1, NULL, NULL),
(3, 'chhun.sokheang', 1, NULL, NULL),
(4, 'pin.bona', 1, NULL, NULL),
(5, 'pang.peseth', 1, NULL, NULL),
(6, 'ak.hourphenh', 1, NULL, NULL),
(7, 'chhim.pagna', 1, NULL, NULL),
(8, 'hul.narith', 1, NULL, NULL),
(9, 'kang.piseth', 1, NULL, NULL),
(10, 'pen.sonorn', 1, NULL, NULL),
(11, 'leng.sengchourng', 1, NULL, NULL),
(12, 'pek.monich', 1, NULL, NULL),
(13, 'hou.pheakdey', 1, NULL, NULL),
(14, 'khim.mengheng', 1, NULL, NULL),
(15, 'nourn.bora1', 1, NULL, NULL),
(16, 'ry.chhounly', 1, NULL, NULL),
(17, 'sor.denny', 1, NULL, NULL),
(18, 'sok.heang', 1, NULL, NULL),
(19, 'leng.chanleap', 1, NULL, NULL),
(20, 'khun.chamman', 1, NULL, NULL),
(21, 'phal.chantrea', 1, NULL, NULL),
(22, 'touch.teng', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `change_offering_msisdns`
--

DROP TABLE IF EXISTS `change_offering_msisdns`;
CREATE TABLE IF NOT EXISTS `change_offering_msisdns` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `change_offering_schedule_id` int(11) NOT NULL,
  `msisdn` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `change_offering_msisdns`
--

INSERT INTO `change_offering_msisdns` (`id`, `change_offering_schedule_id`, `msisdn`, `created_at`, `updated_at`) VALUES
(1, 2, '86585891', '2018-12-28 08:11:58', '2018-12-28 08:11:58'),
(2, 2, '10968869', '2018-12-28 08:11:58', '2018-12-28 08:11:58'),
(3, 2, '86585891', '2018-12-28 08:11:58', '2018-12-28 08:11:58'),
(4, 4, '86585891', '2018-12-28 08:16:33', '2018-12-28 08:16:33'),
(5, 4, '10968869', '2018-12-28 08:16:33', '2018-12-28 08:16:33'),
(6, 4, '86585891', '2018-12-28 08:16:33', '2018-12-28 08:16:33'),
(7, 63, '87255141', '2019-01-17 00:45:14', '2019-01-17 00:45:14'),
(8, 63, '961193594', '2019-01-17 00:45:14', '2019-01-17 00:45:14'),
(9, 81, '961193594', '2019-01-17 02:04:19', '2019-01-17 02:04:19'),
(10, 82, '961193594', '2019-01-17 02:12:04', '2019-01-17 02:12:04'),
(11, 83, '961193594', '2019-01-17 02:50:10', '2019-01-17 02:50:10'),
(12, 84, '961193594', '2019-01-17 02:55:19', '2019-01-17 02:55:19'),
(13, 85, '961193594', '2019-01-17 03:01:14', '2019-01-17 03:01:14'),
(14, 86, '961193594', '2019-01-17 03:03:20', '2019-01-17 03:03:20'),
(15, 87, '961193594', '2019-01-17 03:06:04', '2019-01-17 03:06:04'),
(16, 95, '87255141', '2019-01-30 01:48:40', '2019-01-30 01:48:40'),
(17, 95, '', '2019-01-30 01:48:40', '2019-01-30 01:48:40'),
(18, 96, '87255141', '2019-01-30 01:55:26', '2019-01-30 01:55:26'),
(19, 96, '', '2019-01-30 01:55:26', '2019-01-30 01:55:26'),
(20, 97, '87255141', '2019-01-30 01:59:07', '2019-01-30 01:59:07'),
(21, 97, '', '2019-01-30 01:59:07', '2019-01-30 01:59:07'),
(22, 98, '69204899', '2019-01-30 02:02:18', '2019-01-30 02:02:18'),
(23, 98, '', '2019-01-30 02:02:18', '2019-01-30 02:02:18'),
(24, 99, '87255141', '2019-01-30 02:20:26', '2019-01-30 02:20:26'),
(25, 99, '', '2019-01-30 02:20:26', '2019-01-30 02:20:26'),
(26, 101, '87255141', '2019-01-30 02:27:50', '2019-01-30 02:27:50'),
(27, 101, '', '2019-01-30 02:27:50', '2019-01-30 02:27:50'),
(28, 102, '87255141', '2019-01-30 02:48:02', '2019-01-30 02:48:02'),
(29, 102, '', '2019-01-30 02:48:02', '2019-01-30 02:48:02');

-- --------------------------------------------------------

--
-- Table structure for table `change_offering_schedules`
--

DROP TABLE IF EXISTS `change_offering_schedules`;
CREATE TABLE IF NOT EXISTS `change_offering_schedules` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `offering_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `effective_date` datetime NOT NULL,
  `execute_date` datetime NOT NULL,
  `remark` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=147 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `change_offering_schedules`
--

INSERT INTO `change_offering_schedules` (`id`, `offering_id`, `effective_date`, `execute_date`, `remark`, `completed`, `user_id`, `created_at`, `updated_at`) VALUES
(83, '1579659517', '2019-01-18 04:11:00', '2019-01-17 16:00:00', 'Test', 1, 1, '2019-01-17 02:50:10', '2019-01-17 02:50:10'),
(84, '1579659517', '2019-02-01 09:55:18', '2019-01-17 16:00:00', 'Test', 1, 1, '2019-01-17 02:55:19', '2019-01-17 02:55:19'),
(85, '1579659517', '2019-02-01 23:59:59', '2019-01-17 16:00:00', 'Test', 1, 1, '2019-01-17 03:01:14', '2019-01-17 03:01:14'),
(86, '1579659517', '2019-02-01 16:59:59', '2019-01-17 16:00:00', 'Test', 1, 1, '2019-01-17 03:03:20', '2019-01-17 03:03:20'),
(87, '1579659517', '2019-01-31 17:00:00', '2019-01-17 16:00:00', 'Test', 1, 1, '2019-01-17 03:06:04', '2019-01-17 03:06:04'),
(88, '1579659517', '2019-01-23 09:41:44', '2019-01-23 00:00:00', 'TEST 2019', 1, 2, '2019-01-23 02:40:44', '2019-01-23 02:40:44'),
(89, '1486071018', '2019-02-01 09:52:42', '2019-01-23 00:00:00', 'TEST 2019 12', 1, 2, '2019-01-23 02:52:42', '2019-01-23 02:52:42'),
(90, '1486071018', '2019-01-23 10:07:00', '2019-01-23 00:00:00', 'TEST Operation', 1, 2, '2019-01-23 03:05:11', '2019-01-23 03:05:11'),
(91, '1579659517', '2019-01-24 04:00:00', '2019-01-24 00:00:00', 'TEST Appointed', 1, 2, '2019-01-23 19:50:02', '2019-01-23 19:50:02'),
(92, '1486071018', '2019-01-30 08:27:28', '2019-01-30 00:00:00', 'TEST', 1, 2, '2019-01-30 01:26:28', '2019-01-30 01:26:28'),
(93, '1579659517', '2019-02-01 08:29:33', '2019-01-30 00:00:00', 'TEST', 1, 2, '2019-01-30 01:29:33', '2019-01-30 01:29:33'),
(94, '1579659517', '2019-01-30 08:40:00', '2019-01-30 00:00:00', 'TESt', 1, 2, '2019-01-30 01:36:18', '2019-01-30 01:36:18'),
(95, '1486071018', '2019-01-31 17:00:00', '2019-01-30 15:50:00', 'TESt', 0, 2, '2019-01-30 01:48:40', '2019-01-30 01:48:40'),
(96, '1486071018', '2019-01-31 17:00:00', '2019-01-30 15:58:00', 'TEST PPPPP', 0, 2, '2019-01-30 01:55:26', '2019-01-30 01:55:26'),
(97, '1786076982', '2019-01-31 17:00:00', '2019-01-30 16:01:00', 'FFFFF', 0, 2, '2019-01-30 01:59:07', '2019-01-30 01:59:07'),
(98, '1786076982', '2019-01-31 17:00:00', '2019-01-30 16:04:00', 'FFF', 0, 2, '2019-01-30 02:02:18', '2019-01-30 02:02:18'),
(99, '1786076982', '2019-01-31 17:00:00', '2019-01-30 16:25:00', 'FFFFF', 0, 2, '2019-01-30 02:20:26', '2019-01-30 02:20:26'),
(100, '1786076982', '2019-01-30 09:28:09', '2019-01-30 00:00:00', 'FFFFFFT TTTTTT', 1, 2, '2019-01-30 02:27:09', '2019-01-30 02:27:09'),
(101, '1486071018', '2019-01-31 17:00:00', '2019-01-30 16:39:00', 'FTESTESTSETSETSETSETESTESTEST', 0, 2, '2019-01-30 02:27:50', '2019-01-30 02:27:50'),
(102, '1486071018', '2019-01-30 09:55:00', '2019-01-30 16:50:00', 'Run AT', 0, 2, '2019-01-30 02:48:02', '2019-01-30 02:48:02'),
(103, '1385995483', '2019-02-13 06:22:09', '2019-02-13 00:00:00', 'Value 3 Down', 1, 2, '2019-02-12 23:21:09', '2019-02-12 23:21:09'),
(104, '1486071018', '2019-02-13 06:22:49', '2019-02-13 00:00:00', 'Test Value 5', 1, 2, '2019-02-12 23:21:49', '2019-02-12 23:21:49'),
(105, '1456030185', '2019-02-18 02:40:48', '2019-02-18 00:00:00', 'Power+', 1, 2, '2019-02-17 19:39:48', '2019-02-17 19:39:48'),
(106, '1456030185', '2019-02-18 02:48:37', '2019-02-18 00:00:00', 'Power+ Enough balance', 1, 2, '2019-02-17 19:47:37', '2019-02-17 19:47:37'),
(107, '701276563', '2019-02-18 03:10:09', '2019-02-18 00:00:00', 'SmartThomMong to Power+1', 1, 2, '2019-02-17 20:09:09', '2019-02-17 20:09:09'),
(108, '1456030185', '2019-03-11 03:42:26', '2019-03-11 00:00:00', 'fix issue', 1, 4, '2019-03-10 20:41:26', '2019-03-10 20:41:26'),
(109, '1456030185', '2019-03-13 14:58:50', '2019-03-13 00:00:00', 'Fix issue cannot call out.', 1, 4, '2019-03-13 07:57:50', '2019-03-13 07:57:50'),
(110, '1682738683', '2019-04-01 06:51:05', '2019-03-14 00:00:00', 'Requested by DOCH Bunnat', 1, 7, '2019-03-13 23:51:05', '2019-03-13 23:51:05'),
(111, '1682738683', '2019-04-01 06:54:05', '2019-03-14 00:00:00', 'Requested by DOCH Bunnat', 1, 7, '2019-03-13 23:54:05', '2019-03-13 23:54:05'),
(112, '1682738683', '2019-04-01 07:03:31', '2019-03-14 00:00:00', 'Requested by DOCH Bunnat', 1, 7, '2019-03-14 00:03:31', '2019-03-14 00:03:31'),
(113, '1682738683', '2019-03-14 07:15:29', '2019-03-14 00:00:00', 'TEST', 1, 2, '2019-03-14 00:14:29', '2019-03-14 00:14:29'),
(114, '1579659517', '2019-04-01 07:16:52', '2019-03-14 00:00:00', 'TTTTTTT', 1, 2, '2019-03-14 00:16:52', '2019-03-14 00:16:52'),
(115, '1486071018', '2019-03-14 07:21:32', '2019-03-14 00:00:00', 'Smart Value 5', 1, 2, '2019-03-14 00:20:32', '2019-03-14 00:20:32'),
(116, '1682738683', '2019-04-01 07:21:10', '2019-03-14 00:00:00', 'TTTTT', 1, 2, '2019-03-14 00:21:10', '2019-03-14 00:21:10'),
(117, '1682738683', '2019-04-01 07:29:50', '2019-03-14 00:00:00', 'Requested by DOCH Bunnat', 1, 7, '2019-03-14 00:29:50', '2019-03-14 00:29:50'),
(118, '1682738683', '2019-04-01 07:34:01', '2019-03-14 00:00:00', 'Requested by DOCH Bunnat, Testing', 1, 7, '2019-03-14 00:34:01', '2019-03-14 00:34:01'),
(119, '1682738683', '2019-04-01 07:46:59', '2019-03-14 00:00:00', 'Requested by DOCH Bunnat', 1, 7, '2019-03-14 00:46:59', '2019-03-14 00:46:59'),
(120, '1682738683', '2019-04-01 07:47:03', '2019-03-14 00:00:00', 'Requested by DOCH Bunnat', 1, 7, '2019-03-14 00:47:03', '2019-03-14 00:47:03'),
(121, '1486071018', '2019-04-01 07:57:03', '2019-03-14 00:00:00', 'Requested by LY Sarida', 1, 6, '2019-03-14 00:57:03', '2019-03-14 00:57:03'),
(122, '1456030185', '2019-03-15 08:10:59', '2019-03-15 00:00:00', 'Fix issue', 1, 4, '2019-03-15 01:09:59', '2019-03-15 01:09:59'),
(123, '1456030185', '2019-03-15 11:15:20', '2019-03-15 00:00:00', 'fix issue', 1, 4, '2019-03-15 04:14:20', '2019-03-15 04:14:20'),
(124, '1456030185', '2019-03-15 11:53:11', '2019-03-15 00:00:00', 'fix issue', 1, 4, '2019-03-15 04:52:11', '2019-03-15 04:52:11'),
(125, '1456030185', '2019-03-15 12:30:17', '2019-03-15 00:00:00', 'fix issue', 1, 4, '2019-03-15 05:29:17', '2019-03-15 05:29:17'),
(126, '971687707', '2019-03-15 12:41:13', '2019-03-15 00:00:00', 'fix issue', 1, 4, '2019-03-15 05:40:13', '2019-03-15 05:40:13'),
(127, '1796163840', '2019-04-01 03:19:17', '2019-03-27 00:00:00', 'Requested by KOEUN Chinda', 1, 6, '2019-03-26 20:19:17', '2019-03-26 20:19:17'),
(128, '1140820383', '2019-04-01 03:45:07', '2019-03-27 00:00:00', 'Requested by DOCH Bunnat', 1, 6, '2019-03-26 20:45:07', '2019-03-26 20:45:07'),
(129, '1486071018', '2019-04-01 01:50:27', '2019-03-28 00:00:00', 'Requested by LY Sarida', 1, 6, '2019-03-27 18:50:27', '2019-03-27 18:50:27'),
(130, '1486071018', '2019-04-01 04:01:00', '2019-03-29 00:00:00', 'RE:KOEUN Chinda', 1, 15, '2019-03-28 21:01:00', '2019-03-28 21:01:00'),
(131, '1486071018', '2019-04-01 09:25:01', '2019-03-29 00:00:00', 'From: SITH Maliya \r\nSent: Friday, March 29, 2019 10:34', 1, 13, '2019-03-29 02:25:01', '2019-03-29 02:25:01'),
(132, '1486071018', '2019-04-01 09:40:23', '2019-03-29 00:00:00', 'Re:SITH Maliya', 1, 13, '2019-03-29 02:40:23', '2019-03-29 02:40:23'),
(133, '1486071018', '2019-04-01 09:46:49', '2019-03-29 00:00:00', 'RE:SITH Maliya', 1, 15, '2019-03-29 02:46:49', '2019-03-29 02:46:49'),
(134, '1486071018', '2019-04-01 09:47:34', '2019-03-29 00:00:00', 'RE:SITH Maliya', 1, 15, '2019-03-29 02:47:34', '2019-03-29 02:47:34'),
(135, '1796163840', '2019-05-01 03:45:39', '2019-04-11 00:00:00', 'Requested by UNG Engheang', 1, 6, '2019-04-10 20:45:39', '2019-04-10 20:45:39'),
(136, '1796163840', '2019-05-01 03:51:25', '2019-04-11 00:00:00', 'Requested by UNG Engheang', 1, 6, '2019-04-10 20:51:25', '2019-04-10 20:51:25'),
(137, '1375693025', '2019-04-24 02:07:41', '2019-04-24 00:00:00', 'testing_changepri', 1, 4, '2019-04-23 19:06:41', '2019-04-23 19:06:41'),
(138, '1575616339', '2019-04-24 02:09:36', '2019-04-24 00:00:00', 'test_CRM', 1, 4, '2019-04-23 19:08:36', '2019-04-23 19:08:36'),
(139, '1300054684', '2019-04-24 02:14:45', '2019-04-24 00:00:00', 'test_to_super60', 1, 4, '2019-04-23 19:13:45', '2019-04-23 19:13:45'),
(140, '1486071018', '2019-04-24 02:23:12', '2019-04-24 00:00:00', 'test_bed', 1, 4, '2019-04-23 19:22:12', '2019-04-23 19:22:12'),
(141, '1575616339', '2019-04-24 02:35:01', '2019-04-24 00:00:00', 'testing_testbed', 1, 4, '2019-04-23 19:34:01', '2019-04-23 19:34:01'),
(142, '1300054684', '2019-04-24 03:23:01', '2019-04-24 00:00:00', 'super_60', 1, 4, '2019-04-23 20:22:01', '2019-04-23 20:22:01'),
(143, '1886074278', '2019-04-25 04:04:07', '2019-04-25 00:00:00', 'testing', 1, 4, '2019-04-24 21:03:07', '2019-04-24 21:03:07'),
(144, '1886074278', '2019-04-25 04:06:37', '2019-04-25 00:00:00', '666', 1, 4, '2019-04-24 21:05:37', '2019-04-24 21:05:37'),
(145, '1456030185', '2019-04-26 10:32:39', '2019-04-26 00:00:00', 'test', 1, 4, '2019-04-26 03:31:39', '2019-04-26 03:31:39'),
(146, '1456030185', '2019-04-26 10:32:45', '2019-04-26 00:00:00', 'test', 1, 4, '2019-04-26 03:31:45', '2019-04-26 03:31:45');

-- --------------------------------------------------------

--
-- Table structure for table `customer_info_report`
--

DROP TABLE IF EXISTS `customer_info_report`;
CREATE TABLE IF NOT EXISTS `customer_info_report` (
  `Remark` varchar(300) DEFAULT NULL,
  `Executed_Date` date DEFAULT NULL,
  `Executed_By` varchar(100) DEFAULT NULL,
  `Amount` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='cust info';

--
-- Dumping data for table `customer_info_report`
--

INSERT INTO `customer_info_report` (`Remark`, `Executed_Date`, `Executed_By`, `Amount`) VALUES
('My_Test_Batch', '2019-04-25', '4', 3),
('Bona_Testing', '2019-04-25', '4', 3),
('test_submit', '2019-04-26', '4', 3),
('testing', '2019-04-26', '4', 3),
('tst', '2019-04-26', '4', 3),
('test_10_num', '2019-04-26', '4', 9),
('test', '2019-04-26', '4', 9),
('stest', '2019-04-26', '4', 9),
('test', '2019-04-26', '4', 9),
('test', '2019-04-26', '4', 9),
('test', '2019-04-26', '4', 9),
('test_new_name', '2019-05-17', '4', 1),
('test_new_name', '2019-05-17', '4', 1),
('test_new', '2019-05-17', '4', 1),
('test', '2019-05-17', '4', 1),
('test', '2019-05-17', '4', 1),
('tests', '2019-05-17', '4', 1),
('test', '2019-05-17', '4', 1),
('success_test', '2019-05-28', '4', 1),
('EVC_Change', '2019-05-28', '4', 2);

-- --------------------------------------------------------

--
-- Table structure for table `functions`
--

DROP TABLE IF EXISTS `functions`;
CREATE TABLE IF NOT EXISTS `functions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(50) NOT NULL DEFAULT '0',
  `function_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `functions`
--

INSERT INTO `functions` (`id`, `path`, `function_name`) VALUES
(1, 'home', 'Batch Change PriOffering'),
(2, 'cust', 'Change CustInfo'),
(3, 'evc', 'Change EVC_SMAP Info'),
(4, 'dealer', 'Change Dealer_NGBSS Info'),
(5, 'deactivate', 'Deactivate Sub'),
(6, 'changepretopost', 'Pre To Post'),
(7, 'changeacctinfo', 'Post To Pre'),
(8, 'activate', 'Activate Sub'),
(9, 'changeposttopre', 'Post To Pre'),
(10, 'todo', 'TO DO'),
(12, 'completed', 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(42, '2014_10_12_000000_create_users_table', 1),
(43, '2014_10_12_100000_create_password_resets_table', 1),
(44, '2018_12_21_093507_access_user', 1),
(45, '2018_12_23_075335_change_offering_schedules', 1),
(46, '2018_12_23_105745_change_offering_msisdns', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

DROP TABLE IF EXISTS `plans`;
CREATE TABLE IF NOT EXISTS `plans` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name_en` varchar(50) DEFAULT NULL,
  `name_kh` varchar(50) DEFAULT NULL,
  `name_zh` varchar(50) DEFAULT NULL,
  `logo` varchar(250) DEFAULT NULL,
  `banner_image` varchar(250) DEFAULT NULL,
  `body_image_en` varchar(250) DEFAULT NULL,
  `body_image_kh` varchar(250) DEFAULT NULL,
  `body_image_zh` varchar(250) DEFAULT NULL,
  `body_image_v2_en` varchar(250) DEFAULT NULL,
  `body_image_v2_kh` varchar(250) DEFAULT NULL,
  `body_image_v2_zh` varchar(250) DEFAULT NULL,
  `description_en` longtext,
  `description_kh` longtext,
  `description_zh` longtext,
  `plan_category_id` int(11) NOT NULL DEFAULT '0',
  `weight` int(11) DEFAULT '1',
  `view_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1: other, 2: stream on, 3: super 60',
  `show` tinyint(4) NOT NULL DEFAULT '1',
  `link_en` text,
  `link_kh` text,
  `link_zh` text,
  `amount_en` varchar(25) NOT NULL DEFAULT '0',
  `amount_kh` varchar(25) NOT NULL DEFAULT '0',
  `amount_zh` varchar(25) NOT NULL DEFAULT '0',
  `offering_id` varchar(50) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT '1',
  `modified_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `offering_id` (`offering_id`),
  KEY `view_type` (`view_type`),
  KEY `status` (`status`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `plan_category_id` (`plan_category_id`),
  KEY `weight` (`weight`),
  KEY `show` (`show`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `name_en`, `name_kh`, `name_zh`, `logo`, `banner_image`, `body_image_en`, `body_image_kh`, `body_image_zh`, `body_image_v2_en`, `body_image_v2_kh`, `body_image_v2_zh`, `description_en`, `description_kh`, `description_zh`, `plan_category_id`, `weight`, `view_type`, `show`, `link_en`, `link_kh`, `link_zh`, `amount_en`, `amount_kh`, `amount_zh`, `offering_id`, `created_at`, `created_by`, `modified_at`, `modified_by`, `status`) VALUES
(1, 'Smart Flexi1000', 'Smart Flexi1000', 'Smart Flexi1000', 'flexi1000.png', 'flexi1000.png', 'flexi1000.png', 'flexi1000.png', 'flexi1000.png', 'flexi1000.png', 'flexi1000.png', 'flexi1000.png', 'Love BONUS? The more you top up, the more BONUSES you get!', 'ចូលចិត្តលុយបន្ថែម? បញ្ចូលលុយកាន់តែច្រើន ទទួលបានលុយបន្ថែមកាន់តែច្រើន!', 'Love BONUS? The more you top up, the more BONUSES you get!', 1, 1000, 1, 1, NULL, NULL, NULL, '0 USD', '0 ដុល្លារ', '0美元', '1575616339', '2017-10-11 02:19:35', 1, '2018-06-26 03:31:18', NULL, 1),
(2, 'Smart PowerPlus 1', 'Smart PowerPlus 1', 'Smart PowerPlus 1', 'powerplus.png', 'powerplus.png', 'powerplus1_5.png', 'powerplus1_5.png', 'powerplus1_5.png', 'powerplus1.png', 'powerplus1_1.png', 'powerplus1.png', '1 USD = 150 USD in data, calls and SMS!', '1 ដុល្លារ = 150 ដុល្លារ សម្រាប់អ៊ិនធឺណិត តេនិងផ្ញើសារ!', '1 USD = 150 USD in data, calls and SMS!', 2, 3000, 1, 1, NULL, NULL, NULL, '1.00 USD', '1.00 ដុល្លារ', '1.00美元', '701276563', '2017-10-11 04:39:50', 1, '2018-03-07 10:37:08', NULL, 1),
(3, 'Smart PowerPlus 2', 'Smart PowerPlus 2', 'Smart PowerPlus 2', 'powerplus.png', 'powerplus.png', 'powerplus2.png', 'powerplus2.png', 'powerplus2.png', 'powerplus2.png', 'powerplus2_1.png', 'powerplus2.png', '2 USD = 250 USD in data, calls and SMS!', '2 ដុល្លារ = 250 ដុល្លារ សម្រាប់អ៊ិនធឺណិត តេនិងផ្ញើសារ!', '2 USD = 250 USD in data, calls and SMS!', 2, 2000, 1, 1, NULL, NULL, NULL, '2.00 USD', '2.00 ដុល្លារ', '2.00美元', '1524584871', '2017-10-11 04:46:23', 1, '2018-03-07 10:37:15', NULL, 1),
(4, 'Smart PowerPlus 8', 'Smart PowerPlus 8', 'Smart PowerPlus 8', 'powerplus.png', 'powerplus.png', 'powerplus8.png', 'powerplus8.png', 'powerplus8.png', 'powerplus8.png', 'powerplus8_1.png', 'powerplus8.png', '8 USD = 1000 USD in data, calls and SMS!', '8 ដុល្លារ = 1000 ដុល្លារ សម្រាប់អ៊ិនធឺណិត តេនិងផ្ញើសារ!', '8 USD = 1000 USD in data, calls and SMS!', 2, 1000, 1, 1, NULL, NULL, NULL, '8.00 USD', '8.00 ដុល្លារ', '8.00美元', '1701598590', '2017-10-11 06:46:58', 1, '2018-05-28 03:54:30', NULL, 1),
(5, 'Smart XtraLong!', 'Smart XtraLong!', 'Smart XtraLong!', 'xtralong.png', 'xtralong!.png', 'xtralong.png', 'xtralong.png', 'xtralong.png', 'xtralong.png', 'xtralong_1.png', 'xtralong.png', 'Keep your number active for longer.', 'លេខទូរស័ព្ទរបស់អ្នកនឹងមានសុពលភាពបានកាន់តែយូរជាងមុន', '让您的号码活跃更长时间。', 4, 1000, 1, 1, NULL, NULL, NULL, '2.00 USD', '2.00 ដុល្លារ', '2.00美元', '174839026', '2017-10-11 07:30:38', 1, '2018-03-12 08:11:55', NULL, 1),
(6, 'Smart Super60', 'Smart Super60', 'Smart Super60', 'super60.png', 'super60.png', 'super60.png', 'super60.png', 'super60.png', 'super60.png', 'super60.png', 'super60.png', 'Stay in touch with free talk time every day.', 'រក្សាទំនាក់ទំនង​ដោយ​តេ​ចេញ ឥត​​គិត​​​លុយ​​រៀង​រាល់ថ្ងៃ!', '每天免费通话时间以保持联系。', 3, 1000, 1, 1, NULL, NULL, NULL, '0 USD', '0 ដុល្លារ', '0美元', '1375693025', '2017-10-11 08:11:47', 1, '2018-06-26 03:31:22', NULL, 1),
(7, 'Smart StreamOn 2', 'Smart StreamOn 2', 'Smart StreamOn 2', 'streamon.png', 'streamon.png', 'streamon2_1.png', 'streamon2_1.png', 'streamon2_1.png', 'streamon2.png', 'streamon2_1.png', 'streamon2.png', '2 USD = 777 USD in data, calls and SMS plus 3 GB streaming bonus and FREE iflix, JaiKonTV and Soyo.', '2 ដុល្លារ = 777 ដុល្លារ សម្រាប់អ៊ិនធឺណិត តេនិងផ្ញើសារ បន្ថែមជាមួយអ៊ិនធឺណិត 3 GB សម្រាប់មើលនិងប្រើឥតគិតលុយលើកម្មវិធី iflix, JaiKonTV និង Soyo', '2 USD = 777 USD in data, calls and SMS plus 3 GB streaming bonus and FREE iflix, JaiKonTV and Soyo.', 5, 2000, 2, 1, 'https://app-assets.smart.com.kh/smartnas/web-view/streamon/streamon2_en.html', 'https://app-assets.smart.com.kh/smartnas/web-view/streamon/streamon2_kh.html', 'https://app-assets.smart.com.kh/smartnas/web-view/streamon/streamon2_en.html', '2.00 USD', '2.00 ដុល្លារ', '2.00美元', '354823646', '2017-10-20 02:24:50', 1, '2018-03-07 10:37:34', NULL, 1),
(8, 'Smart StreamOn 8', 'Smart StreamOn 8', 'Smart StreamOn 8', 'streamon.png', 'streamon.png', 'streamon8.png', 'streamon8.png', 'streamon8.png', 'streamon8.png', 'streamon8_1.png', 'streamon8.png', '8 USD = 3333 USD in data, calls and SMS plus 15 GB streaming bonus and FREE iflix, JaiKonTV and Soyo.', '8 ដុល្លារ = 3333 ដុល្លារ សម្រាប់អ៊ិនធឺណិត តេនិងផ្ញើសារ បន្ថែមជាមួយអ៊ិនធឺណិត 15 GB សម្រាប់មើលនិងប្រើឥតគិតលុយលើកម្មវិធី iflix, JaiKonTV និង Soyo', '8 USD = 3333 USD in data, calls and SMS plus 15 GB streaming bonus and FREE iflix, JaiKonTV and Soyo.', 5, 1000, 2, 1, 'https://app-assets.smart.com.kh/smartnas/web-view/streamon/streamon8_en.html', 'https://app-assets.smart.com.kh/smartnas/web-view/streamon/streamon8_kh.html', 'https://app-assets.smart.com.kh/smartnas/web-view/streamon/streamon8_en.html', '8.00 USD', '8.00 ដុល្លារ', '8.00美元', '954827077', '2017-10-20 02:28:11', 1, '2018-03-07 10:37:41', NULL, 1),
(9, 'Smart ThomMorng! Weekly', 'Smart ធំហ្ម៎ង! រាល់សប្តាហ៍', 'Smart ThomMorng !每周', 'thommorng.png', 'thommorng!.png', 'thommorng_weekly.png', 'thommorng_weekly.png', 'thommorng_weekly.png', 'thommorng_weekly.png', 'thommorng_weekly_1.png', 'thommorng_weekly.png', 'Smart ThomMorng! is a plan which you spend only 1 USD per week, get 333 USD for on-net calls, SMS and data.', 'Smart ធំហ្ម៎ង! គឺជាគម្រោងដែលលោកអ្នកគ្រាន់តែចំណាយ​ ១ ដុល្លារក្នុងមួយសប្តាហ៍ ដើម្បីទទួលបាន ៣៣៣ ដុល្លារ សម្រាប់ការតេចេញ និង ផ្ញើរសារក្នុងប្រព័ន្ធ រួមនឹងការប្រើប្រាស់អ៊ីនធឺណិត។', 'Smart ThomMorng! is a plan which you spend only 1 USD per week, get 333 USD for on-net calls, SMS and data.', 6, 950, 1, 1, NULL, NULL, NULL, '1.00 USD', '1.00 ដុល្លារ', '1.00美元', '1456030185', '2017-10-20 02:28:11', 1, '2018-03-07 10:37:56', NULL, 1),
(10, 'Smart Thomada', 'Smart ធម្មតា', 'Smart Thomada', 'thomada.png', 'thomada.png', NULL, NULL, NULL, 'thomada.png', 'thomada_1.png', 'thomada.png', 'Simple, pay-as-you-go plan with no auto-renewal.', 'ងាយស្រួល​និងគ្មានការភ្ជាប់ឡើងវិញដោយស្វ័យប្រវត្តិ', '每个月得到100美元奖金，达6个月时间!', 7, 1000, 1, 1, NULL, NULL, NULL, '50 Cents', '50 សេន', '50美分', '560291561', '2017-10-20 02:28:11', 1, '2018-03-12 08:18:46', NULL, 1),
(13, 'Smart PowerPlus 8 Uber', 'Smart PowerPlus 8 Uber', 'Smart PowerPlus 8超级', 'powerplus.png', 'powerplus.png', 'powerplus8uber.png', 'powerplus8uber.png', 'powerplus8uber.png', 'power_plus_8_uber.png', 'powerplus_8_uber_1.png', 'power_plus_8_uber.png', '8 USD = 1000 USD in data, calls and SMS plus 5 GB Uber Driver Appdata bonus!', '8 ដុល្លារ = 1000 ដុល្លារ សម្រាប់អ៊ិនធឺណិត តេនិងផ្ញើសារ! រួមទាំងអ៊ិនធឺណិត 5 GB សម្រាប់ប្រើកម្មវិធី Uber Driver', '8 USD = 1000 USD in data, calls and SMS plus 5 GB Uber Driver Appdata bonus!', 10, 1000, 1, 1, NULL, NULL, NULL, '8.00 USD', '8.00 ដុល្លារ', '8.00美元', '959590506', '2017-10-20 02:28:11', 1, '2018-06-11 03:39:08', NULL, 1),
(14, 'Smart ThomMorng! Daily', 'Smart ធំហ្ម៎ង! រាល់ថ្ងៃ', 'Smart ThomMorng !每天', 'thommorng.png', 'thommorng!.png', 'thommorng_daily.png', 'thommorng_daily.png', 'thommorng_daily.png', 'thommorng_daily.png', 'thommorng_daily_1.png', 'thommorng_daily.png', 'Smart ThomMorng! is a plan which you spend only 20 cents per day, get 50 USD for on-net calls, SMS and data.', 'Smart ធំហ្ម៎ង! គឺជាគម្រោងដែលលោកអ្នកគ្រាន់តែចំណាយ​ ក្នុងមួយថ្ងៃ ដើម្បីទទួលបាន ២០​ ដុល្លារ សម្រាប់ការតេចេញ និង ផ្ញើរសារក្នុងប្រព័ន្ធ រួមនឹងការប្រើប្រាស់អ៊ីនធឺណិត។', 'Smart ThomMorng! is a plan which you spend only 20 cents per day, get 50 USD for on-net calls, SMS and data.', 6, 1100, 1, 1, NULL, NULL, NULL, '1.00 USD', '1.00 ដុល្លារ', '1.00美元', '971687707', '2017-10-20 02:28:11', 1, '2018-03-07 10:38:14', NULL, 1),
(15, 'Smart ThomMorng! One Off', 'Smart ធំហ្ម៎ង! One Off', 'Smart ThomMorng ! 一次', 'thommorng.png', 'thommorng!.png', 'thommorng_weekly.png', 'thommorng_weekly.png', 'thommorng_weekly.png', 'thommorng_weekly.png', 'thommorng_weekly_1.png', 'thommorng_weekly.png', 'Smart ThomMorng! is a plan which you spend only 1 USD per week, get 333 USD for on-net calls, SMS and data.', 'Smart ធំហ្ម៎ង! គឺជាគម្រោងដែលលោកអ្នកគ្រាន់តែចំណាយ ​១ ដុល្លារ ក្នុងមួយសប្តាហ៍ ដើម្បីទទួលបាន ៣៣៣ ដុល្លារ សម្រាប់ការតេចេញ និង ផ្ញើរសារក្នុងប្រព័ន្ធ រួមនឹងការប្រើប្រាស់អ៊ីនធឺណិត។', 'Smart ThomMorng! is a plan which you spend only 1 USD per week, get 333 USD for on-net calls, SMS and data.', 6, 900, 1, 1, NULL, NULL, NULL, '1.00 USD', '1.00 ដុល្លារ', '1.00美元', '571696116', '2017-10-20 02:28:11', 1, '2018-04-12 09:32:57', NULL, 1),
(16, 'Plan X1', 'Plan X1', 'Plan X1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10, 1, 1, 1, NULL, NULL, NULL, '0', '0', '0', '1477648596', '2018-02-27 10:24:31', 1, '2018-06-11 03:37:08', NULL, 1),
(17, 'Plan X2', 'Plan X2', 'Plan X2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10, 1, 1, 1, NULL, NULL, NULL, '0', '0', '0', '177726843', '2018-02-27 10:24:31', 1, '2018-06-11 03:37:11', NULL, 1),
(18, 'Smart ThaemOsSteah!', 'Smart ថែមអស់ស្ទះ!', 'Smart 加极了!', 'thaemossteah.png', 'thaemossteah_banner.png', 'thaemossteah_banner.png', 'thaemossteah_banner.png', 'thaemossteah_banner.png', 'thaemossteah.png', 'thaemossteah.png', 'thaemossteah.png', 'Get 40 USD BONUS every time you spend 20 cents of your main balance on calls, SMS and data. There’s no subscription fee and you can use your BONUS balance for calls, SMS and data!', 'ទទួលបានលុយបន្ថែម 40 ដុល្លាររាល់ពេលដែលអ្នកចំណាយអស់ 20 សេនក្នុងគណនីចម្បងរបស់អ្នកលើការតេ ផ្ញើសារ និងលេងអុិនធឺណិត។ ឥតគិតលុយថ្លៃសេវា ហើយអ្នកអាចប្រើលុយបន្ថែមសម្រាប់លេងអុិនធឺណិត តេ និងផ្ញើសារក្នុងប្រព័ន្ធ!', '每次从您的主余额上在通话，短信和数据花费了20美分，您可获得40美元奖金。\r\n没有任何订阅费，您可在通话，短信和数据上使用您的奖金。\r\n', 8, 1, 1, 1, NULL, NULL, NULL, '0', '0', '0', '177727102', '2018-02-27 10:24:31', 1, '2018-06-26 09:22:46', NULL, 1),
(19, 'Smart Flexi 1000', 'Smart Flexi 1000', 'Smart Flexi 1000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9, 1, 1, 1, NULL, NULL, NULL, '0', '0', '0', '1117424890', '2018-03-28 04:54:33', 1, '2018-03-29 08:28:23', NULL, 1),
(20, 'Smart Thomada Seeding', 'Smart Thomada Seeding', 'Smart Thomada Seeding', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10, 1, 1, 1, NULL, NULL, NULL, '0', '0', '0', '163286157', '2018-03-28 06:56:20', 1, '2018-03-28 06:59:32', NULL, 1),
(21, 'Smart Thomada Wedding Promo', 'Smart Thomada Wedding Promo', 'Smart Thomada Wedding Promo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10, 1, 1, 1, NULL, NULL, NULL, '0', '0', '0', '768561242', '2018-03-28 06:56:36', 1, '2018-03-28 06:59:37', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `phone_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `position` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '$2y$10$xgmaNVEYIBAvbPPs7/az0e5IQVd7.M5y0lWHdmLcCFHVjhL7nKEmS',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `status`, `phone_number`, `email_verified_at`, `position`, `location`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'pen.lymeng', 'pen.lymeng@smart.com.kh', 1, '+85510202542', NULL, 'Application Developer', 'Phnom Penh - Headquarter', '$2y$10$xgmaNVEYIBAvbPPs7/az0e5IQVd7.M5y0lWHdmLcCFHVjhL7nKEmS', 'GFjPGA9CCqrI1Qmmhvqf2TPoDPgsW6pQGXvUA02S68nADC5OG1fEcdRh8nNp', NULL, NULL),
(2, 'samol.yuthpanha', 'samol.yuthpanha@smart.com.kh', 1, '+85510202177', NULL, 'Junior Billing Administrator', 'Phnom Penh - Headquarter', '$2y$10$xgmaNVEYIBAvbPPs7/az0e5IQVd7.M5y0lWHdmLcCFHVjhL7nKEmS', 'lCu6MbIstA2xh0SntIoH8g2NUPrnTnS9QrOekaXvVBQDrLnyknz3AbPGbroO', NULL, NULL),
(3, 'ak.hourphenh', 'ak.hourphenh@smart.com.kh', 1, '+85510202646', NULL, 'Senior CRM Administrator', 'Phnom Penh - Headquarter', '$2y$10$xgmaNVEYIBAvbPPs7/az0e5IQVd7.M5y0lWHdmLcCFHVjhL7nKEmS', NULL, NULL, NULL),
(4, 'pin.bona', 'pin.bona@smart.com.kh', 1, '+85510202625', NULL, 'CRM Administrator', 'Phnom Penh - Headquarter', '$2y$10$xgmaNVEYIBAvbPPs7/az0e5IQVd7.M5y0lWHdmLcCFHVjhL7nKEmS', 'VOrHYGZo5GM8kYSC8tjmqVbGL51xKGnAUDAsxjdpgLEHJLlMUqMahwwhAr0R', NULL, NULL),
(5, 'khun.chamman', 'khun.chamman@smart.com.kh', 1, '+85510202943', NULL, 'Back Office Support Manager', 'Phnom Penh - Nehru', '$2y$10$xgmaNVEYIBAvbPPs7/az0e5IQVd7.M5y0lWHdmLcCFHVjhL7nKEmS', NULL, NULL, NULL),
(6, 'leng.sengchourng', 'leng.sengchourng@smart.com.kh', 1, '+85510202918', NULL, 'Back Office Support Officer', 'Phnom Penh - Nehru', '$2y$10$xgmaNVEYIBAvbPPs7/az0e5IQVd7.M5y0lWHdmLcCFHVjhL7nKEmS', '7os79JoF9Y7Zx9l3YKURjbduVWMVmPAOBwAoDSddoBhDybbQJXY9s6tri5LF', NULL, NULL),
(7, 'kang.piseth', 'kang.piseth@smart.com.kh', 1, '+85510202392', NULL, 'Back Office Support Officer', 'Phnom Penh - Nehru', '$2y$10$xgmaNVEYIBAvbPPs7/az0e5IQVd7.M5y0lWHdmLcCFHVjhL7nKEmS', '49mjvgDQmA7VSqQH9KlabBrrlcqjeTrO9wR4hPzxjaO50MPrQebp6RasBBUT', NULL, NULL),
(8, 'chhim.pagna', 'chhim.pagna@smart.com.kh', 1, '+85510202145', NULL, 'Back Office Support Officer', 'Phnom Penh - Nehru', '$2y$10$xgmaNVEYIBAvbPPs7/az0e5IQVd7.M5y0lWHdmLcCFHVjhL7nKEmS', NULL, NULL, NULL),
(9, 'khim.mengheng', 'khim.mengheng@smart.com.kh', 1, '+85510202649', NULL, 'Back Office Support Officer', 'Phnom Penh - Nehru', '$2y$10$xgmaNVEYIBAvbPPs7/az0e5IQVd7.M5y0lWHdmLcCFHVjhL7nKEmS', NULL, NULL, NULL),
(10, 'sor.denny', 'sor.denny@smart.com.kh', 1, '+85510202604', NULL, 'Back Office Support Officer', 'Phnom Penh - Nehru', '$2y$10$xgmaNVEYIBAvbPPs7/az0e5IQVd7.M5y0lWHdmLcCFHVjhL7nKEmS', NULL, NULL, NULL),
(11, 'nourn.bora1', 'nourn.bora1@smart.com.kh', 1, '+85510202685', NULL, 'Back Office Support Officer', 'Phnom Penh - Nehru', '$2y$10$xgmaNVEYIBAvbPPs7/az0e5IQVd7.M5y0lWHdmLcCFHVjhL7nKEmS', NULL, NULL, NULL),
(12, 'sok.heang', 'sok.heang@smart.com.kh', 1, '+85510202974', NULL, 'Back Office Support Officer', 'Phnom Penh - Nehru', '$2y$10$xgmaNVEYIBAvbPPs7/az0e5IQVd7.M5y0lWHdmLcCFHVjhL7nKEmS', NULL, NULL, NULL),
(13, 'pek.monich', 'pek.monich@smart.com.kh', 1, '+85510202715', NULL, 'Back Office Support Officer', 'Phnom Penh - Nehru', '$2y$10$xgmaNVEYIBAvbPPs7/az0e5IQVd7.M5y0lWHdmLcCFHVjhL7nKEmS', NULL, NULL, NULL),
(14, 'ry.chhounly', 'ry.chhounly@smart.com.kh', 1, '+85510202650', NULL, 'Back Office Support Officer', 'Phnom Penh - Nehru', '$2y$10$xgmaNVEYIBAvbPPs7/az0e5IQVd7.M5y0lWHdmLcCFHVjhL7nKEmS', NULL, NULL, NULL),
(15, 'pen.sonorn', 'pen.sonorn@smart.com.kh', 1, '+85510202310', NULL, 'Back Office Support Officer', 'Phnom Penh - Nehru', '$2y$10$xgmaNVEYIBAvbPPs7/az0e5IQVd7.M5y0lWHdmLcCFHVjhL7nKEmS', 'zOxaIF5fv4weedBSLPHrJrRrqlrJYtshAbLB11XjBujrOd3jUeevYKI1AZhh', NULL, NULL),
(16, 'chhun.sokheang', 'chhun.sokheang@smart.com.kh', 1, '+85510202632', NULL, 'Head of CRM & Billing', 'Phnom Penh - Headquarter', '$2y$10$xgmaNVEYIBAvbPPs7/az0e5IQVd7.M5y0lWHdmLcCFHVjhL7nKEmS', '06OwH72pFsqpmHzUxkqzmvjcc1faMe4cdxOYXVVSDMIy7US7XsTu7dChNpxl', NULL, NULL),
(21, 'phal.chantrea', 'phal.chantrea@smart.com.kh', 1, '+85510202784', NULL, 'Junior Billing Administrator', 'Phnom Penh - Headquarter', '$2y$10$xgmaNVEYIBAvbPPs7/az0e5IQVd7.M5y0lWHdmLcCFHVjhL7nKEmS', 'R02V6D2w0B5tD1GTJ6ThLuAoaEUh4vfEudaQfJCWUSxCPrkaZUsmPixYQuiO', NULL, NULL),
(22, 'touch.teng', 'touch.teng@smart.com.kh', 1, '+85510345282', NULL, 'CRM & Billing Intern', 'Phnom Penh - Headquarter', '$2y$10$xgmaNVEYIBAvbPPs7/az0e5IQVd7.M5y0lWHdmLcCFHVjhL7nKEmS', 'HhBRqk2WTgwMv8JKO0xrXRizzOqOIL36At8FMqBpfUYTJbEMsEMRqlhoFqKN', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_functions`
--

DROP TABLE IF EXISTS `user_functions`;
CREATE TABLE IF NOT EXISTS `user_functions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `function_id` int(11) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_functions`
--

INSERT INTO `user_functions` (`id`, `user_id`, `function_id`, `active`) VALUES
(1, 4, 1, 1),
(2, 4, 2, 1),
(3, 4, 3, 1),
(4, 4, 4, 1),
(5, 4, 5, 1),
(6, 4, 6, 1),
(7, 4, 7, 1),
(8, 4, 8, 1),
(9, 4, 9, 1),
(10, 4, 10, 1),
(11, 4, 11, 1),
(12, 16, 3, 1),
(13, 16, 4, 1),
(14, 16, 2, 1),
(15, 21, 2, 1),
(16, 21, 4, 1),
(17, 2, 1, 1),
(18, 2, 2, 1),
(19, 2, 3, 1),
(20, 2, 4, 1),
(21, 2, 5, 1),
(22, 2, 6, 1),
(23, 2, 7, 1),
(24, 2, 8, 1),
(25, 2, 9, 1),
(26, 2, 10, 1),
(27, 2, 11, 1),
(28, 2, 12, 1),
(32, 22, 4, 1),
(33, 22, 5, 1),
(34, 22, 6, 1),
(35, 22, 7, 1),
(37, 22, 9, 1),
(38, 22, 10, 1),
(40, 22, 12, 1),
(41, 22, 8, 1),
(48, 22, 2, 1),
(51, 22, 1, 1),
(52, 22, 3, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

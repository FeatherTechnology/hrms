-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2026 at 03:14 PM
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
-- Database: `hrms_testing`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(50) NOT NULL,
  `staff_profile_id` int(50) DEFAULT NULL,
  `company_id` int(50) DEFAULT NULL,
  `branch_id` int(50) DEFAULT NULL,
  `dep_id` int(50) DEFAULT NULL,
  `des_id` int(50) DEFAULT NULL,
  `team_id` int(50) DEFAULT NULL,
  `staff_type` int(50) DEFAULT NULL,
  `entry_time` datetime DEFAULT NULL,
  `updated_by` int(50) DEFAULT NULL,
  `reason` varchar(250) DEFAULT NULL,
  `insert_login_id` int(50) DEFAULT NULL,
  `update_login_id` int(50) DEFAULT NULL,
  `inserted_date` date DEFAULT NULL,
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `staff_profile_id`, `company_id`, `branch_id`, `dep_id`, `des_id`, `team_id`, `staff_type`, `entry_time`, `updated_by`, `reason`, `insert_login_id`, `update_login_id`, `inserted_date`, `updated_date`) VALUES
(2, 3, 1, 1, 1, 1, 2, 2, '2026-05-22 08:31:00', 1, 'dsfdf', NULL, 1, NULL, '2026-05-21'),
(3, 5, 1, 1, 1, 1, 2, 2, '2026-05-22 15:30:00', 1, 'fgdf', NULL, 1, NULL, '2026-05-21'),
(4, 6, 3, 1, 1, 1, 2, 2, '2026-05-22 05:30:00', 1, 'fgh', NULL, 1, NULL, '2026-05-21'),
(5, 7, 3, 1, 1, 1, 2, 2, '2026-05-22 08:30:00', 1, 'cff', NULL, 1, NULL, '2026-05-21');

-- --------------------------------------------------------

--
-- Table structure for table `branch_creation`
--

CREATE TABLE `branch_creation` (
  `id` int(11) NOT NULL,
  `company_id` varchar(255) NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `branch_name` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `state` int(11) NOT NULL,
  `district` int(11) NOT NULL,
  `place` varchar(100) NOT NULL,
  `pincode` varchar(100) NOT NULL,
  `location` varchar(50) NOT NULL,
  `email_id` varchar(100) NOT NULL,
  `mobile_number` varchar(100) NOT NULL,
  `whatsapp` varchar(100) NOT NULL,
  `landline_code` varchar(50) DEFAULT NULL,
  `landline` varchar(100) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branch_creation`
--

INSERT INTO `branch_creation` (`id`, `company_id`, `branch_code`, `branch_name`, `address`, `state`, `district`, `place`, `pincode`, `location`, `email_id`, `mobile_number`, `whatsapp`, `landline_code`, `landline`, `insert_login_id`, `update_login_id`, `created_date`, `updated_date`) VALUES
(1, '1', 'FT-101', 'Villianur', '', 2, 39, 'villianur', '605110', 'villlianur', '', '8678678678', '8786786786', '', '', 1, 1, '2026-05-22 11:32:01', '2026-05-22'),
(2, '2', 'UF-101', 'Dindigul', '', 1, 9, 'Devinaikanpatti', '767867', '78787878', '', '', '', '', '', 1, NULL, '2026-05-22 11:33:48', NULL),
(3, '1', 'FT-102', 'IG', '', 2, 39, 'villianur', '567576', '78787878', 'ig331@gmail.com', '', '', '', '', 1, 1, '2026-05-22 11:34:23', '2026-05-22'),
(4, '3', 'M-101', 'vandavasi', '', 1, 27, 'Chengalpattu', '567567', '8878577577567', '', '9678678886', '', '67678', '67687867', 1, 1, '2026-05-22 11:38:18', '2026-05-22');

-- --------------------------------------------------------

--
-- Table structure for table `company_creation`
--

CREATE TABLE `company_creation` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `gst_num` varchar(50) DEFAULT NULL,
  `cin_number` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `district` int(11) DEFAULT NULL,
  `place` varchar(255) DEFAULT NULL,
  `pincode` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `landline_code` varchar(255) DEFAULT NULL,
  `landline` varchar(255) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `mailid` varchar(250) DEFAULT NULL,
  `instagram` varchar(250) DEFAULT NULL,
  `youtube_link` varchar(250) DEFAULT NULL,
  `facebook` varchar(250) DEFAULT NULL,
  `twitter` varchar(250) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `insert_user_id` int(11) DEFAULT NULL,
  `update_user_id` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_creation`
--

INSERT INTO `company_creation` (`id`, `company_name`, `gst_num`, `cin_number`, `address`, `state`, `district`, `place`, `pincode`, `mobile`, `whatsapp`, `landline_code`, `landline`, `website`, `mailid`, `instagram`, `youtube_link`, `facebook`, `twitter`, `status`, `insert_user_id`, `update_user_id`, `created_date`, `updated_date`) VALUES
(1, 'Feather Technology', '64564', '6456', 'Bussy Street', 2, 39, 'villianur', '605110', '7867867876', '8678678678', '43565', '78989898', 'feather.com', 'feather@gmail.com', 'sfdsf', 'feather@gmail.com', 'fdsf', 'aaaaaaaaa', 1, 1, 1, '2026-05-22 11:19:31', '2026-05-22'),
(2, 'Uzhavan Finance', '', '', 'Bussy Street', 1, 23, 'Vandavasi', '867867', '8978978978', '', '', '', '', '', '', '', '', '', 1, 1, 1, '2026-05-22 11:26:58', '2026-05-22'),
(3, 'Marudham', '64564', '', 'Vedasandur', 1, 34, 'Vandavasi', '878978', '7867867867', '', '', '', '', '', '', '', '', '', 1, 1, NULL, '2026-05-22 11:30:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `company_department_mapping`
--

CREATE TABLE `company_department_mapping` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_department_mapping`
--

INSERT INTO `company_department_mapping` (`id`, `company_id`, `department_id`) VALUES
(2, 1, 2),
(3, 2, 3),
(4, 2, 2),
(5, 3, 1),
(6, 3, 2),
(8, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `company_designation_mapping`
--

CREATE TABLE `company_designation_mapping` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `designation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_designation_mapping`
--

INSERT INTO `company_designation_mapping` (`id`, `company_id`, `designation_id`) VALUES
(2, 1, 2),
(3, 2, 3),
(4, 2, 2),
(5, 3, 3),
(6, 3, 1),
(7, 3, 2),
(9, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `company_policies`
--

CREATE TABLE `company_policies` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `max_permission` varchar(25) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_policies`
--

INSERT INTO `company_policies` (`id`, `company_id`, `max_permission`, `insert_login_id`, `update_login_id`, `created_date`, `updated_date`) VALUES
(1, 1, '3', 1, NULL, '2026-05-22 12:25:25', NULL),
(2, 2, '2', 1, NULL, '2026-05-22 12:29:21', NULL),
(3, 3, '3', 1, 1, '2026-05-22 12:30:53', '2026-05-22');

-- --------------------------------------------------------

--
-- Table structure for table `company_weekoffs`
--

CREATE TABLE `company_weekoffs` (
  `id` int(11) NOT NULL,
  `company_policies_id` int(11) NOT NULL,
  `week_day` varchar(50) NOT NULL,
  `week_off` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_weekoffs`
--

INSERT INTO `company_weekoffs` (`id`, `company_policies_id`, `week_day`, `week_off`) VALUES
(1, 1, 'sunday', 1),
(2, 1, 'monday', 3),
(3, 1, 'tuesday', 2),
(4, 2, 'sunday', 5),
(5, 2, 'saturday', 5),
(10, 3, 'wednesday', 5),
(11, 3, 'friday', 3);

-- --------------------------------------------------------

--
-- Table structure for table `ctc_creation`
--

CREATE TABLE `ctc_creation` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `salary_component` varchar(50) NOT NULL,
  `component_classification` int(11) NOT NULL,
  `component_category` int(11) NOT NULL,
  `pay_frequency` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `insert_login_id` varchar(50) NOT NULL,
  `update_login_id` varchar(50) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ctc_creation`
--

INSERT INTO `ctc_creation` (`id`, `company_id`, `salary_component`, `component_classification`, `component_category`, `pay_frequency`, `status`, `insert_login_id`, `update_login_id`, `created_date`, `updated_date`) VALUES
(1, 1, 'Basic', 1, 1, 1, 0, '1', NULL, '2026-05-22 11:54:02', NULL),
(2, 1, 'HRA', 1, 1, 1, 0, '1', NULL, '2026-05-22 11:54:17', NULL),
(3, 1, 'Basic', 1, 1, 1, 1, '1', NULL, '2026-05-22 11:54:30', NULL),
(4, 1, 'DA', 1, 1, 1, 0, '1', NULL, '2026-05-22 11:55:38', NULL),
(5, 1, 'Allowance', 1, 2, 2, 0, '1', '1', '2026-05-22 11:55:55', '2026-05-22'),
(6, 2, 'Basic', 1, 1, 1, 0, '1', NULL, '2026-05-22 11:56:25', NULL),
(7, 2, 'HRA', 1, 1, 2, 0, '1', NULL, '2026-05-22 11:56:35', NULL),
(8, 2, 'DA', 1, 1, 1, 0, '1', NULL, '2026-05-22 11:56:46', NULL),
(9, 2, 'Allowance', 2, 2, 1, 0, '1', '1', '2026-05-22 11:56:59', '2026-05-22'),
(10, 3, 'Allowance', 1, 1, 1, 1, '1', NULL, '2026-05-22 11:57:34', NULL),
(11, 3, 'Basic', 1, 1, 1, 0, '1', NULL, '2026-05-22 11:57:55', NULL),
(12, 3, 'DA', 1, 1, 1, 0, '1', NULL, '2026-05-22 11:58:05', NULL),
(13, 3, 'HRA', 1, 1, 2, 0, '1', NULL, '2026-05-22 11:58:17', NULL),
(14, 3, 'Allowance', 2, 2, 2, 0, '1', NULL, '2026-05-22 11:58:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `department_creation`
--

CREATE TABLE `department_creation` (
  `id` int(11) NOT NULL,
  `department_code` varchar(25) NOT NULL,
  `department_name` varchar(50) NOT NULL,
  `department_status` int(11) NOT NULL DEFAULT 0,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_creation`
--

INSERT INTO `department_creation` (`id`, `department_code`, `department_name`, `department_status`, `insert_login_id`, `update_login_id`, `created_date`, `updated_date`) VALUES
(1, 'D-101', 'Development', 0, 1, NULL, '2026-05-22 11:17:48', NULL),
(2, 'D-102', 'Sales', 0, 1, NULL, '2026-05-22 11:18:30', NULL),
(3, 'D-103', 'Testings', 0, 1, 1, '2026-05-22 11:25:16', '2026-05-22'),
(4, 'D-104', 'dfsdfsdf', 1, 1, NULL, '2026-05-22 11:25:22', NULL),
(5, 'D-105', 'Testings', 0, 1, NULL, '2026-05-22 11:52:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `designation_creation`
--

CREATE TABLE `designation_creation` (
  `id` int(11) NOT NULL,
  `designation` varchar(25) NOT NULL,
  `designation_level` varchar(25) NOT NULL,
  `designation_status` int(11) NOT NULL DEFAULT 0,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `designation_creation`
--

INSERT INTO `designation_creation` (`id`, `designation`, `designation_level`, `designation_status`, `insert_login_id`, `update_login_id`, `created_date`, `updated_date`) VALUES
(1, 'Manager', '10', 0, 1, NULL, '2026-05-22 11:18:17', NULL),
(2, 'Staff', '5', 0, 1, NULL, '2026-05-22 11:18:49', NULL),
(3, 'HR', '3', 1, 1, NULL, '2026-05-22 11:26:02', NULL),
(4, 'ffdfs', '345345', 1, 1, 1, '2026-05-22 11:26:09', '2026-05-22'),
(5, 'HR', '3', 0, 1, NULL, '2026-05-22 11:53:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `district_name` varchar(50) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`id`, `state_id`, `district_name`, `status`) VALUES
(1, 1, 'Ariyalur', 1),
(2, 1, 'Chennai', 1),
(3, 1, 'Chengalpattu', 1),
(4, 1, 'Coimbatore', 1),
(5, 1, 'Cuddalore', 1),
(6, 1, 'Dharmapuri', 1),
(7, 1, 'Dindigul', 1),
(8, 1, 'Erode', 1),
(9, 1, 'Kallakurichi', 1),
(10, 1, 'Kancheepuram', 1),
(11, 1, 'Kanniyakumari', 1),
(12, 1, 'Karur', 1),
(13, 1, 'Krishnagiri', 1),
(14, 1, 'Madurai', 1),
(15, 1, 'Mayiladuthurai', 1),
(16, 1, 'Nagapattinam', 1),
(17, 1, 'Namakkal', 1),
(18, 1, 'Nilgiris', 1),
(19, 1, 'Perambalur', 1),
(20, 1, 'Pudukkottai', 1),
(21, 1, 'Ramanathapuram', 1),
(22, 1, 'Ranipet', 1),
(23, 1, 'Salem', 1),
(24, 1, 'Sivaganga', 1),
(25, 1, 'Tenkasi', 1),
(26, 1, 'Thanjavur', 1),
(27, 1, 'Theni', 1),
(28, 1, 'Thoothukudi', 1),
(29, 1, 'Tiruchirappalli', 1),
(30, 1, 'Tirunelveli', 1),
(31, 1, 'Tiruppur', 1),
(32, 1, 'Tirupathur', 1),
(33, 1, 'Tiruvallur', 1),
(34, 1, 'Tiruvannamalai', 1),
(35, 1, 'Tiruvarur', 1),
(36, 1, 'Vellore', 1),
(37, 1, 'Viluppuram', 1),
(38, 1, 'Virudhunagar', 1),
(39, 2, 'Puducherry', 1);

-- --------------------------------------------------------

--
-- Table structure for table `document_info`
--

CREATE TABLE `document_info` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(100) NOT NULL,
  `staff_profile_id` varchar(100) NOT NULL,
  `doc_name` varchar(150) NOT NULL,
  `doc_type` int(11) NOT NULL,
  `upload` varchar(100) NOT NULL,
  `return_date` date DEFAULT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_info`
--

INSERT INTO `document_info` (`id`, `staff_id`, `staff_profile_id`, `doc_name`, `doc_type`, `upload`, `return_date`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(1, 'FTS-001', '1', 'AadharCrad', 1, '6a10050ceb8cb.jpg', NULL, 1, NULL, '2026-05-22', NULL),
(2, 'FTS-002', '4', 'PAN Card', 1, '', NULL, 1, NULL, '2026-05-22', NULL),
(3, 'FTS-003', '10', 'PAN', 1, '', '2026-05-22', 1, NULL, '2026-05-22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `experience_info`
--

CREATE TABLE `experience_info` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(100) NOT NULL,
  `staff_profile_id` varchar(100) NOT NULL,
  `exp_type` varchar(150) NOT NULL,
  `total_experience` varchar(150) NOT NULL,
  `pre_company` varchar(150) NOT NULL,
  `pre_designation` varchar(150) NOT NULL,
  `work_duration` varchar(100) NOT NULL DEFAULT '0',
  `last_salary` varchar(1000) DEFAULT NULL,
  `reason_for_leaving` varchar(150) DEFAULT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `experience_info`
--

INSERT INTO `experience_info` (`id`, `staff_id`, `staff_profile_id`, `exp_type`, `total_experience`, `pre_company`, `pre_designation`, `work_duration`, `last_salary`, `reason_for_leaving`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(1, 'FTS-001', '1', '2', '2', 'CTS', 'Manager', '2 hr', '20000', 'jhfjh', 1, NULL, '2026-05-22', NULL),
(2, 'FTS-002', '4', '1', '2', 'ffs', 'sfsdf', '8', '70000', 'jkhjkjhk', 1, NULL, '2026-05-22', NULL),
(3, 'MS-001', '6', '1', '0', '0', '0', '0', '0', '0', 1, NULL, '2026-05-22', NULL),
(4, 'MS-002', '7', '1', '0', '0', '0', '0', '0', '0', 1, NULL, '2026-05-22', NULL),
(5, 'UFS-001', '8', '1', '0', '0', '0', '0', '0', '0', 1, NULL, '2026-05-22', NULL),
(6, 'FTS-003', '10', '1', '0', '0', '0', '0', '0', '0', 1, NULL, '2026-05-22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `family_info`
--

CREATE TABLE `family_info` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(100) NOT NULL,
  `staff_profile_id` varchar(50) NOT NULL,
  `fam_name` varchar(100) NOT NULL,
  `fam_relationship` varchar(100) NOT NULL,
  `fam_dob` varchar(100) DEFAULT NULL,
  `fam_occupation` varchar(100) DEFAULT NULL,
  `fam_mobile` varchar(100) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `family_info`
--

INSERT INTO `family_info` (`id`, `staff_id`, `staff_profile_id`, `fam_name`, `fam_relationship`, `fam_dob`, `fam_occupation`, `fam_mobile`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(1, 'FTS-001', '1', 'Arjun', 'Spouse', '2026-05-23', 'Manger', '7898978978', 1, NULL, '2026-05-22', NULL),
(2, 'FTS-002', '4', 'Meera', 'Mother', '2026-05-22', 'sfsdf', '8965685677', 1, NULL, '2026-05-22', NULL),
(3, 'MS-001', '6', 'kalai', 'Mother', '2026-05-22', 'fdgdfg', '7887878878', 1, NULL, '2026-05-22', NULL),
(4, 'MS-002', '7', 'Ghira', 'Father', '2026-05-23', 'dfgf', '6787878878', 1, NULL, '2026-05-22', NULL),
(5, 'UFS-001', '8', 'Madhu', 'Mother', '2026-05-22', 'dfgfgf', '7868678788', 1, NULL, '2026-05-22', NULL),
(6, 'FTS-003', '10', 'Thara', 'Spouse', '2026-05-22', 'fdf', '7878678687', 1, NULL, '2026-05-22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `holiday_creation`
--

CREATE TABLE `holiday_creation` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `no_of_days` int(11) NOT NULL,
  `holiday_name` varchar(50) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `holiday_creation`
--

INSERT INTO `holiday_creation` (`id`, `company_id`, `from_date`, `to_date`, `no_of_days`, `holiday_name`, `status`, `insert_login_id`, `update_login_id`, `created_date`, `updated_date`) VALUES
(1, 1, '2026-05-01', '2026-05-01', 1, 'Labours Day', 0, 1, NULL, '2026-05-22 12:40:12', NULL),
(2, 1, '2026-08-23', '2026-08-25', 3, 'Ayudha Pooja', 0, 1, NULL, '2026-05-22 12:41:05', NULL),
(3, 3, '2026-05-01', '2026-05-01', 1, 'Labour day', 1, 1, 1, '2026-05-22 12:41:20', '2026-05-22'),
(4, 1, '2026-05-01', '2026-05-01', 1, 'sfsdfdsf', 1, 1, NULL, '2026-05-22 12:44:24', NULL),
(5, 2, '2026-05-23', '2026-05-25', 3, 'weekoff', 0, 1, 1, '2026-05-22 12:45:43', '2026-05-22'),
(6, 3, '2026-05-22', '2026-05-23', 2, 'fgfg', 0, 1, NULL, '2026-05-22 12:47:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `home_upload`
--

CREATE TABLE `home_upload` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `media_path` varchar(255) NOT NULL,
  `media_type` enum('image','video','audio') NOT NULL,
  `upload_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `home_upload`
--

INSERT INTO `home_upload` (`id`, `user_id`, `media_path`, `media_type`, `upload_date`) VALUES
(29, 1, '1779455237_WhatsApp Image 2026-04-20 at 11.24.54 AM.jpeg', 'image', '2026-05-22 18:37:17');

-- --------------------------------------------------------

--
-- Table structure for table `leave_creation`
--

CREATE TABLE `leave_creation` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `leave_type` varchar(50) NOT NULL,
  `no_of_days` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_creation`
--

INSERT INTO `leave_creation` (`id`, `company_id`, `leave_type`, `no_of_days`, `status`, `insert_login_id`, `update_login_id`, `created_date`, `updated_date`) VALUES
(1, 1, 'causal', 2, 1, 1, NULL, '2026-05-22 12:21:22', NULL),
(2, 1, 'CL', 2, 0, 1, NULL, '2026-05-22 12:21:38', NULL),
(3, 1, 'SL', 2, 0, 1, NULL, '2026-05-22 12:22:11', NULL),
(4, 2, 'sick', 1, 0, 1, NULL, '2026-05-22 12:28:26', NULL),
(5, 3, 'casual', 2, 0, 1, NULL, '2026-05-22 12:30:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `license_limits`
--

CREATE TABLE `license_limits` (
  `id` int(11) NOT NULL,
  `company_limit` int(11) NOT NULL,
  `branch_limit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `license_limits`
--

INSERT INTO `license_limits` (`id`, `company_limit`, `branch_limit`) VALUES
(1, 3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `location_access_mapping`
--

CREATE TABLE `location_access_mapping` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(25) NOT NULL,
  `staff_profile_id` int(11) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `assigned_branch` int(11) NOT NULL,
  `lattitude_longitude` varchar(250) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `location_access_mapping`
--

INSERT INTO `location_access_mapping` (`id`, `staff_id`, `staff_profile_id`, `from_date`, `to_date`, `assigned_branch`, `lattitude_longitude`, `reason`, `status`, `insert_login_id`, `update_login_id`, `created_date`, `updated_date`) VALUES
(1, 'FTS-002', 5, '2026-05-23', '2026-05-24', 3, '78787878', 'checking', 1, 1, 1, '2026-05-22 17:24:24', '2026-05-22'),
(2, 'FTS-002', 5, '2026-05-23', '2026-05-23', 3, '78787878', 'gfhfghf', 0, 1, NULL, '2026-05-22 17:27:07', NULL),
(3, 'FTS-002', 5, '2026-05-24', '2026-05-27', 1, 'villlianur', 'fgfgf', 0, 1, NULL, '2026-05-22 17:27:35', NULL),
(4, 'MS-002', 7, '2026-05-23', '2026-05-27', 4, '8878577577567', 'ok', 1, 20, NULL, '2026-05-22 17:33:09', NULL),
(5, 'MS-002', 7, '2026-05-23', '2026-05-29', 4, '8878577577567', 'jkhkjk', 0, 20, 20, '2026-05-22 17:33:28', '2026-05-22'),
(6, 'FTS-002', 5, '2026-05-29', '2026-05-30', 1, 'villlianur', 'gfdg', 0, 1, NULL, '2026-05-22 18:34:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `menu_list`
--

CREATE TABLE `menu_list` (
  `id` int(11) NOT NULL,
  `menu` varchar(100) NOT NULL,
  `link` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='All Main Menu''s will be placed here';

--
-- Dumping data for table `menu_list`
--

INSERT INTO `menu_list` (`id`, `menu`, `link`, `icon`) VALUES
(1, 'Home', 'home', 'home'),
(2, 'Dashboard', 'dashboard', 'developer_board'),
(3, 'Organization Management', 'organization_management', 'camera1'),
(4, 'Staff Management', 'staff_management', 'user-check'),
(5, 'Leave & Permission Regularization', 'leave_permission_regularization', 'event_note'),
(6, 'Attendance Management', 'attendance_Management', 'schedule'),
(7, 'Promotion And Transfer', 'promotion_transfer', 'trending_up'),
(8, 'Payroll Management', 'payroll_management', 'credit'),
(9, 'Monitoring Chart', 'monitoring_chart', 'assignment_turned_in');

-- --------------------------------------------------------

--
-- Table structure for table `occupation_info`
--

CREATE TABLE `occupation_info` (
  `id` int(11) NOT NULL,
  `staff_profile_id` int(100) NOT NULL,
  `staff_id` varchar(100) NOT NULL,
  `company_id` varchar(255) NOT NULL,
  `branch_id` int(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `team` varchar(100) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `off_type` int(11) NOT NULL,
  `branch_admin` varchar(100) NOT NULL,
  `reporting_person` varchar(100) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `pf_available` int(11) NOT NULL,
  `esi_available` int(11) NOT NULL,
  `pt_available` int(11) NOT NULL,
  `total_ctc` varchar(100) NOT NULL,
  `annual_ctc` varchar(100) NOT NULL,
  `shift` varchar(100) NOT NULL,
  `ot_payment` varchar(100) NOT NULL,
  `ot_per_hour` varchar(100) NOT NULL,
  `ot_per_day` varchar(100) NOT NULL,
  `effective_from` varchar(100) DEFAULT NULL,
  `occ_status` int(11) NOT NULL DEFAULT 0,
  `insert_login_id` int(11) NOT NULL,
  `created_on` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `occupation_info`
--

INSERT INTO `occupation_info` (`id`, `staff_profile_id`, `staff_id`, `company_id`, `branch_id`, `department`, `team`, `designation`, `off_type`, `branch_admin`, `reporting_person`, `branch`, `pf_available`, `esi_available`, `pt_available`, `total_ctc`, `annual_ctc`, `shift`, `ot_payment`, `ot_per_hour`, `ot_per_day`, `effective_from`, `occ_status`, `insert_login_id`, `created_on`) VALUES
(1, 3, 'FTS-001', '1', 1, '1', '1', '1', 1, '1', '', '1', 2, 2, 1, '50000', '600000', '1', '1', '167', '', NULL, 0, 1, '2026-05-22'),
(2, 5, 'FTS-002', '1', 1, '1', '1', '2', 1, '1', '3', '3', 1, 1, 2, '35000', '420000', '2', '1', '130', '', NULL, 0, 1, '2026-05-22'),
(3, 6, 'MS-001', '3', 4, '2', '5', '1', 1, '2', '', '', 2, 2, 1, '20000', '240000', '6', '2', '', '200', NULL, 0, 1, '2026-05-22'),
(4, 7, 'MS-002', '3', 4, '2', '6', '2', 1, '1', '6', '4', 1, 1, 1, '15000', '180000', '6', '1', '56', '', NULL, 0, 1, '2026-05-22'),
(5, 8, 'UFS-001', '2', 2, '2', '4', '2', 1, '1', '', '2', 2, 2, 2, '30000', '360000', '5', '1', '100', '', NULL, 0, 1, '2026-05-22'),
(6, 10, 'FTS-003', '1', 3, '1', '2', '2', 2, '2', '3', '', 1, 1, 1, '30000', '360000', '2', '1', '111', '', NULL, 0, 1, '2026-05-22'),
(7, 5, 'FTS-002', '1', 1, '1', '8', '2', 1, '1', '3', '1', 1, 1, 2, '35000', '420000', '2', '1', '130', '', '2026-05-23', 2, 1, '2026-05-22'),
(8, 10, 'FTS-003', '1', 1, '2', '3', '2', 2, '2', '3', '', 1, 1, 1, '30000', '360000', '2', '1', '111', '', '2026-05-30', 2, 1, '2026-05-22'),
(9, 10, 'FTS-003', '1', 1, '2', '3', '2', 2, '2', '3', '', 1, 2, 2, '45000', '540000', '2', '1', '111', '', '2026-05-23', 3, 1, '2026-05-22'),
(10, 3, 'FTS-001', '1', 1, '1', '1', '1', 1, '1', '', '1', 2, 2, 2, '63000', '756000', '1', '1', '167', '', '2026-06-02', 3, 1, '2026-05-22'),
(11, 5, 'FTS-002', '1', 3, '1', '8', '2', 1, '2', '3', '', 1, 1, 2, '35000', '420000', '2', '1', '130', '', '2026-05-22', 2, 1, '2026-05-22');

-- --------------------------------------------------------

--
-- Table structure for table `qualification_info`
--

CREATE TABLE `qualification_info` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(255) NOT NULL,
  `staff_profile_id` varchar(255) NOT NULL,
  `highest_qualification` varchar(255) NOT NULL,
  `degree` varchar(255) NOT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `college` varchar(255) NOT NULL,
  `university` varchar(255) NOT NULL,
  `year_of_passing` varchar(200) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `qualification_info`
--

INSERT INTO `qualification_info` (`id`, `staff_id`, `staff_profile_id`, `highest_qualification`, `degree`, `specialization`, `college`, `university`, `year_of_passing`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(1, 'FTS-001', '1', 'PG', 'M tech', 'IT', 'SMVEC', 'Pondicherry', '2000', 1, 1, '2026-05-22', '2026-05-22'),
(2, 'FTS-002', '4', 'UG', 'B tech', 'dfssdf', 'SMVEC', 'dfgdf', '2020', 1, NULL, '2026-05-22', NULL),
(3, 'MS-001', '6', 'PG', 'MTech', 'CSE', 'sdfsd', '22', '8900', 1, NULL, '2026-05-22', NULL),
(4, 'MS-002', '7', 'UG', 'dfgfgfg', 'hhgh', 'dgdfg', 'gdfg', '2002', 1, NULL, '2026-05-22', NULL),
(5, 'UFS-001', '8', 'UG', 'BTech', 'CSE', 'ghg', 'uyh', '1990', 1, NULL, '2026-05-22', NULL),
(6, 'FTS-003', '10', 'UG', 'B Tech', 'ECE', 'sfsf', 'fsfsdf', '9099', 1, NULL, '2026-05-22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `regularization`
--

CREATE TABLE `regularization` (
  `id` int(50) NOT NULL,
  `staff_profile_id` int(50) DEFAULT NULL,
  `company_id` int(50) DEFAULT NULL,
  `branch_id` int(50) DEFAULT NULL,
  `dep_id` int(50) DEFAULT NULL,
  `des_id` int(50) DEFAULT NULL,
  `team_id` int(50) DEFAULT NULL,
  `req_type` varchar(250) DEFAULT NULL,
  `leave_type` varchar(250) DEFAULT NULL,
  `balance_req` varchar(250) DEFAULT NULL,
  `req_date` date DEFAULT NULL,
  `from_date` datetime DEFAULT NULL,
  `total_min` varchar(50) DEFAULT NULL,
  `to_date` datetime DEFAULT NULL,
  `approved_from_date` datetime DEFAULT NULL,
  `approved_to_date` datetime DEFAULT NULL,
  `approved_total_min` varchar(250) DEFAULT NULL,
  `remarks` varchar(250) DEFAULT NULL,
  `reason` varchar(250) DEFAULT NULL,
  `status` int(50) DEFAULT NULL,
  `insert_login_id` int(50) DEFAULT NULL,
  `updated_login_id` int(50) DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `regularization`
--

INSERT INTO `regularization` (`id`, `staff_profile_id`, `company_id`, `branch_id`, `dep_id`, `des_id`, `team_id`, `req_type`, `leave_type`, `balance_req`, `req_date`, `from_date`, `total_min`, `to_date`, `approved_from_date`, `approved_to_date`, `approved_total_min`, `remarks`, `reason`, `status`, `insert_login_id`, `updated_login_id`, `created_date`, `updated_date`) VALUES
(6, 3, 1, 1, 1, 2, 1, '2', '', '3', '2026-05-16', '2026-05-22 15:54:00', '120', '2026-05-22 16:54:00', '2026-05-22 06:00:00', '2026-05-22 08:00:00', '120', 'gdfg', 'fddf', 1, 1, 1, '2026-05-16', '2026-05-19'),
(12, 5, 1, 1, 1, 2, 1, '4', '', '', '2026-05-18', '2026-05-22 10:34:00', '120', '2026-05-22 12:34:00', '2026-05-22 16:00:00', '2026-05-22 17:00:00', '60', 'cxzcxzc', 'fdf', 1, 1, 1, '2026-05-18', '2026-05-19'),
(13, 6, 3, 1, 1, 2, 1, '4', NULL, NULL, '2026-05-18', '2026-05-22 10:34:00', '120', '2026-05-22 12:34:00', '2026-05-22 14:00:00', '2026-05-22 15:30:00', '90', 'hgfh', 'fghgf', 1, 1, 1, '2026-05-18', '2026-05-19'),
(14, 7, 3, 1, 1, 1, 2, '4', NULL, NULL, '2026-05-18', '2026-05-22 10:34:00', '120', '2026-05-22 12:34:00', '2026-05-22 05:00:00', '2026-05-15 06:00:00', '60', 'sds', 'fsdf', 1, 1, 1, '2026-05-18', '2026-05-19');

-- --------------------------------------------------------

--
-- Table structure for table `shift_creation`
--

CREATE TABLE `shift_creation` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `shift_name` varchar(25) NOT NULL,
  `start_time` varchar(25) NOT NULL,
  `end_time` varchar(25) NOT NULL,
  `shift_time` varchar(25) NOT NULL,
  `grace_time` varchar(25) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shift_creation`
--

INSERT INTO `shift_creation` (`id`, `company_id`, `shift_name`, `start_time`, `end_time`, `shift_time`, `grace_time`, `status`, `insert_login_id`, `update_login_id`, `created_date`, `updated_date`) VALUES
(1, 1, 'Morning Shift', '06:00:00', '16:00:00', '10 Hrs', '1', 0, 1, NULL, '2026-05-22 12:23:04', NULL),
(2, 1, 'Regular Shift', '09:30:00', '19:00:00', '9 Hrs 30 Mins', '30m', 0, 1, NULL, '2026-05-22 12:23:56', NULL),
(3, 1, 'fghfgh', '12:24:00', '00:28:00', '12 Hrs 4 Mins', '57', 1, 1, 1, '2026-05-22 12:24:11', '2026-05-22'),
(4, 1, 'Night', '19:00:00', '04:00:00', '9 Hrs', '30', 0, 1, NULL, '2026-05-22 12:25:17', NULL),
(5, 2, 'Morning', '06:00:00', '16:28:00', '10 Hrs 28 Mins', '2', 0, 1, NULL, '2026-05-22 12:29:07', NULL),
(6, 3, 'evening  shift', '18:30:00', '04:00:00', '9 Hrs 30 Mins', '2', 0, 1, NULL, '2026-05-22 12:30:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `staff_creation`
--

CREATE TABLE `staff_creation` (
  `id` int(11) NOT NULL,
  `company_id` int(100) NOT NULL,
  `staff_id` varchar(250) DEFAULT NULL,
  `staff_name` varchar(100) NOT NULL,
  `staff_type` int(11) NOT NULL,
  `address` varchar(100) NOT NULL,
  `state` varchar(50) NOT NULL,
  `district` varchar(50) DEFAULT NULL,
  `place` varchar(100) DEFAULT NULL,
  `pincode` varchar(100) NOT NULL,
  `dob` varchar(100) DEFAULT NULL,
  `blood_group` varchar(50) DEFAULT NULL,
  `pic` varchar(100) NOT NULL,
  `gender` varchar(100) DEFAULT NULL,
  `marital_status` varchar(100) DEFAULT NULL,
  `spouse_name` varchar(100) DEFAULT NULL,
  `anniversary_date` varchar(100) DEFAULT NULL,
  `joining_date` varchar(100) DEFAULT NULL,
  `relieve_date` varchar(100) DEFAULT NULL,
  `notice_period` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mobile1` varchar(100) DEFAULT NULL,
  `mobile2` varchar(100) DEFAULT NULL,
  `whatsapp` varchar(100) DEFAULT NULL,
  `instagram` varchar(100) DEFAULT NULL,
  `facebook` varchar(100) DEFAULT NULL,
  `acc_holder_name` varchar(255) NOT NULL,
  `acc_number` varchar(255) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `bank_branch` varchar(255) NOT NULL,
  `ifsc_code` varchar(255) NOT NULL,
  `notice_per_served` int(11) DEFAULT NULL,
  `exit_type` varchar(200) DEFAULT NULL,
  `reason` varchar(200) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_creation`
--

INSERT INTO `staff_creation` (`id`, `company_id`, `staff_id`, `staff_name`, `staff_type`, `address`, `state`, `district`, `place`, `pincode`, `dob`, `blood_group`, `pic`, `gender`, `marital_status`, `spouse_name`, `anniversary_date`, `joining_date`, `relieve_date`, `notice_period`, `email`, `mobile1`, `mobile2`, `whatsapp`, `instagram`, `facebook`, `acc_holder_name`, `acc_number`, `bank_name`, `bank_branch`, `ifsc_code`, `notice_per_served`, `exit_type`, `reason`, `status`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(3, 1, 'FTS-001', 'Maya', 1, 'Bussy Street', '2', '39', 'villianur', '658878', '', '', '6a100b8fb7346.jpg', '2', '1', 'Arjun', '2026-05-30', '2026-05-23', '', '3', 'maya@gmail.com', '6867868678', '7878787886', '6767867886', 'mauuy12', 'dffg55', 'Maya', '6786787686767878', 'ICICI', 'Villianur', 'SBI-009', NULL, NULL, NULL, 1, 1, 1, '2026-05-22 13:20:48', '2026-05-22'),
(5, 1, 'FTS-002', 'Heera', 2, 'Dindugul', '1', '10', 'Vandavasi', '676777', '', '', '6a102b46c2b17.jpg', '2', '2', '', '', '2026-05-23', '', '6', 'vdrmarudham@gmail.com', '8786567657', '', '', '', '', 'Heera', '6786786768688', 'ICICI', 'Villianur', '878', NULL, NULL, NULL, 1, 1, 1, '2026-05-22 15:35:30', '2026-05-22'),
(6, 3, 'MS-001', 'kaushi', 1, 'Dindugul', '1', '14', 'villianur', '787868', '', '', '6a102c5f8e0e2.jpg', '1', '2', '', '', '2026-05-23', '', '3', 'feather@gmail.com', '8768678678', '', '', '', '', 'Madhi', '675675757567', 'ICICI', 'Villianur', '567567', NULL, NULL, NULL, 1, 1, 1, '2026-05-22 15:41:42', '2026-05-22'),
(7, 3, 'MS-002', 'Thara', 2, 'Bussy Street', '1', '2', 'Vandavasi', '789789', '', '', '6a102de8d53f6.jpg', '1', '2', '', '', '2026-05-23', '', '', 'fgfg@gmail.com', '7878678678', '', '', '', '', 'Naveen', '8678566567567', 'gdfgdfg', 'Villianur', 'SBI-009', NULL, NULL, NULL, 1, 1, 1, '2026-05-22 15:45:22', '2026-05-22'),
(8, 2, 'UFS-001', 'Madhan', 1, 'Bussy Street', '1', '4', 'villianur', '786788', '', '', '6a102ef8d0d26.jpg', '1', '2', '', '', '2026-05-22', '', '', 'tttryt@gmail.com', '7897878978', '', '', '', '', 'Madhan', '67567675756777', 'ICICI', 'Villianur', 'SBI-002', NULL, NULL, NULL, 1, 1, 1, '2026-05-22 15:52:44', '2026-05-22'),
(10, 1, 'FTS-003', 'Zayn', 2, 'Bussy Street', '1', '6', 'Vandavasi', '789789', '', '', '6a1033c0e56e7.jpg', '1', '2', '', '', '2026-05-23', '2026-05-23', '', 'feather@gmail.com', '8987979789', '', '', 'sfdsf', 'fdsf', 'Loga', '7978978978978978978', 'ICICI', 'Villianur', 'SBI-009', 2, 'dfsdf', 'sfdfdsdsfffsdfg', 2, 1, 1, '2026-05-22 16:13:08', '2026-05-22');

-- --------------------------------------------------------

--
-- Table structure for table `staff_ctc_info`
--

CREATE TABLE `staff_ctc_info` (
  `id` int(11) NOT NULL,
  `staff_profile_id` varchar(100) NOT NULL,
  `staff_id` varchar(100) NOT NULL,
  `ctc_id` int(11) NOT NULL,
  `ctc_amount` varchar(100) DEFAULT NULL,
  `ctc_percentage` varchar(100) DEFAULT NULL,
  `total_ctc` varchar(100) DEFAULT NULL,
  `total_amount` varchar(100) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_ctc_info`
--

INSERT INTO `staff_ctc_info` (`id`, `staff_profile_id`, `staff_id`, `ctc_id`, `ctc_amount`, `ctc_percentage`, `total_ctc`, `total_amount`, `insert_login_id`, `created_date`) VALUES
(1, '3', 'FTS-001', 1, '45000', '90.00', '50000', '51000', 1, '2026-05-22 13:23:51'),
(2, '3', 'FTS-001', 2, '3000', '6.00', '50000', '51000', 1, '2026-05-22 13:23:51'),
(3, '3', 'FTS-001', 4, '2000', '4.00', '50000', '51000', 1, '2026-05-22 13:23:51'),
(4, '3', 'FTS-001', 5, '1000', '0', '50000', '51000', 1, '2026-05-22 13:23:51'),
(5, '5', 'FTS-002', 1, '30000', '85.71', '35000', '36000', 1, '2026-05-22 15:39:10'),
(6, '5', 'FTS-002', 2, '3500', '10.00', '35000', '36000', 1, '2026-05-22 15:39:10'),
(7, '5', 'FTS-002', 4, '1500', '4.29', '35000', '36000', 1, '2026-05-22 15:39:10'),
(8, '5', 'FTS-002', 5, '1000', '0', '35000', '36000', 1, '2026-05-22 15:39:10'),
(9, '6', 'MS-001', 11, '15000', '75.00', '20000', '20000', 1, '2026-05-22 15:43:51'),
(10, '6', 'MS-001', 12, '3000', '15.00', '20000', '20000', 1, '2026-05-22 15:43:51'),
(11, '6', 'MS-001', 13, '2000', '10.00', '20000', '20000', 1, '2026-05-22 15:43:51'),
(12, '6', 'MS-001', 14, '', '', '20000', '20000', 1, '2026-05-22 15:43:51'),
(13, '7', 'MS-002', 11, '12000', '80.00', '15000', '15050', 1, '2026-05-22 15:50:24'),
(14, '7', 'MS-002', 12, '2000', '13.33', '15000', '15050', 1, '2026-05-22 15:50:24'),
(15, '7', 'MS-002', 13, '1000', '6.67', '15000', '15050', 1, '2026-05-22 15:50:24'),
(16, '7', 'MS-002', 14, '50', '0', '15000', '15050', 1, '2026-05-22 15:50:24'),
(17, '8', 'UFS-001', 6, '27000', '90.00', '30000', '30200', 1, '2026-05-22 15:54:56'),
(18, '8', 'UFS-001', 7, '2000', '6.67', '30000', '30200', 1, '2026-05-22 15:54:56'),
(19, '8', 'UFS-001', 8, '1000', '3.33', '30000', '30200', 1, '2026-05-22 15:54:56'),
(20, '8', 'UFS-001', 9, '200', '0', '30000', '30200', 1, '2026-05-22 15:54:56'),
(21, '10', 'FTS-003', 1, '27000', '90.00', '30000', '30200', 1, '2026-05-22 16:15:20'),
(22, '10', 'FTS-003', 2, '3000', '10.00', '30000', '30200', 1, '2026-05-22 16:15:20'),
(23, '10', 'FTS-003', 4, '', '', '30000', '30200', 1, '2026-05-22 16:15:20'),
(24, '10', 'FTS-003', 5, '200', '0', '30000', '30200', 1, '2026-05-22 16:15:20'),
(25, '10', 'FTS-003', 1, '40000', '88.89', '45000', '45000', 1, '2026-05-22 17:09:41'),
(26, '10', 'FTS-003', 2, '3000', '6.67', '45000', '45000', 1, '2026-05-22 17:09:41'),
(27, '10', 'FTS-003', 4, '2000', '4.44', '45000', '45000', 1, '2026-05-22 17:09:41'),
(28, '3', 'FTS-001', 1, '57000', '90.48', '63000', '63500', 1, '2026-05-22 17:12:00'),
(29, '3', 'FTS-001', 2, '2000', '3.17', '63000', '63500', 1, '2026-05-22 17:12:00'),
(30, '3', 'FTS-001', 4, '4000', '6.35', '63000', '63500', 1, '2026-05-22 17:12:00'),
(31, '3', 'FTS-001', 5, '500', '0', '63000', '63500', 1, '2026-05-22 17:12:00');

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` int(11) NOT NULL,
  `state_name` varchar(50) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `state_name`, `status`) VALUES
(1, 'Tamil Nadu', 1),
(2, 'Puducherry', 1);

-- --------------------------------------------------------

--
-- Table structure for table `statutory_compliance`
--

CREATE TABLE `statutory_compliance` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  `pf_applicable` varchar(25) NOT NULL,
  `pf_number` varchar(25) DEFAULT NULL,
  `employee_contribution` varchar(25) DEFAULT NULL,
  `employer_contribution` varchar(25) DEFAULT NULL,
  `admin_charge` varchar(25) DEFAULT NULL,
  `pension` varchar(25) DEFAULT NULL,
  `apply_wage_limit` varchar(25) DEFAULT NULL,
  `pf_wage_limit` varchar(25) DEFAULT NULL,
  `esi_applicable` varchar(25) NOT NULL,
  `employee_share` varchar(25) DEFAULT NULL,
  `employer_share` varchar(25) DEFAULT NULL,
  `professional_tax_applicable` varchar(25) DEFAULT NULL,
  `calculation_type` varchar(25) DEFAULT NULL,
  `percentage` varchar(25) DEFAULT NULL,
  `slab` varchar(25) DEFAULT NULL,
  `insert_login_id` varchar(25) NOT NULL,
  `update_login_id` varchar(25) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `statutory_compliance`
--

INSERT INTO `statutory_compliance` (`id`, `company_id`, `state`, `pf_applicable`, `pf_number`, `employee_contribution`, `employer_contribution`, `admin_charge`, `pension`, `apply_wage_limit`, `pf_wage_limit`, `esi_applicable`, `employee_share`, `employer_share`, `professional_tax_applicable`, `calculation_type`, `percentage`, `slab`, `insert_login_id`, `update_login_id`, `created_date`, `updated_date`) VALUES
(1, 1, 2, '1', '5645654654', '70', '30', '2', '1', '1', '20', '1', '70', '30', '1', '1', '5', '', '1', NULL, '2026-05-22 12:16:59', NULL),
(2, 2, 1, '1', '', '70', '30', '2', '2', '', '', '1', '70', '30', '', '2', '', '3', '1', NULL, '2026-05-22 12:18:49', NULL),
(3, 3, 1, '2', '', '', '', '', '', '', '', '1', '70', '25', '1', '1', '', '', '1', NULL, '2026-05-22 12:19:30', NULL),
(4, 1, 2, '2', '', '', '', '', '', '', '', '2', '', '', '2', '', '', '', '1', NULL, '2026-05-22 12:19:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sub_menu_list`
--

CREATE TABLE `sub_menu_list` (
  `id` int(11) NOT NULL,
  `main_menu` int(11) NOT NULL,
  `sub_menu` varchar(100) NOT NULL,
  `link` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='All Sub menu of the project should be placed here';

--
-- Dumping data for table `sub_menu_list`
--

INSERT INTO `sub_menu_list` (`id`, `main_menu`, `sub_menu`, `link`, `icon`) VALUES
(1, 1, 'Home', 'home_page', 'library'),
(2, 2, 'Dashboard', 'dashboard', 'home'),
(3, 3, 'Company Creation', 'company_creation', 'briefcase'),
(4, 3, 'Department Creation', 'department_creation', 'grid'),
(5, 3, 'Designation Creation', 'designation_creation', 'award'),
(6, 3, 'Branch Creation', 'branch_creation', 'layers'),
(7, 3, 'Team Creation', 'team_creation', 'users'),
(8, 3, 'CTC Creation', 'ctc_creation', 'wallet'),
(9, 3, 'Statutory Compliance', 'statutory_compliance', 'folder'),
(10, 3, 'Leave Master', 'leave_master', 'calendar'),
(11, 3, 'Holiday Creation', 'holiday_creation', 'date_range'),
(12, 4, 'Staff Creation', 'staff_creation', 'person_add'),
(13, 4, 'Staff Exit Management', 'staff_exit_management', 'exit_to_app'),
(14, 4, 'Manage User', 'manage_user', 'settings'),
(15, 5, 'Leave & Permission Regularization', 'leave_permission_regularization', 'assignment'),
(16, 6, 'Location Access', 'location_access', 'my_location'),
(17, 6, 'Daily Attendance', 'daily_attendance', 'today'),
(18, 7, 'Promotion And Transfer', 'promotion_transfer', 'swap_horiz'),
(19, 8, 'Payroll Processing', 'payroll_processing', 'attach_money'),
(20, 8, 'Pay Slip', 'pay_slip', 'receipt'),
(21, 9, 'Attendance & OT Monitor', 'attendance_ot_monitor', 'area-graph');

-- --------------------------------------------------------

--
-- Table structure for table `team_creation`
--

CREATE TABLE `team_creation` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team_creation`
--

INSERT INTO `team_creation` (`id`, `company_id`, `department_id`, `status`, `insert_login_id`, `update_login_id`, `created_date`, `updated_date`) VALUES
(1, 1, 1, 0, 1, 1, '2026-05-22 11:41:38', '2026-05-22'),
(2, 2, 3, 0, 1, NULL, '2026-05-22 11:42:27', NULL),
(3, 1, 2, 0, 1, NULL, '2026-05-22 11:42:55', NULL),
(4, 3, 2, 0, 1, NULL, '2026-05-22 11:43:54', NULL),
(5, 2, 2, 0, 1, NULL, '2026-05-22 11:45:27', NULL),
(6, 1, 1, 0, 1, NULL, '2026-05-22 11:45:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `team_creation_mapping`
--

CREATE TABLE `team_creation_mapping` (
  `id` int(11) NOT NULL,
  `team_creation_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team_creation_mapping`
--

INSERT INTO `team_creation_mapping` (`id`, `team_creation_id`, `team_id`) VALUES
(2, 1, 2),
(3, 2, 4),
(4, 2, 3),
(5, 3, 3),
(6, 4, 5),
(7, 4, 6),
(8, 1, 8),
(9, 5, 3),
(10, 5, 4),
(11, 5, 9),
(12, 6, 1),
(13, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `team_name_creation`
--

CREATE TABLE `team_name_creation` (
  `id` int(11) NOT NULL,
  `team_code` varchar(25) NOT NULL,
  `team_name` varchar(50) NOT NULL,
  `team_status` int(11) NOT NULL DEFAULT 0,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team_name_creation`
--

INSERT INTO `team_name_creation` (`id`, `team_code`, `team_name`, `team_status`, `insert_login_id`, `update_login_id`, `created_date`, `updated_date`) VALUES
(1, 'T-101', 'Team 1', 0, 1, NULL, '2026-05-22 11:41:14', NULL),
(2, 'T-102', 'Team 2', 0, 1, NULL, '2026-05-22 11:41:30', NULL),
(3, 'T-103', 'Test1', 0, 1, NULL, '2026-05-22 11:42:04', NULL),
(4, 'T-104', 'Test 2', 0, 1, NULL, '2026-05-22 11:42:19', NULL),
(5, 'T-105', 'sales1', 0, 1, 1, '2026-05-22 11:43:15', '2026-05-22'),
(6, 'T-106', 'sales2', 0, 1, NULL, '2026-05-22 11:43:23', NULL),
(7, 'T-107', 'dfgdfgdfg', 1, 1, NULL, '2026-05-22 11:43:40', NULL),
(8, 'T-108', 'team3', 0, 1, NULL, '2026-05-22 11:44:13', NULL),
(9, 'T-109', 'sales3', 0, 1, NULL, '2026-05-22 11:44:26', NULL),
(10, 'T-110', 'sales2', 0, 1, NULL, '2026-05-22 11:52:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_code` varchar(100) NOT NULL,
  `company_id` int(11) NOT NULL,
  `role` int(11) NOT NULL,
  `staff_name_id` int(11) NOT NULL,
  `staff_id` varchar(25) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `confirm_password` varchar(50) NOT NULL,
  `download_access` int(11) NOT NULL,
  `report_access` int(11) NOT NULL,
  `home_access` int(11) NOT NULL,
  `screens` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `insert_login_id` varchar(100) NOT NULL,
  `update_login_id` varchar(100) DEFAULT NULL,
  `created_on` date NOT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='All the users will be stored here with screen access details';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_code`, `company_id`, `role`, `staff_name_id`, `staff_id`, `user_name`, `password`, `confirm_password`, `download_access`, `report_access`, `home_access`, `screens`, `status`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(1, 'US-001', 1, 1, 3, 'FTS-001', 'admin', '123', '123', 1, 1, 1, '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21', 0, '1', '1', '2024-06-13', '2026-05-22'),
(18, 'US-002', 3, 2, 7, 'MS-002', 'thara', '123', '123', 2, 2, 2, '1,2,3,4,6,7,8,9,10,11,12,13,14', 1, '1', NULL, '2026-05-22', NULL),
(19, 'US-003', 1, 2, 5, 'FTS-002', 'heera', '123', '123', 2, 2, 2, '1,3,7,11,12,13,15,16', 0, '1', '1', '2026-05-22', '2026-05-22'),
(20, 'US-004', 3, 1, 6, 'MS-001', 'kaushi', '123', '123', 1, 2, 2, '1,12,16,18', 0, '1', NULL, '2026-05-22', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branch_creation`
--
ALTER TABLE `branch_creation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `state_id` (`state`),
  ADD KEY `district_id` (`district`);

--
-- Indexes for table `company_creation`
--
ALTER TABLE `company_creation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `State ids` (`state`),
  ADD KEY `District ids` (`district`);

--
-- Indexes for table `company_department_mapping`
--
ALTER TABLE `company_department_mapping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_designation_mapping`
--
ALTER TABLE `company_designation_mapping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_policies`
--
ALTER TABLE `company_policies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_weekoffs`
--
ALTER TABLE `company_weekoffs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ctc_creation`
--
ALTER TABLE `ctc_creation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department_creation`
--
ALTER TABLE `department_creation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `designation_creation`
--
ALTER TABLE `designation_creation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `State id` (`state_id`);

--
-- Indexes for table `document_info`
--
ALTER TABLE `document_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `experience_info`
--
ALTER TABLE `experience_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `family_info`
--
ALTER TABLE `family_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `holiday_creation`
--
ALTER TABLE `holiday_creation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `home_upload`
--
ALTER TABLE `home_upload`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leave_creation`
--
ALTER TABLE `leave_creation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `license_limits`
--
ALTER TABLE `license_limits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `location_access_mapping`
--
ALTER TABLE `location_access_mapping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_list`
--
ALTER TABLE `menu_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `occupation_info`
--
ALTER TABLE `occupation_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qualification_info`
--
ALTER TABLE `qualification_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `regularization`
--
ALTER TABLE `regularization`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shift_creation`
--
ALTER TABLE `shift_creation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_creation`
--
ALTER TABLE `staff_creation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_ctc_info`
--
ALTER TABLE `staff_ctc_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `statutory_compliance`
--
ALTER TABLE `statutory_compliance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sub_menu_list`
--
ALTER TABLE `sub_menu_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Main menu id` (`main_menu`);

--
-- Indexes for table `team_creation`
--
ALTER TABLE `team_creation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `team_creation_mapping`
--
ALTER TABLE `team_creation_mapping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `team_name_creation`
--
ALTER TABLE `team_name_creation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `branch_creation`
--
ALTER TABLE `branch_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `company_creation`
--
ALTER TABLE `company_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `company_department_mapping`
--
ALTER TABLE `company_department_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `company_designation_mapping`
--
ALTER TABLE `company_designation_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `company_policies`
--
ALTER TABLE `company_policies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `company_weekoffs`
--
ALTER TABLE `company_weekoffs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `ctc_creation`
--
ALTER TABLE `ctc_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `department_creation`
--
ALTER TABLE `department_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `designation_creation`
--
ALTER TABLE `designation_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `document_info`
--
ALTER TABLE `document_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `experience_info`
--
ALTER TABLE `experience_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `family_info`
--
ALTER TABLE `family_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `holiday_creation`
--
ALTER TABLE `holiday_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `home_upload`
--
ALTER TABLE `home_upload`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `leave_creation`
--
ALTER TABLE `leave_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `license_limits`
--
ALTER TABLE `license_limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `location_access_mapping`
--
ALTER TABLE `location_access_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `menu_list`
--
ALTER TABLE `menu_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `occupation_info`
--
ALTER TABLE `occupation_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `qualification_info`
--
ALTER TABLE `qualification_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `regularization`
--
ALTER TABLE `regularization`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `shift_creation`
--
ALTER TABLE `shift_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `staff_creation`
--
ALTER TABLE `staff_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `staff_ctc_info`
--
ALTER TABLE `staff_ctc_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `statutory_compliance`
--
ALTER TABLE `statutory_compliance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sub_menu_list`
--
ALTER TABLE `sub_menu_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `team_creation`
--
ALTER TABLE `team_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `team_creation_mapping`
--
ALTER TABLE `team_creation_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `team_name_creation`
--
ALTER TABLE `team_name_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `branch_creation`
--
ALTER TABLE `branch_creation`
  ADD CONSTRAINT `district_id` FOREIGN KEY (`district`) REFERENCES `districts` (`id`),
  ADD CONSTRAINT `state_id` FOREIGN KEY (`state`) REFERENCES `states` (`id`);

--
-- Constraints for table `districts`
--
ALTER TABLE `districts`
  ADD CONSTRAINT `State id` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

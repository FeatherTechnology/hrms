-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2026 at 03:10 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hr`
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

-- --------------------------------------------------------

--
-- Table structure for table `company_department_mapping`
--

CREATE TABLE `company_department_mapping` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_designation_mapping`
--

CREATE TABLE `company_designation_mapping` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `designation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_policies`
--

CREATE TABLE `company_policies` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `max_permission` varchar(25) DEFAULT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_weekoffs`
--

CREATE TABLE `company_weekoffs` (
  `id` int(11) NOT NULL,
  `company_policies_id` int(11) NOT NULL,
  `week_day` varchar(50) DEFAULT NULL,
  `week_off` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `experience_info`
--

CREATE TABLE `experience_info` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(100) NOT NULL,
  `staff_profile_id` varchar(100) NOT NULL,
  `exp_type` varchar(150) NOT NULL,
  `total_experience` varchar(150) DEFAULT NULL,
  `pre_company` varchar(150) DEFAULT NULL,
  `pre_designation` varchar(150) DEFAULT NULL,
  `work_duration` varchar(100) DEFAULT NULL,
  `last_salary` varchar(1000) DEFAULT NULL,
  `reason_for_leaving` varchar(150) DEFAULT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `feedback_department_mapping`
--

CREATE TABLE `feedback_department_mapping` (
  `id` int(11) NOT NULL,
  `feedback_titles_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback_questions_mapping`
--

CREATE TABLE `feedback_questions_mapping` (
  `id` int(11) NOT NULL,
  `feedback_titles_id` int(11) NOT NULL,
  `feedback_questions` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback_titles`
--

CREATE TABLE `feedback_titles` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `start_date_time` datetime NOT NULL,
  `end_date_time` datetime NOT NULL,
  `feedback_title` varchar(100) NOT NULL,
  `feedback_status` int(11) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `general_feedback`
--

CREATE TABLE `general_feedback` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `feedback_name` varchar(50) NOT NULL,
  `status` int(11) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `no_of_days` int(25) DEFAULT NULL,
  `assigned_branch` int(11) NOT NULL,
  `lattitude_longitude` varchar(250) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(2, 'Organization Management', 'organization_management', 'camera1'),
(3, 'Staff Management', 'staff_management', 'user-check'),
(4, 'Regularization', 'regularization', 'event_note'),
(5, 'Attendance Management', 'attendance_Management', 'schedule'),
(6, 'Promotion And Transfer', 'promotion_transfer', 'trending_up'),
(7, 'Payroll Management', 'payroll_management', 'credit'),
(8, 'Feedback Management', 'feedback_management', 'pie_chart'),
(9, 'My Feedbacks', 'my_feedbacks', 'comment'),
(10, 'Monitoring Chart', 'monitoring_chart', 'assignment_turned_in'),
(11, 'Reports', 'reports', 'event_note');

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

-- --------------------------------------------------------

--
-- Table structure for table `poll_answers`
--

CREATE TABLE `poll_answers` (
  `id` int(11) NOT NULL,
  `poll_titles_id` int(11) NOT NULL,
  `poll_value` int(11) NOT NULL,
  `reason` varchar(100) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `poll_department_mapping`
--

CREATE TABLE `poll_department_mapping` (
  `id` int(11) NOT NULL,
  `poll_titles_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `poll_options_mapping`
--

CREATE TABLE `poll_options_mapping` (
  `id` int(11) NOT NULL,
  `poll_titles_id` int(11) NOT NULL,
  `poll_options` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `poll_titles`
--

CREATE TABLE `poll_titles` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `start_date_time` datetime NOT NULL,
  `end_date_time` datetime NOT NULL,
  `poll_title` varchar(100) NOT NULL,
  `poll_description` varchar(100) NOT NULL,
  `poll_status` int(11) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `rating_answers`
--

CREATE TABLE `rating_answers` (
  `id` int(11) NOT NULL,
  `rating_titles_id` int(11) NOT NULL,
  `rating_value` int(11) NOT NULL,
  `reason` varchar(100) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rating_department_mapping`
--

CREATE TABLE `rating_department_mapping` (
  `id` int(11) NOT NULL,
  `rating_titles_id` int(11) NOT NULL,
  `department_id` int(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rating_titles`
--

CREATE TABLE `rating_titles` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `start_date_time` datetime NOT NULL,
  `end_date_time` datetime NOT NULL,
  `rating_title` varchar(100) NOT NULL,
  `rating_description` varchar(100) NOT NULL,
  `rating_status` int(11) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `age` int(25) DEFAULT NULL,
  `blood_group` varchar(50) DEFAULT NULL,
  `pic` varchar(100) NOT NULL,
  `gender` varchar(100) DEFAULT NULL,
  `marital_status` varchar(100) DEFAULT NULL,
  `spouse_name` varchar(100) DEFAULT NULL,
  `anniversary_date` varchar(100) DEFAULT NULL,
  `joining_date` varchar(100) DEFAULT NULL,
  `relieve_date` varchar(100) DEFAULT NULL,
  `notice_period` varchar(100) NOT NULL,
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
  `created_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_general_feedback`
--

CREATE TABLE `staff_general_feedback` (
  `id` int(50) NOT NULL,
  `general_feedback_id` int(50) DEFAULT NULL,
  `commants` varchar(250) DEFAULT NULL,
  `attachment` varchar(250) DEFAULT NULL,
  `insert_login_id` int(50) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_sch_feedback`
--

CREATE TABLE `staff_sch_feedback` (
  `id` int(11) NOT NULL,
  `feedback_titles_id` int(50) DEFAULT NULL,
  `feedback_ques_map_id` int(11) DEFAULT NULL,
  `answer` text DEFAULT NULL,
  `insert_login_id` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 1, 'Home', 'home_page', 'home'),
(2, 2, 'Company Creation', 'company_creation', 'briefcase'),
(3, 2, 'Branch Creation', 'branch_creation', 'layers'),
(4, 2, 'Team Creation', 'team_creation', 'users'),
(5, 2, 'CTC Creation', 'ctc_creation', 'wallet'),
(6, 2, 'Statutory Compliance', 'statutory_compliance', 'folder'),
(7, 2, 'Leave Master', 'leave_master', 'calendar'),
(11, 2, 'Holiday Creation', 'holiday_creation', 'date_range'),
(12, 3, 'Staff Creation', 'staff_creation', 'person_add'),
(13, 3, 'Staff Exit Management', 'staff_exit_management', 'exit_to_app'),
(14, 3, 'Manage User', 'manage_user', 'settings'),
(15, 4, 'Regularization', 'regularization', 'assignment'),
(16, 5, 'Location Access', 'location_access', 'my_location'),
(17, 5, 'Attendance', 'attendance', 'today'),
(18, 6, 'Promotion And Transfer', 'promotion_transfer', 'swap_horiz'),
(19, 7, 'Payroll Processing', 'payroll_processing', 'attach_money'),
(20, 7, 'Pay Slip', 'pay_slip', 'receipt'),
(21, 8, 'Analytics Dashboard', 'analytics_dashboard', 'bar-chart'),
(22, 8, 'Feedback Engagement', 'feedback_engagement', 'message-circle'),
(23, 9, 'Feedback', 'feedback', 'chat'),
(24, 9, 'Rating', 'rating', 'award'),
(25, 9, 'Poll', 'poll', 'trending-up'),
(26, 10, 'Monitoring Chart', 'monitoring_chart', 'assignment_turned_in'),
(27, 11, 'Staff Report', 'staff_report', 'event_available'),
(28, 11, 'Location Access Report', 'location_access_report', 'terrain'),
(29, 11, 'Feedback Report', 'feedback_report', 'event_note'),
(30, 11, 'Regularization Report', 'regularization_report', 'note'),
(31, 11, 'Promotion And Transfer', 'promotion_transfer_report', 'insert_invitation'),
(32, 11, 'Attendance Report', 'attendance_report', 'today');

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

-- --------------------------------------------------------

--
-- Table structure for table `team_creation_mapping`
--

CREATE TABLE `team_creation_mapping` (
  `id` int(11) NOT NULL,
  `team_creation_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'US-001', 3, 1, 6, 'MS-001', 'admin', '123', '123', 1, 1, 1, '1,2,3,4,5,6,7,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26', 1, '1', '1', '2024-06-13', '2026-06-02'),
(19, 'US-003', 1, 2, 5, 'FTS-002', 'naveen', '123', '123', 2, 2, 2, '1,2,3,4,5,6,7,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26', 0, '1', '21', '2026-05-22', '2026-06-05'),
(21, 'US-005', 1, 1, 4, 'FTS-002', 'priya', '123', '123', 1, 1, 1, '1,2,3,4,5,6,7,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32', 0, '1', '21', '2026-06-03', '2026-06-05'),
(22, 'US-006', 1, 1, 4, 'FTS-002', 'pr', '123', '123', 1, 1, 1, '1,2,3,4,5,6,7,11,12,13,14,19,20', 1, '21', NULL, '2026-06-04', NULL),
(23, 'US-007', 1, 2, 10, 'FTS-007', 'maya', '123', '123', 1, 2, 1, '1,15,20', 0, '19', NULL, '2026-06-11', NULL),
(24, 'US-008', 1, 2, 5, 'FTS-003', '1222', '123', '123', 1, 1, 1, '1,2,3,12', 0, '21', '21', '2026-06-12', '2026-06-12');

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
-- Indexes for table `feedback_department_mapping`
--
ALTER TABLE `feedback_department_mapping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback_questions_mapping`
--
ALTER TABLE `feedback_questions_mapping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback_titles`
--
ALTER TABLE `feedback_titles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `general_feedback`
--
ALTER TABLE `general_feedback`
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
-- Indexes for table `poll_answers`
--
ALTER TABLE `poll_answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `poll_department_mapping`
--
ALTER TABLE `poll_department_mapping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `poll_options_mapping`
--
ALTER TABLE `poll_options_mapping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `poll_titles`
--
ALTER TABLE `poll_titles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qualification_info`
--
ALTER TABLE `qualification_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rating_answers`
--
ALTER TABLE `rating_answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rating_department_mapping`
--
ALTER TABLE `rating_department_mapping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rating_titles`
--
ALTER TABLE `rating_titles`
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
-- Indexes for table `staff_general_feedback`
--
ALTER TABLE `staff_general_feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_sch_feedback`
--
ALTER TABLE `staff_sch_feedback`
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
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch_creation`
--
ALTER TABLE `branch_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_creation`
--
ALTER TABLE `company_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_department_mapping`
--
ALTER TABLE `company_department_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_designation_mapping`
--
ALTER TABLE `company_designation_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_policies`
--
ALTER TABLE `company_policies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_weekoffs`
--
ALTER TABLE `company_weekoffs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ctc_creation`
--
ALTER TABLE `ctc_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department_creation`
--
ALTER TABLE `department_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `designation_creation`
--
ALTER TABLE `designation_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `document_info`
--
ALTER TABLE `document_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `experience_info`
--
ALTER TABLE `experience_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `family_info`
--
ALTER TABLE `family_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback_department_mapping`
--
ALTER TABLE `feedback_department_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback_questions_mapping`
--
ALTER TABLE `feedback_questions_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback_titles`
--
ALTER TABLE `feedback_titles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `general_feedback`
--
ALTER TABLE `general_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `holiday_creation`
--
ALTER TABLE `holiday_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `home_upload`
--
ALTER TABLE `home_upload`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_creation`
--
ALTER TABLE `leave_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `license_limits`
--
ALTER TABLE `license_limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `location_access_mapping`
--
ALTER TABLE `location_access_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_list`
--
ALTER TABLE `menu_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `occupation_info`
--
ALTER TABLE `occupation_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `poll_answers`
--
ALTER TABLE `poll_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `poll_department_mapping`
--
ALTER TABLE `poll_department_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `poll_options_mapping`
--
ALTER TABLE `poll_options_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `poll_titles`
--
ALTER TABLE `poll_titles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `qualification_info`
--
ALTER TABLE `qualification_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rating_answers`
--
ALTER TABLE `rating_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rating_department_mapping`
--
ALTER TABLE `rating_department_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rating_titles`
--
ALTER TABLE `rating_titles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `regularization`
--
ALTER TABLE `regularization`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shift_creation`
--
ALTER TABLE `shift_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_creation`
--
ALTER TABLE `staff_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_ctc_info`
--
ALTER TABLE `staff_ctc_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_general_feedback`
--
ALTER TABLE `staff_general_feedback`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_sch_feedback`
--
ALTER TABLE `staff_sch_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `statutory_compliance`
--
ALTER TABLE `statutory_compliance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_menu_list`
--
ALTER TABLE `sub_menu_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `team_creation`
--
ALTER TABLE `team_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `team_creation_mapping`
--
ALTER TABLE `team_creation_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `team_name_creation`
--
ALTER TABLE `team_name_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2025 at 06:22 AM
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
-- Database: `finance`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts_collect_entry`
--

CREATE TABLE `accounts_collect_entry` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `line` varchar(50) NOT NULL,
  `branch` varchar(50) NOT NULL,
  `coll_mode` int(11) NOT NULL,
  `bank_id` varchar(50) DEFAULT NULL,
  `no_of_bills` int(11) NOT NULL,
  `collection_amnt` varchar(150) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `created_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `agent_creation`
--

CREATE TABLE `agent_creation` (
  `id` int(11) NOT NULL,
  `agent_code` varchar(100) NOT NULL,
  `agent_name` varchar(100) NOT NULL,
  `mobile1` varchar(100) NOT NULL,
  `mobile2` varchar(100) NOT NULL,
  `area` varchar(100) NOT NULL,
  `occupation` varchar(100) NOT NULL,
  `insert_login_id` varchar(100) NOT NULL,
  `update_login_id` varchar(100) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `area_creation`
--

CREATE TABLE `area_creation` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `line_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` datetime NOT NULL DEFAULT current_timestamp(),
  `update_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `area_creation_area_name`
--

CREATE TABLE `area_creation_area_name` (
  `id` int(11) NOT NULL,
  `area_creation_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `area_name_creation`
--

CREATE TABLE `area_name_creation` (
  `id` int(11) NOT NULL,
  `areaname` varchar(200) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_clearance`
--

CREATE TABLE `bank_clearance` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `bank_id` varchar(255) DEFAULT NULL,
  `trans_date` date DEFAULT NULL,
  `narration` varchar(255) NOT NULL,
  `trans_id` varchar(255) DEFAULT NULL,
  `credit` varchar(255) DEFAULT NULL,
  `debit` varchar(255) DEFAULT NULL,
  `balance` varchar(255) DEFAULT NULL,
  `clr_status` varchar(10) NOT NULL DEFAULT '0' COMMENT '0 - unclear,1-cleared',
  `insert_login_id` varchar(255) DEFAULT NULL,
  `update_login_id` varchar(255) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_creation`
--

CREATE TABLE `bank_creation` (
  `id` int(11) NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `bank_short_name` varchar(100) NOT NULL,
  `account_number` varchar(100) NOT NULL,
  `ifsc_code` varchar(100) NOT NULL,
  `branch_name` varchar(100) NOT NULL,
  `qr_code` varchar(100) NOT NULL,
  `gpay` varchar(100) NOT NULL,
  `under_branch` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1',
  `insert_login_id` varchar(100) NOT NULL,
  `update_login_id` varchar(100) DEFAULT NULL,
  `delete_login_id` varchar(100) DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_info`
--

CREATE TABLE `bank_info` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(100) NOT NULL,
  `aadhar_num` varchar(250) DEFAULT NULL,
  `cus_profile_id` varchar(255) NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `branch_name` varchar(100) NOT NULL,
  `acc_holder_name` varchar(100) NOT NULL,
  `acc_number` varchar(100) NOT NULL,
  `ifsc_code` varchar(100) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branch_creation`
--

CREATE TABLE `branch_creation` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `branch_name` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `state` int(11) NOT NULL,
  `district` int(11) NOT NULL,
  `taluk` int(11) NOT NULL,
  `place` varchar(100) NOT NULL,
  `pincode` varchar(100) NOT NULL,
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
-- Table structure for table `cash_tally_modes`
--

CREATE TABLE `cash_tally_modes` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `modes` varchar(255) DEFAULT NULL,
  `bankcredit` varchar(10) NOT NULL DEFAULT '1',
  `bankdebit` varchar(10) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `cash_tally_modes`
--

INSERT INTO `cash_tally_modes` (`id`, `modes`, `bankcredit`, `bankdebit`) VALUES
(1, 'Collection', '0', '1'),
(2, 'Bank Withdrawal', '1', '1'),
(3, 'Other Income', '0', '1'),
(4, 'Exchange', '0', '0'),
(5, 'Bank Deposit', '1', '1'),
(6, 'Investment', '0', '0'),
(7, 'Deposit', '0', '0'),
(8, 'EL', '0', '0'),
(9, 'Expenses', '1', '0');

-- --------------------------------------------------------

--
-- Table structure for table `cheque_info`
--

CREATE TABLE `cheque_info` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(100) NOT NULL,
  `cus_profile_id` int(11) NOT NULL,
  `holder_type` int(11) NOT NULL,
  `holder_name` varchar(150) NOT NULL,
  `holder_id` varchar(25) DEFAULT NULL,
  `relationship` varchar(50) NOT NULL,
  `bank_name` varchar(150) NOT NULL,
  `cheque_cnt` int(11) NOT NULL,
  `upload` varchar(255) DEFAULT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cheque_no_list`
--

CREATE TABLE `cheque_no_list` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(250) DEFAULT NULL,
  `cus_profile_id` int(11) DEFAULT NULL,
  `cheque_info_id` int(11) DEFAULT NULL,
  `cheque_no` varchar(200) DEFAULT NULL,
  `used_status` int(11) NOT NULL DEFAULT 0,
  `noc_status` int(11) NOT NULL DEFAULT 0,
  `date_of_noc` date DEFAULT NULL,
  `noc_member` varchar(150) DEFAULT NULL,
  `noc_relationship` varchar(150) DEFAULT NULL,
  `insert_login_id` int(11) DEFAULT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cheque_upd`
--

CREATE TABLE `cheque_upd` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(250) DEFAULT NULL,
  `cus_profile_id` int(11) DEFAULT NULL,
  `cheque_info_id` int(11) DEFAULT NULL,
  `uploads` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `collection`
--

CREATE TABLE `collection` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `coll_code` varchar(255) DEFAULT NULL,
  `cus_profile_id` int(11) DEFAULT NULL,
  `cus_id` varchar(255) DEFAULT NULL,
  `cus_name` varchar(255) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `line` varchar(255) DEFAULT NULL,
  `loan_category` varchar(255) DEFAULT NULL,
  `coll_status` varchar(255) DEFAULT NULL,
  `coll_sub_status` varchar(255) DEFAULT NULL,
  `tot_amt` varchar(255) DEFAULT NULL,
  `paid_amt` varchar(255) DEFAULT NULL,
  `bal_amt` varchar(255) DEFAULT NULL,
  `due_amt` varchar(255) DEFAULT NULL,
  `pending_amt` varchar(255) DEFAULT NULL,
  `payable_amt` varchar(255) DEFAULT NULL,
  `till_date_int` varchar(100) DEFAULT NULL,
  `penalty` varchar(255) DEFAULT NULL,
  `coll_charge` varchar(255) DEFAULT NULL,
  `coll_mode` varchar(255) DEFAULT NULL,
  `bank_id` varchar(10) NOT NULL,
  `cheque_no` varchar(255) DEFAULT NULL,
  `trans_id` varchar(255) DEFAULT NULL,
  `trans_date` date DEFAULT NULL,
  `coll_date` datetime DEFAULT current_timestamp(),
  `due_amt_track` varchar(255) NOT NULL DEFAULT '0',
  `princ_amt_track` varchar(255) DEFAULT '0',
  `int_amt_track` varchar(255) DEFAULT '0',
  `penalty_track` varchar(255) NOT NULL DEFAULT '0',
  `coll_charge_track` varchar(255) NOT NULL DEFAULT '0',
  `total_paid_track` varchar(255) NOT NULL DEFAULT '0',
  `pre_close_waiver` varchar(255) NOT NULL DEFAULT '0',
  `principal_waiver` varchar(100) DEFAULT NULL,
  `interest_waiver` varchar(100) DEFAULT NULL,
  `penalty_waiver` varchar(255) NOT NULL DEFAULT '0',
  `coll_charge_waiver` varchar(255) NOT NULL DEFAULT '0',
  `total_waiver` varchar(255) NOT NULL DEFAULT '0',
  `collect_sts` int(11) NOT NULL DEFAULT 0,
  `insert_login_id` varchar(255) DEFAULT NULL,
  `update_login_id` varchar(255) DEFAULT NULL,
  `delete_login_id` varchar(255) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL COMMENT 'Create Time',
  `updated_date` datetime DEFAULT current_timestamp() COMMENT 'Update Time'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `collection_charges`
--

CREATE TABLE `collection_charges` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `cus_profile_id` int(11) DEFAULT NULL,
  `cus_id` varchar(255) DEFAULT NULL,
  `coll_date` varchar(255) DEFAULT NULL,
  `coll_purpose` varchar(255) DEFAULT NULL,
  `coll_charge` varchar(255) NOT NULL DEFAULT '0',
  `paid_date` varchar(255) DEFAULT NULL,
  `paid_amnt` varchar(255) DEFAULT '0',
  `waiver_amnt` varchar(255) DEFAULT '0',
  `status` int(11) DEFAULT NULL,
  `insert_login_id` varchar(255) DEFAULT NULL,
  `update_login_id` varchar(255) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL COMMENT 'Create Time',
  `updated_date` datetime DEFAULT current_timestamp() COMMENT 'Update Time'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_creation`
--

CREATE TABLE `company_creation` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `district` int(11) DEFAULT NULL,
  `taluk` int(11) DEFAULT NULL,
  `place` varchar(255) DEFAULT NULL,
  `pincode` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `mailid` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `landline_code` varchar(100) DEFAULT NULL,
  `landline` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `insert_user_id` int(11) DEFAULT NULL,
  `update_user_id` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_data`
--

CREATE TABLE `customer_data` (
  `id` int(11) NOT NULL,
  `cus_name` varchar(100) NOT NULL,
  `area` varchar(100) NOT NULL,
  `mobile` varchar(100) NOT NULL,
  `loan_cat` varchar(100) NOT NULL,
  `loan_amount` varchar(100) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_profile`
--

CREATE TABLE `customer_profile` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(250) DEFAULT NULL,
  `aadhar_num` varchar(100) NOT NULL,
  `cus_name` varchar(100) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `dob` varchar(50) DEFAULT NULL,
  `age` varchar(100) DEFAULT NULL,
  `mobile1` varchar(100) NOT NULL,
  `mobile2` varchar(100) DEFAULT NULL,
  `whatsapp_no` varchar(50) DEFAULT NULL,
  `pic` varchar(100) NOT NULL,
  `guarantor_name` varchar(100) DEFAULT NULL,
  `gu_pic` varchar(100) DEFAULT NULL,
  `cus_data` varchar(100) NOT NULL,
  `cus_status` varchar(100) NOT NULL,
  `res_type` varchar(100) DEFAULT NULL,
  `res_detail` varchar(100) DEFAULT NULL,
  `res_address` varchar(100) DEFAULT NULL,
  `native_address` varchar(100) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `occ_detail` varchar(100) DEFAULT NULL,
  `occ_income` varchar(100) DEFAULT NULL,
  `occ_address` varchar(100) DEFAULT NULL,
  `area_confirm` varchar(100) DEFAULT NULL,
  `area` int(11) DEFAULT NULL,
  `line` varchar(100) DEFAULT NULL,
  `cus_limit` varchar(100) DEFAULT NULL,
  `about_cus` varchar(100) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_status`
--

CREATE TABLE `customer_status` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(100) NOT NULL,
  `cus_profile_id` int(11) NOT NULL,
  `loan_calculation_id` int(11) DEFAULT NULL,
  `coll_status` varchar(250) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `sub_status` int(11) DEFAULT NULL,
  `in_closed_date` date DEFAULT NULL,
  `closed_date` date DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date NOT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `designation`
--

CREATE TABLE `designation` (
  `id` int(11) NOT NULL,
  `designation` varchar(150) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
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
(39, 2, 'Puducherry', 1),
(41, 3, 'Chittoor', 1);

-- --------------------------------------------------------

--
-- Table structure for table `document_info`
--

CREATE TABLE `document_info` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(100) NOT NULL,
  `cus_profile_id` int(11) NOT NULL,
  `doc_name` varchar(150) NOT NULL,
  `doc_type` int(11) NOT NULL,
  `holder_name` int(11) NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `upload` varchar(100) NOT NULL,
  `noc_status` int(11) NOT NULL DEFAULT 0,
  `date_of_noc` date DEFAULT NULL,
  `noc_member` varchar(150) DEFAULT NULL,
  `noc_relationship` varchar(150) DEFAULT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_need`
--

CREATE TABLE `document_need` (
  `id` int(11) NOT NULL,
  `cus_profile_id` int(11) NOT NULL,
  `cus_id` varchar(100) NOT NULL,
  `document_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `endorsement_info`
--

CREATE TABLE `endorsement_info` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(100) NOT NULL,
  `cus_profile_id` int(11) NOT NULL,
  `owner_name` int(11) NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `vehicle_details` varchar(255) NOT NULL,
  `endorsement_name` varchar(250) NOT NULL,
  `key_original` varchar(50) NOT NULL,
  `rc_original` varchar(50) NOT NULL,
  `upload` varchar(255) NOT NULL,
  `noc_status` int(11) NOT NULL DEFAULT 0,
  `date_of_noc` date DEFAULT NULL,
  `noc_member` varchar(150) DEFAULT NULL,
  `noc_relationship` varchar(150) DEFAULT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `existing_customer`
--

CREATE TABLE `existing_customer` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(100) NOT NULL,
  `cus_profile_id` int(11) NOT NULL,
  `cus_name` varchar(100) NOT NULL,
  `area` varchar(100) NOT NULL,
  `mobile1` varchar(100) NOT NULL,
  `linename` varchar(100) NOT NULL,
  `branch_name` varchar(100) NOT NULL,
  `c_sts` varchar(100) NOT NULL,
  `c_substs` varchar(100) NOT NULL,
  `existing_detail` varchar(250) DEFAULT NULL,
  `insert_login_id` int(11) NOT NULL,
  `created_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `coll_mode` int(11) NOT NULL,
  `bank_id` varchar(11) DEFAULT NULL,
  `invoice_id` varchar(100) NOT NULL,
  `branch` int(11) NOT NULL,
  `expenses_category` varchar(50) NOT NULL,
  `agent_id` varchar(50) DEFAULT NULL,
  `total_issued` varchar(50) DEFAULT NULL,
  `total_amount` varchar(100) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `amount` varchar(150) NOT NULL,
  `trans_id` varchar(150) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `created_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `family_info`
--

CREATE TABLE `family_info` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(100) NOT NULL,
  `fam_name` varchar(100) NOT NULL,
  `fam_relationship` varchar(100) NOT NULL,
  `remarks` varchar(250) DEFAULT NULL,
  `fam_age` varchar(100) DEFAULT NULL,
  `fam_live` varchar(100) NOT NULL,
  `fam_occupation` varchar(100) DEFAULT NULL,
  `fam_aadhar` varchar(100) NOT NULL,
  `fam_mobile` varchar(100) NOT NULL,
  `other_details` varchar(255) DEFAULT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gold_info`
--

CREATE TABLE `gold_info` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(100) NOT NULL,
  `cus_profile_id` int(11) NOT NULL,
  `gold_type` varchar(150) NOT NULL,
  `purity` varchar(150) NOT NULL,
  `weight` varchar(150) NOT NULL,
  `value` varchar(150) NOT NULL,
  `noc_status` int(11) NOT NULL DEFAULT 0,
  `date_of_noc` date DEFAULT NULL,
  `noc_member` varchar(150) DEFAULT NULL,
  `noc_relationship` varchar(150) DEFAULT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guarantor_info`
--

CREATE TABLE `guarantor_info` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(255) DEFAULT NULL,
  `relationship_type` varchar(250) DEFAULT NULL,
  `guarantor_name` varchar(255) DEFAULT NULL,
  `family_id` varchar(100) DEFAULT NULL,
  `other_mem_id` int(11) DEFAULT NULL,
  `guarantor_relationship` varchar(250) DEFAULT NULL,
  `details` varchar(100) DEFAULT NULL,
  `gu_pic` varchar(100) DEFAULT NULL,
  `insert_login_id` int(11) DEFAULT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `updated_on` date DEFAULT NULL,
  `created_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kyc_info`
--

CREATE TABLE `kyc_info` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(100) NOT NULL,
  `cus_profile_id` varchar(255) NOT NULL,
  `proof_of` varchar(100) NOT NULL,
  `fam_mem` int(11) DEFAULT NULL,
  `proof` int(11) NOT NULL,
  `proof_detail` varchar(100) NOT NULL,
  `upload` varchar(100) DEFAULT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `line_name_creation`
--

CREATE TABLE `line_name_creation` (
  `id` int(11) NOT NULL,
  `linename` varchar(150) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_category`
--

CREATE TABLE `loan_category` (
  `id` int(11) NOT NULL,
  `loan_category` varchar(150) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_category_creation`
--

CREATE TABLE `loan_category_creation` (
  `id` int(11) NOT NULL,
  `loan_category` int(11) NOT NULL,
  `loan_limit` varchar(100) NOT NULL,
  `due_method` varchar(50) NOT NULL,
  `due_type` varchar(50) NOT NULL,
  `interest_calculate` varchar(11) DEFAULT NULL,
  `interest_rate_min` varchar(50) DEFAULT NULL,
  `interest_rate_max` varchar(50) DEFAULT NULL,
  `due_period_min` varchar(50) DEFAULT NULL,
  `due_period_max` varchar(50) DEFAULT NULL,
  `document_charge_type` varchar(100) DEFAULT NULL,
  `doc_charge_min` varchar(50) DEFAULT NULL,
  `doc_charge_max` varchar(50) DEFAULT NULL,
  `processing_fees_type` varchar(100) DEFAULT NULL,
  `processing_fee_min` varchar(50) DEFAULT NULL,
  `processing_fee_max` varchar(100) DEFAULT NULL,
  `penalty_type` varchar(100) DEFAULT NULL,
  `overdue_penalty` varchar(100) DEFAULT NULL,
  `scheme_name` varchar(150) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_entry_loan_calculation`
--

CREATE TABLE `loan_entry_loan_calculation` (
  `id` int(11) NOT NULL,
  `cus_profile_id` int(11) NOT NULL,
  `cus_id` varchar(100) NOT NULL,
  `loan_id` varchar(50) NOT NULL,
  `loan_category` varchar(50) NOT NULL,
  `category_info` varchar(255) DEFAULT NULL,
  `loan_amount` int(11) NOT NULL,
  `profit_type` int(11) NOT NULL,
  `due_method` varchar(50) DEFAULT NULL,
  `due_type` varchar(50) DEFAULT NULL,
  `interest_calculate` varchar(100) DEFAULT NULL,
  `profit_method` varchar(50) DEFAULT NULL,
  `scheme_due_method` varchar(50) DEFAULT NULL,
  `scheme_day` varchar(50) DEFAULT NULL,
  `scheme_name` varchar(100) DEFAULT NULL,
  `interest_rate` int(11) NOT NULL,
  `due_period` int(11) NOT NULL,
  `doc_charge` int(11) NOT NULL,
  `processing_fees` int(11) NOT NULL,
  `loan_amnt` int(11) NOT NULL,
  `principal_amnt` int(11) NOT NULL,
  `interest_amnt` int(11) NOT NULL,
  `total_amnt` varchar(11) DEFAULT NULL,
  `due_amnt` varchar(11) DEFAULT NULL,
  `doc_charge_calculate` int(11) NOT NULL,
  `processing_fees_calculate` int(11) NOT NULL,
  `net_cash` int(11) NOT NULL,
  `loan_date` date NOT NULL,
  `due_startdate` date NOT NULL,
  `maturity_date` date NOT NULL,
  `referred` int(11) NOT NULL,
  `agent_id` varchar(100) DEFAULT NULL,
  `agent_name` varchar(150) DEFAULT NULL,
  `cus_status` int(11) NOT NULL DEFAULT 0,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_issue`
--

CREATE TABLE `loan_issue` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(255) NOT NULL,
  `cus_profile_id` int(11) NOT NULL,
  `loan_amnt` int(11) NOT NULL,
  `net_cash` int(11) NOT NULL,
  `net_bal_cash` varchar(100) NOT NULL,
  `payment_type` int(11) NOT NULL,
  `payment_mode` varchar(11) DEFAULT NULL,
  `bank_name` varchar(11) DEFAULT NULL,
  `cash` varchar(100) DEFAULT NULL,
  `cheque_val` varchar(100) DEFAULT NULL,
  `transaction_val` varchar(100) DEFAULT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `cheque_no` varchar(50) DEFAULT NULL,
  `cheque_remark` varchar(100) DEFAULT NULL,
  `tran_remark` varchar(100) DEFAULT NULL,
  `balance_amount` varchar(100) DEFAULT NULL,
  `issue_date` date NOT NULL,
  `issue_person` varchar(50) NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL
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
(1, 'Dashboard', 'dashboard', 'developer_board'),
(2, 'Master', 'master', 'camera1'),
(3, 'Administration', 'admin', 'layers'),
(4, 'Loan Entry', 'loan_entry', 'archive'),
(5, 'Approval', 'approval', 'user-check'),
(6, 'Loan Issue', 'loan_issue', 'wallet'),
(7, 'Collection', 'collection', 'credit'),
(8, 'Closed', 'closed', 'uninstall'),
(9, 'NOC', 'noc', 'export'),
(10, 'Accounts', 'accounts', 'domain'),
(11, 'Update', 'update', 'share1'),
(12, 'Customer Data', 'customer_data', 'folder_shared'),
(13, 'Search', 'search', 'magnifying-glass'),
(14, 'Reports - EMI', 'reports', 'assignment_turned_in'),
(15, 'Reports - Interest', 'reports_interest', 'assignment_turned_in'),
(16, 'Bulk Upload', 'bulk_upload', 'cloud_upload');

-- --------------------------------------------------------

--
-- Table structure for table `mortgage_info`
--

CREATE TABLE `mortgage_info` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(100) NOT NULL,
  `cus_profile_id` int(11) NOT NULL,
  `property_holder_name` int(11) NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `property_details` varchar(255) NOT NULL,
  `mortgage_name` varchar(100) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `mortgage_number` varchar(100) NOT NULL,
  `reg_office` varchar(100) NOT NULL,
  `mortgage_value` varchar(100) NOT NULL,
  `upload` varchar(100) NOT NULL,
  `noc_status` int(11) NOT NULL DEFAULT 0,
  `date_of_noc` date DEFAULT NULL,
  `noc_member` varchar(150) DEFAULT NULL,
  `noc_relationship` varchar(150) DEFAULT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `noc`
--

CREATE TABLE `noc` (
  `id` int(11) NOT NULL,
  `cus_profile_id` int(11) NOT NULL,
  `cus_id` varchar(100) NOT NULL,
  `cheque_list` int(11) NOT NULL DEFAULT 0,
  `mortgage_list` int(11) NOT NULL DEFAULT 0,
  `endorsement_list` int(11) NOT NULL DEFAULT 0,
  `document_list` int(11) NOT NULL DEFAULT 0,
  `gold_info` int(11) NOT NULL DEFAULT 0,
  `noc_status` int(11) NOT NULL DEFAULT 0,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `noc_ref`
--

CREATE TABLE `noc_ref` (
  `id` int(11) NOT NULL,
  `noc_id` int(11) NOT NULL,
  `date_of_noc` date NOT NULL,
  `noc_member` varchar(150) NOT NULL,
  `noc_relationship` varchar(150) NOT NULL,
  `created_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `other_transaction`
--

CREATE TABLE `other_transaction` (
  `id` int(11) NOT NULL,
  `coll_mode` int(11) NOT NULL,
  `bank_id` varchar(11) DEFAULT NULL,
  `trans_cat` int(11) NOT NULL,
  `name` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `ref_id` varchar(100) DEFAULT NULL,
  `trans_id` varchar(100) DEFAULT NULL,
  `user_name` varchar(11) DEFAULT NULL,
  `amount` varchar(150) NOT NULL,
  `remark` varchar(255) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `created_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `other_trans_name`
--

CREATE TABLE `other_trans_name` (
  `id` int(11) NOT NULL,
  `trans_cat` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `created_on` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penalty_charges`
--

CREATE TABLE `penalty_charges` (
  `cus_profile_id` varchar(255) DEFAULT NULL,
  `penalty_date` varchar(255) DEFAULT NULL,
  `penalty` varchar(255) DEFAULT NULL,
  `paid_date` varchar(255) DEFAULT NULL,
  `paid_amnt` varchar(255) DEFAULT '0',
  `waiver_amnt` varchar(255) DEFAULT '0',
  `created_date` datetime DEFAULT current_timestamp(),
  `updated_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proof_info`
--

CREATE TABLE `proof_info` (
  `id` int(11) NOT NULL,
  `addProof_name` varchar(100) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `property_info`
--

CREATE TABLE `property_info` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(100) NOT NULL,
  `aadhar_num` varchar(250) DEFAULT NULL,
  `cus_profile_id` varchar(255) NOT NULL,
  `property` varchar(100) NOT NULL,
  `property_detail` varchar(100) NOT NULL,
  `property_holder` int(11) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repromotion_customer`
--

CREATE TABLE `repromotion_customer` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(100) DEFAULT NULL,
  `cus_profile_id` int(11) NOT NULL,
  `cus_name` varchar(100) DEFAULT NULL,
  `mobile1` varchar(11) DEFAULT NULL,
  `area` varchar(50) DEFAULT NULL,
  `linename` varchar(50) DEFAULT NULL,
  `branch_name` varchar(50) DEFAULT NULL,
  `c_sts` int(11) DEFAULT NULL,
  `repromotion_detail` varchar(250) DEFAULT NULL,
  `insert_login_id` int(11) NOT NULL,
  `created_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `role` varchar(150) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scheme`
--

CREATE TABLE `scheme` (
  `id` int(11) NOT NULL,
  `scheme_name` varchar(150) NOT NULL,
  `due_method` varchar(50) NOT NULL,
  `profit_method` varchar(20) NOT NULL,
  `interest_rate_percent` varchar(10) NOT NULL,
  `due_period_percent` varchar(10) NOT NULL,
  `overdue_penalty_percent` varchar(10) NOT NULL,
  `doc_charge_type` varchar(10) NOT NULL,
  `doc_charge_min` varchar(10) NOT NULL,
  `doc_charge_max` varchar(10) NOT NULL,
  `processing_fee_type` varchar(10) NOT NULL,
  `processing_fee_min` varchar(10) NOT NULL,
  `processing_fee_max` varchar(10) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
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
(2, 'Puducherry', 1),
(3, 'Andhra Pradesh', 1);

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
(1, 1, 'Dashboard', 'dashboard', 'view_comfy'),
(2, 2, 'Company Creation', 'company_creation', 'domain'),
(3, 2, 'Branch Creation', 'branch_creation', 'add-to-list'),
(4, 2, 'Loan Category Creation', 'loan_category_creation', 'recent_actors'),
(5, 2, 'Area Creation', 'area_creation', 'location'),
(6, 3, 'Bank Creation', 'bank_creation', 'store_mall_directory'),
(7, 3, 'Agent Creation', 'agent_creation', 'person_add'),
(8, 3, 'User Creation', 'user_creation', 'group_add'),
(9, 4, 'Loan Entry', 'loan_entry', 'local_library'),
(10, 5, 'Approval', 'approval', 'offline_pin'),
(11, 6, 'Loan Issue', 'loan_issue', 'credit-card'),
(12, 7, 'Collection', 'collection', 'devices_other'),
(13, 8, 'Closed', 'closed', 'circle-with-cross'),
(14, 9, 'NOC', 'noc', 'book'),
(15, 10, 'Accounts', 'accounts', 'rate_review'),
(16, 10, 'Bank Clearance', 'bank_clearance', 'assignment'),
(17, 10, 'Balance Sheet', 'balance_sheet', 'colours'),
(18, 11, 'Update Customer', 'update_customer', 'cloud_upload'),
(19, 12, 'Customer Data', 'customer_data', 'person_pin'),
(20, 13, 'Search', 'search_screen', 'search'),
(21, 14, 'Loan Issue Report', 'loan_issue_report', 'area-graph'),
(22, 14, 'Collection Report', 'collection_report', 'event_note'),
(23, 14, 'Balance Report', 'balance_report', 'event_available'),
(24, 14, 'Closed Report', 'closed_report', 'erase'),
(25, 14, 'Ledger View Report', 'ledger_view_report', 'terrain'),
(26, 14, 'Other Transaction Report', 'other_transaction_report', 'area-graph'),
(27, 14, 'Cancel Revoke Report', 'cancel_revoke_report', 'event_note'),
(28, 14, 'Uncleared Report', 'uncleared_report', 'event_available'),
(29, 14, 'In Closed Report', 'in_closed_report', 'erase'),
(30, 14, 'Principal / Interest Report', 'principal_interest_report', 'terrain'),
(31, 14, 'Due List Report', 'due_list_report', 'area-graph'),
(32, 14, 'No Due Pay Report', 'no_due_pay_report', 'event_note'),
(33, 15, 'Interest Loan Issue Report', 'interest_loan_issue_report', 'area-graph'),
(34, 15, 'Interest Collection Report', 'interest_collection_report', 'event_note'),
(35, 15, 'Interest Balance Report', 'interest_balance_report', 'event_available'),
(36, 15, 'Interest Closed Report', 'interest_closed_report', 'erase'),
(37, 15, 'Interest Ledger View Report', 'interest_ledger_view_report', 'terrain'),
(38, 15, 'Interest Cancel Revoke Report', 'interest_cancel_revoke_report', 'area-graph'),
(39, 15, 'Interest In Closed Report', 'interest_in_closed_report', 'event_note'),
(40, 15, 'Interest Principal / Interest Report', 'interest_principal_interest_report', 'event_available'),
(41, 15, 'Interest Due List Report', 'interest_due_list_report', 'erase'),
(42, 15, 'Interest No Due Pay Report', 'interest_no_due_pay_report', 'terrain'),
(43, 16, 'Bulk Upload Report', 'bulk_upload', 'cloud_done');

-- --------------------------------------------------------

--
-- Table structure for table `taluks`
--

CREATE TABLE `taluks` (
  `id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `district_id` int(11) NOT NULL,
  `taluk_name` varchar(50) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `taluks`
--

INSERT INTO `taluks` (`id`, `state_id`, `district_id`, `taluk_name`, `status`) VALUES
(1, 1, 1, 'Ariyalur', 1),
(2, 1, 1, 'Andimadam', 1),
(3, 1, 1, 'Sendurai', 1),
(4, 1, 1, 'Udaiyarpalayam', 1),
(5, 1, 2, 'Alandur', 1),
(6, 1, 2, 'Ambattur', 1),
(7, 1, 2, 'Aminjikarai', 1),
(8, 1, 2, 'Ayanavaram', 1),
(9, 1, 2, 'Egmore', 1),
(10, 1, 2, 'Guindy', 1),
(11, 1, 2, 'Madhavaram', 1),
(12, 1, 2, 'Madhuravoyal', 1),
(13, 1, 2, 'Mambalam', 1),
(14, 1, 2, 'Mylapore', 1),
(15, 1, 2, 'Perambur', 1),
(16, 1, 2, 'Purasavakkam', 1),
(17, 1, 2, 'Sholinganallur', 1),
(18, 1, 2, 'Thiruvottriyur', 1),
(19, 1, 2, 'Tondiarpet', 1),
(20, 1, 2, 'Velacherry', 1),
(21, 1, 3, 'Chengalpattu', 1),
(22, 1, 3, 'Cheyyur', 1),
(23, 1, 3, 'Maduranthakam', 1),
(24, 1, 3, 'Pallavaram', 1),
(25, 1, 3, 'Tambaram', 1),
(26, 1, 3, 'Thirukalukundram', 1),
(27, 1, 3, 'Tiruporur', 1),
(28, 1, 3, 'Vandalur', 1),
(29, 1, 4, 'Aanaimalai', 1),
(30, 1, 4, 'Annur', 1),
(31, 1, 4, 'Coimbatore(North)', 1),
(32, 1, 4, 'Coimbatore(South)', 1),
(33, 1, 4, 'Kinathukadavu', 1),
(34, 1, 4, 'Madukarai', 1),
(35, 1, 4, 'Mettupalayam', 1),
(36, 1, 4, 'Perur', 1),
(37, 1, 4, 'Pollachi', 1),
(38, 1, 4, 'Sulur', 1),
(39, 1, 4, 'Valparai', 1),
(40, 1, 5, 'Cuddalore', 1),
(41, 1, 5, 'Bhuvanagiri', 1),
(42, 1, 5, 'Chidambaram', 1),
(43, 1, 5, 'Kattumannarkoil', 1),
(44, 1, 5, 'Kurinjipadi', 1),
(45, 1, 5, 'Panruti', 1),
(46, 1, 5, 'Srimushnam', 1),
(47, 1, 5, 'Thittakudi', 1),
(48, 1, 5, 'Veppur', 1),
(49, 1, 5, 'Virudhachalam', 1),
(50, 1, 6, 'Dharmapuri', 1),
(51, 1, 6, 'Harur', 1),
(52, 1, 6, 'Karimangalam', 1),
(53, 1, 6, 'Nallampalli', 1),
(54, 1, 6, 'Palacode', 1),
(55, 1, 6, 'Pappireddipatti', 1),
(56, 1, 6, 'Pennagaram', 1),
(57, 1, 7, 'Atthur', 1),
(58, 1, 7, 'Dindigul(East)', 1),
(59, 1, 7, 'Dindigul(West)', 1),
(60, 1, 7, 'Guziliyamparai', 1),
(61, 1, 7, 'Kodaikanal', 1),
(62, 1, 7, 'Natham', 1),
(63, 1, 7, 'Nilakottai', 1),
(64, 1, 7, 'Oddanchatram', 1),
(65, 1, 7, 'Palani', 1),
(66, 1, 7, 'Vedasandur', 1),
(67, 1, 8, 'Erode', 1),
(68, 1, 8, 'Anthiyur', 1),
(69, 1, 8, 'Bhavani', 1),
(70, 1, 8, 'Gobichettipalayam', 1),
(71, 1, 8, 'Kodumudi', 1),
(72, 1, 8, 'Modakurichi', 1),
(73, 1, 8, 'Nambiyur', 1),
(74, 1, 8, 'Perundurai', 1),
(75, 1, 8, 'Sathiyamangalam', 1),
(76, 1, 8, 'Thalavadi', 1),
(77, 1, 9, 'Kallakurichi', 1),
(78, 1, 9, 'Chinnaselam', 1),
(79, 1, 9, 'Kalvarayan Hills', 1),
(80, 1, 9, 'Sankarapuram', 1),
(81, 1, 9, 'Tirukoilur', 1),
(82, 1, 9, 'Ulundurpet', 1),
(83, 1, 10, 'Kancheepuram', 1),
(84, 1, 10, 'Kundrathur', 1),
(85, 1, 10, 'Sriperumbudur', 1),
(86, 1, 10, 'Uthiramerur', 1),
(87, 1, 10, 'Walajabad', 1),
(88, 1, 11, 'Agasteeswaram', 1),
(89, 1, 11, 'Kalkulam', 1),
(90, 1, 11, 'Killiyur', 1),
(91, 1, 11, 'Thiruvatar', 1),
(92, 1, 11, 'Thovalai', 1),
(93, 1, 11, 'Vilavankodu', 1),
(94, 1, 12, 'Karur', 1),
(95, 1, 12, 'Aravakurichi', 1),
(96, 1, 12, 'Kadavur', 1),
(97, 1, 12, 'Krishnarayapuram', 1),
(98, 1, 12, 'Kulithalai', 1),
(99, 1, 12, 'Manmangalam', 1),
(100, 1, 12, 'Pugalur', 1),
(101, 1, 13, 'Krishnagiri', 1),
(102, 1, 13, 'Anjetty', 1),
(103, 1, 13, 'Bargur', 1),
(104, 1, 13, 'Hosur', 1),
(105, 1, 13, 'Pochampalli', 1),
(106, 1, 13, 'Sulagiri', 1),
(107, 1, 13, 'Thenkanikottai', 1),
(108, 1, 13, 'Uthangarai', 1),
(109, 1, 14, 'Kallikudi', 1),
(110, 1, 14, 'Madurai (East)', 1),
(111, 1, 14, 'Madurai (North)', 1),
(112, 1, 14, 'Madurai (South)', 1),
(113, 1, 14, 'Madurai (West)', 1),
(114, 1, 14, 'Melur', 1),
(115, 1, 14, 'Peraiyur', 1),
(116, 1, 14, 'Thirumangalam', 1),
(117, 1, 14, 'Thiruparankundram', 1),
(118, 1, 14, 'Usilampatti', 1),
(119, 1, 14, 'Vadipatti', 1),
(120, 1, 15, 'Mayiladuthurai', 1),
(121, 1, 15, 'Kuthalam', 1),
(122, 1, 15, 'Sirkali', 1),
(123, 1, 15, 'Tharangambadi', 1),
(124, 1, 16, 'Nagapattinam', 1),
(125, 1, 16, 'Kilvelur', 1),
(126, 1, 16, 'Thirukkuvalai', 1),
(127, 1, 16, 'Vedaranyam', 1),
(128, 1, 17, 'Namakkal', 1),
(129, 1, 17, 'Kholli Hills', 1),
(130, 1, 17, 'Kumarapalayam', 1),
(131, 1, 17, 'Mohanoor', 1),
(132, 1, 17, 'Paramathi Velur', 1),
(133, 1, 17, 'Rasipuram', 1),
(134, 1, 17, 'Senthamangalam', 1),
(135, 1, 17, 'Tiruchengode', 1),
(136, 1, 18, 'Udagamandalam', 1),
(137, 1, 18, 'Coonoor', 1),
(138, 1, 18, 'Gudalur', 1),
(139, 1, 18, 'Kothagiri', 1),
(140, 1, 18, 'Kundah', 1),
(141, 1, 18, 'Pandalur', 1),
(142, 1, 19, 'Perambalur', 1),
(143, 1, 19, 'Alathur', 1),
(144, 1, 19, 'Kunnam', 1),
(145, 1, 19, 'Veppanthattai', 1),
(146, 1, 20, 'Pudukottai', 1),
(147, 1, 20, 'Alangudi', 1),
(148, 1, 20, 'Aranthangi', 1),
(149, 1, 20, 'Avudiyarkoil', 1),
(150, 1, 20, 'Gandarvakottai', 1),
(151, 1, 20, 'Iluppur', 1),
(152, 1, 20, 'Karambakudi', 1),
(153, 1, 20, 'Kulathur', 1),
(154, 1, 20, 'Manamelkudi', 1),
(155, 1, 20, 'Ponnamaravathi', 1),
(156, 1, 20, 'Thirumayam', 1),
(157, 1, 20, 'Viralimalai', 1),
(158, 1, 21, 'Ramanathapuram', 1),
(159, 1, 21, 'Kadaladi', 1),
(160, 1, 21, 'Kamuthi', 1),
(161, 1, 21, 'Kezhakarai', 1),
(162, 1, 21, 'Mudukulathur', 1),
(163, 1, 21, 'Paramakudi', 1),
(164, 1, 21, 'Rajasingamangalam', 1),
(165, 1, 21, 'Rameswaram', 1),
(166, 1, 21, 'Tiruvadanai', 1),
(167, 1, 22, 'Arakkonam', 1),
(168, 1, 22, 'Arcot', 1),
(169, 1, 22, 'Kalavai', 1),
(170, 1, 22, 'Nemili', 1),
(171, 1, 22, 'Sholingur', 1),
(172, 1, 22, 'Walajah', 1),
(173, 1, 23, 'Salem', 1),
(174, 1, 23, 'Attur', 1),
(175, 1, 23, 'Edapadi', 1),
(176, 1, 23, 'Gangavalli', 1),
(177, 1, 23, 'Kadaiyampatti', 1),
(178, 1, 23, 'Mettur', 1),
(179, 1, 23, 'Omalur', 1),
(180, 1, 23, 'Pethanayakanpalayam', 1),
(181, 1, 23, 'Salem South', 1),
(182, 1, 23, 'Salem West', 1),
(183, 1, 23, 'Sankari', 1),
(184, 1, 23, 'Vazhapadi', 1),
(185, 1, 23, 'Yercaud', 1),
(186, 1, 24, 'Sivagangai', 1),
(187, 1, 24, 'Devakottai', 1),
(188, 1, 24, 'Ilayankudi', 1),
(189, 1, 24, 'Kalaiyarkovil', 1),
(190, 1, 24, 'Karaikudi', 1),
(191, 1, 24, 'Manamadurai', 1),
(192, 1, 24, 'Singampunari', 1),
(193, 1, 24, 'Thirupuvanam', 1),
(194, 1, 24, 'Tirupathur', 1),
(195, 1, 25, 'Tenkasi', 1),
(196, 1, 25, 'Alangulam', 1),
(197, 1, 25, 'Kadayanallur', 1),
(198, 1, 25, 'Sankarankovil', 1),
(199, 1, 25, 'Shenkottai', 1),
(200, 1, 25, 'Sivagiri', 1),
(201, 1, 25, 'Thiruvengadam', 1),
(202, 1, 25, 'Veerakeralampudur', 1),
(203, 1, 26, 'Thanjavur', 1),
(204, 1, 26, 'Boothalur', 1),
(205, 1, 26, 'Kumbakonam', 1),
(206, 1, 26, 'Orathanadu', 1),
(207, 1, 26, 'Papanasam', 1),
(208, 1, 26, 'Pattukottai', 1),
(209, 1, 26, 'Peravurani', 1),
(210, 1, 26, 'Thiruvaiyaru', 1),
(211, 1, 26, 'Thiruvidaimaruthur', 1),
(212, 1, 27, 'Theni', 1),
(213, 1, 27, 'Aandipatti', 1),
(214, 1, 27, 'Bodinayakanur', 1),
(215, 1, 27, 'Periyakulam', 1),
(216, 1, 27, 'Uthamapalayam', 1),
(217, 1, 28, 'Thoothukudi', 1),
(218, 1, 28, 'Eral', 1),
(219, 1, 28, 'Ettayapuram', 1),
(220, 1, 28, 'Kayathar', 1),
(221, 1, 28, 'Kovilpatti', 1),
(222, 1, 28, 'Ottapidaram', 1),
(223, 1, 28, 'Sattankulam', 1),
(224, 1, 28, 'Srivaikundam', 1),
(225, 1, 28, 'Tiruchendur', 1),
(226, 1, 28, 'Vilathikulam', 1),
(227, 1, 29, 'Lalgudi', 1),
(228, 1, 29, 'Manachanallur', 1),
(229, 1, 29, 'Manapparai', 1),
(230, 1, 29, 'Marungapuri', 1),
(231, 1, 29, 'Musiri', 1),
(232, 1, 29, 'Srirangam', 1),
(233, 1, 29, 'Thottiam', 1),
(234, 1, 29, 'Thuraiyur', 1),
(235, 1, 29, 'Tiruchirapalli (West)', 1),
(236, 1, 29, 'Tiruchirappalli (East)', 1),
(237, 1, 29, 'Tiruverumbur', 1),
(238, 1, 30, 'Tirunelveli', 1),
(239, 1, 30, 'Ambasamudram', 1),
(240, 1, 30, 'Cheranmahadevi', 1),
(241, 1, 30, 'Manur', 1),
(242, 1, 30, 'Nanguneri', 1),
(243, 1, 30, 'Palayamkottai', 1),
(244, 1, 30, 'Radhapuram', 1),
(245, 1, 30, 'Thisayanvilai', 1),
(246, 1, 31, 'Avinashi', 1),
(247, 1, 31, 'Dharapuram', 1),
(248, 1, 31, 'Kangeyam', 1),
(249, 1, 31, 'Madathukkulam', 1),
(250, 1, 31, 'Oothukuli', 1),
(251, 1, 31, 'Palladam', 1),
(252, 1, 31, 'Tiruppur (North)', 1),
(253, 1, 31, 'Tiruppur (South)', 1),
(254, 1, 31, 'Udumalaipettai', 1),
(255, 1, 32, 'Tirupathur\"', 1),
(256, 1, 32, 'Ambur', 1),
(257, 1, 32, 'Natrampalli', 1),
(258, 1, 32, 'Vaniyambadi', 1),
(259, 1, 33, 'Thiruvallur', 1),
(260, 1, 33, 'Avadi', 1),
(261, 1, 33, 'Gummidipoondi', 1),
(262, 1, 33, 'Pallipattu', 1),
(263, 1, 33, 'Ponneri', 1),
(264, 1, 33, 'Poonamallee', 1),
(265, 1, 33, 'R.K. Pet', 1),
(266, 1, 33, 'Tiruthani', 1),
(267, 1, 33, 'Uthukottai', 1),
(268, 1, 34, 'Thiruvannamalai', 1),
(269, 1, 34, 'Arni', 1),
(270, 1, 34, 'Chengam', 1),
(271, 1, 34, 'Chetpet', 1),
(272, 1, 34, 'Cheyyar', 1),
(273, 1, 34, 'Jamunamarathur', 1),
(274, 1, 34, 'Kalasapakkam', 1),
(275, 1, 34, 'Kilpennathur', 1),
(276, 1, 34, 'Polur', 1),
(277, 1, 34, 'Thandramet', 1),
(278, 1, 34, 'Vandavasi', 1),
(279, 1, 34, 'Vembakkam', 1),
(280, 1, 35, 'Thiruvarur', 1),
(281, 1, 35, 'Kodavasal', 1),
(282, 1, 35, 'Koothanallur', 1),
(283, 1, 35, 'Mannargudi', 1),
(284, 1, 35, 'Nannilam', 1),
(285, 1, 35, 'Needamangalam', 1),
(286, 1, 35, 'Thiruthuraipoondi', 1),
(287, 1, 35, 'Valangaiman', 1),
(288, 1, 36, 'Vellore', 1),
(289, 1, 36, 'Aanikattu', 1),
(290, 1, 36, 'Gudiyatham', 1),
(291, 1, 36, 'K V Kuppam', 1),
(292, 1, 36, 'Katpadi', 1),
(293, 1, 36, 'Pernambut', 1),
(294, 1, 37, 'Villupuram', 1),
(295, 1, 37, 'Gingee', 1),
(296, 1, 37, 'Kandachipuram', 1),
(297, 1, 37, 'Marakanam', 1),
(298, 1, 37, 'Melmalaiyanur', 1),
(299, 1, 37, 'Thiruvennainallur', 1),
(300, 1, 37, 'Tindivanam', 1),
(301, 1, 37, 'Vanur', 1),
(302, 1, 37, 'Vikravandi', 1),
(303, 1, 38, 'Virudhunagar', 1),
(304, 1, 38, 'Aruppukottai', 1),
(305, 1, 38, 'Kariyapatti', 1),
(306, 1, 38, 'Rajapalayam', 1),
(307, 1, 38, 'Sathur', 1),
(308, 1, 38, 'Sivakasi', 1),
(309, 1, 38, 'Srivilliputhur', 1),
(310, 1, 38, 'Tiruchuli', 1),
(311, 1, 38, 'Vembakottai', 1),
(312, 1, 38, 'Watrap', 1),
(313, 2, 39, 'Puducherry', 1),
(314, 2, 39, 'Oulgaret', 1),
(315, 2, 39, 'Villianur', 1),
(316, 2, 39, 'Bahour', 1),
(317, 2, 39, 'Karaikal', 1),
(318, 2, 39, 'Thirunallar', 1),
(319, 2, 39, 'Mahe', 1),
(320, 2, 39, 'Yanam', 1),
(321, 3, 41, 'Nagari', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `user_code` varchar(100) NOT NULL,
  `role` int(11) NOT NULL,
  `designation` int(11) NOT NULL,
  `address` varchar(100) NOT NULL,
  `place` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(100) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `branch` varchar(255) NOT NULL,
  `loan_category` varchar(255) NOT NULL,
  `line` varchar(255) NOT NULL,
  `collection_access` int(11) NOT NULL,
  `download_access` int(11) NOT NULL,
  `screens` varchar(255) NOT NULL,
  `insert_login_id` varchar(100) NOT NULL,
  `update_login_id` varchar(100) DEFAULT NULL,
  `created_on` date NOT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='All the users will be stored here with screen access details';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `user_code`, `role`, `designation`, `address`, `place`, `email`, `mobile`, `user_name`, `password`, `branch`, `loan_category`, `line`, `collection_access`, `download_access`, `screens`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(1, 'Super Admin', 'US-001', 1, 1, '', '', '', '', 'admin', '123', '1,2,3', '1,2,3,4,5', '3,1,2', 1, 1, '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42', '1', '1', '2024-06-13', '2025-11-18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts_collect_entry`
--
ALTER TABLE `accounts_collect_entry`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `agent_creation`
--
ALTER TABLE `agent_creation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `area_creation`
--
ALTER TABLE `area_creation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Line id` (`line_id`),
  ADD KEY `branch` (`branch_id`);

--
-- Indexes for table `area_creation_area_name`
--
ALTER TABLE `area_creation_area_name`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `area_name_creation`
--
ALTER TABLE `area_name_creation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branchid` (`branch_id`);

--
-- Indexes for table `bank_clearance`
--
ALTER TABLE `bank_clearance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bank_creation`
--
ALTER TABLE `bank_creation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bank_info`
--
ALTER TABLE `bank_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branch_creation`
--
ALTER TABLE `branch_creation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `state_id` (`state`),
  ADD KEY `district_id` (`district`),
  ADD KEY `taluk_id` (`taluk`);

--
-- Indexes for table `cash_tally_modes`
--
ALTER TABLE `cash_tally_modes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cheque_info`
--
ALTER TABLE `cheque_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cheque_no_list`
--
ALTER TABLE `cheque_no_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cheque_upd`
--
ALTER TABLE `cheque_upd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `collection`
--
ALTER TABLE `collection`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Profileid` (`cus_profile_id`);

--
-- Indexes for table `collection_charges`
--
ALTER TABLE `collection_charges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cusprofileid` (`cus_profile_id`);

--
-- Indexes for table `company_creation`
--
ALTER TABLE `company_creation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `State ids` (`state`),
  ADD KEY `District ids` (`district`),
  ADD KEY `Taluk ids` (`taluk`);

--
-- Indexes for table `customer_data`
--
ALTER TABLE `customer_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_profile`
--
ALTER TABLE `customer_profile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_status`
--
ALTER TABLE `customer_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customerProfileId` (`cus_profile_id`);

--
-- Indexes for table `designation`
--
ALTER TABLE `designation`
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
-- Indexes for table `document_need`
--
ALTER TABLE `document_need`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `endorsement_info`
--
ALTER TABLE `endorsement_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `existing_customer`
--
ALTER TABLE `existing_customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `family_info`
--
ALTER TABLE `family_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gold_info`
--
ALTER TABLE `gold_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guarantor_info`
--
ALTER TABLE `guarantor_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kyc_info`
--
ALTER TABLE `kyc_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proof` (`proof`),
  ADD KEY `fam-mem` (`fam_mem`);

--
-- Indexes for table `line_name_creation`
--
ALTER TABLE `line_name_creation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch id` (`branch_id`);

--
-- Indexes for table `loan_category`
--
ALTER TABLE `loan_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_category_creation`
--
ALTER TABLE `loan_category_creation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Loan Category` (`loan_category`);

--
-- Indexes for table `loan_entry_loan_calculation`
--
ALTER TABLE `loan_entry_loan_calculation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer profile id` (`cus_profile_id`);

--
-- Indexes for table `loan_issue`
--
ALTER TABLE `loan_issue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_list`
--
ALTER TABLE `menu_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mortgage_info`
--
ALTER TABLE `mortgage_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `noc`
--
ALTER TABLE `noc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `noc_ref`
--
ALTER TABLE `noc_ref`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `other_transaction`
--
ALTER TABLE `other_transaction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `other_trans_name`
--
ALTER TABLE `other_trans_name`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `proof_info`
--
ALTER TABLE `proof_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `property_info`
--
ALTER TABLE `property_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `repromotion_customer`
--
ALTER TABLE `repromotion_customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scheme`
--
ALTER TABLE `scheme`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sub_menu_list`
--
ALTER TABLE `sub_menu_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Main menu id` (`main_menu`);

--
-- Indexes for table `taluks`
--
ALTER TABLE `taluks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `District id` (`district_id`),
  ADD KEY `States id` (`state_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Role id` (`role`),
  ADD KEY `Designation id` (`designation`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts_collect_entry`
--
ALTER TABLE `accounts_collect_entry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `agent_creation`
--
ALTER TABLE `agent_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `area_creation`
--
ALTER TABLE `area_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `area_creation_area_name`
--
ALTER TABLE `area_creation_area_name`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `area_name_creation`
--
ALTER TABLE `area_name_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_clearance`
--
ALTER TABLE `bank_clearance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key';

--
-- AUTO_INCREMENT for table `bank_creation`
--
ALTER TABLE `bank_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_info`
--
ALTER TABLE `bank_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch_creation`
--
ALTER TABLE `branch_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cash_tally_modes`
--
ALTER TABLE `cash_tally_modes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `cheque_info`
--
ALTER TABLE `cheque_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cheque_no_list`
--
ALTER TABLE `cheque_no_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cheque_upd`
--
ALTER TABLE `cheque_upd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `collection`
--
ALTER TABLE `collection`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key';

--
-- AUTO_INCREMENT for table `collection_charges`
--
ALTER TABLE `collection_charges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key';

--
-- AUTO_INCREMENT for table `company_creation`
--
ALTER TABLE `company_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_data`
--
ALTER TABLE `customer_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_profile`
--
ALTER TABLE `customer_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_status`
--
ALTER TABLE `customer_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `designation`
--
ALTER TABLE `designation`
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
-- AUTO_INCREMENT for table `document_need`
--
ALTER TABLE `document_need`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `endorsement_info`
--
ALTER TABLE `endorsement_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `existing_customer`
--
ALTER TABLE `existing_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `family_info`
--
ALTER TABLE `family_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gold_info`
--
ALTER TABLE `gold_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guarantor_info`
--
ALTER TABLE `guarantor_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kyc_info`
--
ALTER TABLE `kyc_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `line_name_creation`
--
ALTER TABLE `line_name_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_category`
--
ALTER TABLE `loan_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_category_creation`
--
ALTER TABLE `loan_category_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_entry_loan_calculation`
--
ALTER TABLE `loan_entry_loan_calculation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_issue`
--
ALTER TABLE `loan_issue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_list`
--
ALTER TABLE `menu_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `mortgage_info`
--
ALTER TABLE `mortgage_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `noc`
--
ALTER TABLE `noc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `noc_ref`
--
ALTER TABLE `noc_ref`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `other_transaction`
--
ALTER TABLE `other_transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `other_trans_name`
--
ALTER TABLE `other_trans_name`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `proof_info`
--
ALTER TABLE `proof_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `property_info`
--
ALTER TABLE `property_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repromotion_customer`
--
ALTER TABLE `repromotion_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scheme`
--
ALTER TABLE `scheme`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sub_menu_list`
--
ALTER TABLE `sub_menu_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `taluks`
--
ALTER TABLE `taluks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=322;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `area_creation`
--
ALTER TABLE `area_creation`
  ADD CONSTRAINT `Line id` FOREIGN KEY (`line_id`) REFERENCES `line_name_creation` (`id`),
  ADD CONSTRAINT `branch` FOREIGN KEY (`branch_id`) REFERENCES `branch_creation` (`id`);

--
-- Constraints for table `area_name_creation`
--
ALTER TABLE `area_name_creation`
  ADD CONSTRAINT `branchid` FOREIGN KEY (`branch_id`) REFERENCES `branch_creation` (`id`);

--
-- Constraints for table `branch_creation`
--
ALTER TABLE `branch_creation`
  ADD CONSTRAINT `district_id` FOREIGN KEY (`district`) REFERENCES `districts` (`id`),
  ADD CONSTRAINT `state_id` FOREIGN KEY (`state`) REFERENCES `states` (`id`),
  ADD CONSTRAINT `taluk_id` FOREIGN KEY (`taluk`) REFERENCES `taluks` (`id`);

--
-- Constraints for table `collection`
--
ALTER TABLE `collection`
  ADD CONSTRAINT `Profileid` FOREIGN KEY (`cus_profile_id`) REFERENCES `customer_profile` (`id`);

--
-- Constraints for table `collection_charges`
--
ALTER TABLE `collection_charges`
  ADD CONSTRAINT `cusprofileid` FOREIGN KEY (`cus_profile_id`) REFERENCES `customer_profile` (`id`);

--
-- Constraints for table `company_creation`
--
ALTER TABLE `company_creation`
  ADD CONSTRAINT `District ids` FOREIGN KEY (`district`) REFERENCES `districts` (`id`),
  ADD CONSTRAINT `State ids` FOREIGN KEY (`state`) REFERENCES `states` (`id`),
  ADD CONSTRAINT `Taluk ids` FOREIGN KEY (`taluk`) REFERENCES `taluks` (`id`);

--
-- Constraints for table `customer_status`
--
ALTER TABLE `customer_status`
  ADD CONSTRAINT `customerProfileId` FOREIGN KEY (`cus_profile_id`) REFERENCES `customer_profile` (`id`);

--
-- Constraints for table `districts`
--
ALTER TABLE `districts`
  ADD CONSTRAINT `State id` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`);

--
-- Constraints for table `kyc_info`
--
ALTER TABLE `kyc_info`
  ADD CONSTRAINT `fam-mem` FOREIGN KEY (`fam_mem`) REFERENCES `family_info` (`id`),
  ADD CONSTRAINT `kyc_info_ibfk_1` FOREIGN KEY (`proof`) REFERENCES `proof_info` (`id`);

--
-- Constraints for table `line_name_creation`
--
ALTER TABLE `line_name_creation`
  ADD CONSTRAINT `branch id` FOREIGN KEY (`branch_id`) REFERENCES `branch_creation` (`id`);

--
-- Constraints for table `loan_category_creation`
--
ALTER TABLE `loan_category_creation`
  ADD CONSTRAINT `Loan Category` FOREIGN KEY (`loan_category`) REFERENCES `loan_category` (`id`);

--
-- Constraints for table `loan_entry_loan_calculation`
--
ALTER TABLE `loan_entry_loan_calculation`
  ADD CONSTRAINT `customer profile id` FOREIGN KEY (`cus_profile_id`) REFERENCES `customer_profile` (`id`);

--
-- Constraints for table `sub_menu_list`
--
ALTER TABLE `sub_menu_list`
  ADD CONSTRAINT `Main menu id` FOREIGN KEY (`main_menu`) REFERENCES `menu_list` (`id`);

--
-- Constraints for table `taluks`
--
ALTER TABLE `taluks`
  ADD CONSTRAINT `District id` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`),
  ADD CONSTRAINT `States id` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2025 at 01:12 PM
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
-- Database: `barangay_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL,
  `announcement_title` varchar(255) NOT NULL,
  `announcement_content` text NOT NULL,
  `announcement_category` enum('General','Emergency','Event','Health','Other') DEFAULT 'General',
  `announcement_location` varchar(255) DEFAULT NULL,
  `announcement_image` varchar(255) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `status` enum('Draft','Published','Archived') DEFAULT 'Published',
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `valid_until` timestamp NULL DEFAULT NULL,
  `priority` enum('Low','Normal','High','Urgent') DEFAULT 'Normal',
  `audience` enum('Public','Officials','Residents','Staff') DEFAULT 'Public',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `archived_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approvals`
--

CREATE TABLE `approvals` (
  `approval_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('Approved','Rejected') NOT NULL,
  `remarks` text DEFAULT NULL,
  `approved_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_requests`
--

CREATE TABLE `document_requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `document_name` varchar(100) NOT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `indigency_for` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Denied') NOT NULL DEFAULT 'Pending',
  `requested_at` datetime NOT NULL DEFAULT current_timestamp(),
  `processed_by` int(11) DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_requests`
--

INSERT INTO `document_requests` (`request_id`, `user_id`, `document_name`, `purpose`, `business_name`, `indigency_for`, `status`, `requested_at`, `processed_by`, `processed_at`, `remarks`, `attachment_path`, `is_deleted`) VALUES
(1, 1, 'Business Permit', 'Opening New Business', 'Shawarmahan ni Tito', NULL, 'Approved', '2025-09-29 10:00:46', 1, '2025-10-04 21:33:37', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `event_title` varchar(255) NOT NULL,
  `event_description` text NOT NULL,
  `event_start` datetime NOT NULL,
  `event_end` datetime NOT NULL,
  `event_location` varchar(255) NOT NULL,
  `event_type` enum('Community','Cultural','Health','Sports','Emergency','Other') DEFAULT 'Other',
  `event_image` varchar(255) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `audience` enum('Public','Residents','Officials','Staff','Admin') DEFAULT 'Public',
  `status` enum('Upcoming','Ongoing','Completed','Cancelled') DEFAULT 'Upcoming',
  `is_archived` tinyint(1) DEFAULT 0,
  `is_deleted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `incidents`
--

CREATE TABLE `incidents` (
  `incident_id` int(11) NOT NULL,
  `reporter_user_id` int(11) DEFAULT NULL,
  `reporter_non_resident_id` int(11) DEFAULT NULL,
  `category` enum('Incident','Emergency','Accident') NOT NULL,
  `type` enum('Theft','Vandalism','Physical Assault','Burglary','Robbery','Property Damage','Harassment','Disturbance','Trespassing','Other','Fire','Medical Emergency','Natural Disaster','Flood','Earthquake','Severe Weather','Gas Leak','Power Outage','Missing Person','Traffic Accident','Slip/Trip/Fall','Workplace Accident','Construction Accident','Drowning','Explosion','Electrical Accident','Animal Bite/Attack','Poisoning') NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `date_time` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `is_archived` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incidents`
--

INSERT INTO `incidents` (`incident_id`, `reporter_user_id`, `reporter_non_resident_id`, `category`, `type`, `description`, `location`, `photo`, `date_time`, `created_at`, `updated_at`, `is_archived`) VALUES
(1, NULL, 1, 'Incident', 'Vandalism', 'Someone vandalize the wall in the church', 'Church of Poblacion', '1760611481_back.png', '2025-10-16 18:42:00', '2025-10-16 10:44:41', NULL, 0),
(2, 1, NULL, 'Emergency', 'Flood', 'tryy incident', 'incident location', '1760878296_back.png', '2025-10-19 20:49:00', '2025-10-19 12:51:36', NULL, 0),
(3, 1, NULL, 'Emergency', 'Medical Emergency', 'addsdsadsadasd', 'Church of Poblacion', '1760905355_captured_photo.png', '2025-10-20 04:22:00', '2025-10-19 20:22:35', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `incident_persons`
--

CREATE TABLE `incident_persons` (
  `person_id` int(11) NOT NULL,
  `incident_id` int(11) NOT NULL,
  `person_type` enum('resident','non_resident') DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `non_resident_id` int(11) DEFAULT NULL,
  `role` enum('Victim','Witness','Suspect','Reporter','Respondent','Complainant','Other') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incident_persons`
--

INSERT INTO `incident_persons` (`person_id`, `incident_id`, `person_type`, `user_id`, `non_resident_id`, `role`, `created_at`, `updated_at`) VALUES
(1, 1, 'resident', 1, NULL, 'Witness', '2025-10-16 10:44:41', NULL),
(2, 1, 'non_resident', NULL, 2, 'Suspect', '2025-10-16 10:44:41', NULL),
(3, 2, 'resident', 11, NULL, 'Victim', '2025-10-19 12:51:36', NULL),
(4, 3, 'resident', 1, NULL, 'Witness', '2025-10-19 20:22:35', NULL),
(5, 3, 'non_resident', NULL, 3, 'Suspect', '2025-10-19 20:22:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `inquiries_id` int(11) NOT NULL,
  `inquiries_name` varchar(150) NOT NULL,
  `inquiries_email` varchar(150) NOT NULL,
  `inquiries_message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`inquiries_id`, `inquiries_name`, `inquiries_email`, `inquiries_message`, `created_at`) VALUES
(1, 'crizel', 'galvezcrizelvalenzuela13@gmail.com', 'HAHAHHA', '2025-10-14 12:15:26');

-- --------------------------------------------------------

--
-- Table structure for table `non_residents`
--

CREATE TABLE `non_residents` (
  `non_resident_id` int(11) NOT NULL,
  `f_name` varchar(50) NOT NULL,
  `m_name` varchar(50) DEFAULT NULL,
  `l_name` varchar(50) NOT NULL,
  `ext_name` varchar(10) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `non_residents`
--

INSERT INTO `non_residents` (`non_resident_id`, `f_name`, `m_name`, `l_name`, `ext_name`, `email`, `contact_no`, `address`, `created_at`) VALUES
(1, 'JONALYN ANN', 'VILLANUEVA', 'AGUBA', 'II', 'poblacionsur648+2@gmail.com', '9123456789', 'Pobsur', '2025-10-16 10:39:35'),
(2, 'crizel', 'valenzuela', 'galvez', '', 'crizelgalvez@gmail.com', '9444545664', 'bantug talavera nueva ecija', '2025-10-16 10:44:41'),
(3, 'asdadsd', 'asd', 'galvez', 'II', 'poblacionsur658+9@gmail.com', '9232132312', 'jan lang', '2025-10-19 20:22:35');

-- --------------------------------------------------------

--
-- Table structure for table `officials`
--

CREATE TABLE `officials` (
  `official_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `position` varchar(255) NOT NULL,
  `start_of_term` date NOT NULL,
  `end_of_term` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `officials`
--

INSERT INTO `officials` (`official_id`, `user_id`, `position`, `start_of_term`, `end_of_term`) VALUES
(2, 2, 'Barangay Captain', '2025-10-07', '2025-10-24');

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `otp_id` int(11) NOT NULL,
  `non_resident_id` int(11) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_verifications`
--

INSERT INTO `otp_verifications` (`otp_id`, `non_resident_id`, `otp_code`, `expires_at`, `verified`, `created_at`) VALUES
(43, 1, '767845', '2025-10-16 18:44:35', 1, '2025-10-16 10:39:35');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `password_reset_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `password_reset_token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_sent_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `reminder_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reminder_type` varchar(100) DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reminders`
--

INSERT INTO `reminders` (`reminder_id`, `user_id`, `reminder_type`, `sent_at`) VALUES
(1, 2, 'Complete Profile', '2025-10-05 02:24:02'),
(2, 2, 'Complete Profile', '2025-10-14 20:38:59'),
(3, 3, 'Complete Profile', '2025-10-17 17:52:26'),
(4, 11, 'Complete Profile', '2025-10-18 22:32:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Resident','Official') NOT NULL DEFAULT 'Resident',
  `is_alive` tinyint(1) DEFAULT 1,
  `status` enum('Pending','Approved','Rejected','Verified') DEFAULT 'Pending',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `archived_at` datetime DEFAULT NULL,
  `dead_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `role`, `is_alive`, `status`, `is_deleted`, `deleted_at`, `is_archived`, `created_at`, `updated_at`, `archived_at`, `dead_at`) VALUES
(1, 'poblacionsur648@gmail.com', '$2y$10$fldyE3o3aNfR2U7Q2WJ/aOT8yAzvTYN4wAGSPdPuzTuyTKWJ0G.4C', 'Admin', 1, 'Approved', 0, NULL, 0, '2025-09-27 09:50:22', '2025-10-05 01:38:10', NULL, NULL),
(2, 'poblacionsur648+1@gmail.com', '$2y$10$BbBDydw4tSFTZhuplhx47edM9avwEuixqUg86j0nZAftR9dhnBC3i', 'Official', 1, 'Approved', 0, NULL, 0, '2025-10-01 20:23:52', '2025-10-17 09:03:12', NULL, NULL),
(3, 'poblacionsur648+2@gmail.com', '$2y$10$TumE2hUR8VGYv0iPOTbMkekDtCN.OudX9elTZJVlWRoGcloHW.3xS', 'Resident', 1, 'Approved', 0, NULL, 0, '2025-10-17 16:20:23', '2025-10-18 08:32:30', NULL, NULL),
(5, 'poblacionsur648+4@gmail.com', '$2y$10$R.j1ZYMBLkHN/mCHxg3sMuwvax3hYWHq7UQ8C2tbUmWgePf8H8z9C', 'Resident', 1, 'Approved', 0, NULL, 0, '2025-10-17 19:25:30', '2025-10-18 08:37:17', NULL, NULL),
(6, 'poblacionsur648+3@gmail.com', '$2y$10$MnJj3aukn5/mxwidEwEHu.KIKaUgOv.Ct6fVSfJUWDm3cHBC705FG', 'Official', 1, 'Pending', 0, NULL, 0, '2025-10-17 19:34:39', '2025-10-17 19:34:39', NULL, NULL),
(7, 'poblacionsur648+5@gmail.com', '$2y$10$PtzQU6E65a3DrON3p4xMpORzlrfr16rXt2aVI/U9m0zE/MY49nQa6', 'Official', 1, 'Approved', 0, NULL, 0, '2025-10-17 19:37:35', '2025-10-18 08:37:02', NULL, NULL),
(10, 'poblacionsur648+6@gmail.com', '$2y$10$xgRmXvJZ7hcwgP8fbiJQ5.BzBq38siwcqf7vxjnM.vYHkglXQaUE2', 'Resident', 1, 'Approved', 0, NULL, 0, '2025-10-17 19:47:57', '2025-10-17 19:48:54', NULL, NULL),
(11, 'poblacionsur648+7@gmail.com', '$2y$10$cBXcmw9EmQbndf2clCWccuRkEMM5HIoXeA0sKtZevbgx1Ayq.DT9i', 'Resident', 1, 'Verified', 0, NULL, 0, '2025-10-18 22:29:16', '2025-10-18 22:31:39', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_birthdates`
--

CREATE TABLE `user_birthdates` (
  `user_birthdate_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `birth_place` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_birthdates`
--

INSERT INTO `user_birthdates` (`user_birthdate_id`, `user_id`, `birth_date`, `birth_place`, `updated_at`) VALUES
(1, 1, '2018-09-05', 'POBLACION SUR, TALAVERA, NUEVA ECIJA', '2025-10-04 13:32:00'),
(2, 2, '2025-10-01', 'POBLACION SUR, TALAVERA, NUEVA ECIJA', NULL),
(3, 3, '2006-01-01', 'POBLACION', NULL),
(5, 5, '2025-10-15', 'POBLACION SUR, TALAVERA, NUEVA ECIJA', NULL),
(6, 6, '2025-10-15', 'POBLACION SUR, TALAVERA, NUEVA ECIJA', NULL),
(7, 7, '2025-10-15', 'POBLACION SUR, TALAVERA, NUEVA ECIJA', NULL),
(9, 10, '2001-01-16', 'POBLACION SUR, TALAVERA, NUEVA ECIJA', NULL),
(10, 11, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `user_details_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `f_name` varchar(50) NOT NULL,
  `m_name` varchar(50) DEFAULT NULL,
  `l_name` varchar(50) NOT NULL,
  `ext_name` varchar(50) DEFAULT NULL,
  `gender` enum('MALE','FEMALE','LGBTQ','OTHER') DEFAULT NULL,
  `photo` varchar(255) NOT NULL,
  `contact_no` varchar(50) DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `voter_status` enum('Yes','No') DEFAULT NULL,
  `pwd_status` enum('Yes','No') DEFAULT NULL,
  `senior_citizen_status` enum('Yes','No') DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `educational_attainment` varchar(50) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`user_details_id`, `user_id`, `f_name`, `m_name`, `l_name`, `ext_name`, `gender`, `photo`, `contact_no`, `civil_status`, `occupation`, `nationality`, `voter_status`, `pwd_status`, `senior_citizen_status`, `religion`, `blood_type`, `educational_attainment`, `updated_at`) VALUES
(1, 1, 'ADMIN', 'POBSUR', 'TALAVERA', 'II', 'MALE', '../uploads/profile/1758938060_logo.jpg', '9123456789', 'Single', 'Student', 'Filipino', 'Yes', 'Yes', 'Yes', 'Roman Catholic', 'A+', '', '2025-10-17 09:02:09'),
(2, 2, 'OFFICIAL', 'POBSUR', 'TALAVERA', '', 'MALE', '../uploads/profile/1759376262_profile.jpg', '9123456789', 'Married', 'Student', 'Filipino', 'Yes', 'No', 'No', 'Roman Catholic', 'A+', 'ELEMENTARY', '2025-10-17 09:02:12'),
(3, 3, 'RESIDENT', 'POBSUR', 'TALAVERA', 'II', 'FEMALE', '../uploads/profile/1760694993_profile.jpg', '9544555666', 'Single', 'Student', 'Filipino', 'Yes', 'No', 'No', 'Roman Catholic', 'O+', 'ELEMENTARY', '2025-10-17 17:56:33'),
(5, 5, 'CHRISTAL JOY', 'VILLANUEVA', 'PALILIO', 'II', 'FEMALE', '../uploads/profile/1760694993_profile.jpg', '+639123456789', 'MARRIED', 'STUDENT', 'FILIPINO', 'Yes', 'No', 'No', 'ROMAN CATHOLIC', 'A+', 'COLLEGE', '2025-10-17 19:32:13'),
(6, 6, 'crizel', 'valenzuela', 'galvez', '', 'FEMALE', '../uploads/profile/1760700879_captured_photo.jpg', '+639123456789', 'SINGLE', 'STUDENT', 'FILIPINO', 'Yes', 'No', 'No', 'ROMAN CATHOLIC', 'A+', 'COLLEGE', '2025-10-17 19:34:39'),
(7, 7, 'crizel', 'valenzuela', 'galvez', '', 'FEMALE', '../uploads/profile/1760701055_captured_photo.jpg', '+639123456789', 'SINGLE', 'STUDENT', 'FILIPINO', 'Yes', 'No', 'No', 'ROMAN CATHOLIC', 'A+', 'COLLEGE', '2025-10-17 19:37:35'),
(9, 10, 'resident', 'middle', 'pob', '', 'LGBTQ', '../uploads/profile/1760701677_captured_photo.jpg', '+639123456781', 'SINGLE', 'STUDENT', 'FILIPINO', 'Yes', 'No', 'No', 'HINDUISM', 'O-', 'COLLEGE', '2025-10-17 19:47:57'),
(10, 11, 'CRIZEL', '', 'GALVEZ', '', NULL, '', '9123456789', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-18 22:29:16');

-- --------------------------------------------------------

--
-- Table structure for table `user_family_info`
--

CREATE TABLE `user_family_info` (
  `family_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fathers_name` varchar(255) DEFAULT NULL,
  `fathers_birthplace` varchar(255) DEFAULT NULL,
  `mothers_name` varchar(255) DEFAULT NULL,
  `mothers_birthplace` varchar(255) DEFAULT NULL,
  `spouse_name` varchar(255) DEFAULT NULL,
  `num_dependents` int(11) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `emergency_contact_no` varchar(20) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_family_info`
--

INSERT INTO `user_family_info` (`family_id`, `user_id`, `fathers_name`, `fathers_birthplace`, `mothers_name`, `mothers_birthplace`, `spouse_name`, `num_dependents`, `contact_person`, `emergency_contact_no`, `updated_at`) VALUES
(1, 1, 'FATHER', 'FATHER PLACE2', 'MOTHER', 'MOTHER PLACE2', '', 0, 'EMERGENCY', '9123456789', '2025-10-04 13:32:00'),
(2, 2, 'FATHER', 'FATHER PLACE2', 'MOTHER', 'MOTHER PLACE', 'SPOUSE', 12, 'EMERGENCY', '9123456789', NULL),
(3, 3, 'FATHER', 'FATHER BIRTHPLACE', 'MOTHER', 'MOTHER BIRTHPLACE', '', 0, 'CHRIZA GALVEZ', '9884266654', NULL),
(5, 5, 'POBLACION', 'FATHER PLACE2', 'MOTHER2', 'MOTHER PLACE', 'MyLove', 1, 'EMERGENCY', '+639123456789', NULL),
(6, 6, 'POBLACION', 'FATHER PLACE2', 'MOTHER', 'MOTHER PLACE', '', 1, 'EMERGENCY', '+639123456789', NULL),
(7, 7, 'POBLACION', 'FATHER PLACE2', 'MOTHER', 'MOTHER PLACE', '', 1, 'EMERGENCY', '+639123456789', NULL),
(9, 10, 'FATHER', 'FATHER PLACE2', 'MOTHER', 'MOTHER PLACE2', '', 11, 'EMERGENCY', '+633232323232', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_health_info`
--

CREATE TABLE `user_health_info` (
  `health_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `health_condition` varchar(100) DEFAULT NULL,
  `common_health_issue` varchar(100) DEFAULT NULL,
  `vaccination_status` varchar(50) DEFAULT NULL,
  `height_cm` decimal(5,2) DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `last_medical_checkup` date DEFAULT NULL,
  `health_remarks` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_health_info`
--

INSERT INTO `user_health_info` (`health_id`, `user_id`, `health_condition`, `common_health_issue`, `vaccination_status`, `height_cm`, `weight_kg`, `last_medical_checkup`, `health_remarks`, `updated_at`) VALUES
(1, 1, 'Disabled', 'Hypertension', 'Partially Vaccinated', 170.00, 90.00, '2025-09-25', 'HEALTH REMARKS', '2025-10-04 13:32:00'),
(2, 2, 'Healthy', 'Diabetes', 'Partially Vaccinated', 170.00, 80.00, '2025-10-01', 'REMARKS', NULL),
(3, 3, 'Healthy', 'None', 'Fully Vaccinated', 130.00, 50.00, '2025-10-15', '', NULL),
(5, 5, 'HEALTHY', 'NONE', 'PARTIALLY VACCINATED', 125.00, 46.00, '2025-10-14', 'n/A', NULL),
(6, 6, 'HEALTHY', 'NONE', 'PARTIALLY VACCINATED', 120.00, 50.00, '2025-10-05', 'dadas', NULL),
(7, 7, 'HEALTHY', 'NONE', 'PARTIALLY VACCINATED', 120.00, 50.00, '2025-10-05', 'dadas', NULL),
(9, 10, 'HEALTHY', 'NONE', 'FULLY VACCINATED', 130.00, 46.00, '2025-10-15', 'n/a', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_identity_docs`
--

CREATE TABLE `user_identity_docs` (
  `identity_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `id_type` varchar(100) DEFAULT NULL,
  `front_valid_id_path` varchar(255) DEFAULT NULL,
  `back_valid_id_path` varchar(255) DEFAULT NULL,
  `selfie_with_id` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_identity_docs`
--

INSERT INTO `user_identity_docs` (`identity_id`, `user_id`, `id_type`, `front_valid_id_path`, `back_valid_id_path`, `selfie_with_id`, `updated_at`) VALUES
(1, 1, 'TIN ID', '../uploads/ids/front/1758938060_front.webp', '../uploads/ids/back/1758938060_back.png', '../uploads/ids/selfie/1758938060_selfie.jpg', '2025-10-04 13:32:00'),
(2, 2, 'National ID', '../uploads/ids/front/1759376262_front.webp', '../uploads/ids/back/1759376262_back.png', '../uploads/ids/selfie/1759376262_selfie.jpg', NULL),
(3, 3, "Voter's ID", '../uploads/ids/front/1760694993_front.webp', '../uploads/ids/back/1760694993_back.png', '../uploads/ids/selfie/1760694993_selfie.jpg', NULL),
(5, 5, 'SSS ID', '../uploads/ids/front/1760694993_front.webp', '../uploads/ids/back/1760694993_back.png', '../uploads/ids/selfie/1760694993_selfie.jpg', NULL),
(6, 6, 'STUDENT ID', '../uploads/ids/1760700879_front_front.webp', '../uploads/ids/1760700879_back_back.png', '../uploads/ids/1760700879_selfie_selfie.jpg', NULL),
(7, 7, 'STUDENT ID', '../uploads/ids/1760701055_front_front.webp', '../uploads/ids/1760701055_back_back.png', '../uploads/ids/1760701055_selfie_selfie.jpg', NULL),
(9, 10, 'NATIONAL ID', '../uploads/ids/1760701677_front_front.webp', '../uploads/ids/1760701677_back_back.png', '../uploads/ids/1760701677_selfie_selfie.jpg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_income_info`
--

CREATE TABLE `user_income_info` (
  `income_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `monthly_income` decimal(10,2) DEFAULT NULL,
  `income_source` varchar(100) DEFAULT NULL,
  `household_members` int(11) DEFAULT NULL,
  `additional_income_sources` varchar(255) DEFAULT NULL,
  `household_head_occupation` varchar(255) DEFAULT NULL,
  `income_proof` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_income_info`
--

INSERT INTO `user_income_info` (`income_id`, `user_id`, `monthly_income`, `income_source`, `household_members`, `additional_income_sources`, `household_head_occupation`, `income_proof`, `updated_at`) VALUES
(1, 1, 20000.00, 'Farming', 5, 'additional', 'head occupation', '../uploads/income/1758938060_income.jpeg', '2025-10-04 13:32:00'),
(2, 2, 10.00, 'Employment', 10, 'SUGAL', 'N/A', '../uploads/income/1759376262_income.jpeg', NULL),
(3, 3, 0.00, 'None', 6, '', '', '../uploads/income/1760694993_income.jpeg', NULL),
(5, 5, 0.00, 'None', 2, 'N/A', 'N/A', '../uploads/income/1760700330_income.jpeg', NULL),
(6, 6, 2.00, 'None', 1, 'SUGAL', 'N/A', '../uploads/income/1760700879_income.jpeg', NULL),
(7, 7, 2.00, 'None', 1, 'SUGAL', 'N/A', '../uploads/income/1760701055_income.jpeg', NULL),
(9, 10, 0.00, 'None', 12, 'N/A', 'N/A', '../uploads/income/1760701677_income.jpeg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_residency`
--

CREATE TABLE `user_residency` (
  `user_residency_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `house_no` varchar(255) DEFAULT NULL,
  `purok` varchar(100) DEFAULT NULL,
  `barangay` varchar(100) NOT NULL DEFAULT 'POBLACION SUR',
  `municipality` varchar(100) NOT NULL DEFAULT 'TALAVERA',
  `province` varchar(100) NOT NULL DEFAULT 'NUEVA ECIJA',
  `years_residency` int(11) DEFAULT NULL,
  `household_head` enum('Yes','No') DEFAULT NULL,
  `house_type` varchar(100) DEFAULT NULL,
  `ownership_status` varchar(100) DEFAULT NULL,
  `previous_address` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_residency`
--

INSERT INTO `user_residency` (`user_residency_id`, `user_id`, `house_no`, `purok`, `barangay`, `municipality`, `province`, `years_residency`, `household_head`, `house_type`, `ownership_status`, `previous_address`, `updated_at`) VALUES
(1, 1, '1', 'PUROK2', 'POBLACION SUR', 'TALAVERA', 'NUEVA ECIJA', 12, 'Yes', 'HOUSE', 'OWNED', 'previous', '2025-10-04 21:32:00'),
(2, 2, '12', 'PUROK5', 'POBLACION SUR', 'TALAVERA', 'NUEVA ECIJA', 3, 'Yes', 'HOUSE', 'OWNED', 'PREVIOUS', '2025-10-02 11:37:42'),
(3, 3, '13', 'PUROK4', 'POBLACION SUR', 'TALAVERA', 'NUEVA ECIJA', 10, 'No', 'HOUSE', 'OWNED', '', '2025-10-17 17:56:33'),
(5, 5, '12', 'PUROK 7B', 'POBLACION SUR', 'TALAVERA', 'NUEVA ECIJA', 12, 'Yes', 'APARTMENT', 'OWNED', 'n/A', '2025-10-17 19:25:30'),
(6, 6, '1', 'PUROK 2', 'POBLACION SUR', 'TALAVERA', 'NUEVA ECIJA', 12, 'No', 'HOUSE', 'OWNED', 'n/A', '2025-10-17 19:34:39'),
(7, 7, '1', 'PUROK 2', 'POBLACION SUR', 'TALAVERA', 'NUEVA ECIJA', 12, 'No', 'HOUSE', 'OWNED', 'n/A', '2025-10-17 19:37:35'),
(9, 10, '1', 'PUROK 3', 'POBLACION SUR', 'TALAVERA', 'NUEVA ECIJA', 12, 'No', 'HOUSE', 'OWNED', 'n/A', '2025-10-17 19:47:57'),
(10, 11, '13', 'PUROK1', 'POBLACION SUR', 'TALAVERA', 'NUEVA ECIJA', NULL, NULL, NULL, NULL, NULL, '2025-10-18 22:29:16');

-- --------------------------------------------------------

--
-- Table structure for table `verifications`
--

CREATE TABLE `verifications` (
  `verification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `verified_at` datetime DEFAULT NULL,
  `session_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verifications`
--

INSERT INTO `verifications` (`verification_id`, `user_id`, `token`, `expires_at`, `verified_at`, `session_token`) VALUES
(1, 1, '9a614e76e81c83881299667ba6dc9b8c', '2025-09-28 09:50:22', '2025-09-27 09:51:01', '93b011f040f20b780d21a12cd734f8d2715321a475a3e51ec6cc805a0019cb99'),
(2, 2, '312a946cd9261460a20fecfee0db0fa9', '2025-10-02 20:23:52', '2025-10-01 20:24:13', '64f3eeae659d4222e2b452c323af094cde92fe7bc68b891f5af2716b056a6c8d'),
(3, 3, 'd6dcbf939f6eef34b937d383edfa495d', '2025-10-18 16:20:23', '2025-10-17 16:21:09', 'd77b54bb9964b3550709a72c39d2ee623ebe672a23e67a4b21bff2c0adb20972'),
(4, 5, '4ecef30e0faa9a5e156caa4108a2753b', '2025-10-18 13:25:30', NULL, NULL),
(5, 6, '8710bec6e7b2126ea7a84bf5756cf92a', '2025-10-18 13:34:39', NULL, NULL),
(6, 7, '46e679b092b82c1e6e735b33c0be8981', '2025-10-18 13:37:35', NULL, NULL),
(8, 10, '5b0df637112455c8ca42acd1c8d56fc3', '2025-10-18 13:47:57', '2025-10-17 19:48:54', '06580124996b98caa313fad4ff9dfa92417ffe2622e4b764a0882e3cca40ce5d'),
(9, 11, '0c568afdcdfb7d0525e9d597c207a349', '2025-10-19 22:29:16', '2025-10-18 22:31:39', 'd92835ca40d388754fd8b65c94ec73365669965579c3f7ff8882a29bbf5ece8c');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`);

--
-- Indexes for table `approvals`
--
ALTER TABLE `approvals`
  ADD PRIMARY KEY (`approval_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `document_requests`
--
ALTER TABLE `document_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `fk_user_request` (`user_id`),
  ADD KEY `fk_processed_by` (`processed_by`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `incidents`
--
ALTER TABLE `incidents`
  ADD PRIMARY KEY (`incident_id`),
  ADD KEY `reporter_user_id` (`reporter_user_id`),
  ADD KEY `reporter_non_resident_id` (`reporter_non_resident_id`);

--
-- Indexes for table `incident_persons`
--
ALTER TABLE `incident_persons`
  ADD PRIMARY KEY (`person_id`),
  ADD KEY `incident_id` (`incident_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `non_resident_id` (`non_resident_id`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`inquiries_id`);

--
-- Indexes for table `non_residents`
--
ALTER TABLE `non_residents`
  ADD PRIMARY KEY (`non_resident_id`);

--
-- Indexes for table `officials`
--
ALTER TABLE `officials`
  ADD PRIMARY KEY (`official_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`otp_id`),
  ADD KEY `fk_otp_non_resident` (`non_resident_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`password_reset_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`reminder_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_birthdates`
--
ALTER TABLE `user_birthdates`
  ADD PRIMARY KEY (`user_birthdate_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`user_details_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_family_info`
--
ALTER TABLE `user_family_info`
  ADD PRIMARY KEY (`family_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_health_info`
--
ALTER TABLE `user_health_info`
  ADD PRIMARY KEY (`health_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_identity_docs`
--
ALTER TABLE `user_identity_docs`
  ADD PRIMARY KEY (`identity_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_income_info`
--
ALTER TABLE `user_income_info`
  ADD PRIMARY KEY (`income_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_residency`
--
ALTER TABLE `user_residency`
  ADD PRIMARY KEY (`user_residency_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `verifications`
--
ALTER TABLE `verifications`
  ADD PRIMARY KEY (`verification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approvals`
--
ALTER TABLE `approvals`
  MODIFY `approval_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_requests`
--
ALTER TABLE `document_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `incident_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `incident_persons`
--
ALTER TABLE `incident_persons`
  MODIFY `person_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `inquiries_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `non_residents`
--
ALTER TABLE `non_residents`
  MODIFY `non_resident_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `officials`
--
ALTER TABLE `officials`
  MODIFY `official_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `otp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `password_reset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
  MODIFY `reminder_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_birthdates`
--
ALTER TABLE `user_birthdates`
  MODIFY `user_birthdate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `user_details_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_family_info`
--
ALTER TABLE `user_family_info`
  MODIFY `family_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_health_info`
--
ALTER TABLE `user_health_info`
  MODIFY `health_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_identity_docs`
--
ALTER TABLE `user_identity_docs`
  MODIFY `identity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_income_info`
--
ALTER TABLE `user_income_info`
  MODIFY `income_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_residency`
--
ALTER TABLE `user_residency`
  MODIFY `user_residency_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `verifications`
--
ALTER TABLE `verifications`
  MODIFY `verification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approvals`
--
ALTER TABLE `approvals`
  ADD CONSTRAINT `approvals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `document_requests`
--
ALTER TABLE `document_requests`
  ADD CONSTRAINT `fk_processed_by` FOREIGN KEY (`processed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_request` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `incidents`
--
ALTER TABLE `incidents`
  ADD CONSTRAINT `incidents_ibfk_1` FOREIGN KEY (`reporter_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `incidents_ibfk_2` FOREIGN KEY (`reporter_non_resident_id`) REFERENCES `non_residents` (`non_resident_id`) ON DELETE SET NULL;

--
-- Constraints for table `incident_persons`
--
ALTER TABLE `incident_persons`
  ADD CONSTRAINT `incident_persons_ibfk_1` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`incident_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `incident_persons_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `incident_persons_ibfk_3` FOREIGN KEY (`non_resident_id`) REFERENCES `non_residents` (`non_resident_id`) ON DELETE SET NULL;

--
-- Constraints for table `officials`
--
ALTER TABLE `officials`
  ADD CONSTRAINT `officials_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD CONSTRAINT `fk_otp_non_resident` FOREIGN KEY (`non_resident_id`) REFERENCES `non_residents` (`non_resident_id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `reminders`
--
ALTER TABLE `reminders`
  ADD CONSTRAINT `reminders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_birthdates`
--
ALTER TABLE `user_birthdates`
  ADD CONSTRAINT `user_birthdates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_details`
--
ALTER TABLE `user_details`
  ADD CONSTRAINT `user_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_family_info`
--
ALTER TABLE `user_family_info`
  ADD CONSTRAINT `user_family_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_health_info`
--
ALTER TABLE `user_health_info`
  ADD CONSTRAINT `user_health_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_identity_docs`
--
ALTER TABLE `user_identity_docs`
  ADD CONSTRAINT `user_identity_docs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_income_info`
--
ALTER TABLE `user_income_info`
  ADD CONSTRAINT `user_income_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_residency`
--
ALTER TABLE `user_residency`
  ADD CONSTRAINT `user_residency_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `verifications`
--
ALTER TABLE `verifications`
  ADD CONSTRAINT `verifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

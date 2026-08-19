-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 18, 2026 at 06:05 PM
-- Server version: 11.8.8-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u850523537_iRecover_DB`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `user_id` int(100) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `number` int(100) NOT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `district` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `type_of_entity` varchar(100) NOT NULL,
  `role` enum('super_admin','admin','station') NOT NULL DEFAULT 'station',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `registered_at` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`user_id`, `user_name`, `password`, `number`, `contact_phone`, `email`, `district`, `address`, `type_of_entity`, `role`, `is_active`, `registered_at`) VALUES
(5, 'Voice of Lango FM', '$2y$10$3ffCRS7OKwQfGzFiO8BfbONMdGXyOxNOYwJKXxlX8YobvGigTigXi', 777676206, NULL, 'vol@irecover.info', 'Lira City', 'Lira City', 'Company', 'station', 1, '2025-01-28 / 09:59:23 AM'),
(6, 'Qfm', '$2y$10$OnX.kx72OFISrmI0gx2Jau/NXNNeD/Vp3wNFrsWa02CFDP1nrcWRC', 777676206, NULL, 'qfm@irecover.info', 'Lira City', 'Lira City', 'Company', 'station', 1, '2025-01-28 / 01:17:07 PM'),
(7, 'Voice of The Gospel', '$2y$10$od9bpIGSqh87w429ABpdJujLSiSgItuy2vPPaRNbqYMVlcgw2u0C6', 777676206, NULL, 'vog@irecover.info', 'Lira City', 'Lira City', 'Company', 'station', 1, '2025-01-31 / 06:48:04 AM'),
(8, 'superadmin', '$2y$10$22U/gLbnOzIL62BJIpgIDusOWyhdZYOLnPf1SW2PEY.w3OTH1LBPy', 777000001, NULL, 'superadmin@irecover.ug', 'Kampala', 'Head Office', 'iRecovery', 'super_admin', 1, '2026-07-13 14:56:22'),
(9, 'admin', '$2y$10$Joua22FzCZxldfMGPfpB7eRtCOqBeen2Hf17Tr.HYznjC35wuQQCC', 777000002, NULL, 'admin@irecover.ug', 'Kampala', 'Head Office', 'iRecovery', 'admin', 1, '2026-07-13 14:56:22'),
(15, 'Lira Central Police', '$2y$10$mCEa2NfxiZu8DJXRTQwgg.JGspQpbF57d/H2W8ZMG9f8NuPTh0l6i', 772100100, '0772100100', 'police.lira@irecover.ug', 'Lira City', 'Lira Central Police Station', 'Police', 'station', 1, '2026-07-13 11:57:02'),
(16, 'liracps', '$2y$10$.hsFvF0xf/oCjxrfFpPLBe0JAQkgCWiMe.uSn8cL2Kvcjm8sNHYj2', 777676206, NULL, 'ot.sedrick@gmail.com', 'Lira', 'Lira', 'Company', 'station', 1, '2026-07-13 / 03:25:49 PM');

-- --------------------------------------------------------

--
-- Table structure for table `collection_log`
--

CREATE TABLE `collection_log` (
  `id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `alert_id` int(11) DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `station` varchar(150) NOT NULL,
  `collected_by` varchar(255) DEFAULT NULL,
  `collected_at` datetime NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `collection_log`
--

INSERT INTO `collection_log` (`id`, `document_id`, `alert_id`, `payment_id`, `station`, `collected_by`, `collected_at`, `notes`) VALUES
(1, 18, 1, NULL, 'superadmin', 'Admin (manual)', '2026-08-09 09:15:35', NULL),
(2, 1, 3, NULL, '0', 'Sedrick', '2026-08-15 08:38:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `doc_type` enum('national_id','driving_permit','passport','student_id','academic_document','land_title','birth_certificate','other') NOT NULL,
  `sur_name` varchar(255) NOT NULL,
  `given_name` varchar(255) NOT NULL DEFAULT '',
  `dob` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `id_number` varchar(150) DEFAULT NULL,
  `extra_field1` varchar(255) DEFAULT NULL,
  `extra_field2` varchar(255) DEFAULT NULL,
  `extra_field3` varchar(255) DEFAULT NULL,
  `front_img` varchar(255) DEFAULT NULL,
  `back_img` varchar(255) DEFAULT NULL,
  `action` enum('found','reported','matched','collected','cancelled') NOT NULL DEFAULT 'found',
  `reporter` varchar(150) NOT NULL DEFAULT 'Public',
  `reporter_phone` varchar(20) DEFAULT NULL,
  `police_letter` varchar(255) DEFAULT NULL,
  `station_holding` varchar(150) DEFAULT NULL,
  `payment_status` enum('pending','paid','waived') NOT NULL DEFAULT 'pending',
  `payment_ref` varchar(100) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `doc_type`, `sur_name`, `given_name`, `dob`, `gender`, `id_number`, `extra_field1`, `extra_field2`, `extra_field3`, `front_img`, `back_img`, `action`, `reporter`, `reporter_phone`, `police_letter`, `station_holding`, `payment_status`, `payment_ref`, `payment_date`, `submitted_at`, `updated_at`) VALUES
(1, 'national_id', 'OTOLO', 'SEDU', '2026-07-13', 'male', 'CM9505', '', '', '', 'DOC_FRONT_DBK8OS_1783947847.png', 'DOC_BACK_Jkqk1f_1783947847.png', 'collected', 'Voice of Lango FM', '', NULL, 'Voice of Lango FM', 'pending', NULL, NULL, '2026-07-13 15:04:07', '2026-08-15 08:38:02'),
(2, 'national_id', 'NAKATO', 'ESTHER', '1994-03-12', 'female', 'CM94037112AXTZ', NULL, NULL, NULL, 'https://placehold.co/640x400/0d9488/ffffff?text=National+ID+Front', 'https://placehold.co/640x400/0f766e/ffffff?text=National+ID+Back', 'found', 'Voice of Lango FM', '0777676206', NULL, 'Voice of Lango FM', 'pending', NULL, NULL, '2026-08-08 17:15:22', NULL),
(3, 'national_id', 'MUGISHA', 'PATRICK', '1990-11-05', 'male', 'CM90113105BQWK', NULL, NULL, NULL, 'https://placehold.co/640x400/0d9488/ffffff?text=National+ID+Front', 'https://placehold.co/640x400/0f766e/ffffff?text=National+ID+Back', 'found', 'Qfm', '0777676206', NULL, 'Qfm', 'pending', NULL, NULL, '2026-08-08 17:15:22', NULL),
(4, 'driving_permit', 'ATIM', 'GLORIA', '1992-07-21', 'female', 'DP-2024-001122', NULL, NULL, NULL, 'https://placehold.co/640x400/2563eb/ffffff?text=Driving+Permit+Front', 'https://placehold.co/640x400/1d4ed8/ffffff?text=Driving+Permit+Back', 'found', 'Voice of The Gospel', '0777676206', NULL, 'Voice of The Gospel', 'pending', NULL, NULL, '2026-08-08 17:15:22', NULL),
(5, 'driving_permit', 'OKELLO', 'BRIAN', '1988-02-14', 'male', 'DP-2023-998877', NULL, NULL, NULL, 'https://placehold.co/640x400/2563eb/ffffff?text=Driving+Permit+Front', 'https://placehold.co/640x400/1d4ed8/ffffff?text=Driving+Permit+Back', 'found', 'Voice of Lango FM', '0777676206', NULL, 'Voice of Lango FM', 'pending', NULL, NULL, '2026-08-08 17:15:22', NULL),
(6, 'passport', 'NAMUTEBI', 'SARAH', '1996-09-30', 'female', 'B1234567', NULL, NULL, NULL, 'https://placehold.co/640x400/7c3aed/ffffff?text=Passport+Front', 'https://placehold.co/640x400/6d28d9/ffffff?text=Passport+Back', 'found', 'Qfm', '0777676206', NULL, 'Qfm', 'pending', NULL, NULL, '2026-08-08 17:15:22', NULL),
(7, 'passport', 'SSEMPALA', 'JOHN', '1985-01-18', 'male', 'B7654321', NULL, NULL, NULL, 'https://placehold.co/640x400/7c3aed/ffffff?text=Passport+Front', 'https://placehold.co/640x400/6d28d9/ffffff?text=Passport+Back', 'found', 'Lira Central Police', '0772100100', NULL, 'Lira Central Police', 'pending', NULL, NULL, '2026-08-08 17:15:22', NULL),
(8, 'student_id', 'AKELLO', 'PRECIOUS', '2002-05-09', 'female', 'STU/2024/0456', NULL, NULL, NULL, 'https://placehold.co/640x400/ea580c/ffffff?text=Student+ID+Front', 'https://placehold.co/640x400/c2410c/ffffff?text=Student+ID+Back', 'found', 'Voice of Lango FM', '0777676206', NULL, 'Voice of Lango FM', 'pending', NULL, NULL, '2026-08-08 17:15:22', NULL),
(9, 'student_id', 'WABWIRE', 'DENIS', '2001-12-03', 'male', 'STU/2023/1122', NULL, NULL, NULL, 'https://placehold.co/640x400/ea580c/ffffff?text=Student+ID+Front', 'https://placehold.co/640x400/c2410c/ffffff?text=Student+ID+Back', 'found', 'Voice of The Gospel', '0777676206', NULL, 'Voice of The Gospel', 'pending', NULL, NULL, '2026-08-08 17:15:22', NULL),
(10, 'academic_document', 'NABWIRE', 'FLORENCE', '1993-04-27', 'female', 'CERT-UG-8890', NULL, NULL, NULL, 'https://placehold.co/640x400/0891b2/ffffff?text=Academic+Doc+Front', 'https://placehold.co/640x400/0e7490/ffffff?text=Academic+Doc+Back', 'found', 'Qfm', '0777676206', NULL, 'Qfm', 'pending', NULL, NULL, '2026-08-08 17:15:22', NULL),
(11, 'academic_document', 'KATO', 'EMMANUEL', '1991-08-16', 'male', 'CERT-UG-4471', NULL, NULL, NULL, 'https://placehold.co/640x400/0891b2/ffffff?text=Academic+Doc+Front', 'https://placehold.co/640x400/0e7490/ffffff?text=Academic+Doc+Back', 'found', 'Voice of Lango FM', '0777676206', NULL, 'Voice of Lango FM', 'pending', NULL, NULL, '2026-08-08 17:15:22', NULL),
(12, 'land_title', 'BYARUHANGA', 'MOSES', '1980-06-11', 'male', 'LRV-445-KLA', NULL, NULL, NULL, 'https://placehold.co/640x400/65a30d/ffffff?text=Land+Title+Front', 'https://placehold.co/640x400/4d7c0f/ffffff?text=Land+Title+Back', 'found', 'Lira Central Police', '0772100100', NULL, 'Lira Central Police', 'pending', NULL, NULL, '2026-08-08 17:15:22', NULL),
(13, 'land_title', 'NAMARA', 'JOAN', '1983-10-24', 'female', 'LRV-778-MBR', NULL, NULL, NULL, 'https://placehold.co/640x400/65a30d/ffffff?text=Land+Title+Front', 'https://placehold.co/640x400/4d7c0f/ffffff?text=Land+Title+Back', 'found', 'Voice of The Gospel', '0777676206', NULL, 'Voice of The Gospel', 'pending', NULL, NULL, '2026-08-08 17:15:22', NULL),
(14, 'birth_certificate', 'TUMWEBAZE', 'IVAN', '2010-01-08', 'male', 'BC-2019-33221', NULL, NULL, NULL, 'https://placehold.co/640x400/db2777/ffffff?text=Birth+Cert+Front', 'https://placehold.co/640x400/be185d/ffffff?text=Birth+Cert+Back', 'found', 'Qfm', '0777676206', NULL, 'Qfm', 'pending', NULL, NULL, '2026-08-08 17:15:22', NULL),
(15, 'birth_certificate', 'ACHENG', 'LILIAN', '2012-03-19', 'female', 'BC-2020-44556', NULL, NULL, NULL, 'https://placehold.co/640x400/db2777/ffffff?text=Birth+Cert+Front', 'https://placehold.co/640x400/be185d/ffffff?text=Birth+Cert+Back', 'found', 'Voice of Lango FM', '0777676206', NULL, 'Voice of Lango FM', 'pending', NULL, NULL, '2026-08-08 17:15:22', NULL),
(16, 'other', 'OKIROR', 'SAMUEL', '1975-09-02', 'male', 'OTH-0091', NULL, NULL, NULL, 'https://placehold.co/640x400/475569/ffffff?text=Document+Front', 'https://placehold.co/640x400/334155/ffffff?text=Document+Back', 'found', 'Lira Central Police', '0772100100', NULL, 'Lira Central Police', 'pending', NULL, NULL, '2026-08-08 17:15:22', NULL),
(17, 'other', 'NAKAWEESI', 'RUTH', '1998-12-30', 'female', 'OTH-0245', NULL, NULL, NULL, 'https://placehold.co/640x400/475569/ffffff?text=Document+Front', 'https://placehold.co/640x400/334155/ffffff?text=Document+Back', 'found', 'Voice of The Gospel', '0777676206', NULL, 'Voice of The Gospel', 'pending', NULL, NULL, '2026-08-08 17:15:22', NULL),
(18, 'national_id', 'OBIN', 'IVAN', '2026-08-09', 'male', 'CM950', '', '', '', 'https://irecover.site/uploads/DOC_FRONT_UOqVS8_1786266637.png', NULL, 'collected', 'superadmin', '', NULL, 'superadmin', 'paid', NULL, '2026-08-09 09:15:12', '2026-08-09 09:10:37', '2026-08-09 09:15:35'),
(19, 'national_id', 'KOMAKECH', 'MOSES', '1998-04-10', 'male', 'CM98050103GG8F', '', '', '', 'https://irecover.site/uploads/DOC_FRONT_hPtgXR_1786557890.png', 'https://irecover.site/uploads/DOC_BACK_uFrEUc_1786557890.png', 'found', 'Public', '', NULL, 'Public', 'pending', NULL, NULL, '2026-08-12 18:04:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `documents_legacy`
--

CREATE TABLE `documents_legacy` (
  `id` int(11) NOT NULL,
  `document_type` varchar(50) DEFAULT NULL,
  `id_number` varchar(100) DEFAULT NULL,
  `id_name` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `document_photo` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents_legacy`
--

INSERT INTO `documents_legacy` (`id`, `document_type`, `id_number`, `id_name`, `dob`, `document_photo`, `phone_number`, `email`, `created_at`) VALUES
(2, NULL, NULL, NULL, NULL, 'uploads/WhatsApp Image 2024-12-04 at 11.08.55_ef8acdcc.jpg', NULL, NULL, '2024-12-04 14:12:33'),
(3, 'nationalID', 'hhghghgh', 'jrttt', '2024-12-25', 'uploads/WhatsApp Image 2024-12-04 at 11.08.54_86e19604.jpg', '+256 9777676206', 'sedricksedu2@gmail.com', '2024-12-04 14:17:08'),
(4, 'nationalID', 'cm48038345-08', 'Sedrick Otolo', '2024-12-04', 'uploads/ekuka.png', '+256 9777676206', 'sedricksedu2@gmail.com', '2024-12-04 14:33:59'),
(5, 'nationalID', '123', 'Sedu', '2024-12-04', 'uploads/WhatsApp Image 2024-12-04 at 11.08.55_ef8acdcc.jpg', '+256 9777676206', 'sedricksedu2@gmail.com', '2024-12-04 14:45:48'),
(6, 'studentID', '12', '12', '2024-12-03', 'uploads/WhatsApp Image 2024-12-04 at 11.08.55_04591312.jpg', '0777676206', 'sedricksedu2@gmail.com', '2024-12-04 15:00:26'),
(7, 'drivingPermit', '1234', 'Nam Ronny', '2024-12-04', 'uploads/WhatsApp Image 2024-12-04 at 11.08.56_7bfff6b1.jpg', '0780286800', 'steujps@gmail.com', '2024-12-04 15:21:31');

-- --------------------------------------------------------

--
-- Table structure for table `driving_permits`
--

CREATE TABLE `driving_permits` (
  `driver_id` int(11) NOT NULL,
  `sur_name` varchar(255) NOT NULL,
  `given_name` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `permit_number` varchar(100) NOT NULL,
  `nin_number` varchar(100) NOT NULL,
  `front` varchar(255) NOT NULL,
  `back` varchar(255) NOT NULL,
  `user_action` varchar(100) NOT NULL,
  `reporter` varchar(100) NOT NULL,
  `date_found` varchar(100) NOT NULL,
  `uploader_n` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `driving_permits`
--

INSERT INTO `driving_permits` (`driver_id`, `sur_name`, `given_name`, `dob`, `permit_number`, `nin_number`, `front`, `back`, `user_action`, `reporter`, `date_found`, `uploader_n`) VALUES
(1, 'NONO', 'INNOCENT', '2002-05-05', '12913912', 'CM02103107K18J', 'NID_FrontRand_66_h67Lj.png', 'NID_BackRand_55_h67Lj.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:46:24 PM', 0),
(2, 'MAGANGA', 'JOHN MARY', '1980-01-29', '10218867/4/1', 'CM800129112128863', 'NID_FrontRand_66_4Ox8w.png', 'NID_BackRand_55_4Ox8w.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:34:29 PM', 0),
(3, 'OGONG', 'RONALD', '1983-10-23', '10244191/4/1983', '8201411294712', 'NID_FrontRand_66_qvIRY.png', 'NID_BackRand_55_qvIRY.png', 'Found', 'Voice of Lango Fm', '2025-01-30 / 07:17:06 AM', 0),
(4, 'MAYANJA', 'ROBERT', '1986-10-10', '12898593', 'CM860521017J1H', 'NID_FrontRand_66_TFu6L.png', 'NID_BackRand_55_TFu6L.png', 'Found', 'QFM', '2025-01-31 / 08:04:13 AM', 0),
(5, 'KYANDA', 'EDWIN BUWEMBO', '1996-12-06', '13239439', 'CM9605210A25YD', 'NID_FrontRand_66_XfPwQ.png', 'NID_BackRand_55_XfPwQ.png', 'Found', 'QFM', '2025-01-31 / 08:08:36 AM', 0),
(6, 'OTOLO ', 'SEDRICK', '2025-05-12', '13138422', 'CM95057101A9CC', 'NID_FrontRand_66_Aq5ad.png', 'NID_BackRand_55_Aq5ad.png', 'Found', 'Public', '2025-05-13 / 06:33:38 AM', 0);

-- --------------------------------------------------------

--
-- Table structure for table `fee_config`
--

CREATE TABLE `fee_config` (
  `id` int(11) NOT NULL,
  `doc_type` varchar(50) NOT NULL,
  `fee_ugx` decimal(10,2) NOT NULL DEFAULT 10000.00,
  `commission_percent` decimal(5,2) NOT NULL DEFAULT 20.00,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_config`
--

INSERT INTO `fee_config` (`id`, `doc_type`, `fee_ugx`, `commission_percent`, `updated_at`) VALUES
(1, 'national_id', 25000.00, 20.00, '2026-08-08 15:26:40'),
(2, 'driving_permit', 15000.00, 20.00, NULL),
(3, 'passport', 30000.00, 20.00, '2026-08-08 15:26:40'),
(4, 'student_id', 5000.00, 20.00, NULL),
(5, 'academic_document', 30000.00, 20.00, '2026-08-08 15:32:50'),
(6, 'land_title', 25000.00, 20.00, NULL),
(7, 'birth_certificate', 5000.00, 20.00, NULL),
(8, 'other', 10000.00, 20.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `found_documents`
--

CREATE TABLE `found_documents` (
  `id` int(11) NOT NULL,
  `document_type` varchar(255) NOT NULL,
  `name_on_document` varchar(255) NOT NULL,
  `id_number` varchar(50) NOT NULL,
  `contact_info` varchar(50) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `found_ids`
--

CREATE TABLE `found_ids` (
  `id` int(11) NOT NULL,
  `id_type` varchar(50) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `submitter_name` varchar(255) NOT NULL,
  `submitter_phone` varchar(15) NOT NULL,
  `nin` varchar(50) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `sub_county` varchar(100) DEFAULT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `place_found` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `school_name` varchar(255) DEFAULT NULL,
  `student_number` varchar(50) DEFAULT NULL,
  `document_type` varchar(100) DEFAULT NULL,
  `institution_name` varchar(255) DEFAULT NULL,
  `graduation_year` year(4) DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lost_reports`
--

CREATE TABLE `lost_reports` (
  `id` int(11) NOT NULL,
  `doc_type` enum('national_id','driving_permit','passport','student_id','academic_document','land_title','birth_certificate','other') NOT NULL,
  `sur_name` varchar(255) NOT NULL,
  `given_name` varchar(255) NOT NULL DEFAULT '',
  `dob` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `id_number` varchar(150) DEFAULT NULL,
  `extra_field1` varchar(255) DEFAULT NULL,
  `extra_field2` varchar(255) DEFAULT NULL,
  `reporter_name` varchar(255) NOT NULL,
  `reporter_phone` varchar(20) NOT NULL,
  `reporter_email` varchar(150) DEFAULT NULL,
  `police_letter` varchar(255) DEFAULT NULL,
  `match_status` enum('unmatched','matched','notified','collected') NOT NULL DEFAULT 'unmatched',
  `matched_doc_id` int(11) DEFAULT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lost_reports`
--

INSERT INTO `lost_reports` (`id`, `doc_type`, `sur_name`, `given_name`, `dob`, `gender`, `id_number`, `extra_field1`, `extra_field2`, `reporter_name`, `reporter_phone`, `reporter_email`, `police_letter`, `match_status`, `matched_doc_id`, `submitted_at`, `updated_at`) VALUES
(1, 'national_id', 'OBIN', 'IVAN', '2026-08-09', NULL, 'CM950', NULL, NULL, 'OBIN IVAN', '', NULL, NULL, 'matched', 18, '2026-08-09 09:12:05', NULL),
(2, 'national_id', 'OTOLO', 'SEDU', '2026-07-13', NULL, 'CM9505', NULL, NULL, 'OTOLO SEDU', '', NULL, NULL, 'matched', 1, '2026-08-15 08:35:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `match_alerts`
--

CREATE TABLE `match_alerts` (
  `id` int(11) NOT NULL,
  `lost_report_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `station` varchar(150) DEFAULT NULL,
  `alert_status` enum('new','admin_notified','owner_notified','payment_pending','paid','collected','closed','pending') NOT NULL DEFAULT 'new',
  `admin_approved` tinyint(1) NOT NULL DEFAULT 0,
  `admin_approved_by` varchar(150) DEFAULT NULL,
  `admin_approved_at` datetime DEFAULT NULL,
  `station_approved` tinyint(1) NOT NULL DEFAULT 0,
  `station_approved_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `match_alerts`
--

INSERT INTO `match_alerts` (`id`, `lost_report_id`, `document_id`, `station`, `alert_status`, `admin_approved`, `admin_approved_by`, `admin_approved_at`, `station_approved`, `station_approved_at`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 18, 'superadmin', 'collected', 1, 'superadmin', '2026-08-09 09:13:35', 0, NULL, NULL, '2026-08-09 09:12:05', '2026-08-09 09:15:35'),
(2, 1, 388, 'superadmin', 'new', 1, 'superadmin', '2026-08-12 18:00:11', 0, NULL, NULL, '2026-08-12 17:57:24', '2026-08-12 18:00:11'),
(3, 2, 1, 'Voice of Lango FM', 'collected', 0, NULL, NULL, 1, '2026-08-15 08:37:24', NULL, '2026-08-15 08:35:54', '2026-08-15 08:38:02');

-- --------------------------------------------------------

--
-- Table structure for table `national_ids`
--

CREATE TABLE `national_ids` (
  `national_id` int(11) NOT NULL,
  `sur_name` varchar(255) NOT NULL,
  `given_name` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `nin_number` varchar(100) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `front` varchar(255) NOT NULL,
  `back` varchar(255) NOT NULL,
  `user_action` varchar(100) NOT NULL,
  `reporter` varchar(100) NOT NULL,
  `date_found` varchar(100) NOT NULL,
  `uploader_n` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `national_ids`
--

INSERT INTO `national_ids` (`national_id`, `sur_name`, `given_name`, `dob`, `nin_number`, `gender`, `front`, `back`, `user_action`, `reporter`, `date_found`, `uploader_n`) VALUES
(388, 'OBIN', 'IVAN', '2026-08-09', 'CM950', 'male', 'https://irecover.site/uploads/DOC_FRONT_UOqVS8_1786266637.png', '', 'Found', 'superadmin', '2026-08-09 / 09:10:37 AM', 0),
(389, 'KOMAKECH', 'MOSES', '1998-04-10', 'CM98050103GG8F', 'male', 'https://irecover.site/uploads/DOC_FRONT_hPtgXR_1786557890.png', 'https://irecover.site/uploads/DOC_BACK_uFrEUc_1786557890.png', 'Found', 'Public', '2026-08-12 / 06:04:50 PM', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `type` enum('match_found','payment_confirmed','doc_collected','new_report','new_upload') NOT NULL,
  `target_role` enum('super_admin','admin','station','all') NOT NULL DEFAULT 'admin',
  `target_user` varchar(150) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `ref_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `target_role`, `target_user`, `message`, `is_read`, `ref_id`, `created_at`) VALUES
(1, 'new_upload', 'station', 'Voice of Lango FM', 'New national_id uploaded by Voice of Lango FM.', 1, 1, '2026-07-13 16:04:07'),
(2, 'match_found', 'admin', NULL, 'Public search MATCHED: OTOLO SEDU (national_id). Searcher phone: 0777676206. Held at: Voice of Lango FM', 0, 1, '2026-07-13 17:03:05'),
(3, 'new_upload', 'station', 'superadmin', 'New national_id uploaded by superadmin.', 0, 18, '2026-08-09 09:10:37'),
(4, 'match_found', 'admin', NULL, 'Owner searched and found their national_id (CM950). Awaiting your approval. Alert #1.', 0, 1, '2026-08-09 09:12:05'),
(5, 'match_found', 'station', 'superadmin', 'An owner found a match for a national_id you\'re holding. Awaiting your confirmation. Alert #1.', 0, 1, '2026-08-09 09:12:05'),
(6, 'match_found', 'admin', NULL, 'Owner searched and found their national_id (CM950). Awaiting your approval. Alert #2.', 0, 2, '2026-08-12 17:57:24'),
(7, 'match_found', 'station', 'superadmin', 'An owner found a match for a national_id you\'re holding. Awaiting your confirmation. Alert #2.', 0, 2, '2026-08-12 17:57:24'),
(8, 'new_upload', 'station', 'Public', 'New national_id uploaded by Public.', 0, 19, '2026-08-12 18:04:50'),
(9, 'match_found', 'admin', NULL, 'Owner searched and found their national_id (CM9505). Awaiting your approval. Alert #3.', 0, 3, '2026-08-15 08:35:54'),
(10, 'match_found', 'station', 'Voice of Lango FM', 'An owner found a match for a national_id you\'re holding. Awaiting your confirmation. Alert #3.', 0, 3, '2026-08-15 08:35:54'),
(11, 'doc_collected', 'admin', NULL, 'Document collected at station \'Voice of Lango FM\' by \'Sedrick\'. Alert #3.', 0, 3, '2026-08-15 08:38:02');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `match_alert_id` int(11) NOT NULL,
  `document_id` int(11) DEFAULT NULL,
  `payer_name` varchar(255) DEFAULT NULL,
  `payer_phone` varchar(20) NOT NULL,
  `id_number` varchar(150) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 10000.00,
  `currency` varchar(5) NOT NULL DEFAULT 'UGX',
  `payment_method` enum('mobile_money','cash','waived') NOT NULL DEFAULT 'mobile_money',
  `provider` enum('MTN','Airtel','other') DEFAULT 'MTN',
  `transaction_ref` varchar(150) DEFAULT NULL,
  `status` enum('initiated','pending','confirmed','failed') NOT NULL DEFAULT 'initiated',
  `initiated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `confirmed_at` datetime DEFAULT NULL,
  `verification_code` varchar(12) DEFAULT NULL,
  `download_allowed` tinyint(1) NOT NULL DEFAULT 0,
  `used_at` datetime DEFAULT NULL,
  `iotec_transaction_id` varchar(64) DEFAULT NULL,
  `iotec_status` varchar(30) DEFAULT NULL,
  `station_commission` decimal(10,2) DEFAULT NULL,
  `callback_payload` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `search_log`
--

CREATE TABLE `search_log` (
  `id` int(11) NOT NULL,
  `doc_type` varchar(50) DEFAULT NULL,
  `search_name` varchar(255) DEFAULT NULL,
  `search_id_num` varchar(150) DEFAULT NULL,
  `searcher_phone` varchar(20) DEFAULT NULL,
  `result` enum('matched','not_found') NOT NULL DEFAULT 'not_found',
  `matched_doc_id` int(11) DEFAULT NULL,
  `searched_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `search_log`
--

INSERT INTO `search_log` (`id`, `doc_type`, `search_name`, `search_id_num`, `searcher_phone`, `result`, `matched_doc_id`, `searched_at`) VALUES
(1, 'national_id', 'OTOLO SEDU', 'CM9505', '', 'matched', 1, '2026-07-13 16:06:55'),
(2, 'national_id', 'OTOLO SEDU', 'CM95057', '', 'matched', 1, '2026-07-13 16:08:02'),
(3, 'national_id', 'OTOLO SEDU', 'CM95057', '', 'not_found', NULL, '2026-07-13 16:08:13'),
(4, 'national_id', 'OTOLO SEDU', 'CM9505', '', 'matched', 1, '2026-07-13 16:59:47'),
(5, 'national_id', 'OTOLO SEDU', 'CM9505', '', 'matched', 1, '2026-07-13 17:01:38'),
(6, 'national_id', 'OTOLO SEDU', 'CM9505', '', 'matched', 1, '2026-07-13 17:02:39'),
(7, 'national_id', 'OTOLO SEDU', 'CM9505', '0777676206', 'matched', 1, '2026-07-13 17:03:05'),
(8, 'national_id', 'OTOLO SEDU', 'CM9505', '', 'matched', 1, '2026-07-16 12:56:07'),
(9, 'national_id', 'OTOLO SEDU', 'CM9505', '', 'matched', 1, '2026-07-16 14:14:20'),
(10, 'national_id', 'OBIN IVAN', 'CM950', '', 'matched', 18, '2026-08-09 09:12:05'),
(11, 'national_id', 'OBIN IVAN', 'CM950', '', 'matched', 388, '2026-08-12 17:57:24'),
(12, 'national_id', 'OTOLO SEDU', 'CM9505', '', 'matched', 1, '2026-08-15 08:35:54');

-- --------------------------------------------------------

--
-- Table structure for table `student_ids`
--

CREATE TABLE `student_ids` (
  `student_id` int(11) NOT NULL,
  `sur_name` varchar(255) NOT NULL,
  `given_name` varchar(255) NOT NULL,
  `student_number` varchar(255) NOT NULL,
  `course` varchar(255) NOT NULL,
  `date_issued` date NOT NULL,
  `school` varchar(255) NOT NULL,
  `front` varchar(255) NOT NULL,
  `back` varchar(255) NOT NULL,
  `user_action` varchar(100) NOT NULL,
  `reporter` varchar(100) NOT NULL,
  `date_found` varchar(100) NOT NULL,
  `uploader_n` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `superadmins`
--

CREATE TABLE `superadmins` (
  `admin_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `superadmins`
--

INSERT INTO `superadmins` (`admin_id`, `name`, `password`) VALUES
(2, 'Kakebe Technologies Limited', '.');

-- --------------------------------------------------------

--
-- Table structure for table `user_documents`
--

CREATE TABLE `user_documents` (
  `id` int(11) NOT NULL,
  `document_type` varchar(255) DEFAULT NULL,
  `name_on_document` varchar(255) DEFAULT NULL,
  `id_number` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `collection_log`
--
ALTER TABLE `collection_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_doc_type` (`doc_type`),
  ADD KEY `idx_id_number` (`id_number`),
  ADD KEY `idx_sur_name` (`sur_name`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_reporter` (`reporter`);

--
-- Indexes for table `documents_legacy`
--
ALTER TABLE `documents_legacy`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `driving_permits`
--
ALTER TABLE `driving_permits`
  ADD PRIMARY KEY (`driver_id`);

--
-- Indexes for table `fee_config`
--
ALTER TABLE `fee_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `doc_type` (`doc_type`);

--
-- Indexes for table `found_documents`
--
ALTER TABLE `found_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `found_ids`
--
ALTER TABLE `found_ids`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lost_reports`
--
ALTER TABLE `lost_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lr_id_number` (`id_number`),
  ADD KEY `idx_lr_sur_name` (`sur_name`),
  ADD KEY `idx_lr_match` (`match_status`);

--
-- Indexes for table `match_alerts`
--
ALTER TABLE `match_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ma_lost` (`lost_report_id`),
  ADD KEY `idx_ma_doc` (`document_id`),
  ADD KEY `idx_ma_status` (`alert_status`);

--
-- Indexes for table `national_ids`
--
ALTER TABLE `national_ids`
  ADD PRIMARY KEY (`national_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notif_target` (`target_role`,`target_user`),
  ADD KEY `idx_notif_read` (`is_read`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_vcode` (`verification_code`),
  ADD KEY `idx_pay_phone` (`payer_phone`),
  ADD KEY `idx_pay_id_num` (`id_number`),
  ADD KEY `idx_pay_status` (`status`),
  ADD KEY `idx_iotec_txn` (`iotec_transaction_id`);

--
-- Indexes for table `search_log`
--
ALTER TABLE `search_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sl_result` (`result`);

--
-- Indexes for table `student_ids`
--
ALTER TABLE `student_ids`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `student_number` (`student_number`);

--
-- Indexes for table `superadmins`
--
ALTER TABLE `superadmins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `user_documents`
--
ALTER TABLE `user_documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_number` (`id_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `user_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `collection_log`
--
ALTER TABLE `collection_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `documents_legacy`
--
ALTER TABLE `documents_legacy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `driving_permits`
--
ALTER TABLE `driving_permits`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `fee_config`
--
ALTER TABLE `fee_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `found_documents`
--
ALTER TABLE `found_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `found_ids`
--
ALTER TABLE `found_ids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lost_reports`
--
ALTER TABLE `lost_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `match_alerts`
--
ALTER TABLE `match_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `national_ids`
--
ALTER TABLE `national_ids`
  MODIFY `national_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=390;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `search_log`
--
ALTER TABLE `search_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `student_ids`
--
ALTER TABLE `student_ids`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `superadmins`
--
ALTER TABLE `superadmins`
  MODIFY `admin_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_documents`
--
ALTER TABLE `user_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

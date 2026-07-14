-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 22, 2025 at 07:02 AM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u850523537_iRecoverDB`
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
  `email` varchar(100) NOT NULL,
  `district` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `type_of_entity` varchar(100) NOT NULL,
  `registered_at` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`user_id`, `user_name`, `password`, `number`, `email`, `district`, `address`, `type_of_entity`, `registered_at`) VALUES
(5, 'Voice of Lango FM', '123', 777676206, 'vol@irecover.info', 'Lira City', 'Lira City', 'Company', '2025-01-28 / 09:59:23 AM'),
(6, 'Qfm', '123', 777676206, 'qfm@irecover.info', 'Lira City', 'Lira City', 'Company', '2025-01-28 / 01:17:07 PM'),
(7, 'Voice of The Gospel', '123', 777676206, 'vog@irecover.info', 'Lira City', 'Lira City', 'Company', '2025-01-31 / 06:48:04 AM');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `document_type` varchar(50) DEFAULT NULL,
  `id_number` varchar(100) DEFAULT NULL,
  `id_name` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `document_photo` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `document_type`, `id_number`, `id_name`, `dob`, `document_photo`, `phone_number`, `email`, `created_at`) VALUES
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `national_ids`
--

INSERT INTO `national_ids` (`national_id`, `sur_name`, `given_name`, `dob`, `nin_number`, `gender`, `front`, `back`, `user_action`, `reporter`, `date_found`, `uploader_n`) VALUES
(7, 'OKELLO', 'DICKENS MAXWELL', '1990-05-05', 'CM90103100DLAH', 'male', 'NID_FrontRand_66_o0S7J.png', 'NID_BackRand_55_o0S7J.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:27:57 PM', 0),
(8, 'Okao', 'Bonny Uhuru', '1985-10-09', 'CM85074101548E', 'male', 'NID_FrontRand_66_ZIcaB.png', 'NID_BackRand_55_ZIcaB.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:28:20 PM', 0),
(9, 'KOMAKECH', 'RONALD', '1997-02-25', 'CM970631003G4C', 'male', 'NID_FrontRand_66_4NObh.png', 'NID_BackRand_55_4NObh.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:29:08 PM', 0),
(10, 'Oyo', 'Denis', '1986-03-07', 'CM86022105FYZK', 'male', 'NID_FrontRand_66_PuK9H.png', 'NID_BackRand_55_PuK9H.png', 'Found', 'Public', '2025-01-29 / 02:30:03 PM', 0),
(11, 'AYANG', 'EMMANUEL', '1996-04-08', 'CM9600110229AA', 'male', 'NID_FrontRand_66_dNNiC.png', 'NID_BackRand_55_dNNiC.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:30:42 PM', 0),
(12, 'AWINO', 'KATHERINE', '1996-03-02', 'CF960881019TZG', 'female', 'NID_FrontRand_66_2vblu.png', 'NID_BackRand_55_2vblu.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:32:22 PM', 0),
(13, 'OWANG', 'SAM', '1996-12-05', 'CM96086102E4CK', 'male', 'NID_FrontRand_66_ZUZuO.png', 'NID_BackRand_55_ZUZuO.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:32:22 PM', 0),
(14, 'ACIO', 'SIDONIA', '1959-08-09', 'CF590571017RTD', 'female', 'NID_FrontRand_66_Aiey2.png', 'NID_BackRand_55_Aiey2.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:32:58 PM', 0),
(15, 'ONGOM', 'EMMANUEL', '1999-06-06', 'CM9900110CLMVK', 'male', 'NID_FrontRand_66_LOqjd.png', 'NID_BackRand_55_LOqjd.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:34:11 PM', 0),
(16, 'OBUYA', 'MOSES', '1967-02-20', 'CM67013108CJAC', 'male', 'NID_FrontRand_66_UXXu4.png', 'NID_BackRand_55_UXXu4.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:34:14 PM', 0),
(17, 'OKULLO', 'BENSON ONGWALA', '1992-11-25', 'CM920881021LMD', 'male', 'NID_FrontRand_66_iSCk4.png', 'NID_BackRand_55_iSCk4.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:34:21 PM', 0),
(18, 'EPAU', 'ISAAC', '1998-03-21', 'CM98074102ANQD', 'male', 'NID_FrontRand_66_9Fx71.png', 'NID_BackRand_55_9Fx71.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:35:00 PM', 0),
(19, 'Okwir', 'Robert', '1997-02-03', 'CM97074101WL2F', 'male', 'NID_FrontRand_66_p24Qv.png', 'NID_BackRand_55_p24Qv.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:35:05 PM', 0),
(20, 'ADUPA', 'ALEX', '1982-10-18', 'CM82001103AWNH', 'male', 'NID_FrontRand_66_1ZZAZ.png', 'NID_BackRand_55_1ZZAZ.png', 'Found', 'Public', '2025-01-29 / 02:35:13 PM', 0),
(21, 'ADERO', 'FOSCA JANE', '1990-05-08', 'CF9002210320TJ', 'male', 'NID_FrontRand_66_tyOIw.png', 'NID_BackRand_55_tyOIw.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:35:33 PM', 0),
(22, 'ALOBO', 'DENISH', '1996-06-01', 'CM96088100KYTG', 'male', 'NID_FrontRand_66_qsClq.png', 'NID_BackRand_55_qsClq.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:35:51 PM', 0),
(23, 'Obonyo', 'Bonny', '1982-10-05', 'CM82088101KYHL', 'male', 'NID_FrontRand_66_OtsR2.png', 'NID_BackRand_55_OtsR2.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:36:21 PM', 0),
(24, 'KITARA', 'DAVID', '1978-12-03', 'CM780851036V9D', 'male', 'NID_FrontRand_66_NGQNg.png', 'NID_BackRand_55_NGQNg.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:36:22 PM', 0),
(25, 'ODONGO', 'LAWRENCE', '1998-03-03', 'CM980881057LKD', 'male', 'NID_FrontRand_66_pDoLv.png', 'NID_BackRand_55_pDoLv.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:37:15 PM', 0),
(26, 'OJOK', 'PATRICK', '1974-02-18', 'CM74057101U0PF', 'male', 'NID_FrontRand_66_380Vn.png', 'NID_BackRand_55_380Vn.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:37:40 PM', 0),
(27, 'WAMBOSA', 'SAM', '1993-01-01', 'CM93051105583F', 'male', 'NID_FrontRand_66_kM8d2.png', 'NID_BackRand_55_kM8d2.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:37:41 PM', 0),
(28, 'EPAU', 'ISAAC', '1998-03-21', 'CM98074102ANQD', 'male', 'NID_FrontRand_66_FH1NM.png', 'NID_BackRand_55_FH1NM.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:37:49 PM', 0),
(29, 'OPIO', 'SAM', '1992-12-10', 'CM92088104QNHK', 'male', 'NID_FrontRand_66_kSmuj.png', 'NID_BackRand_55_kSmuj.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:38:20 PM', 0),
(30, 'ONENCAN', 'KENNEDY', '1994-04-14', 'CM94005102KUNA', 'male', 'NID_FrontRand_66_Fzz3g.png', 'NID_BackRand_55_Fzz3g.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:39:11 PM', 0),
(31, 'OMODI', 'SANTO', '1990-12-15', 'CM90088100AZ8J', 'male', 'NID_FrontRand_66_Xules.png', 'NID_BackRand_55_Xules.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:39:12 PM', 0),
(32, 'AYENA', 'FRANCIS', '1992-12-04', 'CM92086101WQKG', 'male', 'NID_FrontRand_66_99sFQ.png', 'NID_BackRand_55_99sFQ.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:39:30 PM', 0),
(33, 'OGWANG', 'FRANCIS', '1973-05-19', 'CM73088100EDUA', 'male', 'NID_FrontRand_66_own4y.png', 'NID_BackRand_55_own4y.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:39:37 PM', 0),
(34, 'AKELLO', 'BETTY', '1973-10-08', 'CF73022104403RH', 'female', 'NID_FrontRand_66_oPymu.png', 'NID_BackRand_55_oPymu.png', 'Found', 'Public', '2025-01-29 / 02:40:20 PM', 0),
(35, 'Woniala', 'Rogers', '1982-09-10', 'CM8205110541ZA', 'male', 'NID_FrontRand_66_t0HjW.png', 'NID_BackRand_55_t0HjW.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:40:30 PM', 0),
(36, 'OKWIR ', 'RONALD', '1996-10-09', 'CM96103102WJZD', 'male', 'NID_FrontRand_66_3Hovm.png', 'NID_BackRand_55_3Hovm.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:40:58 PM', 0),
(37, 'OCEN', 'COSMAS ADYEBO', '1995-09-10', 'CM95086102U2QG', 'male', 'NID_FrontRand_66_7LKMr.png', 'NID_BackRand_55_7LKMr.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:41:08 PM', 0),
(38, 'Okwir', 'Patrick ', '1985-04-25', 'CM85103103FGKD', 'male', 'NID_FrontRand_66_vWelA.png', 'NID_BackRand_55_vWelA.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:41:15 PM', 0),
(39, 'MAGANGA', 'JOHNMARY YAWE', '1980-01-29', 'CM800301065PCA', 'male', 'NID_FrontRand_66_gQKvG.png', 'NID_BackRand_55_gQKvG.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:41:26 PM', 0),
(40, 'OPWONYA', 'FRANCIS JERRY', '1994-12-04', 'CM940501006PTC', 'male', 'NID_FrontRand_66_QvLG3.png', 'NID_BackRand_55_QvLG3.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:42:16 PM', 0),
(41, 'AWANY', 'BONNY', '1988-08-08', 'CM88088102JF4G', 'male', 'NID_FrontRand_66_yJl9p.png', 'NID_BackRand_55_yJl9p.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:42:31 PM', 0),
(42, 'ODONGO', 'ALEX', '1982-06-03', 'CM82103102FJQL', 'male', 'NID_FrontRand_66_sCOPm.png', 'NID_BackRand_55_sCOPm.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:42:39 PM', 0),
(43, 'AGOA', 'PRISCA', '1997-05-20', 'CF97111102YDLJ', 'female', 'NID_FrontRand_66_N7hms.png', 'NID_BackRand_55_N7hms.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:42:59 PM', 0),
(44, 'OTIM', 'STEPHEN GERALD', '1996-10-03', 'CM960221050ADH', 'male', 'NID_FrontRand_66_20bKd.png', 'NID_BackRand_55_20bKd.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:43:34 PM', 0),
(45, 'KOLI', 'ANNA', '1985-01-29', 'CF80001104LKYH', 'male', 'NID_FrontRand_66_HJzRo.png', 'NID_BackRand_55_HJzRo.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:43:42 PM', 0),
(46, 'OGWANG', 'DANIEL', '1996-12-24', 'CM961031009FNC', 'male', 'NID_FrontRand_66_XdpJv.png', 'NID_BackRand_55_XdpJv.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:44:29 PM', 0),
(47, 'OYANG', 'EMMANUEL', '1995-06-06', 'CM95088101GC6A', 'male', 'NID_FrontRand_66_CSxGQ.png', 'NID_BackRand_55_CSxGQ.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:44:40 PM', 0),
(48, 'ONYANGA', 'BONNY BILLY', '1993-01-01', 'CM93022105DARD', 'male', 'NID_FrontRand_66_dQUDp.png', 'NID_BackRand_55_dQUDp.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:44:52 PM', 0),
(49, 'Oyo', 'Denis', '1986-09-07', 'CM86022105FYZK', 'male', 'NID_FrontRand_66_Eh3q1.png', 'NID_BackRand_55_Eh3q1.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:44:58 PM', 0),
(50, 'AUMA', 'ROBINA', '1997-02-07', 'CF97092101NCPH', 'female', 'NID_FrontRand_66_SBtk3.png', 'NID_BackRand_55_SBtk3.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:45:52 PM', 0),
(51, 'BUKU', 'WALKER', '1992-03-21', 'CM92022106TWWJ', 'male', 'NID_FrontRand_66_ZWCf7.png', 'NID_BackRand_55_ZWCf7.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:45:54 PM', 0),
(52, 'SEMENTE', 'SOLOMON', '2000-07-17', 'CM00103105WM5G', 'male', 'NID_FrontRand_66_3ZMeg.png', 'NID_BackRand_55_3ZMeg.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:46:11 PM', 0),
(53, 'AMUGE', 'CISSY', '1996-02-12', 'CF96001101Q2G2G', 'female', 'NID_FrontRand_66_7oyzv.png', 'NID_BackRand_55_7oyzv.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:46:39 PM', 0),
(54, 'EGABU', 'TIMOTHY', '2005-09-06', 'CM05038107EWTE', 'male', 'NID_FrontRand_66_kOLFL.png', 'NID_BackRand_55_kOLFL.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:47:36 PM', 0),
(55, 'AKITE', 'LIVAROTA', '1992-01-06', 'CF92103104A04G', 'female', 'NID_FrontRand_66_DGJPs.png', 'NID_BackRand_55_DGJPs.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:47:58 PM', 0),
(56, 'EJANG ', 'SANTA', '1985-02-11', 'CF85057102KN8D', 'female', 'NID_FrontRand_66_jv4xM.png', 'NID_BackRand_55_jv4xM.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:48:10 PM', 0),
(57, 'Okello', 'Remmy Robin', '1993-09-05', 'CM93103102F94E', 'male', 'NID_FrontRand_66_5D12S.png', 'NID_BackRand_55_5D12S.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:48:28 PM', 0),
(58, 'OPITO', 'ERICK', '1991-01-01', 'CM910011006KWK', 'male', 'NID_FrontRand_66_kZOiz.png', 'NID_BackRand_55_kZOiz.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:48:46 PM', 0),
(59, 'AKULLU', 'NANCY ELIZABETH', '1998-05-13', 'CF9800510606TE', 'female', 'NID_FrontRand_66_zFO6U.png', 'NID_BackRand_55_zFO6U.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:48:49 PM', 0),
(60, 'AKITE', 'REBECCA', '1996-06-23', 'CF961031079DDJ', 'female', 'NID_FrontRand_66_jPbJW.png', 'NID_BackRand_55_jPbJW.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:49:49 PM', 0),
(61, 'OUNI', 'TONNY', '1993-06-07', 'CM93088102033G', 'male', 'NID_FrontRand_66_FDHQL.png', 'NID_BackRand_55_FDHQL.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:50:53 PM', 0),
(62, 'ETAP', 'LONA', '1998-02-08', 'CF98001103PUGD', 'female', 'NID_FrontRand_66_keo9x.png', 'NID_BackRand_55_keo9x.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:51:20 PM', 0),
(63, 'ALOBO', 'HARRIET', '2000-02-04', 'CF00074106HCAD', 'female', 'NID_FrontRand_66_WMPjQ.png', 'NID_BackRand_55_WMPjQ.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:51:48 PM', 0),
(64, 'OTILE', 'INNOCENT', '2000-03-28', 'CM00022107L19G', 'male', 'NID_FrontRand_66_6PIqr.png', 'NID_BackRand_55_6PIqr.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:52:00 PM', 0),
(65, 'OKELLO', 'AMBROSE', '1993-09-26', 'CM931031024JZJ', 'male', 'NID_FrontRand_66_oTjMR.png', 'NID_BackRand_55_oTjMR.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:52:38 PM', 0),
(66, 'ATOKE', 'HARRIET', '1984-08-09', 'CF84022104QXKE', 'female', 'NID_FrontRand_66_yH4UO.png', 'NID_BackRand_55_yH4UO.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:52:48 PM', 0),
(67, 'OKELLO', 'JASPHER', '1985-07-04', 'CM85076104K6ME', 'male', 'NID_FrontRand_66_plJos.png', 'NID_BackRand_55_plJos.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:53:19 PM', 0),
(68, 'ALUM', 'STELLA', '1996-05-04', 'CF960011058LAA', 'female', 'NID_FrontRand_66_rQR7V.png', 'NID_BackRand_55_rQR7V.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:53:35 PM', 0),
(69, 'OCHAN', 'AUGUSTINE ', '1998-02-05', 'CM980191034NMD', 'male', 'NID_FrontRand_66_vvzZp.png', 'NID_BackRand_55_vvzZp.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:53:43 PM', 0),
(70, 'Odongo', 'Jackson', '2000-12-09', 'CM000221091XZE', 'male', 'NID_FrontRand_66_z8E6U.png', 'NID_BackRand_55_z8E6U.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:53:57 PM', 0),
(71, 'AUMA', 'KETTY', '1978-06-21', 'CF78001102YD2G', 'female', 'NID_FrontRand_66_CPecK.png', 'NID_BackRand_55_CPecK.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:54:26 PM', 0),
(72, 'OKELLO', 'STEVEN', '1989-08-20', 'CM891031001EFA', 'male', 'NID_FrontRand_66_zF2qU.png', 'NID_BackRand_55_zF2qU.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:54:36 PM', 0),
(73, 'ODONGO', 'MORRIS', '1993-01-01', 'CM930881042ZWA', 'male', 'NID_FrontRand_66_VA9qG.png', 'NID_BackRand_55_VA9qG.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:54:56 PM', 0),
(74, 'AKAO', 'EUNICE', '1987-12-08', 'CF87022104Z9KJ', 'female', 'NID_FrontRand_66_3Rbls.png', 'NID_BackRand_55_3Rbls.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:55:04 PM', 0),
(75, 'KIZZA', 'ROBINSON', '0000-00-00', 'CM96022101QW6L', 'male', 'NID_FrontRand_66_mMMG9.png', 'NID_BackRand_55_mMMG9.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:56:27 PM', 0),
(76, 'ADONG', 'JOYCE', '1987-02-01', 'CF87057102FM8A', 'female', 'NID_FrontRand_66_jyg4q.png', 'NID_BackRand_55_jyg4q.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:56:33 PM', 0),
(77, 'ECAN', 'JACOB', '0000-00-00', 'CM91022100XHWG', 'male', 'NID_FrontRand_66_knIxe.png', 'NID_BackRand_55_knIxe.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:56:51 PM', 0),
(78, 'ODUR', 'MICHEAL', '0000-00-00', 'CM98022100LVJC', 'male', 'NID_FrontRand_66_j5A1X.png', 'NID_BackRand_55_j5A1X.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:56:57 PM', 0),
(79, 'ACEN', 'JACKOLINE', '0000-00-00', 'CF9508810138QH', 'female', 'NID_FrontRand_66_CLG2P.png', 'NID_BackRand_55_CLG2P.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:57:34 PM', 0),
(80, 'OKWIR', 'ASUMAN', '0000-00-00', 'CM61022101MT8A', 'male', 'NID_FrontRand_66_OUPJD.png', 'NID_BackRand_55_OUPJD.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:58:04 PM', 0),
(81, 'ODUR', 'SOLOMON TOMAS', '0000-00-00', 'CM920921033PED', 'male', 'NID_FrontRand_66_OVrR3.png', 'NID_BackRand_55_OVrR3.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:58:18 PM', 0),
(82, 'OJOK', 'JIMMY', '0000-00-00', 'CM87103103102GT2L', 'male', 'NID_FrontRand_66_e6A04.png', 'NID_BackRand_55_e6A04.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:58:22 PM', 0),
(83, 'OJOK', 'JIMMY', '0000-00-00', 'CM87103103102GT2L', 'male', 'NID_FrontRand_66_ifIxP.png', 'NID_BackRand_55_ifIxP.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:58:51 PM', 0),
(84, 'ATIM', 'MODESTA', '0000-00-00', 'CF96038102P1MH', 'female', 'NID_FrontRand_66_HUl1U.png', 'NID_BackRand_55_HUl1U.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 02:59:31 PM', 0),
(85, 'AKWERO', 'TEDDY', '0000-00-00', 'CF90097100X76J', 'female', 'NID_FrontRand_66_cooL7.png', 'NID_BackRand_55_cooL7.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 02:59:41 PM', 0),
(86, 'OGWANG', 'DOUGLAS', '1996-09-03', 'CM96103104MONE', 'male', 'NID_FrontRand_66_l9Jql.png', 'NID_BackRand_55_l9Jql.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:00:33 PM', 0),
(87, 'OGWANG', 'DOUGLAS', '1996-09-03', 'CM96103104MONE', 'male', 'NID_FrontRand_66_hfjrW.png', 'NID_BackRand_55_hfjrW.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:00:33 PM', 0),
(88, 'AWIO ', 'JUSTINE', '1995-01-22', 'CM9502210390UC', 'male', 'NID_FrontRand_66_mrPXK.png', 'NID_BackRand_55_mrPXK.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:00:55 PM', 0),
(89, 'ODONGO', 'FRANCIS', '1976-10-01', 'CM760051060COC', 'male', 'NID_FrontRand_66_svPPN.png', 'NID_BackRand_55_svPPN.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:01:28 PM', 0),
(90, 'OCHOLA', 'SAMUEL', '1996-05-25', 'CM960431026QXK', 'male', 'NID_FrontRand_66_QPxHc.png', 'NID_BackRand_55_QPxHc.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:01:33 PM', 0),
(91, 'ELONG', 'JOE BADDEN', '1989-10-09', 'CM89022102J27D', 'male', 'NID_FrontRand_66_pupfl.png', 'NID_BackRand_55_pupfl.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:02:02 PM', 0),
(92, 'OBONG', 'SAM', '1993-06-06', 'CM03001104AX1K', 'male', 'NID_FrontRand_66_mu1fU.png', 'NID_BackRand_55_mu1fU.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:02:42 PM', 0),
(93, 'ACAYO', 'DAFINE', '1992-01-15', 'CF92076105KVGD', 'female', 'NID_FrontRand_66_YThQt.png', 'NID_BackRand_55_YThQt.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:03:11 PM', 0),
(94, 'OYUKU', 'BONNY', '1993-05-10', 'CM93103100DYKA', 'male', 'NID_FrontRand_66_HA9Ml.png', 'NID_BackRand_55_HA9Ml.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:03:20 PM', 0),
(95, 'OJEE', 'TONNY', '0000-00-00', 'CM94022104F83K', 'male', 'NID_FrontRand_66_ez8y6.png', 'NID_BackRand_55_ez8y6.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:03:25 PM', 0),
(96, 'APILI', 'JUDITH', '1995-01-07', 'CF95074103W04H', 'female', 'NID_FrontRand_66_u4vjH.png', 'NID_BackRand_55_u4vjH.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:03:50 PM', 0),
(97, 'AMONO', 'FLAVIA', '1999-12-12', 'CF99022109C3XE', 'female', 'NID_FrontRand_66_YU7nV.png', 'NID_BackRand_55_YU7nV.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:04:01 PM', 0),
(98, 'AMONG', 'JULIET', '2001-08-20', 'CF01022109K6JJ', 'female', 'NID_FrontRand_66_1mfMg.png', 'NID_BackRand_55_1mfMg.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:04:22 PM', 0),
(99, 'NYEKO', 'CLAY', '1991-05-02', 'CM9107110EERG', 'male', 'NID_FrontRand_66_WWvfV.png', 'NID_BackRand_55_WWvfV.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:04:37 PM', 0),
(100, 'OKELLO', ' ROBBY', '1997-12-12', 'CM97103101GDVA', 'male', 'NID_FrontRand_66_maQAF.png', 'NID_BackRand_55_maQAF.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:04:38 PM', 0),
(101, 'OCEN', 'FELIX', '1984-12-07', 'CM841031033MID', 'male', 'NID_FrontRand_66_ucOel.png', 'NID_BackRand_55_ucOel.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:05:42 PM', 0),
(102, 'OJOK', 'JIMMY', '0000-00-00', 'CM87103103102GT2L', 'male', 'NID_FrontRand_66_GvKyb.png', 'NID_BackRand_55_GvKyb.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:05:49 PM', 0),
(103, 'OJOK', 'JIMMY', '0000-00-00', 'CM87103103102GT2L', 'male', 'NID_FrontRand_66_IjVIt.png', 'NID_BackRand_55_IjVIt.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:06:02 PM', 0),
(104, 'ADUPA', 'RONALD', '1995-08-16', 'CM95076103EOLH', 'male', 'NID_FrontRand_66_BsocV.png', 'NID_BackRand_55_BsocV.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:06:04 PM', 0),
(105, 'MUTEBI', 'MOHAMED', '1986-01-16', 'CM86024100C7KH', 'male', 'NID_FrontRand_66_Lkpby.png', 'NID_BackRand_55_Lkpby.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:06:04 PM', 0),
(106, 'OJOK', 'JIMMY', '0000-00-00', 'CM87103103102GT2L', 'male', 'NID_FrontRand_66_UtiDE.png', 'NID_BackRand_55_UtiDE.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:06:05 PM', 0),
(107, 'TABU', 'JAMES', '1983-08-14', 'CM830511031TXK', 'male', 'NID_FrontRand_66_DNdCa.png', 'NID_BackRand_55_DNdCa.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:06:17 PM', 0),
(108, 'MANYIREKI', 'CHRISTINE', '1983-12-14', 'CF830251011JEG', 'female', 'NID_FrontRand_66_qOlGW.png', 'NID_BackRand_55_qOlGW.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:06:43 PM', 0),
(109, 'AJOK', 'SANTA', '1973-01-01', 'CF730501022UYE', 'female', 'NID_FrontRand_66_1FiaH.png', 'NID_BackRand_55_1FiaH.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:06:48 PM', 0),
(110, 'ETUNGU', 'JAMES', '1982-12-19', 'CM82054101M1PJ', 'male', 'NID_FrontRand_66_cMwy2.png', 'NID_BackRand_55_cMwy2.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:07:29 PM', 0),
(111, 'OKELLO', 'TONNY', '1985-11-03', 'CM850221034FRL', 'male', 'NID_FrontRand_66_k7g19.png', 'NID_BackRand_55_k7g19.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:07:52 PM', 0),
(112, 'OJOK', 'RONALD', '1994-02-24', 'CM94103101PC8F', 'male', 'NID_FrontRand_66_bNFx0.png', 'NID_BackRand_55_bNFx0.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:08:09 PM', 0),
(113, 'AKWIR', 'SAMUEL', '1993-12-12', 'CM93022101CCQH', 'male', 'NID_FrontRand_66_MALnT.png', 'NID_BackRand_55_MALnT.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:08:20 PM', 0),
(114, 'KANAURA', 'MORRIS', '1983-10-25', 'CM830651012JYE', 'male', 'NID_FrontRand_66_w6fFT.png', 'NID_BackRand_55_w6fFT.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:09:22 PM', 0),
(115, 'ACEN', 'FLORENCE', '1996-04-05', 'CF96088100JX0J', 'female', 'NID_FrontRand_66_u1AMd.png', 'NID_BackRand_55_u1AMd.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:09:35 PM', 0),
(116, 'OPIO', 'AMBROSE', '1992-01-03', 'CM92022103XCGH', 'male', 'NID_FrontRand_66_VtCFn.png', 'NID_BackRand_55_VtCFn.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:09:39 PM', 0),
(117, 'LUBINGA', 'STEPHEN', '1999-05-21', 'CM9902310C6PGJ', 'male', 'NID_FrontRand_66_9oQ3R.png', 'NID_BackRand_55_9oQ3R.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:09:49 PM', 0),
(118, 'OBUA', 'FELIX GEORGE', '1985-08-05', 'CM840051063P0A', 'male', 'NID_FrontRand_66_BZFJ4.png', 'NID_BackRand_55_BZFJ4.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:10:28 PM', 0),
(119, 'LUBINGA', 'STEPHEN', '1999-05-21', 'CM9902310C6PGJ', 'male', 'NID_FrontRand_66_rWHc0.png', 'NID_BackRand_55_rWHc0.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:10:40 PM', 0),
(120, 'ODONGO', 'CRISTOPHER', '1995-09-17', 'CM95103102CPYF', 'male', 'NID_FrontRand_66_p2tO7.png', 'NID_BackRand_55_p2tO7.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:10:42 PM', 0),
(121, 'ACEN', 'MILDREN', '2001-08-26', 'CF01110103SATH', 'female', 'NID_FrontRand_66_jcC39.png', 'NID_BackRand_55_jcC39.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:10:52 PM', 0),
(122, 'LUBINGA', 'STEPHEN', '1999-05-21', 'CM9902310C6PGJ', 'male', 'NID_FrontRand_66_TFO7g.png', 'NID_BackRand_55_TFO7g.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:11:18 PM', 0),
(123, 'OJOK', 'BOSCO', '1983-08-10', 'CM83050101VMXA', 'male', 'NID_FrontRand_66_4XzZp.png', 'NID_BackRand_55_4XzZp.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:11:30 PM', 0),
(124, 'OGWAL', 'GEOFFREY OYENA', '1984-08-06', 'CM840881048TQH', 'male', 'NID_FrontRand_66_cbEEm.png', 'NID_BackRand_55_cbEEm.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:12:18 PM', 0),
(125, 'ADONG', 'SUSAN', '1983-08-07', 'CF8307611028AFE', 'male', 'NID_FrontRand_66_s2Yn4.png', 'NID_BackRand_55_s2Yn4.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:14:52 PM', 0),
(126, 'APIO', 'HARRIET', '1994-03-04', 'CF94088102WV4D', 'female', 'NID_FrontRand_66_KqI4F.png', 'NID_BackRand_55_KqI4F.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:14:58 PM', 0),
(127, 'OKULLU', 'RAPHAEL', '1987-10-26', 'CM8707410393JL', 'male', 'NID_FrontRand_66_URuO1.png', 'NID_BackRand_55_URuO1.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:15:19 PM', 0),
(128, 'OBOGA', 'JOB', '2000-12-09', 'CM00103105WGJH', 'male', 'NID_FrontRand_66_AJPIi.png', 'NID_BackRand_55_AJPIi.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:15:22 PM', 0),
(129, 'ADONGO', 'SHARON', '0000-00-00', 'CF980221046DGF', 'female', 'NID_FrontRand_66_bqlyl.png', 'NID_BackRand_55_bqlyl.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:16:06 PM', 0),
(130, 'RUKUNDO', 'ALEX', '1997-08-01', 'CM970371060HTG', 'male', 'NID_FrontRand_66_PYplr.png', 'NID_BackRand_55_PYplr.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:16:20 PM', 0),
(131, 'ABAKA', 'ROBERT', '1998-03-04', 'CM980581035FJE', 'male', 'NID_FrontRand_66_xMPxR.png', 'NID_BackRand_55_xMPxR.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:16:30 PM', 0),
(132, 'OKELLO', 'STEPHEN', '1997-09-30', 'CM97103103RTXJ', 'male', 'NID_FrontRand_66_O5lgM.png', 'NID_BackRand_55_O5lgM.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:16:49 PM', 0),
(133, 'OBOTE', 'RONALD', '1995-10-02', 'CM95076AF7D', 'male', 'NID_FrontRand_66_sMW1b.png', 'NID_BackRand_55_sMW1b.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:17:07 PM', 0),
(134, 'Ayuli', 'George Washington ', '1971-09-11', 'CM710571025UPG', 'male', 'NID_FrontRand_66_5W2bP.png', 'NID_BackRand_55_5W2bP.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:17:53 PM', 0),
(135, 'KYDOBANI', 'FARUK', '1982-10-15', 'CM82035105RFG', 'male', 'NID_FrontRand_66_PF3Uq.png', 'NID_BackRand_55_PF3Uq.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:18:23 PM', 0),
(136, 'OPIO', 'EMMANUEL', '2002-02-06', 'CM02022108J3A', 'male', 'NID_FrontRand_66_n3JS8.png', 'NID_BackRand_55_n3JS8.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:18:23 PM', 0),
(137, 'AMONY', 'VIVIAN', '1996-04-07', 'CF96088103VKA', 'female', 'NID_FrontRand_66_Dr0RJ.png', 'NID_BackRand_55_Dr0RJ.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:18:26 PM', 0),
(138, 'OMAA', 'ALBERT', '1995-12-20', 'CM95088100X1KC', 'male', 'NID_FrontRand_66_PiJGV.png', 'NID_BackRand_55_PiJGV.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:18:27 PM', 0),
(139, 'OGWANG', 'ARON', '2001-11-07', 'CM01022108QD7D', 'male', 'NID_FrontRand_66_FVgMx.png', 'NID_BackRand_55_FVgMx.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:18:37 PM', 0),
(140, 'OGODO', 'ROBSON', '1991-12-04', 'CM91001101WN2H', 'male', 'NID_FrontRand_66_3aTBT.png', 'NID_BackRand_55_3aTBT.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:19:31 PM', 0),
(141, 'ODYEK', 'JAMES', '1989-12-24', 'CM89103100PAUE', 'male', 'NID_FrontRand_66_KkBMh.png', 'NID_BackRand_55_KkBMh.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:19:36 PM', 0),
(142, 'OBONG', 'CALVIN', '1982-01-01', 'CM82103101189A', 'male', 'NID_FrontRand_66_N496m.png', 'NID_BackRand_55_N496m.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:19:49 PM', 0),
(143, 'ORUTTE', 'BENAD', '1992-01-15', 'CM92111101Z7NJ', 'male', 'NID_FrontRand_66_j1OC3.png', 'NID_BackRand_55_j1OC3.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:19:52 PM', 0),
(144, 'AGOMA', 'JOSEPHINE', '1994-06-24', 'CF94088103AQ2F', 'female', 'NID_FrontRand_66_uUQNI.png', 'NID_BackRand_55_uUQNI.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:20:37 PM', 0),
(145, 'ANGOM', 'BRENDA', '1994-06-17', 'CF940011053T8G', 'male', 'NID_FrontRand_66_FW82M.png', 'NID_BackRand_55_FW82M.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:20:40 PM', 0),
(146, 'ODONGO', 'KENETH', '1985-05-05', 'CM85074101T6DD', 'male', 'NID_FrontRand_66_g3rPR.png', 'NID_BackRand_55_g3rPR.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:21:04 PM', 0),
(147, 'OLEL', 'BONNY', '0000-00-00', 'CM97001100942E', 'male', 'NID_FrontRand_66_XS41a.png', 'NID_BackRand_55_XS41a.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:21:07 PM', 0),
(148, 'ALWALA', 'ANDREW', '1994-05-14', 'CM94076102QMFK', 'male', 'NID_FrontRand_66_NOrhx.png', 'NID_BackRand_55_NOrhx.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:21:50 PM', 0),
(149, 'OKELLO', 'TONNY OJOK', '1977-11-28', 'CM77057101RRJC', 'male', 'NID_FrontRand_66_XgjX2.png', 'NID_BackRand_55_XgjX2.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:21:50 PM', 0),
(150, 'OLUKA', 'IVAN', '1994-07-27', 'CM94108100WWWFD', 'male', 'NID_FrontRand_66_eYqkE.png', 'NID_BackRand_55_eYqkE.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:22:13 PM', 0),
(151, 'ETUC', 'MICHAEL', '1990-11-04', 'CM90001106ER2F', 'male', 'NID_FrontRand_66_KgMbf.png', 'NID_BackRand_55_KgMbf.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:22:14 PM', 0),
(152, 'AKULU', 'LILLY', '1974-01-01', 'CF74022100T5PA', 'female', 'NID_FrontRand_66_qWqk9.png', 'NID_BackRand_55_qWqk9.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:22:48 PM', 0),
(153, 'ODONGO ', 'RICHARD', '1988-10-16', 'CM88038108X2HD', 'male', 'NID_FrontRand_66_9tMEO.png', 'NID_BackRand_55_9tMEO.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:22:53 PM', 0),
(154, 'NAMBASI', 'SAMAILI', '1998-08-06', 'CM98051102V26C', 'male', 'NID_FrontRand_66_PUNwJ.png', 'NID_BackRand_55_PUNwJ.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:23:03 PM', 0),
(155, 'Okello ', 'Daniel', '1988-10-17', 'CM88103102ZD6G', 'male', 'NID_FrontRand_66_0b1J2.png', 'NID_BackRand_55_0b1J2.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:23:08 PM', 0),
(156, 'OMARA', 'JACKSON', '1999-10-15', 'CM99022109DTPJ', 'male', 'NID_FrontRand_66_WgbIJ.png', 'NID_BackRand_55_WgbIJ.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:23:35 PM', 0),
(157, 'ONENCAN', 'VINCENT KILAMA', '1986-10-05', 'CM86050103G5AD', 'male', 'NID_FrontRand_66_S8hi5.png', 'NID_BackRand_55_S8hi5.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:23:54 PM', 0),
(158, 'AKELLO', 'LUCY', '1994-01-01', 'CM94088103NCWL', 'female', 'NID_FrontRand_66_rByWy.png', 'NID_BackRand_55_rByWy.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:24:00 PM', 0),
(159, 'ALIRA', 'REBECCA', '2002-03-25', 'CF0207410705ML', 'female', 'NID_FrontRand_66_azYLe.png', 'NID_BackRand_55_azYLe.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:24:27 PM', 0),
(160, 'NYANG', 'BONNIFACE', '1996-04-12', 'CM960881034GWC', 'male', 'NID_FrontRand_66_SO9mc.png', 'NID_BackRand_55_SO9mc.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:24:30 PM', 0),
(161, 'NAMBUYA', 'CATHERINE', '1996-12-25', 'CF96026106CQGE', 'female', 'NID_FrontRand_66_EVayM.png', 'NID_BackRand_55_EVayM.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:24:52 PM', 0),
(162, 'ODONGO', 'JASTIN', '1998-02-01', 'CM98001108YUWJ', 'male', 'NID_FrontRand_66_QgitO.png', 'NID_BackRand_55_QgitO.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:25:11 PM', 0),
(163, 'OPIO', 'TONNY ODUR', '1994-08-18', 'CM941031044LJK', 'male', 'NID_FrontRand_66_NzJZr.png', 'NID_BackRand_55_NzJZr.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:25:17 PM', 0),
(164, 'NAGUDI', 'ANNET', '1993-05-07', 'CF93026101F95D', 'female', 'NID_FrontRand_66_jvtDK.png', 'NID_BackRand_55_jvtDK.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:25:54 PM', 0),
(165, 'OBAA', 'DANIEL', '1992-03-23', 'CM92088102UN6L', 'male', 'NID_FrontRand_66_u2hNK.png', 'NID_BackRand_55_u2hNK.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:25:58 PM', 0),
(166, 'MAWEREWE', 'ZAAKE', '1994-02-24', 'CM94008107XMEF', 'male', 'NID_FrontRand_66_2B2hk.png', 'NID_BackRand_55_2B2hk.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:26:17 PM', 0),
(167, 'ATIM', 'JACKOLIN', '1996-12-10', 'CM960881040RUD', 'female', 'NID_FrontRand_66_CEeVR.png', 'NID_BackRand_55_CEeVR.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:26:23 PM', 0),
(168, 'OGWANG', 'DENIS', '1987-01-01', 'CM87022105Q4GG', 'male', 'NID_FrontRand_66_JWUlB.png', 'NID_BackRand_55_JWUlB.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:26:25 PM', 0),
(169, 'WORI', 'EUNICE', '1996-03-01', 'CF96039107PQUL', 'male', 'NID_FrontRand_66_daK49.png', 'NID_BackRand_55_daK49.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:27:02 PM', 0),
(170, 'OUME', 'FELIX', '1997-08-23', 'CM970011059CXE', 'male', 'NID_FrontRand_66_oXXGg.png', 'NID_BackRand_55_oXXGg.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:27:21 PM', 0),
(171, 'OCIMWA', 'STEPHEN SIMON', '1969-04-13', 'CM690431005X1D', 'male', 'NID_FrontRand_66_KvYda.png', 'NID_BackRand_55_KvYda.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:27:25 PM', 0),
(172, 'OKWIR', 'BRIAN BONIFACE', '1998-01-01', 'CM9805010264PF', 'male', 'NID_FrontRand_66_Karzd.png', 'NID_BackRand_55_Karzd.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:27:30 PM', 0),
(173, 'MUNU', 'SAMUEL', '1979-12-05', 'CM790881000MPF', 'male', 'NID_FrontRand_66_txzdI.png', 'NID_BackRand_55_txzdI.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:27:38 PM', 0),
(174, 'OGWAL', 'GORDON', '1995-07-15', 'CM95103100T9WC', 'male', 'NID_FrontRand_66_4tR53.png', 'NID_BackRand_55_4tR53.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:27:54 PM', 0),
(175, 'OGWAL', 'GORDON', '1995-07-15', 'CM95103100T9WC', 'male', 'NID_FrontRand_66_fJVNS.png', 'NID_BackRand_55_fJVNS.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:28:29 PM', 0),
(176, 'NONO', 'INNOCENT', '2002-05-05', 'CMO2103107K18J', 'male', 'NID_FrontRand_66_yDujd.png', 'NID_BackRand_55_yDujd.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:29:07 PM', 0),
(177, 'OGWANG', 'ABDU RASHID', '1991-06-26', 'CM91057102NWCD', 'male', 'NID_FrontRand_66_iO1or.png', 'NID_BackRand_55_iO1or.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:29:17 PM', 0),
(178, 'AYOO', 'STELLA', '1982-05-10', 'CF82022102GJWG', 'female', 'NID_FrontRand_66_k5SYV.png', 'NID_BackRand_55_k5SYV.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:29:24 PM', 0),
(179, 'OKELLO', 'PETER', '1979-08-27', 'CM79022100XK0K', 'male', 'NID_FrontRand_66_Nxh8Y.png', 'NID_BackRand_55_Nxh8Y.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:30:23 PM', 0),
(180, 'OGWAL', 'GORDON', '1995-07-15', 'CM95103100T9WC', 'male', 'NID_FrontRand_66_s4VUV.png', 'NID_BackRand_55_s4VUV.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:30:31 PM', 0),
(181, 'AWINO', 'EDDY', '1992-08-08', 'CF92103103GDRA', 'female', 'NID_FrontRand_66_aVhGC.png', 'NID_BackRand_55_aVhGC.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:30:32 PM', 0),
(182, 'OLOYA ', 'SANTO', '1958-03-15', 'CM58005101PFMA', 'male', 'NID_FrontRand_66_x8qlf.png', 'NID_BackRand_55_x8qlf.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:31:12 PM', 0),
(183, 'ODYEK', 'SAM', '1992-12-01', 'CM920571029Q7H', 'male', 'NID_FrontRand_66_wybXo.png', 'NID_BackRand_55_wybXo.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:31:19 PM', 0),
(184, 'ADERA', 'AGNES', '1990-05-03', 'CF900881025R5H', 'female', 'NID_FrontRand_66_BljnA.png', 'NID_BackRand_55_BljnA.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:31:39 PM', 0),
(185, 'OGWAL', 'GORDON', '1995-07-15', 'CM95103100T9WC', 'male', 'NID_FrontRand_66_9JVLg.png', 'NID_BackRand_55_9JVLg.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:31:41 PM', 0),
(186, 'ADONGO', 'DANIELA', '1997-07-01', 'CF970221062ZGC', 'female', 'NID_FrontRand_66_FqVUF.png', 'NID_BackRand_55_FqVUF.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:31:46 PM', 0),
(187, 'WACHA', 'MIKE ELMON', '1995-08-26', 'CM95022105HH2A', 'male', 'NID_FrontRand_66_h2APq.png', 'NID_BackRand_55_h2APq.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:31:53 PM', 0),
(188, 'OCEN', 'STEVEN', '1990-12-05', 'CM900011050UPL', 'male', 'NID_FrontRand_66_2Yozb.png', 'NID_BackRand_55_2Yozb.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:32:47 PM', 0),
(189, 'EJANG', 'EVALIN', '1995-05-15', 'CF851031009HNF', 'female', 'NID_FrontRand_66_Vu7rk.png', 'NID_BackRand_55_Vu7rk.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:32:49 PM', 0),
(190, 'AKOT ', 'STELLA ROSE', '1995-01-01', 'CF93111100A52D', 'male', 'NID_FrontRand_66_BT3sy.png', 'NID_BackRand_55_BT3sy.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:33:07 PM', 0),
(191, 'OKELLO', 'JOLLY', '1988-07-12', 'CM88103102RAFC', 'male', 'NID_FrontRand_66_PsWI1.png', 'NID_BackRand_55_PsWI1.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:33:53 PM', 0),
(192, 'OYURU', 'MICHEAL BENJAMIN', '1996-12-29', 'CM96001102EAQL', 'male', 'NID_FrontRand_66_aQHOI.png', 'NID_BackRand_55_aQHOI.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:34:04 PM', 0),
(193, 'AMONY', 'SARAH', '1982-10-24', 'CF82076103G27E', 'female', 'NID_FrontRand_66_1ch1W.png', 'NID_BackRand_55_1ch1W.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:34:24 PM', 0),
(194, 'APIO', 'SCOVIA DIANER', '1997-11-25', 'CF97022102VK3F', 'male', 'NID_FrontRand_66_bedl3.png', 'NID_BackRand_55_bedl3.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:34:36 PM', 0),
(195, 'AKONGO', 'MORINE', '1997-02-10', 'CF97057101ZMDL', 'female', 'NID_FrontRand_66_jCOpU.png', 'NID_BackRand_55_jCOpU.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:35:15 PM', 0),
(196, 'OKELLO', 'TONNY ABOCE ', '1987-08-11', 'CM87001104RTML', 'male', 'NID_FrontRand_66_hHjDe.png', 'NID_BackRand_55_hHjDe.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:36:23 PM', 0),
(197, 'OGWAL', 'BONIFACE', '1994-05-15', 'CM94974102FHYL', 'male', 'NID_FrontRand_66_wg3ZD.png', 'NID_BackRand_55_wg3ZD.png', 'Found', 'Voice of Lango Fm', '2025-01-29 / 03:37:13 PM', 0),
(198, 'APILI', 'POLLY', '1882-12-22', 'CF82022103KX4D', 'female', 'NID_FrontRand_66_O92sc.png', 'NID_BackRand_55_O92sc.png', 'Found', 'Voice of Lango FM', '2025-01-29 / 03:37:40 PM', 0),
(199, 'KIA ', 'SILVIA', '1978-04-15', 'CF78103103C8TJ', 'female', 'NID_FrontRand_66_WZyQX.png', 'NID_BackRand_55_WZyQX.png', 'Found', 'Voice of Lango Fm', '2025-01-30 / 06:48:25 AM', 0),
(200, 'ACOPE', 'WALTER', '2000-02-27', 'CM000741056EAG', 'male', 'NID_FrontRand_66_rfbyf.png', 'NID_BackRand_55_rfbyf.png', 'Found', 'Voice of Lango Fm', '2025-01-30 / 06:55:57 AM', 0),
(201, 'APIO', 'BABRA', '1994-09-09', 'CF94103100U15L', 'female', 'NID_FrontRand_66_9aPhs.png', 'NID_BackRand_55_9aPhs.png', 'Found', 'Voice of Lango Fm', '2025-01-30 / 06:57:39 AM', 0),
(202, 'ONGWEN', 'JOSHUA', '1995-07-05', 'CM95088102P74G', 'male', 'NID_FrontRand_66_stqqR.png', 'NID_BackRand_55_stqqR.png', 'Found', 'Public', '2025-01-30 / 06:58:35 AM', 0),
(203, 'EBUKU', 'ROBERT', '1981-04-10', 'CM8108810352XC', 'male', 'NID_FrontRand_66_V2hgo.png', 'NID_BackRand_55_V2hgo.png', 'Found', 'Voice of Lango FM', '2025-01-30 / 07:00:31 AM', 0),
(204, 'NGURA', 'GEOFFERY ELONG', '1997-03-20', 'CM970571009R6C', 'male', 'NID_FrontRand_66_Fmdjj.png', 'NID_BackRand_55_Fmdjj.png', 'Found', 'Voice of Lango FM', '2025-01-30 / 07:01:51 AM', 0),
(205, 'ABWANGO', 'SAM', '1995-03-05', 'CM95086101EV1F', 'male', 'NID_FrontRand_66_iOMa6.png', 'NID_BackRand_55_iOMa6.png', 'Found', 'Voice of Lango FM', '2025-01-30 / 07:02:47 AM', 0),
(206, 'AIRO', 'SHARON', '1998-04-30', 'CM980541005GTF', 'female', 'NID_FrontRand_66_JeHDg.png', 'NID_BackRand_55_JeHDg.png', 'Found', 'Voice of Lango FM', '2025-01-30 / 07:05:34 AM', 0),
(207, 'ACAN', 'CHRISTINE', '1979-11-29', 'CF79001104YFUF', 'female', 'NID_FrontRand_66_Rv7aC.png', 'NID_BackRand_55_Rv7aC.png', 'Found', 'Voice of Lango FM', '2025-01-30 / 07:07:21 AM', 0),
(208, 'EMOL', 'JASPHER DIZASTER', '1987-02-02', 'CM87076102RXGL', 'male', 'NID_FrontRand_66_smUCa.png', 'NID_BackRand_55_smUCa.png', 'Found', 'Voice of Lango FM', '2025-01-30 / 07:08:33 AM', 0),
(209, 'OBUA', 'EMMANUEL', '1995-12-25', 'CM95022103D3HG', 'male', 'NID_FrontRand_66_lItQ3.png', 'NID_BackRand_55_lItQ3.png', 'Found', 'Voice of Lango FM', '2025-01-30 / 07:09:43 AM', 0),
(210, 'OWANI', 'MOSES', '1995-05-25', 'CM950761069HKD', 'male', 'NID_FrontRand_66_LCd1F.png', 'NID_BackRand_55_LCd1F.png', 'Found', 'Voice of Lango Fm', '2025-01-30 / 07:19:17 AM', 0),
(211, 'AKIDE', 'HELGA', '1997-11-19', 'CF970741018VEG', 'female', 'NID_FrontRand_66_ydrx2.png', 'NID_BackRand_55_ydrx2.png', 'Found', 'Voice of Lango Fm', '2025-01-30 / 07:23:56 AM', 0),
(212, 'BUKU', 'WALKER', '1992-03-21', 'CM92022106TWWJ', 'male', 'NID_FrontRand_66_GOzfX.png', 'NID_BackRand_55_GOzfX.png', 'Found', 'Voice of Lango Fm', '2025-01-30 / 07:25:38 AM', 0),
(213, 'KOLI', 'ANNA', '1980-12-11', 'CF80001104LKYH', 'female', 'NID_FrontRand_66_mSBab.png', 'NID_BackRand_55_mSBab.png', 'Found', 'Voice of Lango Fm', '2025-01-30 / 07:27:24 AM', 0),
(214, 'OKWIR', 'PATRICK', '1985-04-25', 'CM85103103FGKD', 'male', 'NID_FrontRand_66_EhAXs.png', 'NID_BackRand_55_EhAXs.png', 'Found', 'Voice of Lango Fm', '2025-01-30 / 07:29:45 AM', 0),
(215, 'OKAO', 'BONNY UHURU', '1985-10-09', 'CM85074101548E', 'male', 'NID_FrontRand_66_OojwB.png', 'NID_BackRand_55_OojwB.png', 'Found', 'Voice of Lango Fm', '2025-01-30 / 07:31:17 AM', 0),
(216, 'OBUA', 'ISAAC', '1996-08-05', 'CM96022102U69E', 'male', 'NID_FrontRand_66_7jEep.png', 'NID_BackRand_55_7jEep.png', 'Found', 'Voice of Lango Fm', '2025-01-30 / 07:33:04 AM', 0),
(217, 'OKELLO', 'DANIEL', '1993-08-15', 'CM93022102AQAL', 'male', 'NID_FrontRand_66_l3ERJ.png', 'NID_BackRand_55_l3ERJ.png', 'Found', 'VOG', '2025-01-31 / 07:19:28 AM', 0),
(218, 'OGWANG', 'HENDRY', '1997-09-11', 'CM971271008HXJ', 'male', 'NID_FrontRand_66_ObTgq.png', 'NID_BackRand_55_ObTgq.png', 'Found', 'VOG', '2025-01-31 / 07:21:19 AM', 0),
(219, 'ODIO', 'LAZARUS', '1997-01-09', 'CM97022103QMQA', 'male', 'NID_FrontRand_66_mQeCT.png', 'NID_BackRand_55_mQeCT.png', 'Found', 'VOG', '2025-01-31 / 07:22:45 AM', 0),
(220, 'AKELLO', 'TEDDY', '1960-11-10', 'CF60074101XPJJ', 'female', 'NID_FrontRand_66_hmXzk.png', 'NID_BackRand_55_hmXzk.png', 'Found', 'VOG', '2025-01-31 / 07:24:17 AM', 0),
(221, 'KALE', 'LAWRENCE', '1986-05-23', 'CM86001100ZK7F', 'male', 'NID_FrontRand_66_tZlbK.png', 'NID_BackRand_55_tZlbK.png', 'Found', 'VOG', '2025-01-31 / 07:25:31 AM', 0),
(222, 'AKELLO', 'DORCUS', '1993-10-11', 'CF93088103J2NJ', 'female', 'NID_FrontRand_66_tFmUH.png', 'NID_BackRand_55_tFmUH.png', 'Found', 'VOG', '2025-01-31 / 07:27:12 AM', 0),
(223, 'OKWARA', 'NICHOLAS', '1995-11-25', 'CM95042106RA9C', 'male', 'NID_FrontRand_66_nD3Ec.png', 'NID_BackRand_55_nD3Ec.png', 'Found', 'VOG', '2025-01-31 / 07:29:41 AM', 0),
(224, 'OCEN', 'FRANCIS', '1996-07-09', 'CM96001100667F', 'male', 'NID_FrontRand_66_fXTKv.png', 'NID_BackRand_55_fXTKv.png', 'Found', 'VOG', '2025-01-31 / 07:30:31 AM', 0),
(225, 'APIO', 'LILY', '1975-08-04', 'CF75111101RUAE', 'female', 'NID_FrontRand_66_9QVz7.png', 'NID_BackRand_55_9QVz7.png', 'Found', 'VOG', '2025-01-31 / 07:31:20 AM', 0),
(226, 'APIO', 'ESTHER', '1982-04-12', 'CF82076104YW7F', 'female', 'NID_FrontRand_66_hm8tl.png', 'NID_BackRand_55_hm8tl.png', 'Found', 'VOG', '2025-01-31 / 07:33:53 AM', 0),
(227, 'OGWAL ', 'YUBU', '1989-12-12', 'CM890861012K7H', 'male', 'NID_FrontRand_66_ztDLz.png', 'NID_BackRand_55_ztDLz.png', 'Found', 'VOG', '2025-01-31 / 07:35:30 AM', 0),
(228, 'OPYENE', 'DANIEL', '1988-07-02', 'CM88001103C7KK', 'male', 'NID_FrontRand_66_f6ylX.png', 'NID_BackRand_55_f6ylX.png', 'Found', 'VOG', '2025-01-31 / 07:36:59 AM', 0),
(229, 'OCEN', 'FRANCIS', '1996-07-09', 'CM96001100667F', 'male', 'NID_FrontRand_66_yjuga.png', 'NID_BackRand_55_yjuga.png', 'Found', 'VOG', '2025-01-31 / 07:41:26 AM', 0),
(230, 'OKUMU', 'RIGAN OSCAR', '1986-05-12', 'CM86022102T83L', 'male', 'NID_FrontRand_66_f0Uh3.png', 'NID_BackRand_55_f0Uh3.png', 'Found', 'VOG', '2025-01-31 / 07:42:54 AM', 0),
(231, 'ALWEDO', 'NANCY', '1988-04-16', 'CF88074101YLND', 'female', 'NID_FrontRand_66_bI4xo.png', 'NID_BackRand_55_bI4xo.png', 'Found', 'VOG', '2025-01-31 / 07:44:20 AM', 0),
(232, 'ACAN', 'CAROLINE', '1988-05-07', 'CF98088102PK1J', 'female', 'NID_FrontRand_66_9l9o4.png', 'NID_BackRand_55_9l9o4.png', 'Found', 'VOG', '2025-01-31 / 07:45:55 AM', 0),
(233, 'OKUNU', 'KELLY', '1984-12-02', 'CM841031050TQL', 'male', 'NID_FrontRand_66_TX12b.png', 'NID_BackRand_55_TX12b.png', 'Found', 'Qfm', '2025-01-31 / 07:55:54 AM', 0),
(234, 'OMOKO', 'EMMANUEL', '1996-06-01', 'CM961031015E0F', 'male', 'NID_FrontRand_66_9VexA.png', 'NID_BackRand_55_9VexA.png', 'Found', 'Qfm', '2025-01-31 / 07:57:56 AM', 0),
(235, 'OGWOK', 'POLICARP', '1998-05-12', 'CM98076103N48J', 'male', 'NID_FrontRand_66_b51fT.png', 'NID_BackRand_55_b51fT.png', 'Found', 'Qfm', '2025-01-31 / 07:59:23 AM', 0),
(236, 'OCEN', 'GEOFFREY', '1988-09-09', 'CM8800110271FG', 'male', 'NID_FrontRand_66_0vuhd.png', 'NID_BackRand_55_0vuhd.png', 'Found', 'Qfm', '2025-01-31 / 08:01:29 AM', 0),
(237, 'AKELLO', 'JENIFER', '1975-08-11', 'CF75022103KLVL', 'female', 'NID_FrontRand_66_VqcVj.png', 'NID_BackRand_55_VqcVj.png', 'Found', 'Qfm', '2025-01-31 / 08:03:05 AM', 0),
(238, 'APIO', 'SAIDA', '1948-07-15', 'CF481031033TFA', 'female', 'NID_FrontRand_66_bdenQ.png', 'NID_BackRand_55_bdenQ.png', 'Found', 'Qfm', '2025-01-31 / 08:04:23 AM', 0),
(239, 'ETUWAT', 'JOEL PATRICK', '1992-09-08', 'CM920211009LEC', 'male', 'NID_FrontRand_66_onvkI.png', 'NID_BackRand_55_onvkI.png', 'Found', 'qfm', '2025-01-31 / 08:04:30 AM', 0),
(240, 'OGWAL', 'EMMANUEL', '1994-03-07', 'CM94074100A79G', 'male', 'NID_FrontRand_66_YBM9T.png', 'NID_BackRand_55_YBM9T.png', 'Found', 'Qfm', '2025-01-31 / 08:07:40 AM', 0),
(241, 'OMOKO', 'JIMMY', '1983-05-14', 'CM830221047GCJ', 'male', 'NID_FrontRand_66_VbdpV.png', 'NID_BackRand_55_VbdpV.png', 'Found', 'Public', '2025-01-31 / 08:07:57 AM', 0),
(242, 'APON', 'MARIA ANNA', '2002-09-07', 'CF02022107G40E', 'female', 'NID_FrontRand_66_hDXG1.png', 'NID_BackRand_55_hDXG1.png', 'Found', 'QFM', '2025-01-31 / 08:08:36 AM', 0),
(243, 'AMOLLO', 'LYDIA', '1990-05-05', 'CF900741014YDA', 'female', 'NID_FrontRand_66_tZeOy.png', 'NID_BackRand_55_tZeOy.png', 'Found', 'Qfm', '2025-01-31 / 08:09:39 AM', 0),
(244, 'OBOGA', 'VINCENT', '1970-07-01', 'CM70103103MELD', 'male', 'NID_FrontRand_66_yIj19.png', 'NID_BackRand_55_yIj19.png', 'Found', 'QFM', '2025-01-31 / 08:10:18 AM', 0),
(245, 'OBARO', 'RONALD', '1992-01-01', 'CM920221064Z9F', 'male', 'NID_FrontRand_66_QlZtF.png', 'NID_BackRand_55_QlZtF.png', 'Found', 'Qfm', '2025-01-31 / 08:10:52 AM', 0),
(246, 'LOLII', 'ROBERT', '1984-07-04', 'CM84085101PHJF', 'male', 'NID_FrontRand_66_T0Llb.png', 'NID_BackRand_55_T0Llb.png', 'Found', 'QFM', '2025-01-31 / 08:11:15 AM', 0),
(248, 'OKELLO', 'CALVIN', '1985-08-12', 'CM860011007FDJ', 'male', 'NID_FrontRand_66_m7m7l.png', 'NID_BackRand_55_m7m7l.png', 'Found', 'QFM', '2025-01-31 / 08:12:21 AM', 0),
(249, 'ETUM', 'MOSES', '1993-09-15', 'CM93001103NF9K', 'male', 'NID_FrontRand_66_VY6Yo.png', 'NID_BackRand_55_VY6Yo.png', 'Found', 'Qfm', '2025-01-31 / 08:13:35 AM', 0),
(250, 'OKODI', 'MOSES', '1972-08-08', 'CM72074100NUYH', 'male', 'NID_FrontRand_66_RqxyF.png', 'NID_BackRand_55_RqxyF.png', 'Found', 'QFM', '2025-01-31 / 08:14:32 AM', 0),
(251, 'AKWERO', 'LILLY', '1980-01-01', 'CF8008510220HD', 'female', 'NID_FrontRand_66_Kt3pB.png', 'NID_BackRand_55_Kt3pB.png', 'Found', 'QFM', '2025-01-31 / 08:14:46 AM', 0),
(253, 'NAINALI', 'MUSITAFAH', '1986-02-03', 'CM86102101WLQH', 'male', 'NID_FrontRand_66_SRKIy.png', 'NID_BackRand_55_SRKIy.png', 'Found', 'Qfm', '2025-01-31 / 08:16:23 AM', 0),
(255, 'ORYEM', 'PATRICK', '1998-08-01', 'CM98111102ZL7K', 'male', 'NID_FrontRand_66_hcLB5.png', 'NID_BackRand_55_hcLB5.png', 'Found', 'QFM', '2025-01-31 / 08:16:54 AM', 0),
(256, 'OJUKA', 'DENISH', '1997-01-01', 'CM97074102Y6GA', 'male', 'NID_FrontRand_66_c5OcG.png', 'NID_BackRand_55_c5OcG.png', 'Found', 'QFM', '2025-01-31 / 08:17:10 AM', 0),
(257, 'OKELLO', 'CEASER', '1998-04-24', 'CM9807610462HF', 'male', 'NID_FrontRand_66_1e4cG.png', 'NID_BackRand_55_1e4cG.png', 'Found', 'qfm', '2025-01-31 / 08:18:35 AM', 0),
(258, 'ARIKO', 'CHARLES', '1980-01-25', 'CM80021103C2QC', 'male', 'NID_FrontRand_66_oVHks.png', 'NID_BackRand_55_oVHks.png', 'Found', 'QFM', '2025-01-31 / 08:18:44 AM', 0),
(260, 'OKELLO', 'CEASER', '1998-04-24', 'CM9807610462HF', 'male', 'NID_FrontRand_66_eSiR3.png', 'NID_BackRand_55_eSiR3.png', 'Found', 'qfm', '2025-01-31 / 08:19:46 AM', 0),
(261, 'OPIO', 'JUDE TADEO', '1983-10-14', 'CM83043103GXFF', 'male', 'NID_FrontRand_66_KrXRR.png', 'NID_BackRand_55_KrXRR.png', 'Found', 'Qfm', '2025-01-31 / 08:20:39 AM', 0),
(262, 'EKUKA', 'PHONE', '1991-06-21', 'CM910861007Y1C', 'male', 'NID_FrontRand_66_MXyib.png', 'NID_BackRand_55_MXyib.png', 'Found', 'QFM', '2025-01-31 / 08:21:37 AM', 0),
(264, 'ETWALU', 'EMMANUEL', '1990-01-01', 'CM900571029C1H', 'male', 'NID_FrontRand_66_oFCLw.png', 'NID_BackRand_55_oFCLw.png', 'Found', 'Qfm', '2025-01-31 / 08:22:04 AM', 0),
(265, 'AUMA', 'SANDRA', '1998-08-11', 'CF89022106HGKK', 'female', 'NID_FrontRand_66_qR3MS.png', 'NID_BackRand_55_qR3MS.png', 'Found', 'qfm', '2025-01-31 / 08:23:24 AM', 0),
(266, 'EYURU', 'ISAAC ', '1993-07-02', 'CM930571000Q2J', 'male', 'NID_FrontRand_66_mQJIU.png', 'NID_BackRand_55_mQJIU.png', 'Found', 'Public', '2025-01-31 / 08:23:32 AM', 0),
(267, 'AKELLO', 'JUDITH', '1998-04-08', 'CF981031025GMA', 'female', 'NID_FrontRand_66_nmG9Z.png', 'NID_BackRand_55_nmG9Z.png', 'Found', 'Qfm', '2025-01-31 / 08:23:52 AM', 0),
(268, 'AKULLO ', 'JUSPANTI', '1945-01-06', 'CF45103103J9CH', 'female', 'NID_FrontRand_66_X1831.png', 'NID_BackRand_55_X1831.png', 'Found', 'QFM', '2025-01-31 / 08:24:11 AM', 0),
(269, 'AKULLU', 'JOAN', '1986-09-11', 'CF86001103NRYF', 'female', 'NID_FrontRand_66_j1Dwy.png', 'NID_BackRand_55_j1Dwy.png', 'Found', 'QFM', '2025-01-31 / 08:24:13 AM', 0),
(271, 'ERIPU', 'MICHAEL IRASA', '1982-01-23', 'CM920541001WCJ', 'male', 'NID_FrontRand_66_lcl9l.png', 'NID_BackRand_55_lcl9l.png', 'Found', 'Qfm', '2025-01-31 / 08:25:39 AM', 0),
(272, 'AKELLO', 'PRISCA', '1996-02-24', 'CF96001105G1LC', 'female', 'NID_FrontRand_66_6k1iO.png', 'NID_BackRand_55_6k1iO.png', 'pending', 'QFM', '2025-01-31 / 08:25:41 AM', 0),
(273, 'NAMANYA', 'ANDREW', '1995-05-25', 'CM95061103A5FC', 'male', 'NID_FrontRand_66_PGzss.png', 'NID_BackRand_55_PGzss.png', 'Found', 'QFM', '2025-01-31 / 08:26:03 AM', 0),
(274, 'ACENG', 'OLIVER', '1985-06-11', 'CF85022102LMKA', 'female', 'NID_FrontRand_66_xRJBl.png', 'NID_BackRand_55_xRJBl.png', 'Found', 'qfm', '2025-01-31 / 08:26:24 AM', 0),
(276, 'ACENG', 'OLIVER', '1985-06-11', 'CF85022102LMKA', 'female', 'NID_FrontRand_66_wFGnS.png', 'NID_BackRand_55_wFGnS.png', 'Found', 'qfm', '2025-01-31 / 08:26:41 AM', 0),
(277, 'OTHIENO', 'ANDREW', '2000-12-14', 'CM0003910HCL5A', 'male', 'NID_FrontRand_66_sCEKF.png', 'NID_BackRand_55_sCEKF.png', 'Found', 'Qfm', '2025-01-31 / 08:27:08 AM', 0),
(278, 'ACIPA', 'JOY MORINE', '1988-01-02', 'CF88054101AW7J', 'female', 'NID_FrontRand_66_hRYZE.png', 'NID_BackRand_55_hRYZE.png', 'Found', 'Public', '2025-01-31 / 08:27:26 AM', 0),
(279, 'OKOA', 'GODFREY', '1992-10-10', 'CM92074101PZZE', 'male', 'NID_FrontRand_66_TxFGg.png', 'NID_BackRand_55_TxFGg.png', 'Found', 'QFM', '2025-01-31 / 08:28:10 AM', 0),
(281, 'AGABA', 'SILVER', '1984-06-26', 'CM840551038KTJ', 'male', 'NID_FrontRand_66_jnXxd.png', 'NID_BackRand_55_jnXxd.png', 'Found', 'QFM', '2025-01-31 / 08:28:19 AM', 0),
(282, 'BUA', 'JOHNSON', '1995-08-09', 'CM95086102LLVD', 'male', 'NID_FrontRand_66_9PXsu.png', 'NID_BackRand_55_9PXsu.png', 'Found', 'qfm', '2025-01-31 / 08:28:31 AM', 0),
(284, 'ATINO', 'DOMINIKA', '1996-02-01', 'CF96022100Y55C', 'female', 'NID_FrontRand_66_Ul4ZJ.png', 'NID_BackRand_55_Ul4ZJ.png', 'Found', 'QFM', '2025-01-31 / 08:29:45 AM', 0),
(285, 'AKAO', 'EVALINE', '1992-08-08', 'CF920861007YUA', 'female', 'NID_FrontRand_66_BxFmA.png', 'NID_BackRand_55_BxFmA.png', 'Found', 'QFM', '2025-01-31 / 08:30:44 AM', 0),
(287, 'LOKOL', 'DAVID', '1989-12-25', 'CM890561024FPJ', 'male', 'NID_FrontRand_66_VzfMa.png', 'NID_BackRand_55_VzfMa.png', 'Found', 'Qfm', '2025-01-31 / 08:31:18 AM', 0),
(288, 'LAJARA', 'GRACE', '1962-08-10', 'CF62050100TX3K', 'female', 'NID_FrontRand_66_rS9Mk.png', 'NID_BackRand_55_rS9Mk.png', 'Found', 'QFM', '2025-01-31 / 08:31:18 AM', 0),
(289, 'OKULLU', 'BOSCO', '2000-02-01', 'CM00103107K9XH', 'male', 'NID_FrontRand_66_38wyO.png', 'NID_BackRand_55_38wyO.png', 'Found', 'Public', '2025-01-31 / 08:31:19 AM', 0);
INSERT INTO `national_ids` (`national_id`, `sur_name`, `given_name`, `dob`, `nin_number`, `gender`, `front`, `back`, `user_action`, `reporter`, `date_found`, `uploader_n`) VALUES
(290, 'AMONY', 'MIRIAM', '1954-08-11', 'CF54022101X14H', 'female', 'NID_FrontRand_66_jS2SK.png', 'NID_BackRand_55_jS2SK.png', 'Found', 'qfm', '2025-01-31 / 08:31:49 AM', 0),
(292, 'OBONGIT', 'EMMANUEL', '1990-08-27', 'CM90054103C66A', 'male', 'NID_FrontRand_66_2GImt.png', 'NID_BackRand_55_2GImt.png', 'Found', 'QFM', '2025-01-31 / 08:32:33 AM', 0),
(293, 'ANGUNDRU', 'WILSON', '1984-01-15', 'CM8400210DR97E', 'male', 'NID_FrontRand_66_DQ4XJ.png', 'NID_BackRand_55_DQ4XJ.png', 'Found', 'QFM', '2025-01-31 / 08:32:34 AM', 0),
(294, 'ACENG', 'AGNESS', '1973-01-01', 'CF73103103T4JL', 'female', 'NID_FrontRand_66_YJnbM.png', 'NID_BackRand_55_YJnbM.png', 'Found', 'Qfm', '2025-01-31 / 08:32:38 AM', 0),
(295, 'OCEN', 'ALEX', '1996-06-21', 'CM96086102C2UH', 'male', 'NID_FrontRand_66_24Fo9.png', 'NID_BackRand_55_24Fo9.png', 'Found', 'Public', '2025-01-31 / 08:33:36 AM', 0),
(296, 'ODWONGO', 'GEOFFREY', '1988-03-21', 'CM88057102CT6D', 'male', 'NID_FrontRand_66_Frksh.png', 'NID_BackRand_55_Frksh.png', 'Found', 'Qfm', '2025-01-31 / 08:34:08 AM', 0),
(297, 'ATINO', 'SHARON', '1993-04-24', 'CF931031032ANF', 'female', 'NID_FrontRand_66_Hd0Oq.png', 'NID_BackRand_55_Hd0Oq.png', 'Found', 'QFM', '2025-01-31 / 08:34:27 AM', 0),
(298, 'ONGOM', 'ROBERT', '1993-01-15', 'CM93086101F7FJ', 'male', 'NID_FrontRand_66_T8cJt.png', 'NID_BackRand_55_T8cJt.png', 'Found', 'QFM', '2025-01-31 / 08:34:37 AM', 0),
(299, 'ONYERA', 'JOHN BOSCO', '1987-09-02', 'CM87050101XTVJ', 'male', 'NID_FrontRand_66_VNlBD.png', 'NID_BackRand_55_VNlBD.png', 'Found', 'qfm', '2025-01-31 / 08:35:05 AM', 0),
(301, 'OKELLO', 'DAVID BEN', '1986-07-06', 'CM86111103Z7TA', 'male', 'NID_FrontRand_66_4m5l9.png', 'NID_BackRand_55_4m5l9.png', 'Found', 'Qfm', '2025-01-31 / 08:35:30 AM', 0),
(302, 'AKULLO', 'DORCUS', '1985-11-19', 'CF850221045LLD', 'female', 'NID_FrontRand_66_50FB8.png', 'NID_BackRand_55_50FB8.png', 'Found', 'QFM', '2025-01-31 / 08:35:33 AM', 0),
(303, 'NDOOLI', 'ROGERS', '1994-06-05', 'CM940721046ZQF', 'male', 'NID_FrontRand_66_QiQXM.png', 'NID_BackRand_55_QiQXM.png', 'Found', 'QFM', '2025-01-31 / 08:36:06 AM', 0),
(306, 'OJERA', 'BRIAN MOSES', '1996-02-22', 'CM960051034C7E', 'male', 'NID_FrontRand_66_r1haw.png', 'NID_BackRand_55_r1haw.png', 'Found', 'QFM', '2025-01-31 / 08:36:42 AM', 0),
(307, 'ONGOM', 'JOEL', '1979-08-15', 'CM7908610147KL', 'male', 'NID_FrontRand_66_TxVxJ.png', 'NID_BackRand_55_TxVxJ.png', 'Found', 'Qfm', '2025-01-31 / 08:36:51 AM', 0),
(308, 'MORO', 'SAMUEL', '1984-10-07', 'CM84022101434G', 'female', 'NID_FrontRand_66_wHnwl.png', 'NID_BackRand_55_wHnwl.png', 'Found', 'qfm', '2025-01-31 / 08:37:07 AM', 0),
(309, 'MUGERA', 'DANIEL', '1987-02-01', 'CM87105106Y21C', 'male', 'NID_FrontRand_66_7qfl1.png', 'NID_BackRand_55_7qfl1.png', 'Found', 'QFM', '2025-01-31 / 08:37:28 AM', 0),
(310, 'ANGWEN', 'DEFINE', '1994-10-28', 'CM940761050UCJ', 'female', 'NID_FrontRand_66_yXOU8.png', 'NID_BackRand_55_yXOU8.png', 'Found', 'QFM', '2025-01-31 / 08:37:56 AM', 0),
(311, 'AWOR', 'EVELYN', '1991-01-01', 'CF91088103XHEJ', 'female', 'NID_FrontRand_66_svWSZ.png', 'NID_BackRand_55_svWSZ.png', 'Found', 'Qfm', '2025-01-31 / 08:38:16 AM', 0),
(312, 'ODYEK', 'RONALD', '1996-01-01', 'CM96103103F70C', 'male', 'NID_FrontRand_66_76RYp.png', 'NID_BackRand_55_76RYp.png', 'Found', 'Public', '2025-01-31 / 08:38:34 AM', 0),
(313, 'ODYEK', 'RONALD', '1996-01-01', 'CM96103103F70C', 'male', 'NID_FrontRand_66_DUNgT.png', 'NID_BackRand_55_DUNgT.png', 'Found', 'Public', '2025-01-31 / 08:38:34 AM', 0),
(314, 'SEGAWA', 'RONALD', '1977-01-01', 'CM77012104Q4GH', 'male', 'NID_FrontRand_66_r0Ycs.png', 'NID_BackRand_55_r0Ycs.png', 'Found', 'QFM', '2025-01-31 / 08:39:07 AM', 0),
(315, 'KIBUBUKA', 'SALIMU', '1996-06-04', 'CM960721028PMD', 'male', 'NID_FrontRand_66_A8CaQ.png', 'NID_BackRand_55_A8CaQ.png', 'Found', 'QFM', '2025-01-31 / 08:39:20 AM', 0),
(316, 'MUGODA', 'MOSES', '1995-03-18', 'CM95035103X76F', 'male', 'NID_FrontRand_66_s4qMS.png', 'NID_BackRand_55_s4qMS.png', 'Found', 'qfm', '2025-01-31 / 08:39:32 AM', 0),
(317, 'AKULLU', 'EUNICE', '1993-05-02', 'CF93057100DH0D', 'female', 'NID_FrontRand_66_3gW1P.png', 'NID_BackRand_55_3gW1P.png', 'Found', 'Qfm', '2025-01-31 / 08:40:26 AM', 0),
(318, 'AKULLU', 'HOPE', '1995-12-29', 'CF95076102RXCJ', 'female', 'NID_FrontRand_66_yTUwa.png', 'NID_BackRand_55_yTUwa.png', 'Found', 'QFM', '2025-01-31 / 08:40:59 AM', 0),
(319, 'ACUTI', 'GEORGE OCEN', '1991-09-23', 'CM1103100T2JG', 'male', 'NID_FrontRand_66_gepvo.png', 'NID_BackRand_55_gepvo.png', 'Found', 'qfm', '2025-01-31 / 08:42:01 AM', 0),
(320, 'ALYAO', 'KENNETH', '1995-12-10', 'CM95022104PPVK', 'male', 'NID_FrontRand_66_tRGaI.png', 'NID_BackRand_55_tRGaI.png', 'Found', 'QFM', '2025-01-31 / 08:43:39 AM', 0),
(321, 'ODONGO', 'WILSON ONGOM', '1962-12-12', 'CM62111102JJHE', 'male', 'NID_FrontRand_66_MiV7t.png', 'NID_BackRand_55_MiV7t.png', 'Found', 'Qfm', '2025-01-31 / 08:43:50 AM', 0),
(323, 'ANYANGO', 'CHRISTINE', '1973-01-01', 'CF73022102ATKL', 'female', 'NID_FrontRand_66_KLkuA.png', 'NID_BackRand_55_KLkuA.png', 'Found', 'qfm', '2025-01-31 / 08:44:42 AM', 0),
(324, 'HAGUMA', 'PRISCILLA', '1984-10-18', 'CF84018108ATAA', 'female', 'NID_FrontRand_66_gXwOl.png', 'NID_BackRand_55_gXwOl.png', 'Found', 'QFM', '2025-01-31 / 08:45:17 AM', 0),
(325, 'OBALA', 'BRIAN', '1997-12-31', 'CM97001103UT9C', 'male', 'NID_FrontRand_66_FXeG5.png', 'NID_BackRand_55_FXeG5.png', 'Found', 'Qfm', '2025-01-31 / 08:45:48 AM', 0),
(326, 'AMONGI', 'REBECCA', '1996-11-30', 'CF96088104EKWC', 'female', 'NID_FrontRand_66_9JSEC.png', 'NID_BackRand_55_9JSEC.png', 'Found', 'QFM', '2025-01-31 / 08:46:58 AM', 0),
(327, 'AWOTU', 'MICHAEL', '1996-03-18', 'CM96054100NG9G', 'male', 'NID_FrontRand_66_qM56m.png', 'NID_BackRand_55_qM56m.png', 'Found', 'qfm', '2025-01-31 / 08:47:03 AM', 0),
(328, 'MUKISA', 'SAMUEL', '1990-06-03', 'CM9003210AVVDF', 'male', 'NID_FrontRand_66_fVtl4.png', 'NID_BackRand_55_fVtl4.png', 'Found', 'Qfm', '2025-01-31 / 08:47:12 AM', 0),
(329, 'ACEN', 'BRENDA', '1999-10-10', 'CF99074106UGQL', 'female', 'NID_FrontRand_66_C72fR.png', 'NID_BackRand_55_C72fR.png', 'Found', 'Qfm', '2025-01-31 / 08:48:42 AM', 0),
(330, 'OPIO ', 'DENIS', '1982-12-12', 'CM821031033LJE', 'male', 'NID_FrontRand_66_Mc7W3.png', 'NID_BackRand_55_Mc7W3.png', 'Found', 'qfm', '2025-01-31 / 08:49:29 AM', 0),
(331, 'KOMAKECH', 'KENNETH', '1995-12-19', 'CM951111009D4H', 'male', 'NID_FrontRand_66_B2MOO.png', 'NID_BackRand_55_B2MOO.png', 'Found', 'Qfm', '2025-01-31 / 08:50:10 AM', 0),
(332, 'OLWENY', 'PATRICK', '1973-07-23', 'CM73085100U54J', 'male', 'NID_FrontRand_66_meMnf.png', 'NID_BackRand_55_meMnf.png', 'Found', 'Qfm', '2025-01-31 / 08:51:43 AM', 0),
(333, 'TUGUME', 'DUNCAN', '1989-04-14', 'CM89061101Q18L', 'male', 'NID_FrontRand_66_arKOh.png', 'NID_BackRand_55_arKOh.png', 'Found', 'qfm', '2025-01-31 / 08:52:29 AM', 0),
(334, 'ABONGA', 'KENNETH', '1993-07-26', 'CM930501010JXG', 'male', 'NID_FrontRand_66_DmosX.png', 'NID_BackRand_55_DmosX.png', 'Found', 'Qfm', '2025-01-31 / 08:53:07 AM', 0),
(336, 'ADOCH', 'POLINE OTIM', '1983-08-25', 'CF830051043WDH', 'male', 'NID_FrontRand_66_87MR4.png', 'NID_BackRand_55_87MR4.png', 'Found', 'QFM', '2025-01-31 / 08:53:17 AM', 0),
(337, 'OKUK', 'JOHN', '1992-08-02', 'CM92022102EXWG', 'male', 'NID_FrontRand_66_UnptQ.png', 'NID_BackRand_55_UnptQ.png', 'Found', 'Qfm', '2025-01-31 / 08:54:29 AM', 0),
(338, 'ORWA', 'JACOB', '1993-04-09', 'CM93022105T8ZF', 'male', 'NID_FrontRand_66_VDelg.png', 'NID_BackRand_55_VDelg.png', 'Found', 'qfm', '2025-01-31 / 08:54:56 AM', 0),
(339, 'EJUPU', 'JAMES', '1995-08-10', 'CM95054102H29A', 'male', 'NID_FrontRand_66_34wJ9.png', 'NID_BackRand_55_34wJ9.png', 'Found', 'qfm', '2025-01-31 / 08:57:12 AM', 0),
(340, 'ADIBA', 'ALFRED', '1994-02-25', 'CM94035103XRRF', 'male', 'NID_FrontRand_66_w9rEG.png', 'NID_BackRand_55_w9rEG.png', 'Found', 'qfm', '2025-01-31 / 08:57:36 AM', 0),
(341, 'MUKWENDA', 'PIUS', '1996-07-14', 'CM960151013J1D', 'male', 'NID_FrontRand_66_1kjWT.png', 'NID_BackRand_55_1kjWT.png', 'Found', 'QFM', '2025-01-31 / 08:58:23 AM', 0),
(342, 'BUA', 'DAVID', '1997-12-11', 'CM970051027P8D', 'male', 'NID_FrontRand_66_EXWAQ.png', 'NID_BackRand_55_EXWAQ.png', 'Found', 'qfm', '2025-01-31 / 08:58:52 AM', 0),
(345, 'OMARA ', 'AMBROSE', '1972-12-06', 'CM7207010117DL', 'male', 'NID_FrontRand_66_NuOwN.png', 'NID_BackRand_55_NuOwN.png', 'Found', 'QFM', '2025-01-31 / 09:06:16 AM', 0),
(346, 'ALUM', 'PRISCA', '1995-04-03', 'CF95001103Y7MJ', 'female', 'NID_FrontRand_66_YRBqE.png', 'NID_BackRand_55_YRBqE.png', 'Found', 'QFM', '2025-01-31 / 09:08:17 AM', 0),
(347, 'ABAL', 'EVELYN', '1986-02-04', 'CF8605710153ND', 'female', 'NID_FrontRand_66_RBXmP.png', 'NID_BackRand_55_RBXmP.png', 'Found', 'QFM', '2025-01-31 / 09:11:54 AM', 0),
(348, 'ODOKORAC', 'STEPHEN', '1998-10-05', 'CM98005104UDLC', 'male', 'NID_FrontRand_66_VDejU.png', 'NID_BackRand_55_VDejU.png', 'Found', 'QFM', '2025-01-31 / 09:13:58 AM', 0),
(349, 'ATALA', 'LILLIAN', '1993-12-11', 'CF93022103QZHC', 'female', 'NID_FrontRand_66_5GaEb.png', 'NID_BackRand_55_5GaEb.png', 'Found', 'QFM', '2025-01-31 / 09:17:42 AM', 0),
(350, 'ACHOLA', 'CHAROLINE', '1984-06-04', 'CF840221023CNA', 'female', 'NID_FrontRand_66_QgSfw.png', 'NID_BackRand_55_QgSfw.png', 'Found', 'QFM', '2025-01-31 / 09:19:15 AM', 0),
(351, 'APILI', 'BARBRA', '1991-08-11', 'CF9100103V5FL', 'female', 'NID_FrontRand_66_uIkCc.png', 'NID_BackRand_55_uIkCc.png', 'Found', 'QFM', '2025-01-31 / 09:21:20 AM', 0),
(352, 'OPIO', 'DANIEL', '1991-12-03', 'CM910581058103HMD', 'female', 'NID_FrontRand_66_1teLt.png', 'NID_BackRand_55_1teLt.png', 'Found', 'QFM', '2025-01-31 / 09:23:07 AM', 0),
(353, 'JAKISA', 'RONALD', '1987-03-15', 'CM87033107PRDH', 'male', 'NID_FrontRand_66_fKfor.png', 'NID_BackRand_55_fKfor.png', 'Found', 'QFM', '2025-01-31 / 02:57:35 PM', 0),
(354, 'WAKHATA', 'RONALD', '1993-12-12', 'CM93067103KJQA', 'male', 'NID_FrontRand_66_WKJC5.png', 'NID_BackRand_55_WKJC5.png', 'Found', 'Qfm', '2025-01-31 / 02:58:14 PM', 0),
(355, 'ANGOL', 'DENIS', '1997-02-24', 'CM97057102YXCK', 'male', 'NID_FrontRand_66_2lYNr.png', 'NID_BackRand_55_2lYNr.png', 'Found', 'QFM', '2025-01-31 / 02:59:16 PM', 0),
(356, 'OGWANG', 'SOLOMON', '1995-03-23', 'CM95022107655A', 'male', 'NID_FrontRand_66_PSIxC.png', 'NID_BackRand_55_PSIxC.png', 'Found', 'QFM', '2025-01-31 / 03:00:44 PM', 0),
(357, 'WAGALANGA', 'LASULE', '1994-12-20', 'CM94026103H2GA', 'male', 'NID_FrontRand_66_k9bEe.png', 'NID_BackRand_55_k9bEe.png', 'Found', 'Qfm', '2025-01-31 / 03:00:55 PM', 0),
(358, 'OGWANG', 'RAYMOND OKELLO', '1971-06-10', 'CM7102210653KE', 'male', 'NID_FrontRand_66_pWNi2.png', 'NID_BackRand_55_pWNi2.png', 'Found', 'QFM', '2025-01-31 / 03:02:19 PM', 0),
(359, 'ODONGO', 'SAM', '1992-04-02', 'CM92103103D6DJ', 'male', 'NID_FrontRand_66_pjtk3.png', 'NID_BackRand_55_pjtk3.png', 'Found', 'QFM', '2025-01-31 / 03:03:37 PM', 0),
(360, 'ATINE', 'ANDREW', '1996-02-21', 'CM96103104E7HF', 'male', 'NID_FrontRand_66_PlcHv.png', 'NID_BackRand_55_PlcHv.png', 'Found', 'QFM', '2025-01-31 / 03:04:44 PM', 0),
(361, 'OMOKO', 'JIMMY', '1983-05-14', 'CM83221047GCJ', 'male', 'NID_FrontRand_66_MT7Zb.png', 'NID_BackRand_55_MT7Zb.png', 'Found', 'qfm', '2025-01-31 / 03:05:00 PM', 0),
(362, 'ABAK', 'PASKAL PIUS', '2002-01-15', 'CM02088106LEXC', 'male', 'NID_FrontRand_66_wVqgc.png', 'NID_BackRand_55_wVqgc.png', 'Found', 'Qfm', '2025-01-31 / 03:05:38 PM', 0),
(363, 'OCEN', 'ALEX', '1996-06-21', 'CM96086102C2UH', 'male', 'NID_FrontRand_66_GTWZn.png', 'NID_BackRand_55_GTWZn.png', 'Found', 'QFM', '2025-01-31 / 03:06:19 PM', 0),
(364, 'OPWONYA', 'KENNETH', '1994-09-29', 'CM94022101JHFD', 'male', 'NID_FrontRand_66_WJni7.png', 'NID_BackRand_55_WJni7.png', 'Found', 'Qfm', '2025-01-31 / 03:07:10 PM', 0),
(365, 'EYUTU', 'ISAAC', '1993-07-02', 'CM930571000Q2J', 'male', 'NID_FrontRand_66_HAkdu.png', 'NID_BackRand_55_HAkdu.png', 'Found', 'QFM', '2025-01-31 / 03:07:40 PM', 0),
(366, 'EGWOR', 'REFEAL', '1995-12-13', 'CM95001105ZLLD', 'male', 'NID_FrontRand_66_cQ9mO.png', 'NID_BackRand_55_cQ9mO.png', 'Found', 'qfm', '2025-01-31 / 03:08:01 PM', 0),
(367, 'OKULLO', 'BOSCO', '2000-02-01', 'CM00103107K9XH', 'male', 'NID_FrontRand_66_yOsjZ.png', 'NID_BackRand_55_yOsjZ.png', 'Found', 'QFM', '2025-01-31 / 03:08:54 PM', 0),
(368, 'ODYEK', 'RONALD', '1996-01-01', 'CM96103103F70C', 'male', 'NID_FrontRand_66_639iN.png', 'NID_BackRand_55_639iN.png', 'Found', 'Qfm', '2025-01-31 / 03:09:07 PM', 0),
(369, 'OGWENG', 'GODDY', '1987-05-18', 'CM87103103HHOE', 'male', 'NID_FrontRand_66_9Ah7k.png', 'NID_BackRand_55_9Ah7k.png', 'Found', 'qfm', '2025-01-31 / 03:09:34 PM', 0),
(370, 'OKELLO', 'JOB', '1996-10-24', 'CM96001107TJUH', 'male', 'NID_FrontRand_66_gpyAL.png', 'NID_BackRand_55_gpyAL.png', 'Found', 'qfm', '2025-01-31 / 03:12:03 PM', 0),
(371, 'WALUGYO', 'HENRY', '1986-04-12', 'CM86041103Z1ZH', 'male', 'NID_FrontRand_66_Bw1Wb.png', 'NID_BackRand_55_Bw1Wb.png', 'Found', 'qfm', '2025-01-31 / 03:15:09 PM', 0),
(372, 'ACIPA', 'JOY MORINE', '1988-01-02', 'CF88054101AW7J', 'male', 'NID_FrontRand_66_nmjLi.png', 'NID_BackRand_55_nmjLi.png', 'Found', 'QFM', '2025-01-31 / 03:15:26 PM', 0),
(373, 'AMULE', 'JUDITH', '1989-07-11', 'CF89057102T6JJ', 'male', 'NID_FrontRand_66_fnhxS.png', 'NID_BackRand_55_fnhxS.png', 'Found', 'qfm', '2025-01-31 / 03:17:41 PM', 0),
(374, 'OCEN', 'OSCAR', '1972-11-04', 'CM720861017R9D', 'male', 'NID_FrontRand_66_i66nb.png', 'NID_BackRand_55_i66nb.png', 'Found', 'qfm', '2025-01-31 / 03:20:45 PM', 0),
(375, 'Sedrick', 'Otolo', '1995-01-01', '12345', 'male', 'NID_FrontRand_66_W6CMG.png', 'NID_BackRand_55_W6CMG.png', 'Found', 'Public', '2025-02-01 / 07:31:09 PM', 777676206),
(376, 'Rita', 'Apio', '1995-01-01', '1234', 'male', 'NID_FrontRand_66_CRJu0.png', 'NID_BackRand_55_CRJu0.png', 'Found', 'Public', '2025-02-03 / 04:06:41 PM', 780688221),
(377, 'Apio', 'Gifty', '1995-01-01', '1234', 'male', 'NID_FrontRand_66_bglFV.png', 'NID_BackRand_55_bglFV.png', 'Found', 'Public', '2025-02-05 / 06:14:33 PM', 778232323),
(378, 'AMUGE ', 'DAIZY', '2001-05-10', 'CF01022107JFQK', 'male', 'NID_FrontRand_66_dJ5SG.png', 'NID_BackRand_55_dJ5SG.png', 'Found', 'Public', '2025-02-13 / 09:49:29 AM', 778024838),
(379, 'Okuda', 'Timothy', '1994-10-07', 'CM94015104z40E', 'male', 'NID_FrontRand_66_vvuUI.png', 'NID_BackRand_55_vvuUI.png', 'Found', 'Public', '2025-02-18 / 04:22:12 PM', 779244226),
(380, 'OCWICH', 'MOSES', '1997-07-12', 'CM97022101X6PF', 'male', 'NID_FrontRand_66_ID5bA.png', 'NID_BackRand_55_ID5bA.png', 'Reported', 'Public', '2025-02-18 / 04:22:40 PM', 777344139),
(386, 'Makayi', 'Miria', '2003-11-25', 'CF03067108AR8J', 'male', 'NID_FrontRand_1742112751_qCUWo.png', 'NID_BackRand_1742112751_qCUWo.png', 'Reported', 'Public', '2025-03-16 08:12:31', 774388883);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `superadmins`
--

CREATE TABLE `superadmins` (
  `admin_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `driving_permits`
--
ALTER TABLE `driving_permits`
  ADD PRIMARY KEY (`driver_id`);

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
-- Indexes for table `national_ids`
--
ALTER TABLE `national_ids`
  ADD PRIMARY KEY (`national_id`);

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
  MODIFY `user_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `driving_permits`
--
ALTER TABLE `driving_permits`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- AUTO_INCREMENT for table `national_ids`
--
ALTER TABLE `national_ids`
  MODIFY `national_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=387;

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

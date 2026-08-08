-- ============================================================
-- iRecovery Database Migration v2.0
-- Full system: Super Admin, Admin, Station, Documents, Alerts,
--              Payments, Lost Reports, Match Notifications
-- Run this AFTER your existing schema (it is additive)
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ── 1. ADD ROLE COLUMN TO admins (stations) ──────────────────
-- Stations already exist in `admins`. We add a role to distinguish
-- Super Admin, Admin, and Station tiers inside one users reference.
-- superadmins table stays for backward compat but we add full admins too.

ALTER TABLE `admins`
  ADD COLUMN IF NOT EXISTS `role` ENUM('super_admin','admin','station') NOT NULL DEFAULT 'station' AFTER `type_of_entity`,
  ADD COLUMN IF NOT EXISTS `contact_phone` varchar(20) DEFAULT NULL AFTER `number`,
  ADD COLUMN IF NOT EXISTS `is_active` tinyint(1) NOT NULL DEFAULT 1 AFTER `role`;

-- ── 2. SEED USERS ─────────────────────────────────────────────

-- Super Admin (full system access)
-- Passwords are bcrypt hashes (password_hash / PASSWORD_DEFAULT) verified via password_verify()
-- in login.php / adminlogin.php. Never seed plaintext passwords here — set real credentials
-- out of band and rotate before going live.
INSERT INTO `admins` (`user_name`, `password`, `number`, `contact_phone`, `email`, `district`, `address`, `type_of_entity`, `role`, `is_active`, `registered_at`)
VALUES
('superadmin',  '$2y$10$mKp9I21r9qQMpmI4st0pBu0Wz.m9SoKKLc0z29GebfT3LUw60LeWC',  777000001, '0777000001', 'superadmin@irecover.ug',  'Kampala', 'Head Office', 'iRecovery',   'super_admin', 1, NOW()),
('admin',       '$2y$10$PqvecA0RqCJWi6H32naC5Og4Y5GidS4BpWKMyp8ic9Bp3FYWVYHLC',  777000002, '0777000002', 'admin@irecover.ug',       'Kampala', 'Head Office', 'iRecovery',   'admin',       1, NOW());

-- Station Admins (radio stations, police posts, etc.)
INSERT INTO `admins` (`user_name`, `password`, `number`, `contact_phone`, `email`, `district`, `address`, `type_of_entity`, `role`, `is_active`, `registered_at`)
VALUES
('Voice of Lango FM', '$2y$10$Y9JcvCh9qDNhYL4UFtIfgOUMHuYbiW3LTzfd2UYdU1h.TXOspSMlW', 777676206, '0777676206', 'vol@irecover.info',     'Lira City', 'Lira City',    'Radio Station', 'station', 1, NOW()),
('Qfm',               '$2y$10$Y9JcvCh9qDNhYL4UFtIfgOUMHuYbiW3LTzfd2UYdU1h.TXOspSMlW', 777676206, '0777676206', 'qfm@irecover.info',     'Lira City', 'Lira City',    'Radio Station', 'station', 1, NOW()),
('Voice of The Gospel','$2y$10$Y9JcvCh9qDNhYL4UFtIfgOUMHuYbiW3LTzfd2UYdU1h.TXOspSMlW', 777676206, '0777676206', 'vog@irecover.info',     'Lira City', 'Lira City',    'Radio Station', 'station', 1, NOW()),
('Lira Central Police','$2y$10$QCEuUaUMBAfxkzoTOgabgOpi8J2fr7lKfFm.1BOkkbGnSVwex8Cyu', 772100100, '0772100100', 'police.lira@irecover.ug', 'Lira City', 'Lira Central Police Station', 'Police', 'station', 1, NOW());

-- ── 3. UNIFIED DOCUMENTS TABLE ────────────────────────────────
-- Replaces the fragmented national_ids / driving_permits / student_ids
-- tables for new submissions. Old tables kept for backward compat.

CREATE TABLE IF NOT EXISTS `documents` (
  `id`              int(11)       NOT NULL AUTO_INCREMENT,
  `doc_type`        ENUM(
                      'national_id',
                      'driving_permit',
                      'passport',
                      'student_id',
                      'academic_document',
                      'land_title',
                      'birth_certificate',
                      'other'
                    ) NOT NULL,
  -- Identity fields (shared across types)
  `sur_name`        varchar(255)  NOT NULL,
  `given_name`      varchar(255)  NOT NULL DEFAULT '',
  `dob`             date          DEFAULT NULL,
  `gender`          ENUM('male','female','other') DEFAULT NULL,
  -- Type-specific ID numbers stored in one flexible column
  `id_number`       varchar(150)  DEFAULT NULL COMMENT 'NIN, permit no., passport no., student no., plot no., etc.',
  `extra_field1`    varchar(255)  DEFAULT NULL COMMENT 'permitNumber / institution / school / land_ref etc.',
  `extra_field2`    varchar(255)  DEFAULT NULL COMMENT 'course / graduation_year / school etc.',
  `extra_field3`    varchar(255)  DEFAULT NULL COMMENT 'additional doc-specific data',
  -- Images
  `front_img`       varchar(255)  DEFAULT NULL,
  `back_img`        varchar(255)  DEFAULT NULL,
  -- Workflow
  `action`          ENUM('found','reported','matched','collected','cancelled') NOT NULL DEFAULT 'found',
  `reporter`        varchar(150)  NOT NULL DEFAULT 'Public' COMMENT 'station username or Public',
  `reporter_phone`  varchar(20)   DEFAULT NULL COMMENT 'contact of whoever submitted',
  `police_letter`   varchar(255)  DEFAULT NULL COMMENT 'uploaded police letter for lost reports',
  `station_holding` varchar(150)  DEFAULT NULL COMMENT 'station where doc is physically kept',
  -- Payment
  `payment_status`  ENUM('pending','paid','waived') NOT NULL DEFAULT 'pending',
  `payment_ref`     varchar(100)  DEFAULT NULL,
  `payment_date`    datetime      DEFAULT NULL,
  -- Timestamps
  `submitted_at`    datetime      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      datetime      DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_doc_type`   (`doc_type`),
  KEY `idx_id_number`  (`id_number`),
  KEY `idx_sur_name`   (`sur_name`),
  KEY `idx_action`     (`action`),
  KEY `idx_reporter`   (`reporter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 4. LOST REPORTS TABLE ─────────────────────────────────────
-- Someone reports a document they lost, with police letter

CREATE TABLE IF NOT EXISTS `lost_reports` (
  `id`               int(11)      NOT NULL AUTO_INCREMENT,
  `doc_type`         ENUM(
                       'national_id','driving_permit','passport',
                       'student_id','academic_document','land_title',
                       'birth_certificate','other'
                     ) NOT NULL,
  `sur_name`         varchar(255) NOT NULL,
  `given_name`       varchar(255) NOT NULL DEFAULT '',
  `dob`              date         DEFAULT NULL,
  `gender`           ENUM('male','female','other') DEFAULT NULL,
  `id_number`        varchar(150) DEFAULT NULL,
  `extra_field1`     varchar(255) DEFAULT NULL,
  `extra_field2`     varchar(255) DEFAULT NULL,
  -- Reporter contact
  `reporter_name`    varchar(255) NOT NULL,
  `reporter_phone`   varchar(20)  NOT NULL,
  `reporter_email`   varchar(150) DEFAULT NULL,
  -- Police letter
  `police_letter`    varchar(255) DEFAULT NULL,
  -- Match status
  `match_status`     ENUM('unmatched','matched','notified','collected') NOT NULL DEFAULT 'unmatched',
  `matched_doc_id`   int(11)      DEFAULT NULL COMMENT 'FK to documents.id when matched',
  `submitted_at`     datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       datetime     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_lr_id_number` (`id_number`),
  KEY `idx_lr_sur_name`  (`sur_name`),
  KEY `idx_lr_match`     (`match_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 5. MATCH ALERTS TABLE ─────────────────────────────────────
-- Created whenever system detects a lost_report matches a found document

CREATE TABLE IF NOT EXISTS `match_alerts` (
  `id`             int(11)    NOT NULL AUTO_INCREMENT,
  `lost_report_id` int(11)    NOT NULL,
  `document_id`    int(11)    NOT NULL,
  `station`        varchar(150) DEFAULT NULL COMMENT 'station holding the found doc',
  `alert_status`   ENUM('new','admin_notified','owner_notified','payment_pending','paid','collected','closed') NOT NULL DEFAULT 'new',
  `notes`          text       DEFAULT NULL,
  `created_at`     datetime   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     datetime   DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ma_lost`   (`lost_report_id`),
  KEY `idx_ma_doc`    (`document_id`),
  KEY `idx_ma_status` (`alert_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 6. PAYMENTS TABLE ─────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `payments` (
  `id`             int(11)       NOT NULL AUTO_INCREMENT,
  `match_alert_id` int(11)       NOT NULL,
  `document_id`    int(11)       DEFAULT NULL,
  `payer_name`     varchar(255)  DEFAULT NULL,
  `payer_phone`    varchar(20)   NOT NULL COMMENT 'mobile money number',
  `id_number`      varchar(150)  DEFAULT NULL COMMENT 'NIN or doc ID used to initiate payment',
  `amount`         decimal(10,2) NOT NULL DEFAULT '10000.00' COMMENT 'UGX recovery fee',
  `currency`       varchar(5)    NOT NULL DEFAULT 'UGX',
  `payment_method` ENUM('mobile_money','cash','waived') NOT NULL DEFAULT 'mobile_money',
  `provider`       ENUM('MTN','Airtel','other') DEFAULT 'MTN',
  `transaction_ref` varchar(150) DEFAULT NULL,
  `status`         ENUM('initiated','pending','confirmed','failed') NOT NULL DEFAULT 'initiated',
  `initiated_at`   datetime      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `confirmed_at`   datetime      DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pay_phone`   (`payer_phone`),
  KEY `idx_pay_id_num`  (`id_number`),
  KEY `idx_pay_status`  (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 7. COLLECTION LOG ─────────────────────────────────────────
-- Marked by station when owner physically collects document

CREATE TABLE IF NOT EXISTS `collection_log` (
  `id`           int(11)      NOT NULL AUTO_INCREMENT,
  `document_id`  int(11)      NOT NULL,
  `alert_id`     int(11)      DEFAULT NULL,
  `payment_id`   int(11)      DEFAULT NULL,
  `station`      varchar(150) NOT NULL,
  `collected_by` varchar(255) DEFAULT NULL COMMENT 'name of person collecting',
  `collected_at` datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notes`        text         DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 8. SYSTEM NOTIFICATIONS ───────────────────────────────────

CREATE TABLE IF NOT EXISTS `notifications` (
  `id`          int(11)    NOT NULL AUTO_INCREMENT,
  `type`        ENUM('match_found','payment_confirmed','doc_collected','new_report','new_upload') NOT NULL,
  `target_role` ENUM('super_admin','admin','station','all') NOT NULL DEFAULT 'admin',
  `target_user` varchar(150) DEFAULT NULL COMMENT 'specific username or NULL for role-wide',
  `message`     text       NOT NULL,
  `is_read`     tinyint(1) NOT NULL DEFAULT 0,
  `ref_id`      int(11)    DEFAULT NULL COMMENT 'ID of related match_alert, payment, etc.',
  `created_at`  datetime   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notif_target` (`target_role`, `target_user`),
  KEY `idx_notif_read`   (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 9. SEARCH LOG ─────────────────────────────────────────────
-- Logs every public search so admins can see who searched & contact them

CREATE TABLE IF NOT EXISTS `search_log` (
  `id`             int(11)    NOT NULL AUTO_INCREMENT,
  `doc_type`       varchar(50) DEFAULT NULL,
  `search_name`    varchar(255) DEFAULT NULL,
  `search_id_num`  varchar(150) DEFAULT NULL,
  `searcher_phone` varchar(20)  DEFAULT NULL,
  `result`         ENUM('matched','not_found') NOT NULL DEFAULT 'not_found',
  `matched_doc_id` int(11)    DEFAULT NULL,
  `searched_at`    datetime   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sl_result` (`result`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 10. RECOVERY FEE CONFIG ───────────────────────────────────

CREATE TABLE IF NOT EXISTS `fee_config` (
  `id`         int(11)       NOT NULL AUTO_INCREMENT,
  `doc_type`   varchar(50)   NOT NULL,
  `fee_ugx`    decimal(10,2) NOT NULL DEFAULT '10000.00',
  `updated_at` datetime      DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `doc_type` (`doc_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 11. ADD 'pending' STATUS TO match_alerts ─────────────
-- Stations / admins can mark an alert as 'pending' (awaiting action)
ALTER TABLE `match_alerts`
  MODIFY `alert_status` ENUM('new','admin_notified','owner_notified','payment_pending','paid','collected','closed','pending') NOT NULL DEFAULT 'new';

INSERT INTO `fee_config` (`doc_type`, `fee_ugx`) VALUES
('national_id',        10000.00),
('driving_permit',     15000.00),
('passport',           20000.00),
('student_id',          5000.00),
('academic_document',  10000.00),
('land_title',         25000.00),
('birth_certificate',   5000.00),
('other',              10000.00)
ON DUPLICATE KEY UPDATE `fee_ugx` = VALUES(`fee_ugx`);

COMMIT;

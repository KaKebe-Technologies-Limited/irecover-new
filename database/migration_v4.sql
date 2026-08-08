-- iRecovery Migration v4
-- Two-step match approval, station commission %, IOTec Pay integration fields

-- ── 1. MATCH APPROVAL FLAGS ────────────────────────────────────
-- A match is payable only once BOTH flags are 1.
ALTER TABLE `match_alerts`
  ADD COLUMN IF NOT EXISTS `admin_approved`     TINYINT(1) NOT NULL DEFAULT 0 AFTER `alert_status`,
  ADD COLUMN IF NOT EXISTS `admin_approved_by`  VARCHAR(150) DEFAULT NULL     AFTER `admin_approved`,
  ADD COLUMN IF NOT EXISTS `admin_approved_at`  DATETIME DEFAULT NULL         AFTER `admin_approved_by`,
  ADD COLUMN IF NOT EXISTS `station_approved`   TINYINT(1) NOT NULL DEFAULT 0 AFTER `admin_approved_at`,
  ADD COLUMN IF NOT EXISTS `station_approved_at` DATETIME DEFAULT NULL        AFTER `station_approved`;

-- ── 2. FEE CONFIG: commission % + updated fees ─────────────────
ALTER TABLE `fee_config`
  ADD COLUMN IF NOT EXISTS `commission_percent` DECIMAL(5,2) NOT NULL DEFAULT 20.00 AFTER `fee_ugx`;

UPDATE `fee_config` SET `fee_ugx` = 25000.00 WHERE `doc_type` = 'national_id';
UPDATE `fee_config` SET `fee_ugx` = 30000.00 WHERE `doc_type` = 'passport';

-- ── 3. PAYMENTS: IOTec Pay + commission tracking ───────────────
ALTER TABLE `payments`
  ADD COLUMN IF NOT EXISTS `iotec_transaction_id` VARCHAR(64)   DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `iotec_status`         VARCHAR(30)   DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `station_commission`   DECIMAL(10,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `callback_payload`     TEXT          DEFAULT NULL;

CREATE INDEX IF NOT EXISTS idx_iotec_txn ON `payments` (`iotec_transaction_id`);

-- ── 4. Shrink any existing 10-char verification codes to NULL ──
-- (they'll be regenerated at 6 chars on next payment confirmation)
UPDATE `payments` SET `verification_code` = NULL WHERE LENGTH(`verification_code`) = 10;

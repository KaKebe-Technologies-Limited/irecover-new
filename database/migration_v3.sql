-- iRecovery Migration v3
-- Verification codes + download approval flow

ALTER TABLE `payments`
  ADD COLUMN IF NOT EXISTS `verification_code` VARCHAR(12) DEFAULT NULL COMMENT '10-char unique code on PDF receipt',
  ADD COLUMN IF NOT EXISTS `download_allowed`  TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=admin approved download',
  ADD COLUMN IF NOT EXISTS `used_at`           DATETIME   DEFAULT NULL COMMENT 'When station scanned the code';

CREATE UNIQUE INDEX IF NOT EXISTS idx_vcode ON `payments` (verification_code);

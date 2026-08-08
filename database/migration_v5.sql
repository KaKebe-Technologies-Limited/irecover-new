-- iRecovery Migration v5
-- Fix collation mismatch: the original base-dump tables (admins, national_ids,
-- driving_permits, student_ids, superadmins, documents_legacy, found_documents,
-- found_ids, user_documents) were created with utf8mb4_general_ci, while every
-- table added since migration_v2.sql (documents, lost_reports, match_alerts,
-- payments, etc.) uses utf8mb4_unicode_ci. Any query that JOINs or compares a
-- string column across these two groups (e.g. admins.user_name against
-- documents.station_holding) throws:
--   "Illegal mix of collations (utf8mb4_general_ci,IMPLICIT) and
--    (utf8mb4_unicode_ci,IMPLICIT) for operation '='"
-- This unifies everything to utf8mb4_unicode_ci. Safe, non-destructive —
-- only changes how strings are compared/sorted, not the data itself.

ALTER TABLE `admins`           CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `national_ids`     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `driving_permits`  CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `student_ids`      CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `superadmins`      CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `documents_legacy` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `found_documents`  CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `found_ids`        CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `user_documents`   CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

<?php
// ─────────────────────────────────────────────
// iRecovery Match Engine
// Checks for matches between found docs and
// lost reports, creates alerts + notifications
// ─────────────────────────────────────────────

/**
 * After a found document is uploaded, check if any lost report matches it.
 * Creates a match_alert and notification if a match exists.
 */
function checkMatchOnUpload(mysqli $conn, string $doc_type, string $id_number, string $sur_name, string $given_name, string $dob, int $document_id, string $station): void {
    // Match by ID number (exact) OR name+DOB (fuzzy)
    $stmt = $conn->prepare(
        "SELECT id, reporter_name, reporter_phone, reporter_email
         FROM lost_reports
         WHERE match_status = 'unmatched'
           AND doc_type = ?
           AND (id_number = ? OR (sur_name = ? AND given_name = ? AND dob = ?))
         LIMIT 1"
    );
    $stmt->bind_param('sssss', $doc_type, $id_number, $sur_name, $given_name, $dob);
    $stmt->execute();
    $match = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$match) return;

    // Create match alert
    $alertStmt = $conn->prepare(
        "INSERT INTO match_alerts (lost_report_id, document_id, station, alert_status)
         VALUES (?, ?, ?, 'new')"
    );
    $alertStmt->bind_param('iis', $match['id'], $document_id, $station);
    $alertStmt->execute();
    $alertId = $conn->insert_id;
    $alertStmt->close();

    // Update lost_report match status
    $updStmt = $conn->prepare("UPDATE lost_reports SET match_status='matched', matched_doc_id=? WHERE id=?");
    $updStmt->bind_param('ii', $document_id, $match['id']);
    $updStmt->execute();
    $updStmt->close();

    // Create notification for admin
    $name    = $match['reporter_name'];
    $phone   = $match['reporter_phone'];
    $msg     = "Match found! Document ($doc_type) uploaded by station '$station' matches lost report by $name ($phone). Alert #$alertId.";
    createNotification($conn, 'match_found', 'admin', null, $msg, $alertId);
}

/**
 * After a lost report is submitted, check if any uploaded found doc matches it.
 */
function checkMatchOnReport(mysqli $conn, string $doc_type, string $id_number, string $sur_name, string $given_name, string $dob, int $lost_report_id, string $reporter_name, string $reporter_phone): void {
    // Search legacy tables + new documents table
    $found_doc_id = null;
    $station      = 'Unknown';

    // Check new documents table first
    $stmt = $conn->prepare(
        "SELECT id, station_holding FROM documents
         WHERE action = 'found'
           AND doc_type = ?
           AND (id_number = ? OR (sur_name = ? AND given_name = ? AND dob = ?))
         LIMIT 1"
    );
    $stmt->bind_param('sssss', $doc_type, $id_number, $sur_name, $given_name, $dob);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $found_doc_id = $row['id'];
        $station      = $row['station_holding'] ?? 'Unknown';
    } else {
        // Fall back to legacy national_ids table
        if ($doc_type === 'national_id') {
            $s = $conn->prepare("SELECT national_id as id, reporter FROM national_ids WHERE user_action='Found' AND (nin_number=? OR (sur_name=? AND given_name=? AND dob=?)) LIMIT 1");
            $s->bind_param('ssss', $id_number, $sur_name, $given_name, $dob);
            $s->execute();
            $r = $s->get_result()->fetch_assoc();
            $s->close();
            if ($r) { $found_doc_id = $r['id']; $station = $r['reporter']; }
        } elseif ($doc_type === 'driving_permit') {
            $s = $conn->prepare("SELECT driver_id as id, reporter FROM driving_permits WHERE user_action='Found' AND (permit_number=? OR (sur_name=? AND given_name=? AND dob=?)) LIMIT 1");
            $s->bind_param('ssss', $id_number, $sur_name, $given_name, $dob);
            $s->execute();
            $r = $s->get_result()->fetch_assoc();
            $s->close();
            if ($r) { $found_doc_id = $r['id']; $station = $r['reporter']; }
        } elseif ($doc_type === 'student_id') {
            $s = $conn->prepare("SELECT student_id as id, reporter FROM student_ids WHERE user_action='Found' AND (student_number=? OR (sur_name=? AND given_name=? AND dob=?)) LIMIT 1");
            $s->bind_param('ssss', $id_number, $sur_name, $given_name, $dob);
            $s->execute();
            $r = $s->get_result()->fetch_assoc();
            $s->close();
            if ($r) { $found_doc_id = $r['id']; $station = $r['reporter']; }
        }
    }

    if (!$found_doc_id) return;

    // Create alert
    $alertStmt = $conn->prepare(
        "INSERT INTO match_alerts (lost_report_id, document_id, station, alert_status)
         VALUES (?, ?, ?, 'new')"
    );
    $alertStmt->bind_param('iis', $lost_report_id, $found_doc_id, $station);
    $alertStmt->execute();
    $alertId = $conn->insert_id;
    $alertStmt->close();

    // Update lost_report
    $updStmt = $conn->prepare("UPDATE lost_reports SET match_status='matched', matched_doc_id=? WHERE id=?");
    $updStmt->bind_param('ii', $found_doc_id, $lost_report_id);
    $updStmt->execute();
    $updStmt->close();

    // Notify admins
    $msg = "Match found! Lost report by $reporter_name ($reporter_phone) matches a found $doc_type held by station '$station'. Alert #$alertId.";
    createNotification($conn, 'match_found', 'admin', null, $msg, $alertId);
}

/**
 * Insert a notification row.
 */
function createNotification(mysqli $conn, string $type, string $target_role, ?string $target_user, string $message, ?int $ref_id = null): void {
    $stmt = $conn->prepare(
        "INSERT INTO notifications (type, target_role, target_user, message, ref_id)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('ssssi', $type, $target_role, $target_user, $message, $ref_id);
    $stmt->execute();
    $stmt->close();
}

/**
 * Get unread notification count for a role/user.
 */
function getUnreadCount(mysqli $conn, string $target_role, ?string $target_user = null): int {
    $stmt = $conn->prepare(
        "SELECT COUNT(*) as cnt FROM notifications
         WHERE is_read = 0 AND (target_role = ? OR target_role = 'all')
           AND (target_user IS NULL OR target_user = ?)"
    );
    $stmt->bind_param('ss', $target_role, $target_user);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return (int)($row['cnt'] ?? 0);
}

/**
 * Get recovery fee for a doc type from fee_config table.
 */
function getRecoveryFee(mysqli $conn, string $doc_type): float {
    $stmt = $conn->prepare("SELECT fee_ugx FROM fee_config WHERE doc_type = ? LIMIT 1");
    $stmt->bind_param('s', $doc_type);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ? (float)$row['fee_ugx'] : 10000.00;
}

/**
 * Generate a unique 10-character alphanumeric verification code.
 */
function generateVerificationCode(mysqli $conn): string {
    do {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // no confusable chars
        $code  = '';
        for ($i = 0; $i < 10; $i++) $code .= $chars[random_int(0, strlen($chars) - 1)];
        $stmt = $conn->prepare("SELECT id FROM payments WHERE verification_code=? LIMIT 1");
        $stmt->bind_param('s', $code);
        $stmt->execute();
        $r = $stmt->get_result();
        $stmt->close();
    } while ($r && $r->num_rows > 0);
    return $code;
}


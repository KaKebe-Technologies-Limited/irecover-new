<?php
// ─────────────────────────────────────────────
// iRecovery Match Engine
// Checks for matches between found docs and
// lost reports, creates alerts + notifications
// ─────────────────────────────────────────────
require_once __DIR__ . '/sms_notify.php';
require_once __DIR__ . '/smtp_mailer.php';

const REPORT_NOTIFY_TO = 'kakebetech.comms@gmail.com';
const REPORT_NOTIFY_CC = ['komabono1998@gmail.com', 'jeromeoscar2002@gmail.com', 'derricklamarh@gmail.com', 'oscarbrianojok@gmail.com'];

/**
 * Sends one email, trying real authenticated SMTP first (if email.local.php
 * is configured) and falling back to PHP's mail() otherwise. Never throws;
 * failures are only logged so a bad address/config can never break the
 * caller's flow.
 */
function sendAppEmail(string $to, string $subject, string $htmlBody, array $cc = [], ?string $replyTo = null, string $logContext = ''): bool {
    if (smtpSendMail($to, $subject, $htmlBody, $cc, $replyTo)) {
        return true;
    }

    $headers   = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: ' . (defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'iRecovery Uganda') . ' <noreply@irecover.site>';
    if (!empty($cc)) $headers[] = 'Cc: ' . implode(', ', array_filter($cc));
    if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) $headers[] = 'Reply-To: ' . $replyTo;

    $sent = @mail($to, $subject, $htmlBody, implode("\r\n", $headers));
    if (!$sent) {
        error_log('iRecovery: email failed (SMTP + mail() fallback both failed)' . ($logContext ? " — $logContext" : ''));
    }
    return $sent;
}

/**
 * Shared sender for every internal Kakebe-team notification email — same
 * To/Cc list, same simple label/value table layout.
 */
function sendTeamNotificationEmail(string $subject, string $heading, array $rows, ?string $replyTo, string $logContext): void {
    $body = '<div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#222;">';
    $body .= '<h2 style="color:#CC0000;margin:0 0 .75rem;">' . htmlspecialchars($heading) . '</h2>';
    $body .= '<table cellpadding="6" cellspacing="0" style="border-collapse:collapse;">';
    foreach ($rows as $label => $value) {
        $body .= '<tr>'
               . '<td style="font-weight:bold;border-bottom:1px solid #eee;white-space:nowrap;">' . htmlspecialchars($label) . '</td>'
               . '<td style="border-bottom:1px solid #eee;">' . htmlspecialchars((string)$value) . '</td>'
               . '</tr>';
    }
    $body .= '</table>';
    $body .= '<p style="margin-top:1rem;"><a href="' . htmlspecialchars(siteBaseUrl()) . '/admin/">Open Admin Dashboard</a></p>';
    $body .= '</div>';

    sendAppEmail(REPORT_NOTIFY_TO, $subject, $body, REPORT_NOTIFY_CC, $replyTo, $logContext);
}

/**
 * Emails the Kakebe team whenever a payment is confirmed — the final,
 * highest-signal event in the whole recovery pipeline.
 */
function notifyTeamOfPaymentConfirmed(array $p): void {
    $docTypeLabel = ucwords(str_replace('_', ' ', $p['doc_type'] ?? ''));
    sendTeamNotificationEmail(
        'Payment Confirmed — ' . ($docTypeLabel ?: 'Document') . ' — UGX ' . number_format((float)($p['amount'] ?? 0)),
        'Payment Confirmed',
        [
            'Document Type'  => $docTypeLabel ?: '—',
            'ID / Reference' => $p['id_number'] ?: '—',
            'Payer Name'     => $p['payer_name'] ?: '—',
            'Payer Phone'    => $p['payer_phone'] ?: '—',
            'Amount'         => 'UGX ' . number_format((float)($p['amount'] ?? 0)),
            'Station'        => $p['station'] ?: '—',
            'Payment ID'     => '#' . (int)($p['payment_id'] ?? 0),
            'Confirmed At'   => date('Y-m-d H:i:s'),
        ],
        null,
        'payment #' . (int)($p['payment_id'] ?? 0) . ' confirmed'
    );
}

/**
 * Tells the person who reported/searched for a document that it has been
 * found — sent by email (if they gave one) AND SMS (if they gave a phone).
 * Never throws — failures are only logged, so a bad phone/email can never
 * break the match that triggered this.
 */
function notifyOwnerDocumentFound(array $r): void {
    $docTypeLabel = ucwords(str_replace('_', ' ', $r['doc_type'] ?? ''));
    $payUrl       = siteBaseUrl() . '/pay.php?id_number=' . urlencode($r['id_number'] ?? '');
    $name         = $r['reporter_name'] ?? '';
    $station      = $r['station'] ?? '';
    $whereLine    = $station !== '' ? " It's being held at <strong>" . htmlspecialchars($station) . "</strong>." : '';

    if (!empty($r['reporter_email']) && filter_var($r['reporter_email'], FILTER_VALIDATE_EMAIL)) {
        $subject = 'Good news — your ' . $docTypeLabel . ' has been found!';
        $body  = '<div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#222;">';
        $body .= '<h2 style="color:#15803d;margin:0 0 .75rem;">We found your document!</h2>';
        $body .= '<p>Hi' . ($name !== '' ? ' ' . htmlspecialchars($name) : '') . ',</p>';
        $body .= '<p>Your <strong>' . htmlspecialchars($docTypeLabel) . '</strong> has been matched to a document in our system.' . $whereLine . ' '
               . 'You can pay the recovery fee right now — no waiting — and get an instant pickup receipt with a verification code for the station.</p>';
        $body .= '<p><a href="' . htmlspecialchars($payUrl) . '" style="display:inline-block;background:#CC0000;color:#fff;padding:.65rem 1.4rem;border-radius:8px;text-decoration:none;font-weight:bold;">Pay Now</a></p>';
        $body .= '<p style="color:#888;font-size:.85rem;margin-top:1.5rem;">iRecovery Uganda — Kakebe Technologies Limited</p>';
        $body .= '</div>';

        sendAppEmail($r['reporter_email'], $subject, $body, [], null, 'found-document email to ' . $r['reporter_email']);
    }

    if (!empty($r['reporter_phone'])) {
        $whereSms = $station !== '' ? " Held at $station." : '';
        $msg = "iRecovery: Good news! Your $docTypeLabel has been found.$whereSms Pay now: $payUrl";
        sendSms($r['reporter_phone'], $msg);
    }
}

/**
 * Absolute base URL of the site (scheme + host + base path), derived from
 * the current request so it works unmodified on both local XAMPP
 * (http://localhost/irecover) and production (https://irecover.site).
 */
function siteBaseUrl(): string {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir    = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    // Scripts under /admin or /station still belong to the app root
    $dir    = preg_replace('#/(admin|station)$#', '', $dir);
    return $scheme . '://' . $host . $dir;
}

/**
 * Resolve a stored document image value into a displayable <img> src.
 * New records store full absolute URLs (returned as-is); older records
 * store a bare filename relative to /uploads/, which gets the correct
 * prefix applied based on the current script's location.
 */
function docImageUrl(?string $img, string $relPrefix = 'uploads/'): string {
    $img = trim((string)$img);
    if ($img === '') return '';
    if (preg_match('#^https?://#i', $img)) return $img;
    return $relPrefix . $img;
}

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

    // Let the person who reported it lost know right away
    notifyOwnerDocumentFound([
        'doc_type'       => $doc_type,
        'id_number'      => $id_number,
        'reporter_name'  => $match['reporter_name'],
        'reporter_phone' => $match['reporter_phone'],
        'reporter_email' => $match['reporter_email'],
        'station'        => $station,
    ]);
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
            // student_ids has no dob column — it stores "date issued" instead,
            // which is what the shared $dob parameter actually holds for this doc type.
            $s = $conn->prepare("SELECT student_id as id, reporter FROM student_ids WHERE user_action='Found' AND (student_number=? OR (sur_name=? AND given_name=? AND date_issued=?)) LIMIT 1");
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

    // Let the reporter know right away — the match already exists, no need to wait
    $emailStmt = $conn->prepare("SELECT reporter_email FROM lost_reports WHERE id=?");
    $emailStmt->bind_param('i', $lost_report_id);
    $emailStmt->execute();
    $reporterEmail = $emailStmt->get_result()->fetch_assoc()['reporter_email'] ?? null;
    $emailStmt->close();

    notifyOwnerDocumentFound([
        'doc_type'       => $doc_type,
        'id_number'      => $id_number,
        'reporter_name'  => $reporter_name,
        'reporter_phone' => $reporter_phone,
        'reporter_email' => $reporterEmail,
        'station'        => $station,
    ]);
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
 * Broad staff-facing document search (partial match on ID number or name),
 * across the unified `documents` table and the legacy per-type tables.
 * Returns up to $limit unified rows: id, doc_type, sur_name, given_name,
 * dob, id_number, front_img, back_img, station_holding, action, submitted_at, source.
 */
function searchDocumentsBroad(mysqli $conn, string $query, int $limit = 50): array {
    $query = trim($query);
    if ($query === '') return [];
    $like = '%' . $query . '%';
    $results = [];

    $stmt = $conn->prepare(
        "SELECT id, doc_type, sur_name, given_name, dob, id_number, gender, front_img, back_img,
                station_holding, action, submitted_at
         FROM documents
         WHERE id_number LIKE ? OR sur_name LIKE ? OR given_name LIKE ?
         ORDER BY submitted_at DESC LIMIT ?"
    );
    $stmt->bind_param('sssi', $like, $like, $like, $limit);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $r['source'] = 'new';
        $r['front_img'] = docImageUrl($r['front_img']);
        $r['back_img']  = docImageUrl($r['back_img']);
        $results[] = $r;
    }
    $stmt->close();

    $legacy = [
        'national_ids'    => ['pk' => 'national_id', 'idcol' => 'nin_number',      'type' => 'national_id',    'dob' => 'dob'],
        'driving_permits' => ['pk' => 'driver_id',    'idcol' => 'permit_number',   'type' => 'driving_permit', 'dob' => 'dob'],
        'student_ids'     => ['pk' => 'student_id',   'idcol' => 'student_number',  'type' => 'student_id',     'dob' => 'NULL'],
    ];
    foreach ($legacy as $table => $cfg) {
        if (count($results) >= $limit) break;
        $remaining = $limit - count($results);
        $sql = "SELECT `{$cfg['pk']}` as id, '{$cfg['type']}' as doc_type, sur_name, given_name, {$cfg['dob']} as dob,
                        `{$cfg['idcol']}` as id_number, front as front_img, back as back_img,
                        reporter as station_holding, user_action as action, date_found as submitted_at
                 FROM `$table`
                 WHERE `{$cfg['idcol']}` LIKE ? OR sur_name LIKE ? OR given_name LIKE ?
                 ORDER BY `{$cfg['pk']}` DESC LIMIT ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssi', $like, $like, $like, $remaining);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) {
            $r['source'] = 'legacy';
            if (!empty($r['front_img'])) $r['front_img'] = 'uploads/' . $r['front_img'];
            if (!empty($r['back_img']))  $r['back_img']  = 'uploads/' . $r['back_img'];
            $results[] = $r;
        }
        $stmt->close();
    }

    // Each source above is internally sorted, but the merged list isn't —
    // re-sort the combined set so results are truly most-recent-first
    // across new + legacy tables together.
    usort($results, fn($a, $b) => strcmp((string)$b['submitted_at'], (string)$a['submitted_at']));

    return $results;
}

/**
 * Get fee + station commission % for a doc type from fee_config table.
 * Returns ['fee_ugx' => float, 'commission_percent' => float].
 */
function getFeeConfig(mysqli $conn, string $doc_type): array {
    $stmt = $conn->prepare("SELECT fee_ugx, commission_percent FROM fee_config WHERE doc_type = ? LIMIT 1");
    $stmt->bind_param('s', $doc_type);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return [
        'fee_ugx'            => $row ? (float)$row['fee_ugx'] : 10000.00,
        'commission_percent' => $row ? (float)$row['commission_percent'] : 20.00,
    ];
}

/**
 * Record an admin (super_admin/admin) approval of a match alert, then
 * transition the alert to 'payment_pending' once both sides have approved.
 */
function approveMatchByAdmin(mysqli $conn, int $alertId, string $adminUser): void {
    $stmt = $conn->prepare("UPDATE match_alerts SET admin_approved=1, admin_approved_by=?, admin_approved_at=NOW() WHERE id=?");
    $stmt->bind_param('si', $adminUser, $alertId);
    $stmt->execute();
    $stmt->close();
    tryActivatePaymentPending($conn, $alertId);
}

/**
 * Record a station's approval that a match alert matches their held document, then
 * transition the alert to 'payment_pending' once both sides have approved.
 * Scoped to the station's own alerts only.
 */
function approveMatchByStation(mysqli $conn, int $alertId, string $stationUser): void {
    $stmt = $conn->prepare("UPDATE match_alerts SET station_approved=1, station_approved_at=NOW() WHERE id=? AND station=?");
    $stmt->bind_param('is', $alertId, $stationUser);
    $stmt->execute();
    $stmt->close();
    tryActivatePaymentPending($conn, $alertId);
}

/**
 * If both admin_approved and station_approved are true, move the alert to
 * 'payment_pending' and notify admin + the holding station.
 */
function tryActivatePaymentPending(mysqli $conn, int $alertId): void {
    $stmt = $conn->prepare("SELECT admin_approved, station_approved, alert_status, station FROM match_alerts WHERE id=?");
    $stmt->bind_param('i', $alertId);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$r) return;

    $alreadyPast = in_array($r['alert_status'], ['payment_pending', 'paid', 'collected', 'closed'], true);
    if ((int)$r['admin_approved'] === 1 && (int)$r['station_approved'] === 1 && !$alreadyPast) {
        $upd = $conn->prepare("UPDATE match_alerts SET alert_status='payment_pending', updated_at=NOW() WHERE id=?");
        $upd->bind_param('i', $alertId);
        $upd->execute();
        $upd->close();

        createNotification($conn, 'match_found', 'admin', null, "Match #$alertId fully approved by admin and station — owner can now be called to pay.", $alertId);
        if (!empty($r['station'])) {
            createNotification($conn, 'match_found', 'station', $r['station'], "Match #$alertId fully approved — owner can now be called to pay.", $alertId);
        }
    }
}

/**
 * Generate a unique 6-character alphanumeric verification code.
 */
function generateVerificationCode(mysqli $conn): string {
    do {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // no confusable chars
        $code  = '';
        for ($i = 0; $i < 6; $i++) $code .= $chars[random_int(0, strlen($chars) - 1)];
        $stmt = $conn->prepare("SELECT id FROM payments WHERE verification_code=? LIMIT 1");
        $stmt->bind_param('s', $code);
        $stmt->execute();
        $r = $stmt->get_result();
        $stmt->close();
    } while ($r && $r->num_rows > 0);
    return $code;
}

/**
 * Ensure a payable match_alerts record exists for a document found via public
 * search, so admin/station have something to review and approve. If a
 * lost_reports + match_alerts pair doesn't already exist for this document,
 * create one from the searcher-supplied details (status starts 'new', both
 * approvals false — nothing here bypasses the approval gate).
 * Returns the match_alerts.id (existing or newly created), or null if the
 * document's id_number is empty (nothing to key the report on).
 */
function ensureMatchAlertForSearch(mysqli $conn, string $doc_type, string $id_number, string $sur_name, string $given_name, ?string $dob, int $foundDocId, string $station, string $searcherPhone): ?int {
    if ($id_number === '') return null;

    // Already has a live (non-collected/closed) alert for this document?
    $chk = $conn->prepare("SELECT id FROM match_alerts WHERE document_id=? AND alert_status NOT IN ('collected','closed') LIMIT 1");
    $chk->bind_param('i', $foundDocId);
    $chk->execute();
    $existing = $chk->get_result()->fetch_assoc();
    $chk->close();
    if ($existing) return (int)$existing['id'];

    // Reuse an existing lost_reports row for this id_number if present, else create one
    $lr = $conn->prepare("SELECT id FROM lost_reports WHERE id_number=? AND doc_type=? ORDER BY id DESC LIMIT 1");
    $lr->bind_param('ss', $id_number, $doc_type);
    $lr->execute();
    $lrRow = $lr->get_result()->fetch_assoc();
    $lr->close();

    if ($lrRow) {
        $lostReportId = (int)$lrRow['id'];
    } else {
        $ins = $conn->prepare(
            "INSERT INTO lost_reports (doc_type, sur_name, given_name, dob, id_number, reporter_name, reporter_phone, match_status, matched_doc_id)
             VALUES (?,?,?,?,?,?,?,'matched',?)"
        );
        $reporterName = trim($sur_name . ' ' . $given_name) ?: 'Public Search';
        $ins->bind_param('sssssssi', $doc_type, $sur_name, $given_name, $dob, $id_number, $reporterName, $searcherPhone, $foundDocId);
        $ins->execute();
        $lostReportId = $conn->insert_id;
        $ins->close();
    }

    $alertStmt = $conn->prepare(
        "INSERT INTO match_alerts (lost_report_id, document_id, station, alert_status) VALUES (?, ?, ?, 'new')"
    );
    $alertStmt->bind_param('iis', $lostReportId, $foundDocId, $station);
    $alertStmt->execute();
    $alertId = $conn->insert_id;
    $alertStmt->close();

    createNotification($conn, 'match_found', 'admin', null,
        "Owner searched and found their $doc_type ($id_number). Awaiting your approval. Alert #$alertId.", $alertId);
    if (!empty($station)) {
        createNotification($conn, 'match_found', 'station', $station,
            "An owner found a match for a $doc_type you're holding. Awaiting your confirmation. Alert #$alertId.", $alertId);
    }
    return $alertId;
}

/**
 * Apply an IOTec Pay transaction status (from callback or a status poll) to
 * our payments row. Idempotent — safe to call more than once for the same
 * transaction. Returns ['status' => 'confirmed'|'failed'|'pending', 'message' => string].
 */
function applyPaymentStatus(mysqli $conn, int $paymentId, array $iotec): array {
    $stmt = $conn->prepare("SELECT * FROM payments WHERE id=? LIMIT 1");
    $stmt->bind_param('i', $paymentId);
    $stmt->execute();
    $pay = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$pay) return ['status' => 'failed', 'message' => 'Payment record not found.'];

    if ($pay['status'] === 'confirmed') {
        return ['status' => 'confirmed', 'message' => 'Payment already confirmed.'];
    }

    $iotecStatus = $iotec['status'] ?? 'Pending';
    $vendor      = $iotec['vendor'] ?? null;
    $payloadJson = json_encode($iotec);

    if ($iotecStatus === 'Success') {
        // Look up the doc type via the linked match alert -> lost report to compute commission
        $feeInfo = ['fee_ugx' => (float)$pay['amount'], 'commission_percent' => 20.00];
        if (!empty($pay['match_alert_id'])) {
            $q = $conn->prepare(
                "SELECT lr.doc_type FROM match_alerts ma
                 JOIN lost_reports lr ON lr.id = ma.lost_report_id
                 WHERE ma.id=? LIMIT 1"
            );
            $q->bind_param('i', $pay['match_alert_id']);
            $q->execute();
            $docTypeRow = $q->get_result()->fetch_assoc();
            $q->close();
            if ($docTypeRow) $feeInfo = getFeeConfig($conn, $docTypeRow['doc_type']);
        }
        $commission = round((float)$pay['amount'] * ($feeInfo['commission_percent'] / 100), 2);
        $vcode = $pay['verification_code'] ?: generateVerificationCode($conn);

        // Map IOTec vendor string → our provider enum (MTN|Airtel|other)
        $providerVal = match(strtolower((string)$vendor)) {
            'mtn'    => 'MTN',
            'airtel' => 'Airtel',
            default  => 'other',
        };
        // Store the raw IOTec status string for auditability
        $rawIotecStatus = $iotecStatus; // 'Success'

        $upd = $conn->prepare(
            "UPDATE payments SET status='confirmed', confirmed_at=NOW(), download_allowed=1,
                    iotec_status=?, provider=?, station_commission=?, verification_code=?, callback_payload=?
             WHERE id=?"
        );
        $upd->bind_param('ssdssi', $rawIotecStatus, $providerVal, $commission, $vcode, $payloadJson, $paymentId);
        $upd->execute();
        $upd->close();

        if (!empty($pay['match_alert_id'])) {
            $conn->query("UPDATE match_alerts SET alert_status='paid', updated_at=NOW() WHERE id=" . (int)$pay['match_alert_id']);
            $st = $conn->query("SELECT station FROM match_alerts WHERE id=" . (int)$pay['match_alert_id'])->fetch_assoc();
            $station = $st['station'] ?? null;
            createNotification($conn, 'payment_confirmed', 'admin', null,
                "Payment CONFIRMED — {$pay['payer_name']} ({$pay['payer_phone']}) paid UGX " . number_format((float)$pay['amount']) . " for ID {$pay['id_number']}.", $paymentId);
            if ($station) {
                createNotification($conn, 'payment_confirmed', 'station', $station,
                    "Payment confirmed for document {$pay['id_number']}. Your commission: UGX " . number_format($commission) . ". Owner will collect soon.", $paymentId);
            }

            notifyTeamOfPaymentConfirmed([
                'doc_type'    => $docTypeRow['doc_type'] ?? '',
                'id_number'   => $pay['id_number'],
                'payer_name'  => $pay['payer_name'],
                'payer_phone' => $pay['payer_phone'],
                'amount'      => $pay['amount'],
                'station'     => $station,
                'payment_id'  => $paymentId,
            ]);
        }
        return ['status' => 'confirmed', 'message' => 'Payment confirmed.'];
    }

    if ($iotecStatus === 'Failed') {
        $upd = $conn->prepare("UPDATE payments SET status='failed', iotec_status=?, callback_payload=? WHERE id=?");
        $upd->bind_param('ssi', $iotecStatus, $payloadJson, $paymentId);
        $upd->execute();
        $upd->close();
        // Revert match alert to payment_pending so the owner can try again
        if (!empty($pay['match_alert_id'])) {
            $conn->query("UPDATE match_alerts SET alert_status='payment_pending', updated_at=NOW() WHERE id=" . (int)$pay['match_alert_id']);
        }
        return ['status' => 'failed', 'message' => 'Payment was not completed. You can try again.'];
    }

    // Still Pending / SentToVendor / AwaitingApproval / Scheduled
    $upd = $conn->prepare("UPDATE payments SET status='pending', iotec_status=?, callback_payload=? WHERE id=?");
    $upd->bind_param('ssi', $iotecStatus, $payloadJson, $paymentId);
    $upd->execute();
    $upd->close();
    return ['status' => 'pending', 'message' => 'Waiting for you to approve the payment prompt on your phone.'];
}


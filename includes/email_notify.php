<?php
// ─────────────────────────────────────────────
// Email notifications — internal Kakebe team alerts
// ─────────────────────────────────────────────
require_once __DIR__ . '/match_engine.php';

const REPORT_NOTIFY_TO = 'kakebetech.comms@gmail.com';
const REPORT_NOTIFY_CC = ['komabono1998@gmail.com', 'jeromeoscar2002@gmail.com', 'derricklamarh@gmail.com', 'oscarbrianojok@gmail.com'];

/**
 * Shared sender for every internal Kakebe-team notification email — same
 * To/Cc list, same simple label/value table layout. Never throws; a
 * failed send is only logged so it can never break the caller's flow.
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

    $headers   = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: iRecovery Uganda <noreply@irecover.site>';
    $headers[] = 'Cc: ' . implode(', ', REPORT_NOTIFY_CC);
    if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $headers[] = 'Reply-To: ' . $replyTo;
    }

    if (!@mail(REPORT_NOTIFY_TO, $subject, $body, implode("\r\n", $headers))) {
        error_log('iRecovery: failed to send team notification email (' . $logContext . ')');
    }
}

/**
 * Emails the Kakebe team whenever a lost-document report comes in.
 */
function notifyTeamOfLostReport(array $r): void {
    $docTypeLabel = ucwords(str_replace('_', ' ', $r['doc_type'] ?? ''));
    $fullName     = trim(($r['sur_name'] ?? '') . ' ' . ($r['given_name'] ?? ''));

    sendTeamNotificationEmail(
        'New Lost Document Report — ' . $docTypeLabel . ($fullName !== '' ? ' — ' . $fullName : ''),
        'New Lost Document Report',
        [
            'Document Type'  => $docTypeLabel,
            'Name'           => $fullName !== '' ? $fullName : '—',
            'Date of Birth'  => $r['dob'] ?: '—',
            'ID / Reference' => $r['id_number'] ?: '—',
            'Reporter Name'  => $r['reporter_name'] ?: '—',
            'Reporter Phone' => $r['reporter_phone'] ?: '—',
            'Reporter Email' => $r['reporter_email'] ?: '—',
            'Report ID'      => '#' . (int)($r['lost_id'] ?? 0),
            'Submitted At'   => date('Y-m-d H:i:s'),
        ],
        $r['reporter_email'] ?? null,
        'lost_report #' . (int)($r['lost_id'] ?? 0)
    );
}

/**
 * Emails the Kakebe team whenever a public search finds a match — a real
 * person just confirmed their document is here and may need follow-up
 * (calling them, verifying the match) even before they pay.
 */
function notifyTeamOfSearchMatch(array $m): void {
    $docTypeLabel = ucwords(str_replace('_', ' ', $m['doc_type'] ?? ''));
    $fullName     = trim(($m['sur_name'] ?? '') . ' ' . ($m['given_name'] ?? ''));

    sendTeamNotificationEmail(
        'Search Match — ' . $docTypeLabel . ($fullName !== '' ? ' — ' . $fullName : ''),
        'Public Search Matched a Document',
        [
            'Document Type'   => $docTypeLabel,
            'Name'            => $fullName !== '' ? $fullName : '—',
            'ID / Reference'  => $m['id_number'] ?: '—',
            'Station Holding' => $m['station_holding'] ?: '—',
            'Searcher Phone'  => $m['searcher_phone'] ?: '—',
            'Document ID'     => '#' . (int)($m['document_id'] ?? 0),
            'Searched At'     => date('Y-m-d H:i:s'),
        ],
        null,
        'search match on document #' . (int)($m['document_id'] ?? 0)
    );
}

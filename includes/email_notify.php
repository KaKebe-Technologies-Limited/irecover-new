<?php
// ─────────────────────────────────────────────
// Email notifications — internal Kakebe team alerts
// ─────────────────────────────────────────────
require_once __DIR__ . '/match_engine.php';

const REPORT_NOTIFY_TO = 'kakebetech.comms@gmail.com';
const REPORT_NOTIFY_CC = ['komabono1998@gmail.com', 'jeromeoscar2002@gmail.com', 'derricklamarh@gmail.com'];

/**
 * Emails the Kakebe team whenever a lost-document report comes in.
 * Never throws — a failed send must not break the report flow, it only
 * gets logged.
 */
function notifyTeamOfLostReport(array $r): void {
    $docTypeLabel = ucwords(str_replace('_', ' ', $r['doc_type'] ?? ''));
    $fullName     = trim(($r['sur_name'] ?? '') . ' ' . ($r['given_name'] ?? ''));

    $subject = 'New Lost Document Report — ' . $docTypeLabel . ($fullName !== '' ? ' — ' . $fullName : '');

    $rows = [
        'Document Type'  => $docTypeLabel,
        'Name'           => $fullName !== '' ? $fullName : '—',
        'Date of Birth'  => $r['dob'] ?: '—',
        'ID / Reference' => $r['id_number'] ?: '—',
        'Reporter Name'  => $r['reporter_name'] ?: '—',
        'Reporter Phone' => $r['reporter_phone'] ?: '—',
        'Reporter Email' => $r['reporter_email'] ?: '—',
        'Report ID'      => '#' . (int)($r['lost_id'] ?? 0),
        'Submitted At'   => date('Y-m-d H:i:s'),
    ];

    $body = '<div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#222;">';
    $body .= '<h2 style="color:#CC0000;margin:0 0 .75rem;">New Lost Document Report</h2>';
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
    if (!empty($r['reporter_email']) && filter_var($r['reporter_email'], FILTER_VALIDATE_EMAIL)) {
        $headers[] = 'Reply-To: ' . $r['reporter_email'];
    }

    $sent = @mail(REPORT_NOTIFY_TO, $subject, $body, implode("\r\n", $headers));
    if (!$sent) {
        error_log('iRecovery: failed to send report-notification email for lost_report #' . (int)($r['lost_id'] ?? 0));
    }
}

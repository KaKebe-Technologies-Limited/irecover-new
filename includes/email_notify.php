<?php
// ─────────────────────────────────────────────
// Email notifications — internal Kakebe team alerts
// (shared REPORT_NOTIFY_TO/CC + sendTeamNotificationEmail live in
// match_engine.php, since payment confirmation also needs them there)
// ─────────────────────────────────────────────
require_once __DIR__ . '/match_engine.php';

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

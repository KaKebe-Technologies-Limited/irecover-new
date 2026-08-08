<?php
// ─────────────────────────────────────────────
// Public status tracker — enter your document ID/NIN to see exactly
// where your recovery stands, and pickup info once it's ready.
// ─────────────────────────────────────────────
include_once 'db.php';

// admins.number is stored as INT, which drops a leading 0 on Ugandan numbers
function formatUgPhone(?string $n): string {
    $n = trim((string)$n);
    if ($n === '') return '';
    if (preg_match('/^[1-9][0-9]{8}$/', $n)) return '0' . $n; // 9 digits, no leading 0 -> restore it
    return $n;
}

$id_number = trim(strtoupper($_GET['id_number'] ?? $_POST['id_number'] ?? ''));
$stage     = null; // 'awaiting_admin' | 'awaiting_station' | 'ready_to_pay' | 'payment_in_progress' | 'ready_for_pickup' | 'collected' | 'found_unlinked' | 'not_found'
$info      = [];
$DEFAULT_CONTACT = '0777676206';

if ($id_number !== '') {
    $stmt = $conn->prepare(
        "SELECT ma.id AS alert_id, ma.alert_status, ma.admin_approved, ma.station_approved, ma.station,
                lr.doc_type, lr.sur_name, lr.given_name,
                p.id AS payment_id, p.status AS pay_status, p.download_allowed,
                a.number AS station_phone
         FROM match_alerts ma
         JOIN lost_reports lr ON lr.id = ma.lost_report_id
         LEFT JOIN payments p ON p.match_alert_id = ma.id
         LEFT JOIN admins a ON a.user_name = ma.station
         WHERE lr.id_number = ?
         ORDER BY ma.created_at DESC LIMIT 1"
    );
    $stmt->bind_param('s', $id_number);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $info = $row;
        if ($row['alert_status'] === 'collected') {
            $stage = 'collected';
        } elseif ($row['pay_status'] === 'confirmed' && (int)$row['download_allowed'] === 1) {
            $stage = 'ready_for_pickup';
        } elseif (in_array($row['pay_status'], ['initiated', 'pending'], true)) {
            $stage = 'payment_in_progress';
        } elseif ((int)$row['admin_approved'] === 1 && (int)$row['station_approved'] === 1) {
            $stage = 'ready_to_pay';
        } elseif ((int)$row['admin_approved'] === 1) {
            $stage = 'awaiting_station';
        } else {
            $stage = 'awaiting_admin';
        }
    } else {
        // No report/match yet — check if it exists as a found document at all
        // (new unified table first, then legacy tables for older data)
        $found = false;
        $d = $conn->prepare("SELECT id FROM documents WHERE id_number=? AND action IN ('found','matched') LIMIT 1");
        $d->bind_param('s', $id_number);
        $d->execute();
        $found = (bool)$d->get_result()->fetch_assoc();
        $d->close();

        if (!$found) {
            $legacyChecks = [
                'national_ids'    => 'nin_number',
                'driving_permits' => 'permit_number',
                'student_ids'     => 'student_number',
            ];
            foreach ($legacyChecks as $table => $col) {
                $lq = $conn->prepare("SELECT 1 FROM `$table` WHERE `$col`=? AND user_action='Found' LIMIT 1");
                $lq->bind_param('s', $id_number);
                $lq->execute();
                if ($lq->get_result()->fetch_assoc()) { $found = true; $lq->close(); break; }
                $lq->close();
            }
        }
        $stage = $found ? 'found_unlinked' : 'not_found';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Document | iRecovery</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/1570/1570887.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --red:#CC0000; --orange:#ff6f00; --green:#15803d; --navy:#0f172a; }
        * { box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:url('img/bg.jpg') center/cover fixed; min-height:100vh; display:flex; flex-direction:column; position:relative; margin:0; }
        body::before { content:''; position:fixed; inset:0; background:rgba(0,0,0,0.65); z-index:0; }
        .wrap { position:relative; z-index:1; flex:1; display:flex; align-items:flex-start; justify-content:center; padding:3rem 1rem; }
        .card { background:#fff; border-radius:1.25rem; padding:2.25rem 2rem; max-width:560px; width:100%; box-shadow:0 16px 48px rgba(0,0,0,0.3); }
        h1 { font-size:1.35rem; font-weight:700; text-align:center; margin-bottom:.3rem; }
        .sub { text-align:center; color:#666; font-size:.88rem; margin-bottom:1.5rem; }
        .form-control { border-radius:.6rem; padding:.7rem 1rem; border:1.5px solid #ddd; }
        .btn-track { width:100%; padding:.8rem; background:var(--navy); color:#fff; border:none; border-radius:50px; font-weight:700; }
        .btn-track:hover { background:#1e293b; color:#fff; }

        .stage-box { border-radius:1rem; padding:1.5rem; margin-top:1.5rem; text-align:center; }
        .stage-icon { font-size:2.5rem; margin-bottom:.75rem; display:block; }
        .stage-box h3 { font-size:1.1rem; font-weight:700; margin-bottom:.4rem; }
        .stage-box p { font-size:.88rem; color:#555; margin:0; }

        .stage-amber { background:#fffbeb; border:1px solid #fde68a; }
        .stage-amber .stage-icon, .stage-amber h3 { color:#b45309; }
        .stage-blue  { background:#eff6ff; border:1px solid #bfdbfe; }
        .stage-blue  .stage-icon, .stage-blue h3 { color:#1d4ed8; }
        .stage-teal  { background:#f0fdfa; border:1px solid #99f6e4; }
        .stage-teal  .stage-icon, .stage-teal h3 { color:#0f766e; }
        .stage-green { background:#f0fdf4; border:1px solid #bbf7d0; }
        .stage-green .stage-icon, .stage-green h3 { color:#15803d; }
        .stage-grey  { background:#f8fafc; border:1px solid #e2e8f0; }
        .stage-grey  .stage-icon, .stage-grey h3 { color:#64748b; }

        .pickup-detail { background:#fff; border:1px dashed #bbf7d0; border-radius:.75rem; padding:1rem; margin-top:1rem; text-align:left; }
        .pickup-detail div { font-size:.88rem; margin-bottom:.4rem; }
        .pickup-detail strong { color:#14532d; }

        .steps-track { list-style:none; padding:0; margin:1.5rem 0 0; }
        .steps-track li { display:flex; gap:.6rem; align-items:flex-start; font-size:.82rem; color:#666; padding:.35rem 0; }
        .steps-track .sn { width:20px; height:20px; border-radius:50%; background:#e2e8f0; color:#475569; display:flex; align-items:center; justify-content:center; font-size:.68rem; font-weight:700; flex-shrink:0; }
        .steps-track li.done .sn { background:var(--green); color:#fff; }
        .steps-track li.active .sn { background:var(--orange); color:#fff; }

        footer { position:relative; z-index:1; text-align:center; padding:.8rem; color:#ccc; font-size:.82rem; }
        footer a { color:#aaa; text-decoration:none; }
    </style>
</head>
<body>
<div class="wrap">
<div class="card">
    <h1><i class="bi bi-signpost-split me-2" style="color:var(--orange);"></i>Track Your Document</h1>
    <p class="sub">Enter your ID / NIN number to see exactly where your recovery stands.</p>

    <form method="GET">
        <input type="text" name="id_number" class="form-control mb-3" placeholder="e.g. CM90103100DLAH" value="<?= htmlspecialchars($id_number) ?>" required autocomplete="off">
        <button type="submit" class="btn-track"><i class="bi bi-search me-1"></i> Check Status</button>
    </form>

    <?php if ($stage): ?>
        <?php
        $stepOrder = ['awaiting_admin', 'awaiting_station', 'ready_to_pay', 'payment_in_progress', 'ready_for_pickup'];
        $curIdx = array_search($stage, $stepOrder, true);
        ?>

        <?php if ($stage === 'not_found'): ?>
            <div class="stage-box stage-grey">
                <i class="bi bi-search stage-icon"></i>
                <h3>No Record Found</h3>
                <p>We couldn't find anything for <strong><?= htmlspecialchars($id_number) ?></strong> yet. If you've lost a document, report it below.</p>
            </div>
            <div class="text-center mt-3"><a href="index.php#services" class="btn btn-outline-secondary btn-sm">Report It Lost</a></div>

        <?php elseif ($stage === 'found_unlinked'): ?>
            <div class="stage-box stage-blue">
                <i class="bi bi-flag stage-icon"></i>
                <h3>Document Found — Report It</h3>
                <p>A document matching <strong><?= htmlspecialchars($id_number) ?></strong> has been found, but you haven't reported it lost yet. Please report it or search for it so our team can start verifying the match.</p>
            </div>
            <div class="text-center mt-3">
                <a href="search_id.php" class="btn btn-outline-secondary btn-sm me-2">Search</a>
                <a href="index.php#services" class="btn btn-danger btn-sm">Report It Lost</a>
            </div>

        <?php elseif ($stage === 'awaiting_admin'): ?>
            <div class="stage-box stage-amber">
                <i class="bi bi-hourglass-split stage-icon"></i>
                <h3>Match Found — Awaiting Verification</h3>
                <p>Great news, a match exists! Our admin team is verifying the details. Call <strong><?= htmlspecialchars($DEFAULT_CONTACT) ?></strong> to speed this up.</p>
            </div>

        <?php elseif ($stage === 'awaiting_station'): ?>
            <div class="stage-box stage-amber">
                <i class="bi bi-building-check stage-icon"></i>
                <h3>Admin Verified — Awaiting Station Confirmation</h3>
                <p>Our admin team has verified your match. The holding station just needs to confirm they physically have the document. Almost there!</p>
            </div>

        <?php elseif ($stage === 'ready_to_pay'): ?>
            <div class="stage-box stage-teal">
                <i class="bi bi-check-circle stage-icon"></i>
                <h3>Approved — Ready to Pay</h3>
                <p>Your match has been fully verified. Pay the recovery fee to unlock your pickup code.</p>
            </div>
            <div class="text-center mt-3"><a href="pay.php?id_number=<?= urlencode($id_number) ?>" class="btn btn-danger">Pay Now</a></div>

        <?php elseif ($stage === 'payment_in_progress'): ?>
            <div class="stage-box stage-blue">
                <i class="bi bi-phone stage-icon"></i>
                <h3>Payment In Progress</h3>
                <p>We're waiting for your Mobile Money payment to be approved. This page will update once it's confirmed.</p>
            </div>

        <?php elseif ($stage === 'ready_for_pickup'): ?>
            <div class="stage-box stage-green">
                <i class="bi bi-gift stage-icon"></i>
                <h3>Your Document Has Been Found!</h3>
                <p>Payment confirmed. Kindly go to the station below to collect it.</p>
                <div class="pickup-detail">
                    <div><i class="bi bi-building me-1"></i> Station: <strong><?= htmlspecialchars($info['station'] ?? 'Contact Admin') ?></strong></div>
                    <div><i class="bi bi-telephone me-1"></i> Call: <strong><?= htmlspecialchars(formatUgPhone($info['station_phone'] ?? '') ?: $DEFAULT_CONTACT) ?></strong></div>
                </div>
            </div>
            <div class="text-center mt-3"><a href="receipt.php?pid=<?= (int)$info['payment_id'] ?>" class="btn btn-outline-secondary btn-sm">View Receipt</a></div>

        <?php elseif ($stage === 'collected'): ?>
            <div class="stage-box stage-grey">
                <i class="bi bi-check2-all stage-icon"></i>
                <h3>Already Collected</h3>
                <p>This document has already been picked up. This was a successful recovery — thank you for using iRecovery!</p>
            </div>
        <?php endif; ?>

        <?php if ($curIdx !== false): ?>
        <ul class="steps-track">
            <?php
            $labels = [
                'awaiting_admin'      => 'Admin verification',
                'awaiting_station'    => 'Station confirmation',
                'ready_to_pay'        => 'Payment',
                'payment_in_progress' => 'Payment',
                'ready_for_pickup'    => 'Ready for pickup',
            ];
            $shown = ['awaiting_admin' => 1, 'awaiting_station' => 2, 'ready_to_pay' => 3, 'payment_in_progress' => 3, 'ready_for_pickup' => 4];
            $curStep = $shown[$stage];
            foreach (['Admin verification' => 1, 'Station confirmation' => 2, 'Payment' => 3, 'Ready for pickup' => 4] as $label => $n):
                $cls = $n < $curStep ? 'done' : ($n === $curStep ? 'active' : '');
            ?>
                <li class="<?= $cls ?>"><div class="sn"><?= $n < $curStep ? '✓' : $n ?></div><span><?= $label ?></span></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    <?php endif; ?>
</div>
</div>
<footer>&copy; <?= date('Y') ?> iRecovery &mdash; <a href="https://kakebe.tech/" target="_blank" rel="noopener">Kakebe Technologies Limited</a></footer>
</body>
</html>

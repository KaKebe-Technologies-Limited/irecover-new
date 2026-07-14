<?php
// ─────────────────────────────────────────────
// Payment Receipt
// Only downloadable when admin has approved
// ─────────────────────────────────────────────
include_once 'db.php';

$pid = (int)($_GET['pid'] ?? 0);
if (!$pid) { header('Location: index.php'); exit(); }

$stmt = $conn->prepare(
    "SELECT p.*, ma.station,
            lr.doc_type as lr_doc_type, lr.sur_name, lr.given_name
     FROM payments p
     LEFT JOIN match_alerts ma ON ma.id = p.match_alert_id
     LEFT JOIN lost_reports  lr ON lr.id = ma.lost_report_id
     WHERE p.id = ? LIMIT 1"
);
$stmt->bind_param('i', $pid);
$stmt->execute();
$pay = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pay) { header('Location: index.php'); exit(); }

$approved      = (int)$pay['download_allowed'] === 1 && $pay['status'] === 'confirmed';
$payer         = htmlspecialchars($pay['payer_name']  ?? 'N/A');
$phone         = htmlspecialchars($pay['payer_phone'] ?? 'N/A');
$id_number_raw = $pay['id_number'] ?? '';
$id_display    = htmlspecialchars($id_number_raw);
$amount        = number_format((float)($pay['amount'] ?? 30000));
$station       = htmlspecialchars($pay['station']     ?? 'iRecovery Station');
$date          = date('d F Y, h:i A', strtotime($pay['initiated_at'] ?? 'now'));
$ref           = 'IRCV-' . str_pad($pid, 6, '0', STR_PAD_LEFT);
$doc_type      = $pay['lr_doc_type'] ?? '';
$doc_label     = strtoupper(str_replace('_', ' ', $doc_type));
$owner_name    = htmlspecialchars(trim(($pay['sur_name'] ?? '') . ' ' . ($pay['given_name'] ?? ''))) ?: $payer;
$vcode_raw     = $pay['verification_code'] ?? '';
// Format code as XXXX-XXXX-XX for readability
$vcode_fmt = '';
if (strlen($vcode_raw) === 10) {
    $vcode_fmt = substr($vcode_raw,0,4) . '-' . substr($vcode_raw,4,4) . '-' . substr($vcode_raw,8,2);
} else {
    $vcode_fmt = $vcode_raw;
}
$vcode_display = htmlspecialchars($vcode_fmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $approved ? "Receipt #$ref" : "Pending Approval" ?> | iRecovery</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/1570/1570887.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --navy:#0f172a; --blue:#2563eb; --green:#16a34a; --amber:#d97706; --orange:#ea580c; }
        * { box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:#f1f5f9; min-height:100vh; display:flex; flex-direction:column; margin:0; }

        /* Screen shell */
        .screen { background:url('img/bg.jpg') center/cover fixed; flex:1; display:flex; flex-direction:column; align-items:center; justify-content:flex-start; padding:2rem 1rem; position:relative; }
        .screen::before { content:''; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:0; }
        .screen-inner { position:relative; z-index:1; width:100%; max-width:680px; }

        /* Action bar */
        .action-bar { display:flex; gap:.75rem; justify-content:center; flex-wrap:wrap; margin-bottom:1.5rem; }
        .btn-dl { background:var(--blue); color:#fff; border:none; border-radius:50px; padding:.65rem 1.5rem; font-weight:600; font-size:.9rem; display:inline-flex; align-items:center; gap:.4rem; cursor:pointer; transition:all .2s; text-decoration:none; }
        .btn-dl:hover { background:#1d4ed8; color:#fff; }
        .btn-home { background:rgba(255,255,255,.15); color:#fff; border:1px solid rgba(255,255,255,.3); border-radius:50px; padding:.65rem 1.5rem; font-size:.9rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; transition:all .2s; }
        .btn-home:hover { background:rgba(255,255,255,.25); color:#fff; }

        /* Pending state card */
        .pending-card { background:#fff; border-radius:1rem; padding:2.5rem 2rem; text-align:center; box-shadow:0 16px 48px rgba(0,0,0,.3); }
        .pending-icon { width:72px; height:72px; border-radius:50%; background:#fffbeb; border:2px solid #fcd34d; display:flex; align-items:center; justify-content:center; font-size:2rem; color:var(--amber); margin:0 auto 1.25rem; }
        .pending-card h2 { font-size:1.4rem; font-weight:700; color:var(--navy); margin-bottom:.5rem; }
        .pending-card p  { color:#64748b; font-size:.9rem; }
        .pending-steps { background:#f8fafc; border-radius:.75rem; padding:1rem 1.25rem; margin:1.25rem 0; text-align:left; }
        .pending-steps h6 { font-size:.82rem; font-weight:700; color:#1e293b; margin-bottom:.6rem; }
        .p-step { display:flex; gap:.5rem; font-size:.82rem; color:#475569; margin-bottom:.35rem; }
        .p-step:last-child { margin:0; }
        .psn { width:18px; height:18px; border-radius:50%; background:var(--amber); color:#fff; display:flex; align-items:center; justify-content:center; font-size:.65rem; font-weight:700; flex-shrink:0; margin-top:.1rem; }

        /* ═══ RECEIPT ═════════════════════════════ */
        .receipt { background:#fff; border-radius:1rem; overflow:hidden; box-shadow:0 16px 48px rgba(0,0,0,.3); }

        .receipt-top { background:var(--navy); padding:1.75rem 2rem; display:flex; align-items:center; gap:1rem; }
        .receipt-top img { height:40px; filter:brightness(0) invert(1); }
        .receipt-top-text h2 { color:#fff; font-size:1.25rem; font-weight:700; margin:0 0 .15rem; }
        .receipt-top-text p  { color:rgba(255,255,255,.65); font-size:.8rem; margin:0; }

        .receipt-status-bar { background:var(--green); color:#fff; padding:.65rem 2rem; font-size:.85rem; font-weight:600; display:flex; align-items:center; gap:.5rem; }

        .receipt-body { padding:1.75rem 2rem; }

        /* Reference */
        .ref-block { background:#f8fafc; border:1px solid #e2e8f0; border-radius:.75rem; padding:.85rem 1rem; display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:.5rem; }
        .ref-label  { font-size:.72rem; text-transform:uppercase; letter-spacing:.5px; color:#94a3b8; }
        .ref-value  { font-size:1.1rem; font-weight:700; color:var(--navy); }
        .ref-date   { font-size:.8rem; color:#94a3b8; }

        /* Detail rows */
        .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:.1rem; margin-bottom:1.5rem; }
        .d-item { padding:.6rem .25rem; border-bottom:1px solid #f1f5f9; }
        .d-item:nth-last-child(-n+2) { border:none; }
        .d-label { font-size:.72rem; text-transform:uppercase; letter-spacing:.4px; color:#94a3b8; margin-bottom:.2rem; }
        .d-value  { font-size:.88rem; font-weight:600; color:var(--navy); }

        /* Amount */
        .amount-row { background:linear-gradient(135deg,#fff7ed,#ffedd5); border:1px solid #fed7aa; border-radius:.75rem; padding:1rem 1.25rem; display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; }
        .amount-lbl { font-size:.8rem; color:#9a3412; }
        .amount-val { font-size:1.6rem; font-weight:700; color:var(--orange); }

        /* Verification code — THE KEY ELEMENT */
        .vcode-block {
            background:var(--navy);
            border-radius:.75rem;
            padding:1.25rem;
            text-align:center;
            margin-bottom:1.5rem;
        }
        .vcode-label { font-size:.72rem; text-transform:uppercase; letter-spacing:1px; color:rgba(255,255,255,.55); margin-bottom:.6rem; }
        .vcode-value {
            font-size:1.8rem;
            font-weight:700;
            color:#fff;
            letter-spacing:.2rem;
            font-family:'Courier New', monospace;
            background:rgba(255,255,255,.08);
            border:1px solid rgba(255,255,255,.15);
            border-radius:.5rem;
            padding:.5rem 1rem;
            display:inline-block;
            margin:.3rem 0;
        }
        .vcode-hint { font-size:.78rem; color:rgba(255,255,255,.5); margin-top:.4rem; }

        /* Steps */
        .steps-box { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:.75rem; padding:1rem 1.25rem; margin-bottom:1rem; }
        .steps-box h6 { font-size:.82rem; font-weight:700; color:#14532d; margin-bottom:.6rem; }
        .s-step { display:flex; gap:.6rem; font-size:.82rem; color:#166534; margin-bottom:.35rem; }
        .s-step:last-child { margin:0; }
        .ssn { width:20px; height:20px; border-radius:50%; background:var(--green); color:#fff; display:flex; align-items:center; justify-content:center; font-size:.68rem; font-weight:700; flex-shrink:0; margin-top:.1rem; }

        /* Footer */
        .receipt-footer { background:#f8fafc; border-top:1px solid #e2e8f0; padding:.85rem 2rem; text-align:center; font-size:.75rem; color:#94a3b8; }
        .receipt-footer strong { color:#475569; }

        /* Print */
        @media print {
            body { background:none; }
            .screen::before, .action-bar { display:none !important; }
            .receipt { box-shadow:none; border-radius:0; }
            .screen { padding:0; background:none; }
            @page { margin:1.5cm; }
        }
        @media (max-width:520px) {
            .receipt-top, .receipt-body { padding:1.25rem 1rem; }
            .detail-grid { grid-template-columns:1fr; }
            .vcode-value { font-size:1.3rem; letter-spacing:.1rem; }
        }

        /* Page footer */
        footer { text-align:center; padding:.75rem; color:#94a3b8; font-size:.8rem; background:#0f172a; }
        footer a { color:#64748b; text-decoration:none; }
    </style>
</head>
<body>

<?php if (!$approved): ?>
<!-- ═══ PENDING STATE ═══════════════════════════════════════ -->
<div class="screen">
<div class="screen-inner">
    <div class="action-bar">
        <a href="index.php" class="btn-home"><i class="bi bi-house"></i> Back to Home</a>
    </div>
    <div class="pending-card">
        <div class="pending-icon"><i class="bi bi-hourglass-split"></i></div>
        <h2>Payment Pending Approval</h2>
        <p>Your payment of <strong>UGX 30,000</strong> has been received and is being reviewed by the iRecovery admin team.</p>

        <div class="pending-steps">
            <h6><i class="bi bi-info-circle me-1"></i>What happens next?</h6>
            <div class="p-step"><div class="psn">1</div><span>Admin verifies your payment against Mobile Money records.</span></div>
            <div class="p-step"><div class="psn">2</div><span>Once confirmed, your receipt is <strong>unlocked</strong> automatically.</span></div>
            <div class="p-step"><div class="psn">3</div><span>Return to <a href="get_receipt.php"><strong>iRecovery &rarr; Get Receipt</strong></a> and enter your ID number.</span></div>
            <div class="p-step"><div class="psn">4</div><span>Download your PDF receipt and take it to the station to collect your document.</span></div>
        </div>

        <p style="font-size:.82rem;color:#94a3b8;">Reference: <strong style="color:#1e293b;"><?= $ref ?></strong> &mdash; <?= $date ?></p>
        <a href="get_receipt.php" class="btn-dl mt-2" style="margin:0 auto;display:inline-flex;">
            <i class="bi bi-arrow-clockwise"></i> Check Again
        </a>
    </div>
</div>
</div>

<?php else: ?>
<!-- ═══ APPROVED RECEIPT ════════════════════════════════════ -->
<div class="screen">
<div class="screen-inner">

    <div class="action-bar">
        <button class="btn-dl" onclick="window.print()">
            <i class="bi bi-file-earmark-pdf"></i> Download PDF Receipt
        </button>
        <a href="index.php" class="btn-home">
            <i class="bi bi-house"></i> Home
        </a>
    </div>

    <div class="receipt" id="receipt">

        <div class="receipt-top">
            <img src="https://cdn-icons-png.flaticon.com/512/1570/1570887.png" alt="iRecovery">
            <div class="receipt-top-text">
                <h2>iRecovery</h2>
                <p>Document Recovery Platform &mdash; Official Payment Receipt</p>
            </div>
        </div>

        <div class="receipt-status-bar">
            <i class="bi bi-check-circle-fill"></i>
            Payment Approved &mdash; Present this receipt at the station to collect your document
        </div>

        <div class="receipt-body">

            <!-- Reference row -->
            <div class="ref-block">
                <div>
                    <div class="ref-label">Receipt Reference</div>
                    <div class="ref-value"><?= $ref ?></div>
                </div>
                <div class="ref-date"><?= $date ?></div>
            </div>

            <!-- Detail grid -->
            <div class="detail-grid">
                <div class="d-item">
                    <div class="d-label">Payer Name</div>
                    <div class="d-value"><?= $payer ?></div>
                </div>
                <div class="d-item">
                    <div class="d-label">Mobile Number</div>
                    <div class="d-value"><?= $phone ?></div>
                </div>
                <div class="d-item">
                    <div class="d-label">Document Owner</div>
                    <div class="d-value"><?= $owner_name ?></div>
                </div>
                <div class="d-item">
                    <div class="d-label">Document Type</div>
                    <div class="d-value"><?= htmlspecialchars($doc_label ?: 'Document') ?></div>
                </div>
                <div class="d-item">
                    <div class="d-label">Document ID / NIN</div>
                    <div class="d-value" style="font-family:monospace;letter-spacing:.5px;"><?= $id_display ?></div>
                </div>
                <div class="d-item">
                    <div class="d-label">Collection Station</div>
                    <div class="d-value"><?= $station ?></div>
                </div>
                <div class="d-item">
                    <div class="d-label">Payment Method</div>
                    <div class="d-value">Mobile Money</div>
                </div>
                <div class="d-item">
                    <div class="d-label">Payment Status</div>
                    <div class="d-value" style="color:var(--green);">&#10003; Confirmed &amp; Approved</div>
                </div>
            </div>

            <!-- Amount -->
            <div class="amount-row">
                <div class="amount-lbl">Total Amount Paid</div>
                <div class="amount-val">UGX <?= $amount ?></div>
            </div>

            <!-- VERIFICATION CODE — station scans this -->
            <div class="vcode-block">
                <div class="vcode-label">&#128274; Station Verification Code</div>
                <div class="vcode-value"><?= $vcode_display ?></div>
                <div class="vcode-hint">Station officer enters this code to verify payment and release your document</div>
            </div>

            <!-- Collection steps -->
            <div class="steps-box">
                <h6><i class="bi bi-clipboard2-check me-1"></i>How to collect your document</h6>
                <div class="s-step"><div class="ssn">1</div><span>Print or save this receipt on your phone.</span></div>
                <div class="s-step"><div class="ssn">2</div><span>Visit <strong><?= $station ?></strong> in person.</span></div>
                <div class="s-step"><div class="ssn">3</div><span>Show this receipt &mdash; the station officer will enter the <strong>verification code</strong> above.</span></div>
                <div class="s-step"><div class="ssn">4</div><span>Present a valid government-issued photo ID to confirm your identity.</span></div>
                <div class="s-step"><div class="ssn">5</div><span>Your document will be handed over once verified. &#127881;</span></div>
            </div>

        </div><!-- /.receipt-body -->

        <div class="receipt-footer">
            This is an official iRecovery payment receipt. Ref: <strong><?= $ref ?></strong> &mdash; Verification Code: <strong><?= $vcode_display ?></strong><br>
            &copy; <?= date('Y') ?> iRecovery &mdash; Powered by <strong>Kakebe Technologies Limited</strong> &mdash; kakebe.tech
        </div>

    </div><!-- /.receipt -->
</div>
</div>
<?php endif; ?>

<footer>&copy; <?= date('Y') ?> iRecovery &mdash; <a href="https://kakebe.tech/" target="_blank" rel="noopener">Kakebe Technologies Limited</a></footer>
<script>
<?php if (isset($_GET['pdf'])): ?>
window.addEventListener('load', () => window.print());
<?php endif; ?>
</script>
</body>
</html>

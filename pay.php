<?php
// ─────────────────────────────────────────────
// Pay to Recover — real IOTec Pay Mobile Money collection.
// Payable as soon as a match exists (no admin/station approval gate) —
// the moment a search finds it, the owner can pay right away.
// No PIN is ever collected here — IOTec prompts the payer on their
// own phone; we only ever see phone number + name.
// ─────────────────────────────────────────────
include_once 'db.php';
include_once 'includes/match_engine.php';
include_once 'includes/iotec_pay.php';

$id_number = trim(strtoupper($_GET['id_number'] ?? $_POST['id_number'] ?? ''));
$match     = null;   // match_alerts + lost_reports row, not yet paid
$fee       = 0.0;
$error     = '';
$initiated = null;   // ['payment_id' => .., 'transaction_id' => ..] once a collection has been started

function findPayableMatch(mysqli $conn, string $idNumber): ?array {
    if ($idNumber === '') return null;
    $stmt = $conn->prepare(
        "SELECT ma.id AS alert_id, ma.station, ma.document_id,
                lr.doc_type, lr.sur_name, lr.given_name, lr.id_number
         FROM match_alerts ma
         JOIN lost_reports lr ON lr.id = ma.lost_report_id
         WHERE lr.id_number = ?
           AND ma.alert_status NOT IN ('paid','collected','closed')
         ORDER BY ma.created_at DESC LIMIT 1"
    );
    $stmt->bind_param('s', $idNumber);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

if ($id_number !== '') {
    $match = findPayableMatch($conn, $id_number);
    if ($match) {
        $feeInfo = getFeeConfig($conn, $match['doc_type']);
        $fee = $feeInfo['fee_ugx'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payer_name'])) {
    $payer_name  = trim($_POST['payer_name']  ?? '');
    $payer_phone = preg_replace('/[^0-9]/', '', trim($_POST['payer_phone'] ?? ''));

    if (!$match) {
        $error = 'No payable match found for this document. Please search for it again.';
    } elseif (empty($payer_name) || strlen($payer_phone) < 9 || strlen($payer_phone) > 13) {
        $error = 'Please enter your full name and a valid Mobile Money phone number.';
    } else {
        // Detect MTN vs Airtel from the Ugandan number prefix
        // MTN prefixes: 077, 078, 076, 039; Airtel prefixes: 075, 070, 074
        $providerGuess = 'other';
        $phonePrefix   = substr($payer_phone, 0, 3);
        // Normalize: if number starts with 256, strip it first
        $normPhone = $payer_phone;
        if (str_starts_with($normPhone, '256')) {
            $normPhone   = '0' . substr($normPhone, 3);
            $phonePrefix = substr($normPhone, 0, 3);
        }
        if (in_array($phonePrefix, ['077','078','076','039'], true)) {
            $providerGuess = 'MTN';
        } elseif (in_array($phonePrefix, ['075','070','074'], true)) {
            $providerGuess = 'Airtel';
        }

        $ps = $conn->prepare(
            "INSERT INTO payments
             (match_alert_id, document_id, payer_name, payer_phone, id_number, amount, payment_method, provider, status, initiated_at)
             VALUES (?,?,?,?,?,?,'mobile_money',?,'initiated',NOW())"
        );
        $ps->bind_param('iisssds', $match['alert_id'], $match['document_id'], $payer_name, $payer_phone, $id_number, $fee, $providerGuess);
        $ps->execute();
        $payment_id = $conn->insert_id;
        $ps->close();

        try {
            $note = 'iRecovery document recovery fee - ' . ucwords(str_replace('_', ' ', $match['doc_type'])) . ' ' . $id_number;
            $resp = iotecInitiateCollection($fee, $payer_phone, $payer_name, (string)$payment_id, $note);

            $upd = $conn->prepare("UPDATE payments SET iotec_transaction_id=?, iotec_status=? WHERE id=?");
            $txnId = $resp['id'];
            $txnStatus = $resp['status'] ?? 'Pending';
            $upd->bind_param('ssi', $txnId, $txnStatus, $payment_id);
            $upd->execute();
            $upd->close();

            createNotification($conn, 'payment_confirmed', 'admin', null,
                "Payment INITIATED — $payer_name ($payer_phone) for " . strtoupper(str_replace('_', ' ', $match['doc_type'])) . " | ID: $id_number | UGX " . number_format($fee), $payment_id);
            if (!empty($match['station'])) {
                createNotification($conn, 'payment_confirmed', 'station', $match['station'],
                    "Payment initiated by $payer_name for document $id_number. Awaiting confirmation.", $payment_id);
            }

            $initiated = ['payment_id' => $payment_id, 'transaction_id' => $txnId];
        } catch (IotecPayException $e) {
            $conn->query("UPDATE payments SET status='failed', iotec_status='InitError' WHERE id=" . (int)$payment_id);
            $error = 'We could not start the Mobile Money payment. Please try again in a moment. (' . htmlspecialchars($e->getMessage()) . ')';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay to Recover | iRecovery</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/1570/1570887.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --red:#CC0000; --orange:#ff6f00; --orange-dark:#e65100; }
        *   { box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:url('img/bg.jpg') center/cover fixed; min-height:100vh; display:flex; flex-direction:column; position:relative; }
        body::before { content:''; position:fixed; inset:0; background:rgba(0,0,0,0.68); z-index:0; }
        .wrap { position:relative; z-index:1; flex:1; display:flex; align-items:center; justify-content:center; padding:2rem 1rem; }

        .pay-card {
            background:#fff; border-radius:1.25rem; padding:2.5rem 2rem;
            max-width:500px; width:100%;
            box-shadow:0 16px 48px rgba(0,0,0,0.3);
            animation:fadeUp .4s ease;
        }
        @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }

        .pay-header { text-align:center; margin-bottom:1.75rem; }
        .pay-icon {
            width:64px; height:64px; border-radius:50%;
            background:linear-gradient(135deg,#fff3e0,#ffe0b2);
            color:var(--orange); font-size:1.8rem;
            display:flex; align-items:center; justify-content:center;
            margin:0 auto 1rem;
        }
        .pay-header h1 { font-size:1.5rem; font-weight:700; color:#1a1a1a; margin-bottom:.3rem; }
        .pay-header p  { color:#666; font-size:.88rem; margin:0; }

        .fee-box {
            background:linear-gradient(135deg,#fff8e1,#fff3cd);
            border:2px solid #ffe082;
            border-radius:1rem; padding:1.25rem 1.5rem;
            display:flex; align-items:center; gap:1rem;
            margin-bottom:1.5rem;
        }
        .fee-num  { font-size:2rem; font-weight:700; color:var(--orange); line-height:1; }
        .fee-note { font-size:.8rem; color:#888; margin-top:.2rem; }

        .mm-steps { background:#f8f9fa; border-radius:.75rem; padding:1rem 1.25rem; margin-bottom:1.5rem; }
        .mm-steps h6 { font-size:.85rem; font-weight:700; color:#333; margin-bottom:.75rem; }
        .mm-step { display:flex; align-items:flex-start; gap:.6rem; font-size:.83rem; color:#555; margin-bottom:.45rem; }
        .mm-step:last-child { margin:0; }
        .ms-n {
            width:20px; height:20px; border-radius:50%;
            background:var(--orange); color:#fff;
            display:flex; align-items:center; justify-content:center;
            font-size:.7rem; font-weight:700; flex-shrink:0;
        }

        .form-label { font-weight:600; font-size:.88rem; color:#333; }
        .form-control, .form-select {
            border-radius:.6rem; padding:.7rem 1rem;
            border:1.5px solid #ddd; font-size:.92rem;
            transition:border-color .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color:var(--orange);
            box-shadow:0 0 0 3px rgba(255,111,0,0.15);
        }

        .btn-pay-now {
            display:flex; align-items:center; justify-content:center; gap:.5rem;
            width:100%; padding:.85rem 1.5rem;
            background:var(--orange); color:#fff; border:none;
            border-radius:50px; font-weight:700; font-size:1rem;
            cursor:pointer; transition:all .2s;
            box-shadow:0 4px 16px rgba(255,111,0,0.4);
        }
        .btn-pay-now:hover { background:var(--orange-dark); transform:translateY(-2px); box-shadow:0 6px 22px rgba(255,111,0,0.5); }
        .btn-pay-now:disabled { opacity:.6; cursor:not-allowed; transform:none; }

        .security-note { text-align:center; font-size:.78rem; color:#aaa; margin-top:.75rem; }

        .not-approved { text-align:center; padding:1rem 0; }
        .not-approved i { font-size:3rem; color:#ddd; display:block; margin-bottom:1rem; }

        /* Waiting-for-phone-approval screen */
        .waiting { text-align:center; padding:1rem 0; }
        .waiting .spin-icon { font-size:3rem; color:var(--orange); animation:spin 1.4s linear infinite; display:block; margin-bottom:1rem; }
        @keyframes spin { to { transform:rotate(360deg); } }
        .waiting h2 { font-size:1.25rem; font-weight:700; margin-bottom:.5rem; }
        .waiting p { color:#666; font-size:.9rem; }
        #waitStatus { margin-top:1rem; font-size:.85rem; color:#888; }

        footer { position:relative; z-index:1; text-align:center; padding:.8rem; color:#ccc; font-size:.82rem; }
        footer a { color:#aaa; text-decoration:none; }
    </style>
</head>
<body>
<div class="wrap">
<div class="pay-card">

    <?php if ($initiated): ?>
        <!-- ═══ Waiting for the payer to approve the prompt on their phone ═══ -->
        <div class="waiting" id="waitPanel" data-pid="<?= (int)$initiated['payment_id'] ?>">
            <i class="bi bi-arrow-repeat spin-icon"></i>
            <h2>Check Your Phone</h2>
            <p>We've sent a Mobile Money payment prompt to your phone for <strong>UGX <?= number_format($fee) ?></strong>. Enter your PIN there to approve it.</p>
            <div id="waitStatus">Waiting for approval…</div>
        </div>

    <?php elseif (!$match): ?>
        <!-- ═══ Lookup form ═══ -->
        <div class="pay-header">
            <div class="pay-icon"><i class="bi bi-phone"></i></div>
            <h1>Pay to Recover Your Document</h1>
            <p>Enter your document ID / NIN number to proceed.</p>
        </div>
        <?php if ($id_number): ?>
            <div class="not-approved">
                <i class="bi bi-hourglass-split"></i>
                <p>No payable match found for <strong><?= htmlspecialchars($id_number) ?></strong>.<br>
                Search for your document first — once it's found, you can pay right here.</p>
                <a href="index.php#services" class="btn btn-outline-secondary btn-sm mt-2">Search for Your Document</a>
            </div>
        <?php endif; ?>
        <form method="GET">
            <label class="form-label">Document ID / NIN Number</label>
            <input type="text" name="id_number" class="form-control mb-3" placeholder="e.g. CM90103100DLAH" required value="<?= htmlspecialchars($id_number) ?>">
            <button type="submit" class="btn-pay-now"><i class="bi bi-search"></i> Check</button>
        </form>

    <?php else: ?>
        <!-- ═══ Payer details form ═══ -->
        <div class="pay-header">
            <div class="pay-icon"><i class="bi bi-phone"></i></div>
            <h1>Pay to Recover Your Document</h1>
            <p>Complete Mobile Money payment to unlock your PDF receipt and collect your document.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 mb-3" style="font-size:.88rem;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="fee-box">
            <div>
                <div class="fee-num">UGX <?= number_format($fee) ?></div>
                <div class="fee-note">One-time document recovery fee</div>
            </div>
            <div style="margin-left:auto;font-size:.8rem;color:#888;text-align:right;">
                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $match['doc_type']))) ?><br>
                <strong style="color:#333;"><?= htmlspecialchars(trim($match['sur_name'] . ' ' . $match['given_name'])) ?></strong>
            </div>
        </div>

        <div class="mm-steps">
            <h6><i class="bi bi-info-circle me-1" style="color:var(--orange);"></i>How Mobile Money Payment Works</h6>
            <div class="mm-step"><div class="ms-n">1</div><span>Enter your name and Mobile Money number below.</span></div>
            <div class="mm-step"><div class="ms-n">2</div><span>You'll get a real payment prompt <strong>on your own phone</strong> — enter your PIN there, never on this site.</span></div>
            <div class="mm-step"><div class="ms-n">3</div><span>Once approved, your <strong>PDF receipt</strong> and pickup code are ready instantly.</span></div>
            <div class="mm-step"><div class="ms-n">4</div><span>Take the receipt to the holding station to collect your document.</span></div>
        </div>

        <form method="POST" id="payForm">
            <input type="hidden" name="id_number" value="<?= htmlspecialchars($id_number) ?>">

            <div class="mb-3">
                <label class="form-label">Your Full Name</label>
                <input type="text" name="payer_name" class="form-control" placeholder="Name as on your ID" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Mobile Money Phone Number</label>
                <input type="tel" name="payer_phone" class="form-control" placeholder="e.g. 0771234567" required maxlength="13">
                <small class="text-muted">MTN or Airtel number registered to your name</small>
            </div>

            <button type="submit" class="btn-pay-now" id="payBtn">
                <i class="bi bi-phone"></i> Send Payment Prompt — UGX <?= number_format($fee) ?>
            </button>
        </form>

        <div class="security-note">
            <i class="bi bi-shield-check me-1"></i>
            We never see or store your Mobile Money PIN — it's entered only on your own phone.
        </div>
    <?php endif; ?>

</div>
</div>
<footer>&copy; <?= date('Y') ?> iRecovery &mdash;
    <a href="https://kakebe.tech/" target="_blank" rel="noopener">Kakebe Technologies Limited</a>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const payForm = document.getElementById('payForm');
    if (payForm) {
        payForm.addEventListener('submit', function() {
            const btn = document.getElementById('payBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending prompt...';
        });
    }

    const waitPanel = document.getElementById('waitPanel');
    if (waitPanel) {
        const pid = waitPanel.dataset.pid;
        const statusEl = document.getElementById('waitStatus');
        let tries = 0;
        const poll = setInterval(async () => {
            tries++;
            try {
                const res = await fetch('payment_status.php?pid=' + pid);
                const data = await res.json();
                statusEl.textContent = data.message || 'Waiting for approval…';
                if (data.status === 'confirmed') {
                    clearInterval(poll);
                    statusEl.innerHTML = '<span style="color:#15803d;font-weight:600;">Payment confirmed! Redirecting…</span>';
                    setTimeout(() => { window.location.href = 'receipt.php?pid=' + pid; }, 1200);
                } else if (data.status === 'failed') {
                    clearInterval(poll);
                    statusEl.innerHTML = '<span style="color:#b91c1c;font-weight:600;">' + (data.message || 'Payment failed.') + '</span> <a href="pay.php?id_number=<?= urlencode($id_number) ?>">Try again</a>';
                }
            } catch (e) { /* keep polling */ }
            if (tries > 60) clearInterval(poll); // stop after ~5 minutes
        }, 5000);
    }
</script>
</body>
</html>

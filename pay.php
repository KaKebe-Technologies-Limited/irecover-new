<?php
// ─────────────────────────────────────────────
// Pay to Recover — UGX 30,000 fixed fee
// Creates payment record, redirects to receipt
// ─────────────────────────────────────────────
include_once 'db.php';
include_once 'includes/match_engine.php';

$doc_type   = trim($_GET['doc_type']  ?? $_POST['doc_type']  ?? '');
$id_number  = trim(strtoupper($_GET['id_number'] ?? $_POST['id_number'] ?? ''));
$station    = trim($_GET['station']   ?? $_POST['station']   ?? '');
$owner_name = trim($_GET['name']      ?? $_POST['name']      ?? '');
$fee        = 30000; // Fixed recovery fee UGX 30,000

$status     = null;
$error      = '';
$payment_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payer_name  = trim($_POST['payer_name']  ?? '');
    $payer_phone = trim($_POST['payer_phone'] ?? '');
    $mm_pin      = trim($_POST['mm_pin']      ?? ''); // client-side only — never stored
    $id_number   = trim(strtoupper($_POST['id_number'] ?? ''));
    $doc_type    = trim($_POST['doc_type']    ?? '');
    $station     = trim($_POST['station']     ?? '');
    $owner_name  = trim($_POST['owner_name']  ?? '');

    if (empty($payer_name) || empty($payer_phone) || strlen($payer_phone) < 10) {
        $error = 'Please enter your full name and a valid phone number.';
    } else {
        // Find matching alert (best-effort — may be null for legacy docs)
        $alert_id = null;
        $doc_id   = null;
        $as = $conn->prepare(
            "SELECT ma.id, ma.document_id FROM match_alerts ma
             LEFT JOIN documents d ON d.id = ma.document_id
             WHERE ma.alert_status NOT IN ('collected','closed')
               AND (d.id_number = ?
                    OR ma.document_id IN (
                        SELECT national_id FROM national_ids WHERE nin_number=?
                        UNION SELECT driver_id FROM driving_permits WHERE permit_number=?
                        UNION SELECT student_id FROM student_ids WHERE student_number=?
                    ))
             ORDER BY ma.created_at DESC LIMIT 1"
        );
        $as->bind_param('ssss', $id_number, $id_number, $id_number, $id_number);
        $as->execute();
        $ar = $as->get_result()->fetch_assoc();
        $as->close();
        if ($ar) { $alert_id = $ar['id']; $doc_id = $ar['document_id']; }

        // Insert payment record
        $ps = $conn->prepare(
            "INSERT INTO payments
             (match_alert_id, document_id, payer_name, payer_phone, id_number, amount, payment_method, provider, status, initiated_at)
             VALUES (?,?,?,?,?,30000,'mobile_money','MTN','initiated',NOW())"
        );
        $ps->bind_param('iisss', $alert_id, $doc_id, $payer_name, $payer_phone, $id_number);
        if ($ps->execute()) {
            $payment_id = $conn->insert_id;

            // Generate unique verification code (used by station to verify payment)
            $vcode = generateVerificationCode($conn);
            $vc_stmt = $conn->prepare("UPDATE payments SET verification_code=? WHERE id=?");
            $vc_stmt->bind_param('si', $vcode, $payment_id);
            $vc_stmt->execute();
            $vc_stmt->close();

            $status = 'success';

            // Update match alert status if linked
            if ($alert_id) {
                $us = $conn->prepare("UPDATE match_alerts SET alert_status='owner_notified' WHERE id=?");
                $us->bind_param('i', $alert_id);
                $us->execute();
                $us->close();
            }

            // Notify admins
            createNotification($conn, 'payment_confirmed', 'admin', null,
                "Payment INITIATED — $payer_name ($payer_phone) for " . strtoupper(str_replace('_',' ',$doc_type)) . " | ID: $id_number | UGX 30,000",
                $payment_id
            );
            // Also notify the holding station
            if ($station) {
                createNotification($conn, 'payment_confirmed', 'station', $station,
                    "Payment initiated by $payer_name ($payer_phone) for document $id_number. Prepare for collection.",
                    $payment_id
                );
            }

            // Redirect to receipt
            header("Location: receipt.php?pid=$payment_id");
            exit();
        } else {
            $error = 'Could not record your payment. Please try again.';
        }
        $ps->close();
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

        /* Card */
        .pay-card {
            background:#fff; border-radius:1.25rem; padding:2.5rem 2rem;
            max-width:500px; width:100%;
            box-shadow:0 16px 48px rgba(0,0,0,0.3);
            animation:fadeUp .4s ease;
        }
        @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }

        /* Header */
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

        /* Fee box */
        .fee-box {
            background:linear-gradient(135deg,#fff8e1,#fff3cd);
            border:2px solid #ffe082;
            border-radius:1rem; padding:1.25rem 1.5rem;
            display:flex; align-items:center; gap:1rem;
            margin-bottom:1.5rem;
        }
        .fee-num  { font-size:2rem; font-weight:700; color:var(--orange); line-height:1; }
        .fee-note { font-size:.8rem; color:#888; margin-top:.2rem; }

        /* MM steps */
        .mm-steps {
            background:#f8f9fa; border-radius:.75rem;
            padding:1rem 1.25rem; margin-bottom:1.5rem;
        }
        .mm-steps h6 { font-size:.85rem; font-weight:700; color:#333; margin-bottom:.75rem; }
        .mm-step { display:flex; align-items:flex-start; gap:.6rem; font-size:.83rem; color:#555; margin-bottom:.45rem; }
        .mm-step:last-child { margin:0; }
        .ms-n {
            width:20px; height:20px; border-radius:50%;
            background:var(--orange); color:#fff;
            display:flex; align-items:center; justify-content:center;
            font-size:.7rem; font-weight:700; flex-shrink:0;
        }

        /* Form */
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

        /* PIN field styling */
        .pin-wrap { position:relative; }
        .pin-wrap .bi { position:absolute; right:.9rem; top:50%; transform:translateY(-50%); color:#aaa; cursor:pointer; }
        .pin-wrap input { padding-right:2.5rem; letter-spacing:.2rem; font-weight:600; }

        /* Submit */
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

        /* Security note */
        .security-note { text-align:center; font-size:.78rem; color:#aaa; margin-top:.75rem; }

        footer { position:relative; z-index:1; text-align:center; padding:.8rem; color:#ccc; font-size:.82rem; }
        footer a { color:#aaa; text-decoration:none; }
    </style>
</head>
<body>
<div class="wrap">
<div class="pay-card">

    <div class="pay-header">
        <div class="pay-icon"><i class="bi bi-phone"></i></div>
        <h1>Pay to Recover Your Document</h1>
        <p>Complete Mobile Money payment to get your PDF receipt and collect your document.</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger py-2 mb-3" style="font-size:.88rem;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Fee display -->
    <div class="fee-box">
        <div>
            <div class="fee-num">UGX 30,000</div>
            <div class="fee-note">One-time document recovery fee</div>
        </div>
        <div style="margin-left:auto;font-size:.8rem;color:#888;">
            <?= htmlspecialchars(ucwords(str_replace('_',' ',$doc_type))) ?><br>
            <?php if ($owner_name): ?>
                <strong style="color:#333;"><?= htmlspecialchars($owner_name) ?></strong>
            <?php endif; ?>
        </div>
    </div>

    <!-- How MM payment works -->
    <div class="mm-steps">
        <h6><i class="bi bi-info-circle me-1" style="color:var(--orange);"></i>How Mobile Money Payment Works</h6>
        <div class="mm-step"><div class="ms-n">1</div><span>Enter your name and Mobile Money number below.</span></div>
        <div class="mm-step"><div class="ms-n">2</div><span>Enter your 4-digit Mobile Money PIN to authorise.</span></div>
        <div class="mm-step"><div class="ms-n">3</div><span>UGX 30,000 is deducted and your <strong>PDF receipt</strong> is generated instantly.</span></div>
        <div class="mm-step"><div class="ms-n">4</div><span>Take the receipt to <strong><?= htmlspecialchars($station ?: 'the station') ?></strong> to collect your document.</span></div>
    </div>

    <!-- Payment form -->
    <form method="POST" id="payForm">
        <input type="hidden" name="doc_type"    value="<?= htmlspecialchars($doc_type) ?>">
        <input type="hidden" name="id_number"   value="<?= htmlspecialchars($id_number) ?>">
        <input type="hidden" name="station"     value="<?= htmlspecialchars($station) ?>">
        <input type="hidden" name="owner_name"  value="<?= htmlspecialchars($owner_name) ?>">

        <div class="mb-3">
            <label class="form-label">Your Full Name</label>
            <input type="text" name="payer_name" class="form-control"
                   placeholder="Name as on your ID" required
                   value="<?= htmlspecialchars($owner_name) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Mobile Money Phone Number</label>
            <input type="tel" name="payer_phone" class="form-control"
                   placeholder="e.g. 0771234567" required maxlength="13">
            <small class="text-muted">MTN or Airtel number registered to your name</small>
        </div>

        <div class="mb-4">
            <label class="form-label">Mobile Money PIN <span style="color:#999;font-weight:400;">(4 digits)</span></label>
            <div class="pin-wrap">
                <input type="password" name="mm_pin" id="mmPin" class="form-control"
                       placeholder="&#9679;&#9679;&#9679;&#9679;"
                       maxlength="4" pattern="\d{4}" inputmode="numeric" required>
                <i class="bi bi-eye-slash" id="togglePin"></i>
            </div>
            <small class="text-muted">Your PIN is used only to process this payment and is never stored.</small>
        </div>

        <button type="submit" class="btn-pay-now" id="payBtn">
            <i class="bi bi-lock-fill"></i>
            Confirm Payment — UGX 30,000
        </button>
    </form>

    <div class="security-note">
        <i class="bi bi-shield-check me-1"></i>
        Secured by iRecovery &mdash; your payment details are encrypted and never stored.
    </div>

</div>
</div>
<footer>&copy; <?= date('Y') ?> iRecovery &mdash;
    <a href="https://kakebe.tech/" target="_blank" rel="noopener">Kakebe Technologies Limited</a>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle PIN visibility
    const pinInput  = document.getElementById('mmPin');
    const togglePin = document.getElementById('togglePin');
    togglePin.addEventListener('click', () => {
        if (pinInput.type === 'password') {
            pinInput.type = 'text';
            togglePin.className = 'bi bi-eye';
        } else {
            pinInput.type = 'password';
            togglePin.className = 'bi bi-eye-slash';
        }
    });

    // Prevent double-submit
    document.getElementById('payForm').addEventListener('submit', function() {
        const btn = document.getElementById('payBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    });
</script>
</body>
</html>

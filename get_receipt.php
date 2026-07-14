<?php
// ─────────────────────────────────────────────
// Get Receipt — user enters ID number to
// download their approved payment receipt
// ─────────────────────────────────────────────
include_once 'db.php';

$found   = null;
$error   = '';
$id_input = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_input = trim(strtoupper($_POST['id_number'] ?? ''));
    if (empty($id_input)) {
        $error = 'Please enter your ID / NIN number.';
    } else {
        // Find an approved payment for this ID
        $stmt = $conn->prepare(
            "SELECT id, download_allowed, status, payer_name, initiated_at
             FROM payments
             WHERE id_number = ?
             ORDER BY initiated_at DESC
             LIMIT 1"
        );
        $stmt->bind_param('s', $id_input);
        $stmt->execute();
        $found = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$found) {
            $error = 'No payment record found for that ID number. Please check and try again.';
        } elseif ($found['download_allowed'] != 1) {
            $error = 'Your payment is still pending admin approval. You will be contacted once approved. Please check back later.';
            $found = null;
        } elseif ($found['status'] !== 'confirmed') {
            $error = 'Your payment has not been confirmed yet. Please wait for admin confirmation.';
            $found = null;
        } else {
            // Approved — redirect to receipt
            header('Location: receipt.php?pid=' . (int)$found['id']);
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Receipt | iRecovery</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/1570/1570887.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --navy:#0f172a; --blue:#2563eb; --green:#16a34a; }
        * { box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:url('img/bg.jpg') center/cover fixed; min-height:100vh; display:flex; flex-direction:column; margin:0; position:relative; }
        body::before { content:''; position:fixed; inset:0; background:rgba(0,0,0,0.65); z-index:0; }
        .wrap { position:relative; z-index:1; flex:1; display:flex; align-items:center; justify-content:center; padding:2rem 1rem; }
        .card {
            background:#fff; border-radius:1rem; padding:2.5rem 2rem;
            max-width:460px; width:100%;
            box-shadow:0 16px 48px rgba(0,0,0,0.3);
            animation:up .4s ease;
        }
        @keyframes up { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
        .logo { text-align:center; margin-bottom:1.5rem; }
        .logo img { height:50px; }
        h1 { font-size:1.35rem; font-weight:700; color:var(--navy); text-align:center; margin-bottom:.3rem; }
        .sub { text-align:center; color:#64748b; font-size:.88rem; margin-bottom:1.75rem; }
        .form-label { font-weight:600; font-size:.88rem; color:#1e293b; }
        .form-control { border-radius:.5rem; padding:.7rem 1rem; border:1.5px solid #e2e8f0; font-size:.95rem; }
        .form-control:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(37,99,235,0.12); outline:none; }
        .btn-get {
            display:flex; align-items:center; justify-content:center; gap:.5rem;
            width:100%; padding:.8rem; background:var(--blue); color:#fff;
            border:none; border-radius:.5rem; font-size:.95rem; font-weight:700;
            cursor:pointer; transition:background .2s; font-family:'Inter',sans-serif;
        }
        .btn-get:hover { background:#1d4ed8; }
        .alert-danger { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; border-radius:.5rem; padding:.75rem 1rem; font-size:.88rem; margin-bottom:1rem; }
        .steps { background:#f8fafc; border-radius:.75rem; padding:1rem 1.25rem; margin-bottom:1.5rem; }
        .steps h6 { font-size:.82rem; font-weight:700; color:#1e293b; margin-bottom:.6rem; }
        .step { display:flex; align-items:flex-start; gap:.5rem; font-size:.82rem; color:#475569; margin-bottom:.35rem; }
        .step:last-child { margin:0; }
        .sn { width:18px; height:18px; border-radius:50%; background:var(--blue); color:#fff; display:flex; align-items:center; justify-content:center; font-size:.65rem; font-weight:700; flex-shrink:0; margin-top:.1rem; }
        footer { position:relative; z-index:1; text-align:center; padding:.75rem; color:#ccc; font-size:.82rem; }
        footer a { color:#aaa; text-decoration:none; }
    </style>
</head>
<body>
<div class="wrap">
<div class="card">
    <div class="logo"><img src="https://cdn-icons-png.flaticon.com/512/1570/1570887.png" alt="iRecovery"></div>
    <h1>Download Your Receipt</h1>
    <p class="sub">Enter the ID / NIN number you used when paying to access your approved receipt.</p>

    <?php if ($error): ?>
        <div class="alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="steps">
        <h6><i class="bi bi-info-circle me-1"></i>How this works</h6>
        <div class="step"><div class="sn">1</div><span>Pay UGX 30,000 via Mobile Money on the search results page.</span></div>
        <div class="step"><div class="sn">2</div><span>Admin reviews and <strong>approves</strong> your payment.</span></div>
        <div class="step"><div class="sn">3</div><span>Return here, enter your ID number, and download your PDF receipt.</span></div>
        <div class="step"><div class="sn">4</div><span>Take the receipt to the station — they scan your <strong>verification code</strong> and hand over your document.</span></div>
    </div>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Your ID / NIN Number</label>
            <input type="text" name="id_number" class="form-control"
                   placeholder="e.g. CM90103100DLAH"
                   value="<?= htmlspecialchars($id_input) ?>"
                   autocomplete="off" required>
        </div>
        <button type="submit" class="btn-get">
            <i class="bi bi-file-earmark-pdf"></i> Get My Receipt
        </button>
    </form>
</div>
</div>
<footer>&copy; <?= date('Y') ?> iRecovery &mdash; <a href="https://kakebe.tech/" target="_blank" rel="noopener">Kakebe Technologies Limited</a></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

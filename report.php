<?php
// ─────────────────────────────────────────────
// Report a Lost Document
// Inserts into lost_reports, runs auto-match
// ─────────────────────────────────────────────
session_start();
include_once 'db.php';
include_once 'includes/match_engine.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit(); }

function genRand(int $len = 6): string {
    $c = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $s = ''; for ($i=0;$i<$len;$i++) $s.=$c[random_int(0,strlen($c)-1)]; return $s;
}
function saveFile(array $file, string $prefix): ?string {
    if (empty($file['tmp_name'])) return null;
    $name = $prefix . genRand() . '_' . time() . '.png';
    move_uploaded_file($file['tmp_name'], __DIR__ . '/uploads/' . $name);
    return $name;
}

$doc_type      = $_POST['doc_type']       ?? '';
$sur_name      = trim(strtoupper($_POST['surName']   ?? ''));
$given_name    = trim(strtoupper($_POST['givenName'] ?? ''));
$dob           = $_POST['dob']            ?? null;
$gender        = $_POST['gender']         ?? null;
$id_number     = trim(strtoupper($_POST['id_number'] ?? ''));
$extra1        = trim($_POST['extra1']    ?? '');
$extra2        = trim($_POST['extra2']    ?? '');
$reporter_name = trim($_POST['reporter_name']  ?? '');
$reporter_phone= trim($_POST['reporter_phone'] ?? '');
$reporter_email= trim($_POST['reporter_email'] ?? '');
$police_letter = saveFile($_FILES['police_letter'] ?? [], 'POLICE_LTR_');

$status  = null;
$message = '';
$matchAlert = false;

$stmt = $conn->prepare(
    "INSERT INTO lost_reports
     (doc_type, sur_name, given_name, dob, gender, id_number, extra_field1, extra_field2,
      reporter_name, reporter_phone, reporter_email, police_letter, match_status)
     VALUES (?,?,?,?,?,?,?,?,?,?,?,?,'unmatched')"
);
if (!$stmt) {
    $status  = 'error';
    $message = 'Database error: ' . $conn->error;
} else {
    $stmt->bind_param('ssssssssssss',
        $doc_type, $sur_name, $given_name, $dob, $gender, $id_number,
        $extra1, $extra2,
        $reporter_name, $reporter_phone, $reporter_email, $police_letter
    );
    if ($stmt->execute()) {
        $lost_id = $conn->insert_id;
        $status  = 'success';
        $message = 'Your lost document report has been submitted. We will notify you if a match is found.';

        // Run auto-match
        checkMatchOnReport($conn, $doc_type, $id_number, $sur_name, $given_name, $dob ?? '', $lost_id, $reporter_name, $reporter_phone);

        // Check if it got matched
        $chk = $conn->prepare("SELECT match_status FROM lost_reports WHERE id=?");
        $chk->bind_param('i', $lost_id); $chk->execute();
        $chkRow = $chk->get_result()->fetch_assoc(); $chk->close();
        $matchAlert = ($chkRow['match_status'] === 'matched');

        // Notify admins of new report
        createNotification($conn, 'new_report', 'admin', null,
            "New lost $doc_type report by $reporter_name ($reporter_phone).", $lost_id);
    } else {
        $status  = 'error';
        $message = 'Submission failed. Please try again.';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Result | iRecovery</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/1570/1570887.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --red:#CC0000; --red-dark:#990000; }
        body { font-family:'Inter',sans-serif; background:url('img/bg.jpg') center/cover fixed; min-height:100vh; display:flex; flex-direction:column; position:relative; }
        body::before { content:''; position:absolute; inset:0; background:rgba(0,0,0,0.65); z-index:0; }
        .wrap { position:relative; z-index:1; flex:1; display:flex; align-items:center; justify-content:center; padding:2rem 1rem; }
        .card-result { background:#fff; border-radius:1.2rem; padding:2.5rem 2rem; max-width:520px; width:100%; text-align:center; box-shadow:0 12px 40px rgba(0,0,0,0.25); }
        .icon-circle { width:72px; height:72px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem; font-size:2rem; }
        .icon-success { background:#e8f5e9; color:#2e7d32; }
        .icon-error   { background:#ffebee; color:#c62828; }
        .icon-match   { background:#fff3e0; color:#e65100; }
        h2 { font-size:1.5rem; font-weight:700; margin-bottom:.5rem; }
        p  { color:#555; }
        .alert-match { background:#fff8e1; border:1px solid #ffe082; border-radius:.75rem; padding:1rem; margin:1rem 0; text-align:left; }
        .alert-match strong { color:#e65100; }
        .chip { display:inline-flex; align-items:center; gap:.4rem; background:#f5f5f5; border-radius:50px; padding:.4rem 1rem; font-size:.85rem; color:#555; margin-top:1rem; }
        .chip .num { font-weight:700; color:var(--red); }
        footer { position:relative; z-index:1; text-align:center; padding:.75rem; color:#ccc; font-size:.82rem; }
        footer a { color:#aaa; text-decoration:none; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card-result">
        <?php if ($status === 'success'): ?>
            <?php if ($matchAlert): ?>
                <div class="icon-circle icon-match"><i class="bi bi-lightning-fill"></i></div>
                <h2>Match Found!</h2>
                <div class="alert-match">
                    <strong><i class="bi bi-check-circle-fill me-1"></i>Great news!</strong>
                    <p class="mb-0 mt-1">A document matching your details was already in our system. Our admin team has been notified and will contact you shortly at <strong><?= htmlspecialchars($reporter_phone) ?></strong>.</p>
                </div>
            <?php else: ?>
                <div class="icon-circle icon-success"><i class="bi bi-check-lg"></i></div>
                <h2>Report Submitted!</h2>
                <p><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>
            <p class="text-muted" style="font-size:.85rem;">Keep your phone <strong><?= htmlspecialchars($reporter_phone) ?></strong> reachable — we'll call when your document is found.</p>
            <div class="chip"><i class="bi bi-arrow-repeat"></i> Redirecting in <span class="num" id="countdown">8</span>s</div>
        <?php elseif ($status === 'error'): ?>
            <div class="icon-circle icon-error"><i class="bi bi-x-lg"></i></div>
            <h2>Submission Failed</h2>
            <p><?= htmlspecialchars($message) ?></p>
            <div class="chip"><i class="bi bi-arrow-repeat"></i> Redirecting in <span class="num" id="countdown">5</span>s</div>
        <?php else: ?>
            <div class="icon-circle icon-error"><i class="bi bi-slash-circle"></i></div>
            <h2>No Data</h2>
            <p>Please go back and complete the form.</p>
            <a href="index.php" class="btn btn-danger mt-2">Go Back</a>
        <?php endif; ?>
    </div>
</div>
<footer>&copy; <?= date('Y') ?> iRecovery — <a href="https://kakebe.tech/" target="_blank" rel="noopener">Kakebe Technologies Limited</a></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const el = document.getElementById('countdown');
    if (el) { let n=parseInt(el.textContent); const t=setInterval(()=>{ el.textContent=--n; if(n<=0){clearInterval(t);window.location.href='index.php';} },1000); }
</script>
</body>
</html>

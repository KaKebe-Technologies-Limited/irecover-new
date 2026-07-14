<?php
// ─────────────────────────────────────────────
// Upload Found Document (Station / Public)
// Inserts into unified `documents` table + legacy tables
// Runs auto-match engine after insert
// ─────────────────────────────────────────────
session_start();
include_once 'db.php';
include_once 'includes/match_engine.php';

// Determine reporter identity
$reporter = 'Public';
if (isset($_SESSION['station_user'])) $reporter = $_SESSION['station_user'];
if (isset($_SESSION['admin_user']))   $reporter = $_SESSION['admin_user'];

// ── Helpers ───────────────────────────────────
function genRand(int $len = 6): string {
    $c = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $s = '';
    for ($i = 0; $i < $len; $i++) $s .= $c[random_int(0, strlen($c) - 1)];
    return $s;
}

function saveFile(array $file, string $prefix): ?string {
    if (empty($file['tmp_name'])) return null;
    $rand = genRand() . '_' . time();
    $name = $prefix . $rand . '.png';
    move_uploaded_file($file['tmp_name'], __DIR__ . '/uploads/' . $name);
    return $name;
}

$status  = null;
$message = '';
$matchMsg = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$doc_type   = $_POST['doc_type']   ?? '';
$sur_name   = trim(strtoupper($_POST['surName']   ?? ''));
$given_name = trim(strtoupper($_POST['givenName'] ?? ''));
$dob        = $_POST['dob']        ?? null;
$gender     = $_POST['gender']     ?? null;
$id_number  = trim(strtoupper($_POST['id_number'] ?? ''));
$extra1     = trim($_POST['extra1'] ?? '');
$extra2     = trim($_POST['extra2'] ?? '');
$extra3     = trim($_POST['extra3'] ?? '');
$rep_phone  = trim($_POST['reporter_phone'] ?? '');

$front_img = saveFile($_FILES['front_img'] ?? [], 'DOC_FRONT_');
$back_img  = saveFile($_FILES['back_img']  ?? [], 'DOC_BACK_');
$date_now  = date('Y-m-d H:i:s');

// ── Insert into unified documents table ──────
$stmt = $conn->prepare(
    "INSERT INTO documents
     (doc_type, sur_name, given_name, dob, gender, id_number, extra_field1, extra_field2, extra_field3,
      front_img, back_img, action, reporter, reporter_phone, station_holding, submitted_at)
     VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
);
if (!$stmt) {
    $status  = 'error';
    $message = 'Database error: ' . $conn->error;
} else {
    $action = 'found';
    $stmt->bind_param(
        'ssssssssssssssss',
        $doc_type, $sur_name, $given_name, $dob, $gender, $id_number,
        $extra1, $extra2, $extra3,
        $front_img, $back_img,
        $action, $reporter, $rep_phone, $reporter, $date_now
    );

    if ($stmt->execute()) {
        $new_doc_id = $conn->insert_id;
        $status     = 'success';
        $message    = ucwords(str_replace('_', ' ', $doc_type)) . ' uploaded successfully. Thank you!';

        // ── Also write to legacy table for backward compat ──
        $legDate = date('Y-m-d / h:i:s A');
        $fr = $front_img ?? '';
        $bk = $back_img  ?? '';
        if ($doc_type === 'national_id') {
            $ls = $conn->prepare("INSERT INTO national_ids (sur_name,given_name,dob,nin_number,gender,front,back,user_action,reporter,date_found) VALUES(?,?,?,?,?,?,?,'Found',?,?)");
            $ls->bind_param('sssssssss', $sur_name,$given_name,$dob,$id_number,$gender,$fr,$bk,$reporter,$legDate);
            $ls->execute(); $ls->close();
        } elseif ($doc_type === 'driving_permit') {
            $ls = $conn->prepare("INSERT INTO driving_permits (sur_name,given_name,dob,permit_number,nin_number,front,back,user_action,reporter,date_found) VALUES(?,?,?,?,?,?,?,'Found',?,?)");
            $ls->bind_param('sssssssss', $sur_name,$given_name,$dob,$id_number,$extra1,$fr,$bk,$reporter,$legDate);
            $ls->execute(); $ls->close();
        } elseif ($doc_type === 'student_id') {
            $ls = $conn->prepare("INSERT INTO student_ids (sur_name,given_name,student_number,course,date_issued,school,front,back,user_action,reporter,date_found) VALUES(?,?,?,?,?,?,?,'Found',?,?)");
            $dob2 = $dob ?? '0000-00-00';
            $ls->bind_param('sssssssss', $sur_name,$given_name,$id_number,$extra1,$dob2,$extra2,$fr,$bk,$reporter,$legDate);
            $ls->execute(); $ls->close();
        }

        // ── Run auto-match ──
        checkMatchOnUpload($conn, $doc_type, $id_number, $sur_name, $given_name, $dob ?? '', $new_doc_id, $reporter);

        // Log notification for station
        createNotification($conn, 'new_upload', 'station', $reporter, "New $doc_type uploaded by $reporter.", $new_doc_id);

    } else {
        $status  = 'error';
        $message = 'Upload failed. Please try again.';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Result | iRecovery</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/1570/1570887.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --red: #CC0000; --red-dark: #990000; }
        body { font-family:'Inter',sans-serif; background:url('img/bg.jpg') center/cover fixed; min-height:100vh; display:flex; flex-direction:column; position:relative; }
        body::before { content:''; position:absolute; inset:0; background:rgba(0,0,0,0.65); z-index:0; }
        .wrap { position:relative; z-index:1; flex:1; display:flex; align-items:center; justify-content:center; padding:2rem 1rem; }
        .card-result { background:#fff; border-radius:1.2rem; padding:2.5rem 2rem; max-width:480px; width:100%; text-align:center; box-shadow:0 12px 40px rgba(0,0,0,0.25); }
        .icon-circle { width:72px; height:72px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem; font-size:2rem; }
        .icon-success { background:#e8f5e9; color:#2e7d32; }
        .icon-error   { background:#ffebee; color:#c62828; }
        h2 { font-size:1.5rem; font-weight:700; margin-bottom:.5rem; }
        p  { color:#555; margin-bottom:1.5rem; }
        .badge-match { background:#fff3e0; color:#e65100; border:1px solid #ffcc80; border-radius:50px; padding:.4rem 1rem; font-size:.85rem; font-weight:600; display:inline-block; margin-bottom:1rem; }
        .chip { display:inline-flex; align-items:center; gap:.4rem; background:#f5f5f5; border-radius:50px; padding:.4rem 1rem; font-size:.85rem; color:#555; }
        .chip .num { font-weight:700; color:var(--red); }
        footer { position:relative; z-index:1; text-align:center; padding:.75rem; color:#ccc; font-size:.82rem; }
        footer a { color:#aaa; text-decoration:none; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card-result">
        <?php if ($status === 'success'): ?>
            <div class="icon-circle icon-success"><i class="bi bi-check-lg"></i></div>
            <h2>Thank You!</h2>
            <p><?= htmlspecialchars($message) ?></p>
            <?php if ($matchMsg): ?>
                <div class="badge-match"><i class="bi bi-lightning-fill me-1"></i><?= htmlspecialchars($matchMsg) ?></div>
            <?php endif; ?>
            <div class="chip"><i class="bi bi-arrow-repeat"></i> Redirecting in <span class="num" id="countdown">5</span>s</div>
        <?php elseif ($status === 'error'): ?>
            <div class="icon-circle icon-error"><i class="bi bi-x-lg"></i></div>
            <h2>Upload Failed</h2>
            <p><?= htmlspecialchars($message) ?></p>
            <div class="chip"><i class="bi bi-arrow-repeat"></i> Redirecting in <span class="num" id="countdown">5</span>s</div>
        <?php else: ?>
            <div class="icon-circle icon-error"><i class="bi bi-slash-circle"></i></div>
            <h2>No Data Submitted</h2>
            <p>Please go back and fill in the form.</p>
            <a href="index.php" class="btn btn-danger">Go Back</a>
        <?php endif; ?>
    </div>
</div>
<footer>&copy; <?= date('Y') ?> iRecovery — <a href="https://kakebe.tech/" target="_blank" rel="noopener">Kakebe Technologies Limited</a></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const el = document.getElementById('countdown');
    if (el) { let n=5; const t=setInterval(()=>{ el.textContent=--n; if(n<=0){clearInterval(t);window.location.href='index.php';} },1000); }
</script>
</body>
</html>

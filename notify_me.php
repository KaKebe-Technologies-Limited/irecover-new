<?php
// ─────────────────────────────────────────────
// "Notify me when found" — a lighter-weight opt-in offered right on the
// search results page when nothing matched yet. Registers a standing
// lookout (a lost_reports row, same as the full Report Lost Document
// flow) so the searcher gets emailed/texted automatically the moment a
// station uploads a matching document — without making them fill out the
// full report form or attach a police letter.
// ─────────────────────────────────────────────
session_start();
include_once 'db.php';
include_once 'includes/match_engine.php';
include_once 'includes/email_notify.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit(); }

$doc_type      = $_POST['doc_type']   ?? '';
$sur_name      = trim(strtoupper($_POST['sur_name']   ?? ''));
$given_name    = trim(strtoupper($_POST['given_name'] ?? ''));
$dob           = $_POST['dob'] ?: null;
$id_number     = trim(strtoupper($_POST['id_number'] ?? ''));
$reporter_name = trim($_POST['notify_name']  ?? '');
$reporter_phone= trim($_POST['notify_phone'] ?? '');
$reporter_email= trim($_POST['notify_email'] ?? '');

$status  = null;
$message = '';

if ($doc_type === '' || $reporter_phone === '') {
    $status  = 'error';
    $message = 'Please provide at least your phone number so we can reach you.';
} else {
    $stmt = $conn->prepare(
        "INSERT INTO lost_reports
         (doc_type, sur_name, given_name, dob, id_number, reporter_name, reporter_phone, reporter_email, match_status)
         VALUES (?,?,?,?,?,?,?,?,'unmatched')"
    );
    if (!$stmt) {
        $status  = 'error';
        $message = 'Something went wrong. Please try again.';
    } else {
        $stmt->bind_param('ssssssss', $doc_type, $sur_name, $given_name, $dob, $id_number, $reporter_name, $reporter_phone, $reporter_email);
        if ($stmt->execute()) {
            $lost_id = $conn->insert_id;
            $status  = 'success';
            $message = "We'll notify you the moment your document is found.";

            checkMatchOnReport($conn, $doc_type, $id_number, $sur_name, $given_name, $dob ?? '', $lost_id, $reporter_name, $reporter_phone);

            createNotification($conn, 'new_report', 'admin', null,
                "New \"notify me\" lookout registered for $doc_type by $reporter_name ($reporter_phone).", $lost_id);

            notifyTeamOfLostReport([
                'doc_type'       => $doc_type,
                'sur_name'       => $sur_name,
                'given_name'     => $given_name,
                'dob'            => $dob,
                'id_number'      => $id_number,
                'reporter_name'  => $reporter_name,
                'reporter_phone' => $reporter_phone,
                'reporter_email' => $reporter_email,
                'lost_id'        => $lost_id,
            ]);
        } else {
            $status  = 'error';
            $message = 'Submission failed. Please try again.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notify Me | iRecovery</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/1570/1570887.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --red:#CC0000; --red-dark:#990000; }
        body { font-family:'Inter',sans-serif; background:url('img/bg.jpg') center/cover fixed; min-height:100vh; display:flex; flex-direction:column; position:relative; }
        body::before { content:''; position:absolute; inset:0; background:rgba(0,0,0,0.65); z-index:0; }
        .wrap { position:relative; z-index:1; flex:1; display:flex; align-items:center; justify-content:center; padding:2rem 1rem; }
        .card-result { background:#fff; border-radius:1.2rem; padding:2.5rem 2rem; max-width:480px; width:100%; text-align:center; box-shadow:0 12px 40px rgba(0,0,0,0.25); }
        .icon-circle { width:72px; height:72px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem; font-size:2rem; }
        .icon-success { background:#e8f5e9; color:#2e7d32; }
        .icon-error   { background:#ffebee; color:#c62828; }
        h2 { font-size:1.4rem; font-weight:700; margin-bottom:.5rem; }
        p  { color:#555; }
        footer { position:relative; z-index:1; text-align:center; padding:.75rem; color:#ccc; font-size:.82rem; }
        footer a { color:#aaa; text-decoration:none; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card-result">
        <?php if ($status === 'success'): ?>
            <div class="icon-circle icon-success"><i class="bi bi-bell-fill"></i></div>
            <h2>You're All Set!</h2>
            <p><?= htmlspecialchars($message) ?></p>
            <p class="text-muted" style="font-size:.85rem;">We'll reach you at <strong><?= htmlspecialchars($reporter_phone) ?></strong><?= $reporter_email ? ' and <strong>' . htmlspecialchars($reporter_email) . '</strong>' : '' ?>.</p>
            <a href="index.php" class="btn btn-danger mt-2">Back to Home</a>
        <?php else: ?>
            <div class="icon-circle icon-error"><i class="bi bi-x-lg"></i></div>
            <h2>Couldn't Set That Up</h2>
            <p><?= htmlspecialchars($message) ?></p>
            <a href="index.php" class="btn btn-outline-secondary mt-2">Go Back</a>
        <?php endif; ?>
    </div>
</div>
<footer>&copy; <?= date('Y') ?> iRecovery &mdash; <a href="https://kakebe.tech/" target="_blank" rel="noopener">Kakebe Technologies Limited</a></footer>
</body>
</html>

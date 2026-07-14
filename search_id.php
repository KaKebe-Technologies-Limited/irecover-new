<?php
// ─────────────────────────────────────────────
// Search for a Found Document
// Searches by ID number (primary) OR name+DOB
// Logs every search. Shows Pay button on match.
// ─────────────────────────────────────────────
include_once 'db.php';
include_once 'includes/match_engine.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit(); }

$doc_type      = $_POST['doc_type']       ?? '';
$sur_name      = trim(strtoupper($_POST['surName']   ?? ''));
$given_name    = trim(strtoupper($_POST['givenName'] ?? ''));
$dob           = $_POST['dob']            ?? '';
$id_number     = trim(strtoupper($_POST['id_number'] ?? ''));
$searcher_phone= trim($_POST['searcher_phone'] ?? '');

// Map legacy form field names
if (empty($id_number)) {
    $id_number = trim(strtoupper(
        $_POST['nationality'] ?? $_POST['permitNumber'] ?? $_POST['studentNumber'] ?? ''
    ));
}
if (empty($doc_type)) {
    if (isset($_POST['nationality']))   $doc_type = 'national_id';
    elseif (isset($_POST['permitNumber'])) $doc_type = 'driving_permit';
    elseif (isset($_POST['studentNumber'])) $doc_type = 'student_id';
}

$found      = null;  // result row
$result_src = '';    // 'new' or 'legacy'
$station_phone = '0777512529'; // default contact

// ── 1. Search new documents table ─────────────
if ($doc_type && ($id_number || ($sur_name && $dob))) {
    $stmt = $conn->prepare(
        "SELECT d.id, d.doc_type, d.sur_name, d.given_name, d.dob,
                d.id_number, d.front_img, d.back_img, d.submitted_at,
                d.station_holding, d.action, d.payment_status,
                a.number as station_phone
         FROM documents d
         LEFT JOIN admins a ON a.user_name = d.station_holding
         WHERE d.action IN ('found','matched')
           AND d.doc_type = ?
           AND (d.id_number = ? OR (d.sur_name = ? AND d.given_name = ? AND d.dob = ?))
         LIMIT 1"
    );
    $stmt->bind_param('sssss', $doc_type, $id_number, $sur_name, $given_name, $dob);
    $stmt->execute();
    $found = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($found) {
        $result_src = 'new';
        if (!empty($found['station_phone'])) $station_phone = $found['station_phone'];
    }
}

// ── 2. Fall back to legacy tables ─────────────
if (!$found) {
    if ($doc_type === 'national_id' || isset($_POST['nationality'])) {
        $nin = $id_number ?: ($_POST['nationality'] ?? '');
        $g   = $_POST['gender'] ?? '';
        $stmt = $conn->prepare(
            "SELECT national_id as id, 'national_id' as doc_type, sur_name, given_name, dob,
                    nin_number as id_number, front as front_img, back as back_img,
                    date_found as submitted_at, reporter as station_holding, user_action as action
             FROM national_ids
             WHERE user_action='Found'
               AND (nin_number=? OR (sur_name=? AND given_name=? AND dob=?))
             LIMIT 1"
        );
        $stmt->bind_param('ssss', $nin, $sur_name, $given_name, $dob);
        $stmt->execute();
        $found = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($found) { $result_src='legacy'; $found['front_img']='uploads/'.$found['front_img']; $found['back_img']='uploads/'.$found['back_img']; }
    } elseif ($doc_type === 'driving_permit' || isset($_POST['permitNumber'])) {
        $pn = $id_number ?: ($_POST['permitNumber'] ?? '');
        $stmt = $conn->prepare(
            "SELECT driver_id as id, 'driving_permit' as doc_type, sur_name, given_name, dob,
                    permit_number as id_number, front as front_img, back as back_img,
                    date_found as submitted_at, reporter as station_holding, user_action as action
             FROM driving_permits
             WHERE user_action='Found'
               AND (permit_number=? OR (sur_name=? AND given_name=? AND dob=?))
             LIMIT 1"
        );
        $stmt->bind_param('ssss', $pn, $sur_name, $given_name, $dob);
        $stmt->execute();
        $found = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($found) { $result_src='legacy'; $found['front_img']='uploads/'.$found['front_img']; $found['back_img']='uploads/'.$found['back_img']; $station_phone='0393249845'; }
    } elseif ($doc_type === 'student_id' || isset($_POST['studentNumber'])) {
        $sn  = $id_number ?: ($_POST['studentNumber'] ?? '');
        $sch = $_POST['school'] ?? '';
        $stmt = $conn->prepare(
            "SELECT student_id as id, 'student_id' as doc_type, sur_name, given_name, dob,
                    student_number as id_number, front as front_img, back as back_img,
                    date_found as submitted_at, reporter as station_holding, user_action as action
             FROM student_ids
             WHERE user_action='Found'
               AND (student_number=? OR (sur_name=? AND given_name=? AND school=?))
             LIMIT 1"
        );
        $stmt->bind_param('ssss', $sn, $sur_name, $given_name, $sch);
        $stmt->execute();
        $found = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($found) { $result_src='legacy'; $found['front_img']='uploads/'.$found['front_img']; $found['back_img']='uploads/'.$found['back_img']; }
    }
}

$result_status = $found ? 'matched' : 'not_found';

// ── Log every search ──────────────────────────
$matched_id = $found ? ($found['id'] ?? null) : null;
$log = $conn->prepare(
    "INSERT INTO search_log (doc_type, search_name, search_id_num, searcher_phone, result, matched_doc_id)
     VALUES (?,?,?,?,?,?)"
);
$search_name = trim("$sur_name $given_name");
$log->bind_param('sssssi', $doc_type, $search_name, $id_number, $searcher_phone, $result_status, $matched_id);
$log->execute();
$log_id = $conn->insert_id;
$log->close();

// ── Notify admins of match with searcher contact
if ($found && $searcher_phone) {
    $nm = $found['sur_name'] . ' ' . $found['given_name'];
    $msg = "Public search MATCHED: $nm ($doc_type). Searcher phone: $searcher_phone. Held at: " . ($found['station_holding'] ?? 'Unknown');
    createNotification($conn, 'match_found', 'admin', null, $msg, $matched_id);
}

// ── Helper: mask ID number — first 4 + last 2 only ────
function maskId(string $id): string {
    $len = strlen($id);
    if ($len <= 6) return str_repeat('*', $len); // too short to reveal safely
    return substr($id, 0, 4) . str_repeat('*', $len - 6) . substr($id, -2);
}

// ── Helper: blur the owner name — reveal first 2 letters only ────
function maskName(string $name): string {
    $name = trim($name);
    if ($name === '') return '****';
    $len  = mb_strlen($name);
    $keep = min(2, $len);
    $first = mb_strtoupper(mb_substr($name, 0, $keep));
    return $first . str_repeat('*', max(3, $len - $keep));
}

// ── Get recovery fee ──────────────────────────
$fee = 30000; // Fixed fee UGX 30,000
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results | iRecovery</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/1570/1570887.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --red:#CC0000; --red-dark:#990000; --orange:#ff6f00; }
        * { box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:url('img/bg.jpg') center/cover fixed; min-height:100vh; display:flex; flex-direction:column; position:relative; margin:0; }
        body::before { content:''; position:fixed; inset:0; background:rgba(0,0,0,0.62); z-index:-1; }
        .wrap { flex:1; display:flex; justify-content:center; align-items:flex-start; padding:3rem 1rem; animation:fadeUp .5s ease; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        .result-card { background:#fff; border-radius:1.25rem; padding:2rem; max-width:660px; width:100%; box-shadow:0 16px 48px rgba(0,0,0,0.28); }

        /* ── Found banner ── */
        .found-banner { background:linear-gradient(135deg,#e8f5e9,#f1f8e9); border:1px solid #a5d6a7; border-radius:.75rem; padding:1rem 1.25rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:.75rem; }
        .found-banner .bi { font-size:1.6rem; color:#2e7d32; }
        .found-banner strong { font-size:1.05rem; }

        /* ── Blurred document preview ── */
        .doc-preview-wrap {
            position: relative;
            border-radius: .75rem;
            overflow: hidden;
            margin-bottom: 1.25rem;
            border: 2px solid #e0e0e0;
            background: #f5f5f5;
        }
        .doc-preview-img {
            width: 100%;
            max-height: 260px;
            object-fit: cover;
            display: block;
            /* Heavy blur — details unreadable */
            filter: blur(12px) brightness(0.85);
            transform: scale(1.05); /* hide blur edges */
        }
        .doc-preview-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0.35);
            gap: .5rem;
        }
        .doc-preview-overlay .lock-icon {
            font-size: 2.5rem;
            color: #fff;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));
        }
        .doc-preview-overlay p {
            color: #fff;
            font-size: .85rem;
            font-weight: 600;
            margin: 0;
            text-shadow: 0 1px 3px rgba(0,0,0,0.6);
            text-align: center;
            padding: 0 1rem;
        }
        .doc-preview-overlay .blur-badge {
            background: rgba(255,111,0,0.9);
            color: #fff;
            border-radius: 50px;
            padding: .3rem .9rem;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .3px;
        }

        /* ── Info rows ── */
        .info-row { display:flex; justify-content:space-between; align-items:center; padding:.6rem 0; border-bottom:1px solid #f0f0f0; font-size:.9rem; }
        .info-row:last-child { border:none; }
        .info-label { color:#888; font-weight:500; display:flex; align-items:center; gap:.35rem; }
        .info-value { font-weight:600; color:#1a1a1a; font-family:monospace; letter-spacing:.5px; }
        .info-value.masked { color:#555; letter-spacing:1px; }

        /* ── Action buttons ── */
        .action-buttons { display:flex; flex-wrap:wrap; gap:.75rem; justify-content:center; margin:1.5rem 0 1rem; }
        .btn-call {
            background: var(--red); color:#fff; border:none; border-radius:50px;
            padding:.65rem 1.5rem; font-weight:600; font-size:.9rem;
            display:inline-flex; align-items:center; gap:.45rem;
            text-decoration:none; transition:all .2s;
        }
        .btn-call:hover { background:var(--red-dark); color:#fff; transform:translateY(-2px); }
        .btn-pay {
            background:var(--orange); color:#fff; border:none; border-radius:50px;
            padding:.65rem 1.5rem; font-weight:700; font-size:.9rem;
            display:inline-flex; align-items:center; gap:.45rem;
            text-decoration:none; transition:all .2s;
            box-shadow: 0 4px 16px rgba(255,111,0,0.4);
        }
        .btn-pay:hover { background:#e65100; color:#fff; transform:translateY(-2px); box-shadow:0 6px 20px rgba(255,111,0,0.5); }

        /* ── Fee callout ── */
        .fee-callout {
            background: linear-gradient(135deg,#fff8e1,#fff3cd);
            border: 1px solid #ffe082;
            border-radius: .75rem;
            padding: 1rem 1.25rem;
            display: flex; align-items: center; gap: 1rem;
            margin-bottom: 1rem;
        }
        .fee-amount { font-size: 1.6rem; font-weight: 700; color: var(--orange); line-height: 1; }
        .fee-label  { font-size: .82rem; color: #888; margin-top: .15rem; }

        /* ── Steps ── */
        .steps-mini { list-style:none; padding:0; margin:.75rem 0 0; }
        .steps-mini li { display:flex; align-items:flex-start; gap:.6rem; font-size:.84rem; color:#555; padding:.35rem 0; border-bottom:1px solid #f5f5f5; }
        .steps-mini li:last-child { border:none; }
        .sn { width:20px; height:20px; border-radius:50%; background:var(--orange); color:#fff; display:flex; align-items:center; justify-content:center; font-size:.7rem; font-weight:700; flex-shrink:0; margin-top:.1rem; }

        /* ── Not found ── */
        .not-found { text-align:center; padding:2rem 0; }
        .nf-icon { font-size:3.5rem; color:#ddd; display:block; margin-bottom:1rem; }

        /* ── Timer ── */
        .timer-chip { display:inline-flex; align-items:center; gap:.4rem; background:#f5f5f5; border-radius:50px; padding:.4rem 1rem; font-size:.82rem; color:#666; }
        .timer-chip .num { font-weight:700; color:var(--red); }

        footer { text-align:center; padding:.8rem 1rem; color:#ccc; font-size:.82rem; }
        footer a { color:#aaa; text-decoration:none; }
        footer a:hover { color:#eee; }
    </style>
</head>
<body>
<div class="wrap">
<div class="result-card">

<?php if ($found):
    $raw_id    = $found['id_number'] ?? $id_number;
    $masked    = maskId($raw_id);
    $doc_label = ucwords(str_replace('_', ' ', $found['doc_type'] ?? $doc_type));
    $station   = htmlspecialchars($found['station_holding'] ?? 'Contact Admin');
    $date_f    = htmlspecialchars($found['submitted_at'] ?? '—');
    $img_src   = '';
    if (!empty($found['front_img'])) {
        $img_src = $result_src === 'legacy'
            ? htmlspecialchars($found['front_img'])
            : 'uploads/' . htmlspecialchars($found['front_img']);
    }
?>

    <!-- Found banner -->
    <div class="found-banner">
        <i class="bi bi-check-circle-fill"></i>
        <div>
            <strong>Your Document Has Been Found!</strong>
            <div style="font-size:.83rem;color:#555;margin-top:.15rem;">
                A match exists in our database. Pay UGX 30,000 to retrieve it.
            </div>
        </div>
    </div>

    <!-- Blurred document preview -->
    <?php if ($img_src): ?>
    <div class="doc-preview-wrap">
        <img src="<?= $img_src ?>" alt="Document preview" class="doc-preview-img">
        <div class="doc-preview-overlay">
            <i class="bi bi-lock-fill lock-icon"></i>
            <p>Document image is hidden for privacy</p>
            <div class="blur-badge"><i class="bi bi-eye-slash me-1"></i>Pay to Unlock &amp; Collect</div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Document details with masked ID -->
    <div class="mb-3">
        <div class="info-row">
            <span class="info-label"><i class="bi bi-person"></i> Name on Document</span>
            <span class="info-value"><?= htmlspecialchars(maskName($found['sur_name']) . ' ' . maskName($found['given_name'])) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><i class="bi bi-file-earmark-text"></i> Document Type</span>
            <span class="info-value"><?= htmlspecialchars($doc_label) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><i class="bi bi-credit-card"></i> ID / Reference Number</span>
            <span class="info-value masked"><?= htmlspecialchars($masked) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><i class="bi bi-calendar3"></i> Date Found</span>
            <span class="info-value"><?= $date_f ?></span>
        </div>
    </div>

    <!-- Fee callout -->
    <div class="fee-callout">
        <div>
            <div class="fee-amount">UGX 30,000</div>
            <div class="fee-label">One-time document recovery fee</div>
        </div>
        <div style="margin-left:auto;font-size:.82rem;color:#666;max-width:220px;">
            Pay via Mobile Money, get an instant PDF receipt, then collect your document from the station.
        </div>
    </div>

    <!-- Action buttons -->
    <div class="action-buttons">
        <a href="pay.php?id_number=<?= urlencode($raw_id) ?>&doc_type=<?= urlencode($found['doc_type'] ?? $doc_type) ?>&station=<?= urlencode($found['station_holding'] ?? '') ?>&name=<?= urlencode(trim($found['sur_name'].' '.$found['given_name'])) ?>" class="btn-pay">
            <i class="bi bi-phone"></i> Pay UGX 30,000 &amp; Get Receipt
        </a>
    </div>

    <!-- Mini steps -->
    <ul class="steps-mini">
        <li><div class="sn">1</div><span>Click <strong>Pay UGX 30,000</strong> and enter your Mobile Money number &amp; PIN.</span></li>
        <li><div class="sn">2</div><span>Your payment is confirmed instantly and a <strong>PDF receipt</strong> is generated.</span></li>
        <li><div class="sn">3</div><span>Visit the <strong>holding station</strong> with your receipt and a valid ID to collect your document.</span></li>
    </ul>

    <div class="text-center mt-3">
        <a href="index.php" class="btn btn-outline-secondary btn-sm me-2">
            <i class="bi bi-arrow-left me-1"></i> Search Another
        </a>
        <a href="index.php#services" class="btn btn-danger btn-sm">
            <i class="bi bi-flag me-1"></i> Report It Lost
        </a>
    </div>

<?php else: ?>

    <div class="not-found">
        <i class="bi bi-search nf-icon"></i>
        <h2 style="font-weight:700;font-size:1.4rem;">No Match Found</h2>
        <p class="text-muted">We could not find a document matching your details right now.</p>
        <p class="text-muted" style="font-size:.85rem;">Documents are added daily by our partner stations — check back soon.</p>
        <div class="d-flex flex-wrap gap-2 justify-content-center mt-3">
            <a href="index.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Try Again
            </a>
            <a href="index.php#services" class="btn btn-danger btn-sm">
                <i class="bi bi-flag me-1"></i> Report It Lost
            </a>
        </div>
    </div>

<?php endif; ?>

</div>
</div>
<footer>&copy; <?= date('Y') ?> iRecovery &mdash;
    <a href="https://kakebe.tech/" target="_blank" rel="noopener">Kakebe Technologies Limited</a>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Page intentionally stays open so the owner can review the match.
    // They must click "Search Another" / "Report It Lost" to leave.
</script>
</body>
</html>

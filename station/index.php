<?php
// ─────────────────────────────────────────────
// Station Dashboard
// ─────────────────────────────────────────────
session_start();
require_once '../db.php';
require_once '../includes/match_engine.php';

// Auth guard
if (!isset($_SESSION['station_user'])) {
    header('Location: ../login.php');
    exit();
}
$userId = $_SESSION['station_user'];

// ── Mark notifications read ──────────────────
if (isset($_GET['mark_read'])) {
    $mr = $conn->prepare("UPDATE notifications SET is_read=1 WHERE target_role IN ('station','all') AND (target_user IS NULL OR target_user=?)");
    $mr->bind_param('s', $userId);
    $mr->execute();
    $mr->close();
}

// ── Station approval of a match (step 2 of 2 before payment) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_match_station'])) {
    $alert_id = (int)($_POST['alert_id'] ?? 0);
    if ($alert_id > 0) approveMatchByStation($conn, $alert_id, $userId);
    header('Location: index.php');
    exit();
}

// ── Station status actions: paid / pending / collected ─
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_status'])) {
    $alert_id   = (int)($_POST['alert_id'] ?? 0);
    $new_status = $_POST['set_status'];
    $allowed    = ['paid', 'pending', 'collected'];
    if ($alert_id > 0 && in_array($new_status, $allowed, true)) {
        // Scoped strictly to this station's own alerts
        $s = $conn->prepare("UPDATE match_alerts SET alert_status=?, updated_at=NOW() WHERE id=? AND station=?");
        $s->bind_param('sis', $new_status, $alert_id, $userId);
        $s->execute();
        $s->close();

        $r = $conn->query("SELECT document_id FROM match_alerts WHERE id=$alert_id")->fetch_assoc();
        $did = $r ? (int)$r['document_id'] : 0;

        if ($new_status === 'collected' && $did > 0) {
            @$conn->query("UPDATE documents SET action='collected' WHERE id=$did");
            $collector = trim($_POST['collected_by'] ?? '');
            $cs = $conn->prepare("INSERT INTO collection_log (document_id, alert_id, station, collected_by) VALUES (?,?,?,?)");
            $cs->bind_param('iiis', $did, $alert_id, $userId, $collector);
            $cs->execute();
            $cs->close();
            createNotification($conn, 'doc_collected', 'admin', null,
                "Document collected at station '$userId' by '$collector'. Alert #$alert_id.", $alert_id);
        } elseif ($new_status === 'paid' && $did > 0) {
            @$conn->query("UPDATE documents SET payment_status='paid', payment_date=NOW() WHERE id=$did");
        }
    }
    header('Location: index.php');
    exit();
}

// ── Collection confirmation (by alert id) ─────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_collection'])) {
    $alert_id = (int)($_POST['alert_id'] ?? 0);
    if ($alert_id > 0) {
        $s = $conn->prepare("UPDATE match_alerts SET alert_status='collected' WHERE id=? AND station=?");
        $s->bind_param('is', $alert_id, $userId); $s->execute(); $s->close();
        $r = $conn->query("SELECT document_id FROM match_alerts WHERE id=$alert_id")->fetch_assoc();
        if ($r) {
            $did = (int)$r['document_id'];
            if ($did > 0) @$conn->query("UPDATE documents SET action='collected' WHERE id=$did");
            $collector = trim($_POST['collected_by'] ?? '');
            $cs = $conn->prepare("INSERT INTO collection_log (document_id, alert_id, station, collected_by) VALUES (?,?,?,?)");
            $cs->bind_param('iiis', $did, $alert_id, $userId, $collector); $cs->execute(); $cs->close();
            createNotification($conn, 'doc_collected', 'admin', null,
                "Document collected at station '$userId' by '$collector'. Alert #$alert_id.", $alert_id);
        }
    }
    header('Location: index.php');
    exit();
}

// ── Verify receipt code → prepare release ─────
$verify = null;     // result row for verified payment
$verifyErr = '';
$released = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_code'])) {
    $code = trim(strtoupper(preg_replace('/\s+/', '', $_POST['code'] ?? '')));
    if (empty($code)) {
        $verifyErr = 'Please enter the verification code from the receipt.';
    } else {
        $vs = $conn->prepare(
            "SELECT p.id, p.payer_name, p.payer_phone, p.id_number, p.station_commission, p.status, p.download_allowed, p.verification_code,
                    ma.id AS alert_id, ma.alert_status, ma.station,
                    lr.doc_type, lr.sur_name, lr.given_name
             FROM payments p
             LEFT JOIN match_alerts ma ON ma.id = p.match_alert_id
             LEFT JOIN lost_reports  lr ON lr.id = ma.lost_report_id
             WHERE p.verification_code = ? LIMIT 1"
        );
        $vs->bind_param('s', $code);
        $vs->execute();
        $verify = $vs->get_result()->fetch_assoc();
        $vs->close();

        if (!$verify) {
            $verifyErr = 'Invalid verification code. Please check the code on the receipt and try again.';
        } elseif ($verify['status'] !== 'confirmed' || (int)$verify['download_allowed'] !== 1) {
            $verifyErr = 'This payment has not been approved by admin yet. The owner cannot collect until payment is confirmed.';
        } elseif (in_array($verify['alert_status'], ['collected', 'closed'], true)) {
            $verifyErr = 'This document has already been collected (Alert #' . (int)$verify['alert_id'] . ').';
        } elseif ($verify['station'] !== $userId) {
            $verifyErr = 'This receipt belongs to a different station (' . htmlspecialchars($verify['station'] ?? 'Unknown') . '). Please direct the owner there.';
        }
    }
}

// ── Stats scoped to this station ──────────────
$stmt = $conn->prepare("SELECT COUNT(*) c FROM national_ids WHERE reporter=?"); $stmt->bind_param('s',$userId); $stmt->execute(); $nidCount = $stmt->get_result()->fetch_assoc()['c']; $stmt->close();
$stmt = $conn->prepare("SELECT COUNT(*) c FROM driving_permits WHERE reporter=?"); $stmt->bind_param('s',$userId); $stmt->execute(); $dpCount  = $stmt->get_result()->fetch_assoc()['c']; $stmt->close();
$stmt = $conn->prepare("SELECT COUNT(*) c FROM student_ids WHERE reporter=?");    $stmt->bind_param('s',$userId); $stmt->execute(); $sidCount = $stmt->get_result()->fetch_assoc()['c']; $stmt->close();
$stmt = $conn->prepare("SELECT COUNT(*) c FROM documents WHERE station_holding=? AND action='found'"); $stmt->bind_param('s',$userId); $stmt->execute(); $newCount = $stmt->get_result()->fetch_assoc()['c']; $stmt->close();
$totalFound = $nidCount + $dpCount + $sidCount + $newCount;

$stmt = $conn->prepare("SELECT COUNT(*) c FROM match_alerts WHERE station=? AND alert_status NOT IN ('collected','closed')"); $stmt->bind_param('s',$userId); $stmt->execute(); $alertCount = $stmt->get_result()->fetch_assoc()['c']; $stmt->close();
$stmt = $conn->prepare("SELECT COUNT(*) c FROM payments p INNER JOIN match_alerts ma ON ma.id=p.match_alert_id WHERE ma.station=? AND p.status='confirmed'"); $stmt->bind_param('s',$userId); $stmt->execute(); $paidCount = $stmt->get_result()->fetch_assoc()['c']; $stmt->close();
$stmt = $conn->prepare("SELECT COUNT(*) c FROM match_alerts WHERE station=? AND alert_status='collected'"); $stmt->bind_param('s',$userId); $stmt->execute(); $collectedCount = $stmt->get_result()->fetch_assoc()['c']; $stmt->close();
$stmt = $conn->prepare("SELECT COUNT(*) c FROM match_alerts WHERE station=? AND alert_status='pending'"); $stmt->bind_param('s',$userId); $stmt->execute(); $pendingCount = $stmt->get_result()->fetch_assoc()['c']; $stmt->close();
$stmt = $conn->prepare("SELECT COUNT(*) c FROM national_ids WHERE user_action='Reported' AND reporter=?"); $stmt->bind_param('s',$userId); $stmt->execute(); $rptCount = $stmt->get_result()->fetch_assoc()['c']; $stmt->close();

$unreadNotif = getUnreadCount($conn, 'station', $userId);

$earn = $conn->prepare("SELECT COALESCE(SUM(p.station_commission),0) total, COUNT(*) cnt FROM payments p JOIN match_alerts ma ON ma.id=p.match_alert_id WHERE ma.station=? AND p.status='confirmed'");
$earn->bind_param('s', $userId); $earn->execute();
$earnRow = $earn->get_result()->fetch_assoc(); $earn->close();
$totalEarnings = (float)$earnRow['total'];
$earningsCount = (int)$earnRow['cnt'];

// ── Staff document search ────────────────────────
$docSearchQuery   = trim($_GET['q'] ?? '');
$docSearchResults = isset($_GET['doc_search']) ? searchDocumentsBroad($conn, $docSearchQuery) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($userId) ?> — Station Dashboard | iRecovery</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/1570/1570887.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/variables.css?v=<?= @filemtime(__DIR__.'/../assets/css/variables.css') ?>">
    <link rel="stylesheet" href="../assets/css/dashboard.css?v=<?= @filemtime(__DIR__.'/../assets/css/dashboard.css') ?>">
</head>
<body>

  <div class="dash-shell">
    <!-- ── Sidebar ─────────────────────────────── -->
    <aside class="sidebar" id="sidebar">
      <a href="index.php" class="sidebar-brand">
        <span class="hd-brand-ico"><i class="bi bi-building"></i></span>
        iRecovery Station
      </a>
      <nav class="sidebar-nav" id="tabBar">
        <a href="../index.php" target="_blank" rel="noopener" class="sidebar-link"><i class="bi bi-box-arrow-up-right"></i> View Website</a>
        <button class="sidebar-link" onclick="switchTab(this,'tDocSearch')"><i class="bi bi-search-heart"></i> Search Documents</button>
        <button class="sidebar-link active" id="tabMatches" onclick="switchTab(this,'tMatches')"><i class="bi bi-lightning-charge"></i> Matches<?php if ($alertCount > 0): ?><span class="nb"><?= $alertCount ?></span><?php endif; ?></button>
        <button class="sidebar-link" onclick="switchTab(this,'tFound')"><i class="bi bi-cloud-upload"></i> Found Docs</button>
        <button class="sidebar-link" onclick="switchTab(this,'tVerify')"><i class="bi bi-shield-check"></i> Verify Code</button>
        <button class="sidebar-link" onclick="switchTab(this,'tCollected')"><i class="bi bi-check2-circle"></i> Collected</button>
        <button class="sidebar-link" onclick="switchTab(this,'tEarnings')"><i class="bi bi-wallet2"></i> My Earnings</button>
      </nav>
      <div class="sidebar-foot">
        <div class="sidebar-user">
          <div class="u-av"><?= strtoupper(substr($userId, 0, 1)) ?></div>
          <div><div class="u-nm"><?= htmlspecialchars($userId) ?></div><div class="u-role">Station Admin</div></div>
        </div>
        <a href="logout.php" class="btn-out" style="width:100%;justify-content:center;"><i class="bi bi-box-arrow-right"></i> Logout</a>
      </div>
    </aside>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="main-area">
      <!-- ── Topbar ──────────────────────────────── -->
      <div class="topbar">
        <button class="hamburger" onclick="openSidebar()" aria-label="Menu"><i class="bi bi-list"></i></button>
        <div style="margin-left:auto;display:flex;align-items:center;gap:.75rem;">
          <a href="?mark_read=1" class="hd-bell hd-bell-light" title="Notifications">
            <i class="bi bi-bell-fill"></i>
            <?php if ($unreadNotif > 0): ?><span class="nb-dot"><?= $unreadNotif ?></span><?php endif; ?>
          </a>
        </div>
      </div>

  <div class="page">

    <!-- ── Welcome bar ───────────────────────── -->
    <div class="welcome-bar">
      <div>
        <h1><i class="bi bi-hand-thumbs-up-fill" style="color:var(--teal);font-size:1.1rem;margin-right:.4rem;"></i>Welcome back, <?= htmlspecialchars($userId) ?></h1>
        <p>Here's what's happening at your station today.</p>
      </div>
      <div class="welcome-chip"><i class="bi bi-calendar3"></i> <?= date('l, d F Y') ?></div>
    </div>

    <?php if ($alertCount > 0): ?>
    <div class="alert-banner">
      <div class="ab-ico"><i class="bi bi-lightning-charge"></i></div>
      <div>
        <div class="ab-title"><?= $alertCount ?> active match<?= $alertCount > 1 ? 'es' : '' ?> need your attention</div>
        <div class="ab-sub">Update payment status or verify a receipt code to release these documents.</div>
      </div>
      <button class="btn btn-warning" onclick="switchTab(document.getElementById('tabMatches'), 'tMatches')"><i class="bi bi-arrow-right-circle"></i> Review</button>
    </div>
    <?php endif; ?>

    <!-- ── Stat cards ────────────────────────── -->
    <div class="stats">
      <div class="sc sc-blue">
        <div class="sc-ico"><i class="bi bi-cloud-upload"></i></div>
        <div class="sc-val"><?= $totalFound ?></div>
        <div class="sc-lbl">Total Uploaded</div>
      </div>
      <div class="sc sc-navy">
        <div class="sc-ico"><i class="bi bi-lightning-charge"></i></div>
        <div class="sc-val"><?= $alertCount ?></div>
        <div class="sc-lbl">Active Matches</div>
      </div>
      <div class="sc sc-green">
        <div class="sc-ico"><i class="bi bi-phone"></i></div>
        <div class="sc-val"><?= $paidCount ?></div>
        <div class="sc-lbl">Payments</div>
      </div>
      <div class="sc sc-teal">
        <div class="sc-ico"><i class="bi bi-check2-circle"></i></div>
        <div class="sc-val"><?= $collectedCount ?></div>
        <div class="sc-lbl">Collected</div>
      </div>
      <div class="sc sc-amber">
        <div class="sc-ico"><i class="bi bi-hourglass-split"></i></div>
        <div class="sc-val"><?= $pendingCount ?></div>
        <div class="sc-lbl">Pending</div>
      </div>
      <div class="sc sc-amber">
        <div class="sc-ico"><i class="bi bi-flag"></i></div>
        <div class="sc-val"><?= $rptCount ?></div>
        <div class="sc-lbl">Lost Reports</div>
      </div>
      <div class="sc sc-teal">
        <div class="sc-ico"><i class="bi bi-wallet2"></i></div>
        <div class="sc-val">UGX <?= number_format($totalEarnings) ?></div>
        <div class="sc-lbl">My Earnings</div>
      </div>
    </div>

    <!-- ── Search Documents ─────────────────────── -->
    <div id="tDocSearch" class="tcard" style="display:none;">
      <div class="p-4">
        <form method="GET" class="mb-3" style="display:flex;gap:.6rem;flex-wrap:wrap;">
          <input type="hidden" name="doc_search" value="1">
          <input type="text" name="q" class="fc" style="max-width:340px;flex:1;" placeholder="Search by ID / NIN number or name…" value="<?= htmlspecialchars($docSearchQuery) ?>">
          <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
        </form>
        <p style="color:var(--muted);font-size:.82rem;margin:-.5rem 0 1rem;">Searches all found documents system-wide, so you can help any caller regardless of which station is holding their document.</p>
        <div class="table-responsive">
          <table class="dt">
            <thead><tr><th>ID</th><th>Doc Type</th><th>Owner Name</th><th>ID / NIN</th><th>Station Holding</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
            <?php if ($docSearchQuery === ''): ?>
              <tr><td colspan="8"><div class="empty"><i class="bi bi-search-heart ei"></i>Enter an ID number or name above to search all found documents</div></td></tr>
            <?php elseif (empty($docSearchResults)): ?>
              <tr><td colspan="8"><div class="empty"><i class="bi bi-search-heart ei"></i>No documents match "<?= htmlspecialchars($docSearchQuery) ?>"</div></td></tr>
            <?php else: foreach ($docSearchResults as $r):
              $ab = match($r['action']) {
                'found', 'Found'         => '<span class="bd bd-green">Found</span>',
                'matched'                => '<span class="bd bd-amber">Matched</span>',
                'collected', 'Collected' => '<span class="bd bd-grey">Collected</span>',
                default                  => '<span class="bd bd-grey">' . htmlspecialchars($r['action']) . '</span>'
              };
            ?>
              <tr>
                <td><?= (int)$r['id'] ?></td>
                <td><span class="bd bd-blue"><?= htmlspecialchars(ucwords(str_replace('_',' ',$r['doc_type']))) ?></span></td>
                <td><?= htmlspecialchars(trim($r['sur_name'].' '.$r['given_name'])) ?></td>
                <td><?= htmlspecialchars($r['id_number'] ?? '—') ?></td>
                <td><?= htmlspecialchars($r['station_holding'] ?? '—') ?></td>
                <td><?= $ab ?></td>
                <td><?= htmlspecialchars($r['submitted_at'] ?? '—') ?></td>
                <td><button class="btn btn-outline view-btn"
                  data-id="<?= (int)$r['id'] ?>" data-type="<?= htmlspecialchars(ucwords(str_replace('_',' ',$r['doc_type']))) ?>" data-name="<?= htmlspecialchars($r['sur_name']) ?>"
                  data-second-name="<?= htmlspecialchars($r['given_name']) ?>" data-front-image="<?= htmlspecialchars(docImageUrl($r['front_img'], '../')) ?>" data-back-image="<?= htmlspecialchars(docImageUrl($r['back_img'], '../')) ?>"
                  data-status="<?= htmlspecialchars($r['action']) ?>" data-date="<?= htmlspecialchars($r['submitted_at'] ?? '') ?>"><i class="bi bi-eye"></i> View</button></td>
              </tr>
            <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ── Match Alerts ──────────────────────── -->
    <div id="tMatches" class="tcard">
      <div class="table-responsive">
        <table class="dt">
          <thead><tr><th>#</th><th>Document</th><th>Owner</th><th>Reporter Contact</th><th>Approval</th><th>Your Commission</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
          <tbody>
          <?php
          $maStmt = $conn->prepare("
            SELECT ma.id, ma.alert_status, ma.created_at, ma.admin_approved, ma.station_approved,
                   lr.doc_type, lr.sur_name, lr.given_name, lr.reporter_name, lr.reporter_phone,
                   p.id as pay_id, p.status as pay_status, p.station_commission, p.payer_phone
            FROM match_alerts ma
            LEFT JOIN lost_reports lr ON lr.id = ma.lost_report_id
            LEFT JOIN payments p ON p.match_alert_id = ma.id
            WHERE ma.station = ?
            ORDER BY ma.created_at DESC");
          $maStmt->bind_param('s', $userId); $maStmt->execute();
          $maRes = $maStmt->get_result(); $maStmt->close();
          if ($maRes && $maRes->num_rows > 0):
            while ($r = $maRes->fetch_assoc()):
              $statusBadge = match($r['alert_status']) {
                'new'             => '<span class="bd bd-danger">New</span>',
                'payment_pending' => '<span class="bd bd-teal">Ready to Pay</span>',
                'paid'            => '<span class="bd bd-green">Paid — Ready</span>',
                'pending'         => '<span class="bd bd-amber">Pending</span>',
                'collected'       => '<span class="bd bd-grey">Collected</span>',
                'owner_notified'  => '<span class="bd bd-blue">Owner Notified</span>',
                default           => '<span class="bd bd-grey">' . htmlspecialchars($r['alert_status']) . '</span>'
              };
              $adminApproved   = (int)$r['admin_approved'] === 1;
              $stationApproved = (int)$r['station_approved'] === 1;
              $approvalChips = ($adminApproved ? '<span class="bd bd-green"><i class="bi bi-shield-check"></i> Admin</span>' : '<span class="bd bd-grey">Admin Pending</span>')
                             . ' ' . ($stationApproved ? '<span class="bd bd-green"><i class="bi bi-building-check"></i> You</span>' : '<span class="bd bd-grey">Your Confirm.</span>');
              // Commission-only — station never sees the real fee amount, only their cut
              $payBadge = $r['pay_id']
                ? ($r['pay_status'] === 'confirmed' ? '<span class="bd bd-green">UGX ' . number_format((float)($r['station_commission'] ?? 0)) . '</span>'
                                                     : '<span class="bd bd-amber">Pending</span>')
                : '<span class="bd bd-grey">None</span>';
              echo "<tr>
                <td>{$r['id']}</td>
                <td><span class='bd bd-blue'>" . htmlspecialchars(ucwords(str_replace('_', ' ', $r['doc_type'] ?? ''))) . "</span></td>
                <td>" . htmlspecialchars(($r['sur_name'] ?? '') . ' ' . ($r['given_name'] ?? '')) . "</td>
                <td>" . htmlspecialchars($r['reporter_name'] ?? '—') . "<br><a href='tel:" . htmlspecialchars($r['reporter_phone'] ?? '') . "' style='color:var(--teal);'>" . htmlspecialchars($r['reporter_phone'] ?? '—') . "</a></td>
                <td>$approvalChips</td>
                <td>$payBadge</td>
                <td>$statusBadge</td>
                <td>" . htmlspecialchars($r['created_at']) . "</td>
                <td>";
              if ($r['alert_status'] === 'collected') {
                echo "<span style='color:var(--green);font-weight:600;'><i class='bi bi-check2-all'></i> Done</span>";
              } else {
                $aid = (int)$r['id'];
                if (!$stationApproved) {
                  echo "<form method='POST' class='d-inline'><input type='hidden' name='alert_id' value='$aid'><button type='submit' name='approve_match_station' class='btn btn-primary btn-sm mb-1'><i class='bi bi-building-check'></i> Confirm It's Ours</button></form> ";
                }
                echo "<div class='act-grp'>
                  <form method='POST' class='d-inline'><input type='hidden' name='alert_id' value='$aid'><button type='submit' name='set_status' value='paid' class='btn btn-success btn-sm'><i class='bi bi-cash'></i> Paid</button></form>
                  <form method='POST' class='d-inline'><input type='hidden' name='alert_id' value='$aid'><button type='submit' name='set_status' value='pending' class='btn btn-warning btn-sm'><i class='bi bi-hourglass-split'></i> Pending</button></form>
                  <button type='button' class='btn btn-teal btn-sm' onclick='openCollectModal($aid)'><i class='bi bi-check2-circle'></i> Collected</button>
                </div>";
              }
              echo "</td></tr>";
            endwhile;
          else: echo "<tr><td colspan='9'><div class='empty'><i class='bi bi-lightning-charge ei'></i>No match alerts for your station yet</div></td></tr>";
          endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ── Found Documents uploaded by this station ── -->
    <div id="tFound" class="tcard" style="display:none;">
      <div class="table-responsive">
        <table class="dt">
          <thead><tr><th>ID</th><th>Doc Type</th><th>Owner Name</th><th>ID / NIN</th><th>Status</th><th>Uploaded</th><th>Action</th></tr></thead>
          <tbody>
          <?php
          $hasRows = false;
          $dStmt = $conn->prepare("SELECT id, doc_type, sur_name, given_name, id_number, dob, gender, front_img, back_img, action, submitted_at FROM documents WHERE station_holding=? ORDER BY submitted_at DESC LIMIT 200");
          $dStmt->bind_param('s', $userId); $dStmt->execute();
          $dRes = $dStmt->get_result(); $dStmt->close();
          if ($dRes && $dRes->num_rows > 0):
            $hasRows = true;
            while ($r = $dRes->fetch_assoc()):
              $ab = match($r['action']) {
                'found'     => '<span class="bd bd-green">Found</span>',
                'matched'   => '<span class="bd bd-amber">Matched</span>',
                'collected' => '<span class="bd bd-grey">Collected</span>',
                default     => '<span class="bd bd-grey">' . htmlspecialchars($r['action']) . '</span>'
              };
              $frontUrl = htmlspecialchars(docImageUrl($r['front_img'] ?? '', '../uploads/'));
              $backUrl  = htmlspecialchars(docImageUrl($r['back_img']  ?? '', '../uploads/'));
              echo "<tr>
                <td>{$r['id']}</td>
                <td><span class='bd bd-blue'>" . htmlspecialchars(ucwords(str_replace('_', ' ', $r['doc_type']))) . "</span></td>
                <td>" . htmlspecialchars($r['sur_name'] . ' ' . $r['given_name']) . "</td>
                <td>" . htmlspecialchars($r['id_number'] ?? '—') . "</td>
                <td>$ab</td>
                <td>" . htmlspecialchars($r['submitted_at']) . "</td>
                <td><button class='btn btn-outline view-btn' data-id='{$r['id']}' data-type='" . htmlspecialchars(ucwords(str_replace('_', ' ', $r['doc_type']))) . "' data-name='" . htmlspecialchars($r['sur_name']) . "' data-second-name='" . htmlspecialchars($r['given_name']) . "' data-front-image='{$frontUrl}' data-back-image='{$backUrl}' data-id-number='" . htmlspecialchars($r['id_number'] ?? '') . "' data-dob='" . htmlspecialchars($r['dob'] ?? '') . "' data-gender='" . htmlspecialchars(ucfirst($r['gender'] ?? '')) . "' data-status='" . htmlspecialchars($r['action']) . "' data-date='" . htmlspecialchars($r['submitted_at']) . "'><i class='bi bi-eye'></i> View</button></td>
              </tr>";
            endwhile;
          endif;

          $legStmt = $conn->prepare("SELECT national_id as id,'National ID' as doc_type, sur_name, given_name, nin_number as id_number, dob, gender, front as front_img, back as back_img, user_action as action, date_found as submitted_at FROM national_ids WHERE reporter=? ORDER BY national_id DESC LIMIT 100");
          $legStmt->bind_param('s', $userId); $legStmt->execute();
          $legRes = $legStmt->get_result(); $legStmt->close();
          if ($legRes && $legRes->num_rows > 0):
            $hasRows = true;
            while ($r = $legRes->fetch_assoc()):
              $ab = ($r['action'] === 'Found') ? '<span class="bd bd-green">Found</span>' : '<span class="bd bd-amber">' . htmlspecialchars($r['action']) . '</span>';
              $frontUrl = htmlspecialchars(docImageUrl($r['front_img'], '../uploads/'));
              $backUrl  = htmlspecialchars(docImageUrl($r['back_img'],  '../uploads/'));
              echo "<tr>
                <td>{$r['id']}</td>
                <td><span class='bd bd-blue'>{$r['doc_type']}</span></td>
                <td>" . htmlspecialchars($r['sur_name'] . ' ' . $r['given_name']) . "</td>
                <td>" . htmlspecialchars($r['id_number']) . "</td>
                <td>$ab</td>
                <td>" . htmlspecialchars($r['submitted_at']) . "</td>
                <td><button class='btn btn-outline view-btn' data-id='{$r['id']}' data-type='{$r['doc_type']}' data-name='" . htmlspecialchars($r['sur_name']) . "' data-second-name='" . htmlspecialchars($r['given_name']) . "' data-front-image='{$frontUrl}' data-back-image='{$backUrl}' data-id-number='" . htmlspecialchars($r['id_number'] ?? '') . "' data-dob='" . htmlspecialchars($r['dob'] ?? '') . "' data-gender='" . htmlspecialchars(ucfirst($r['gender'] ?? '')) . "' data-status='" . htmlspecialchars($r['action']) . "' data-date='" . htmlspecialchars($r['submitted_at']) . "'><i class='bi bi-eye'></i> View</button></td>
              </tr>";
            endwhile;
          endif;

          if (!$hasRows) echo "<tr><td colspan='7'><div class='empty'><i class='bi bi-cloud-upload ei'></i>No documents uploaded yet</div></td></tr>";
          ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ── Verify Receipt Code ────────────────── -->
    <div id="tVerify" class="tcard" style="display:none;">
      <div class="verify-grid">
        <div class="verify-box">
          <h6 class="mb-1"><i class="bi bi-shield-check me-2" style="color:var(--teal);"></i>Verify Receipt &amp; Release Document</h6>
          <p style="color:var(--muted);font-size:.85rem;margin-bottom:1rem;">
            Ask the owner for the <strong>verification code</strong> printed on their PDF receipt. Enter it below to confirm payment and release the document.
          </p>
          <form method="POST">
            <label class="fl">Receipt Verification Code</label>
            <input type="text" name="code" class="fc code-input" placeholder="XXXX-XXXX-XX" autocomplete="off" value="<?= htmlspecialchars($_POST['code'] ?? '') ?>" required>
            <button type="submit" name="verify_code" class="btn btn-teal btn-block mt-3"><i class="bi bi-shield-check"></i> Verify &amp; Prepare Release</button>
          </form>

          <?php if ($verifyErr): ?>
            <div class="verify-result verify-bad"><i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($verifyErr) ?></div>
          <?php elseif ($verify): ?>
            <div class="verify-result verify-ok">
              <div class="d-flex align-items-center gap-2 mb-2" style="color:var(--green);">
                <i class="bi bi-check-circle-fill" style="font-size:1.3rem;"></i>
                <strong>Payment verified — ready to release</strong>
              </div>
              <div class="verify-code-display"><?= htmlspecialchars(rtrim(chunk_split($verify['verification_code'], 3, '-'), '-')) ?></div>
              <div class="kv"><span class="k">Document Owner</span><span class="v"><?= htmlspecialchars(trim(($verify['sur_name'] ?? '') . ' ' . ($verify['given_name'] ?? '')) ?: $verify['payer_name']) ?></span></div>
              <div class="kv"><span class="k">Document Type</span><span class="v"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $verify['doc_type'] ?? 'Document'))) ?></span></div>
              <div class="kv"><span class="k">ID / NIN</span><span class="v"><?= htmlspecialchars($verify['id_number'] ?? '—') ?></span></div>
              <div class="kv"><span class="k">Your Commission</span><span class="v">UGX <?= number_format((float)($verify['station_commission'] ?? 0)) ?></span></div>
              <div class="kv"><span class="k">Payer Phone</span><span class="v"><?= htmlspecialchars($verify['payer_phone'] ?? '—') ?></span></div>

              <form method="POST" class="mt-3">
                <input type="hidden" name="alert_id" value="<?= (int)$verify['alert_id'] ?>">
                <label class="fl">Collected By (full name of owner)</label>
                <input type="text" name="collected_by" class="fc" placeholder="e.g. John Okello" required>
                <button type="submit" name="confirm_collection" class="btn btn-success btn-block mt-2">
                  <i class="bi bi-check2-all me-2"></i>Confirm Collection &amp; Hand Over Document
                </button>
              </form>
            </div>
          <?php endif; ?>
        </div>
        <div class="verify-side">
          <h6><i class="bi bi-info-circle"></i> How release works</h6>
          <div class="verify-step"><div class="vs-n">1</div><span>Owner shows their printed or saved PDF receipt with the verification code.</span></div>
          <div class="verify-step"><div class="vs-n">2</div><span>Enter the code here — the system confirms payment was approved by admin.</span></div>
          <div class="verify-step"><div class="vs-n">3</div><span>Check the owner's government-issued ID matches the document details shown.</span></div>
          <div class="verify-step"><div class="vs-n">4</div><span>Confirm collection to hand over the document and close the case.</span></div>
        </div>
      </div>
    </div>

    <!-- ── Collected Documents ────────────────── -->
    <div id="tCollected" class="tcard" style="display:none;">
      <div class="table-responsive">
        <table class="dt">
          <thead><tr><th>#</th><th>Document</th><th>Owner</th><th>Collected By</th><th>Date Collected</th></tr></thead>
          <tbody>
          <?php
          $clStmt = $conn->prepare("
            SELECT cl.id, cl.collected_by, cl.collected_at, d.doc_type, d.sur_name, d.given_name, d.id_number
            FROM collection_log cl
            LEFT JOIN documents d ON d.id = cl.document_id
            WHERE cl.station = ?
            ORDER BY cl.collected_at DESC LIMIT 100");
          $clStmt->bind_param('s', $userId); $clStmt->execute();
          $clRes = $clStmt->get_result(); $clStmt->close();
          if ($clRes && $clRes->num_rows > 0):
            while ($r = $clRes->fetch_assoc()):
              echo "<tr>
                <td>{$r['id']}</td>
                <td><span class='bd bd-grey'>" . htmlspecialchars(ucwords(str_replace('_', ' ', $r['doc_type'] ?? ''))) . "</span></td>
                <td>" . htmlspecialchars(($r['sur_name'] ?? '') . ' ' . ($r['given_name'] ?? '')) . " — " . htmlspecialchars($r['id_number'] ?? '') . "</td>
                <td>" . htmlspecialchars($r['collected_by'] ?? '—') . "</td>
                <td>" . htmlspecialchars($r['collected_at']) . "</td>
              </tr>";
            endwhile;
          else: echo "<tr><td colspan='5'><div class='empty'><i class='bi bi-check2-circle ei'></i>No collections recorded yet</div></td></tr>";
          endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ── My Earnings ─────────────────────────── -->
    <div id="tEarnings" class="tcard" style="display:none;">
      <div class="p-4">
        <div class="stats" style="margin-bottom:1.5rem;">
          <div class="sc sc-teal">
            <div class="sc-ico"><i class="bi bi-wallet2"></i></div>
            <div class="sc-val">UGX <?= number_format($totalEarnings) ?></div>
            <div class="sc-lbl">Total Earned (<?= $earningsCount ?> pickups)</div>
          </div>
        </div>
        <p style="color:var(--muted);font-size:.85rem;">
          This is your commission from confirmed payments — the percentage set by iRecovery admin per document type. Full fee amounts are not shown here; only your share.
        </p>
        <div class="table-responsive mt-3">
          <table class="dt">
            <thead><tr><th>#</th><th>Document</th><th>ID / NIN</th><th>Your Commission</th><th>Date</th></tr></thead>
            <tbody>
            <?php
            $eq = $conn->prepare("
              SELECT p.id, p.id_number, p.station_commission, p.confirmed_at, lr.doc_type
              FROM payments p
              JOIN match_alerts ma ON ma.id = p.match_alert_id
              LEFT JOIN lost_reports lr ON lr.id = ma.lost_report_id
              WHERE ma.station=? AND p.status='confirmed'
              ORDER BY p.confirmed_at DESC LIMIT 100");
            $eq->bind_param('s', $userId); $eq->execute();
            $eRes = $eq->get_result(); $eq->close();
            if ($eRes && $eRes->num_rows > 0):
              while ($r = $eRes->fetch_assoc()):
                echo "<tr><td>{$r['id']}</td><td><span class='bd bd-blue'>" . htmlspecialchars(ucwords(str_replace('_',' ',$r['doc_type'] ?? ''))) . "</span></td><td>" . htmlspecialchars($r['id_number'] ?? '—') . "</td><td><span class='bd bd-green'>UGX " . number_format((float)$r['station_commission']) . "</span></td><td>" . htmlspecialchars($r['confirmed_at'] ?? '—') . "</td></tr>";
              endwhile;
            else: echo "<tr><td colspan='5'><div class='empty'><i class='bi bi-wallet2 ei'></i>No earnings yet</div></td></tr>";
            endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div><!-- /.page -->
    </div><!-- /.main-area -->
  </div><!-- /.dash-shell -->

  <!-- ── Collection Modal (by alert) ─────────── -->
  <div id="collectModal" class="mo-bg">
    <div class="mo-box" style="max-width:480px;">
      <div class="mo-hd">
        <div class="mo-title"><i class="bi bi-check2-circle me-2" style="color:var(--teal);"></i>Confirm Document Collection</div>
        <button class="mo-close" onclick="closeModal('collectModal')">&times;</button>
      </div>
      <p style="color:var(--muted);font-size:.9rem;">Confirm that the owner has physically collected this document. Enter the collector's name as verification.</p>
      <form method="POST">
        <input type="hidden" name="alert_id" id="collectAlertId" value="">
        <div class="mb-3">
          <label class="fl">Collected By (full name of owner)</label>
          <input type="text" name="collected_by" class="fc" placeholder="e.g. John Okello" required>
        </div>
        <button type="submit" name="confirm_collection" class="btn btn-success btn-block">
          <i class="bi bi-check2-all me-2"></i>Confirm Collection &amp; Close Case
        </button>
      </form>
    </div>
  </div>

  <!-- ── Document Detail Modal ───────────────── -->
  <div id="docModal" class="mo-bg">
    <div class="mo-box">
      <div class="mo-hd">
        <div class="mo-title"><i class="bi bi-file-earmark-text me-2" style="color:var(--blue);"></i>Document Details</div>
        <button class="mo-close" onclick="closeModal('docModal')">&times;</button>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <div class="detail-row"><div class="detail-label">ID</div><div class="detail-value" id="dPopupId">—</div></div>
          <div class="detail-row"><div class="detail-label">Document Type</div><div class="detail-value" id="dPopupType">—</div></div>
          <div class="detail-row"><div class="detail-label">Surname</div><div class="detail-value" id="dPopupName">—</div></div>
          <div class="detail-row"><div class="detail-label">Given Name</div><div class="detail-value" id="dPopupGiven">—</div></div>
        </div>
        <div class="col-md-6">
          <div class="detail-row"><div class="detail-label">ID / NIN Number</div><div class="detail-value" id="dPopupIdNumber">—</div></div>
          <div class="detail-row"><div class="detail-label">Date of Birth</div><div class="detail-value" id="dPopupDob">—</div></div>
          <div class="detail-row"><div class="detail-label">Gender</div><div class="detail-value" id="dPopupGender">—</div></div>
          <div class="detail-row"><div class="detail-label">Status</div><div class="detail-value"><span class="bd" id="dPopupStatus">—</span></div></div>
          <div class="detail-row"><div class="detail-label">Date</div><div class="detail-value" id="dPopupDate">—</div></div>
        </div>
      </div>
      <hr style="border-color:var(--border);">
      <h6 style="color:var(--muted);"><i class="bi bi-images me-2"></i>Document Images</h6>
      <div class="row g-2 mt-2">
        <div class="col-md-6"><div class="detail-label mb-1">Front</div><img id="dPopupFront" src="" alt="Front" class="doc-image"></div>
        <div class="col-md-6"><div class="detail-label mb-1">Back</div><img id="dPopupBack" src="" alt="Back" class="doc-image"></div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function switchTab(btn, id) {
      document.querySelectorAll('.tcard').forEach(c => c.style.display = 'none');
      document.querySelectorAll('.sidebar-link').forEach(b => b.classList.remove('active'));
      document.getElementById(id).style.display = 'block';
      btn.classList.add('active');
      closeSidebar();
    }
    function openSidebar()  { document.getElementById('sidebar').classList.add('open'); document.getElementById('sidebarOverlay').classList.add('open'); }
    function closeSidebar() { document.getElementById('sidebar').classList.remove('open'); document.getElementById('sidebarOverlay').classList.remove('open'); }
    <?php if (isset($_GET['doc_search'])): ?>
    document.addEventListener('DOMContentLoaded', () => {
      const link = [...document.querySelectorAll('.sidebar-link')].find(b => b.textContent.includes('Search Documents'));
      if (link) switchTab(link, 'tDocSearch');
    });
    <?php endif; ?>
    function filterTable() {
      const q = document.getElementById('searchInput')?.value.toLowerCase() ?? '';
      const tbl = document.querySelector('.tcard[style*="block"]') || document.getElementById('tMatches');
      tbl.querySelectorAll('tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
      });
    }
    function openModal(id) { document.getElementById(id).classList.add('open'); document.body.style.overflow = 'hidden'; }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow = 'auto'; }
    window.addEventListener('click', e => {
      ['collectModal', 'docModal'].forEach(id => {
        const el = document.getElementById(id);
        if (el && e.target === el) closeModal(id);
      });
    });
    function openCollectModal(alertId) {
      document.getElementById('collectAlertId').value = alertId;
      openModal('collectModal');
    }
    document.querySelectorAll('.view-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.getElementById('dPopupId').textContent     = btn.dataset.id         || '—';
        document.getElementById('dPopupType').textContent   = btn.dataset.type       || '—';
        document.getElementById('dPopupName').textContent   = btn.dataset.name       || '—';
        document.getElementById('dPopupGiven').textContent  = btn.dataset.secondName || '—';
        document.getElementById('dPopupIdNumber').textContent = btn.dataset.idNumber || '—';
        document.getElementById('dPopupDob').textContent    = btn.dataset.dob        || '—';
        document.getElementById('dPopupGender').textContent = btn.dataset.gender     || '—';
        document.getElementById('dPopupDate').textContent   = btn.dataset.date       || '—';
        const sb = document.getElementById('dPopupStatus');
        sb.textContent = btn.dataset.status || '—';
        sb.className = 'bd ' + ((btn.dataset.status === 'found' || btn.dataset.status === 'Found') ? 'bd-green' : 'bd-amber');
        document.getElementById('dPopupFront').src = btn.dataset.frontImage || '';
        document.getElementById('dPopupBack').src  = btn.dataset.backImage  || '';
        openModal('docModal');
      });
    });
  </script>
</body>
</html>

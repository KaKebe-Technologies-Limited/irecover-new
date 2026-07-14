<?php
session_start();
require_once '../db.php';
require_once '../includes/match_engine.php';

if (!isset($_SESSION['admin_user'])) { header('Location: ../adminlogin.php'); exit(); }
$userId = $_SESSION['admin_user'];

// Determine role
$rs = $conn->prepare("SELECT role FROM admins WHERE user_name=? LIMIT 1");
$rs->bind_param('s', $userId); $rs->execute();
$role = $rs->get_result()->fetch_assoc()['role'] ?? 'admin'; $rs->close();
$isSuperAdmin = ($role === 'super_admin');

// ── POST handlers ─────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Confirm payment AND unlock the owner's PDF receipt in one step
    if (isset($_POST['confirm_payment'])) {
        $pid = (int)$_POST['payment_id'];
        $conn->query("UPDATE payments SET status='confirmed', confirmed_at=NOW(), download_allowed=1 WHERE id=$pid");
        // Ensure a verification code exists for station-side release
        $pr = $conn->query("SELECT match_alert_id, verification_code FROM payments WHERE id=$pid")->fetch_assoc();
        if ($pr) {
            if (empty($pr['verification_code'])) {
                $vcode = generateVerificationCode($conn);
                $conn->query("UPDATE payments SET verification_code='$vcode' WHERE id=$pid");
            }
            $conn->query("UPDATE match_alerts SET alert_status='paid' WHERE id=" . (int)$pr['match_alert_id']);
        }
    }
    // (Kept for compatibility) separate download approval
    if (isset($_POST['approve_download'])) {
        $pid = (int)$_POST['payment_id'];
        $conn->query("UPDATE payments SET download_allowed=1 WHERE id=$pid");
    }
    // Mark a document as collected (guarded: legacy documents table may not exist)
    if (isset($_POST['confirm_collection'])) {
        $aid = (int)$_POST['alert_id'];
        $conn->query("UPDATE match_alerts SET alert_status='collected' WHERE id=$aid");
        $ar = $conn->query("SELECT document_id FROM match_alerts WHERE id=$aid")->fetch_assoc();
        if ($ar) {
            $did = (int)$ar['document_id'];
            if ($did > 0) @$conn->query("UPDATE documents SET action='collected' WHERE id=$did");
        }
    }
    // Admin status actions for ANY station: paid / pending / collected
    if (isset($_POST['set_status'])) {
        $aid = (int)($_POST['alert_id'] ?? 0);
        $new_status = $_POST['set_status'];
        $allowed = ['paid', 'pending', 'collected'];
        if ($aid > 0 && in_array($new_status, $allowed, true)) {
            // Works across any station (no station scope)
            $conn->query("UPDATE match_alerts SET alert_status='$new_status', updated_at=NOW() WHERE id=$aid");
            $ar = $conn->query("SELECT document_id FROM match_alerts WHERE id=$aid")->fetch_assoc();
            $did = $ar ? (int)$ar['document_id'] : 0;
            if ($new_status === 'collected' && $did > 0) {
                @$conn->query("UPDATE documents SET action='collected' WHERE id=$did");
                $conn->query("INSERT INTO collection_log (document_id, alert_id, station, collected_by) SELECT $did, $aid, COALESCE(station,'Admin'), 'Admin (manual)' FROM match_alerts WHERE id=$aid");
            } elseif ($new_status === 'paid' && $did > 0) {
                @$conn->query("UPDATE documents SET payment_status='paid', payment_date=NOW() WHERE id=$did");
            }
        }
    }
    // Mark notifications read
    if (isset($_POST['mark_read'])) {
        $conn->query("UPDATE notifications SET is_read=1 WHERE target_role IN ('admin','all')");
        exit();
    }
}

// ── Stats ─────────────────────────────────────
$stationCount   = $conn->query("SELECT COUNT(*) c FROM admins WHERE role='station'")->fetch_assoc()['c'] ?? 0;
// Found docs live in the legacy tables the rest of the dashboard reads from
$foundCount     = $conn->query("
  SELECT ((SELECT COUNT(*) FROM national_ids WHERE user_action='Found')
        + (SELECT COUNT(*) FROM student_ids WHERE user_action='Found')
        + (SELECT COUNT(*) FROM driving_permits WHERE user_action='Found')) c")->fetch_assoc()['c'] ?? 0;
$lostCount      = $conn->query("SELECT COUNT(*) c FROM lost_reports")->fetch_assoc()['c'] ?? 0;
$matchCount     = $conn->query("SELECT COUNT(*) c FROM match_alerts WHERE alert_status IN ('new','admin_notified')")->fetch_assoc()['c'] ?? 0;
$pendingPay     = $conn->query("SELECT COUNT(*) c FROM payments WHERE status='initiated'")->fetch_assoc()['c'] ?? 0;
$confirmedPay   = $conn->query("SELECT COUNT(*) c FROM payments WHERE status='confirmed'")->fetch_assoc()['c'] ?? 0;
$collectedCount = $conn->query("SELECT COUNT(*) c FROM match_alerts WHERE alert_status IN ('collected','closed')")->fetch_assoc()['c'] ?? 0;
$unread         = getUnreadCount($conn, 'admin');
$pendingApproval = $conn->query("SELECT COUNT(*) c FROM payments WHERE status='confirmed' AND download_allowed=0")->fetch_assoc()['c'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($userId) ?> — Admin Dashboard | iRecovery</title>
  <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/1570/1570887.png">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

  <!-- ── Header ──────────────────────────────── -->
  <header class="dash-header">
    <a href="../index.php" class="hd-brand">
      <img src="https://cdn-icons-png.flaticon.com/512/1570/1570887.png" alt="iRecovery">
      iRecovery Admin
    </a>
    <div class="hd-right">
      <a href="?mark_read=1" style="color:rgba(255,255,255,.75);position:relative;text-decoration:none;" title="Notifications" id="bellLink">
        <i class="bi bi-bell-fill" style="font-size:1.1rem;"></i>
        <?php if ($unread > 0): ?><span style="position:absolute;top:-4px;right:-5px;background:var(--amber);color:#fff;border-radius:50px;padding:.05rem .35rem;font-size:.6rem;font-weight:700;"><?= $unread ?></span><?php endif; ?>
      </a>
      <div class="user-pill">
        <div class="u-av"><?= strtoupper(substr($userId, 0, 1)) ?></div>
        <div>
          <div class="u-nm"><?= htmlspecialchars($userId) ?></div>
          <div class="u-role"><?= $isSuperAdmin ? 'Super Admin' : 'Admin' ?></div>
        </div>
      </div>
      <a href="logout.php" class="btn-out"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
  </header>

  <div class="page">

    <!-- ── Stat cards ────────────────────────── -->
    <div class="stats">
      <div class="sc sc-blue">
        <div class="sc-ico" style="color:var(--blue)"><i class="bi bi-building"></i></div>
        <div class="sc-val"><?= $stationCount ?></div>
        <div class="sc-lbl">Stations<?php if ($isSuperAdmin): ?> <button onclick="openModal('addStation')" style="background:var(--blue);color:#fff;border:none;border-radius:4px;padding:.1rem .4rem;font-size:.65rem;cursor:pointer;margin-left:.3rem;">+</button><?php endif; ?></div>
      </div>
      <div class="sc sc-green">
        <div class="sc-ico" style="color:var(--green)"><i class="bi bi-cloud-upload"></i></div>
        <div class="sc-val"><?= $foundCount ?></div>
        <div class="sc-lbl">Found Docs</div>
      </div>
      <div class="sc sc-amber">
        <div class="sc-ico" style="color:var(--amber)"><i class="bi bi-flag"></i></div>
        <div class="sc-val"><?= $lostCount ?></div>
        <div class="sc-lbl">Lost Reports</div>
      </div>
      <div class="sc sc-navy">
        <div class="sc-ico" style="color:var(--navy2)"><i class="bi bi-lightning-charge"></i></div>
        <div class="sc-val"><?= $matchCount ?></div>
        <div class="sc-lbl">New Matches</div>
      </div>
      <div class="sc sc-amber">
        <div class="sc-ico" style="color:var(--amber)"><i class="bi bi-clock-history"></i></div>
        <div class="sc-val"><?= $pendingPay ?></div>
        <div class="sc-lbl">Pending Pay</div>
      </div>
      <div class="sc sc-green">
        <div class="sc-ico" style="color:var(--green)"><i class="bi bi-phone"></i></div>
        <div class="sc-val"><?= $confirmedPay ?></div>
        <div class="sc-lbl">Payments</div>
      </div>
      <div class="sc sc-teal">
        <div class="sc-ico" style="color:var(--teal)"><i class="bi bi-check2-circle"></i></div>
        <div class="sc-val"><?= $collectedCount ?></div>
        <div class="sc-lbl">Collected</div>
      </div>
      <?php if ($pendingApproval > 0): ?>
      <div class="sc sc-amber">
        <div class="sc-ico" style="color:var(--amber)"><i class="bi bi-unlock"></i></div>
        <div class="sc-val"><?= $pendingApproval ?></div>
        <div class="sc-lbl">Awaiting Approval</div>
      </div>
      <?php endif; ?>
    </div>

    <!-- ── Toolbar + Tabs ───────────────────── -->
    <div class="toolbar">
      <div class="search-box"><i class="bi bi-search"></i><input type="text" id="searchInput" placeholder="Search anything…" onkeyup="filterTable()"></div>
    </div>
    <div class="tabs" id="tabBar">
      <button class="tab-btn active" onclick="switchTab(this,'tMatches')"><i class="bi bi-lightning-charge"></i> Matches<?php if ($matchCount > 0): ?><span class="nb"><?= $matchCount ?></span><?php endif; ?></button>
      <button class="tab-btn" onclick="switchTab(this,'tPayments')"><i class="bi bi-phone"></i> Payments<?php if ($pendingApproval > 0): ?><span class="nb nb-amber"><?= $pendingApproval ?></span><?php endif; ?></button>
      <button class="tab-btn" onclick="switchTab(this,'tSearches')"><i class="bi bi-search"></i> Searches</button>
      <button class="tab-btn" onclick="switchTab(this,'tStations')"><i class="bi bi-building"></i> Stations</button>
      <button class="tab-btn" onclick="switchTab(this,'tFound')"><i class="bi bi-cloud-upload"></i> Found</button>
      <button class="tab-btn" onclick="switchTab(this,'tReported')"><i class="bi bi-flag"></i> Reported</button>
      <?php if ($isSuperAdmin): ?><button class="tab-btn" onclick="switchTab(this,'tNotifs')"><i class="bi bi-bell"></i> Notifications</button><?php endif; ?>
    </div>

    <!-- ── Tables ────────────────────────────── -->
    <div id="tMatches" class="tcard">
      <div class="table-responsive">
        <table class="dt">
          <thead><tr><th>#</th><th>Document</th><th>Owner</th><th>Station Holding</th><th>Reporter Contact</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
          <tbody>
          <?php
          $ma = $conn->query("
            SELECT ma.id, ma.alert_status, ma.created_at, ma.station,
                   lr.doc_type, lr.sur_name, lr.given_name, lr.reporter_name, lr.reporter_phone,
                   p.id as pay_id, p.status as pay_status, p.amount
            FROM match_alerts ma
            LEFT JOIN lost_reports lr ON lr.id = ma.lost_report_id
            LEFT JOIN payments p ON p.match_alert_id = ma.id
            ORDER BY ma.created_at DESC LIMIT 100");
          if ($ma && $ma->num_rows > 0):
            while ($r = $ma->fetch_assoc()):
              $badge = match($r['alert_status']) {
                'new'             => '<span class="bd bd-danger">New Match</span>',
                'admin_notified'  => '<span class="bd bd-amber">Admin Notified</span>',
                'owner_notified'  => '<span class="bd bd-blue">Owner Notified</span>',
                'pending'         => '<span class="bd bd-amber">Pending</span>',
                'paid'            => '<span class="bd bd-green">Paid</span>',
                'collected'       => '<span class="bd bd-grey">Collected</span>',
                'closed'          => '<span class="bd bd-grey">Closed</span>',
                default           => '<span class="bd bd-grey">' . htmlspecialchars($r['alert_status']) . '</span>'
              };
              echo "<tr>
                <td>{$r['id']}</td>
                <td><span class='bd bd-blue'>" . htmlspecialchars(ucwords(str_replace('_', ' ', $r['doc_type'] ?? ''))) . "</span></td>
                <td>" . htmlspecialchars($r['sur_name'] . ' ' . $r['given_name']) . "</td>
                <td>" . htmlspecialchars($r['station'] ?? '—') . "</td>
                <td>" . htmlspecialchars($r['reporter_name'] ?? '—') . "<br><a href='tel:" . htmlspecialchars($r['reporter_phone'] ?? '') . "'>" . htmlspecialchars($r['reporter_phone'] ?? '—') . "</a></td>
                <td>$badge</td>
                <td>" . htmlspecialchars($r['created_at']) . "</td>
                <td>";
              if ($r['alert_status'] === 'collected') {
                echo "<span style='color:var(--green);font-weight:600;'><i class='bi bi-check2-all'></i> Done</span>";
              } else {
                $aid = (int)$r['id'];
                if ($r['pay_id'] && $r['pay_status'] === 'initiated') {
                  echo "<form method='POST' class='d-inline'><input type='hidden' name='payment_id' value='{$r['pay_id']}'><button type='submit' name='confirm_payment' class='btn btn-success btn-sm mb-1'><i class='bi bi-check2'></i> Confirm Pay</button></form> ";
                }
                echo "<div class='act-grp'>
                  <form method='POST' class='d-inline'><input type='hidden' name='alert_id' value='$aid'><button type='submit' name='set_status' value='paid' class='btn btn-success btn-sm'><i class='bi bi-cash'></i> Paid</button></form>
                  <form method='POST' class='d-inline'><input type='hidden' name='alert_id' value='$aid'><button type='submit' name='set_status' value='pending' class='btn btn-warning btn-sm'><i class='bi bi-hourglass-split'></i> Pending</button></form>
                  <form method='POST' class='d-inline'><input type='hidden' name='alert_id' value='$aid'><button type='submit' name='set_status' value='collected' class='btn btn-teal btn-sm'><i class='bi bi-check2-circle'></i> Collected</button></form>
                </div>";
              }
              echo "</td></tr>";
            endwhile;
          else: echo "<tr><td colspan='8'><div class='empty'><i class='bi bi-lightning-charge ei'></i>No match alerts yet</div></td></tr>";
          endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div id="tPayments" class="tcard" style="display:none;">
      <div class="table-responsive">
        <table class="dt">
          <thead><tr><th>#</th><th>Payer</th><th>Phone</th><th>Doc ID / NIN</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
          <tbody>
          <?php
          $pm = $conn->query("SELECT * FROM payments ORDER BY initiated_at DESC LIMIT 100");
          if ($pm && $pm->num_rows > 0):
            while ($r = $pm->fetch_assoc()):
              $badge = $r['status'] === 'confirmed' ? '<span class="bd bd-green">Confirmed</span>' : ($r['status'] === 'initiated' ? '<span class="bd bd-amber">Pending</span>' : '<span class="bd bd-danger">Failed</span>');
              if ($r['status'] === 'confirmed' && !empty($r['verification_code'])) {
                $vc = rtrim(chunk_split($r['verification_code'], 4, '-'), '-');
                $badge .= '<div class="mt-1 small" style="color:var(--muted);">Code: <code>' . htmlspecialchars($vc) . '</code></div>';
              }
              echo "<tr><td>{$r['id']}</td><td>" . htmlspecialchars($r['payer_name'] ?? '—') . "</td><td><a href='tel:" . htmlspecialchars($r['payer_phone'] ?? '') . "'>" . htmlspecialchars($r['payer_phone'] ?? '—') . "</a></td><td>" . htmlspecialchars($r['id_number'] ?? '—') . "</td><td>UGX " . number_format((float)$r['amount']) . "</td><td>" . htmlspecialchars($r['payment_method']) . "</td><td>$badge</td><td>" . htmlspecialchars($r['initiated_at']) . "</td><td>";
              if ($r['status'] === 'initiated') echo "<form method='POST' class='d-inline'><input type='hidden' name='payment_id' value='{$r['id']}'><button class='btn btn-success' type='submit' name='confirm_payment'><i class='bi bi-check2'></i> Confirm</button></form>";
              echo "</td></tr>";
            endwhile;
          else: echo "<tr><td colspan='9'><div class='empty'><i class='bi bi-phone ei'></i>No payments yet</div></td></tr>";
          endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div id="tSearches" class="tcard" style="display:none;">
      <div class="table-responsive">
        <table class="dt">
          <thead><tr><th>#</th><th>Doc Type</th><th>Name Searched</th><th>ID / NIN</th><th>Searcher Phone</th><th>Result</th><th>Date</th></tr></thead>
          <tbody>
          <?php
          $sl = $conn->query("SELECT * FROM search_log ORDER BY searched_at DESC LIMIT 200");
          if ($sl && $sl->num_rows > 0):
            while ($r = $sl->fetch_assoc()):
              $rb = $r['result'] === 'matched' ? '<span class="bd bd-green">Matched</span>' : '<span class="bd bd-grey">Not Found</span>';
              echo "<tr><td>{$r['id']}</td><td>" . htmlspecialchars(ucwords(str_replace('_', ' ', $r['doc_type'] ?? ''))) . "</td><td>" . htmlspecialchars($r['search_name'] ?? '—') . "</td><td>" . htmlspecialchars($r['search_id_num'] ?? '—') . "</td><td><a href='tel:" . htmlspecialchars($r['searcher_phone'] ?? '') . "'>" . htmlspecialchars($r['searcher_phone'] ?? '—') . "</a></td><td>$rb</td><td>" . htmlspecialchars($r['searched_at']) . "</td></tr>";
            endwhile;
          else: echo "<tr><td colspan='7'><div class='empty'><i class='bi bi-search ei'></i>No searches logged yet</div></td></tr>";
          endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div id="tStations" class="tcard" style="display:none;">
      <div class="table-responsive">
        <table class="dt">
          <thead><tr><th>Station Name</th><th>Phone</th><th>NID</th><th>Student</th><th>Permits</th><th>District</th><th>Address</th><th>Entity</th><th>Registered</th></tr></thead>
          <tbody>
          <?php
          $users = $conn->query("
            SELECT a.user_name, a.number, a.district, a.address, a.type_of_entity, a.registered_at,
                   COUNT(DISTINCT n.national_id) as nid_docs,
                   COUNT(DISTINCT s.student_id)  as sid_docs,
                   COUNT(DISTINCT d.driver_id)   as did_docs
            FROM admins a
            LEFT JOIN national_ids    n ON n.reporter = a.user_name
            LEFT JOIN student_ids     s ON s.reporter = a.user_name
            LEFT JOIN driving_permits d ON d.reporter = a.user_name
            WHERE a.role = 'station'
            GROUP BY a.user_id ORDER BY a.user_id DESC");
          if ($users && $users->num_rows > 0):
            while ($row = $users->fetch_assoc()):
              echo "<tr>
                <td>" . htmlspecialchars($row['user_name']) . "</td>
                <td>" . htmlspecialchars($row['number']) . "</td>
                <td>{$row['nid_docs']}</td>
                <td>{$row['sid_docs']}</td>
                <td>{$row['did_docs']}</td>
                <td>" . htmlspecialchars($row['district']) . "</td>
                <td>" . htmlspecialchars($row['address']) . "</td>
                <td>" . htmlspecialchars($row['type_of_entity']) . "</td>
                <td>" . htmlspecialchars($row['registered_at']) . "</td>
              </tr>";
            endwhile;
          else: echo "<tr><td colspan='9'><div class='empty'><i class='bi bi-building ei'></i>No stations found</div></td></tr>";
          endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div id="tFound" class="tcard" style="display:none;">
      <div class="table-responsive">
        <table class="dt">
          <thead><tr><th>ID</th><th>Document Type</th><th>Owner Name</th><th>Status</th><th>Reported At</th><th>Action</th></tr></thead>
          <tbody>
          <?php
          $lostQuery = "
            SELECT national_id as id, 'National ID' AS document_type, sur_name as owner_name, given_name, CONCAT('../uploads/',front) as front, CONCAT('../uploads/',back) as back, user_action, date_found as reported_at FROM national_ids WHERE user_action='Found'
            UNION ALL
            SELECT student_id as id, 'Student ID' AS document_type, sur_name as owner_name, given_name, CONCAT('../uploads/',front) as front, CONCAT('../uploads/',back) as back, user_action, date_found as reported_at FROM student_ids WHERE user_action='Found'
            UNION ALL
            SELECT driver_id as id, 'Driving Permit' AS document_type, sur_name as owner_name, given_name, CONCAT('../uploads/',front) as front, CONCAT('../uploads/',back) as back, user_action, date_found as reported_at FROM driving_permits WHERE user_action='Found'";
          $lost = $conn->query($lostQuery);
          if ($lost && $lost->num_rows > 0):
            while ($row = $lost->fetch_assoc()):
              echo "<tr>
                <td>{$row['id']}</td>
                <td><span class='bd bd-blue'>{$row['document_type']}</span></td>
                <td>{$row['owner_name']} {$row['given_name']}</td>
                <td><span class='bd bd-green'>Found</span></td>
                <td>{$row['reported_at']}</td>
                <td><button class='btn btn-outline view-btn'
                  data-id='{$row['id']}' data-type='{$row['document_type']}' data-name='{$row['owner_name']}'
                  data-second-name='{$row['given_name']}' data-front-image='{$row['front']}' data-back-image='{$row['back']}'
                  data-status='{$row['user_action']}' data-date='{$row['reported_at']}'><i class='bi bi-eye'></i> View</button></td>
              </tr>";
            endwhile;
          else: echo "<tr><td colspan='6'><div class='empty'><i class='bi bi-file-earmark-excel ei'></i>No found documents</div></td></tr>";
          endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div id="tReported" class="tcard" style="display:none;">
      <div class="table-responsive">
        <table class="dt">
          <thead><tr><th>ID</th><th>Document Type</th><th>Owner Name</th><th>Status</th><th>Reported At</th><th>Action</th></tr></thead>
          <tbody>
          <?php
          $reported = $conn->query("
            SELECT national_id as id, 'National ID' AS document_type, sur_name as owner_name, given_name, CONCAT('../uploads/',front) as front, CONCAT('../uploads/',back) as back, user_action, date_found as reported_at FROM national_ids WHERE user_action='Reported'
            UNION ALL
            SELECT student_id as id, 'Student ID' AS document_type, sur_name as owner_name, given_name, CONCAT('../uploads/',front) as front, CONCAT('../uploads/',back) as back, user_action, date_found as reported_at FROM student_ids WHERE user_action='Reported'
            UNION ALL
            SELECT driver_id as id, 'Driving Permit' AS document_type, sur_name as owner_name, given_name, CONCAT('../uploads/',front) as front, CONCAT('../uploads/',back) as back, user_action, date_found as reported_at FROM driving_permits WHERE user_action='Reported'");
          if ($reported && $reported->num_rows > 0):
            while ($row = $reported->fetch_assoc()):
              echo "<tr>
                <td>{$row['id']}</td>
                <td><span class='bd bd-blue'>{$row['document_type']}</span></td>
                <td>{$row['owner_name']} {$row['given_name']}</td>
                <td><span class='bd bd-amber'>Reported</span></td>
                <td>{$row['reported_at']}</td>
                <td><button class='btn btn-outline view-btn'
                  data-id='{$row['id']}' data-type='{$row['document_type']}' data-name='{$row['owner_name']}'
                  data-second-name='{$row['given_name']}' data-front-image='{$row['front']}' data-back-image='{$row['back']}'
                  data-status='{$row['user_action']}' data-date='{$row['reported_at']}'><i class='bi bi-eye'></i> View</button></td>
              </tr>";
            endwhile;
          else: echo "<tr><td colspan='6'><div class='empty'><i class='bi bi-file-earmark-text ei'></i>No reported documents found</div></td></tr>";
          endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <?php if ($isSuperAdmin): ?>
    <div id="tNotifs" class="tcard" style="display:none;">
      <div class="table-responsive">
        <table class="dt">
          <thead><tr><th>#</th><th>Type</th><th>Message</th><th>Read</th><th>Date</th></tr></thead>
          <tbody>
          <?php
          $nf = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 100");
          if ($nf && $nf->num_rows > 0):
            while ($r = $nf->fetch_assoc()):
              $isRead = $r['is_read'] ? '<span class="bd bd-grey">Read</span>' : '<span class="bd bd-amber">New</span>';
              $typeLabel = match($r['type']) {
                'match_found'       => '<span class="bd bd-amber">Match Found</span>',
                'payment_confirmed' => '<span class="bd bd-green">Payment</span>',
                'doc_collected'     => '<span class="bd bd-blue">Collected</span>',
                'new_report'        => '<span class="bd bd-navy">New Report</span>',
                'new_upload'        => '<span class="bd bd-grey">New Upload</span>',
                default             => '<span class="bd bd-grey">' . htmlspecialchars($r['type']) . '</span>'
              };
              echo "<tr><td>{$r['id']}</td><td>$typeLabel</td><td>" . htmlspecialchars($r['message']) . "</td><td>$isRead</td><td>" . htmlspecialchars($r['created_at']) . "</td></tr>";
            endwhile;
          else: echo "<tr><td colspan='5'><div class='empty'><i class='bi bi-bell ei'></i>No notifications</div></td></tr>";
          endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

  </div><!-- /.page -->

  <!-- ── Add Station Modal (super admin) ─────── -->
  <?php if ($isSuperAdmin): ?>
  <div id="addStation" class="mo-bg">
    <div class="mo-box">
      <div class="mo-hd">
        <div class="mo-title"><i class="bi bi-building-add me-2" style="color:var(--blue);"></i>Add New Station</div>
        <button class="mo-close" onclick="closeModal('addStation')">&times;</button>
      </div>
      <form id="addUserForm" method="POST" action="user_saver.php">
        <div id="msg" class="mb-2"></div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="fl">Station Name</label>
            <input type="text" name="user_name" class="fc" placeholder="e.g. Voice of Lango FM" required>
          </div>
          <div class="col-md-6">
            <label class="fl">Password</label>
            <input type="password" name="password" class="fc" placeholder="••••••••" required>
          </div>
          <div class="col-md-6">
            <label class="fl">Phone Number</label>
            <input type="tel" name="number" class="fc" placeholder="07XXXXXXXX" required>
          </div>
          <div class="col-md-6">
            <label class="fl">Email <span style="color:var(--muted)">(optional)</span></label>
            <input type="email" name="email" class="fc" placeholder="station@domain.com">
          </div>
          <div class="col-md-6">
            <label class="fl">District</label>
            <input type="text" name="district" class="fc" required>
          </div>
          <div class="col-md-6">
            <label class="fl">Address</label>
            <input type="text" name="address" class="fc" required>
          </div>
          <div class="col-12">
            <label class="fl">Type of Entity</label>
            <select name="type_of_entity" class="fc" required>
              <option value="">Select entity type</option>
              <option value="Individual">Individual</option>
              <option value="Company">Company</option>
              <option value="Organization">Organization</option>
            </select>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block"><i class="bi bi-building-add"></i> Save Station</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <!-- ── Document Detail Modal ───────────────── -->
  <div id="docModal" class="mo-bg">
    <div class="mo-box">
      <div class="mo-hd">
        <div class="mo-title"><i class="bi bi-file-earmark-text me-2" style="color:var(--blue);"></i>Document Details</div>
        <button class="mo-close" onclick="closeModal('docModal')">&times;</button>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <div class="detail-row"><div class="detail-label">ID</div><div class="detail-value" id="popupId">—</div></div>
          <div class="detail-row"><div class="detail-label">Document Type</div><div class="detail-value" id="popupType">—</div></div>
          <div class="detail-row"><div class="detail-label">Surname</div><div class="detail-value" id="popupName">—</div></div>
        </div>
        <div class="col-md-6">
          <div class="detail-row"><div class="detail-label">Given Name</div><div class="detail-value" id="popupSecondName">—</div></div>
          <div class="detail-row"><div class="detail-label">Status</div><div class="detail-value"><span class="bd" id="popupStatusBadge">—</span></div></div>
          <div class="detail-row"><div class="detail-label">Date</div><div class="detail-value" id="popupDate">—</div></div>
        </div>
      </div>
      <hr style="border-color:var(--border);">
      <h6 class="mb-3" style="color:var(--muted);"><i class="bi bi-images me-2"></i>Document Images</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="doc-image-wrap"><div class="detail-label mb-2">Front</div><img id="popupFrontImage" src="" alt="Front" class="doc-image w-100"></div>
        </div>
        <div class="col-md-6">
          <div class="doc-image-wrap"><div class="detail-label mb-2">Back</div><img id="popupBackImage" src="" alt="Back" class="doc-image w-100"></div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Tab switching
    function switchTab(btn, id) {
      document.querySelectorAll('.tcard').forEach(c => c.style.display = 'none');
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.getElementById(id).style.display = 'block';
      btn.classList.add('active');
    }
    // Mark notifications read via AJAX (keeps user on page)
    document.getElementById('bellLink')?.addEventListener('click', e => {
      e.preventDefault();
      fetch('', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'mark_read=1' })
        .finally(() => window.location.href = '?mark_read=1');
    });
    // Table filtering
    function filterTable() {
      const q = document.getElementById('searchInput').value.toLowerCase();
      const active = document.querySelector('.tcard[style*="display: block"], .tcard:not([style*="none"])');
      const tbl = (document.getElementById('tMatches').style.display !== 'none') ? document.getElementById('tMatches')
                : document.querySelector('.tcard[style*="block"]') || document.getElementById('tMatches');
      tbl.querySelectorAll('tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
      });
    }
    // Modal helpers
    function openModal(id) { document.getElementById(id).classList.add('open'); document.body.style.overflow = 'hidden'; }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow = 'auto'; }
    window.addEventListener('click', e => {
      ['addStation', 'docModal'].forEach(id => {
        const el = document.getElementById(id);
        if (el && e.target === el) closeModal(id);
      });
    });
    // Document detail popup
    document.querySelectorAll('.view-btn').forEach(button => {
      button.addEventListener('click', () => {
        document.getElementById('popupId').textContent = button.dataset.id || '—';
        document.getElementById('popupType').textContent = button.dataset.type || '—';
        document.getElementById('popupName').textContent = button.dataset.name || '—';
        document.getElementById('popupSecondName').textContent = button.dataset.secondName || '—';
        document.getElementById('popupDate').textContent = button.dataset.date || '—';
        const status = button.dataset.status || '—';
        const sb = document.getElementById('popupStatusBadge');
        sb.textContent = status;
        sb.className = 'bd ' + (status === 'Found' ? 'bd-green' : 'bd-amber');
        document.getElementById('popupFrontImage').src = button.dataset.frontImage || '';
        document.getElementById('popupBackImage').src = button.dataset.backImage || '';
        openModal('docModal');
      });
    });
    // Add station AJAX
    const addForm = document.getElementById('addUserForm');
    if (addForm) {
      addForm.addEventListener('submit', function(e) {
        e.preventDefault();
        document.getElementById('msg').innerHTML = "<div class='bd bd-blue py-1'>Saving…</div>";
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'user_saver.php', true);
        xhr.send(new FormData(addForm));
        xhr.onreadystatechange = function() {
          if (this.readyState === 4) {
            document.getElementById('msg').innerHTML = this.responseText;
            if (this.responseText.includes('successfully')) {
              setTimeout(() => { closeModal('addStation'); location.reload(); }, 1500);
            }
          }
        };
      });
    }
  </script>
</body>
</html>

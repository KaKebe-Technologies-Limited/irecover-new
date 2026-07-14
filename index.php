<?php
// ─────────────────────────────────────────────
// Public Home Page
// ─────────────────────────────────────────────
session_start();
require_once 'db.php';

// Resolve current user identity
if (isset($_SESSION['station_user'])) {
    $userId   = $_SESSION['station_user'];
    $userRole = 'Station';
} elseif (isset($_SESSION['admin_user'])) {
    $userId   = $_SESSION['admin_user'];
    $userRole = 'Admin';
} else {
    $userId   = 'Public';
    $userRole = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/512/1570/1570887.png">

    <!-- Primary Meta Tags -->
    <title>iRecovery — Document Recovery Platform</title>
    <meta name="description" content="iRecovery helps you report, upload, and search for lost or found documents with a modern, intuitive interface.">

    <!-- Open Graph -->
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="https://id.faithfellows.online/">
    <meta property="og:title"       content="iRecovery — Modern Document Recovery Platform">
    <meta property="og:description" content="iRecovery helps you report, upload, and search for lost or found documents with a modern, intuitive interface.">
    <meta property="og:image"       content="https://id.faithfellows.online/img/bg.jpg">

    <!-- Twitter Card -->
    <meta property="twitter:card"        content="summary_large_image">
    <meta property="twitter:url"         content="https://id.faithfellows.online/">
    <meta property="twitter:title"       content="iRecovery — Modern Document Recovery Platform">
    <meta property="twitter:description" content="iRecovery helps you report, upload, and search for lost or found documents with a modern, intuitive interface.">
    <meta property="twitter:image"       content="https://id.faithfellows.online/img/bg.jpg">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- iRecovery CSS -->
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/home.css">
</head>
<body>

    <!-- ══════════════════════════════════════════
         TOP NAV
    ══════════════════════════════════════════ -->
    <nav class="top-nav" id="topNav">
        <a href="index.php" class="brand">
            <img src="https://cdn-icons-png.flaticon.com/512/1570/1570887.png" alt="iRecovery">
            iRecovery
        </a>

        <!-- Desktop links -->
        <ul class="nav-links" id="navLinks">
            <li><a href="#how-it-works"><i class="bi bi-info-circle me-1"></i>How It Works</a></li>
            <li><a href="#services"><i class="bi bi-grid me-1"></i>Services</a></li>
            <li><a href="get_receipt.php"><i class="bi bi-file-earmark-pdf me-1"></i>Get Receipt</a></li>
            <?php if ($userRole === 'Station'): ?>
                <li><a href="station/" class="nav-link-pill"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                <li><a href="station/logout.php" class="nav-link-out"><i class="bi bi-box-arrow-right me-1"></i>Logout</a></li>
            <?php elseif ($userRole === 'Admin'): ?>
                <li><a href="admin/" class="nav-link-pill"><i class="bi bi-shield-lock me-1"></i>Dashboard</a></li>
                <li><a href="admin/logout.php" class="nav-link-out"><i class="bi bi-box-arrow-right me-1"></i>Logout</a></li>
            <?php else: ?>
                <li><a href="login.php" class="nav-link-pill"><i class="bi bi-building me-1"></i>Station Login</a></li>
            <?php endif; ?>
        </ul>

        <!-- Hamburger button (mobile only) -->
        <button class="nav-burger" id="navBurger" aria-label="Open menu" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </nav>

    <!-- Mobile drawer -->
    <div class="nav-drawer" id="navDrawer" aria-hidden="true">
        <div class="nav-drawer-header">
            <div class="nav-drawer-brand">
                <img src="https://cdn-icons-png.flaticon.com/512/1570/1570887.png" alt="iRecovery">
                iRecovery
            </div>
            <button class="nav-drawer-close" id="navDrawerClose" aria-label="Close menu">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <ul class="nav-drawer-links">
            <li><a href="#how-it-works" class="drawer-link" onclick="closeDrawer()">
                <i class="bi bi-info-circle"></i> How It Works
            </a></li>
            <li><a href="#services" class="drawer-link" onclick="closeDrawer()">
                <i class="bi bi-grid"></i> Document Services
            </a></li>
            <li><a href="get_receipt.php" class="drawer-link">
                <i class="bi bi-file-earmark-pdf"></i> Get Receipt
            </a></li>
            <div class="drawer-divider"></div>
            <?php if ($userRole === 'Station'): ?>
                <li><a href="station/" class="drawer-link drawer-link-pill">
                    <i class="bi bi-speedometer2"></i> Station Dashboard
                </a></li>
                <li><a href="station/logout.php" class="drawer-link drawer-link-out">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a></li>
            <?php elseif ($userRole === 'Admin'): ?>
                <li><a href="admin/" class="drawer-link drawer-link-pill">
                    <i class="bi bi-shield-lock"></i> Admin Dashboard
                </a></li>
                <li><a href="admin/logout.php" class="drawer-link drawer-link-out">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a></li>
            <?php else: ?>
                <li><a href="login.php" class="drawer-link drawer-link-pill">
                    <i class="bi bi-building"></i> Station Login
                </a></li>
                <li><a href="adminlogin.php" class="drawer-link">
                    <i class="bi bi-shield-lock"></i> Admin Login
                </a></li>
            <?php endif; ?>
        </ul>
        <div class="nav-drawer-footer">
            &copy; <?= date('Y') ?> iRecovery
        </div>
    </div>

    <!-- Backdrop -->
    <div class="nav-backdrop" id="navBackdrop" onclick="closeDrawer()"></div>

    <!-- ══════════════════════════════════════════
         HERO
    ══════════════════════════════════════════ -->
    <section class="hero">
        <div class="hero-inner container">
            <div class="hero-eyebrow">
                <i class="bi bi-patch-check-fill"></i> Uganda's Document Recovery Platform
            </div>
            <h1 class="hero-title">
                Reuniting People with<br>
                <span>Their Lost Documents</span>
            </h1>
            <p class="hero-subtitle">
                iRecovery makes it simple to report lost documents or upload found ones
                with our secure, community-powered platform.
            </p>
            <a href="#services" class="hero-cta">
                <i class="bi bi-arrow-down-circle"></i> Get Started
            </a>
            <div class="hero-user-chip mt-3">
                <i class="bi bi-person-circle"></i>
                <?= htmlspecialchars($userId) ?>
                <?php if ($userRole): ?>
                    &nbsp;&mdash;&nbsp;<span style="opacity:0.7;font-size:0.8em"><?= htmlspecialchars($userRole) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
         HOW IT WORKS
    ══════════════════════════════════════════ -->
    <section class="container py-5" id="how-it-works">
        <div class="text-center mb-4">
            <span class="section-label">Simple Process</span>
            <h2 class="section-title">How It Works</h2>
        </div>
        <div class="row g-4 steps-row">
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-num">1</div>
                    <h5>Report or Upload</h5>
                    <p>Start by reporting a lost document or uploading a found one to the system.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-num">2</div>
                    <h5>Smart Matching</h5>
                    <p>Our system automatically matches lost reports with uploaded found documents.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-num">3</div>
                    <h5>Collect Your Document</h5>
                    <p>Get notified, make payment, receive a receipt, then collect from the station.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
         DOCUMENT SERVICES — Tabbed layout
    ══════════════════════════════════════════ -->
    <section class="py-5" id="services" style="background:#f8fafc;">
        <div class="container">

            <div class="text-center mb-4">
                <span class="section-label">What You Can Do</span>
                <h2 class="section-title">Document Services</h2>
                <p class="text-muted mt-2" style="font-size:.93rem;max-width:560px;margin:0 auto;">
                    Select the service you need below. Each form guides you step by step.
                </p>
            </div>

            <!-- Service tab bar -->
            <div class="svc-tabs" role="tablist">
                <button class="svc-tab active" onclick="switchSvc(this,'svcFound')" role="tab" aria-selected="true">
                    <i class="bi bi-cloud-upload"></i>
                    <span>Upload Found Document</span>
                </button>
                <button class="svc-tab" onclick="switchSvc(this,'svcLost')" role="tab" aria-selected="false">
                    <i class="bi bi-flag"></i>
                    <span>Report Lost Document</span>
                </button>
                <button class="svc-tab" onclick="switchSvc(this,'svcSearch')" role="tab" aria-selected="false">
                    <i class="bi bi-search"></i>
                    <span>Search for Document</span>
                </button>
            </div>

            <!-- ── Tab: Upload Found ──────────────────────── -->
            <div id="svcFound" class="svc-panel" style="display:block;">
                <div class="svc-card">
                    <div class="svc-card-banner svc-banner-found">
                        <i class="bi bi-cloud-upload"></i>
                        <div>
                            <div class="svc-banner-title">Upload a Found Document</div>
                            <div class="svc-banner-sub">Found someone's document? Fill in the details and upload photos so the owner can claim it.</div>
                        </div>
                    </div>
                    <div class="svc-card-body">
                        <form action="submit_id.php" method="POST" enctype="multipart/form-data" id="formFound">
                            <input type="hidden" name="reporter" value="<?= htmlspecialchars($userId) ?>">

                            <!-- Step 1 — Select type -->
                            <div class="svc-step">
                                <div class="svc-step-num">1</div>
                                <div class="svc-step-content">
                                    <div class="svc-step-label">Select Document Type</div>
                                    <select id="docTypeFound" name="doc_type" class="svc-select" required onchange="updateSvcFields(this,'Found')">
                                        <option value="" selected disabled>Choose document type&hellip;</option>
                                        <option value="national_id">National ID</option>
                                        <option value="driving_permit">Driving Permit</option>
                                        <option value="passport">Passport</option>
                                        <option value="student_id">Student ID</option>
                                        <option value="academic_document">Academic Document</option>
                                        <option value="land_title">Land Title</option>
                                        <option value="birth_certificate">Birth Certificate</option>
                                        <option value="other">Other Document</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Step 2 — Dynamic fields -->
                            <div id="svcFieldsFound" class="svc-fields-wrap" style="display:none;">
                                <div class="svc-step">
                                    <div class="svc-step-num">2</div>
                                    <div class="svc-step-content">
                                        <div class="svc-step-label">Document Owner Details</div>
                                        <div class="svc-fields-grid" id="dynamicFieldsFound"></div>
                                    </div>
                                </div>

                                <!-- Step 3 — Photos -->
                                <div class="svc-step">
                                    <div class="svc-step-num">3</div>
                                    <div class="svc-step-content">
                                        <div class="svc-step-label">Upload Document Photos</div>
                                        <div class="svc-fields-grid">
                                            <div class="svc-field">
                                                <label class="svc-label">Front Side <span class="req">*</span></label>
                                                <input type="file" name="front_img" class="svc-input" accept="image/*" required>
                                                <small class="svc-hint">Clear photo of the front</small>
                                            </div>
                                            <div class="svc-field">
                                                <label class="svc-label">Back Side <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                                                <input type="file" name="back_img" class="svc-input" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="svc-submit-row">
                                    <button type="submit" class="svc-btn svc-btn-found">
                                        <i class="bi bi-cloud-upload"></i> Submit Found Document
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <!-- ── Tab: Report Lost ───────────────────────── -->
            <div id="svcLost" class="svc-panel" style="display:none;">
                <div class="svc-card">
                    <div class="svc-card-banner svc-banner-lost">
                        <i class="bi bi-flag"></i>
                        <div>
                            <div class="svc-banner-title">Report a Lost Document</div>
                            <div class="svc-banner-sub">Lost your document? File a report with a police letter so we can match it when it's found.</div>
                        </div>
                    </div>
                    <div class="svc-card-body">
                        <form action="report.php" method="POST" enctype="multipart/form-data" id="formLost">
                            <input type="hidden" name="reporter" value="<?= htmlspecialchars($userId) ?>">

                            <div class="svc-step">
                                <div class="svc-step-num">1</div>
                                <div class="svc-step-content">
                                    <div class="svc-step-label">Select Document Type</div>
                                    <select id="docTypeLost" name="doc_type" class="svc-select" required onchange="updateSvcFields(this,'Lost')">
                                        <option value="" selected disabled>Choose document type&hellip;</option>
                                        <option value="national_id">National ID</option>
                                        <option value="driving_permit">Driving Permit</option>
                                        <option value="passport">Passport</option>
                                        <option value="student_id">Student ID</option>
                                        <option value="academic_document">Academic Document</option>
                                        <option value="land_title">Land Title</option>
                                        <option value="birth_certificate">Birth Certificate</option>
                                        <option value="other">Other Document</option>
                                    </select>
                                </div>
                            </div>

                            <div id="svcFieldsLost" class="svc-fields-wrap" style="display:none;">
                                <div class="svc-step">
                                    <div class="svc-step-num">2</div>
                                    <div class="svc-step-content">
                                        <div class="svc-step-label">Document Details</div>
                                        <div class="svc-fields-grid" id="dynamicFieldsLost"></div>
                                    </div>
                                </div>

                                <div class="svc-step">
                                    <div class="svc-step-num">3</div>
                                    <div class="svc-step-content">
                                        <div class="svc-step-label">Your Contact Details</div>
                                        <div class="svc-fields-grid">
                                            <div class="svc-field">
                                                <label class="svc-label">Your Full Name <span class="req">*</span></label>
                                                <input type="text" name="reporter_name" class="svc-input" placeholder="Enter your name" required>
                                            </div>
                                            <div class="svc-field">
                                                <label class="svc-label">Your Phone <span class="req">*</span></label>
                                                <input type="tel" name="reporter_phone" class="svc-input" placeholder="07XXXXXXXX" required>
                                            </div>
                                            <div class="svc-field">
                                                <label class="svc-label">Your Email <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                                                <input type="email" name="reporter_email" class="svc-input" placeholder="you@example.com">
                                            </div>
                                            <div class="svc-field">
                                                <label class="svc-label">Police Letter / OB Number <span class="req">*</span></label>
                                                <input type="file" name="police_letter" class="svc-input" accept="image/*,.pdf" required>
                                                <small class="svc-hint">Upload a photo of the police OB slip</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="svc-submit-row">
                                    <button type="submit" class="svc-btn svc-btn-lost">
                                        <i class="bi bi-flag"></i> Submit Lost Report
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ── Tab: Search ────────────────────────────── -->
            <div id="svcSearch" class="svc-panel" style="display:none;">
                <div class="svc-card">
                    <div class="svc-card-banner svc-banner-search">
                        <i class="bi bi-search"></i>
                        <div>
                            <div class="svc-banner-title">Search for Your Document</div>
                            <div class="svc-banner-sub">Enter your details to check if your document has been found and uploaded by a partner station.</div>
                        </div>
                    </div>
                    <div class="svc-card-body">
                        <form action="search_id.php" method="POST" id="formSearch">

                            <div class="svc-step">
                                <div class="svc-step-num">1</div>
                                <div class="svc-step-content">
                                    <div class="svc-step-label">Select Document Type</div>
                                    <select id="docTypeSearch" name="doc_type" class="svc-select" required onchange="updateSvcFields(this,'Search')">
                                        <option value="" selected disabled>Choose document type&hellip;</option>
                                        <option value="national_id">National ID</option>
                                        <option value="driving_permit">Driving Permit</option>
                                        <option value="passport">Passport</option>
                                        <option value="student_id">Student ID</option>
                                        <option value="academic_document">Academic Document</option>
                                        <option value="land_title">Land Title</option>
                                        <option value="birth_certificate">Birth Certificate</option>
                                        <option value="other">Other Document</option>
                                    </select>
                                </div>
                            </div>

                            <div id="svcFieldsSearch" class="svc-fields-wrap" style="display:none;">
                                <div class="svc-step">
                                    <div class="svc-step-num">2</div>
                                    <div class="svc-step-content">
                                        <div class="svc-step-label">Enter Search Details</div>
                                        <p class="svc-hint mb-2">Search by ID / NIN number <strong>or</strong> your name + date of birth.</p>
                                        <div class="svc-fields-grid" id="dynamicFieldsSearch"></div>
                                    </div>
                                </div>

                                <div class="svc-step">
                                    <div class="svc-step-num">3</div>
                                    <div class="svc-step-content">
                                        <div class="svc-step-label">Your Contact <span style="color:var(--muted);font-weight:400;">(optional — helps us reach you)</span></div>
                                        <div class="svc-fields-grid">
                                            <div class="svc-field">
                                                <label class="svc-label">Your Phone Number</label>
                                                <input type="tel" name="searcher_phone" class="svc-input" placeholder="07XXXXXXXX">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="svc-submit-row">
                                    <button type="submit" class="svc-btn svc-btn-search">
                                        <i class="bi bi-search"></i> Search Documents
                                    </button>
                                    <a href="get_receipt.php" class="svc-btn-secondary">
                                        <i class="bi bi-file-earmark-pdf"></i> Already paid? Download Receipt
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ══════════════════════════════════════════
         FEATURES
    ══════════════════════════════════════════ -->
    <section class="container py-5">
        <div class="text-center mb-4">
            <span class="section-label">Why iRecovery</span>
            <h2 class="section-title">Built for Uganda</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-tile">
                    <i class="bi bi-shield-lock fi-icon"></i>
                    <h5>Secure Platform</h5>
                    <p>Your data is protected with industry-standard security measures.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-tile">
                    <i class="bi bi-lightning-charge fi-icon"></i>
                    <h5>Fast Matching</h5>
                    <p>Our system quickly matches lost documents with found ones in the database.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-tile">
                    <i class="bi bi-broadcast fi-icon"></i>
                    <h5>Station Network</h5>
                    <p>A growing network of partner stations across Uganda holding found documents.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
         FOOTER
    ══════════════════════════════════════════ -->
    <footer class="home-footer">
        <div class="container">
            <div class="row align-items-center gy-3">
                <div class="col-md-5">
                    <div class="footer-brand">
                        <img src="https://cdn-icons-png.flaticon.com/512/1570/1570887.png"
                             alt="iRecovery" style="height:28px;margin-right:8px;vertical-align:middle;">
                        iRecovery
                    </div>
                    <p>Helping Ugandans recover lost documents since 2024.</p>
                </div>
                <div class="col-md-4 text-md-center">
                    <p>
                        <a href="login.php">Station Login</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                        <a href="adminlogin.php">Admin Login</a>
                    </p>
                </div>
                <div class="col-md-3 text-md-end">
                    <div class="footer-socials">
                        <a href="https://www.facebook.com/kakebetech/" target="_blank" rel="noopener" aria-label="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://x.com/kakebetech/" target="_blank" rel="noopener" aria-label="Twitter">
                            <i class="bi bi-twitter-x"></i>
                        </a>
                        <a href="https://www.linkedin.com/company/kakebetech/" target="_blank" rel="noopener" aria-label="LinkedIn">
                            <i class="bi bi-linkedin"></i>
                        </a>
                    </div>
                </div>
            </div>
            <hr style="border-color:rgba(255,255,255,0.1);margin:1.5rem 0;">
            <p class="text-center" style="font-size:0.82rem;">
                &copy; <?= date('Y') ?> All Rights Reserved. Powered by
                <a href="https://kakebe.tech" target="_blank" rel="noopener">Kakebe Technologies Limited</a>
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // ── Initialise the three forms ──────────────────────────────────────
        ['Found', 'Lost', 'Search'].forEach(function (formType) {
            const sel = document.getElementById('docType' + formType);
            if (sel) {
                if (sel.value) updateFormFields(sel, formType);
                sel.addEventListener('change', function () {
                    updateFormFields(this, formType);
                });
            }
        });

        // ── Master dispatcher ───────────────────────────────────────────────
        function updateFormFields(selectEl, formType) {
            const docType  = selectEl.value;
            const dynWrap  = document.getElementById('dynamicFields' + formType);
            dynWrap.innerHTML = '';

            if (!docType) {
                // Hide contextual extras when nothing is selected
                if (formType === 'Lost')   { document.getElementById('reporterFieldsLost').style.display   = 'none'; }
                if (formType === 'Search') { document.getElementById('searcherPhoneWrap').style.display    = 'none'; }
                return;
            }

            const builders = {
                national_id:       buildNationalID,
                driving_permit:    buildDrivingPermit,
                passport:          buildPassport,
                student_id:        buildStudentID,
                academic_document: buildAcademicDocument,
                land_title:        buildLandTitle,
                birth_certificate: buildBirthCertificate,
                other:             buildOther,
            };

            if (builders[docType]) builders[docType](dynWrap, formType);

            // Append file upload + submit for Found/Lost; plain submit for Search
            if (formType === 'Search') {
                addSearchSubmit(dynWrap);
                document.getElementById('searcherPhoneWrap').style.display = 'block';
            } else {
                addFileUploadAndSubmit(dynWrap, formType);
                if (formType === 'Lost') {
                    document.getElementById('reporterFieldsLost').style.display = 'block';
                }
            }
        }

        // ── Shared helper: build one labelled text/date/select input ────────
        function field(name, label, type, attrs) {
            attrs = attrs || '';
            if (type === 'date') {
                return `<div class="mb-3"><label class="form-label">${label}</label><input type="date" name="${name}" class="form-control" required ${attrs}></div>`;
            }
            if (type === 'number') {
                return `<div class="mb-3"><label class="form-label">${label}</label><input type="number" name="${name}" class="form-control" required ${attrs}></div>`;
            }
            if (type === 'gender') {
                return `<div class="mb-3"><label class="form-label">${label}</label><select name="${name}" class="form-select" required><option value="" selected disabled>Select gender</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>`;
            }
            return `<div class="mb-3"><label class="form-label">${label}</label><input type="text" name="${name}" class="form-control" required ${attrs}></div>`;
        }

        // ── national_id ─────────────────────────────────────────────────────
        function buildNationalID(c) {
            c.insertAdjacentHTML('beforeend',
                field('surName',  'Surname',     'text') +
                field('givenName','Given Name',  'text') +
                field('dob',      'Date of Birth','date') +
                field('id_number','NIN Number',  'text', 'placeholder="CM…"') +
                field('gender',   'Gender',      'gender')
            );
        }

        // ── driving_permit ──────────────────────────────────────────────────
        function buildDrivingPermit(c) {
            c.insertAdjacentHTML('beforeend',
                field('surName',  'Surname',       'text') +
                field('givenName','Given Name',    'text') +
                field('dob',      'Date of Birth', 'date') +
                field('id_number','Permit Number', 'text') +
                field('extra1',   'NIN Number',    'text', 'placeholder="CM…"')
            );
        }

        // ── passport ────────────────────────────────────────────────────────
        function buildPassport(c) {
            c.insertAdjacentHTML('beforeend',
                field('surName',  'Surname',         'text') +
                field('givenName','Given Name',      'text') +
                field('dob',      'Date of Birth',   'date') +
                field('id_number','Passport Number', 'text') +
                field('extra1',   'Nationality',     'text')
            );
        }

        // ── student_id ──────────────────────────────────────────────────────
        function buildStudentID(c) {
            c.insertAdjacentHTML('beforeend',
                field('surName',  'Surname',              'text') +
                field('givenName','Given Name',           'text') +
                field('id_number','Student / Reg Number', 'text') +
                field('extra1',   'Course',               'text') +
                field('dob',      'Date Issued',          'date') +
                field('extra2',   'School / Institution', 'text')
            );
        }

        // ── academic_document ───────────────────────────────────────────────
        function buildAcademicDocument(c) {
            c.insertAdjacentHTML('beforeend',
                field('surName',  'Surname',            'text') +
                field('givenName','Given Name',         'text') +
                field('id_number','Certificate Number', 'text') +
                field('extra1',   'Institution',        'text') +
                field('extra2',   'Course Title',       'text') +
                field('extra3',   'Graduation Year',    'number', 'min="1900" max="2099"')
            );
        }

        // ── land_title ──────────────────────────────────────────────────────
        function buildLandTitle(c) {
            c.insertAdjacentHTML('beforeend',
                field('surName',  'Surname',           'text') +
                field('givenName','Given Name',        'text') +
                field('id_number','Plot / Title Number','text') +
                field('extra1',   'District',          'text') +
                field('extra2',   'Land Reference',    'text')
            );
        }

        // ── birth_certificate ───────────────────────────────────────────────
        function buildBirthCertificate(c) {
            c.insertAdjacentHTML('beforeend',
                field('surName',  'Surname',                          'text') +
                field('givenName','Given Name',                       'text') +
                field('dob',      'Date of Birth',                    'date') +
                field('id_number','Certificate Registration Number',  'text') +
                field('extra1',   'District of Birth',                'text')
            );
        }

        // ── other ────────────────────────────────────────────────────────────
        function buildOther(c) {
            c.insertAdjacentHTML('beforeend',
                field('surName',  'Surname',                   'text') +
                field('givenName','Given Name',                'text') +
                field('id_number','Document Reference Number', 'text') +
                field('extra1',   'Description',              'text')
            );
        }

        // ── File upload + submit (Found / Lost forms) ───────────────────────
        function addFileUploadAndSubmit(container, formType) {
            container.insertAdjacentHTML('beforeend', `
                <div class="mb-3">
                    <label class="form-label">Front Side of Document</label>
                    <input type="file" name="front_img" class="form-control" accept="image/*" required>
                    <small class="text-muted">Clear photo of the front side</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Back Side <span class="text-muted">(if applicable)</span></label>
                    <input type="file" name="back_img" class="form-control" accept="image/*">
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-send-fill"></i> Submit
                    </button>
                </div>
            `);
        }

        // ── Search submit only ───────────────────────────────────────────────
        function addSearchSubmit(container) {
            container.insertAdjacentHTML('beforeend', `
                <div class="d-grid">
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-search"></i> Search Documents
                    </button>
                </div>
            `);
        }

    }); // end DOMContentLoaded
    </script>

</body>
</html>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    /* ── Mobile nav drawer ───────────────────── */
    var burger  = document.getElementById('navBurger');
    var drawer  = document.getElementById('navDrawer');
    var backdrop= document.getElementById('navBackdrop');
    var closeBtn= document.getElementById('navDrawerClose');

    function openDrawer() {
        drawer.classList.add('open');
        backdrop.classList.add('show');
        document.body.style.overflow = 'hidden';
        burger.setAttribute('aria-expanded','true');
        burger.classList.add('open');
    }
    function closeDrawer() {
        drawer.classList.remove('open');
        backdrop.classList.remove('show');
        document.body.style.overflow = '';
        burger.setAttribute('aria-expanded','false');
        burger.classList.remove('open');
    }

    burger.addEventListener('click', function() {
        drawer.classList.contains('open') ? closeDrawer() : openDrawer();
    });
    closeBtn.addEventListener('click', closeDrawer);
    /* ── Service tab switcher ─────────────────────────── */
    function switchSvc(btn, panelId) {
        document.querySelectorAll('.svc-tab').forEach(function(t){ t.classList.remove('active'); });
        document.querySelectorAll('.svc-panel').forEach(function(p){ p.style.display='none'; });
        btn.classList.add('active');
        var el = document.getElementById(panelId);
        el.style.display = 'block';
        el.style.animation = 'svcFadeIn .35s ease';
    }

    /* ── Field builder triggered by each select's onchange ── */
    function updateSvcFields(sel, formType) {
        var docType = sel.value;
        var wrap = document.getElementById('svcFields' + formType);
        var grid = document.getElementById('dynamicFields' + formType);
        grid.innerHTML = '';
        if (!docType) { wrap.style.display = 'none'; return; }
        var map = {
            national_id:       buildNID,
            driving_permit:    buildDP,
            passport:          buildPP,
            student_id:        buildSID,
            academic_document: buildAD,
            land_title:        buildLT,
            birth_certificate: buildBC,
            other:             buildOT
        };
        if (map[docType]) map[docType](grid);
        wrap.style.display = 'block';
    }

    /* ── Shared field helper ─────────────────────────── */
    function f(name, label, type, extra) {
        extra = extra || '';
        var inp;
        if (type === 'date') {
            inp = '<input type="date" name="'+name+'" class="svc-input" required '+extra+'>';
        } else if (type === 'number') {
            inp = '<input type="number" name="'+name+'" class="svc-input" required '+extra+'>';
        } else if (type === 'gender') {
            inp = '<select name="'+name+'" class="svc-input" required>' +
                  '<option value="" disabled selected>Select gender</option>' +
                  '<option value="male">Male</option><option value="female">Female</option>' +
                  '<option value="other">Other</option></select>';
        } else {
            inp = '<input type="text" name="'+name+'" class="svc-input" required '+extra+'>';
        }
        return '<div class="svc-field"><label class="svc-label">'+label+' <span class="req">*</span></label>'+inp+'</div>';
    }

    function buildNID(c) {
        c.innerHTML = f('surName','Surname','text') + f('givenName','Given Name','text') +
            f('dob','Date of Birth','date') + f('id_number','NIN Number','text','placeholder="CM..."') +
            f('gender','Gender','gender');
    }
    function buildDP(c) {
        c.innerHTML = f('surName','Surname','text') + f('givenName','Given Name','text') +
            f('dob','Date of Birth','date') + f('id_number','Permit Number','text') +
            f('extra1','NIN Number','text','placeholder="CM..."');
    }
    function buildPP(c) {
        c.innerHTML = f('surName','Surname','text') + f('givenName','Given Name','text') +
            f('dob','Date of Birth','date') + f('id_number','Passport Number','text') +
            f('extra1','Nationality','text');
    }
    function buildSID(c) {
        c.innerHTML = f('surName','Surname','text') + f('givenName','Given Name','text') +
            f('id_number','Student / Reg Number','text') + f('extra1','Course','text') +
            f('dob','Date Issued','date') + f('extra2','School / Institution','text');
    }
    function buildAD(c) {
        c.innerHTML = f('surName','Surname','text') + f('givenName','Given Name','text') +
            f('id_number','Certificate Number','text') + f('extra1','Institution','text') +
            f('extra2','Course Title','text') + f('extra3','Graduation Year','number','min="1900" max="2099"');
    }
    function buildLT(c) {
        c.innerHTML = f('surName','Surname','text') + f('givenName','Given Name','text') +
            f('id_number','Plot / Title Number','text') + f('extra1','District','text') +
            f('extra2','Land Reference','text');
    }
    function buildBC(c) {
        c.innerHTML = f('surName','Surname','text') + f('givenName','Given Name','text') +
            f('dob','Date of Birth','date') + f('id_number','Certificate Reg Number','text') +
            f('extra1','District of Birth','text');
    }
    function buildOT(c) {
        c.innerHTML = f('surName','Surname','text') + f('givenName','Given Name','text') +
            f('id_number','Document Reference Number','text') + f('extra1','Description','text');
    }
    </script>

</body>
</html>

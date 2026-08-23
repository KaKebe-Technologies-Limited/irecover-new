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
    <title>iRecovery Uganda — Recover Your Lost National ID, Passport &amp; Documents Fast</title>
    <meta name="description" content="Lost your National ID, passport, driving permit, student ID or other document in Uganda? iRecovery reunites you with found documents fast — report, search, and securely pay to recover your document from partner stations nationwide.">
    <meta name="keywords" content="lost national ID Uganda, recover national ID Uganda, find lost NIN, lost passport Uganda, lost driving permit Uganda, NIRA ID recovery, found documents Uganda, document recovery platform, iRecovery Uganda">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Kakebe Technologies Limited">
    <link rel="canonical" href="https://irecover.site/">

    <!-- Open Graph -->
    <meta property="og:type"        content="website">
    <meta property="og:locale"      content="en_UG">
    <meta property="og:site_name"   content="iRecovery Uganda">
    <meta property="og:url"         content="https://irecover.site/">
    <meta property="og:title"       content="iRecovery Uganda — Recover Your Lost National ID, Passport &amp; Documents Fast">
    <meta property="og:description" content="Lost your National ID, passport, driving permit, student ID or other document in Uganda? Search our database of found documents and recover yours securely, fast.">
    <meta property="og:image"       content="https://irecover.site/img/bg.jpg">

    <!-- Twitter Card -->
    <meta property="twitter:card"        content="summary_large_image">
    <meta property="twitter:url"         content="https://irecover.site/">
    <meta property="twitter:title"       content="iRecovery Uganda — Recover Your Lost National ID, Passport &amp; Documents Fast">
    <meta property="twitter:description" content="Uganda's platform for reporting, finding, and securely recovering lost National IDs, passports, permits and more.">
    <meta property="twitter:image"       content="https://irecover.site/img/bg.jpg">

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "iRecovery Uganda",
      "url": "https://irecover.site/",
      "logo": "https://cdn-icons-png.flaticon.com/512/1570/1570887.png",
      "description": "iRecovery helps people across Uganda report, search for, and recover lost documents such as National IDs, passports, driving permits and student IDs through partner stations nationwide.",
      "areaServed": { "@type": "Country", "name": "Uganda" },
      "sameAs": ["https://kakebe.tech/"]
    }
    </script>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "iRecovery Uganda",
      "url": "https://irecover.site/",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "https://irecover.site/search_id.php?id_number={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- iRecovery CSS -->
    <link rel="stylesheet" href="assets/css/variables.css?v=<?= @filemtime(__DIR__.'/assets/css/variables.css') ?>">
    <link rel="stylesheet" href="assets/css/base.css?v=<?= @filemtime(__DIR__.'/assets/css/base.css') ?>">
    <link rel="stylesheet" href="assets/css/home.css?v=<?= @filemtime(__DIR__.'/assets/css/home.css') ?>">
    <link rel="stylesheet" href="assets/css/wizard.css?v=<?= @filemtime(__DIR__.'/assets/css/wizard.css') ?>">
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

            <!-- Service selector -->
            <div class="svc-select-wrap">
                <i class="bi bi-list-check svc-select-icon"></i>
                <select id="svcSelect" class="svc-select-tabs" onchange="switchSvcBySelect(this)" aria-label="Choose a service">
                    <option value="" selected disabled>Select a service</option>
                    <option value="svcFound">Upload Found Document</option>
                    <option value="svcLost">Report Lost Document</option>
                    <option value="svcSearch">Search for Document</option>
                </select>
                <i class="bi bi-chevron-down svc-select-caret"></i>
            </div>

            <!-- ── Tab: Upload Found ──────────────────────── -->
            <div id="svcFound" class="svc-panel" style="display:none;">
                <div class="svc-card">
                    <div class="svc-card-banner svc-banner-found">
                        <i class="bi bi-cloud-upload"></i>
                        <div>
                            <div class="svc-banner-title">Upload a Found Document</div>
                            <div class="svc-banner-sub">Found someone's document? Answer a few quick questions and upload photos so the owner can claim it.</div>
                        </div>
                    </div>
                    <div class="svc-card-body tf-card-body">
                        <form action="submit_id.php" method="POST" enctype="multipart/form-data" id="formFound" class="tf-form" novalidate>
                            <input type="hidden" name="reporter" value="<?= htmlspecialchars($userId) ?>">
                            <div class="tf-collected" hidden></div>
                            <div class="tf-wizard" data-accent="found" data-form="Found">
                                <div class="tf-progress"><div class="tf-progress-bar" id="tfBarFound"></div></div>
                                <div class="tf-stage" id="tfStageFound"></div>
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
                            <div class="svc-banner-sub">Lost your document? Answer a few quick questions and attach a police letter so we can match it when it's found.</div>
                        </div>
                    </div>
                    <div class="svc-card-body tf-card-body">
                        <form action="report.php" method="POST" enctype="multipart/form-data" id="formLost" class="tf-form" novalidate>
                            <input type="hidden" name="reporter" value="<?= htmlspecialchars($userId) ?>">
                            <div class="tf-collected" hidden></div>
                            <div class="tf-wizard" data-accent="lost" data-form="Lost">
                                <div class="tf-progress"><div class="tf-progress-bar" id="tfBarLost"></div></div>
                                <div class="tf-stage" id="tfStageLost"></div>
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
                            <div class="svc-banner-sub">Answer a few quick questions to check if your document has been found and uploaded by a partner station.</div>
                        </div>
                    </div>
                    <div class="svc-card-body tf-card-body">
                        <form action="search_id.php" method="POST" id="formSearch" class="tf-form" novalidate>
                            <div class="tf-collected" hidden></div>
                            <div class="tf-wizard" data-accent="search" data-form="Search">
                                <div class="tf-progress"><div class="tf-progress-bar" id="tfBarSearch"></div></div>
                                <div class="tf-stage" id="tfStageSearch"></div>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <a href="get_receipt.php" class="svc-btn-secondary">
                                <i class="bi bi-file-earmark-pdf"></i> Already paid? Download Receipt
                            </a>
                        </div>
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
    /* ── Service selector (dropdown) ─────────────────────── */
    var SVC_META = {
        svcFound:  { accent: 'found',  icon: 'bi-cloud-upload' },
        svcLost:   { accent: 'lost',   icon: 'bi-flag' },
        svcSearch: { accent: 'search', icon: 'bi-search' }
    };
    function switchSvcBySelect(sel) {
        var panelId = sel.value;
        if (!panelId) return;
        document.querySelectorAll('.svc-panel').forEach(function (p) { p.style.display = 'none'; });
        var el = document.getElementById(panelId);
        el.style.display = 'block';
        el.style.animation = 'svcFadeIn .35s ease';

        var meta = SVC_META[panelId];
        var wrap = sel.closest('.svc-select-wrap');
        wrap.setAttribute('data-accent', meta.accent);
        wrap.querySelector('.svc-select-icon').className = 'bi ' + meta.icon + ' svc-select-icon';
    }

    /* ══════════════════════════════════════════════════════
       Typeform-style one-question-at-a-time wizard engine
       Drives the Found / Lost / Search forms in #services.
    ══════════════════════════════════════════════════════ */
    function fs(name, q, type, opts) {
        opts = opts || {};
        var step = { name: name, q: q, type: type, required: opts.required !== false };
        for (var k in opts) step[k] = opts[k];
        return step;
    }

    var DOC_TYPE_OPTIONS = [
        ['national_id',       'National ID'],
        ['driving_permit',    'Driving Permit'],
        ['passport',          'Passport'],
        ['student_id',        'Student ID'],
        ['academic_document', 'Academic Document'],
        ['land_title',        'Land Title'],
        ['birth_certificate', 'Birth Certificate'],
        ['other',             'Other Document']
    ];

    var DOCTYPE_Q = {
        Found:  'What type of document did you find?',
        Lost:   'What type of document did you lose?',
        Search: 'What type of document are you looking for?'
    };

    var GENDER_OPTIONS = [['male','Male'],['female','Female'],['other','Other']];

    var STEP_DEFS = {
        national_id: [
            fs('surName',   "What's the surname on the document?", 'text'),
            fs('givenName', 'And the given name?', 'text'),
            fs('dob',       'Date of birth on the document?', 'date'),
            fs('id_number', "What's the NIN number?", 'text', {placeholder:'CM...'}),
            fs('gender',    'Gender on the document?', 'choice', {options: GENDER_OPTIONS})
        ],
        driving_permit: [
            fs('surName',   "What's the surname on the permit?", 'text'),
            fs('givenName', 'And the given name?', 'text'),
            fs('dob',       'Date of birth on the permit?', 'date'),
            fs('id_number', "What's the permit number?", 'text'),
            fs('extra1',    "What's the NIN number on it?", 'text', {placeholder:'CM...'})
        ],
        passport: [
            fs('surName',   "What's the surname on the passport?", 'text'),
            fs('givenName', 'And the given name?', 'text'),
            fs('dob',       'Date of birth on the passport?', 'date'),
            fs('id_number', "What's the passport number?", 'text'),
            fs('extra1',    'What nationality is shown?', 'text')
        ],
        student_id: [
            fs('surName',   "What's the surname on the ID?", 'text'),
            fs('givenName', 'And the given name?', 'text'),
            fs('id_number', "What's the student / registration number?", 'text'),
            fs('extra1',    'What course is shown?', 'text'),
            fs('dob',       'What date was it issued?', 'date'),
            fs('extra2',    'Which school or institution?', 'text')
        ],
        academic_document: [
            fs('surName',   "What's the surname on the document?", 'text'),
            fs('givenName', 'And the given name?', 'text'),
            fs('id_number', "What's the certificate number?", 'text'),
            fs('extra1',    'Which institution?', 'text'),
            fs('extra2',    "What's the course title?", 'text'),
            fs('extra3',    'What graduation year?', 'number', {min:1900, max:2099})
        ],
        land_title: [
            fs('surName',   "What's the surname on the title?", 'text'),
            fs('givenName', 'And the given name?', 'text'),
            fs('id_number', "What's the plot / title number?", 'text'),
            fs('extra1',    'Which district?', 'text'),
            fs('extra2',    "What's the land reference?", 'text')
        ],
        birth_certificate: [
            fs('surName',   "What's the surname on the certificate?", 'text'),
            fs('givenName', 'And the given name?', 'text'),
            fs('dob',       'Date of birth shown?', 'date'),
            fs('id_number', "What's the certificate registration number?", 'text'),
            fs('extra1',    'District of birth?', 'text')
        ],
        other: [
            fs('surName',   "What's the surname on the document?", 'text'),
            fs('givenName', 'And the given name?', 'text'),
            fs('id_number', 'Any reference number on it?', 'text'),
            fs('extra1',    'Briefly describe the document', 'text')
        ]
    };

    // Short, contextual label for each doc type's ID/reference field,
    // used to phrase the "search by ID" question naturally.
    var ID_LABELS = {
        national_id:       'NIN',
        driving_permit:    'permit',
        passport:          'passport',
        student_id:        'student / registration',
        academic_document: 'certificate',
        land_title:        'plot / title',
        birth_certificate: 'certificate registration',
        other:             'reference'
    };

    // Search branches after doc type: either "I have the ID number" or
    // "I only know the name + date of birth" — asking for every field
    // regardless of which one the searcher actually has is pointless.
    function buildSearchMethodStep(docType) {
        var idLabel = ID_LABELS[docType] || 'reference';
        return fs('search_method', 'How would you like to search?', 'choice', {
            options: [
                ['id',   'By ID / ' + idLabel.charAt(0).toUpperCase() + idLabel.slice(1) + ' Number'],
                ['name', 'By Full Name + Date of Birth']
            ],
            dynamicNext: function (method) {
                var next = (method === 'id')
                    ? [ fs('id_number', "What's the " + idLabel + " number?", 'text', {placeholder: docType === 'national_id' ? 'CM...' : ''}) ]
                    : [
                        fs('surName',   "What's the surname on the document?", 'text'),
                        fs('givenName', 'And the given name?', 'text'),
                        fs('dob',       'Date of birth on the document?', 'date')
                      ];
                return next.concat(TRAILING.Search.steps);
            }
        });
    }

    var TRAILING = {
        Found: {
            steps: [
                fs('front_img', 'Upload a clear photo of the front', 'file', {required:true, accept:'image/*', hint:'Make sure all text is readable'}),
                fs('back_img',  'Got a photo of the back too?', 'file', {required:false, accept:'image/*', hint:'Optional — skip if you only have the front'})
            ],
            submitLabel: 'Submit Found Document', submitIcon: 'bi-cloud-upload'
        },
        Lost: {
            steps: [
                fs('reporter_name',  "What's your full name?", 'text', {required:true}),
                fs('reporter_phone', "What's your phone number?", 'tel', {required:true, placeholder:'07XXXXXXXX'}),
                fs('reporter_email', "What's your email?", 'email', {required:false, placeholder:'you@example.com', hint:'Optional'}),
                fs('police_report_code', 'Already reported it to police? Enter the OB / reference number', 'text', {required:false, hint:"Skip if you haven't reported it yet — you'll just need to bring the police letter in person when you collect it"})
            ],
            submitLabel: 'Submit Lost Report', submitIcon: 'bi-flag'
        },
        Search: {
            steps: [
                fs('searcher_phone', "What's your phone number?", 'tel', {required:false, placeholder:'07XXXXXXXX', hint:'Optional — helps us reach you'})
            ],
            submitLabel: 'Search Documents', submitIcon: 'bi-search'
        }
    };

    var WIZ = {};

    // What happens right after doc type is answered: Found goes straight
    // into that type's full field list (we need real details to describe
    // what was found); Lost only ever asks for the ID/reference number —
    // that's the one thing a reporter reliably knows for certain; Search
    // asks how the searcher wants to look the document up (see
    // buildSearchMethodStep).
    function docTypeDynamicNext(formType) {
        if (formType === 'Search') {
            return function (docType) { return [ buildSearchMethodStep(docType) ]; };
        }
        if (formType === 'Lost') {
            return function (docType) {
                var idLabel = ID_LABELS[docType] || 'reference';
                var idStep = fs('id_number', "What's the " + idLabel + " number?", 'text', {placeholder: docType === 'national_id' ? 'CM...' : ''});
                return [idStep].concat(TRAILING.Lost.steps);
            };
        }
        return function (docType) {
            return (STEP_DEFS[docType] || []).concat(TRAILING[formType].steps);
        };
    }

    function initWizard(formType) {
        WIZ[formType] = {
            formType: formType,
            started: false,
            steps: [ fs('doc_type', DOCTYPE_Q[formType], 'choice', {options: DOC_TYPE_OPTIONS, dynamicNext: docTypeDynamicNext(formType)}) ],
            index: 0,
            values: {},
            stage: document.getElementById('tfStage' + formType),
            bar:   document.getElementById('tfBar' + formType),
            form:  document.getElementById('form' + formType)
        };
        renderIntro(WIZ[formType]);
    }

    // Nothing is active/filled-in until the user deliberately clicks Start —
    // keeps the panel clean instead of dumping a live question on them.
    function renderIntro(w) {
        w.bar.style.width = '0%';
        w.stage.innerHTML =
            '<div class="tf-intro">' +
                '<p class="tf-intro-sub">Tap start when you’re ready — one quick question at a time.</p>' +
                '<button type="button" class="tf-ok-btn tf-start-btn"><i class="bi bi-play-fill"></i> Start</button>' +
            '</div>';
        w.stage.querySelector('.tf-start-btn').addEventListener('click', function () { startWizard(w); });
    }

    function startWizard(w) {
        w.started = true;
        renderStep(w);
    }

    function escAttr(v) { return String(v == null ? '' : v).replace(/"/g, '&quot;'); }

    function renderStep(w) {
        var step   = w.steps[w.index];
        var isLast = (w.index === w.steps.length - 1) && typeof step.dynamicNext !== 'function';
        var qNum   = w.index + 1;
        var answerHtml = '';

        if (step.type === 'choice') {
            answerHtml = '<div class="tf-choices" role="group">';
            step.options.forEach(function (opt, i) {
                var key = String.fromCharCode(65 + i);
                var sel = (w.values[step.name] === opt[0]) ? ' selected' : '';
                answerHtml += '<button type="button" class="tf-choice' + sel + '" data-value="' + escAttr(opt[0]) + '">' +
                    '<span class="tf-choice-key">' + key + '</span>' + opt[1] + '</button>';
            });
            answerHtml += '</div><input type="hidden" name="' + step.name + '" value="' + escAttr(w.values[step.name]) + '">';
        } else if (step.type === 'file') {
            var hasFile = !!w.values[step.name + '__filename'];
            answerHtml = '<label class="tf-file' + (hasFile ? ' has-file' : '') + '" tabindex="0">' +
                '<input type="file" name="' + step.name + '" accept="' + escAttr(step.accept || '') + '" class="tf-file-input">' +
                '<span class="tf-file-inner">' +
                    '<i class="bi ' + (hasFile ? 'bi-check-circle-fill' : 'bi-cloud-arrow-up') + '"></i>' +
                    '<span class="tf-file-text">' + (hasFile ? w.values[step.name + '__filename'] : 'Click to choose a file') + '</span>' +
                '</span></label>';
        } else {
            var inputType = (step.type === 'number' || step.type === 'date' || step.type === 'tel' || step.type === 'email') ? step.type : 'text';
            answerHtml = '<input type="' + inputType + '" name="' + step.name + '" class="tf-answer-input" autocomplete="off"' +
                (step.placeholder ? ' placeholder="' + escAttr(step.placeholder) + '"' : (inputType === 'text' ? ' placeholder="Type your answer here…"' : '')) +
                (step.min != null ? ' min="' + step.min + '"' : '') +
                (step.max != null ? ' max="' + step.max + '"' : '') +
                ' value="' + escAttr(w.values[step.name]) + '">';
        }

        var okLabel = isLast ? TRAILING[w.formType].submitLabel : 'OK';
        var okIcon  = isLast ? (TRAILING[w.formType].submitIcon || 'bi-check-lg') : 'bi-check-lg';

        w.stage.innerHTML =
            '<div class="tf-step">' +
                '<div class="tf-q"><span class="tf-qnum">' + qNum + ' <i class="bi bi-arrow-right"></i></span>' + step.q +
                    (step.required ? ' <span class="req">*</span>' : ' <span class="tf-optional">(optional)</span>') +
                '</div>' +
                (step.hint ? '<p class="tf-hint">' + step.hint + '</p>' : '') +
                '<div class="tf-answer">' + answerHtml + '</div>' +
                '<div class="tf-error" hidden></div>' +
                '<div class="tf-actions">' +
                    '<button type="button" class="tf-ok-btn">' + okLabel + ' <i class="bi ' + okIcon + '"></i></button>' +
                    (w.index > 0 ? '<button type="button" class="tf-back-btn"><i class="bi bi-arrow-left"></i> Back</button>' : '') +
                    (step.type !== 'file' ? '<span class="tf-enter-hint">press <strong>Enter ↵</strong></span>' : '') +
                    (!step.required ? '<button type="button" class="tf-skip">Skip <i class="bi bi-arrow-right"></i></button>' : '') +
                '</div>' +
            '</div>';

        w.bar.style.width = Math.round((w.index / Math.max(1, w.steps.length - 1)) * 100) + '%';

        var stepEl = w.stage.querySelector('.tf-step');
        stepEl.querySelector('.tf-ok-btn').addEventListener('click', function () { attemptNext(w); });

        var backBtn = stepEl.querySelector('.tf-back-btn');
        if (backBtn) backBtn.addEventListener('click', function () { goPrev(w); });

        var skipBtn = stepEl.querySelector('.tf-skip');
        if (skipBtn) skipBtn.addEventListener('click', function () { goNext(w); });

        if (step.type === 'choice') {
            stepEl.querySelectorAll('.tf-choice').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    w.values[step.name] = btn.getAttribute('data-value');
                    stepEl.querySelectorAll('.tf-choice').forEach(function (b) { b.classList.remove('selected'); });
                    btn.classList.add('selected');
                    setTimeout(function () { attemptNext(w); }, 180);
                });
            });
        } else if (step.type === 'file') {
            stepEl.querySelector('.tf-file-input').addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    w.values[step.name + '__filename'] = this.files[0].name;
                    stepEl.querySelector('.tf-file').classList.add('has-file');
                    stepEl.querySelector('.tf-file-text').textContent = this.files[0].name;
                    stepEl.querySelector('.tf-file-inner i').className = 'bi bi-check-circle-fill';
                    stepEl.querySelector('.tf-error').hidden = true;
                }
            });
        } else {
            var inputEl = stepEl.querySelector('.tf-answer-input');
            inputEl.focus();
            inputEl.addEventListener('input', function () {
                w.values[step.name] = inputEl.value;
                stepEl.querySelector('.tf-error').hidden = true;
            });
            inputEl.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') { e.preventDefault(); attemptNext(w); }
            });
        }
    }

    function validateStep(w) {
        var step = w.steps[w.index];
        if (!step.required) return true;
        if (step.type === 'choice') return !!w.values[step.name];
        if (step.type === 'file') {
            var input = w.stage.querySelector('.tf-file-input');
            return !!(input && input.files && input.files.length > 0);
        }
        var input = w.stage.querySelector('.tf-answer-input');
        return !!(input && input.value.trim() !== '');
    }

    function showStepError(w) {
        var stepEl = w.stage.querySelector('.tf-step');
        var errEl  = stepEl.querySelector('.tf-error');
        errEl.textContent = 'Please answer this question to continue';
        errEl.hidden = false;
        stepEl.classList.remove('tf-shake');
        void stepEl.offsetWidth;
        stepEl.classList.add('tf-shake');
    }

    function attemptNext(w) {
        if (!w.started) { startWizard(w); return; }
        if (!validateStep(w)) { showStepError(w); return; }
        goNext(w);
    }

    // Each step's stage markup gets replaced (and its inputs destroyed) as
    // soon as we move on, so an answered field must be persisted somewhere
    // that survives — a hidden holding area inside the same <form>. Text /
    // choice answers are re-saved as a hidden input; file answers can't be
    // recreated, so the actual <input type="file"> node itself is moved
    // there (which preserves its FileList).
    function commitStep(w) {
        var step = w.steps[w.index];
        var collected = w.form.querySelector('.tf-collected');
        collected.querySelectorAll('[name="' + step.name + '"]').forEach(function (el) { el.remove(); });

        if (step.type === 'file') {
            var fileInput = w.stage.querySelector('.tf-file-input');
            if (fileInput && fileInput.files && fileInput.files.length > 0) {
                collected.appendChild(fileInput);
            }
        } else {
            var hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = step.name;
            hidden.value = w.values[step.name] || '';
            collected.appendChild(hidden);
        }
    }

    function goNext(w) {
        var step = w.steps[w.index];
        commitStep(w);
        if (typeof step.dynamicNext === 'function') {
            var extra = step.dynamicNext(w.values[step.name], w) || [];
            w.steps = w.steps.slice(0, w.index + 1).concat(extra);
        }
        if (w.index >= w.steps.length - 1) {
            w.form.submit();
            return;
        }
        w.index++;
        renderStep(w);
    }

    function goPrev(w) {
        if (w.index === 0) return;
        w.index--;
        renderStep(w);
    }

    ['Found', 'Lost', 'Search'].forEach(function (formType) { initWizard(formType); });
    </script>

</body>
</html>

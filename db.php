<?php
// ─────────────────────────────────────────────
// Database Configuration
// ─────────────────────────────────────────────
// Local (XAMPP) defaults below. `db.local.php` (tracked in this private repo)
// holds the live server's production credentials and overrides these
// defaults automatically when present, so `git pull` on the live server
// just works with no manual config step.
$__db_local = __DIR__ . '/db.local.php';
if (file_exists($__db_local)) {
    require $__db_local;
}

if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'irecover');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

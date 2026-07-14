<?php
require_once 'db.php';
$r = $conn->query("SHOW COLUMNS FROM documents");
if (!$r) { echo "documents table missing: " . $conn->error . "\n"; exit; }
while ($c = $r->fetch_assoc()) echo $c['Field'] . " - " . $c['Type'] . "\n";

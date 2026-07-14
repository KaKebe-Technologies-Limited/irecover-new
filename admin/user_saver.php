<?php
// ─────────────────────────────────────────────
// Add New Station (Super Admin only)
// ─────────────────────────────────────────────
session_start();
require '../db.php';

// Guard: only super admins may call this
if (!isset($_SESSION['admin_user'])) {
    http_response_code(403);
    echo "<p style='color:red;'>Access denied.</p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "<p style='color:red;'>Invalid request method.</p>";
    exit();
}

$user_name      = trim($_POST['user_name']      ?? '');
$password       = trim($_POST['password']       ?? '');
$number         = trim($_POST['number']         ?? '');
$district       = trim($_POST['district']       ?? '');
$address        = trim($_POST['address']        ?? '');
$email          = trim($_POST['email']          ?? '');
$type_of_entity = trim($_POST['type_of_entity'] ?? '');
$registered_at  = date('Y-m-d / h:i:s A');

$stmt = $conn->prepare(
    "INSERT INTO admins (user_name, password, number, email, district, address, type_of_entity, registered_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param('ssssssss', $user_name, $password, $number, $email, $district, $address, $type_of_entity, $registered_at);

if ($stmt->execute()) {
    echo "<p style='color:green; padding:5px;'>Station added successfully!</p>";
} else {
    echo "<p style='color:red; padding:5px;'>Error: " . htmlspecialchars($conn->error) . "</p>";
}

$stmt->close();
$conn->close();

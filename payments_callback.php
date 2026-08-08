<?php
// ─────────────────────────────────────────────
// IOTec Pay IPN / callback endpoint.
// Register this URL (https://irecover.site/payments_callback.php) in the
// IOTec portal under the wallet's Collection callback settings, with
// Security Header name "X-Iotec-Ipn-Secret" and value = your IPN secret.
// ─────────────────────────────────────────────
include_once 'db.php';
include_once 'includes/match_engine.php';
include_once 'includes/iotec_pay.php';

header('Content-Type: text/plain');

if (!iotecVerifyCallback()) {
    http_response_code(401);
    echo 'invalid signature';
    exit();
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);

if (!is_array($payload) || empty($payload['id'])) {
    http_response_code(400);
    echo 'bad payload';
    exit();
}

$stmt = $conn->prepare("SELECT id FROM payments WHERE iotec_transaction_id=? LIMIT 1");
$stmt->bind_param('s', $payload['id']);
$stmt->execute();
$pay = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pay) {
    // Not one of ours (or externalId mismatch) — acknowledge so IOTec stops retrying
    http_response_code(200);
    echo 'ignored: unknown transaction';
    exit();
}

applyPaymentStatus($conn, (int)$pay['id'], $payload);

http_response_code(200);
echo 'ok';

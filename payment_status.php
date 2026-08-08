<?php
// ─────────────────────────────────────────────
// JSON polling endpoint used by pay.php's "waiting for approval" screen.
// Falls back to an active IOTec status check if no callback has landed
// yet — important for local/dev testing where IOTec can't reach us.
// ─────────────────────────────────────────────
include_once 'db.php';
include_once 'includes/match_engine.php';
include_once 'includes/iotec_pay.php';

header('Content-Type: application/json');

$pid = (int)($_GET['pid'] ?? 0);
if ($pid <= 0) {
    echo json_encode(['status' => 'failed', 'message' => 'Invalid payment reference.']);
    exit();
}

$stmt = $conn->prepare("SELECT id, status, iotec_transaction_id FROM payments WHERE id=? LIMIT 1");
$stmt->bind_param('i', $pid);
$stmt->execute();
$pay = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pay) {
    echo json_encode(['status' => 'failed', 'message' => 'Payment not found.']);
    exit();
}

if ($pay['status'] === 'confirmed') {
    echo json_encode(['status' => 'confirmed', 'message' => 'Payment confirmed.']);
    exit();
}
if ($pay['status'] === 'failed') {
    echo json_encode(['status' => 'failed', 'message' => 'Payment was not completed.']);
    exit();
}

// Still pending — actively check with IOTec in case the callback hasn't arrived
if (!empty($pay['iotec_transaction_id'])) {
    try {
        $resp = iotecCheckStatus($pay['iotec_transaction_id']);
        $result = applyPaymentStatus($conn, $pid, $resp);
        echo json_encode($result);
        exit();
    } catch (IotecPayException $e) {
        // Network hiccup — keep the client polling, don't fail the payment
    }
}

echo json_encode(['status' => 'pending', 'message' => 'Waiting for you to approve the payment prompt on your phone.']);

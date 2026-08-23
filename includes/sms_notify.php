<?php
// ─────────────────────────────────────────────
// SMS notifications — Africa's Talking Messaging API
// Docs: https://developers.africastalking.com/docs/sms/overview
// ─────────────────────────────────────────────

$__sms_local = __DIR__ . '/../sms.local.php';
if (file_exists($__sms_local)) {
    require_once $__sms_local;
}

if (!defined('AT_USERNAME')) {
    // No credentials configured — sendSms() will just log and no-op.
    define('AT_USERNAME', '');
    define('AT_API_KEY', '');
    define('AT_SENDER_ID', '');
    define('AT_SANDBOX', true);
}

/**
 * Normalize a Ugandan phone number to the +2567XXXXXXXX format Africa's
 * Talking expects. Accepts 07XXXXXXXX, 2567XXXXXXXX, or +2567XXXXXXXX.
 */
function normalizeUgPhoneForSms(string $phone): ?string {
    $digits = preg_replace('/\D/', '', $phone);
    if ($digits === '') return null;
    if (str_starts_with($digits, '0') && strlen($digits) === 10) {
        $digits = '256' . substr($digits, 1);
    } elseif (str_starts_with($digits, '7') && strlen($digits) === 9) {
        $digits = '256' . $digits;
    }
    if (!preg_match('/^256\d{9}$/', $digits)) return null;
    return '+' . $digits;
}

/**
 * Sends a single SMS. Never throws — a failed/unconfigured send is logged
 * and swallowed so it can never break the caller's main flow.
 */
function sendSms(string $phone, string $message): bool {
    if (empty(AT_USERNAME) || empty(AT_API_KEY)) {
        error_log('iRecovery SMS: not configured (missing sms.local.php) — would have sent to ' . $phone . ': ' . $message);
        return false;
    }
    $to = normalizeUgPhoneForSms($phone);
    if (!$to) {
        error_log('iRecovery SMS: invalid phone number "' . $phone . '"');
        return false;
    }

    $username = AT_SANDBOX ? 'sandbox' : AT_USERNAME;
    $base     = AT_SANDBOX ? 'https://api.sandbox.africastalking.com' : 'https://api.africastalking.com';

    $payload = [
        'username' => $username,
        'to'       => $to,
        'message'  => $message,
    ];
    if (!empty(AT_SENDER_ID)) $payload['from'] = AT_SENDER_ID;

    $ch = curl_init($base . '/version1/messaging');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($payload),
        CURLOPT_HTTPHEADER     => [
            'apiKey: ' . AT_API_KEY,
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json',
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $raw    = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err    = curl_error($ch);
    curl_close($ch);

    if ($raw === false || $status !== 201 && $status !== 200) {
        error_log('iRecovery SMS: send failed to ' . $to . ' (HTTP ' . $status . '): ' . ($err ?: $raw));
        return false;
    }
    return true;
}

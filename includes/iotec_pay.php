<?php
// ─────────────────────────────────────────────
// IOTec Pay client — OAuth2 client-credentials + Collections API
// Docs: https://pay.iotec.io/api-docs/index.html
// ─────────────────────────────────────────────

$__pay_local = __DIR__ . '/../payments.local.php';
if (file_exists($__pay_local)) {
    require_once $__pay_local;
}

if (!defined('IOTEC_CLIENT_ID')) {
    // No credentials configured — collection calls will throw IotecPayException.
    define('IOTEC_CLIENT_ID', '');
    define('IOTEC_CLIENT_SECRET', '');
    define('IOTEC_TEST_WALLET_ID', '');
    define('IOTEC_LIVE_WALLET_ID', '');
    define('IOTEC_IPN_SECRET', '');
    define('IOTEC_SANDBOX', true);
}

define('IOTEC_TOKEN_URL', 'https://id.iotec.io/connect/token');
define('IOTEC_API_BASE', 'https://pay.iotec.io/api');

class IotecPayException extends Exception {}

/**
 * Low-level HTTP helper. Returns [statusCode, decodedBody].
 */
function iotecHttp(string $method, string $url, array $headers, ?string $body = null): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

    $raw = curl_exec($ch);
    if ($raw === false) {
        $err = curl_error($ch);
        curl_close($ch);
        throw new IotecPayException("IOTec Pay network error: $err");
    }
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($raw, true);
    return [$status, is_array($decoded) ? $decoded : ['raw' => $raw]];
}

/**
 * Fetch a fresh OAuth2 access token via client_credentials.
 */
function iotecGetToken(): string {
    if (empty(IOTEC_CLIENT_ID) || empty(IOTEC_CLIENT_SECRET)) {
        throw new IotecPayException('IOTec Pay is not configured (missing payments.local.php).');
    }
    $body = http_build_query([
        'client_id'     => IOTEC_CLIENT_ID,
        'client_secret' => IOTEC_CLIENT_SECRET,
        'grant_type'    => 'client_credentials',
    ]);
    [$status, $resp] = iotecHttp('POST', IOTEC_TOKEN_URL, [
        'Content-Type: application/x-www-form-urlencoded',
    ], $body);

    if ($status !== 200 || empty($resp['access_token'])) {
        throw new IotecPayException('IOTec Pay authentication failed (HTTP ' . $status . ').');
    }
    return $resp['access_token'];
}

/**
 * The wallet ID to use for collections, per IOTEC_SANDBOX.
 */
function iotecWalletId(): string {
    return IOTEC_SANDBOX ? IOTEC_TEST_WALLET_ID : IOTEC_LIVE_WALLET_ID;
}

/**
 * Initiate a Mobile Money collection request.
 * Returns the decoded transaction object from IOTec (includes 'id', 'status', ...).
 */
function iotecInitiateCollection(float $amount, string $payerPhone, string $payerName, string $externalId, string $note): array {
    $token = iotecGetToken();
    $payload = json_encode([
        'category'                   => 'MobileMoney',
        'currency'                   => 'UGX',
        'walletId'                   => iotecWalletId(),
        'externalId'                 => $externalId,
        'payer'                      => $payerPhone,
        'payerName'                  => $payerName,
        'amount'                     => $amount,
        'payeeNote'                  => $note,
        'transactionChargesCategory' => 'ChargeCustomer',
    ]);
    [$status, $resp] = iotecHttp('POST', IOTEC_API_BASE . '/collections/collect', [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
    ], $payload);

    if ($status !== 200 || empty($resp['id'])) {
        $msg = $resp['message'] ?? ('HTTP ' . $status);
        throw new IotecPayException('IOTec Pay collection request failed: ' . $msg);
    }
    return $resp;
}

/**
 * Check the current status of a collection transaction by its IOTec transaction id.
 */
function iotecCheckStatus(string $transactionId): array {
    $token = iotecGetToken();
    [$status, $resp] = iotecHttp('GET', IOTEC_API_BASE . '/collections/status/' . rawurlencode($transactionId), [
        'Authorization: Bearer ' . $token,
    ]);

    if ($status !== 200 || empty($resp['id'])) {
        $msg = $resp['message'] ?? ('HTTP ' . $status);
        throw new IotecPayException('IOTec Pay status check failed: ' . $msg);
    }
    return $resp;
}

/**
 * Verify an incoming IPN/callback request carries the correct security header.
 */
function iotecVerifyCallback(): bool {
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    foreach ($headers as $k => $v) {
        if (strcasecmp($k, 'X-Iotec-Ipn-Secret') === 0) {
            return hash_equals(IOTEC_IPN_SECRET, (string)$v);
        }
    }
    return false;
}

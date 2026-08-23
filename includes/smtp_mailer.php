<?php
// ─────────────────────────────────────────────
// Minimal authenticated SMTP client (STARTTLS + AUTH LOGIN) — no external
// library needed, consistent with this project's no-framework/no-Composer
// style. Works with Gmail (App Password) and any standard SMTP provider.
// ─────────────────────────────────────────────

$__email_local = __DIR__ . '/../email.local.php';
if (file_exists($__email_local)) {
    require_once $__email_local;
}

if (!defined('SMTP_HOST')) {
    // No credentials configured — smtpSendMail() will just return false.
    define('SMTP_HOST', '');
    define('SMTP_PORT', 587);
    define('SMTP_USER', '');
    define('SMTP_PASS', '');
    define('SMTP_FROM_NAME', 'iRecovery Uganda');
}

function smtpConfigured(): bool {
    return !empty(SMTP_HOST) && !empty(SMTP_USER) && !empty(SMTP_PASS);
}

/**
 * Sends one command (or, for DATA's body, the raw payload) and reads the
 * server's response, throwing if the status code isn't one we expect.
 */
function smtpExpect($socket, string $cmd, array $expectCodes): string {
    if ($cmd !== '') {
        fwrite($socket, $cmd . "\r\n");
    }
    $response = '';
    while (($line = fgets($socket, 515)) !== false) {
        $response .= $line;
        // Multi-line SMTP responses use "250-" until the final "250 "
        if (isset($line[3]) && $line[3] === ' ') break;
        if ($line === '') break;
    }
    $code = (int)substr($response, 0, 3);
    if (!in_array($code, $expectCodes, true)) {
        throw new RuntimeException('SMTP error (expected ' . implode('/', $expectCodes) . ", got \"$response\")");
    }
    return $response;
}

/**
 * Sends an HTML email via authenticated SMTP. Returns true on success.
 * Never throws to the caller — failures are caught and logged.
 */
function smtpSendMail(string $to, string $subject, string $htmlBody, array $cc = [], ?string $replyTo = null): bool {
    if (!smtpConfigured()) return false;

    $socket = null;
    try {
        $socket = @stream_socket_client('tcp://' . SMTP_HOST . ':' . SMTP_PORT, $errno, $errstr, 15);
        if (!$socket) throw new RuntimeException("Could not connect to " . SMTP_HOST . ":" . SMTP_PORT . " — $errstr ($errno)");
        stream_set_timeout($socket, 15);

        smtpExpect($socket, '', [220]);
        smtpExpect($socket, 'EHLO irecover.site', [250]);
        smtpExpect($socket, 'STARTTLS', [220]);
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new RuntimeException('TLS negotiation failed');
        }
        smtpExpect($socket, 'EHLO irecover.site', [250]);
        smtpExpect($socket, 'AUTH LOGIN', [334]);
        smtpExpect($socket, base64_encode(SMTP_USER), [334]);
        smtpExpect($socket, base64_encode(SMTP_PASS), [235]);

        smtpExpect($socket, 'MAIL FROM:<' . SMTP_USER . '>', [250]);
        foreach (array_merge([$to], $cc) as $rcpt) {
            $rcpt = trim($rcpt);
            if ($rcpt === '') continue;
            smtpExpect($socket, 'RCPT TO:<' . $rcpt . '>', [250, 251]);
        }
        smtpExpect($socket, 'DATA', [354]);

        $headers   = [];
        $headers[] = 'From: ' . SMTP_FROM_NAME . ' <' . SMTP_USER . '>';
        $headers[] = 'To: ' . $to;
        if (!empty($cc)) $headers[] = 'Cc: ' . implode(', ', array_filter($cc));
        if ($replyTo)    $headers[] = 'Reply-To: ' . $replyTo;
        $headers[] = 'Subject: ' . $subject;
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'Date: ' . date('r');

        // Dot-stuff any body line that starts with a lone "." (SMTP transparency rule)
        $bodyEscaped = preg_replace('/^\./m', '..', $htmlBody);
        $message = implode("\r\n", $headers) . "\r\n\r\n" . $bodyEscaped . "\r\n.";
        smtpExpect($socket, $message, [250]);

        smtpExpect($socket, 'QUIT', [221]);
        fclose($socket);
        return true;
    } catch (\Throwable $e) {
        error_log('iRecovery SMTP: ' . $e->getMessage());
        if (is_resource($socket)) fclose($socket);
        return false;
    }
}

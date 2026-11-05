<?php
/** Outbound calls: Google reCAPTCHA v3 verification and Mailgun sending. */
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function http_post(string $url, array $fields, ?string $basicAuth = null): array
{
    $ch = curl_init($url);
    $opts = [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($fields),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT        => 15,
    ];
    if ($basicAuth !== null) {
        $opts[CURLOPT_USERPWD] = $basicAuth;
    }
    curl_setopt_array($ch, $opts);
    $body = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    return ['status' => $status, 'body' => is_string($body) ? $body : '', 'error' => $error];
}

/**
 * Verifies a v3 token. Returns [ok(bool), reason(string), score(float|null)].
 */
function verify_recaptcha(string $token, string $expectedAction): array
{
    if ($token === '') {
        return [false, 'missing token', null];
    }
    $res = http_post('https://www.google.com/recaptcha/api/siteverify', [
        'secret'   => s('recaptcha_secret_key'),
        'response' => $token,
        'remoteip' => client_ip(),
    ]);
    if ($res['status'] !== 200) {
        return [false, 'verify request failed: ' . ($res['error'] ?: 'HTTP ' . $res['status']), null];
    }
    $data = json_decode($res['body'], true) ?: [];
    $score = isset($data['score']) ? (float) $data['score'] : null;
    if (empty($data['success'])) {
        return [false, 'rejected: ' . implode(',', $data['error-codes'] ?? ['unknown']), $score];
    }
    if (($data['action'] ?? '') !== $expectedAction) {
        return [false, 'action mismatch', $score];
    }
    $min = (float) (s('recaptcha_min_score') ?: '0.5');
    if ($score === null || $score < $min) {
        return [false, 'score below threshold', $score];
    }
    return [true, 'ok', $score];
}

function mailgun_base(): string
{
    return s('mailgun_region') === 'eu' ? 'https://api.eu.mailgun.net' : 'https://api.mailgun.net';
}

/** Comma/semicolon separated list → array of valid addresses. */
function email_list(string $raw): array
{
    $out = [];
    foreach (preg_split('/[,;\s]+/', $raw) as $addr) {
        $addr = trim($addr);
        if ($addr !== '' && filter_var($addr, FILTER_VALIDATE_EMAIL)) {
            $out[] = $addr;
        }
    }
    return array_values(array_unique($out));
}

/** Strip CR/LF so header-like fields can't be split. */
function one_line(string $v): string
{
    return trim(preg_replace('/[\r\n]+/', ' ', $v));
}

/**
 * Sends a plain-text message through the Mailgun API. Returns [ok(bool), message(string)].
 */
function send_mailgun(string $subject, string $text, ?string $replyTo = null, ?array $to = null): array
{
    if (!mailgun_ready()) {
        return [false, 'Mailgun is not fully configured.'];
    }
    $to = $to ?? email_list(s('form_to_emails'));
    if (!$to) {
        return [false, 'No valid admin email address is set.'];
    }
    $fromName = one_line(s('mailgun_from_name') ?: s('office_name'));
    $fromName = str_replace(['"', '<', '>'], '', $fromName);
    $fields = [
        'from'    => sprintf('"%s" <%s>', $fromName, s('mailgun_from_email')),
        'to'      => implode(',', $to),
        'subject' => one_line($subject),
        'text'    => $text,
    ];
    if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $fields['h:Reply-To'] = $replyTo;
    }
    $url = mailgun_base() . '/v3/' . rawurlencode(s('mailgun_domain')) . '/messages';
    $res = http_post($url, $fields, 'api:' . s('mailgun_api_key'));
    if ($res['status'] === 200) {
        return [true, 'Sent.'];
    }
    $detail = json_decode($res['body'], true)['message'] ?? ($res['error'] ?: trim(substr($res['body'], 0, 200)));
    $hint = match ($res['status']) {
        401     => ' Check the API key and that the region (US/EU) matches your Mailgun account.',
        403     => ' The domain may not be verified, or a sandbox domain is sending to an unauthorized recipient.',
        404     => ' Check the sending domain name and region.',
        default => '',
    };
    return [false, 'Mailgun returned ' . ($res['status'] ?: 'no response') . ': ' . $detail . $hint];
}

function log_event(string $line): void
{
    @file_put_contents(DATA_DIR . '/form.log', '[' . date('c') . '] ' . one_line($line) . "\n", FILE_APPEND | LOCK_EX);
}

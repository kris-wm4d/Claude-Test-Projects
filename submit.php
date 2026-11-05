<?php
/** Appointment form endpoint: honeypot → rate limit → reCAPTCHA v3 → Mailgun. Returns JSON. */
declare(strict_types=1);

require_once __DIR__ . '/inc/bootstrap.php';
require_once __DIR__ . '/inc/services.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

function reply(int $code, bool $ok, string $message): never
{
    http_response_code($code);
    echo json_encode(['ok' => $ok, 'message' => $message]);
    exit;
}

$callUs = 'Please call us at ' . s('phone_display') . '.';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    reply(405, false, 'Method not allowed.');
}

// Same-origin check (blocks other sites posting to this endpoint from a browser).
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$host = $_SERVER['HTTP_HOST'] ?? '';
if ($origin !== '' && parse_url($origin, PHP_URL_HOST) !== parse_url('//' . $host, PHP_URL_HOST)) {
    reply(403, false, 'Request blocked.');
}

$f = fn(string $k, int $max) => mb_substr(trim((string) ($_POST[$k] ?? '')), 0, $max);

// Honeypot: bots fill hidden fields. Pretend success.
if ($f('website', 200) !== '') {
    reply(200, true, 'Thanks, we received your request.');
}

if (rate_limited('form', 5, 600)) {
    reply(429, false, 'Too many requests from your connection. ' . $callUs);
}

$name    = one_line($f('name', 100));
$phone   = one_line($f('phone', 30));
$email   = one_line($f('email', 200));
$message = $f('message', 3000);
$page    = one_line($f('page', 200));
$sms     = !empty($_POST['sms_consent']);
$mkt     = !empty($_POST['marketing_consent']);

if ($name === '' || strlen(preg_replace('/\D/', '', $phone)) < 10 || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
    reply(422, false, 'Please fill in your name, a 10-digit phone number, a valid email and a message.');
}

if (recaptcha_ready()) {
    [$ok, $reason, $score] = verify_recaptcha((string) ($_POST['recaptcha_token'] ?? ''), 'appointment');
    if (!$ok) {
        log_event("reCAPTCHA blocked ($reason, score " . ($score ?? 'n/a') . ')');
        reply(400, false, "We couldn't verify your request. Refresh the page and try again, or call us at " . s('phone_display') . '.');
    }
}

if (!mailgun_ready()) {
    log_event('Form submitted but Mailgun is not configured');
    reply(503, false, "Online requests aren't available right now. " . $callUs);
}

$body = implode("\n", [
    'New appointment request from the ' . s('office_name') . ' website',
    '',
    'Name:    ' . $name,
    'Phone:   ' . $phone,
    'Email:   ' . $email,
    '',
    'Message:',
    $message,
    '',
    'Text reminders consent:  ' . ($sms ? 'Yes' : 'No'),
    'Marketing consent:       ' . ($mkt ? 'Yes' : 'No'),
    'Page:                    ' . ($page ?: 'n/a'),
    'Submitted:               ' . date('D, M j Y g:i a T'),
    isset($score) ? 'reCAPTCHA score:         ' . $score : '',
]);

[$sent, $info] = send_mailgun(trim(s('form_subject') ?: 'New appointment request') . ' - ' . $name, $body, $email);
if (!$sent) {
    log_event('Mailgun send failed: ' . $info);
    reply(502, false, "Your request didn't go through. " . $callUs);
}

reply(200, true, "Thanks, we received your request. We'll call you within one business day.");

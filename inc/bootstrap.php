<?php
/**
 * Shared settings, escaping, session and security helpers.
 * Settings live in data/settings.php (a PHP file, so a direct web request returns nothing)
 * and data/.htaccess also denies web access to the folder.
 */
declare(strict_types=1);

const DATA_DIR      = __DIR__ . '/../data';
const SETTINGS_FILE = DATA_DIR . '/settings.php';
const AUTH_FILE     = DATA_DIR . '/auth.php';
const SECRET_KEYS   = ['recaptcha_secret_key', 'mailgun_api_key'];

function defaults(): array
{
    return [
        // Office
        'office_name'         => 'Primary PPC',
        'doctor_name'         => 'Dr. Elena Navarro',
        'doctor_credentials'  => 'DMD',
        // Location
        'location_line1'      => '[Street address]',
        'location_line2'      => 'Miami, FL [ZIP]',
        'location_url'        => 'https://www.google.com/maps/search/?api=1&query=Miami%2C+FL',
        'map_embed_src'       => '',
        // Phone
        'phone_display'       => '(305) 555-0142',
        'phone_link'          => 'tel:+13055550142',
        // Contact form
        'form_to_emails'      => '',
        'form_subject'        => 'New appointment request',
        // reCAPTCHA v3
        'recaptcha_site_key'   => '',
        'recaptcha_secret_key' => '',
        'recaptcha_min_score'  => '0.5',
        // Mailgun
        'mailgun_api_key'     => '',
        'mailgun_domain'      => '',
        'mailgun_region'      => 'us',
        'mailgun_from_email'  => '',
        'mailgun_from_name'   => '',
    ];
}

function settings(bool $reload = false): array
{
    static $cache = null;
    if ($cache === null || $reload) {
        $saved = [];
        if (is_file(SETTINGS_FILE)) {
            $loaded = include SETTINGS_FILE;
            $saved = is_array($loaded) ? $loaded : [];
        }
        $cache = array_merge(defaults(), array_intersect_key($saved, defaults()));
    }
    return $cache;
}

function write_php_array(string $file, array $data): bool
{
    if (!is_dir(DATA_DIR) && !@mkdir(DATA_DIR, 0750, true)) {
        return false;
    }
    $code = "<?php\n// Written by admin.php. Edit settings from the dashboard, not here.\nreturn "
          . var_export($data, true) . ";\n";
    $tmp = $file . '.' . bin2hex(random_bytes(4)) . '.tmp';
    if (@file_put_contents($tmp, $code, LOCK_EX) === false) {
        return false;
    }
    @chmod($tmp, 0640);
    if (!@rename($tmp, $file)) {
        @unlink($tmp);
        return false;
    }
    if (function_exists('opcache_invalidate')) {
        @opcache_invalidate($file, true);
    }
    return true;
}

function save_settings(array $values): bool
{
    $ok = write_php_array(SETTINGS_FILE, array_intersect_key($values, defaults()));
    settings(true);
    return $ok;
}

/** Escape for HTML text and attributes. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Raw setting value. Always pass through e() when printing. */
function s(string $key): string
{
    return (string) (settings()[$key] ?? '');
}

/** tel: link, falling back to the digits of the display number. */
function phone_href(): string
{
    $link = s('phone_link');
    if ($link !== '') {
        return $link;
    }
    $digits = preg_replace('/\D+/', '', s('phone_display'));
    if (strlen($digits) === 10) {
        $digits = '1' . $digits;
    }
    return $digits === '' ? '' : 'tel:+' . $digits;
}

function sms_href(): string
{
    return preg_replace('/^tel:/i', 'sms:', phone_href());
}

function mailgun_ready(): bool
{
    return s('mailgun_api_key') !== '' && s('mailgun_domain') !== ''
        && s('mailgun_from_email') !== '' && s('form_to_emails') !== '';
}

function recaptcha_ready(): bool
{
    return s('recaptcha_site_key') !== '' && s('recaptcha_secret_key') !== '';
}

function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/** Simple file-based limiter: max $max hits per $window seconds per bucket+IP. */
function rate_limited(string $bucket, int $max, int $window): bool
{
    $file = DATA_DIR . '/rate-' . preg_replace('/\W/', '', $bucket) . '.json';
    $now = time();
    $key = hash('sha256', client_ip());
    $fh = @fopen($file, 'c+');
    if (!$fh) {
        return false;
    }
    flock($fh, LOCK_EX);
    $data = json_decode(stream_get_contents($fh) ?: '{}', true) ?: [];
    foreach ($data as $k => $hits) {
        $data[$k] = array_values(array_filter($hits, fn($t) => $t > $now - $window));
        if (!$data[$k]) {
            unset($data[$k]);
        }
    }
    $limited = count($data[$key] ?? []) >= $max;
    if (!$limited) {
        $data[$key][] = $now;
    }
    ftruncate($fh, 0);
    rewind($fh);
    fwrite($fh, json_encode($data));
    flock($fh, LOCK_UN);
    fclose($fh);
    return $limited;
}

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
          || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    session_name('ppcadmin');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_ok(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string) $_POST['csrf']);
}

/**
 * Accepts a Google Maps <iframe> embed snippet or a bare embed URL.
 * Returns the cleaned src, '' for empty input, or null if it is not a Google Maps embed.
 * Only the src is stored, so pasted markup can never inject HTML into the site.
 */
function parse_map_embed(string $input): ?string
{
    $input = trim($input);
    if ($input === '') {
        return '';
    }
    if (preg_match('/\bsrc\s*=\s*["\']([^"\']+)["\']/i', $input, $m)) {
        $input = $m[1];
    }
    $src = html_entity_decode($input, ENT_QUOTES, 'UTF-8');
    $parts = parse_url($src);
    if (!$parts || ($parts['scheme'] ?? '') !== 'https') {
        return null;
    }
    $host = strtolower($parts['host'] ?? '');
    $path = $parts['path'] ?? '';
    $query = $parts['query'] ?? '';
    $isEmbedPath = str_starts_with($path, '/maps/embed');
    $isOutputEmbed = $path === '/maps' && str_contains($query, 'output=embed');
    if (!in_array($host, ['www.google.com', 'maps.google.com', 'google.com'], true) || !($isEmbedPath || $isOutputEmbed)) {
        return null;
    }
    return $src;
}

/** Basic security headers for public pages. */
function send_page_headers(): void
{
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Frame-Options: SAMEORIGIN');
}

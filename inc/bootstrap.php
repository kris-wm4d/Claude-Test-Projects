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
        // Site integrations
        'site_name'           => 'Primary PPC',
        'reporting_email'     => '',
        'whatconverts'        => '',
        'analytics'           => '',
        'chat_script'         => '',
        'header_scripts'      => '',
        'footer_scripts'      => '',
        'practices'           => [],
        // Office
        'office_name'         => 'Primary PPC',
        'doctor_name'         => 'Dr. Jane Doe',
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
        if (!array_key_exists('site_name', $saved)) {
            $cache['site_name'] = (string) ($saved['office_name'] ?? $cache['site_name']);
        }
        $cache['office_name'] = $cache['site_name'];
        if (!array_key_exists('reporting_email', $saved)) {
            $cache['reporting_email'] = (string) ($saved['form_to_emails'] ?? '');
        }
        if (!array_key_exists('practices', $saved) || !is_array($saved['practices']) || $saved['practices'] === []) {
            $cache['practices'] = [[
                'name' => (string) ($saved['office_name'] ?? $cache['site_name']),
                'location' => trim((string) ($saved['location_line1'] ?? $cache['location_line1']) . ' ' . (string) ($saved['location_line2'] ?? $cache['location_line2'])),
                'location_url' => (string) ($saved['location_url'] ?? $cache['location_url']),
                'phone_number' => (string) ($saved['phone_display'] ?? $cache['phone_display']),
                'phone_url' => (string) ($saved['phone_link'] ?? $cache['phone_link']),
                'description' => '',
                'office_hours' => "Monday: 8:00 am - 5:00 pm\nTuesday: 8:00 am - 5:00 pm\nWednesday: 9:00 am - 7:00 pm\nThursday: 8:00 am - 5:00 pm\nFriday: 8:00 am - 2:00 pm\nSaturday: By appointment\nSunday: Closed",
            ]];
        }
        $cache['practices'] = array_values(array_filter($cache['practices'], 'is_array'));
        $primary = $cache['practices'][0] ?? [];
        $cache['location_line1'] = (string) ($primary['location'] ?? '');
        $cache['location_line2'] = '';
        $cache['location_url'] = (string) ($primary['location_url'] ?? '');
        $cache['phone_display'] = (string) ($primary['phone_number'] ?? '');
        $cache['phone_link'] = (string) ($primary['phone_url'] ?? '');
        $cache['form_to_emails'] = $cache['reporting_email'];
        $cache['map_embed_src'] = practice_map_src($primary);
    }
    return $cache;
}

function practice_list(): array
{
    return settings()['practices'];
}

function practice_map_src(array $practice): string
{
    $location = trim((string) ($practice['location'] ?? ''));
    return $location === '' ? '' : 'https://www.google.com/maps?q=' . rawurlencode($location) . '&output=embed';
}

function practice_directions_url(array $practice): string
{
    $url = trim((string) ($practice['location_url'] ?? ''));
    if ($url !== '') {
        return $url;
    }
    $location = trim((string) ($practice['location'] ?? ''));
    return $location === '' ? '' : 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($location);
}

function practice_phone_href(array $practice): string
{
    $link = trim((string) ($practice['phone_url'] ?? ''));
    if ($link !== '') {
        return $link;
    }
    $digits = preg_replace('/\D+/', '', (string) ($practice['phone_number'] ?? ''));
    if (strlen($digits) === 10) {
        $digits = '1' . $digits;
    }
    return $digits === '' ? '' : 'tel:+' . $digits;
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

/** Basic security headers for public pages. */
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}

if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool
    {
        $len = strlen($needle);
        return $len === 0 || substr($haystack, -$len) === $needle;
    }
}

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return $needle !== '' && strpos($haystack, $needle) !== false;
    }
}

function send_page_headers(): void
{
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Frame-Options: SAMEORIGIN');
}

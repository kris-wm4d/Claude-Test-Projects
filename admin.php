<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/bootstrap.php';
require_once __DIR__ . '/inc/services.php';

start_secure_session();
header('X-Robots-Tag: noindex, nofollow');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
header('Cache-Control: no-store');

const IDLE_LIMIT = 7200; // seconds of inactivity before re-login

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$errors = [];
$view = 'dashboard';
$action = $_POST['action'] ?? '';
$writable = is_dir(DATA_DIR) ? is_writable(DATA_DIR) : is_writable(dirname(DATA_DIR));

function redirect_self(array $flash = null): never
{
    if ($flash) {
        $_SESSION['flash'] = $flash;
    }
    header('Location: admin.php', true, 303);
    exit;
}

function auth(): array
{
    if (!is_file(AUTH_FILE)) {
        return [];
    }
    $a = include AUTH_FILE;
    return is_array($a) ? $a : [];
}

// ---------- first-run setup ----------
if (!is_file(AUTH_FILE)) {
    $view = 'setup';
    if ($action === 'setup' && csrf_ok() && $writable) {
        $p1 = (string) ($_POST['password'] ?? '');
        $p2 = (string) ($_POST['password2'] ?? '');
        if (strlen($p1) < 10) {
            $errors['password'] = 'Use at least 10 characters.';
        } elseif ($p1 !== $p2) {
            $errors['password2'] = "The passwords don't match.";
        } else {
            if (!is_dir(DATA_DIR)) {
                @mkdir(DATA_DIR, 0750, true);
            }
            write_php_array(AUTH_FILE, ['hash' => password_hash($p1, PASSWORD_DEFAULT)]);
            if (!is_file(SETTINGS_FILE)) {
                save_settings(settings());
            }
            session_regenerate_id(true);
            $_SESSION['admin'] = true;
            $_SESSION['seen'] = time();
            redirect_self(['ok', 'Admin password created. You are signed in.']);
        }
    }
}

// ---------- session state ----------
$loggedIn = !empty($_SESSION['admin']) && (time() - (int) ($_SESSION['seen'] ?? 0)) < IDLE_LIMIT;
if ($loggedIn) {
    $_SESSION['seen'] = time();
} elseif (!empty($_SESSION['admin'])) {
    $_SESSION = [];
    $flash = ['err', 'You were signed out after 2 hours of inactivity.'];
}

if ($view !== 'setup' && !$loggedIn) {
    $view = 'login';
    if ($action === 'login' && csrf_ok()) {
        if (rate_limited('login', 5, 900)) {
            $errors['password'] = 'Too many attempts. Wait 15 minutes and try again.';
        } elseif (password_verify((string) ($_POST['password'] ?? ''), auth()['hash'] ?? '')) {
            session_regenerate_id(true);
            $_SESSION['admin'] = true;
            $_SESSION['seen'] = time();
            redirect_self();
        } else {
            usleep(600000);
            $errors['password'] = 'That password is incorrect.';
        }
    }
}

// ---------- signed-in actions ----------
$form = settings();

if ($loggedIn && $action !== '') {
    if (!csrf_ok()) {
        $flash = ['err', 'Your session expired. Reload the page and try again.'];
    } elseif ($action === 'logout') {
        $_SESSION = [];
        session_destroy();
        header('Location: admin.php', true, 303);
        exit;
    } elseif ($action === 'save') {
        $in = fn(string $k) => trim((string) ($_POST[$k] ?? ''));
        $new = settings();

        foreach (['doctor_name', 'doctor_credentials', 'form_subject', 'mailgun_from_name'] as $k) {
            $new[$k] = one_line($in($k));
        }
        $new['site_name'] = one_line($in('site_name'));
        if ($new['site_name'] === '') {
            $errors['site_name'] = "Site name can't be empty.";
        }
        $new['office_name'] = $new['site_name'];

        $rawTo = $in('reporting_email');
        $valid = email_list($rawTo);
        $tokens = array_filter(preg_split('/[,;\s]+/', $rawTo));
        if ($rawTo !== '' && count($valid) !== count(array_unique($tokens))) {
            $errors['reporting_email'] = 'Enter a valid email address.';
        }
        $new['reporting_email'] = implode(', ', $valid);
        $new['form_to_emails'] = $new['reporting_email'];

        foreach (['whatconverts', 'analytics'] as $key) {
            $new[$key] = one_line($in($key));
        }
        foreach (['chat_script', 'header_scripts', 'footer_scripts'] as $key) {
            $new[$key] = (string) ($_POST[$key] ?? '');
        }

        $postedPractices = $_POST['practices'] ?? [];
        $practices = [];
        if (!is_array($postedPractices)) {
            $errors['practices'] = 'Practice information must be submitted as a list.';
        } else {
            foreach ($postedPractices as $index => $practice) {
                if (!is_array($practice)) {
                    continue;
                }
                $name = one_line((string) ($practice['name'] ?? ''));
                $location = one_line((string) ($practice['location'] ?? ''));
                $locationUrl = trim((string) ($practice['location_url'] ?? ''));
                $phoneNumber = one_line((string) ($practice['phone_number'] ?? ''));
                $phoneUrl = preg_replace('/\s+/', '', (string) ($practice['phone_url'] ?? ''));
                $description = one_line((string) ($practice['description'] ?? ''));
                $officeHours = trim((string) ($practice['office_hours'] ?? ''));
                $rowIndex = (int) $index;
                $prefix = 'practice_' . $rowIndex . '_';

                if ($name === '') {
                    $errors[$prefix . 'name'] = 'Enter a practice name.';
                }
                if ($location === '') {
                    $errors[$prefix . 'location'] = 'Enter a location.';
                }
                if ($locationUrl !== '' && (!filter_var($locationUrl, FILTER_VALIDATE_URL) || !preg_match('#^https://#i', $locationUrl))) {
                    $errors[$prefix . 'location_url'] = 'Enter a full https:// link.';
                }
                if ($phoneUrl !== '') {
                    if (!preg_match('/^tel:/i', $phoneUrl)) {
                        $phoneUrl = 'tel:' . $phoneUrl;
                    }
                    $phoneUrl = 'tel:' . preg_replace('/[^0-9+]/', '', substr($phoneUrl, 4));
                    if (!preg_match('/^tel:\+?[0-9]{7,15}$/', $phoneUrl)) {
                        $errors[$prefix . 'phone_url'] = 'Use a phone link such as tel:+13055550142.';
                    }
                }

                $practices[$rowIndex] = [
                    'name' => $name,
                    'location' => $location,
                    'location_url' => $locationUrl,
                    'phone_number' => $phoneNumber,
                    'phone_url' => $phoneUrl,
                    'description' => $description,
                    'office_hours' => $officeHours,
                ];
            }
        }
        if (!$practices) {
            $errors['practices'] = 'Add at least one practice.';
        }
        $new['practices'] = $practices;
        $primary = $practices[0] ?? [];
        $new['location_line1'] = (string) ($primary['location'] ?? '');
        $new['location_line2'] = '';
        $new['location_url'] = practice_directions_url($primary);
        $new['map_embed_src'] = practice_map_src($primary);
        $new['phone_display'] = (string) ($primary['phone_number'] ?? '');
        $new['phone_link'] = (string) ($primary['phone_url'] ?? '');

        // reCAPTCHA
        $new['recaptcha_site_key'] = $in('recaptcha_site_key');
        $score = $in('recaptcha_min_score');
        $new['recaptcha_min_score'] = in_array($score, ['0.3', '0.5', '0.7', '0.9'], true) ? $score : '0.5';

        // Mailgun
        $new['mailgun_region'] = $in('mailgun_region') === 'eu' ? 'eu' : 'us';
        $domain = strtolower($in('mailgun_domain'));
        if ($domain !== '' && !preg_match('/^(?=.{3,253}$)([a-z0-9-]+\.)+[a-z]{2,}$/', $domain)) {
            $errors['mailgun_domain'] = 'Enter just the domain, e.g. mg.yourpractice.com.';
        }
        $new['mailgun_domain'] = $domain;
        $from = $in('mailgun_from_email');
        if ($from !== '' && !filter_var($from, FILTER_VALIDATE_EMAIL)) {
            $errors['mailgun_from_email'] = 'Enter a valid email address.';
        } elseif ($from !== '' && $domain !== '') {
            $fromDomain = strtolower(substr(strrchr($from, '@'), 1));
            if ($fromDomain !== $domain && !str_ends_with($domain, '.' . $fromDomain)) {
                $errors['mailgun_from_email'] = 'Use an address on ' . $domain . ' or its parent domain so the email passes SPF/DKIM checks.';
            }
        }
        $new['mailgun_from_email'] = $from;

        // secrets: blank keeps the saved value, "remove" clears it
        foreach (SECRET_KEYS as $k) {
            if (!empty($_POST[$k . '_clear'])) {
                $new[$k] = '';
            } elseif ($in($k) !== '') {
                $new[$k] = $in($k);
            }
        }
        if ($new['recaptcha_site_key'] !== '' && $new['recaptcha_site_key'] === $new['recaptcha_secret_key']) {
            $errors['recaptcha_secret_key'] = 'The secret key must be different from the site key.';
        }

        if ($errors) {
            $form = $new;
            $flash = ['err', 'Nothing was saved. Fix the highlighted fields.'];
        } elseif (!$writable || !save_settings($new)) {
            $form = $new;
            $flash = ['err', 'Could not write to the data folder. Make it writable by the web server (e.g. chmod 750 and owned by the PHP user).'];
        } else {
            redirect_self(['ok', 'Settings saved. The site is using them now.']);
        }
    } elseif ($action === 'test_mail') {
        [$ok, $msg] = send_mailgun(
            'Test email from ' . s('office_name') . ' website',
            "This is a test from the website settings dashboard.\n\nIf you received it, appointment requests will be delivered to: " . s('form_to_emails') . "\n\nSent " . date('r')
        );
        redirect_self([$ok ? 'ok' : 'err', $ok ? 'Test email sent to ' . s('form_to_emails') . '. Check the inbox (and spam).' : $msg]);
    } elseif ($action === 'password') {
        $cur = (string) ($_POST['current'] ?? '');
        $p1 = (string) ($_POST['new'] ?? '');
        if (!password_verify($cur, auth()['hash'] ?? '')) {
            $errors['current'] = 'Current password is incorrect.';
        } elseif (strlen($p1) < 10) {
            $errors['new'] = 'Use at least 10 characters.';
        } elseif ($p1 !== (string) ($_POST['new2'] ?? '')) {
            $errors['new2'] = "The passwords don't match.";
        } elseif (write_php_array(AUTH_FILE, ['hash' => password_hash($p1, PASSWORD_DEFAULT)])) {
            redirect_self(['ok', 'Password changed.']);
        }
        if ($errors) {
            $flash = ['err', 'Password not changed.'];
        }
    }
}

$token = csrf_token();

// ---------- view helpers ----------
function err(array $errors, string $k): string
{
    return isset($errors[$k]) ? '<p class="ferr" id="' . e($k) . '-err">' . e($errors[$k]) . '</p>' : '';
}
function field(array $form, array $errors, string $k, string $label, string $help = '', string $type = 'text', string $extra = ''): string
{
    $inv = isset($errors[$k]) ? ' aria-invalid="true" aria-describedby="' . e($k) . '-err"' : '';
    return '<label class="fld' . (isset($errors[$k]) ? ' bad' : '') . '"><span class="lbl">' . e($label) . '</span>'
        . '<input type="' . e($type) . '" name="' . e($k) . '" id="' . e($k) . '" value="' . e($form[$k] ?? '') . '"' . $inv . ' ' . $extra . '>'
        . ($help ? '<span class="help">' . $help . '</span>' : '') . err($errors, $k) . '</label>';
}
function secret_field(array $errors, string $k, string $label, string $help): string
{
    $saved = s($k);
    $ph = $saved !== '' ? 'Saved, ends in ' . substr($saved, -4) . '. Leave blank to keep.' : 'Not set';
    return '<div class="fld' . (isset($errors[$k]) ? ' bad' : '') . '"><label class="lbl" for="' . e($k) . '">' . e($label) . '</label>'
        . '<div class="secret"><input type="password" name="' . e($k) . '" id="' . e($k) . '" autocomplete="new-password" spellcheck="false" placeholder="' . e($ph) . '">'
        . '<button type="button" class="reveal" data-for="' . e($k) . '" aria-label="Show ' . e($label) . '">Show</button></div>'
        . '<span class="help">' . $help . '</span>'
        . ($saved !== '' ? '<label class="clr"><input type="checkbox" name="' . e($k) . '_clear" value="1"> Remove the saved key</label>' : '')
        . err($errors, $k) . '</div>';
}
function chip(bool $ok, string $yes, string $no): string
{
    return '<span class="chip ' . ($ok ? 'on' : 'off') . '"><i></i>' . e($ok ? $yes : $no) . '</span>';
}
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Site Settings · <?= e(s('office_name')) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Poppins:wght@400;500;600&display=swap">
<style>
/* Admin: navy sidebar with section links, cards per settings group, sticky save bar. Uses the site's palette. */
:root { --navy: #0a1960; --navy-deep: #060f3d; --tint: #eaecf5; --line: #d0d4e8; --gold: #dfb200; --ink: #0e1630; --text: #474747; --muted: #6b7088; --bg: #f5f6fa; --white: #fff; --ok: #157347; --ok-bg: #e5f4ec; --bad: #b42318; --bad-bg: #fdecea; color-scheme: light }
* { box-sizing: border-box }
body { margin: 0; background: var(--bg); color: var(--text); font: 400 15px/1.6 "Poppins", "Segoe UI", system-ui, sans-serif }
h1, h2 { font-family: "DM Serif Display", Georgia, serif; font-weight: 400; color: var(--ink); margin: 0; line-height: 1.1 }
h1 { font-size: 2rem } h2 { font-size: 1.45rem }
a { color: var(--navy) }
:focus-visible { outline: 3px solid var(--gold); outline-offset: 2px; border-radius: 6px }
.shell { display: grid; grid-template-columns: 250px minmax(0, 1fr); min-height: 100vh }
aside { background: var(--navy); color: #c9cee6; padding: 26px 18px; position: sticky; top: 0; height: 100vh; display: flex; flex-direction: column; gap: 22px }
.brand { color: #fff; font: 400 1.35rem/1.2 "DM Serif Display", Georgia, serif }
.brand small { display: block; font: 500 .7rem/1.4 Poppins, sans-serif; letter-spacing: .14em; text-transform: uppercase; color: var(--gold); margin-top: 6px }
aside nav { display: grid; gap: 2px }
aside nav a { color: #c9cee6; text-decoration: none; padding: 9px 12px; border-radius: 10px; font-size: .9rem }
aside nav a:hover { background: rgba(255,255,255,.08); color: #fff }
aside .bottom { margin-top: auto; display: grid; gap: 10px; font-size: .85rem }
aside .bottom a { color: #fff }
aside form button { background: none; border: 1px solid rgba(255,255,255,.3); color: #fff; padding: 8px 12px; border-radius: 10px; cursor: pointer; font: inherit; width: 100% }
main { padding: 32px clamp(16px, 4vw, 48px) 120px; max-width: 980px; width: 100% }
.top { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: end; gap: 16px; margin-bottom: 22px }
.top p { margin: 6px 0 0 }
.status { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 22px }
.chip { display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 999px; font-size: .8rem; font-weight: 500; background: var(--white); border: 1px solid var(--line) }
.chip i { width: 8px; height: 8px; border-radius: 50%; background: currentColor }
.chip.on { color: var(--ok) } .chip.off { color: var(--bad) }
.flash { padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: .9rem }
.flash.ok { background: var(--ok-bg); color: var(--ok) } .flash.err { background: var(--bad-bg); color: var(--bad) }
.card { background: var(--white); border: 1px solid var(--line); border-radius: 18px; padding: 26px; margin-bottom: 20px; scroll-margin-top: 20px }
.card header { display: flex; justify-content: space-between; align-items: start; gap: 16px; margin-bottom: 18px }
.card header p { margin: 4px 0 0; font-size: .85rem; color: var(--muted) }
.grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 20px }
.grid .full { grid-column: 1 / -1 }
@media (max-width: 700px) { .grid { grid-template-columns: 1fr } }
.fld { display: grid; gap: 6px; align-content: start; min-width: 0 }
.lbl { font-size: .82rem; font-weight: 600; color: var(--ink) }
input[type=text], input[type=email], input[type=url], input[type=password], input[type=tel], select, textarea { font: 400 .92rem "Poppins", sans-serif; padding: 11px 13px; border: 1px solid var(--line); border-radius: 10px; background: #fafbfe; color: var(--ink); width: 100%; min-width: 0 }
textarea { min-height: 110px; resize: vertical; font-family: ui-monospace, Menlo, Consolas, monospace; font-size: .8rem }
input:focus, select:focus, textarea:focus { border-color: var(--navy); outline: none; box-shadow: 0 0 0 3px rgba(10,25,96,.12) }
.bad input, .bad textarea, .bad select { border-color: var(--bad) }
.help { font-size: .76rem; color: var(--muted); line-height: 1.45 }
.help code { background: var(--tint); padding: 1px 5px; border-radius: 4px; font-size: .74rem }
.ferr { margin: 0; font-size: .78rem; color: var(--bad) }
.secret { display: flex; gap: 8px } .secret input { flex: 1 }
.reveal { border: 1px solid var(--line); background: var(--white); border-radius: 10px; padding: 0 12px; cursor: pointer; font: 500 .78rem Poppins, sans-serif; color: var(--navy) }
.clr { display: flex; gap: 8px; align-items: center; font-size: .78rem; color: var(--muted) }
.preview { margin-top: 4px; border: 1px solid var(--line); border-radius: 12px; overflow: hidden; height: 220px; background: var(--tint); display: grid; place-items: center; color: var(--muted); font-size: .82rem }
.preview iframe { width: 100%; height: 100%; border: 0 }
.practice-list { display: grid; gap: 12px; margin-bottom: 16px }
.practice-item { position: relative; min-width: 0; margin: 0; padding: 18px; border: 1px solid var(--line); border-radius: 14px }
.practice-item legend { padding: 0 8px; color: var(--ink); font-size: .95rem; font-weight: 600 }
.practice-item .remove-practice { position: absolute; top: 8px; right: 12px; padding: 9px 14px; font-size: .75rem }
.practice-item .grid { padding-top: 4px }
.practice-item textarea { min-height: 150px; font-family: Poppins, sans-serif; font-size: .92rem }
.practice-map { height: 190px }
.fld textarea[id^="chat_script"], .fld textarea[id^="header_scripts"], .fld textarea[id^="footer_scripts"] { min-height: 160px }
.derived { font-size: .78rem; color: var(--muted) } .derived code { color: var(--ink) }
.btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; font: 600 .85rem/1 Poppins, sans-serif; padding: 13px 20px; border-radius: 999px; border: 1px solid transparent; cursor: pointer; text-decoration: none; white-space: nowrap }
.btn-gold { background: var(--gold); color: var(--ink) } .btn-navy { background: var(--navy); color: #fff } .btn-line { background: var(--white); border-color: var(--line); color: var(--navy) }
.btn[disabled] { opacity: .5; cursor: not-allowed }
.savebar { position: fixed; left: 250px; right: 0; bottom: 0; background: rgba(255,255,255,.96); border-top: 1px solid var(--line); padding: 14px clamp(16px, 4vw, 48px); display: flex; justify-content: space-between; align-items: center; gap: 12px; z-index: 10 }
.savebar span { font-size: .82rem; color: var(--muted) }
.inline { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; margin-top: 16px; padding-top: 16px; border-top: 1px dashed var(--line); font-size: .82rem; color: var(--muted) }
@media (max-width: 860px) { .shell { grid-template-columns: 1fr } aside { position: static; height: auto } aside nav { grid-template-columns: repeat(2, 1fr) } .savebar { left: 0 } }
/* auth screens */
.auth { min-height: 100vh; display: grid; place-items: center; padding: 24px; background: var(--navy) }
.auth .card { width: min(420px, 100%); margin: 0; display: grid; gap: 16px }
.auth .brand { color: var(--ink) }
.warn { background: #fff7d7; color: #6b5200; padding: 12px 14px; border-radius: 10px; font-size: .84rem }
</style>
</head>
<body>
<?php if ($view === 'setup' || $view === 'login'): ?>
<div class="auth">
  <form class="card" method="post" action="admin.php" novalidate>
    <div class="brand"><?= e(s('office_name')) ?><small>Website settings</small></div>
    <?php if ($flash): ?><div class="flash <?= e($flash[0]) ?>"><?= e($flash[1]) ?></div><?php endif; ?>
    <input type="hidden" name="csrf" value="<?= e($token) ?>">
    <?php if ($view === 'setup'): ?>
      <input type="hidden" name="action" value="setup">
      <h1>Create admin password</h1>
      <p style="margin:0;font-size:.88rem">First-time setup. This password protects the settings dashboard.</p>
      <?php if (!$writable): ?><div class="warn">The <code>data</code> folder isn't writable by PHP. Create it next to admin.php and give the web server write access, then reload.</div><?php endif; ?>
      <label class="fld<?= isset($errors['password']) ? ' bad' : '' ?>"><span class="lbl">Password</span><input type="password" name="password" autocomplete="new-password" minlength="10" required><span class="help">At least 10 characters.</span><?= err($errors, 'password') ?></label>
      <label class="fld<?= isset($errors['password2']) ? ' bad' : '' ?>"><span class="lbl">Confirm password</span><input type="password" name="password2" autocomplete="new-password" required><?= err($errors, 'password2') ?></label>
      <button class="btn btn-navy" type="submit" <?= $writable ? '' : 'disabled' ?>>Create password</button>
    <?php else: ?>
      <input type="hidden" name="action" value="login">
      <h1>Sign in</h1>
      <label class="fld<?= isset($errors['password']) ? ' bad' : '' ?>"><span class="lbl">Admin password</span><input type="password" name="password" autocomplete="current-password" required autofocus><?= err($errors, 'password') ?></label>
      <button class="btn btn-navy" type="submit">Sign in</button>
    <?php endif; ?>
  </form>
</div>
<?php else: ?>
<div class="shell">
  <aside>
    <div class="brand"><?= e(s('office_name')) ?><small>Website settings</small></div>
    <nav aria-label="Settings sections">
      <a href="#site-info">Site info</a>
      <a href="#practices">Practices</a>
      <a href="#office">Office &amp; doctor</a>
      <a href="#form">Contact form</a>
      <a href="#recaptcha">reCAPTCHA v3</a>
      <a href="#mailgun">Mailgun</a>
      <a href="#account">Password</a>
    </nav>
    <div class="bottom">
      <a href="index.php" target="_blank" rel="noopener">View website ↗</a>
      <form method="post" action="admin.php"><input type="hidden" name="csrf" value="<?= e($token) ?>"><input type="hidden" name="action" value="logout"><button type="submit">Sign out</button></form>
    </div>
  </aside>

  <main>
    <div class="top">
      <div><h1>Website settings</h1><p>Changes apply to every page as soon as you save.</p></div>
    </div>
    <div class="status" aria-label="Setup status">
      <?= chip(mailgun_ready(), 'Mailgun ready', 'Mailgun not set up') ?>
      <?= chip(recaptcha_ready(), 'reCAPTCHA on', 'reCAPTCHA off') ?>
      <?= chip(s('map_embed_src') !== '', 'Map embedded', 'No map embed') ?>
      <?= chip($writable, 'Settings writable', 'Data folder not writable') ?>
    </div>
    <?php if ($flash): ?><div class="flash <?= e($flash[0]) ?>" role="status"><?= e($flash[1]) ?></div><?php endif; ?>

    <form method="post" action="admin.php" id="settings" novalidate>
      <input type="hidden" name="csrf" value="<?= e($token) ?>">
      <input type="hidden" name="action" value="save">

      <section class="card" id="site-info">
        <header><div><h2>Site info</h2><p>Site-wide identity and integration values. WhatConverts and Analytics are stored for later front-end use.</p></div></header>
        <div class="grid">
          <?= field($form, $errors, 'site_name', 'Site name', 'Used in the site title, logo text and practice communication.', 'text', 'required maxlength="80"') ?>
          <?= field($form, $errors, 'reporting_email', 'Reporting email', 'Receives appointment request notifications.', 'text', 'maxlength="500" spellcheck="false" autocomplete="email"') ?>
          <?= field($form, $errors, 'recaptcha_site_key', 'V3 site key', 'Public reCAPTCHA v3 key.', 'text', 'maxlength="200" spellcheck="false" autocomplete="off"') ?>
          <div><?= secret_field($errors, 'recaptcha_secret_key', 'V3 secret key', 'Private reCAPTCHA v3 key. It is never sent to the browser.') ?></div>
          <?= field($form, $errors, 'whatconverts', 'WhatConverts', 'Stored for a future front-end integration.', 'text', 'maxlength="500" spellcheck="false" autocomplete="off"') ?>
          <?= field($form, $errors, 'analytics', 'Analytics', 'Stored for a future front-end integration.', 'text', 'maxlength="500" spellcheck="false" autocomplete="off"') ?>
          <label class="fld full"><span class="lbl">Chat script</span><textarea name="chat_script" id="chat_script" maxlength="30000" spellcheck="false"><?= e($form['chat_script'] ?? '') ?></textarea><span class="help">Paste the complete trusted provider snippet; it is included verbatim on public pages before the footer scripts.</span></label>
          <label class="fld full"><span class="lbl">Header scripts</span><textarea name="header_scripts" id="header_scripts" maxlength="30000" spellcheck="false"><?= e($form['header_scripts'] ?? '') ?></textarea><span class="help">Paste complete trusted HTML snippets. They are included verbatim in the public page head.</span></label>
          <label class="fld full"><span class="lbl">Footer scripts</span><textarea name="footer_scripts" id="footer_scripts" maxlength="30000" spellcheck="false"><?= e($form['footer_scripts'] ?? '') ?></textarea><span class="help">Paste complete trusted HTML snippets. They are included verbatim before the closing body tag on public pages.</span></label>
        </div>
      </section>

      <section class="card" id="practices">
        <header><div><h2>Practice info</h2><p>Add one entry for each office. Each location gets its own directions link, map and hours on the website.</p></div></header>
        <?php if (isset($errors['practices'])): ?><p class="ferr" role="alert"><?= e($errors['practices']) ?></p><?php endif; ?>
        <div class="practice-list" id="practice-list">
          <?php foreach (($form['practices'] ?? []) as $index => $practice): ?>
            <?php $practice = is_array($practice) ? $practice : []; ?>
            <fieldset class="practice-item">
              <legend>Practice <span data-practice-number><?= $index + 1 ?></span></legend>
              <button type="button" class="btn btn-line remove-practice">Remove practice</button>
              <div class="grid">
                <?php foreach (['name' => 'Practice name', 'location' => 'Location', 'location_url' => 'Location URL', 'phone_number' => 'Phone number', 'phone_url' => 'Phone number URL', 'description' => 'Practice description'] as $key => $label): ?>
                  <?php $errorKey = 'practice_' . $index . '_' . $key; ?>
                  <label class="fld<?= isset($errors[$errorKey]) ? ' bad' : '' ?>">
                    <span class="lbl"><?= e($label) ?></span>
                    <input type="<?= $key === 'location_url' ? 'url' : 'text' ?>" name="practices[<?= $index ?>][<?= e($key) ?>]" id="<?= e($errorKey) ?>" value="<?= e($practice[$key] ?? '') ?>" maxlength="500"<?= in_array($key, ['name', 'location'], true) ? ' required' : '' ?><?= isset($errors[$errorKey]) ? ' aria-invalid="true" aria-describedby="' . e($errorKey) . '-err"' : '' ?>>
                    <?= err($errors, $errorKey) ?>
                  </label>
                <?php endforeach; ?>
                <?php $hoursError = 'practice_' . $index . '_office_hours'; ?>
                <label class="fld full<?= isset($errors[$hoursError]) ? ' bad' : '' ?>"><span class="lbl">Office hours</span><textarea name="practices[<?= $index ?>][office_hours]" id="<?= e($hoursError) ?>" maxlength="3000"><?= e($practice['office_hours'] ?? '') ?></textarea><?= err($errors, $hoursError) ?></label>
                <div class="fld full"><span class="lbl">Location map</span><div class="preview practice-map"><?php if (practice_map_src($practice)): ?><iframe src="<?= e(practice_map_src($practice)) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map preview for <?= e($practice['name'] ?? 'practice') ?>"></iframe><?php else: ?>Enter a location to preview its Google Map<?php endif; ?></div><span class="help">The map embed is generated from the Location field.</span></div>
              </div>
            </fieldset>
          <?php endforeach; ?>
        </div>
        <button type="button" class="btn btn-line" id="add-practice">Add another practice</button>
        <template id="practice-template">
          <fieldset class="practice-item">
            <legend>Practice <span data-practice-number></span></legend>
            <button type="button" class="btn btn-line remove-practice">Remove practice</button>
            <div class="grid">
              <label class="fld"><span class="lbl">Practice name</span><input type="text" name="practices[__INDEX__][name]" maxlength="500" required></label>
              <label class="fld"><span class="lbl">Location</span><input type="text" name="practices[__INDEX__][location]" maxlength="500" required></label>
              <label class="fld"><span class="lbl">Location URL</span><input type="url" name="practices[__INDEX__][location_url]" maxlength="500"></label>
              <label class="fld"><span class="lbl">Phone number</span><input type="text" name="practices[__INDEX__][phone_number]" maxlength="500"></label>
              <label class="fld"><span class="lbl">Phone number URL</span><input type="text" name="practices[__INDEX__][phone_url]" maxlength="500"></label>
              <label class="fld"><span class="lbl">Practice description</span><input type="text" name="practices[__INDEX__][description]" maxlength="500"></label>
              <label class="fld full"><span class="lbl">Office hours</span><textarea name="practices[__INDEX__][office_hours]" maxlength="3000"></textarea></label>
              <div class="fld full"><span class="lbl">Location map</span><div class="preview practice-map">Enter a location to preview its Google Map</div><span class="help">The map embed is generated from the Location field.</span></div>
            </div>
          </fieldset>
        </template>
      </section>

      <section class="card" id="office">
        <header><div><h2>Office &amp; doctor</h2><p>Doctor information shown on the homepage.</p></div></header>
        <div class="grid">
          <?= field($form, $errors, 'doctor_name', "Doctor's name", 'Include the title, e.g. <code>Dr. Jane Smith</code>.', 'text', 'maxlength="80"') ?>
          <?= field($form, $errors, 'doctor_credentials', 'Doctor credentials', 'e.g. <code>DMD</code> or <code>DDS, MS</code>', 'text', 'maxlength="40"') ?>
        </div>
      </section>

      <section class="card" id="form">
        <header><div><h2>Contact form</h2><p>Customize the subject line for appointment request emails. The reporting email is set under Site Info.</p></div></header>
        <div class="grid">
          <div class="full"><?= field($form, $errors, 'form_subject', 'Email subject', 'The patient\'s name is added to the end.', 'text', 'maxlength="120"') ?></div>
        </div>
      </section>

      <section class="card" id="recaptcha">
        <header><div><h2>reCAPTCHA v3</h2><p>Blocks spam without a checkbox. Create keys at <a href="https://www.google.com/recaptcha/admin/create" target="_blank" rel="noopener">google.com/recaptcha/admin</a> (choose v3, add this domain).</p></div><?= chip(recaptcha_ready(), 'On', 'Off') ?></header>
        <div class="grid">
          <label class="fld"><span class="lbl">Minimum score</span>
            <select name="recaptcha_min_score" id="recaptcha_min_score">
              <?php foreach (['0.3' => '0.3 · lenient', '0.5' => '0.5 · recommended', '0.7' => '0.7 · strict', '0.9' => '0.9 · very strict'] as $v => $l): ?>
                <option value="<?= e($v) ?>" <?= s('recaptcha_min_score') === $v ? 'selected' : '' ?>><?= e($l) ?></option>
              <?php endforeach; ?>
            </select>
            <span class="help">Submissions scoring below this are rejected. 1.0 is very likely human.</span></label>
        </div>
      </section>

      <section class="card" id="mailgun">
        <header><div><h2>Mailgun</h2><p>Sends form emails through the Mailgun API. Find the key under Mailgun → API Security, and the domain under Sending → Domains.</p></div><?= chip(mailgun_ready(), 'Ready', 'Not set up') ?></header>
        <div class="grid">
          <label class="fld"><span class="lbl">Region</span>
            <select name="mailgun_region" id="mailgun_region">
              <option value="us" <?= s('mailgun_region') !== 'eu' ? 'selected' : '' ?>>US (api.mailgun.net)</option>
              <option value="eu" <?= s('mailgun_region') === 'eu' ? 'selected' : '' ?>>EU (api.eu.mailgun.net)</option>
            </select><span class="help">Must match where your Mailgun domain was created.</span></label>
          <?= field($form, $errors, 'mailgun_domain', 'Sending domain', 'e.g. <code>mg.yourpractice.com</code>', 'text', 'maxlength="253" spellcheck="false" autocomplete="off"') ?>
          <div class="full"><?= secret_field($errors, 'mailgun_api_key', 'API key', 'Private key, used only on the server. A domain-scoped Sending API key is safest.') ?></div>
          <?= field($form, $errors, 'mailgun_from_email', 'From email', 'An address on your sending domain, e.g. <code>website@mg.yourpractice.com</code>', 'email', 'maxlength="200" spellcheck="false"') ?>
          <?= field($form, $errors, 'mailgun_from_name', 'From name', 'Defaults to the office name.', 'text', 'maxlength="80"') ?>
        </div>
      </section>
    </form>

    <section class="card">
      <header><div><h2>Send a test email</h2><p>Uses the saved Mailgun settings and sends to the form admin email. Save any changes first.</p></div></header>
      <form method="post" action="admin.php" class="inline" style="margin:0;padding:0;border:0">
        <input type="hidden" name="csrf" value="<?= e($token) ?>"><input type="hidden" name="action" value="test_mail">
        <button class="btn btn-line" type="submit" <?= mailgun_ready() ? '' : 'disabled' ?>>Send test email</button>
        <span><?= mailgun_ready() ? 'Sends to ' . e(s('form_to_emails')) : 'Add the API key, domain, from email and form admin email to enable.' ?></span>
      </form>
    </section>

    <section class="card" id="account">
      <header><div><h2>Change password</h2></div></header>
      <form method="post" action="admin.php" class="grid" novalidate>
        <input type="hidden" name="csrf" value="<?= e($token) ?>"><input type="hidden" name="action" value="password">
        <label class="fld full<?= isset($errors['current']) ? ' bad' : '' ?>"><span class="lbl">Current password</span><input type="password" name="current" autocomplete="current-password"><?= err($errors, 'current') ?></label>
        <label class="fld<?= isset($errors['new']) ? ' bad' : '' ?>"><span class="lbl">New password</span><input type="password" name="new" autocomplete="new-password"><span class="help">At least 10 characters.</span><?= err($errors, 'new') ?></label>
        <label class="fld<?= isset($errors['new2']) ? ' bad' : '' ?>"><span class="lbl">Confirm new password</span><input type="password" name="new2" autocomplete="new-password"><?= err($errors, 'new2') ?></label>
        <div class="full"><button class="btn btn-line" type="submit">Change password</button></div>
      </form>
    </section>
  </main>
</div>
<div class="savebar"><span id="dirty">All changes saved</span><button class="btn btn-gold" type="submit" form="settings">Save settings</button></div>
<script>
(() => {
  document.querySelectorAll('.reveal').forEach(b => b.addEventListener('click', () => {
    const i = document.getElementById(b.dataset.for); const show = i.type === 'password';
    i.type = show ? 'text' : 'password'; b.textContent = show ? 'Hide' : 'Show';
  }));
  const form = document.getElementById('settings'), dirty = document.getElementById('dirty');
  let changed = <?= $errors && $action === 'save' ? 'true' : 'false' ?>;
  const mark = () => { changed = true; dirty.textContent = 'Unsaved changes'; dirty.style.color = '#b42318'; };
  if (changed) mark();
  form.addEventListener('input', mark);
  form.addEventListener('submit', () => { changed = false; });
  addEventListener('beforeunload', e => { if (changed) { e.preventDefault(); e.returnValue = ''; } });

  const practiceList = document.getElementById('practice-list');
  const practiceTemplate = document.getElementById('practice-template');
  const addPractice = document.getElementById('add-practice');
  let nextPractice = [...practiceList.querySelectorAll('input[name^="practices["]')].reduce((next, input) => {
    const match = input.name.match(/^practices\[(\d+)\]/);
    return match ? Math.max(next, Number(match[1]) + 1) : next;
  }, 0);
  const updateMap = item => {
    const locationInput = item.querySelector('input[name$="[location]"]');
    const preview = item.querySelector('.practice-map');
    const location = locationInput.value.trim();
    preview.replaceChildren();
    if (!location) {
      preview.textContent = 'Enter a location to preview its Google Map';
      return;
    }
    const frame = document.createElement('iframe');
    frame.src = 'https://www.google.com/maps?q=' + encodeURIComponent(location) + '&output=embed';
    frame.loading = 'lazy';
    frame.referrerPolicy = 'no-referrer-when-downgrade';
    frame.title = 'Map preview';
    preview.appendChild(frame);
  };
  practiceList.addEventListener('input', event => {
    if (event.target.matches('input[name$="[location]"]')) {
      updateMap(event.target.closest('.practice-item'));
    }
  });
  practiceList.addEventListener('click', event => {
    const remove = event.target.closest('.remove-practice');
    if (!remove) return;
    remove.closest('.practice-item').remove();
    practiceList.querySelectorAll('.practice-item').forEach((item, index) => {
      item.querySelector('[data-practice-number]').textContent = index + 1;
    });
    mark();
  });
  addPractice.addEventListener('click', () => {
    const item = practiceTemplate.content.firstElementChild.cloneNode(true);
    item.innerHTML = item.innerHTML.replaceAll('__INDEX__', String(nextPractice++));
    item.querySelector('[data-practice-number]').textContent = practiceList.querySelectorAll('.practice-item').length + 1;
    practiceList.appendChild(item);
    mark();
    item.querySelector('input').focus();
  });
})();
</script>
<?php endif; ?>
</body>
</html>

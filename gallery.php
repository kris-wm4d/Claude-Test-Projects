<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/bootstrap.php';
send_page_headers();
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Witness the Transformation | <?= e(s('office_name')) ?></title>
<meta name="description" content="Every smile tells a story. See the difference personalized dental care can make, from where our patients started to the beautiful, confident smiles they have today. Real smiles, real results, and confidence restored, one patient at a time.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Poppins:wght@200;300;400;500;600&display=swap">
<link rel="stylesheet" href="assets/styles.css">
<style>html, body { margin: 0 } img { max-width: 100% } [hidden] { display: none !important }</style>
</head>
<body>
<div class="masthead"><div class="wrap mh">
  <span class="mh-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg>Most PPO plans accepted</span>
  <span class="mh-item mh-hide"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg>Monthly payment plans available</span>
  <a class="mh-link" href="<?= e(phone_href()) ?>">Call us <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
</div></div>

<header class="site">
  <div class="wrap hdr">
    <a class="logo" href="index.php" aria-label="Home"><img src="Uploads/2026/logo.webp" alt="<?= e(s('office_name')) ?>" width="250" height="169"></a>
    <nav class="main" aria-label="Main">
      <a href="index.php">Home</a>
      <div class="nav-item">
        <a href="index.php#welcome">About</a>
        <div class="subnav">
          <a href="index.php#doctor">Meet the Doctor</a>
          <a href="index.php#welcome">Meet The Team</a>
          <a href="financing.php">Financing</a>
          <a href="index.php#technology">Technology</a>
          <a href="gallery.php">Office Tour</a>
        </div>
      </div>
      <a href="index.php#services">Implants</a>
      <a href="index.php#services">Cosmetic</a>
      <a href="index.php#services">General</a>
      <div class="nav-item">
        <a href="index.php#results">Results</a>
        <div class="subnav">
          <a href="testimonials.php">Testimonials</a>
          <a href="gallery.php" aria-current="page">Gallery</a>
        </div>
      </div>
      <a href="index.php#visit">Contact</a>
    </nav>
    <a class="phone" href="<?= e(phone_href()) ?>"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1z"/></svg><?= e(s('phone_display')) ?></a>
    <button class="btn btn-gold" data-open>Book Appointment</button>
    <button class="burger" id="burger" aria-expanded="false" aria-controls="drawer" aria-label="Menu"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
  </div>
  <nav class="wrap drawer" id="drawer" aria-label="Mobile" hidden>
    <a href="index.php">Home</a>
    <div class="drawer-group">
      <a href="index.php#welcome">About</a>
      <div class="drawer-subnav">
        <a href="index.php#doctor">Meet the Doctor</a>
        <a href="index.php#welcome">Meet The Team</a>
        <a href="financing.php">Financing</a>
        <a href="index.php#technology">Technology</a>
        <a href="gallery.php">Office Tour</a>
      </div>
    </div>
    <a href="index.php#services">Implants</a>
    <a href="index.php#services">Cosmetic</a>
    <a href="index.php#services">General</a>
    <div class="drawer-group">
      <a href="index.php#results">Results</a>
      <div class="drawer-subnav">
        <a href="testimonials.php">Testimonials</a>
        <a href="gallery.php" aria-current="page">Gallery</a>
      </div>
    </div>
    <a href="index.php#visit">Contact</a>
  </nav>
</header>

<main id="top">
  <section class="band tint gallery-head">
    <div class="wrap gallery-intro">
      <p class="eyebrow">Before &amp; After</p>
      <h1>Witness the Transformation</h1>
      <p class="lede">Every smile tells a story. See the difference personalized dental care can make, from where our patients started to the beautiful, confident smiles they have today. Real smiles, real results, and confidence restored, one patient at a time.</p>
    </div>
  </section>

  <section class="band">
    <div class="wrap">
      <div class="gallery-grid" aria-label="Before and after smile transformations">
        <button class="gallery-item" type="button" aria-label="View dental implant before and after case 1">
          <img src="Uploads/2026/implants01-2.webp" alt="Dental implant smile transformation, before and after" loading="lazy" decoding="async" data-title="Dental implant smile transformation">
        </button>
        <button class="gallery-item" type="button" aria-label="View dental implant before and after case 2">
          <img src="Uploads/2026/implants02.webp" alt="Dental implant smile transformation, before and after" loading="lazy" decoding="async" data-title="Dental implant smile transformation">
        </button>
        <button class="gallery-item" type="button" aria-label="View dental implant before and after case 3">
          <img src="Uploads/2026/implants03.webp" alt="Dental implant smile transformation, before and after" loading="lazy" decoding="async" data-title="Dental implant smile transformation">
        </button>
      </div>
    </div>
  </section>
</main>

<div class="lightbox" id="lightbox" hidden>
  <div class="lightbox-panel" role="dialog" aria-modal="true" aria-label="Expanded gallery image">
    <button class="lightbox-close" type="button" aria-label="Close image">×</button>
    <div class="lightbox-image-wrap">
      <img class="lightbox-image" src="" alt="">
    </div>
    <div class="lightbox-caption">
      <span>Before &amp; After</span>
      <strong id="lightbox-title">Dental implant smile transformation</strong>
    </div>
  </div>
</div>

<footer>
  <div class="wrap ftr">
    <a class="logo" href="index.php" aria-label="Back to top"><img src="Uploads/2026/logo.webp" alt="<?= e(s('office_name')) ?>" width="250" height="169"></a>
    <nav aria-label="Footer"><a href="financing.php">Financing</a><a href="index.php#top">Disclaimer</a><a href="index.php#top">Privacy Policy</a></nav>
    <span>© Copyright 2026</span>
  </div>
</footer>

<div class="thumb" aria-label="Quick contact">
  <a class="btn btn-line" href="<?= e(sms_href()) ?>">Text</a>
  <a class="btn btn-line" href="<?= e(phone_href()) ?>">Call</a>
  <button class="btn btn-gold" data-open>Book</button>
</div>

<dialog id="dlg" aria-labelledby="dlg-h">
  <div class="m-in">
    <div class="m-top"><h2 id="dlg-h" style="font-size:1.9rem">Request appointment</h2><button class="x" id="dlg-x" aria-label="Close">×</button></div>
    <p class="sub">Call us now at <b><?= e(s('phone_display')) ?></b> or fill out the form below and we'll call you back.</p>
    <form class="req" id="req" novalidate>
      <input class="hp" type="text" name="website" id="f-website" tabindex="-1" autocomplete="off" aria-hidden="true">
      <label class="f">Full name<input name="name" id="f-name" required autocomplete="name"></label>
      <div class="row2">
        <label class="f">Phone<input name="phone" id="f-phone" type="tel" required autocomplete="tel" inputmode="tel"></label>
        <label class="f">Email<input name="email" id="f-email" type="email" required autocomplete="email"></label>
      </div>
      <label class="f">Message<textarea name="message" id="f-msg" required placeholder="What can we help you with?"></textarea></label>
      <label class="chk"><input type="checkbox" name="sms_consent" value="1" id="f-sms"><span>I agree to receive appointment reminders and updates by text from <?= e(s('office_name')) ?>. Message and data rates may apply. Reply STOP to opt out.</span></label>
      <label class="chk"><input type="checkbox" name="marketing_consent" value="1" id="f-mkt"><span>I agree to receive occasional offers by text and email from <?= e(s('office_name')) ?>. Consent is not required to book.</span></label>
      <?php if (recaptcha_ready()): ?><p class="help-recaptcha">This site is protected by reCAPTCHA and the Google <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Privacy Policy</a> and <a href="https://policies.google.com/terms" target="_blank" rel="noopener">Terms of Service</a> apply.</p><?php endif; ?>
      <p class="err" id="f-err" role="alert"></p>
      <button class="btn btn-gold" type="submit">Request Appointment</button>
    </form>
    <div class="done" id="done" hidden>
      <div class="ck"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg></div>
      <h3>Request sent</h3>
      <p class="sub" id="done-msg">Thanks, we received your request.</p>
      <button class="btn btn-royal" id="done-close">Close</button>
    </div>
  </div>
</dialog>

<script>window.SITE = <?= json_encode(['recaptchaSiteKey' => recaptcha_ready() ? s('recaptcha_site_key') : '', 'submitUrl' => 'submit.php'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES) ?>;</script>
<?php if (recaptcha_ready()): ?><script src="https://www.google.com/recaptcha/api.js?render=<?= e(rawurlencode(s('recaptcha_site_key'))) ?>" async defer></script><?php endif; ?>
<script src="assets/site.js"></script>
</body>
</html>

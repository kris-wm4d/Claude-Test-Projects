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
<?= s('header_scripts') ?>
</head>
<body>
<?php $site_base = ''; $site_current = 'gallery'; include __DIR__ . '/inc/site-header.php'; ?>

<main id="top">
  <section class="band tint gallery-head">
    <div class="wrap gallery-intro">
      <p class="eyebrow">Before &amp; After</p>
      <h1>Witness the <em class="heading-accent">Transformation</em></h1>
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

  <section class="band cta">
    <div class="wrap cta-grid">
      <div>
        <h2>Smile transformation starts here</h2>
        <p>Every smile is different. Talk with our team about your goals and explore treatment options that may be right for you.</p>
      </div>
      <div class="cta-actions">
        <a class="btn btn-gold" href="<?= e(phone_href()) ?>">Call Us</a>
        <button class="btn btn-line" data-open><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>Book Appointment</button>
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

<?php include __DIR__ . '/inc/site-footer.php'; ?>

<div class="thumb" aria-label="Quick contact">
  <a class="btn btn-gold" href="<?= e(phone_href()) ?>">Call</a>
  <a class="btn btn-line" href="<?= e(sms_href()) ?>">Text</a>
  <button class="btn btn-line" data-open><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>Book Appointment</button>
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
      <button class="btn btn-gold" type="submit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>Book Appointment</button>
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
<?= s('chat_script') ?>
<?= s('footer_scripts') ?>
</body>
</html>

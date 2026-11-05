<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/bootstrap.php';
send_page_headers();

$testimonials = [
    [
        'quote' => 'I came in worried about the cost. They laid out every option, explained what insurance might cover, and gave me time to decide without any pressure.',
        'treatment' => 'Treatment planning',
    ],
    [
        'quote' => 'My implant took a few months from start to finish, just as they explained at the consultation. The team checked in at every step, and I can eat comfortably again.',
        'treatment' => 'Dental implants',
    ],
    [
        'quote' => 'My kids were nervous before their first visit. The hygienist answered all their questions and made the appointment feel easy. Now they are happy to come back.',
        'treatment' => 'Family dentistry',
    ],
    [
        'quote' => 'Everyone was kind, the office felt calm, and nobody rushed me. I left knowing exactly what the next step was and what it would cost.',
        'treatment' => 'First visit',
    ],
    [
        'quote' => 'I needed a denture adjustment before a trip. They helped me find a time quickly and made sure everything felt right before I left.',
        'treatment' => 'Dentures',
    ],
    [
        'quote' => 'I wanted a natural-looking smile and was unsure where to start. We talked through the choices together, and I felt included in the plan.',
        'treatment' => 'Cosmetic dentistry',
    ],
    [
        'quote' => 'The front desk helped me understand my benefits and the monthly payment options. It was easier to make a plan once I knew the full cost.',
        'treatment' => 'Financing questions',
    ],
    [
        'quote' => 'I had avoided the dentist for years. They started with a conversation, explained my X-rays in plain language, and never made me feel judged.',
        'treatment' => 'First visit',
    ],
    [
        'quote' => 'The appointment was organized from check-in through checkout. I appreciated knowing what each part of the visit was for.',
        'treatment' => 'Routine care',
    ],
    [
        'quote' => 'I had questions about replacing a missing tooth and got clear answers about the options, timing, and what to expect at each visit.',
        'treatment' => 'Dental implants',
    ],
    [
        'quote' => 'Scheduling was straightforward, and the staff took time to listen before recommending anything. I felt comfortable asking questions.',
        'treatment' => 'Patient experience',
    ],
    [
        'quote' => 'The team explained how to care for my new smile at home and made sure I knew who to contact if I had questions afterward.',
        'treatment' => 'Follow-up care',
    ],
];
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Patient Testimonials | <?= e(s('office_name')) ?></title>
<meta name="description" content="Read patient stories about dental implants, dentures, family dentistry, and care at <?= e(s('office_name')) ?>.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Poppins:wght@200;300;400;500;600&display=swap">
<link rel="stylesheet" href="assets/styles.css">
<style>html, body { margin: 0 } img { max-width: 100% } [hidden] { display: none !important }</style>
</head>
<body class="testimonials-page">
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
          <a href="testimonials.php" aria-current="page">Testimonials</a>
          <a href="gallery.php">Gallery</a>
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
        <a href="testimonials.php" aria-current="page">Testimonials</a>
        <a href="gallery.php">Gallery</a>
      </div>
    </div>
    <a href="index.php#visit">Contact</a>
  </nav>
</header>

<main id="top">
  <section class="band tint testimonials-hero">
    <div class="wrap">
      <div class="testimonial-intro">
        <div>
          <h1 data-reveal>Patient stories</h1>
          <p class="lede">Read patient testimonials about dental implants, dentures, and family dental care at <?= e(s('office_name')) ?>.</p>
          <button class="btn btn-gold testimonial-intro-action" type="button" data-open>Book Appointment</button>
        </div>
        <div class="testimonial-mosaic" aria-label="Inside the practice" data-reveal>
          <img src="Uploads/2026/modern-reception.jpg" alt="Reception area at the dental practice" width="900" height="600" data-reveal>
          <img src="Uploads/2026/dr-jane-doe.jpg" alt="Dr. Jane Doe" width="600" height="800" data-reveal>
          <img src="Uploads/2026/treatment-room.jpg" alt="A treatment room at the practice" width="900" height="600" data-reveal>
          <img src="Uploads/2026/receiving-area.jpg" alt="Patient welcome area at the practice" width="900" height="600" data-reveal>
        </div>
      </div>
    </div>
  </section>

  <section class="band testimonial-section" aria-labelledby="testimonial-heading">
    <div class="wrap">
      <div class="testimonial-heading">
        <div data-reveal>
          <h2 id="testimonial-heading">What patients appreciate</h2>
        </div>
        <div class="testimonial-score" data-reveal>
          <svg class="stars" viewBox="0 0 120 24" role="img" aria-label="5 out of 5 stars">
            <path d="M12 2.4 14.9 8.3l6.5.9-4.7 4.6 1.1 6.5-5.8-3.1-5.8 3.1 1.1-6.5-4.7-4.6 6.5-.9z"/>
            <path transform="translate(24)" d="M12 2.4 14.9 8.3l6.5.9-4.7 4.6 1.1 6.5-5.8-3.1-5.8 3.1 1.1-6.5-4.7-4.6 6.5-.9z"/>
            <path transform="translate(48)" d="M12 2.4 14.9 8.3l6.5.9-4.7 4.6 1.1 6.5-5.8-3.1-5.8 3.1 1.1-6.5-4.7-4.6 6.5-.9z"/>
            <path transform="translate(72)" d="M12 2.4 14.9 8.3l6.5.9-4.7 4.6 1.1 6.5-5.8-3.1-5.8 3.1 1.1-6.5-4.7-4.6 6.5-.9z"/>
            <path transform="translate(96)" d="M12 2.4 14.9 8.3l6.5.9-4.7 4.6 1.1 6.5-5.8-3.1-5.8 3.1 1.1-6.5-4.7-4.6 6.5-.9z"/>
          </svg>
          <span>Five-star sample stories</span>
        </div>
      </div>
      <p class="sample testimonial-disclaimer" data-reveal>Illustrative sample testimonials and source icons for layout only. Replace with approved patient feedback and verified source links before publishing.</p>
      <div class="testimonial-masonry" id="testimonial-list">
        <?php foreach ($testimonials as $index => $testimonial): ?>
          <article class="testimonial-card" data-reveal<?= $index >= 8 ? ' hidden' : '' ?>>
            <div class="testimonial-card-top">
              <svg class="stars" viewBox="0 0 120 24" role="img" aria-label="5 out of 5 stars">
                <path d="M12 2.4 14.9 8.3l6.5.9-4.7 4.6 1.1 6.5-5.8-3.1-5.8 3.1 1.1-6.5-4.7-4.6 6.5-.9z"/>
                <path transform="translate(24)" d="M12 2.4 14.9 8.3l6.5.9-4.7 4.6 1.1 6.5-5.8-3.1-5.8 3.1 1.1-6.5-4.7-4.6 6.5-.9z"/>
                <path transform="translate(48)" d="M12 2.4 14.9 8.3l6.5.9-4.7 4.6 1.1 6.5-5.8-3.1-5.8 3.1 1.1-6.5-4.7-4.6 6.5-.9z"/>
                <path transform="translate(72)" d="M12 2.4 14.9 8.3l6.5.9-4.7 4.6 1.1 6.5-5.8-3.1-5.8 3.1 1.1-6.5-4.7-4.6 6.5-.9z"/>
                <path transform="translate(96)" d="M12 2.4 14.9 8.3l6.5.9-4.7 4.6 1.1 6.5-5.8-3.1-5.8 3.1 1.1-6.5-4.7-4.6 6.5-.9z"/>
              </svg>
              <span class="review-source" aria-label="Example Google source">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path fill="#4285F4" d="M23.5 12.27c0-.79-.07-1.54-.2-2.27H12v4.3h6.72a5.75 5.75 0 0 1-2.5 3.77v3.13h4.05c2.37-2.19 3.73-5.42 3.73-8.93z"/>
                  <path fill="#34A853" d="M12 24c3.4 0 6.25-1.13 8.33-3.06l-4.05-3.13c-1.12.75-2.55 1.19-4.28 1.19-3.29 0-6.08-2.22-7.07-5.21H.74v3.23A12 12 0 0 0 12 24z"/>
                  <path fill="#FBBC05" d="M4.93 13.79A7.2 7.2 0 0 1 4.55 12c0-.62.13-1.22.38-1.79V6.98H.74A12 12 0 0 0 0 12c0 1.94.46 3.77 1.27 5.39l3.66-2.85z"/>
                  <path fill="#EA4335" d="M12 4.77c1.85 0 3.51.64 4.82 1.91l3.6-3.6C18.24 1.14 15.4 0 12 0A12 12 0 0 0 .74 6.98l4.19 3.23C5.92 7 8.71 4.77 12 4.77z"/>
                </svg>
                <span>Google</span>
              </span>
            </div>
            <blockquote><?= e($testimonial['quote']) ?></blockquote>
            <div class="testimonial-card-bottom">
              <span>Sample patient</span>
              <span><?= e($testimonial['treatment']) ?></span>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <div class="load-more-wrap">
        <button class="btn btn-line" id="load-more" type="button" aria-controls="testimonial-list">Load More</button>
      </div>
    </div>
  </section>

  <section class="band cta testimonial-cta" aria-labelledby="testimonial-cta-heading">
    <div class="wrap cta-grid">
      <div data-reveal>
        <h2 id="testimonial-cta-heading">Let’s talk about your smile</h2>
        <p>Call our team with a question or book an appointment request online. We’ll help you get started.</p>
      </div>
      <div class="cta-actions" data-reveal>
        <a class="btn btn-gold" href="<?= e(phone_href()) ?>">Call Us</a>
        <button class="btn btn-line" data-open>Book Appointment</button>
      </div>
    </div>
  </section>
</main>

<footer>
  <div class="wrap ftr">
    <a class="logo" href="index.php" aria-label="Home"><img src="Uploads/2026/logo.webp" alt="<?= e(s('office_name')) ?>" width="250" height="169"></a>
    <nav aria-label="Footer"><a href="financing.php">Financing</a><a href="testimonials.php">Testimonials</a><a href="index.php#top">Disclaimer</a><a href="index.php#top">Privacy Policy</a></nav>
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

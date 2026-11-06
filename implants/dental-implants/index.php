<?php
declare(strict_types=1);
$root = dirname(__DIR__, 2);
require_once $root . '/inc/bootstrap.php';
send_page_headers();
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Dental Implants | <?= e(s('office_name')) ?></title>
<meta name="description" content="Learn how dental implants can replace missing teeth, what treatment may involve, and how to discuss your options with <?= e(s('office_name')) ?>.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Poppins:wght@200;300;400;500;600&display=swap">
<link rel="stylesheet" href="../../assets/styles.css">
<link rel="stylesheet" href="../../assets/implants.css">
<?= s('header_scripts') ?>
</head>
<body class="implant-page">
<?php $site_base = '../../'; $site_current = 'dental-implants'; include $root . '/inc/site-header.php'; ?>

<main id="top">
  <section class="imp-hero">
    <div class="wrap imp-hero-grid">
      <div class="imp-hero-copy">
        <h1 data-reveal>Dental implant options</h1>
        <p class="lede" data-reveal style="--reveal-delay:70ms">A dental implant replaces a missing tooth's root and may support a crown, bridge or denture. An evaluation helps determine what may fit your needs.</p>
        <div class="hero-ctas" data-reveal style="--reveal-delay:140ms">
          <a class="btn btn-gold" href="<?= e(phone_href()) ?>">Call <?= e(s('phone_display')) ?></a>
          <button class="btn btn-line" data-open><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>Book Appointment</button>
        </div>
        <p class="imp-hero-note" data-reveal style="--reveal-delay:210ms">Start with a conversation about your options, timeline and costs.</p>
      </div>
      <figure class="imp-hero-photo" data-reveal style="--reveal-delay:90ms">
        <img src="../../Uploads/2026/dental-implant-hero.jpg" alt="Illustration of a dental implant between two natural teeth" width="972" height="768" fetchpriority="high">
        <div class="imp-countdown" aria-label="Offer countdown">
          <span>This offer expires soon:</span>
          <time id="imp-countdown" role="timer" aria-label="Time remaining in today's offer">00:00:00</time>
        </div>
      </figure>
    </div>
  </section>

  <section class="band tint" id="implant-overview">
    <div class="wrap imp-overview">
      <div class="imp-section-title">
        <h2 data-reveal>A new foundation for replacement teeth</h2>
        <p class="lede" data-reveal style="--reveal-delay:70ms">An implant replaces the root of a missing tooth and can support a custom restoration.</p>
      </div>
      <div class="imp-copy">
        <p data-reveal>A dental implant is a small post, often made of titanium, that is placed in the jaw. As the area heals, the post can bond with the surrounding bone and provide support for a replacement tooth.</p>
        <p data-reveal style="--reveal-delay:70ms">Depending on how many teeth are missing and your treatment plan, an implant may support a single crown, a bridge or a denture. Your dentist can explain the alternatives and what each option involves.</p>
        <a class="link" href="#treatment" data-reveal style="--reveal-delay:140ms">See how treatment is planned</a>
      </div>
    </div>
  </section>

  <section class="band imp-options">
    <div class="wrap">
      <div class="imp-options-intro">
        <h2 data-reveal>One missing tooth or several</h2>
        <p data-reveal style="--reveal-delay:70ms">The right restoration depends on the number and position of missing teeth, your oral health and your preferences.</p>
      </div>
      <div class="imp-options-list">
        <article>
          <h3 data-reveal>Replace a single tooth</h3>
          <p data-reveal style="--reveal-delay:70ms">An implant can support an individual crown where a tooth is missing, if your dentist determines it is appropriate.</p>
        </article>
        <article>
          <h3 data-reveal style="--reveal-delay:100ms">Restore a larger space</h3>
          <p data-reveal style="--reveal-delay:170ms">For more than one missing tooth, an implant-supported bridge or another restoration may be considered.</p>
        </article>
        <article>
          <h3 data-reveal style="--reveal-delay:200ms">Discuss every alternative</h3>
          <p data-reveal style="--reveal-delay:270ms">Bridges and removable dentures may also be options. Your consultation can compare the benefits, limits and care needs of each.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="band tint" id="treatment">
    <div class="wrap">
      <div class="imp-treatment-heading">
        <h2 data-reveal>Your treatment, step by step</h2>
        <p data-reveal style="--reveal-delay:70ms">Planning is tailored to your mouth and general health. The number of visits and healing time vary from person to person.</p>
      </div>
      <ol class="imp-steps">
        <li>
          <h3 data-reveal>Evaluation and planning</h3>
          <p data-reveal style="--reveal-delay:70ms">Your dentist reviews your health history and examines your mouth. Imaging may be used to assess the jaw and plan treatment.</p>
        </li>
        <li>
          <h3 data-reveal style="--reveal-delay:100ms">Implant placement</h3>
          <p data-reveal style="--reveal-delay:170ms">If you proceed, the implant is placed in the jaw. Your clinician will discuss anesthesia, aftercare and what to expect during healing.</p>
        </li>
        <li>
          <h3 data-reveal style="--reveal-delay:200ms">Custom restoration</h3>
          <p data-reveal style="--reveal-delay:270ms">Once healing allows, a crown, bridge or denture can be attached. Your dentist will explain how to care for it and arrange follow-up.</p>
        </li>
      </ol>
    </div>
  </section>

  <section class="band imp-candidacy">
    <div class="wrap imp-candidacy-grid">
      <div class="imp-copy">
        <h2 data-reveal>Is age a factor?</h2>
        <p data-reveal style="--reveal-delay:70ms">Age alone does not determine whether someone can receive an implant. Overall health, gum condition, available bone, medications and other factors may affect treatment.</p>
        <p data-reveal style="--reveal-delay:140ms">A dentist can review your medical and dental history, discuss any concerns and tell you whether an implant or another option may be suitable.</p>
        <button class="btn btn-royal" data-open data-reveal style="--reveal-delay:210ms"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>Book Appointment</button>
      </div>
      <figure class="imp-candidacy-photo" data-reveal>
        <img src="../../Uploads/2026/treatment-room.jpg" alt="Dental treatment room" width="800" height="600" loading="lazy" decoding="async">
      </figure>
    </div>
  </section>

  <section class="band tint imp-life">
    <div class="wrap imp-life-grid">
      <div>
        <h2 data-reveal>Designed for everyday life</h2>
      </div>
      <div class="imp-copy">
        <p data-reveal>Implant-supported teeth are made to restore appearance and function, but they do not feel exactly like natural teeth. Your experience depends on your treatment and healing.</p>
        <p data-reveal style="--reveal-delay:70ms">Daily cleaning and regular dental visits remain important. At a consultation, ask about maintenance, possible risks and the long-term care your restoration may need.</p>
        <a class="link" href="../../financing.php" data-reveal style="--reveal-delay:140ms">Explore payment options</a>
      </div>
    </div>
  </section>

  <section class="band imp-gallery-section" id="examples">
    <div class="wrap">
      <div class="imp-gallery-heading">
        <h2 data-reveal>Examples of implant restorations</h2>
        <p data-reveal style="--reveal-delay:70ms">Every treatment plan is individual. Photos should be shown only with the patients' permission and accurate case information.</p>
      </div>
      <div class="imp-gallery">
        <button class="gallery-item" type="button" aria-label="Enlarge dental restoration example one" data-reveal>
          <img src="../../Uploads/2026/implants01-2.webp" alt="Before and after example of dental restoration" width="748" height="1299" loading="lazy" decoding="async" data-title="Dental restoration example">
        </button>
        <button class="gallery-item" type="button" aria-label="Enlarge dental restoration example two" data-reveal style="--reveal-delay:80ms">
          <img src="../../Uploads/2026/implants02.webp" alt="Before and after example of teeth restored with a dental implant" width="748" height="1299" loading="lazy" decoding="async" data-title="Dental restoration example">
        </button>
        <button class="gallery-item" type="button" aria-label="Enlarge dental restoration example three" data-reveal style="--reveal-delay:160ms">
          <img src="../../Uploads/2026/implants03.webp" alt="Before and after example of a full dental restoration" width="748" height="1299" loading="lazy" decoding="async" data-title="Dental restoration example">
        </button>
      </div>
      <p class="imp-gallery-note" data-reveal>Individual results vary. Confirm image consent and case details before publishing.</p>
    </div>
  </section>

  <section class="band tint imp-faq">
    <div class="wrap imp-faq-grid">
      <div><h2 data-reveal>Questions about dental implants</h2></div>
      <div>
        <details data-reveal>
          <summary>How do I know if implants are right for me?</summary>
          <p>A dentist needs to examine your mouth and review your health history. Bone and gum health, medications and other factors can affect candidacy.</p>
        </details>
        <details data-reveal style="--reveal-delay:70ms">
          <summary>How long does treatment take?</summary>
          <p>Timing varies. Some people need additional treatment or healing time before a restoration can be attached. Your dentist can outline a schedule for your case.</p>
        </details>
        <details data-reveal style="--reveal-delay:140ms">
          <summary>Will an implant look like my other teeth?</summary>
          <p>A custom restoration is designed to suit your mouth. Your dentist can explain what may be achievable and what factors could affect the result.</p>
        </details>
        <details data-reveal style="--reveal-delay:210ms">
          <summary>What happens at a consultation?</summary>
          <p>You can discuss your concerns, have your oral health assessed and review possible treatments, expected visits, risks and fees before deciding how to proceed.</p>
        </details>
      </div>
    </div>
  </section>

  <section class="band cta">
    <div class="wrap cta-grid">
      <div>
        <h2 data-reveal>Find out if implants fit your needs</h2>
        <p data-reveal style="--reveal-delay:70ms">Speak with the team about an evaluation and the options available for your smile.</p>
      </div>
      <div class="cta-actions">
        <a class="btn btn-gold" href="<?= e(phone_href()) ?>" data-reveal>Call Us</a>
        <button class="btn btn-line" data-open data-reveal style="--reveal-delay:70ms"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>Book Appointment</button>
      </div>
    </div>
  </section>
</main>

<div class="lightbox" id="lightbox" hidden>
  <div class="lightbox-panel" role="dialog" aria-modal="true" aria-label="Expanded dental restoration photo">
    <button class="lightbox-close" type="button" aria-label="Close image">×</button>
    <div class="lightbox-image-wrap"><img class="lightbox-image" src="" alt=""></div>
    <div class="lightbox-caption"><span>Dental implants</span><strong id="lightbox-title">Dental restoration example</strong></div>
  </div>
</div>

<?php include $root . '/inc/site-footer.php'; ?>

<div class="thumb" aria-label="Quick contact">
  <a class="btn btn-gold" href="<?= e(phone_href()) ?>">Call</a>
  <a class="btn btn-line" href="<?= e(sms_href()) ?>">Text</a>
  <button class="btn btn-line" data-open><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>Book Appointment</button>
</div>

<dialog id="dlg" aria-labelledby="dlg-h">
  <div class="m-in">
    <div class="m-top"><h2 id="dlg-h" style="font-size:1.9rem">Request appointment</h2><button class="x" id="dlg-x" aria-label="Close">×</button></div>
    <p class="sub">Call us at <b><?= e(s('phone_display')) ?></b> or send the form and we'll call you back.</p>
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

<script>window.SITE = <?= json_encode(['recaptchaSiteKey' => recaptcha_ready() ? s('recaptcha_site_key') : '', 'submitUrl' => '../../submit.php'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES) ?>;</script>
<?php if (recaptcha_ready()): ?><script src="https://www.google.com/recaptcha/api.js?render=<?= e(rawurlencode(s('recaptcha_site_key'))) ?>" async defer></script><?php endif; ?>
<script src="../../assets/site.js"></script>
<script src="../../assets/implants.js"></script>
<?= s('chat_script') ?>
<?= s('footer_scripts') ?>
</body>
</html>

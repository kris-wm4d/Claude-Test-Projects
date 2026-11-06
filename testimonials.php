<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/bootstrap.php';
send_page_headers();

function testimonial_source_mark(string $source): string
{
    switch ($source) {
        case 'Google':
            return 'G';
        case 'Yelp':
            return '*';
        case 'Facebook':
            return 'f';
        default:
            return '⌂';
    }
}

$testimonials = [
    ['name' => 'Maya R.', 'source' => 'Google', 'quote' => 'I used to feel nervous before every appointment. The team explained each step and made sure I felt comfortable the whole time.'],
    ['name' => 'Andre P.', 'source' => 'Yelp', 'quote' => 'Everyone was warm from the moment I walked in. I left knowing exactly what my next steps were.'],
    ['name' => 'Leah T.', 'source' => 'Facebook', 'quote' => 'They listened to my concerns and answered every question without rushing me. It was a genuinely reassuring visit.'],
    ['name' => 'Chris M.', 'source' => 'Website', 'quote' => 'Scheduling was easy, the office felt welcoming, and the whole appointment ran smoothly.'],
    ['name' => 'Rina S.', 'source' => 'Google', 'quote' => 'I appreciated how clearly everything was explained. I felt included in the conversation and comfortable asking questions.'],
    ['name' => 'Jordan K.', 'source' => 'Yelp', 'quote' => 'Kind, thoughtful people and a calm office. The care plan was easy to understand, and I never felt pressured.'],
    ['name' => 'Elena W.', 'source' => 'Facebook', 'quote' => 'They took time to listen and made the visit feel personal. I knew what to expect before anything began.'],
    ['name' => 'Tom D.', 'source' => 'Website', 'quote' => 'A friendly team, a comfortable visit, and clear answers. I left feeling much more at ease about my dental care.'],
    ['name' => 'Nora B.', 'source' => 'Google', 'quote' => 'The front desk helped me find a time that worked, and the team kept me informed throughout my visit.'],
    ['name' => 'Sam G.', 'source' => 'Yelp', 'quote' => 'I felt heard and well looked after. Everything was explained in plain language, which made a big difference for me.'],
    ['name' => 'Alicia C.', 'source' => 'Facebook', 'quote' => 'From check-in to checkout, everyone was patient and kind. I never felt like just another appointment on the schedule.'],
    ['name' => 'Marcus V.', 'source' => 'Website', 'quote' => 'The visit was organized, comfortable, and easy to understand. I appreciated having time to talk through my questions.'],
];
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Patient Testimonials | <?= e(s('office_name')) ?></title>
<meta name="description" content="Read patient stories and learn what to expect at <?= e(s('office_name')) ?>.">
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
  <a class="mh-link" href="financing.php">View My Options <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
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
  <section class="testimonials-hero">
    <div class="wrap testimonials-hero-grid">
      <div class="testimonials-intro">
        <h1>Care, in the words of patients.</h1>
        <p>Every visit is a conversation. Explore sample stories about thoughtful care, clear answers, and feeling welcome.</p>
      </div>
      <div class="testimonials-hero-mark" aria-hidden="true">
        <svg viewBox="0 0 220 250" fill="none">
          <path d="M110 33C83 9 38 16 26 49 12 87 36 132 46 165c8 28 13 61 34 62 19 1 18-51 30-51s11 52 30 51c21-1 26-34 34-62 10-33 34-78 20-116C212 16 167 9 140 33c-9 8-21 8-30 0Z" fill="currentColor"/>
          <path d="M110 33C83 9 38 16 26 49 12 87 36 132 46 165c8 28 13 61 34 62 19 1 18-51 30-51s11 52 30 51c21-1 26-34 34-62 10-33 34-78 20-116C212 16 167 9 140 33c-9 8-21 8-30 0Z" stroke="white" stroke-opacity=".45" stroke-width="3"/>
        </svg>
        <p>Care, made personal.</p>
      </div>
    </div>
  </section>

  <section class="band testimonial-section" aria-labelledby="testimonial-heading">
    <div class="wrap">
      <div class="testimonial-heading">
        <h2 id="testimonial-heading">Stories about feeling at ease</h2>
        <p class="testimonial-disclaimer" role="note">Names and reviews are fictional samples, and the photos are illustrative. Replace them with approved, authentic content before publishing.</p>
      </div>
      <div class="testimonial-masonry" id="testimonial-list">
        <?php foreach ($testimonials as $index => $testimonial): ?>
          <article class="testimonial-card<?= $index >= 8 ? ' is-extra' : '' ?>"<?= $index >= 8 ? ' hidden' : '' ?>>
            <blockquote><?= e($testimonial['quote']) ?></blockquote>
            <div class="testimonial-rating" role="img" aria-label="5 out of 5 stars">
              <?php for ($star = 0; $star < 5; $star++): ?>
                <span class="star-mark" aria-hidden="true"></span>
              <?php endfor; ?>
            </div>
            <div class="testimonial-source">
              <span class="source-logo source-<?= strtolower(e($testimonial['source'])) ?>" aria-hidden="true"><?= e(testimonial_source_mark($testimonial['source'])) ?></span>
              <span class="source-copy"><?= e($testimonial['source']) ?><small>Sample review</small></span>
              <span class="testimonial-name"><?= e($testimonial['name']) ?></span>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <p class="testimonial-count" id="testimonial-count" aria-live="polite">Showing 8 of 12 sample testimonials.</p>
      <button class="btn btn-royal testimonial-more" id="testimonial-more" type="button" aria-controls="testimonial-list">Load More</button>
      <noscript><style>.testimonial-card[hidden] { display: inline-grid !important } #testimonial-more { display: none }</style></noscript>
    </div>
  </section>

  <section class="band office-gallery" aria-labelledby="office-gallery-heading">
    <div class="wrap">
      <div class="office-gallery-heading">
        <h2 id="office-gallery-heading">A look around the practice</h2>
        <p>These illustrative images offer a glimpse of the spaces and care behind each visit.</p>
      </div>
      <div class="office-photo-grid">
        <figure class="office-photo">
          <img src="Uploads/2026/hero.webp" alt="Illustrative patient portrait in a dental setting" width="720" height="720" loading="lazy">
          <figcaption>A reason to smile</figcaption>
        </figure>
        <figure class="office-photo">
          <img src="Uploads/2026/receiving-area.jpg" alt="Illustrative care conversation in a dental office" width="800" height="600" loading="lazy">
          <figcaption>Time to talk things through</figcaption>
        </figure>
        <figure class="office-photo">
          <img src="Uploads/2026/treatment-room.jpg" alt="Illustrative dental treatment room" width="800" height="600" loading="lazy">
          <figcaption>A comfortable place to begin</figcaption>
        </figure>
        <figure class="office-photo">
          <img src="Uploads/2026/modern-reception.jpg" alt="Illustrative modern dental care space" width="800" height="600" loading="lazy">
          <figcaption>Thoughtful care, every step</figcaption>
        </figure>
      </div>
    </div>
  </section>

  <section class="band cta">
    <div class="wrap cta-grid">
      <div>
        <h2>Let’s make your next visit feel easy.</h2>
        <p>Have a question or ready to get started? Our team is happy to help you take the next step.</p>
      </div>
      <div class="cta-actions">
        <a class="btn btn-line" href="<?= e(phone_href()) ?>">Call Us</a>
        <button class="btn btn-gold" data-open>Book Appointment</button>
      </div>
    </div>
  </section>
</main>

<footer>
  <div class="wrap ftr">
    <a class="logo" href="index.php" aria-label="Back to home"><img src="Uploads/2026/logo.webp" alt="<?= e(s('office_name')) ?>" width="250" height="169"></a>
    <nav aria-label="Footer"><a href="financing.php">Financing</a><a href="testimonials.php" aria-current="page">Testimonials</a><a href="index.php#top">Home</a></nav>
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
<script>
(() => {
  const button = document.getElementById('testimonial-more');
  const extraReviews = [...document.querySelectorAll('.testimonial-card.is-extra')];
  const counter = document.getElementById('testimonial-count');
  let visible = 8;

  if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const revealObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('has-arrived');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.08 });

    document.querySelectorAll('.testimonial-card, .office-gallery-heading > *, .office-photo, .cta-grid > *').forEach(review => {
      review.classList.add('will-reveal');
      revealObserver.observe(review);
    });
  }

  button.addEventListener('click', () => {
    const next = extraReviews.splice(0, 4);
    next.forEach(review => review.hidden = false);
    visible += next.length;
    counter.textContent = `Showing ${visible} of 12 sample testimonials.`;
    if (!extraReviews.length) button.hidden = true;
  });
})();
</script>
</body>
</html>

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
$initialVisible = 8;
$totalTestimonials = count($testimonials);
$shownTestimonials = min($initialVisible, $totalTestimonials);
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
<?= s('header_scripts') ?>
</head>
<body class="testimonials-page">
<?php $site_base = ''; $site_current = 'testimonials'; include __DIR__ . '/inc/site-header.php'; ?>

<main id="top">
  <section class="testimonials-hero">
    <div class="wrap testimonials-hero-grid">
      <div class="testimonials-intro">
        <h1>Care, in the <em class="heading-accent">words of patients.</em></h1>
        <p>Every visit is a conversation. Explore sample stories about thoughtful care, clear answers, and feeling welcome.</p>
      </div>
      <div class="testimonials-hero-mark">
        <img src="Uploads/2026/testimonials-hero-family.webp" alt="Mother and daughter smiling together" width="768" height="960" fetchpriority="high">
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
          <article class="testimonial-card<?= $index >= $initialVisible ? ' is-extra' : '' ?>"<?= $index >= $initialVisible ? ' hidden' : '' ?>>
            <blockquote><?= e($testimonial['quote']) ?></blockquote>
            <div class="testimonial-rating" role="img" aria-label="5 out of 5 stars">
              <?php for ($star = 0; $star < 5; $star++): ?>
                <span class="star-mark" aria-hidden="true"></span>
              <?php endfor; ?>
            </div>
            <div class="testimonial-source">
              <span class="source-logo source-<?= e(strtolower($testimonial['source'])) ?>" aria-hidden="true"><?= e(testimonial_source_mark($testimonial['source'])) ?></span>
              <span class="source-copy"><?= e($testimonial['source']) ?><small>Sample review</small></span>
              <span class="testimonial-name"><?= e($testimonial['name']) ?></span>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <p class="testimonial-count" id="testimonial-count" aria-live="polite">Showing <?= $shownTestimonials ?> of <?= $totalTestimonials ?> sample testimonials.</p>
      <?php if ($totalTestimonials > $initialVisible): ?>
      <button class="btn btn-royal testimonial-more" id="testimonial-more" type="button" aria-controls="testimonial-list">Load More</button>
      <?php endif; ?>
      <noscript><style>.testimonial-card[hidden] { display: inline-grid !important } #testimonial-more, #testimonial-count { display: none }</style></noscript>
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
        <a class="btn btn-gold" href="<?= e(phone_href()) ?>">Call Us</a>
        <button class="btn btn-line" data-open><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>Book Appointment</button>
      </div>
    </div>
  </section>
</main>

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
<script>
(() => {
  const button = document.getElementById('testimonial-more');
  const counter = document.getElementById('testimonial-count');
  const testimonialCards = [...document.querySelectorAll('.testimonial-card')];
  const extraReviews = testimonialCards.filter(card => card.classList.contains('is-extra'));

  let visible = testimonialCards.filter(card => !card.hidden).length;

  const updateCounter = () => {
    counter.textContent = `Showing ${visible} of ${testimonialCards.length} sample testimonials.`;
  };

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

  if (!button || !counter) return;

  updateCounter();

  button.addEventListener('click', () => {
    const next = extraReviews.splice(0, 4);
    next.forEach(review => { review.hidden = false; });
    visible += next.length;
    updateCounter();
    if (next[0]) {
      next[0].setAttribute('tabindex', '-1');
      next[0].focus({ preventScroll: true });
    }
    if (!extraReviews.length) button.hidden = true;
  });
})();
</script>
<?= s('chat_script') ?>
<?= s('footer_scripts') ?>
</body>
</html>

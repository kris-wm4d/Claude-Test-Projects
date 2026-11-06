<?php
$site_base = $site_base ?? '';
$site_home = $site_base . 'index.php';
?>
<section class="band" id="visit">
  <div class="wrap visit">
    <div class="copy">
      <h2>Visit our office</h2>
      <?php foreach (practice_list() as $practice): ?>
        <article class="practice-entry">
          <h3><?= e($practice['name'] ?? '') ?></h3>
          <?php if (!empty($practice['description'])): ?><p><?= e($practice['description']) ?></p><?php endif; ?>
          <?php if (!empty($practice['location'])): ?><p class="addr"><?= nl2br(e($practice['location'])) ?></p><?php endif; ?>
          <?php if (!empty($practice['phone_number'])): ?><p><a href="<?= e(practice_phone_href($practice)) ?>"><?= e($practice['phone_number']) ?></a></p><?php endif; ?>
          <?php if (practice_directions_url($practice) !== ''): ?><p><a class="link" href="<?= e(practice_directions_url($practice)) ?>" target="_blank" rel="noopener">Get directions</a></p><?php endif; ?>
          <?php if (!empty($practice['office_hours'])): ?><p class="practice-hours"><strong>Office hours</strong><br><?= nl2br(e($practice['office_hours'])) ?></p><?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
    <div class="photos">
      <div class="gallery-card"><img src="<?= e($site_base) ?>Uploads/2026/modern-reception.jpg" alt="Modern dental reception area" width="800" height="600" loading="lazy"></div>
      <div class="gallery-card"><img src="<?= e($site_base) ?>Uploads/2026/receiving-area.jpg" alt="Dental office receiving area" width="800" height="600" loading="lazy"></div>
      <?php foreach (practice_list() as $practice): ?>
        <?php if (practice_map_src($practice) !== ''): ?>
          <div class="map map-embed"><iframe src="<?= e(practice_map_src($practice)) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen title="Map to <?= e($practice['name'] ?? 'practice') ?>"></iframe></div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<footer>
  <div class="wrap ftr">
    <a class="logo" href="<?= e($site_home) ?>#top" aria-label="Back to home"><img src="<?= e($site_base) ?>Uploads/2026/logo.webp" alt="<?= e(s('office_name')) ?>" width="250" height="169"></a>
    <nav aria-label="Footer"><a href="<?= e($site_base) ?>financing.php">Financing</a><a href="<?= e($site_base) ?>testimonials.php">Testimonials</a><a href="<?= e($site_home) ?>#top">Disclaimer</a><a href="<?= e($site_home) ?>#top">Privacy Policy</a></nav>
    <span>© Copyright 2026</span>
  </div>
</footer>

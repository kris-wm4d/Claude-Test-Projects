<?php
$site_base = $site_base ?? '';
$site_current = $site_current ?? '';
$site_home = $site_base . 'index.php';
?>
<div class="masthead"><div class="wrap mh">
  <span class="mh-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg>Most PPO plans accepted</span>
  <span class="mh-item mh-hide"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg>Monthly payment plans available</span>
  <a class="mh-link" href="<?= e($site_base) ?>financing.php">View My Options <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
</div></div>

<header class="site">
  <div class="wrap hdr">
    <a class="logo" href="<?= e($site_home) ?>#top" aria-label="Home"><img src="<?= e($site_base) ?>Uploads/2026/logo.webp" alt="<?= e(s('office_name')) ?>" width="250" height="169"></a>
    <nav class="main" aria-label="Main">
      <a href="<?= e($site_home) ?>#top"<?= $site_current === 'home' ? ' aria-current="page"' : '' ?>>Home</a>
      <div class="nav-item">
        <a href="<?= e($site_home) ?>#welcome">About</a>
        <div class="subnav">
          <a href="<?= e($site_home) ?>#doctor">Meet the Doctor</a>
          <a href="<?= e($site_home) ?>#welcome">Meet The Team</a>
          <a href="<?= e($site_base) ?>financing.php"<?= $site_current === 'financing' ? ' aria-current="page"' : '' ?>>Financing</a>
          <a href="<?= e($site_home) ?>#technology">Technology</a>
          <a href="<?= e($site_base) ?>gallery.php"<?= $site_current === 'gallery' ? ' aria-current="page"' : '' ?>>Office Tour</a>
        </div>
      </div>
      <div class="nav-item">
        <a href="<?= e($site_home) ?>#services">Implants</a>
        <div class="subnav">
          <a href="<?= e($site_base) ?>implants/dental-implants/"<?= $site_current === 'dental-implants' ? ' aria-current="page"' : '' ?>>Dental Implants</a>
        </div>
      </div>
      <a href="<?= e($site_home) ?>#services">Cosmetic</a>
      <a href="<?= e($site_home) ?>#services">General</a>
      <div class="nav-item">
        <a href="<?= e($site_home) ?>#results">Results</a>
        <div class="subnav">
          <a href="<?= e($site_base) ?>testimonials.php"<?= $site_current === 'testimonials' ? ' aria-current="page"' : '' ?>>Testimonials</a>
          <a href="<?= e($site_base) ?>gallery.php"<?= $site_current === 'gallery' ? ' aria-current="page"' : '' ?>>Gallery</a>
        </div>
      </div>
      <a href="<?= e($site_home) ?>#visit">Contact</a>
    </nav>
    <button class="btn btn-line" data-open><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>Book Appointment</button>
    <a class="phone btn btn-gold" href="<?= e(phone_href()) ?>"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1z"/></svg><?= e(s('phone_display')) ?></a>
    <button class="burger" id="burger" aria-expanded="false" aria-controls="drawer" aria-label="Menu"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
  </div>
  <nav class="wrap drawer" id="drawer" aria-label="Mobile" hidden>
    <a href="<?= e($site_home) ?>#top"<?= $site_current === 'home' ? ' aria-current="page"' : '' ?>>Home</a>
    <div class="drawer-group">
      <a href="<?= e($site_home) ?>#welcome">About</a>
      <div class="drawer-subnav">
        <a href="<?= e($site_home) ?>#doctor">Meet the Doctor</a>
        <a href="<?= e($site_home) ?>#welcome">Meet The Team</a>
        <a href="<?= e($site_base) ?>financing.php"<?= $site_current === 'financing' ? ' aria-current="page"' : '' ?>>Financing</a>
        <a href="<?= e($site_home) ?>#technology">Technology</a>
        <a href="<?= e($site_base) ?>gallery.php"<?= $site_current === 'gallery' ? ' aria-current="page"' : '' ?>>Office Tour</a>
      </div>
    </div>
    <div class="drawer-group">
      <a href="<?= e($site_home) ?>#services">Implants</a>
      <div class="drawer-subnav">
        <a href="<?= e($site_base) ?>implants/dental-implants/"<?= $site_current === 'dental-implants' ? ' aria-current="page"' : '' ?>>Dental Implants</a>
      </div>
    </div>
    <a href="<?= e($site_home) ?>#services">Cosmetic</a>
    <a href="<?= e($site_home) ?>#services">General</a>
    <div class="drawer-group">
      <a href="<?= e($site_home) ?>#results">Results</a>
      <div class="drawer-subnav">
        <a href="<?= e($site_base) ?>testimonials.php"<?= $site_current === 'testimonials' ? ' aria-current="page"' : '' ?>>Testimonials</a>
        <a href="<?= e($site_base) ?>gallery.php"<?= $site_current === 'gallery' ? ' aria-current="page"' : '' ?>>Gallery</a>
      </div>
    </div>
    <a href="<?= e($site_home) ?>#visit">Contact</a>
  </nav>
</header>

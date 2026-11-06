(() => {
  const dlg = document.getElementById('dlg'), form = document.getElementById('req'), done = document.getElementById('done');
  const staticSite = document.documentElement.dataset.staticSite === 'true';
  let staticNotice = null;
  const open = () => {
    done.hidden = true;
    document.getElementById('f-err').textContent = '';
    const instructions = dlg.querySelector('.m-in > .sub');
    if (staticSite) {
      form.hidden = true;
      if (instructions) instructions.hidden = true;
      if (!staticNotice) {
        staticNotice = document.createElement('p');
        staticNotice.id = 'static-booking';
        staticNotice.className = 'sub';
        staticNotice.setAttribute('role', 'status');
        staticNotice.append(document.createTextNode('Online appointment requests are unavailable on this static site. Please call us at '));
        const phone = document.querySelector('.phone');
        if (phone) {
          const link = document.createElement('a');
          link.href = phone.href;
          link.textContent = phone.textContent.trim();
          staticNotice.append(link);
          staticNotice.append(document.createTextNode('.'));
        } else {
          staticNotice.append(document.createTextNode('the office.'));
        }
        form.insertAdjacentElement('afterend', staticNotice);
      }
      staticNotice.hidden = false;
    } else {
      form.hidden = false;
      if (instructions) instructions.hidden = false;
    }
    dlg.showModal ? dlg.showModal() : dlg.setAttribute('open', '');
    if (!staticSite) setTimeout(() => document.getElementById('f-name').focus(), 30);
  };
  const close = () => dlg.close ? dlg.close() : dlg.removeAttribute('open');
  document.querySelectorAll('[data-open]').forEach(b => b.addEventListener('click', open));
  document.getElementById('dlg-x').addEventListener('click', close);
  document.getElementById('done-close').addEventListener('click', close);
  dlg.addEventListener('click', e => { if (e.target === dlg) close(); });
  const cfg = window.SITE || {};
  const submitBtn = form.querySelector('[type=submit]');
  const getToken = () => new Promise(resolve => {
    if (!cfg.recaptchaSiteKey || !window.grecaptcha) return resolve('');
    grecaptcha.ready(() => grecaptcha.execute(cfg.recaptchaSiteKey, { action: 'appointment' }).then(resolve, () => resolve('')));
  });
  form.addEventListener('submit', async e => {
    e.preventDefault();
    const err = document.getElementById('f-err');
    const v = id => document.getElementById(id).value.trim();
    if (!v('f-name')) return err.textContent = 'Enter your full name.';
    if (v('f-phone').replace(/\D/g, '').length < 10) return err.textContent = 'Enter a 10-digit phone number so we can call you back.';
    if (!/^\S+@\S+\.\S+$/.test(v('f-email'))) return err.textContent = 'Enter a valid email address.';
    if (!v('f-msg')) return err.textContent = 'Add a short message so we know how to help.';
    err.textContent = '';
    submitBtn.disabled = true; const label = submitBtn.textContent; submitBtn.textContent = 'Sending…';
    try {
      const data = new FormData(form);
      data.set('recaptcha_token', await getToken());
      data.set('page', location.pathname);
      const res = await fetch(cfg.submitUrl || 'submit.php', { method: 'POST', body: data, headers: { 'Accept': 'application/json' } });
      const out = await res.json().catch(() => ({ ok: false, message: 'Something went wrong. Please call us.' }));
      if (!out.ok) { err.textContent = out.message; return; }
      document.getElementById('done-msg').textContent = out.message;
      form.reset(); form.hidden = true; done.hidden = false;
    } catch (_) {
      err.textContent = "We couldn't reach the server. Check your connection or call us.";
    } finally {
      submitBtn.disabled = false; submitBtn.textContent = label;
    }
  });

  const burger = document.getElementById('burger'), drawer = document.getElementById('drawer');
  burger.addEventListener('click', () => { const o = drawer.hidden; drawer.hidden = !o; burger.setAttribute('aria-expanded', o); });
  drawer.querySelectorAll('a').forEach(a => a.addEventListener('click', () => { drawer.hidden = true; burger.setAttribute('aria-expanded', false); }));

  const ba = document.getElementById('ba'), range = document.getElementById('ba-range');
  if (ba && range) {
    const setPos = v => { ba.style.setProperty('--pos', v + '%'); range.value = v; };
    range.addEventListener('input', e => ba.style.setProperty('--pos', e.target.value + '%'));

    // Auto-sweep when idle; any interaction pauses it, and it resumes after IDLE ms.
    const IDLE = 4000, PERIOD = 6000, MIN = 12, MAX = 88;
    const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
    let visible = false, hovered = false, lastTouch = 0, raf = 0, t0 = 0, phase0 = 0;
    const idle = () => !hovered && document.activeElement !== range && Date.now() - lastTouch > IDLE;
    const tick = now => {
      if (!visible || !idle()) { raf = 0; return; }
      if (!t0) {
        // Start the sweep from wherever the handle currently sits so it never jumps.
        const p = Math.min(Math.max(+range.value, MIN), MAX);
        phase0 = Math.acos(1 - 2 * (p - MIN) / (MAX - MIN));
        t0 = now;
      }
      const ph = phase0 + (now - t0) / PERIOD * 2 * Math.PI;
      setPos(+(MIN + (MAX - MIN) * (1 - Math.cos(ph)) / 2).toFixed(2));
      raf = requestAnimationFrame(tick);
    };
    const kick = () => { if (!raf && visible && !reduced) { t0 = 0; raf = requestAnimationFrame(tick); } };
    const touched = () => { lastTouch = Date.now(); t0 = 0; setTimeout(kick, IDLE + 50); };
    ['pointerdown', 'touchstart', 'keydown', 'input'].forEach(ev => range.addEventListener(ev, touched, { passive: true }));
    ba.addEventListener('pointerenter', e => { if (e.pointerType === 'mouse') hovered = true; });
    ba.addEventListener('pointerleave', () => { hovered = false; touched(); });
    range.addEventListener('blur', touched);
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(([en]) => { visible = en.isIntersecting; kick(); }, { threshold: 0.3 }).observe(ba);
    } else { visible = true; kick(); }
  }

  const lightbox = document.getElementById('lightbox');
  const lightboxImg = lightbox ? lightbox.querySelector('.lightbox-image') : null;
  const lightboxTitle = lightbox ? document.getElementById('lightbox-title') : null;
  if (lightbox && lightboxImg && lightboxTitle) {
    let lightboxTrigger = null;
    const closeLightbox = () => {
      lightbox.hidden = true;
      lightboxImg.src = '';
      lightboxImg.alt = '';
      document.body.style.overflow = '';
      if (lightboxTrigger) lightboxTrigger.focus();
    };

    const openLightbox = (img, trigger) => {
      if (!img || !img.src) return;
      lightboxTrigger = trigger;
      lightboxImg.src = img.src;
      lightboxImg.alt = img.alt || 'Expanded gallery photo';
      lightboxTitle.textContent = img.dataset.title || img.alt || 'Gallery image';
      lightbox.hidden = false;
      document.body.style.overflow = 'hidden';
      lightbox.querySelector('.lightbox-close').focus();
    };

    document.querySelectorAll('.gallery-item').forEach(item => {
      const img = item.querySelector('img');
      item.addEventListener('click', () => openLightbox(img, item));
    });
    document.querySelectorAll('.photos .slot img').forEach(img => {
      img.addEventListener('click', () => openLightbox(img, img));
    });

    lightbox.addEventListener('click', e => {
      if (e.target === lightbox) closeLightbox();
    });
    lightbox.querySelector('.lightbox-close').addEventListener('click', closeLightbox);
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && !lightbox.hidden) closeLightbox();
    });
  }

  const t = document.querySelector('#hours tr[data-d="' + new Date().getDay() + '"]'); if (t) t.classList.add('today');
})();

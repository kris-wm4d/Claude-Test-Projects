(() => {
  const dlg = document.getElementById('dlg'), form = document.getElementById('req'), done = document.getElementById('done');
  const open = () => { form.hidden = false; done.hidden = true; document.getElementById('f-err').textContent = ''; dlg.showModal ? dlg.showModal() : dlg.setAttribute('open', ''); setTimeout(() => document.getElementById('f-name').focus(), 30); };
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
  if (ba && range) range.addEventListener('input', e => ba.style.setProperty('--pos', e.target.value + '%'));

  const t = document.querySelector('#hours tr[data-d="' + new Date().getDay() + '"]'); if (t) t.classList.add('today');
})();

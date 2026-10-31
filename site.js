(() => {
  const dlg = document.getElementById('dlg'), form = document.getElementById('req'), done = document.getElementById('done');
  const open = () => { form.hidden = false; done.hidden = true; document.getElementById('f-err').textContent = ''; dlg.showModal ? dlg.showModal() : dlg.setAttribute('open', ''); setTimeout(() => document.getElementById('f-name').focus(), 30); };
  const close = () => dlg.close ? dlg.close() : dlg.removeAttribute('open');
  document.querySelectorAll('[data-open]').forEach(b => b.addEventListener('click', open));
  document.getElementById('dlg-x').addEventListener('click', close);
  document.getElementById('done-close').addEventListener('click', close);
  dlg.addEventListener('click', e => { if (e.target === dlg) close(); });
  form.addEventListener('submit', e => {
    e.preventDefault();
    const err = document.getElementById('f-err');
    if (document.getElementById('f-website').value) return;
    const v = id => document.getElementById(id).value.trim();
    if (!v('f-name')) return err.textContent = 'Enter your full name.';
    if (v('f-phone').replace(/\D/g, '').length < 10) return err.textContent = 'Enter a 10-digit phone number so we can call you back.';
    if (!/^\S+@\S+\.\S+$/.test(v('f-email'))) return err.textContent = 'Enter a valid email address.';
    if (!v('f-msg')) return err.textContent = 'Add a short message so we know how to help.';
    err.textContent = ''; form.hidden = true; done.hidden = false;
  });

  const burger = document.getElementById('burger'), drawer = document.getElementById('drawer');
  burger.addEventListener('click', () => { const o = drawer.hidden; drawer.hidden = !o; burger.setAttribute('aria-expanded', o); });
  drawer.querySelectorAll('a').forEach(a => a.addEventListener('click', () => { drawer.hidden = true; burger.setAttribute('aria-expanded', false); }));

  const ba = document.getElementById('ba'), range = document.getElementById('ba-range');
  if (ba && range) range.addEventListener('input', e => ba.style.setProperty('--pos', e.target.value + '%'));

  const t = document.querySelector('#hours tr[data-d="' + new Date().getDay() + '"]'); if (t) t.classList.add('today');
})();

(() => {
  const countdown = document.getElementById('imp-countdown');
  if (countdown) {
    let target = null;
    const nextMidnight = now => new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);
    const updateCountdown = () => {
      const now = new Date();
      if (!target || now >= target) target = nextMidnight(now);
      let seconds = Math.ceil((target.getTime() - now.getTime()) / 1000);
      const hours = Math.floor(seconds / 3600);
      seconds %= 3600;
      const minutes = Math.floor(seconds / 60);
      seconds %= 60;
      countdown.textContent = [hours, minutes, seconds].map(value => String(value).padStart(2, '0')).join(':'); // taste-skill-ignore
    };
    updateCountdown();
    window.setInterval(updateCountdown, 1000);
  }

  const targets = document.querySelectorAll('.implant-page [data-reveal]');
  if (!targets.length || !('IntersectionObserver' in window) || matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('is-visible');
      observer.unobserve(entry.target);
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -24px 0px' });

  targets.forEach(target => observer.observe(target));
  document.documentElement.classList.add('motion-ready');
})();

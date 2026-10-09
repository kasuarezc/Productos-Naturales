/**
 * HERO — red molecular animada (canvas) + cuenta regresiva.
 */
(() => {
  'use strict';
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- Cuenta regresiva ---------- */
  const cd = document.querySelector('[data-countdown]');
  if (cd) {
    const target = new Date(cd.dataset.countdown).getTime();
    const els = { d: cd.querySelector('[data-d]'), h: cd.querySelector('[data-h]'), m: cd.querySelector('[data-m]'), s: cd.querySelector('[data-s]') };
    const set = (el, v) => {
      const txt = String(v).padStart(2, '0');
      if (el.textContent !== txt) { el.textContent = txt; el.classList.remove('tick'); void el.offsetWidth; el.classList.add('tick'); }
    };
    const tick = () => {
      let diff = Math.max(0, target - Date.now());
      const d = Math.floor(diff / 864e5); diff -= d * 864e5;
      const h = Math.floor(diff / 36e5); diff -= h * 36e5;
      const m = Math.floor(diff / 6e4); diff -= m * 6e4;
      const s = Math.floor(diff / 1e3);
      set(els.d, d); set(els.h, h); set(els.m, m); set(els.s, s);
    };
    tick(); setInterval(tick, 1000);
  }

  /* ---------- Red molecular en canvas ---------- */
  const canvas = document.querySelector('.hero__canvas');
  if (!canvas || reduceMotion) return;
  const ctx = canvas.getContext('2d');
  const colors = ['rgba(228,242,229,', 'rgba(150,166,28,', 'rgba(242,142,19,', 'rgba(132,191,164,'];
  let W, H, dpr, atoms = [], mouse = { x: -999, y: -999 }, raf;

  const resize = () => {
    dpr = Math.min(window.devicePixelRatio || 1, 2);
    W = canvas.clientWidth; H = canvas.clientHeight;
    canvas.width = W * dpr; canvas.height = H * dpr;
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    const n = Math.round(Math.min(90, (W * H) / 16000));
    atoms = Array.from({ length: n }, () => ({
      x: Math.random() * W, y: Math.random() * H,
      vx: (Math.random() - .5) * .35, vy: (Math.random() - .5) * .35,
      r: Math.random() * 2.2 + 1.2, c: colors[Math.floor(Math.random() * colors.length)],
      hex: Math.random() < .08, rot: Math.random() * Math.PI
    }));
  };

  const hexagon = (x, y, r, rot) => {
    ctx.beginPath();
    for (let i = 0; i < 6; i++) {
      const a = rot + i * Math.PI / 3;
      i ? ctx.lineTo(x + r * Math.cos(a), y + r * Math.sin(a)) : ctx.moveTo(x + r * Math.cos(a), y + r * Math.sin(a));
    }
    ctx.closePath();
  };

  const draw = () => {
    ctx.clearRect(0, 0, W, H);
    const maxD = Math.min(150, W / 7);
    for (let i = 0; i < atoms.length; i++) {
      const a = atoms[i];
      a.x += a.vx; a.y += a.vy; a.rot += .003;
      if (a.x < -20) a.x = W + 20; if (a.x > W + 20) a.x = -20;
      if (a.y < -20) a.y = H + 20; if (a.y > H + 20) a.y = -20;
      // atracción leve al cursor
      const mdx = mouse.x - a.x, mdy = mouse.y - a.y, md = Math.hypot(mdx, mdy);
      if (md < 180) { a.x += mdx * .004; a.y += mdy * .004; }
      for (let j = i + 1; j < atoms.length; j++) {
        const b = atoms[j], d = Math.hypot(a.x - b.x, a.y - b.y);
        if (d < maxD) {
          ctx.strokeStyle = `rgba(228,242,229,${(1 - d / maxD) * .28})`;
          ctx.lineWidth = 1;
          ctx.beginPath(); ctx.moveTo(a.x, a.y); ctx.lineTo(b.x, b.y); ctx.stroke();
        }
      }
      if (a.hex) {
        ctx.strokeStyle = a.c + '.45)'; ctx.lineWidth = 1.4;
        hexagon(a.x, a.y, 14, a.rot); ctx.stroke();
        hexagon(a.x, a.y, 8, -a.rot); ctx.stroke();
      }
      ctx.fillStyle = a.c + '.85)';
      ctx.beginPath(); ctx.arc(a.x, a.y, a.r, 0, Math.PI * 2); ctx.fill();
    }
    raf = requestAnimationFrame(draw);
  };

  const hero = canvas.closest('.hero');
  hero.addEventListener('pointermove', e => { const r = canvas.getBoundingClientRect(); mouse.x = e.clientX - r.left; mouse.y = e.clientY - r.top; });
  hero.addEventListener('pointerleave', () => { mouse.x = mouse.y = -999; });
  window.addEventListener('resize', () => { cancelAnimationFrame(raf); resize(); draw(); });

  // Solo anima cuando el hero es visible (ahorra batería)
  new IntersectionObserver(([en]) => {
    cancelAnimationFrame(raf);
    if (en.isIntersecting) draw();
  }).observe(hero);
  resize();
})();

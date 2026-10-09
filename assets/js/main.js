/**
 * NÚCLEO JS — se carga en todas las páginas.
 * Menú dinámico, header al hacer scroll, revelado de elementos,
 * ondas en botones, modal reutilizable, efecto 3D en tarjetas,
 * botón volver arriba y avisos.
 */
(() => {
  'use strict';
  const $ = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => [...c.querySelectorAll(s)];
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const root = document.documentElement;
  const body = document.body;

  /* ---------- Loader ---------- */
  const markLoaded = () => root.classList.add('is-loaded');
  if (document.readyState === 'complete') markLoaded();
  else window.addEventListener('load', markLoaded);
  setTimeout(markLoaded, 2500); // seguridad si alguna imagen tarda

  /* ---------- Header + progreso de scroll ---------- */
  const progressEls = [$('.scroll-progress'), $('.to-top')].filter(Boolean);
  const toTop = $('[data-to-top]');
  let ticking = false;
  const onScroll = () => {
    const y = window.scrollY;
    const max = document.documentElement.scrollHeight - window.innerHeight;
    const p = max > 0 ? Math.min(y / max, 1) : 0;
    progressEls.forEach(el => el.style.setProperty('--progress', p.toFixed(4)));
    root.classList.toggle('is-scrolled', y > 40);
    toTop && toTop.classList.toggle('is-visible', y > 600);
    ticking = false;
  };
  window.addEventListener('scroll', () => { if (!ticking) { requestAnimationFrame(onScroll); ticking = true; } }, { passive: true });
  onScroll();
  toTop && toTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' }));

  /* ---------- Menú móvil ---------- */
  const nav = $('#nav-principal');
  const toggle = $('[data-nav-toggle]');
  const setNav = (open) => {
    root.classList.toggle('nav-open', open);
    body.classList.toggle('nav-open', open);
    toggle && toggle.setAttribute('aria-expanded', String(open));
    toggle && toggle.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
    if (open) setTimeout(() => $('.nav__link', nav)?.focus(), 350);
  };
  toggle && toggle.addEventListener('click', () => setNav(!root.classList.contains('nav-open')));
  $$('[data-nav-close]').forEach(b => b.addEventListener('click', () => { setNav(false); toggle?.focus(); }));
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && root.classList.contains('nav-open')) { setNav(false); toggle?.focus(); }
  });
  window.matchMedia('(min-width: 1200px)').addEventListener('change', e => e.matches && setNav(false));

  /* ---------- Indicador deslizante del menú (escritorio) ---------- */
  const list = $('.nav__list');
  const indicator = $('.nav__indicator');
  if (list && indicator) {
    const active = $('.nav__link.is-active', list);
    const moveTo = (el) => {
      if (!el || window.innerWidth < 1200) return;
      const lr = list.getBoundingClientRect(), r = el.getBoundingClientRect();
      indicator.style.setProperty('--ix', `${r.left - lr.left}px`);
      indicator.style.setProperty('--iw', `${r.width}px`);
      indicator.style.setProperty('--io', '1');
    };
    const reset = () => active ? moveTo(active) : indicator.style.setProperty('--io', '0');
    $$('.nav__link', list).forEach(a => {
      a.addEventListener('mouseenter', () => { moveTo(a); a.classList.add('is-hover'); });
      a.addEventListener('mouseleave', () => a.classList.remove('is-hover'));
      a.addEventListener('focus', () => moveTo(a));
    });
    list.addEventListener('mouseleave', reset);
    window.addEventListener('resize', reset);
    document.fonts ? document.fonts.ready.then(reset) : setTimeout(reset, 300);
    // Mientras el indicador está sobre un enlace no activo, su texto se vuelve blanco
    const style = document.createElement('style');
    style.textContent = '@media(min-width:1200px){.nav__link.is-hover,.nav__link.is-hover .icon{color:#fff}.nav__list:hover .nav__link.is-active:not(.is-hover),.nav__list:hover .nav__link.is-active:not(.is-hover) .icon{color:var(--c-teal-900)}}';
    document.head.appendChild(style);
  }

  /* ---------- Texto dividido en palabras ---------- */
  $$('[data-split]').forEach(el => {
    let w = 0;
    const walk = (node) => {
      [...node.childNodes].forEach(n => {
        if (n.nodeType === 3) {
          const frag = document.createDocumentFragment();
          n.textContent.split(/(\s+)/).forEach(part => {
            if (!part) return;
            if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(part)); return; }
            const outer = document.createElement('span');
            outer.className = 'split-word';
            outer.innerHTML = `<span style="--w:${w++}">${part}</span>`;
            frag.appendChild(outer);
          });
          n.replaceWith(frag);
        } else if (n.nodeType === 1) walk(n);
      });
    };
    walk(el);
    el.classList.add('reveal-split');
  });

  /* ---------- Revelado al hacer scroll ---------- */
  const revealEls = $$('.reveal, .reveal-split');
  if ('IntersectionObserver' in window && !reduceMotion) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(en => { if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); } });
    }, { rootMargin: '0px 0px -8% 0px', threshold: .12 });
    revealEls.forEach(el => io.observe(el));
  } else {
    revealEls.forEach(el => el.classList.add('is-in'));
  }

  /* ---------- Contadores animados [data-count] ---------- */
  const counters = $$('[data-count]');
  if (counters.length && 'IntersectionObserver' in window) {
    const co = new IntersectionObserver(entries => entries.forEach(en => {
      if (!en.isIntersecting) return;
      const el = en.target, end = +el.dataset.count, t0 = performance.now(), dur = 1400;
      const step = (t) => { const k = Math.min((t - t0) / dur, 1); el.textContent = Math.round(end * (1 - Math.pow(1 - k, 3))); if (k < 1) requestAnimationFrame(step); };
      requestAnimationFrame(step); co.unobserve(el);
    }), { threshold: .6 });
    counters.forEach(c => co.observe(c));
  }

  /* ---------- Ondas (ripple) en botones ---------- */
  document.addEventListener('pointerdown', e => {
    const btn = e.target.closest('.btn');
    if (!btn || reduceMotion) return;
    const r = btn.getBoundingClientRect();
    const s = Math.max(r.width, r.height);
    const span = document.createElement('span');
    span.className = 'ripple';
    span.style.cssText = `width:${s}px;height:${s}px;left:${e.clientX - r.left - s / 2}px;top:${e.clientY - r.top - s / 2}px`;
    btn.appendChild(span);
    setTimeout(() => span.remove(), 700);
  });

  /* ---------- Avisos (toast) ---------- */
  const toastEl = $('.toast');
  let toastTimer;
  window.toast = (msg, icon = true) => {
    if (!toastEl) return;
    toastEl.innerHTML = (icon ? '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7.5v.5"/></svg>' : '') + `<span>${msg}</span>`;
    toastEl.classList.add('is-visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toastEl.classList.remove('is-visible'), 3800);
  };
  // Enlaces aún no configurados
  document.addEventListener('click', e => {
    const a = e.target.closest('[data-pendiente]');
    if (!a) return;
    e.preventDefault();
    window.toast(a.dataset.pendiente || 'Información disponible próximamente.');
  });

  /* ---------- Modal reutilizable ---------- */
  const modal = $('#modal');
  let lastFocus = null;
  const openModal = (html) => {
    if (!modal) return;
    lastFocus = document.activeElement;
    $('.modal__content', modal).innerHTML = '';
    $('.modal__content', modal).append(html);
    modal.hidden = false;
    modal.classList.remove('is-closing');
    body.style.overflow = 'hidden';
    setTimeout(() => $('.modal__close', modal).focus(), 50);
  };
  const closeModal = () => {
    if (!modal || modal.hidden) return;
    modal.classList.add('is-closing');
    setTimeout(() => { modal.hidden = true; modal.classList.remove('is-closing'); body.style.overflow = ''; lastFocus && lastFocus.focus(); }, 280);
  };
  window.openModal = openModal;
  modal && $$('[data-modal-close]', modal).forEach(b => b.addEventListener('click', closeModal));
  document.addEventListener('keydown', e => {
    if (!modal || modal.hidden) return;
    if (e.key === 'Escape') closeModal();
    if (e.key === 'Tab') { // trampa de foco
      const f = $$('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])', modal);
      if (!f.length) return;
      const first = f[0], last = f[f.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }
  });
  // Biografías: cualquier botón con data-bio="id-del-template"
  document.addEventListener('click', e => {
    const b = e.target.closest('[data-bio]');
    if (!b) return;
    const tpl = document.getElementById(b.dataset.bio);
    if (tpl) openModal(tpl.content.cloneNode(true));
  });

  /* ---------- Efecto 3D (tilt) en tarjetas ---------- */
  if (!reduceMotion && window.matchMedia('(hover: hover)').matches) {
    $$('[data-tilt]').forEach(card => {
      card.addEventListener('pointermove', e => {
        const r = card.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
        card.style.setProperty('--ry', `${(x - .5) * 10}deg`);
        card.style.setProperty('--rx', `${(.5 - y) * 8}deg`);
        card.style.setProperty('--mx', `${x * 100}%`);
        card.style.setProperty('--my', `${y * 100}%`);
      });
      card.addEventListener('pointerleave', () => { card.style.setProperty('--rx', '0deg'); card.style.setProperty('--ry', '0deg'); });
    });
  }

  /* ---------- Parallax suave [data-parallax] ---------- */
  const par = $$('[data-parallax]');
  if (par.length && !reduceMotion) {
    window.addEventListener('scroll', () => {
      const y = window.scrollY;
      par.forEach(el => { el.style.transform = `translate3d(0, ${y * (+el.dataset.parallax || .3)}px, 0)`; });
    }, { passive: true });
  }
})();

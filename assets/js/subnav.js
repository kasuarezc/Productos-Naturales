/**
 * SUB-NAVEGACIÓN por secciones: resalta la sección visible y
 * desplaza el enlace activo para que siempre se vea en móvil.
 */
(() => {
  'use strict';
  const nav = document.querySelector('.subnav');
  if (!nav) return;
  const links = [...nav.querySelectorAll('a[href^="#"]')];
  const map = new Map(links.map(a => [a.getAttribute('href').slice(1), a]));
  const sections = [...map.keys()].map(id => document.getElementById(id)).filter(Boolean);

  const activate = (id) => {
    links.forEach(a => a.classList.toggle('is-active', a === map.get(id)));
    const a = map.get(id);
    if (a) {
      const inner = nav.querySelector('.subnav__inner');
      inner.scrollTo({ left: a.offsetLeft - inner.clientWidth / 2 + a.clientWidth / 2, behavior: 'smooth' });
    }
  };

  const io = new IntersectionObserver(entries => {
    entries.forEach(en => { if (en.isIntersecting) activate(en.target.id); });
  }, { rootMargin: '-45% 0px -50% 0px' });
  sections.forEach(s => io.observe(s));
  // Por encima de la primera sección no se resalta ningún enlace
  window.addEventListener('scroll', () => {
    if (sections[0] && sections[0].getBoundingClientRect().top > window.innerHeight * .5) links.forEach(l => l.classList.remove('is-active'));
  }, { passive: true });

  // Sombra cuando queda "pegada" bajo el header
  const sentinel = document.createElement('div');
  nav.before(sentinel);
  new IntersectionObserver(([en]) => nav.classList.toggle('is-stuck', !en.isIntersecting), { rootMargin: '-80px 0px 0px 0px' }).observe(sentinel);
})();

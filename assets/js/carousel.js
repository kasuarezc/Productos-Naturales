/**
 * CARRUSEL animado y accesible.
 * Uso: <div class="carousel" data-carousel data-autoplay="6000"> ... .carousel__slide ...
 * - Reproducción automática con barra de progreso en el punto activo
 * - Pausa al pasar el cursor / foco / pestaña oculta
 * - Deslizar con el dedo o arrastrar con el ratón
 * - Teclado: flechas izquierda/derecha
 */
(() => {
  'use strict';
  document.querySelectorAll('[data-carousel]').forEach(car => {
    const track = car.querySelector('.carousel__track');
    const slides = [...car.querySelectorAll('.carousel__slide')];
    const prev = car.querySelector('[data-prev]');
    const next = car.querySelector('[data-next]');
    const dotsWrap = car.querySelector('.carousel__dots');
    const delay = +car.dataset.autoplay || 6000;
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    car.style.setProperty('--autoplay', delay + 'ms');
    let index = 0, timer, perView = 1, pages = 1;

    const readPerView = () => parseInt(getComputedStyle(car).getPropertyValue('--per-view')) || 1;

    const buildDots = () => {
      dotsWrap.innerHTML = '';
      for (let i = 0; i < pages; i++) {
        const b = document.createElement('button');
        b.className = 'carousel__dot'; b.type = 'button';
        b.setAttribute('aria-label', `Ir a la noticia ${i + 1}`);
        b.addEventListener('click', () => { go(i); restart(); });
        dotsWrap.appendChild(b);
      }
    };

    const go = (i) => {
      index = (i + pages) % pages;
      const slideW = slides[0].offsetWidth; // offsetWidth ignora el scale() de la animación
      const gap = parseFloat(getComputedStyle(track).gap) || 0;
      track.style.transform = `translateX(${-index * (slideW + gap)}px)`;
      slides.forEach((s, k) => {
        const vis = k >= index && k < index + perView;
        s.classList.toggle('is-visible', vis);
        s.setAttribute('aria-hidden', String(!vis));
        s.querySelectorAll('a, button').forEach(el => vis ? el.removeAttribute('tabindex') : el.setAttribute('tabindex', '-1'));
      });
      [...dotsWrap.children].forEach((d, k) => {
        d.classList.toggle('is-active', k === index);
        // reinicia la animación de progreso
        if (k === index) { d.classList.remove('is-active'); void d.offsetWidth; d.classList.add('is-active'); }
      });
    };

    const stop = () => clearInterval(timer);
    const start = () => { if (reduce || pages < 2) return; stop(); timer = setInterval(() => go(index + 1), delay); };
    const restart = () => { stop(); if (!car.classList.contains('is-paused')) start(); };

    const layout = () => {
      perView = readPerView();
      pages = Math.max(1, slides.length - perView + 1);
      buildDots();
      go(Math.min(index, pages - 1));
    };

    prev && prev.addEventListener('click', () => { go(index - 1); restart(); });
    next && next.addEventListener('click', () => { go(index + 1); restart(); });
    car.addEventListener('mouseenter', () => { car.classList.add('is-paused'); stop(); });
    car.addEventListener('mouseleave', () => { car.classList.remove('is-paused'); start(); });
    car.addEventListener('focusin', () => { car.classList.add('is-paused'); stop(); });
    car.addEventListener('focusout', () => { car.classList.remove('is-paused'); start(); });
    car.addEventListener('keydown', e => {
      if (e.key === 'ArrowRight') { go(index + 1); restart(); }
      if (e.key === 'ArrowLeft')  { go(index - 1); restart(); }
    });
    document.addEventListener('visibilitychange', () => document.hidden ? stop() : start());

    // Gestos táctiles / arrastre
    let x0 = null, dx = 0;
    const vp = car.querySelector('.carousel__viewport');
    vp.addEventListener('pointerdown', e => { x0 = e.clientX; dx = 0; stop(); });
    vp.addEventListener('pointermove', e => { if (x0 !== null) dx = e.clientX - x0; });
    const end = () => {
      if (x0 === null) return;
      if (Math.abs(dx) > 50) go(index + (dx < 0 ? 1 : -1));
      x0 = null; restart();
    };
    vp.addEventListener('pointerup', end);
    vp.addEventListener('pointercancel', end);
    vp.addEventListener('click', e => { if (Math.abs(dx) > 10) e.preventDefault(); }, true);

    let rt;
    window.addEventListener('resize', () => { clearTimeout(rt); rt = setTimeout(layout, 150); });
    layout(); start();
  });
})();

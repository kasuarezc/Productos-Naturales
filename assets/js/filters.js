/**
 * FILTROS dinámicos (Comités y Conferencistas, Noticias).
 * <div data-filter-group="personas"> <button data-filter="*|grupo"> ...
 * Elementos filtrables: [data-filter-item="personas"] con data-grupo / data-categoria
 * Búsqueda opcional: <input data-filter-search="noticias">
 */
(() => {
  'use strict';
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  document.querySelectorAll('[data-filter-group]').forEach(group => {
    const name = group.dataset.filterGroup;
    const items = [...document.querySelectorAll(`[data-filter-item="${name}"]`)];
    const buttons = [...group.querySelectorAll('[data-filter]')];
    const search = document.querySelector(`[data-filter-search="${name}"]`);
    const empty = document.querySelector(`[data-filter-empty="${name}"]`);
    let current = '*';

    // Contadores por filtro
    buttons.forEach(b => {
      const f = b.dataset.filter;
      const n = f === '*' ? items.length : items.filter(i => (i.dataset.grupo || i.dataset.categoria || '').split(' ').includes(f) || i.dataset.tipo === f).length;
      const c = b.querySelector('.count');
      if (c) c.textContent = n;
    });

    const apply = () => {
      const q = (search?.value || '').trim().toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
      let shown = 0;
      items.forEach((it, k) => {
        const key = (it.dataset.grupo || it.dataset.categoria || '');
        const okF = current === '*' || key === current || it.dataset.tipo === current;
        const txt = it.textContent.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
        const okQ = !q || txt.includes(q);
        const show = okF && okQ;
        it.classList.toggle('is-hidden', !show);
        if (show) {
          shown++;
          if (!reduce) {
            it.classList.remove('is-in');
            it.style.setProperty('--d', `${(shown - 1) % 8 * 60}ms`);
            requestAnimationFrame(() => requestAnimationFrame(() => it.classList.add('is-in')));
          }
        }
      });
      empty && empty.classList.toggle('is-visible', shown === 0);
    };

    buttons.forEach(b => b.addEventListener('click', () => {
      buttons.forEach(x => { x.classList.toggle('is-active', x === b); x.setAttribute('aria-pressed', String(x === b)); });
      current = b.dataset.filter;
      apply();
      if (history.replaceState) history.replaceState(null, '', current === '*' ? location.pathname : `#${current}`);
    }));
    search && search.addEventListener('input', apply);

    // Filtro inicial desde el hash (#internacional, #organizador…)
    const h = decodeURIComponent(location.hash.slice(1));
    const initial = buttons.find(b => b.dataset.filter === h);
    if (initial) initial.click();
  });
})();

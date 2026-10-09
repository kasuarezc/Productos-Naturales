/**
 * FORMULARIO DE CONTACTO — validación en vivo y envío AJAX a api/contacto.php
 */
(() => {
  'use strict';
  const form = document.querySelector('[data-contact-form]');
  if (!form) return;
  const card = form.closest('.form-card');
  const btn = form.querySelector('[type="submit"]');
  const alertBox = form.querySelector('.form-alert');
  const msg = form.querySelector('#mensaje');
  const counter = form.querySelector('[data-counter]');
  const MAX = +(msg?.getAttribute('maxlength') || 2000);

  const rules = {
    nombre:      v => v.trim().length >= 3 || 'Escribe tu nombre completo.',
    correo:      v => /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()) || 'Escribe un correo electrónico válido.',
    telefono:    v => !v.trim() || /^[+\d][\d\s-]{6,19}$/.test(v.trim()) || 'Número no válido (solo dígitos, espacios o +).',
    institucion: v => v.trim().length >= 2 || 'Indica tu institución o empresa.',
    pais:        v => v.trim().length >= 2 || 'Indica tu país.',
    perfil:      v => !!v || 'Selecciona tu perfil.',
    asunto:      v => !!v || 'Selecciona el motivo de tu mensaje.',
    mensaje:     v => v.trim().length >= 20 || 'Cuéntanos un poco más (mínimo 20 caracteres).',
    acepto:      (v, el) => el.checked || 'Debes autorizar el tratamiento de datos.'
  };

  const validate = (el) => {
    const rule = rules[el.name];
    if (!rule) return true;
    const res = rule(el.value, el);
    const field = el.closest('.field') || el.closest('.check')?.parentElement;
    const err = field?.querySelector('.field__error');
    const ok = res === true;
    field?.classList.toggle('has-error', !ok);
    field?.classList.toggle('is-valid', ok && el.type !== 'checkbox' && el.value.trim() !== '');
    el.setAttribute('aria-invalid', String(!ok));
    if (err) err.textContent = ok ? '' : res;
    return ok;
  };

  form.querySelectorAll('input, select, textarea').forEach(el => {
    el.addEventListener('blur', () => el.value !== '' && validate(el));
    el.addEventListener('input', () => el.closest('.has-error') && validate(el));
    el.addEventListener('change', () => validate(el));
  });

  if (msg && counter) {
    const upd = () => { counter.textContent = `${msg.value.length} / ${MAX}`; };
    msg.addEventListener('input', upd); upd();
  }

  const showAlert = (text) => {
    alertBox.querySelector('p').textContent = text;
    alertBox.classList.add('is-visible');
    alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
  };

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    alertBox.classList.remove('is-visible');
    const fields = [...form.querySelectorAll('[name]')].filter(el => rules[el.name]);
    const results = fields.map(validate);
    if (results.includes(false)) {
      const first = form.querySelector('.has-error input, .has-error select, .has-error textarea');
      first && first.focus();
      window.toast && window.toast('Revisa los campos marcados en rojo.');
      return;
    }

    btn.classList.add('is-loading'); btn.disabled = true;
    try {
      const res = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'Accept': 'application/json' } });
      const data = await res.json().catch(() => ({ ok: false, mensaje: 'Respuesta inesperada del servidor.' }));
      if (res.ok && data.ok) {
        const nameEl = card.querySelector('[data-success-name]');
        if (nameEl) nameEl.textContent = form.nombre.value.trim().split(' ')[0];
        card.classList.add('is-sent');
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        form.reset();
      } else {
        if (data.errores) Object.entries(data.errores).forEach(([k, v]) => {
          const el = form.querySelector(`[name="${k}"]`); const err = el?.closest('.field')?.querySelector('.field__error');
          el?.closest('.field')?.classList.add('has-error'); if (err) err.textContent = v;
        });
        showAlert(data.mensaje || 'No fue posible enviar tu mensaje. Inténtalo de nuevo.');
      }
    } catch (err) {
      showAlert('No hay conexión con el servidor. Verifica tu internet o escríbenos directamente al correo.');
    } finally {
      btn.classList.remove('is-loading'); btn.disabled = false;
    }
  });

  const again = card.querySelector('[data-send-again]');
  again && again.addEventListener('click', () => { card.classList.remove('is-sent'); form.querySelector('input:not([type=hidden])').focus(); });
})();

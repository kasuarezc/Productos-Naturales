/**
 * ============================================================
 *  FORMULARIO DE CONTACTO – II Congreso Colombiano de Productos Naturales
 *  Google Apps Script (Web App) para la cuenta sequiamaz@uniamazonia.edu.co
 * ============================================================
 *  Qué hace con cada mensaje del sitio:
 *   1. Lo guarda como una fila en la hoja de cálculo "Contactos Congreso".
 *   2. Envía una notificación a sequiamaz@uniamazonia.edu.co
 *      (con "Responder a" apuntando al remitente).
 *   3. Envía una respuesta automática de confirmación al remitente.
 *
 *  Instalación: ver docs/GUIA-PASO-A-PASO.md (Paso 6).
 * ============================================================
 */

// ⚠️ Debe ser idéntica a GAS_SECRET en includes/config.php
const SECRET = 'CongresoPN2027-5347001f9fefae5d';

const CORREO_DESTINO = 'sequiamaz@uniamazonia.edu.co';
const NOMBRE_EVENTO  = 'II Congreso Colombiano de Productos Naturales';
const NOMBRE_HOJA    = 'Mensajes';
const COLUMNAS = ['Fecha', 'Nombre', 'Correo', 'Teléfono', 'Institución', 'País', 'Perfil', 'Asunto', 'Mensaje', 'Autoriza datos', 'Estado', 'Origen'];

/** Recibe el POST desde api/contacto.php */
function doPost(e) {
  const lock = LockService.getScriptLock();
  try {
    lock.waitLock(20000);
    const d = JSON.parse(e.postData.contents || '{}');

    if (d.secret !== SECRET) return json_({ ok: false, error: 'No autorizado' });
    if (!d.nombre || !d.correo || !d.mensaje) return json_({ ok: false, error: 'Datos incompletos' });

    const hoja = hoja_();
    hoja.appendRow([
      new Date(), d.nombre, d.correo, d.telefono || '', d.institucion || '', d.pais || '',
      d.perfil || '', d.asunto || '', d.mensaje, d.acepto || '', 'Nuevo', d.origen || ''
    ]);

    notificar_(d);
    autorespuesta_(d);

    return json_({ ok: true });
  } catch (err) {
    console.error(err);
    return json_({ ok: false, error: String(err) });
  } finally {
    lock.releaseLock();
  }
}

/** Permite comprobar en el navegador que el Web App está activo. */
function doGet() {
  return json_({ ok: true, servicio: 'Formulario de contacto ' + NOMBRE_EVENTO });
}

/** Obtiene (o crea la primera vez) la hoja de cálculo de mensajes. */
function hoja_() {
  const props = PropertiesService.getScriptProperties();
  let id = props.getProperty('SHEET_ID');
  let libro;
  if (id) {
    libro = SpreadsheetApp.openById(id);
  } else {
    libro = SpreadsheetApp.create('Contactos Congreso – Productos Naturales 2027');
    props.setProperty('SHEET_ID', libro.getId());
  }
  let hoja = libro.getSheetByName(NOMBRE_HOJA);
  if (!hoja) {
    hoja = libro.getSheets()[0];
    hoja.setName(NOMBRE_HOJA);
  }
  if (hoja.getLastRow() === 0) {
    hoja.appendRow(COLUMNAS);
    hoja.getRange(1, 1, 1, COLUMNAS.length)
      .setFontWeight('bold').setBackground('#048C8C').setFontColor('#FFFFFF');
    hoja.setFrozenRows(1);
    hoja.setColumnWidth(9, 420);
    // Lista desplegable de estado para hacer seguimiento
    const regla = SpreadsheetApp.newDataValidation().requireValueInList(['Nuevo', 'En proceso', 'Respondido', 'Cerrado']).build();
    hoja.getRange('K2:K').setDataValidation(regla);
  }
  return hoja;
}

function notificar_(d) {
  const filas = [
    ['Nombre', d.nombre], ['Correo', d.correo], ['Teléfono', d.telefono || '—'],
    ['Institución', d.institucion], ['País', d.pais], ['Perfil', d.perfil], ['Asunto', d.asunto]
  ].map(([k, v]) => `<tr><td style="padding:6px 12px;background:#E4F2E5;font-weight:bold">${k}</td><td style="padding:6px 12px">${esc_(v)}</td></tr>`).join('');
  const html = `
    <div style="font-family:Arial,sans-serif;max-width:640px">
      <div style="background:#048C8C;color:#fff;padding:16px 20px;border-radius:8px 8px 0 0">
        <strong>Nuevo mensaje desde el sitio web</strong><br><small>${NOMBRE_EVENTO}</small>
      </div>
      <table style="border-collapse:collapse;width:100%;border:1px solid #CFE3D3">${filas}</table>
      <div style="padding:16px 20px;border:1px solid #CFE3D3;border-top:0;white-space:pre-wrap">${esc_(d.mensaje)}</div>
      <p style="color:#666;font-size:12px">Responde directamente a este correo para contestar a ${esc_(d.nombre)}.</p>
    </div>`;
  MailApp.sendEmail({
    to: CORREO_DESTINO,
    replyTo: d.correo,
    subject: `[Congreso PN] ${d.asunto} – ${d.nombre}`,
    htmlBody: html,
    name: 'Sitio web ' + NOMBRE_EVENTO
  });
}

function autorespuesta_(d) {
  const nombre = String(d.nombre).split(' ')[0];
  const html = `
    <div style="font-family:Arial,sans-serif;max-width:600px;color:#13302A">
      <div style="background:linear-gradient(135deg,#048C8C,#96A61C);color:#fff;padding:22px;border-radius:10px 10px 0 0">
        <h2 style="margin:0">¡Hola, ${esc_(nombre)}!</h2>
        <p style="margin:6px 0 0">Recibimos tu mensaje.</p>
      </div>
      <div style="padding:22px;border:1px solid #CFE3D3;border-top:0;border-radius:0 0 10px 10px">
        <p>Gracias por escribir al comité organizador del <strong>${NOMBRE_EVENTO}</strong>.
        Tu consulta sobre <strong>${esc_(d.asunto)}</strong> fue registrada y te responderemos lo antes posible.</p>
        <p style="background:#E4F2E5;padding:12px;border-radius:8px;white-space:pre-wrap"><em>${esc_(d.mensaje)}</em></p>
        <p>📅 11 – 14 de mayo de 2027 · 📍 Universidad de la Amazonia, Florencia, Caquetá</p>
        <p style="color:#45605A;font-size:12px">Este es un mensaje automático. Si necesitas agregar información, responde a este correo.</p>
      </div>
    </div>`;
  MailApp.sendEmail({
    to: d.correo,
    replyTo: CORREO_DESTINO,
    subject: 'Recibimos tu mensaje – ' + NOMBRE_EVENTO,
    htmlBody: html,
    name: NOMBRE_EVENTO
  });
}

function json_(obj) {
  return ContentService.createTextOutput(JSON.stringify(obj)).setMimeType(ContentService.MimeType.JSON);
}

function esc_(s) {
  return String(s || '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

/** Ejecuta esta función UNA VEZ desde el editor para autorizar permisos y crear la hoja. */
function configurarPrimeraVez() {
  const h = hoja_();
  Logger.log('Hoja lista: ' + h.getParent().getUrl());
}

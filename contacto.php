<?php
$PAGE = [
    'key'         => 'contacto',
    'titulo'      => 'Contacto',
    'descripcion' => 'Escríbenos: inscripciones, envío de resúmenes, Escuela EAAQ, patrocinios y alojamiento. II Congreso Colombiano de Productos Naturales.',
    'js'          => ['contact.js'],
];
require_once __DIR__ . '/includes/config.php';
$csrf = csrf_token(); // la sesión debe iniciarse antes de enviar HTML
require __DIR__ . '/includes/header.php';
$perfiles = ['Estudiante de pregrado', 'Estudiante de posgrado', 'Docente / Investigador', 'Profesional', 'Sector empresarial', 'Otro'];
$asuntos = ['Inscripciones y pagos', 'Envío de resúmenes', 'Escuela Andino-Amazónica de Química', 'Patrocinios y alianzas', 'Alojamiento y llegada a Florencia', 'Prensa y comunicaciones', 'Otro'];
?>

<?= page_hero('Contacto', '¿Tienes preguntas sobre inscripciones, resúmenes, cursos o tu viaje a Florencia? El comité organizador te responderá.', 'Hablemos', 'mail', null, 'soft') ?>

<section class="section section--soft" aria-labelledby="form-t">
  <div class="container">
    <div class="contact">
      <aside class="contact-info">
        <div class="info-card reveal">
          <div class="card__icon"><?= icon('mail') ?></div>
          <div><h3>Correo electrónico</h3><p><a href="mailto:<?= e($SITE['correo']) ?>"><?= e($SITE['correo']) ?></a></p></div>
        </div>
        <div class="info-card reveal" style="--d:80ms">
          <div class="card__icon"><?= icon('pin') ?></div>
          <div><h3>Dirección</h3><p><?= e($SITE['universidad']) ?><br><?= e($SITE['direccion']) ?><br><?= e($SITE['direccion_2']) ?></p></div>
        </div>
        <div class="info-card reveal" style="--d:160ms">
          <div class="card__icon"><?= icon('calendar') ?></div>
          <div><h3>Fecha del evento</h3><p><?= e($SITE['fecha_texto']) ?></p></div>
        </div>
        <?php if ($SITE['telefono']): ?>
        <div class="info-card reveal" style="--d:240ms">
          <div class="card__icon"><?= icon('phone') ?></div>
          <div><h3>Teléfono</h3><p><?= e($SITE['telefono']) ?></p></div>
        </div>
        <?php endif; ?>
        <div class="info-card reveal" style="--d:240ms">
          <div class="card__icon"><?= icon('clock') ?></div>
          <div><h3>Tiempo de respuesta</h3><p>Respondemos en días hábiles. Recibirás una confirmación automática en tu correo.</p></div>
        </div>
        <div class="reveal" style="--d:300ms"><?= redes_html('social--contact') ?></div>
      </aside>

      <div class="form-card reveal reveal--right">
        <span class="eyebrow">Formulario de contacto</span>
        <h2 id="form-t">Envíanos tu mensaje</h2>
        <p style="color:var(--c-ink-soft)">Los campos marcados con <span style="color:var(--c-orange)">*</span> son obligatorios.</p>

        <form class="form" action="<?= e(url('api/contacto.php')) ?>" method="post" novalidate data-contact-form>
          <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
          <input type="hidden" name="ts" value="<?= time() ?>">
          <div class="hp" aria-hidden="true"><label>No llenar <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

          <div class="form-row">
            <div class="field">
              <label for="nombre">Nombre completo <span class="req">*</span></label>
              <div class="field__wrap"><?= icon('user') ?><input class="input" id="nombre" name="nombre" type="text" autocomplete="name" maxlength="120" required placeholder="Ej.: María Pérez Gómez"></div>
              <span class="field__error" aria-live="polite"></span>
            </div>
            <div class="field">
              <label for="correo">Correo electrónico <span class="req">*</span></label>
              <div class="field__wrap"><?= icon('mail') ?><input class="input" id="correo" name="correo" type="email" autocomplete="email" maxlength="160" required placeholder="nombre@institucion.edu.co"></div>
              <span class="field__error" aria-live="polite"></span>
            </div>
          </div>

          <div class="form-row">
            <div class="field">
              <label for="telefono">Teléfono / WhatsApp</label>
              <div class="field__wrap"><?= icon('phone') ?><input class="input" id="telefono" name="telefono" type="tel" autocomplete="tel" maxlength="25" placeholder="+57 300 000 0000"></div>
              <span class="field__error" aria-live="polite"></span>
            </div>
            <div class="field">
              <label for="pais">País <span class="req">*</span></label>
              <div class="field__wrap"><?= icon('globe') ?><input class="input" id="pais" name="pais" type="text" autocomplete="country-name" maxlength="80" required value="Colombia"></div>
              <span class="field__error" aria-live="polite"></span>
            </div>
          </div>

          <div class="field">
            <label for="institucion">Institución o empresa <span class="req">*</span></label>
            <div class="field__wrap"><?= icon('building') ?><input class="input" id="institucion" name="institucion" type="text" autocomplete="organization" maxlength="160" required placeholder="Universidad, centro de investigación, empresa…"></div>
            <span class="field__error" aria-live="polite"></span>
          </div>

          <div class="form-row">
            <div class="field">
              <label for="perfil">Perfil <span class="req">*</span></label>
              <div class="field__wrap"><?= icon('cap') ?>
                <select class="input" id="perfil" name="perfil" required>
                  <option value="">Selecciona…</option>
                  <?php foreach ($perfiles as $p): ?><option><?= e($p) ?></option><?php endforeach; ?>
                </select>
              </div>
              <span class="field__error" aria-live="polite"></span>
            </div>
            <div class="field">
              <label for="asunto">Motivo del mensaje <span class="req">*</span></label>
              <div class="field__wrap"><?= icon('flask') ?>
                <select class="input" id="asunto" name="asunto" required>
                  <option value="">Selecciona…</option>
                  <?php foreach ($asuntos as $a): ?><option><?= e($a) ?></option><?php endforeach; ?>
                </select>
              </div>
              <span class="field__error" aria-live="polite"></span>
            </div>
          </div>

          <div class="field">
            <label for="mensaje">Mensaje <span class="req">*</span></label>
            <div class="field__wrap field__wrap--area"><?= icon('news') ?><textarea class="input" id="mensaje" name="mensaje" maxlength="2000" required placeholder="Cuéntanos en qué podemos ayudarte…"></textarea></div>
            <div class="field__hint"><span class="field__error" aria-live="polite"></span><span data-counter>0 / 2000</span></div>
          </div>

          <div>
            <label class="check">
              <input type="checkbox" name="acepto" value="1" required>
              <span class="check__box"><?= icon('check') ?></span>
              <span>Autorizo a la Universidad de la Amazonia el tratamiento de mis datos personales conforme a la Ley 1581 de 2012, únicamente para responder esta solicitud y enviarme información del congreso. <a href="#tratamiento-datos">Ver detalle</a>. <span class="req" style="color:var(--c-orange)">*</span></span>
            </label>
            <span class="field__error" aria-live="polite"></span>
          </div>

          <div class="notice form-alert" role="alert"><?= icon('alert') ?><p></p></div>

          <button class="btn btn--accent btn--lg btn--block" type="submit">
            <span class="spinner" aria-hidden="true"></span><?= icon('send') ?><span class="btn__label">Enviar mensaje</span>
          </button>
        </form>

        <div class="form-success" role="status" aria-live="polite">
          <div class="form-success__icon"><?= icon('check') ?></div>
          <h2>¡Gracias, <span data-success-name></span>!</h2>
          <p>Recibimos tu mensaje. Te enviamos una confirmación al correo y el comité organizador te responderá pronto.</p>
          <button class="btn btn--outline" type="button" data-send-again><?= icon('mail') ?><span>Enviar otro mensaje</span></button>
        </div>
      </div>
    </div>

    <div class="legal reveal" id="tratamiento-datos" style="margin-top:2.5rem">
      <h3><?= icon('info') ?> Tratamiento de datos personales</h3>
      <p class="mb-0">Los datos suministrados en este formulario serán tratados por la Universidad de la Amazonia, en el marco del II Congreso Colombiano de Productos Naturales, de acuerdo con la Ley 1581 de 2012 y el Decreto 1377 de 2013, con la finalidad exclusiva de atender su solicitud y comunicar información relacionada con el evento. Como titular puede conocer, actualizar, rectificar y solicitar la supresión de sus datos escribiendo a <a href="mailto:<?= e($SITE['correo']) ?>"><?= e($SITE['correo']) ?></a>.</p>
    </div>

    <div class="map reveal" style="margin-top:2.5rem">
      <iframe title="Mapa: Universidad de la Amazonia, Sede Porvenir" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
        src="https://maps.google.com/maps?q=<?= rawurlencode($SITE['mapa_query']) ?>&z=15&output=embed"></iframe>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

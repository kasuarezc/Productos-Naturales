<?php
/** FOOTER ESTÁNDAR */
$aliados = data('aliados');
$fechas = data('fechas');
?>
  </main>

  <footer class="site-footer">
    <svg class="footer-wave" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true">
      <path class="wave wave--1" d="M0,64 C240,120 480,0 720,48 C960,96 1200,24 1440,64 L1440,120 L0,120 Z"/>
      <path class="wave wave--2" d="M0,80 C260,30 520,110 760,70 C1000,30 1220,100 1440,70 L1440,120 L0,120 Z"/>
    </svg>

    <div class="footer-main">
      <div class="container">

        <!-- Entidades aliadas -->
        <section class="footer-allies" aria-labelledby="aliados-t">
          <h2 class="footer-title footer-title--center" id="aliados-t"><?= icon('handshake') ?> Entidades aliadas</h2>
          <ul class="allies">
            <?php foreach ($aliados as $i => $a): ?>
              <li class="ally reveal" style="--d:<?= $i * 90 ?>ms">
                <?php $tag = enlace($a['url'] ?? '') ? 'a' : 'div'; ?>
                <<?= $tag ?> class="ally__box"<?= $tag === 'a' ? ' href="' . e($a['url']) . '" target="_blank" rel="noopener"' : '' ?> title="<?= e($a['nombre']) ?>">
                  <?= imagen($a['logo'] ?? '', $a['nombre'], 'ally__img', 'Logo pendiente') ?>
                </<?= $tag ?>>
              </li>
            <?php endforeach; ?>
          </ul>
        </section>

        <div class="footer-grid">
          <!-- Marca -->
          <div class="footer-col footer-col--brand">
            <a href="<?= e(url()) ?>" class="footer-logo" aria-label="Inicio"><?= logo_svg('footer') ?></a>
            <p class="footer-lema"><?= e($SITE['lema']) ?>.</p>
            <ul class="footer-events">
              <li>X Seminario Internacional de Química Aplicada para la Amazonía – SEQUIAMAZ</li>
              <li>III Simposio Internacional de Alimentos Funcionales para la Amazonía</li>
              <li>V Escuela Andino-Amazónica de Química – EAAQ</li>
            </ul>
            <?= redes_html('social--footer') ?>
          </div>

          <!-- Fechas clave -->
          <div class="footer-col">
            <h2 class="footer-title"><?= icon('calendar') ?> Fechas clave</h2>
            <ol class="timeline-mini">
              <?php foreach ($fechas as $f): ?>
                <li class="<?= empty($f['fecha']) ? 'is-pending' : '' ?>">
                  <span class="timeline-mini__dot" aria-hidden="true"></span>
                  <strong><?= e($f['fecha_texto'] ?: 'Por confirmar') ?></strong>
                  <span><?= e($f['actividad']) ?></span>
                </li>
              <?php endforeach; ?>
            </ol>
          </div>

          <!-- Enlaces -->
          <div class="footer-col">
            <h2 class="footer-title"><?= icon('molecule') ?> Navegación</h2>
            <ul class="footer-links">
              <?php foreach ($MENU as $item): ?>
                <li><a href="<?= e(url($item['url'])) ?>"><?= icon('chev-r') ?><?= e($item['label']) ?></a></li>
              <?php endforeach; ?>
              <li><a href="<?= e($LINKS['plantilla_resumen']) ?>" download><?= icon('chev-r') ?>Plantilla de resúmenes</a></li>
            </ul>
          </div>

          <!-- Contacto -->
          <div class="footer-col">
            <h2 class="footer-title"><?= icon('pin') ?> Dirección</h2>
            <address class="footer-address">
              <p><strong><?= e($SITE['universidad']) ?></strong><br>
                <?= e($SITE['direccion']) ?><br>
                <?= e($SITE['direccion_2']) ?></p>
              <p><a href="mailto:<?= e($SITE['correo']) ?>"><?= icon('mail') ?> <span><?= str_replace('@', '@<wbr>', e($SITE['correo'])) ?></span></a></p>
              <?php if ($SITE['telefono']): ?>
                <p><a href="tel:<?= e(preg_replace('/\s+/', '', $SITE['telefono'])) ?>"><?= icon('phone') ?> <?= e($SITE['telefono']) ?></a></p>
              <?php endif; ?>
              <a class="btn btn--light btn--sm" href="https://www.google.com/maps/search/?api=1&query=<?= rawurlencode($SITE['mapa_query']) ?>" target="_blank" rel="noopener"><?= icon('pin') ?><span>Cómo llegar</span></a>
            </address>
          </div>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="container footer-bottom__inner">
        <p>© <?= date('Y') ?> <?= e($SITE['nombre']) ?> · <?= e($SITE['universidad']) ?>. Todos los derechos reservados.</p>
        <p>Desarrollado por <a href="https://karol-suarez-portfolio.web.app/" target="_blank" rel="noopener">Karol Andres Suarez</a> </a></p>
        <p><a href="<?= e(url('contacto.php')) ?>">Contacto</a> · <a href="<?= e(url('contacto.php#tratamiento-datos')) ?>">Tratamiento de datos</a></p>
      </div>
    </div>
  </footer>

  <button class="to-top" type="button" aria-label="Volver arriba" data-to-top><?= icon('up') ?>
    <svg class="to-top__ring" viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="21"/></svg>
  </button>

  <!-- Modal reutilizable (biografías, etc.) -->
  <div class="modal" id="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title" hidden>
    <div class="modal__backdrop" data-modal-close></div>
    <div class="modal__panel" role="document">
      <button class="modal__close" type="button" aria-label="Cerrar" data-modal-close><?= icon('close') ?></button>
      <div class="modal__content"></div>
    </div>
  </div>

  <!-- Avisos breves -->
  <div class="toast" role="status" aria-live="polite"></div>

  <script src="<?= e(asset('js/main.js')) ?>" defer></script>
  <?php foreach (($PAGE['js'] ?? []) as $js): ?>
    <script src="<?= e(asset('js/' . $js)) ?>" defer></script>
  <?php endforeach; ?>
</body>
</html>

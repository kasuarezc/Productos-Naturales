<?php
if (!headers_sent()) http_response_code(404);
$PAGE = ['key' => '404', 'titulo' => 'Página no encontrada'];
require_once __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/header.php';
?>
<section class="section notfound">
  <div class="container">
    <?= icon('flask') ?>
    <p class="notfound__code">404</p>
    <h1 class="section-title">Este experimento no dio resultado</h1>
    <p class="section-lead">La página que buscas no existe o fue movida.</p>
    <div class="btn-group" style="justify-content:center">
      <a class="btn btn--accent" href="<?= e(url()) ?>"><?= icon('flask') ?><span>Volver al inicio</span></a>
      <a class="btn btn--outline" href="<?= e(url('contacto.php')) ?>"><?= icon('mail') ?><span>Contáctanos</span></a>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

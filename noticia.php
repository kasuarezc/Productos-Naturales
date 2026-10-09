<?php
require_once __DIR__ . '/includes/config.php';
$slug = $_GET['slug'] ?? '';
$lista = noticias();
$idx = null;
foreach ($lista as $k => $n) {
    if (($n['slug'] ?? '') === $slug) { $idx = $k; break; }
}
if ($idx === null) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}
$n = $lista[$idx];
$PAGE = [
    'key'         => 'noticias',
    'titulo'      => $n['titulo'],
    'descripcion' => resumen($n['resumen'] ?? '', 155),
];
require __DIR__ . '/includes/header.php';
$anterior = $lista[$idx + 1] ?? null; // más antigua
$siguiente = $lista[$idx - 1] ?? null; // más reciente
?>

<section class="page-hero page-hero--plain">
  <span class="page-hero__deco" aria-hidden="true"><?= icon('news') ?></span>
  <div class="container container--narrow">
    <nav class="breadcrumb reveal" aria-label="Ruta de navegación">
      <a href="<?= e(url()) ?>">Inicio</a><?= icon('chev-r') ?><a href="<?= e(url('noticias.php')) ?>">Noticias</a><?= icon('chev-r') ?><span aria-current="page"><?= e(resumen($n['titulo'], 40)) ?></span>
    </nav>
    <span class="eyebrow reveal" style="margin-top:1.25rem"><?= e($n['categoria']) ?></span>
    <h1 class="page-hero__title" style="max-width:none" data-split><?= e($n['titulo']) ?></h1>
    <div class="article__meta reveal"><span><?= icon('calendar') ?> <?= fecha_es($n['fecha']) ?></span><span><?= icon('user') ?> Comité organizador</span></div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <article class="container article">
    <div class="article__cover reveal"><?= imagen($n['imagen'] ?? '', $n['titulo'], '', 'Imagen de la noticia pendiente') ?></div>
    <div class="article__body reveal">
      <p><strong><?= e($n['resumen']) ?></strong></p>
      <?php foreach (($n['contenido'] ?? []) as $p): ?><p><?= e($p) ?></p><?php endforeach; ?>
    </div>
    <nav class="article__nav" aria-label="Más noticias">
      <?php if ($anterior): ?><a class="btn btn--ghost" href="<?= e(url('noticia.php?slug=' . rawurlencode($anterior['slug']))) ?>"><?= icon('arrow-l') ?><span>Anterior</span></a><?php else: ?><span></span><?php endif; ?>
      <a class="btn btn--outline" href="<?= e(url('noticias.php')) ?>"><?= icon('news') ?><span>Todas las noticias</span></a>
      <?php if ($siguiente): ?><a class="btn btn--ghost" href="<?= e(url('noticia.php?slug=' . rawurlencode($siguiente['slug']))) ?>"><span>Siguiente</span><?= icon('arrow') ?></a><?php else: ?><span></span><?php endif; ?>
    </nav>
  </article>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

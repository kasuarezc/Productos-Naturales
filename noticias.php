<?php
$PAGE = [
    'key'         => 'noticias',
    'titulo'      => 'Noticias',
    'descripcion' => 'Noticias, anuncios y novedades del II Congreso Colombiano de Productos Naturales.',
    'js'          => ['filters.js'],
];
require __DIR__ . '/includes/header.php';
$lista = noticias();
$categorias = array_values(array_unique(array_column($lista, 'categoria')));
?>

<?= page_hero('Noticias', 'Anuncios, novedades y avances del congreso y sus eventos asociados.', 'Actualidad', 'news', 'img/sede/idema.jpg', 'soft') ?>

<section class="section section--soft" aria-labelledby="lista-t">
  <div class="container">
    <h2 class="visually-hidden" id="lista-t">Listado de noticias</h2>
    <div class="news-toolbar reveal">
      <div class="filters" data-filter-group="noticias" role="toolbar" aria-label="Filtrar por categoría">
        <button class="filter-btn is-active" type="button" data-filter="*" aria-pressed="true">Todas <span class="count"></span></button>
        <?php foreach ($categorias as $c): ?>
          <button class="filter-btn" type="button" data-filter="<?= e($c) ?>" aria-pressed="false"><?= e($c) ?> <span class="count"></span></button>
        <?php endforeach; ?>
      </div>
      <label class="search">
        <span class="visually-hidden">Buscar noticias</span>
        <?= icon('search') ?><input class="input" type="search" placeholder="Buscar noticias…" data-filter-search="noticias">
      </label>
    </div>

    <div class="news-grid">
      <?php foreach ($lista as $i => $n): ?>
        <div class="reveal" data-filter-item="noticias" data-categoria="<?= e($n['categoria']) ?>" style="--d:<?= ($i % 3) * 100 ?>ms"><?= news_card($n) ?></div>
      <?php endforeach; ?>
    </div>
    <p class="empty" data-filter-empty="noticias"><?= icon('search') ?>No encontramos noticias con ese criterio.</p>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

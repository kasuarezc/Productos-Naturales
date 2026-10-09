<?php
/**
 * Tarjeta de noticia (usada en el carrusel de Inicio y en Noticias).
 */
function news_card(array $n, string $extra = ''): string
{
    $href = url('noticia.php?slug=' . rawurlencode($n['slug'] ?? ''));
    ob_start(); ?>
<article class="news-card <?= e($extra) ?>" data-categoria="<?= e($n['categoria'] ?? '') ?>">
  <a class="news-card__media" href="<?= e($href) ?>" tabindex="-1" aria-hidden="true">
    <?= imagen($n['imagen'] ?? '', $n['titulo'] ?? '', 'news-card__img', 'Imagen de la noticia pendiente') ?>
    <span class="news-card__cat"><?= e($n['categoria'] ?? 'Noticia') ?></span>
  </a>
  <div class="news-card__body">
    <time class="news-card__date" datetime="<?= e($n['fecha'] ?? '') ?>"><?= icon('calendar') ?> <?= fecha_es($n['fecha'] ?? '') ?></time>
    <h3 class="news-card__title"><a href="<?= e($href) ?>"><?= e($n['titulo'] ?? '') ?></a></h3>
    <p class="news-card__excerpt"><?= e(resumen($n['resumen'] ?? '', 150)) ?></p>
    <a class="link-arrow" href="<?= e($href) ?>">Leer más <?= icon('arrow') ?></a>
  </div>
</article>
<?php
    return (string) ob_get_clean();
}

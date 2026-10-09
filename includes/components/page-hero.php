<?php
/**
 * Encabezado animado para páginas internas.
 * page_hero('Título', 'Texto introductorio', 'Antetítulo', 'icono', 'img/sede/foto.jpg', 'mint|soft|');
 */
function page_hero(string $titulo, string $lead = '', string $eyebrow = '', string $icono = 'molecule', ?string $fondo = null, string $variante = ''): string
{
    ob_start(); ?>
<section class="page-hero<?= $variante ? ' page-hero--' . e($variante) : '' ?>">
  <?php if ($fondo && asset_existe($fondo)): ?>
    <div class="page-hero__bg"><img src="<?= e(asset($fondo)) ?>" alt=""></div>
  <?php endif; ?>
  <span class="page-hero__deco" aria-hidden="true"><?= icon($icono) ?></span>
  <div class="container">
    <nav class="breadcrumb reveal" aria-label="Ruta de navegación">
      <a href="<?= e(url()) ?>">Inicio</a><?= icon('chev-r') ?><span aria-current="page"><?= e($titulo) ?></span>
    </nav>
    <?php if ($eyebrow): ?><span class="eyebrow reveal" style="margin-top:1.25rem"><?= e($eyebrow) ?></span><?php endif; ?>
    <h1 class="page-hero__title" data-split><?= e($titulo) ?></h1>
    <?php if ($lead): ?><p class="page-hero__lead reveal" style="--d:250ms"><?= $lead ?></p><?php endif; ?>
  </div>
</section>
<?php
    return (string) ob_get_clean();
}

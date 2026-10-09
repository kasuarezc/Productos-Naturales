<?php
/**
 * HEADER ESTÁNDAR
 * Antes de incluirlo, cada página define:
 *   $PAGE = ['key' => 'inicio', 'titulo' => '...', 'descripcion' => '...', 'js' => ['carousel.js']];
 */
require_once __DIR__ . '/config.php';
require_once INC_PATH . '/components/logo.php';
require_once INC_PATH . '/components/person-card.php';
require_once INC_PATH . '/components/news-card.php';
require_once INC_PATH . '/components/page-hero.php';

$PAGE = $PAGE ?? [];
$paginaActual = $PAGE['key'] ?? '';
$tituloPagina = !empty($PAGE['titulo']) ? $PAGE['titulo'] . ' | ' . $SITE['nombre_corto'] : $SITE['nombre'];
$descripcion = $PAGE['descripcion'] ?? $SITE['descripcion'];
$esquema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$urlActual = $esquema . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($_SERVER['REQUEST_URI'] ?? '/');

/** Botones de redes sociales (se ocultan los que no tengan URL). */
function redes_html(string $clase = ''): string
{
    global $REDES;
    $nombres = ['facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'x' => 'X (Twitter)', 'linkedin' => 'LinkedIn', 'whatsapp' => 'WhatsApp'];
    $html = '';
    foreach ($REDES as $red => $link) {
        $link = enlace($link);
        $attrs = $link
            ? 'href="' . e($link) . '" target="_blank" rel="noopener"'
            : 'href="#" data-pendiente="Enlace de ' . e($nombres[$red]) . ' pendiente"';
        $html .= '<a class="social-btn social-btn--' . e($red) . '" ' . $attrs . ' aria-label="' . e($nombres[$red]) . '">' . icon($red) . '</a>';
    }
    return '<div class="social ' . e($clase) . '">' . $html . '</div>';
}

/** Logo de la Universidad de la Amazonia (SVG si existe, si no, rótulo de texto). */
function logo_universidad(bool $claro = false): string
{
    global $SITE;
    foreach (['img/brand/uniamazonia-2.svg', 'img/brand/uniamazonia.png'] as $r) {
        if (asset_existe($r)) {
            return '<img class="uni-logo" src="' . e(asset($r)) . '" alt="' . e($SITE['universidad']) . '">';
        }
    }
    return '<span class="uni-logo uni-logo--text' . ($claro ? ' is-light' : '') . '" title="Reemplazar por assets/img/brand/uniamazonia.svg"><small>Universidad de la</small><strong>Amazonia</strong></span>';
}
?><!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($tituloPagina) ?></title>
  <meta name="description" content="<?= e($descripcion) ?>">
  <meta name="theme-color" content="#048C8C">
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= e($tituloPagina) ?>">
  <meta property="og:description" content="<?= e($descripcion) ?>">
  <meta property="og:image" content="<?= e($esquema . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . asset('img/brand/logo-completo.png')) ?>">
  <meta property="og:url" content="<?= e($urlActual) ?>">
  <link rel="icon" type="image/png" href="<?= e(asset('img/brand/favicon.png')) ?>">
  <link rel="preload" href="<?= e(BASE_URL . 'assets/fonts/oswald-latin-700-normal.woff2') ?>" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="<?= e(asset('css/fonts.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/variables.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/base.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/layout.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/components.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/animations.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/pages.css')) ?>">
  <script>document.documentElement.classList.add('js');</script>
</head>
<body class="page-<?= e($paginaActual) ?>">
  <a class="skip-link" href="#contenido">Saltar al contenido</a>

  <div class="loader" aria-hidden="true">
    <div class="loader__mol">
      <?= icon('hexagon', 'loader__hex') ?>
      <span></span><span></span><span></span>
    </div>
  </div>
  <div class="scroll-progress" aria-hidden="true"></div>

  <header class="site-header" id="top">
    <div class="topbar">
      <div class="container topbar__inner">
        <div class="topbar__info">
          <span class="topbar__chip"><?= icon('calendar') ?> <?= e($SITE['fecha_texto']) ?></span>
          <span class="topbar__chip hide-sm"><?= icon('pin') ?> <?= e($SITE['ciudad']) ?>, Colombia</span>
          <a class="topbar__chip hide-md" href="mailto:<?= e($SITE['correo']) ?>"><?= icon('mail') ?> <?= e($SITE['correo']) ?></a>
        </div>
        <?= redes_html('social--top') ?>
      </div>
    </div>

    <div class="navbar">
      <div class="container navbar__inner">
        <a class="brand" href="<?= e(url()) ?>" aria-label="Ir al inicio">
          <?= logo_svg('header') ?>
        </a>
        <span class="brand__sep" aria-hidden="true"></span>
        <a class="brand__uni" href="<?= e($LINKS['universidad']) ?>" target="_blank" rel="noopener" aria-label="<?= e($SITE['universidad']) ?>">
          <?= logo_universidad() ?>
        </a>

        <nav class="nav" id="nav-principal" aria-label="Menú principal">
          <div class="nav__head">
            <span class="nav__title">Menú</span>
            <button class="nav__close" type="button" aria-label="Cerrar menú" data-nav-close><?= icon('close') ?></button>
          </div>
          <ul class="nav__list">
            <?php foreach ($MENU as $i => $item):
              $activo = $paginaActual === $item['key']; ?>
              <li class="nav__item" style="--i:<?= $i ?>">
                <a class="nav__link<?= $activo ? ' is-active' : '' ?>" href="<?= e(url($item['url'])) ?>"<?= $activo ? ' aria-current="page"' : '' ?> title="<?= e($item['label']) ?>">
                  <?= icon($item['icon']) ?>
                  <span class="nav__label nav__label--full"><?= e($item['label']) ?></span>
                  <span class="nav__label nav__label--short"><?= e($item['label_corto'] ?? $item['label']) ?></span>
                </a>
              </li>
            <?php endforeach; ?>
            <li class="nav__indicator" aria-hidden="true"></li>
          </ul>
          <div class="nav__footer">
            <a class="btn btn--accent btn--block" href="<?= e(url('resumenes.php#inscripcion')) ?>"><?= icon('ticket') ?><span>Inscríbete</span></a>
            <?= redes_html('social--drawer') ?>
          </div>
        </nav>

        <a class="btn btn--accent btn--sm navbar__cta" href="<?= e(url('resumenes.php#inscripcion')) ?>"><?= icon('ticket') ?><span>Inscríbete</span></a>

        <button class="burger" type="button" aria-label="Abrir menú" aria-controls="nav-principal" aria-expanded="false" data-nav-toggle>
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
  </header>
  <div class="nav-backdrop" data-nav-close></div>

  <main id="contenido">

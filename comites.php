<?php
/**
 * CONFERENCISTAS Y COMITÉS
 * Secciones (en orden de importancia):
 *   1. Conferencistas internacionales  → tarjetas destacadas grandes   (grupo "internacional")
 *   2. Conferencistas nacionales       → tarjetas dinámicas            (grupo "nacional")
 *   3. Conferencistas SEQUIAMAZ        → tarjetas dinámicas            (grupo "sequiamaz")
 *   4. Programa de conferencias plenarias
 *   5. Comités (organizador, editorial, científico) → tarjetas compactas
 * Todo se genera desde data/personas.json.
 */
$PAGE = [
    'key'         => 'comites',
    'titulo'      => 'Conferencistas y Comités',
    'descripcion' => 'Conferencistas plenarios internacionales y nacionales, ponentes SEQUIAMAZ y comités organizador, editorial y científico del II Congreso Colombiano de Productos Naturales.',
    'js'          => ['subnav.js'],
];
require __DIR__ . '/includes/header.php';

$grupos = grupos_personas();
$internacionales = personas(['internacional']);
$nacionales = personas(['nacional']);
$sequiamaz = personas(['sequiamaz']);
$conferencistas = array_merge($internacionales, $nacionales, $sequiamaz);
$paises = array_unique(array_filter(array_column($conferencistas, 'nacionalidad')));
$instituciones = array_unique(array_filter(array_column($conferencistas, 'institucion')));
$comites = [
    'organizador' => ['icon' => 'target',  'texto' => 'Planea, coordina y hace posible cada detalle del encuentro.'],
    'editorial'   => ['icon' => 'book',    'texto' => 'Vela por la calidad de las memorias y publicaciones del evento.'],
    'cientifico'  => ['icon' => 'microscope', 'texto' => 'Evalúa los resúmenes y define la línea académica del congreso.'],
];
?>

<?= page_hero('Conferencistas y Comités', 'Investigadores de referencia en productos naturales, química aplicada y alimentos funcionales compartirán sus avances en Florencia. Conoce también a los comités que hacen posible el congreso.', 'Voces de la ciencia', 'mic', 'img/sede/entrada-uniamazonia.jpg') ?>

<!-- Cifras -->
<div class="container">
  <div class="stats reveal">
    <div class="stat"><strong data-count="<?= count($conferencistas) ?>"><?= count($conferencistas) ?></strong><span>Conferencistas invitados</span></div>
    <div class="stat"><strong data-count="<?= count($internacionales) ?>"><?= count($internacionales) ?></strong><span>Plenarias internacionales</span></div>
    <div class="stat"><strong data-count="<?= count($paises) ?>"><?= count($paises) ?></strong><span>Países representados</span></div>
    <div class="stat"><strong data-count="<?= count($instituciones) ?>"><?= count($instituciones) ?></strong><span>Instituciones</span></div>
  </div>
</div>

<!-- Navegación de secciones -->
<nav class="subnav" aria-label="Secciones de la página">
  <div class="container subnav__inner">
    <a href="#internacionales" class="subnav__link"><?= icon('globe') ?><span>Internacionales</span></a>
    <a href="#nacionales" class="subnav__link"><?= icon('pin') ?><span>Nacionales</span></a>
    <a href="#sequiamaz" class="subnav__link"><?= icon('flask') ?><span>SEQUIAMAZ</span></a>
    <a href="#plenarias" class="subnav__link"><?= icon('mic') ?><span>Programa plenario</span></a>
    <a href="#comites" class="subnav__link"><?= icon('users') ?><span>Comités</span></a>
  </div>
</nav>

<!-- 1. INTERNACIONALES (máxima jerarquía) -->
<section class="section section--speakers" id="internacionales" aria-labelledby="int-t">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Conferencias plenarias</span>
      <h2 class="section-title" id="int-t">Conferencistas <em>internacionales</em></h2>
      <p class="section-lead">Investigadores de Chile, Argentina y México que abren el diálogo entre la biodiversidad latinoamericana y la ciencia aplicada.</p>
    </div>
    <div class="speakers-featured">
      <?php foreach ($internacionales as $i => $p) echo speaker_feature($p, $i); ?>
    </div>
  </div>
</section>

<!-- 2. NACIONALES -->
<section class="section section--soft" id="nacionales" aria-labelledby="nac-t">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Conferencias plenarias</span>
      <h2 class="section-title" id="nac-t">Conferencistas <em>nacionales</em></h2>
      <p class="section-lead">Referentes colombianos en alimentos funcionales, fitoquímica, bioprospección e industria fitofarmacéutica.</p>
    </div>
    <div class="people-grid people-grid--3">
      <?php foreach ($nacionales as $i => $p) echo person_card($p, $i); ?>
    </div>
  </div>
</section>

<!-- 3. SEQUIAMAZ -->
<section class="section" id="sequiamaz" aria-labelledby="seq-t">
  <div class="container">
    <div class="sequiamaz-head reveal">
      <div>
        <span class="eyebrow">X Seminario Internacional de Química Aplicada para la Amazonía</span>
        <h2 class="section-title" id="seq-t">Conferencistas <em>SEQUIAMAZ</em></h2>
        <p class="section-lead">Química de superficies, materiales y química computacional aplicada a los desafíos del contexto amazónico.</p>
      </div>
      <span class="sequiamaz-head__badge" aria-hidden="true"><?= icon('atom') ?><b>X</b></span>
    </div>
    <div class="people-grid people-grid--3">
      <?php foreach ($sequiamaz as $i => $p) echo person_card($p, $i); ?>
    </div>
  </div>
</section>

<!-- 4. PROGRAMA PLENARIO -->
<section class="section section--mint" id="plenarias" aria-labelledby="plenarias-t">
  <div class="container container--narrow">
    <div class="section-head reveal">
      <span class="eyebrow">Programa académico</span>
      <h2 class="section-title" id="plenarias-t">Conferencias <em>plenarias</em></h2>
      <p class="section-lead">Haz clic en cada conferencia para ver la biografía del ponente.</p>
    </div>
    <?php foreach (['internacional', 'nacional', 'sequiamaz'] as $g): ?>
      <h3 class="bio__h reveal"><?= e($grupos[$g]['label']) ?></h3>
      <div class="talks">
        <?php foreach (personas([$g]) as $k => $p): if (empty($p['ponencia'])) continue; ?>
          <button class="talk reveal" style="--d:<?= $k * 70 ?>ms" type="button" data-bio="bio-<?= e($p['id']) ?>" aria-haspopup="dialog">
            <?= persona_foto($p) ?>
            <span>
              <span class="talk__title"><?= e($p['ponencia']) ?></span>
              <span class="talk__who"><?= e(trim($p['titulo'] . ' ' . $p['nombre'])) ?><?= $p['institucion'] ? ' · ' . e($p['institucion']) : '' ?></span>
            </span>
            <span class="talk__tag talk__tag--<?= e($g) ?>"><?= e($grupos[$g]['corto']) ?></span>
          </button>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- 5. COMITÉS -->
<section class="section" id="comites" aria-labelledby="comites-t">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Universidad de la Amazonia</span>
      <h2 class="section-title" id="comites-t">Comités del <em>congreso</em></h2>
      <p class="section-lead">El equipo académico que organiza, evalúa y edita los contenidos del encuentro.</p>
    </div>
    <div class="committees">
      <?php foreach ($comites as $key => $c): $miembros = personas([$key]); ?>
        <div class="committee reveal">
          <header class="committee__head">
            <span class="committee__icon"><?= icon($c['icon']) ?></span>
            <div><h3><?= e($grupos[$key]['label']) ?></h3><p><?= e($c['texto']) ?></p></div>
          </header>
          <div class="committee__list">
            <?php foreach ($miembros as $i => $p) echo committee_card($p, $i); ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

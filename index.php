<?php
$PAGE = [
    'key'    => 'inicio',
    'titulo' => '',
    'js'     => ['hero.js', 'carousel.js'],
];
require __DIR__ . '/includes/header.php';

$heroImg = asset_existe('img/hero/brand-hero.png') ? 'img/hero/brand-hero.png' : 'img/hero/brand-hero.jpg';
$eventos = [
    ['num' => 'II',  'titulo' => 'Congreso Colombiano de Productos Naturales', 'sub' => 'Investigación, caracterización y aplicaciones', 'icon' => 'leaf',
     'texto' => 'Un espacio para presentar y discutir avances en investigación, caracterización, aprovechamiento y aplicaciones de los productos naturales.'],
    ['num' => 'X',   'titulo' => 'Seminario Internacional de Química Aplicada para la Amazonía – SEQUIAMAZ', 'sub' => 'Química para el contexto amazónico', 'icon' => 'flask',
     'texto' => 'Un escenario para el intercambio de conocimientos y avances en química aplicada a los desafíos y oportunidades del contexto amazónico.'],
    ['num' => 'III', 'titulo' => 'Simposio Internacional de Alimentos Funcionales para la Amazonía', 'sub' => 'Nutrición y soberanía alimentaria', 'icon' => 'food',
     'texto' => 'Dedicado al potencial nutricional y funcional de los alimentos amazónicos y su aporte a la alimentación saludable, la seguridad y la soberanía alimentaria.'],
    ['num' => 'V',   'titulo' => 'Escuela Andino-Amazónica de Química – EAAQ', 'sub' => 'Formación de nuevas generaciones', 'icon' => 'atom',
     'texto' => 'Un espacio de formación e interacción científica dirigido al fortalecimiento de capacidades y a la formación de nuevas generaciones de investigadores.'],
];
$objetivos = [
    ['icon' => 'microscope', 't' => 'Compartir resultados de investigación', 'p' => 'Divulgar avances sobre descubrimiento, aislamiento, caracterización y evaluación de compuestos bioactivos amazónicos.'],
    ['icon' => 'cap',        't' => 'Fortalecer capacidades', 'p' => 'Formar nuevas generaciones de científicos a través de cursos, conferencias y diálogo con investigadores de alto nivel.'],
    ['icon' => 'network',    't' => 'Establecer redes de colaboración', 'p' => 'Conectar grupos de investigación nacionales e internacionales alrededor de la biodiversidad del sur de Colombia.'],
    ['icon' => 'handshake',  't' => 'Promover el diálogo', 'p' => 'Acercar a la academia, el sector productivo, las instituciones y las comunidades para generar valor desde el conocimiento.'],
    ['icon' => 'food',       't' => 'Seguridad y soberanía alimentaria', 'p' => 'Identificar propiedades nutricionales y funcionales de los recursos alimentarios propios de la Amazonía.'],
    ['icon' => 'sprout',     't' => 'Desarrollo sostenible', 'p' => 'Poner la investigación al servicio de la conservación, los saberes tradicionales y el bienestar de las comunidades.'],
];
$patro = data('patrocinadores');
$fechas = data('fechas');
?>

<!-- =================== HERO =================== -->
<section class="hero" aria-labelledby="hero-title">
  <div class="hero__bg"><img src="<?= e(asset($heroImg)) ?>" alt="" fetchpriority="high" data-parallax=".25"></div>
  <div class="hero__overlay"></div>
  <canvas class="hero__canvas" aria-hidden="true"></canvas>
  <span class="hero__deco hero__deco--1" aria-hidden="true"><?= icon('hexagon') ?></span>
  <span class="hero__deco hero__deco--2" aria-hidden="true"><?= icon('molecule') ?></span>
  <span class="hero__deco hero__deco--3" aria-hidden="true"><?= icon('atom') ?></span>

  <div class="container hero__grid">
    <div class="hero__content">
      <p class="hero__kicker reveal">
        <b>2027</b>
        <span><?= icon('calendar') ?> <?= e($SITE['fecha_texto']) ?></span>
        <span><?= icon('pin') ?> <?= e($SITE['ciudad']) ?></span>
      </p>
      <h1 class="hero__title" id="hero-title" data-split><span class="hero__title-sm">II Congreso Colombiano de</span> <span class="grad">Productos Naturales</span></h1>
      <p class="hero__lema reveal" style="--d:300ms"><strong>Biodiversidad que inspira, ciencia que transforma la Amazonia.</strong> Cuatro eventos, una visión compartida desde Florencia, Caquetá.</p>
      <div class="btn-group reveal" style="--d:450ms">
        <a class="btn btn--accent btn--lg" href="<?= e(url('resumenes.php#inscripcion')) ?>"><?= icon('ticket') ?><span>Inscripciones</span></a>
        <a class="btn btn--glass btn--lg" href="<?= e(url('noticias.php')) ?>"><?= icon('news') ?><span>Ver noticias</span></a>
      </div>
      <div class="countdown reveal" style="--d:600ms" data-countdown="<?= e($SITE['fecha_inicio']) ?>" aria-label="Cuenta regresiva para el inicio del evento">
        <div class="countdown__item"><span class="countdown__num" data-d>00</span><span class="countdown__lbl">Días</span></div>
        <div class="countdown__item"><span class="countdown__num" data-h>00</span><span class="countdown__lbl">Horas</span></div>
        <div class="countdown__item"><span class="countdown__num" data-m>00</span><span class="countdown__lbl">Min</span></div>
        <div class="countdown__item"><span class="countdown__num" data-s>00</span><span class="countdown__lbl">Seg</span></div>
      </div>
    </div>

    <aside class="hero-panel reveal reveal--right" style="--d:350ms" aria-label="Eventos que integran el encuentro">
      <p class="hero-panel__title"><?= icon('molecule') ?> Cuatro eventos, una visión</p>
      <ul class="hero-events">
        <?php foreach ($eventos as $ev): ?>
          <li class="hero-event">
            <span class="hero-event__num"><?= e($ev['num']) ?></span>
            <span><strong><?= e($ev['titulo']) ?></strong><small><?= e($ev['sub']) ?></small></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </aside>
  </div>
  <a class="scroll-hint" href="#bienvenidos" aria-label="Desplazarse a Bienvenidos"></a>
</section>

<!-- =================== BIENVENIDOS =================== -->
<section class="section" id="bienvenidos" aria-labelledby="bienvenidos-t">
  <div class="container">
    <div class="welcome">
      <div class="welcome__media reveal reveal--left">
        <div class="welcome__img"><?= imagen('img/sede/paujil.jpg', 'Bosque amazónico en El Paujil, Caquetá', '', 'Foto bienvenida pendiente') ?></div>
        <div class="welcome__img welcome__img--sm"><?= imagen('img/sede/entrada-uniamazonia.jpg', 'Sede principal, Universidad de la Amazonia') ?></div>
        <div class="welcome__badge"><?= icon('flask') ?><div><strong data-count="4">4</strong><span>eventos científicos</span></div></div>
      </div>
      <div class="welcome__text reveal reveal--right">
        <span class="eyebrow">Bienvenidos</span>
        <h2 class="section-title" id="bienvenidos-t">La ciencia se encuentra con la <em>Amazonía</em></h2>
        <p>Florencia, Caquetá, será el escenario de un gran encuentro científico que integra cuatro espacios académicos en torno a un propósito común: <strong>reconocer, investigar y potenciar la riqueza de los productos naturales y alimentarios de la Amazonía.</strong></p>
        <p>El II Congreso Colombiano de Productos Naturales, el X SEQUIAMAZ, la V Escuela Andino-Amazónica de Química y el III Simposio Internacional de Alimentos Funcionales para la Amazonía convergen en un espacio donde la ciencia dialoga con la biodiversidad, la cultura y los saberes propios de los territorios amazónicos.</p>
        <blockquote class="welcome__quote"><?= icon('quote') ?>Una Amazonía que inspira preguntas. Una ciencia que busca respuestas. Un territorio que ofrece oportunidades.</blockquote>
        <a class="btn btn--outline" href="<?= e(url('sede.php')) ?>"><?= icon('pin') ?><span>Conoce la sede</span></a>
      </div>
    </div>

    <div class="events-grid">
      <?php foreach ($eventos as $i => $ev): ?>
        <article class="event-card reveal" style="--d:<?= $i * 100 ?>ms">
          <span class="event-card__num" aria-hidden="true"><?= e($ev['num']) ?></span>
          <div class="card__icon"><?= icon($ev['icon']) ?></div>
          <h3><?= e($ev['titulo']) ?></h3>
          <p><?= e($ev['texto']) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- =================== OBJETIVOS =================== -->
<section class="section section--dark" aria-labelledby="objetivos-t">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Objetivos</span>
      <h2 class="section-title" id="objetivos-t" data-split>Biodiversidad que inspira, ciencia que transforma</h2>
      <p class="section-lead">Más allá de un encuentro académico, buscamos que la investigación y la innovación estén al servicio de la Amazonía y de su gente.</p>
    </div>
    <div class="objectives">
      <?php foreach ($objetivos as $i => $o): ?>
        <article class="objective reveal" style="--d:<?= ($i % 3) * 100 ?>ms">
          <div class="card__icon"><?= icon($o['icon']) ?></div>
          <h3><?= e($o['t']) ?></h3>
          <p><?= e($o['p']) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- =================== ÚLTIMAS NOTICIAS =================== -->
<section class="section section--soft" aria-labelledby="noticias-t">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Actualidad</span>
      <h2 class="section-title" id="noticias-t">Últimas <em>noticias</em></h2>
      <p class="section-lead">Novedades, anuncios y avances del congreso.</p>
    </div>
    <div class="carousel reveal" data-carousel data-autoplay="6000" role="region" aria-roledescription="carrusel" aria-label="Últimas noticias">
      <div class="carousel__viewport">
        <div class="carousel__track">
          <?php foreach (noticias(8) as $n): ?>
            <div class="carousel__slide" role="group" aria-roledescription="diapositiva"><?= news_card($n) ?></div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="carousel__controls">
        <button class="carousel__btn" type="button" data-prev aria-label="Noticia anterior"><?= icon('chev-l') ?></button>
        <div class="carousel__dots"></div>
        <button class="carousel__btn" type="button" data-next aria-label="Noticia siguiente"><?= icon('chev-r') ?></button>
      </div>
    </div>
    <p class="text-center" style="margin-top:2rem"><a class="btn btn--ghost" href="<?= e(url('noticias.php')) ?>"><span>Ver todas las noticias</span><?= icon('arrow') ?></a></p>
  </div>
</section>

<!-- =================== RESÚMENES =================== -->
<section class="section" id="resumenes" aria-labelledby="resumenes-t">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Participa con tu investigación</span>
      <h2 class="section-title" id="resumenes-t">Envío de <em>resúmenes</em></h2>
      <p class="section-lead">Descarga la plantilla oficial, prepara tu resumen y envíalo dentro de las fechas establecidas.</p>
    </div>
    <div class="abstracts">
      <article class="abstract-card abstract-card--template reveal reveal--left">
        <span class="abstract-card__bigicon" aria-hidden="true"><?= icon('book') ?></span>
        <div class="abstract-card__meta"><span><?= icon('download') ?> Formato Word (.docx)</span></div>
        <h3>Plantilla de resúmenes</h3>
        <p>Usa la plantilla para presentar título, autores, filiación, resumen y palabras clave con el formato requerido por el comité científico.</p>
        <a class="btn btn--light" href="<?= e($LINKS['plantilla_resumen']) ?>" download><?= icon('download') ?><span>Descargar plantilla</span></a>
      </article>
      <article class="abstract-card abstract-card--send reveal reveal--right">
        <span class="abstract-card__bigicon" aria-hidden="true"><?= icon('send') ?></span>
        <div class="abstract-card__meta">
          <?php foreach (array_slice($fechas, 0, 2) as $f): ?>
            <span><?= icon('calendar') ?> <?= e($f['actividad']) ?>: <?= e($f['fecha_texto'] ?: 'por confirmar') ?></span>
          <?php endforeach; ?>
        </div>
        <h3>Envío de resúmenes</h3>
        <p>Postula tu trabajo en productos naturales, química aplicada o alimentos funcionales para presentarlo en modalidad oral o póster.</p>
        <?php if ($l = enlace($LINKS['envio_resumenes'])): ?>
          <a class="btn btn--light" href="<?= e($l) ?>" target="_blank" rel="noopener"><?= icon('upload') ?><span>Enviar resumen</span></a>
        <?php else: ?>
          <a class="btn btn--light" href="#" data-pendiente="El enlace de envío de resúmenes se habilitará próximamente."><?= icon('upload') ?><span>Enviar resumen</span></a>
        <?php endif; ?>
      </article>
    </div>
  </div>
</section>

<!-- =================== PATROCINADORES =================== -->
<section class="section section--mint" aria-labelledby="patro-t">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Gracias por creer en la ciencia</span>
      <h2 class="section-title" id="patro-t">Patrocinadores</h2>
    </div>
    <div class="sponsors-group">
      <p class="sponsors-label">Patrocinadores oficiales</p>
      <div class="sponsors">
        <?php foreach ($patro['oficiales'] ?? [] as $i => $p): ?>
          <a class="sponsor reveal reveal--zoom" style="--d:<?= $i * 90 ?>ms" href="<?= e($p['url'] ?: '#') ?>" target="_blank" rel="noopener" title="<?= e($p['nombre']) ?>">
            <?= imagen($p['logo'], $p['nombre'], '', 'Logo pendiente') ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php if (!empty($patro['apoyan'])): ?>
    <div class="sponsors-group">
      <p class="sponsors-label">Apoyan</p>
      <div class="sponsors sponsors--sm">
        <?php foreach ($patro['apoyan'] as $p): ?>
          <a class="sponsor reveal reveal--zoom" href="<?= e($p['url'] ?: '#') ?>" target="_blank" rel="noopener" title="<?= e($p['nombre']) ?>">
            <?= imagen($p['logo'], $p['nombre']) ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="sponsors-group">
      <p class="sponsors-label">Organizan</p>
      <div class="sponsors sponsors--sm">
        <?php foreach ($patro['organizan'] as $p): ?>
          <a class="sponsor reveal reveal--zoom" href="<?= e($p['url'] ?: '#') ?>" target="_blank" rel="noopener" title="<?= e($p['nombre']) ?>">
            <?= imagen($p['logo'], $p['nombre']) ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
    <div class="marquee" aria-hidden="true">
      <div class="marquee__track">
        <?php $todos = array_merge($patro['oficiales'] ?? [], $patro['apoyan'] ?? [], $patro['organizan'] ?? [], data('aliados'));
        for ($k = 0; $k < 2; $k++) foreach ($todos as $p) if (asset_existe($p['logo'])): ?>
          <img src="<?= e(asset($p['logo'])) ?>" alt="" loading="lazy">
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

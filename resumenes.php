<?php
/**
 * RESÚMENES Y FECHAS
 * - Próximo hito con cuenta regresiva
 * - Línea de tiempo de fechas clave (data/fechas.json) con estado automático
 * - Ejes temáticos, cómo participar, plantilla y envío
 * - Costos de inscripción (data/inscripciones.json)  → ancla #inscripcion
 */
$PAGE = [
    'key'         => 'resumenes',
    'titulo'      => 'Resúmenes y Fechas',
    'descripcion' => 'Fechas clave, envío de resúmenes y ponencias, ejes temáticos e inscripción al II Congreso Colombiano de Productos Naturales, SEQUIAMAZ y Simposio de Alimentos Funcionales. Florencia, mayo de 2027.',
    'js'          => ['hero.js'],
];
require __DIR__ . '/includes/header.php';

$fechas = data('fechas');
$tarifas = data('inscripciones');
$hoy = date('Y-m-d');

/** Estado de cada hito según la fecha de hoy. */
function estado_hito(array $f, string $hoy, int $i, array $todas): array
{
    $ini = $f['fecha'] ?? '';
    $fin = ($f['fecha_fin'] ?? '') ?: $ini;
    if ($ini === '') return ['pendiente', 'Por confirmar'];
    if ($hoy > $fin) return ['pasado', 'Finalizado'];
    if ($hoy >= $ini) return ['activo', $i === 0 ? 'Convocatoria abierta' : 'En curso'];
    return ['proximo', 'Próximamente'];
}

// El próximo hito (primero cuya fecha aún no llega)
$proximo = null;
foreach ($fechas as $f) {
    if (($f['fecha'] ?? '') > $hoy) { $proximo = $f; break; }
}
// ¿Está abierta la convocatoria?
$abre = $fechas[0]['fecha'] ?? '';
$cierra = $fechas[1]['fecha'] ?? '';
$convocatoriaAbierta = $abre && $cierra && $hoy >= $abre && $hoy <= $cierra;

/** Enlace "Agregar a Google Calendar". */
function gcal(array $f): string
{
    global $SITE;
    $ini = str_replace('-', '', $f['fecha']);
    $fin = date('Ymd', strtotime((($f['fecha_fin'] ?? '') ?: $f['fecha']) . ' +1 day'));
    return 'https://calendar.google.com/calendar/render?action=TEMPLATE&text=' . rawurlencode($f['actividad'] . ' – ' . $SITE['nombre_corto'])
        . '&dates=' . $ini . '/' . $fin . '&details=' . rawurlencode($f['descripcion'] ?? '') . '&location=' . rawurlencode($SITE['universidad'] . ', ' . $SITE['ciudad']);
}

$ejes = [
    ['icon' => 'leaf',  'num' => 'II',  't' => 'Productos naturales', 'evento' => 'II Congreso Colombiano de Productos Naturales',
     'temas' => ['Descubrimiento, aislamiento y caracterización de compuestos bioactivos', 'Fitoquímica, metabolómica y bioprospección', 'Aplicaciones en salud, farmacia, cosmética y nutracéutica', 'Bioinsumos y valorización de residuos agroindustriales']],
    ['icon' => 'flask', 'num' => 'X',   't' => 'Química aplicada', 'evento' => 'X SEQUIAMAZ',
     'temas' => ['Química aplicada a los desafíos del contexto amazónico', 'Química de superficies, materiales y catálisis', 'Química computacional y análisis instrumental', 'Transformación y agregación de valor a recursos regionales']],
    ['icon' => 'food',  'num' => 'III', 't' => 'Alimentos funcionales', 'evento' => 'III Simposio Internacional de Alimentos Funcionales',
     'temas' => ['Potencial nutricional y funcional de alimentos amazónicos', 'Encapsulación, procesamiento y desarrollo de productos', 'Compuestos bioactivos y salud metabólica', 'Seguridad y soberanía alimentaria']],
];
?>

<?= page_hero('Resúmenes y Fechas', 'Comparte tus resultados de investigación en Florencia. Aquí encuentras el calendario oficial, los ejes temáticos y todo lo necesario para enviar tu resumen.', 'Convocatoria 2026 – 2027', 'calendar', 'img/sede/cascada-carano-2.jpg', 'soft') ?>

<!-- Próximo hito -->
<div class="container">
  <div class="next-milestone reveal<?= $convocatoriaAbierta ? ' is-open' : '' ?>">
    <div class="next-milestone__info">
      <span class="next-milestone__pulse" aria-hidden="true"></span>
      <?php if ($convocatoriaAbierta): ?>
        <small>Convocatoria abierta · cierra el <?= e($fechas[1]['fecha_texto']) ?></small>
        <strong>¡Envía tu resumen!</strong>
      <?php elseif ($proximo): ?>
        <small>Próxima fecha · <?= e($proximo['fecha_texto']) ?></small>
        <strong><?= e($proximo['actividad']) ?></strong>
      <?php else: ?>
        <small>Gracias por participar</small><strong>El evento ha finalizado</strong>
      <?php endif; ?>
    </div>
    <?php $objetivo = $convocatoriaAbierta ? $cierra . 'T23:59:59-05:00' : ($proximo ? $proximo['fecha'] . 'T00:00:00-05:00' : ''); ?>
    <?php if ($objetivo): ?>
      <div class="countdown countdown--light" data-countdown="<?= e($objetivo) ?>" aria-label="Cuenta regresiva">
        <div class="countdown__item"><span class="countdown__num" data-d>00</span><span class="countdown__lbl">Días</span></div>
        <div class="countdown__item"><span class="countdown__num" data-h>00</span><span class="countdown__lbl">Horas</span></div>
        <div class="countdown__item"><span class="countdown__num" data-m>00</span><span class="countdown__lbl">Min</span></div>
        <div class="countdown__item"><span class="countdown__num" data-s>00</span><span class="countdown__lbl">Seg</span></div>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- FECHAS CLAVE -->
<section class="section" id="fechas" aria-labelledby="fechas-t">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Calendario oficial</span>
      <h2 class="section-title" id="fechas-t">Fechas <em>clave</em></h2>
      <p class="section-lead">Marca estas fechas en tu calendario: cada hito te acerca a mayo de 2027 en la Amazonía.</p>
    </div>

    <ol class="timeline">
      <?php foreach ($fechas as $i => $f):
        [$estado, $etiqueta] = estado_hito($f, $hoy, $i, $fechas);
        $ts = strtotime($f['fecha']);
        $meses = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC']; ?>
        <li class="timeline__item timeline__item--<?= e($estado) ?> reveal" style="--d:<?= $i * 140 ?>ms">
          <div class="timeline__date">
            <span class="timeline__day"><?= date('j', $ts) ?><?= !empty($f['fecha_fin']) ? '–' . date('j', strtotime($f['fecha_fin'])) : '' ?></span>
            <span class="timeline__month"><?= $meses[(int) date('n', $ts) - 1] ?> <?= date('Y', $ts) ?></span>
          </div>
          <span class="timeline__node" aria-hidden="true"><?= icon($f['icono'] ?? 'calendar') ?></span>
          <article class="timeline__card">
            <span class="timeline__status"><?= e($etiqueta) ?></span>
            <h3><?= e($f['actividad']) ?></h3>
            <p><?= e($f['descripcion'] ?? '') ?></p>
            <a class="link-arrow" href="<?= e(gcal($f)) ?>" target="_blank" rel="noopener"><?= icon('calendar') ?> Agregar a mi calendario</a>
          </article>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<!-- EJES TEMÁTICOS -->
<section class="section section--dark" id="ejes" aria-labelledby="ejes-t">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">¿Sobre qué puedo enviar?</span>
      <h2 class="section-title" id="ejes-t">Ejes temáticos</h2>
      <p class="section-lead">Los trabajos se reciben para los tres eventos académicos que convergen en Florencia. Elige el eje que mejor se ajuste a tu investigación.</p>
    </div>
    <div class="axes">
      <?php foreach ($ejes as $i => $ej): ?>
        <article class="axis reveal" style="--d:<?= $i * 120 ?>ms">
          <span class="axis__num" aria-hidden="true"><?= e($ej['num']) ?></span>
          <div class="card__icon"><?= icon($ej['icon']) ?></div>
          <small><?= e($ej['evento']) ?></small>
          <h3><?= e($ej['t']) ?></h3>
          <ul>
            <?php foreach ($ej['temas'] as $t): ?><li><?= icon('check') ?><span><?= e($t) ?></span></li><?php endforeach; ?>
          </ul>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CÓMO PARTICIPAR -->
<section class="section" id="participar" aria-labelledby="part-t">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Paso a paso</span>
      <h2 class="section-title" id="part-t">¿Cómo enviar tu <em>resumen</em>?</h2>
    </div>
    <div class="steps steps--4">
      <article class="step reveal"><h3>Descarga la plantilla</h3><p>Usa el formato oficial con título, autores, filiación, resumen y palabras clave.</p></article>
      <article class="step reveal" style="--d:100ms"><h3>Elige tu evento</h3><p>Productos naturales, química aplicada (SEQUIAMAZ) o alimentos funcionales.</p></article>
      <article class="step reveal" style="--d:200ms"><h3>Envía antes del cierre</h3><p>Entre el <?= e($fechas[0]['fecha_texto'] ?? '') ?> y el <?= e($fechas[1]['fecha_texto'] ?? '') ?>.</p></article>
      <article class="step reveal" style="--d:300ms"><h3>Evaluación y presentación</h3><p>El Comité Científico evalúa los trabajos y notifica la aceptación al correo del autor de correspondencia.</p></article>
    </div>

    <div class="abstracts" style="margin-top:3rem">
      <article class="abstract-card abstract-card--template reveal reveal--left">
        <span class="abstract-card__bigicon" aria-hidden="true"><?= icon('book') ?></span>
        <div class="abstract-card__meta"><span><?= icon('download') ?> Formato Word (.docx)</span></div>
        <h3>Plantilla de resúmenes</h3>
        <p>Descarga la plantilla y sigue sus indicaciones de formato antes de enviar tu trabajo.</p>
        <a class="btn btn--light" href="<?= e($LINKS['plantilla_resumen']) ?>" download><?= icon('download') ?><span>Descargar plantilla</span></a>
      </article>
      <article class="abstract-card abstract-card--send reveal reveal--right">
        <span class="abstract-card__bigicon" aria-hidden="true"><?= icon('send') ?></span>
        <div class="abstract-card__meta">
          <span><?= icon('upload') ?> Abre: <?= e($fechas[0]['fecha_texto'] ?? '') ?></span>
          <span><?= icon('clock') ?> Cierra: <?= e($fechas[1]['fecha_texto'] ?? '') ?></span>
        </div>
        <h3>Envío de resúmenes y ponencias</h3>
        <p>Postula tu trabajo para presentarlo ante investigadores nacionales e internacionales en la Universidad de la Amazonia.</p>
        <?php if (($l = enlace($LINKS['envio_resumenes'])) && $convocatoriaAbierta): ?>
          <a class="btn btn--light" href="<?= e($l) ?>" target="_blank" rel="noopener"><?= icon('upload') ?><span>Enviar resumen</span></a>
        <?php else: ?>
          <a class="btn btn--light" href="#" data-pendiente="<?= $hoy < $abre ? 'El envío de resúmenes abre el ' . e($fechas[0]['fecha_texto']) . '.' : 'El enlace de envío se publicará próximamente.' ?>"><?= icon('upload') ?><span>Enviar resumen</span></a>
        <?php endif; ?>
      </article>
    </div>
    <div class="notice reveal" style="margin-top:1.5rem"><?= icon('info') ?><p>¿Dudas sobre el formato o los ejes temáticos? Escríbenos a <a href="mailto:<?= e($SITE['correo']) ?>"><?= e($SITE['correo']) ?></a> o usa el <a href="<?= e(url('contacto.php')) ?>">formulario de contacto</a> seleccionando "Envío de resúmenes".</p></div>
  </div>
</section>

<!-- INSCRIPCIÓN -->
<section class="section section--soft" id="inscripcion" aria-labelledby="costos-t">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Inscripción</span>
      <h2 class="section-title" id="costos-t">Valor de la <em>inscripción</em></h2>
      <p class="section-lead">Valores unitarios en pesos colombianos (COP). UDLA: Universidad de la Amazonia.</p>
      <p class="section-lead">Recuerda que si pagas antes del 27 de marzo de 2027, podrás disfrutar de un descuento del 20% en tu inscripción.</p>
    </div>
    <div class="prices">
      <?php foreach ($tarifas as $i => $t): ?>
        <article class="price reveal<?= !empty($t['destacado']) ? ' price--featured' : '' ?>" style="--d:<?= ($i % 3) * 100 ?>ms">
          <?php if (!empty($t['destacado'])): ?><span class="price__ribbon">Posgrado</span><?php endif; ?>
          <div class="price__icon"><?= icon($t['icono'] ?? 'flask') ?></div>
          <h3 class="price__cat"><?= e($t['categoria']) ?></h3>
          <p class="price__val"><small>$</small><span><?= number_format((int) $t['valor'], 0, ',', '.') ?></span></p>
          <span class="price__cur">COP · valor unitario</span>
        </article>
      <?php endforeach; ?>
    </div>
    <div class="cta-box reveal" style="margin-top:2.5rem">
      <div>
        <h2>Inscríbete aquí</h2>
        <p>La información sobre medios de pago será publicada próximamente.</p>
      </div>
      <?php if ($li = enlace($LINKS['inscripcion'])): ?>
        <a class="btn btn--accent btn--lg" href="<?= e($li) ?>" target="_blank" rel="noopener"><?= icon('ticket') ?><span>Formulario de inscripción</span></a>
      <?php else: ?>
        <a class="btn btn--accent btn--lg" href="#" data-pendiente="El formulario de inscripción se habilitará próximamente."><?= icon('ticket') ?><span>Formulario de inscripción</span></a>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

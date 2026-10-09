<?php
$PAGE = [
    'key'         => 'escuela',
    'titulo'      => 'Escuela Andino-Amazónica de Química',
    'descripcion' => 'V Escuela Andino-Amazónica de Química – EAAQ: cursos de formación científica el 11 de mayo de 2027 en la Universidad de la Amazonia, Florencia.',
];
require __DIR__ . '/includes/header.php';
$escuela = data('escuela');
$docentes = [];
?>

<?= page_hero('Escuela Andino-Amazónica de Química', 'V EAAQ: un espacio de formación e interacción científica dirigido al fortalecimiento de capacidades y a la formación de nuevas generaciones de investigadores.', 'V Escuela – EAAQ', 'atom', 'img/sede/mirador-sacharuna-2.jpg') ?>

<section class="section" aria-labelledby="eaaq-t">
  <div class="container">
    <div class="split">
      <div class="reveal reveal--left">
        <span class="eyebrow">Formación científica</span>
        <h2 class="section-title" id="eaaq-t">Aprender ciencia <em>desde la Amazonía</em></h2>
        <p>La V Escuela Andino-Amazónica de Química se integra al II Congreso Colombiano de Productos Naturales, al X SEQUIAMAZ y al III Simposio Internacional de Alimentos Funcionales para la Amazonía, ofreciendo cursos intensivos dictados por conferencistas invitados.</p>
        <p>La articulación entre productos naturales, química aplicada, formación científica y alimentos funcionales permite abordar la biodiversidad amazónica desde una perspectiva integral.</p>
        <div class="btn-group">
          <span class="chip" style="font-size:.85rem;padding:.6rem 1rem"><?= icon('calendar') ?><span><?= fecha_es($escuela['fecha'] ?? '') ?></span></span>
          <span class="chip" style="font-size:.85rem;padding:.6rem 1rem"><?= icon('clock') ?><span><?= count($escuela['cursos'] ?? []) ?> cursos de 4 horas</span></span>
          <span class="chip" style="font-size:.85rem;padding:.6rem 1rem"><?= icon('pin') ?><span>Universidad de la Amazonia</span></span>
        </div>
      </div>
      <div class="split__media reveal reveal--right"><?= imagen('img/sede/entrada-uniamazonia.jpg', 'Entrada de la Universidad de la Amazonia') ?></div>
    </div>
  </div>
</section>

<section class="section section--soft" aria-labelledby="cursos-t">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow"><?= fecha_es($escuela['fecha'] ?? '') ?></span>
      <h2 class="section-title" id="cursos-t">Cursos de la <em>Escuela</em></h2>
    </div>
    <div class="courses">
      <?php foreach (($escuela['cursos'] ?? []) as $i => $c):
        $p = persona($c['persona'] ?? '');
        if ($p) $docentes[] = $p; ?>
        <article class="course reveal" style="--d:<?= $i * 120 ?>ms">
          <div class="course__num"><?= icon($c['icono'] ?? 'flask') ?></div>
          <div>
            <h3><?= e($c['titulo']) ?></h3>
            <div class="course__meta">
              <span class="chip"><?= icon('clock') ?><span><?= (int) $c['horas'] ?> horas</span></span>
              <span class="chip"><?= icon('calendar') ?><span><?= fecha_es($escuela['fecha'] ?? '', true) ?></span></span>
            </div>
          </div>
          <?php if ($p): ?>
            <button class="course__who" type="button" data-bio="bio-<?= e($p['id']) ?>" aria-haspopup="dialog">
              <?php if (asset_existe($p['foto'])): ?><img src="<?= e(asset($p['foto'])) ?>" alt=""><?php endif; ?>
              <span><strong><?= e(trim($p['titulo'] . ' ' . $p['nombre'])) ?></strong><small>Ver biografía</small></span>
            </button>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>

    <div class="cta-box reveal" style="margin-top:3rem">
      <div><h2>Inscripción a los cursos</h2><p>Los cupos son limitados. Consulta las condiciones de inscripción.</p></div>
      <?php if ($l = enlace($LINKS['inscripcion_escuela'])): ?>
        <a class="btn btn--accent btn--lg" href="<?= e($l) ?>" target="_blank" rel="noopener"><?= icon('atom') ?><span>Inscribirme a un curso</span></a>
      <?php else: ?>
        <a class="btn btn--accent btn--lg" href="#" data-pendiente="La inscripción a los cursos se habilitará próximamente."><?= icon('atom') ?><span>Inscribirme a un curso</span></a>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php if ($docentes): ?>
<section class="section" aria-labelledby="docentes-t">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Equipo docente</span>
      <h2 class="section-title" id="docentes-t">Docentes de la <em>Escuela</em></h2>
    </div>
    <div class="people-grid">
      <?php foreach ($docentes as $i => $p) echo person_card($p, $i); ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>

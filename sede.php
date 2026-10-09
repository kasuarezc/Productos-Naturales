<?php
$PAGE = [
    'key'         => 'sede',
    'titulo'      => 'Sede',
    'descripcion' => 'Florencia, Caquetá, puerta de entrada a la Amazonía colombiana, y la Universidad de la Amazonia, sede del II Congreso Colombiano de Productos Naturales.',
];
require __DIR__ . '/includes/header.php';
$lugares = data('sede');
$tira = ['img/sede/cascada-carano-2.jpg', 'img/sede/monumento-colonos-2.jpg', 'img/sede/mirador-sacharuna.jpg', 'img/sede/petroglifos-encanto-2.jpg', 'img/sede/las-pailas.jpg', 'img/sede/cueva-7-colores.jpg'];
$palabras = ['Naturaleza', 'Cultura', 'Gastronomía', 'Biodiversidad', 'Historia', 'Ciencia', 'Amazonía'];
?>

<?= page_hero('Florencia, Caquetá', 'Puerta de entrada a la Amazonía colombiana: un territorio privilegiado por su riqueza natural, cultural e histórica.', 'Sede del congreso', 'pin', 'img/sede/cascada-carano.jpg') ?>

<section class="section" aria-labelledby="florencia-t">
  <div class="container">
    <div class="split">
      <div class="reveal reveal--left">
        <span class="eyebrow">La ciudad</span>
        <h2 class="section-title" id="florencia-t">Donde la Amazonía <em>se vive</em></h2>
        <p>Florencia, capital del departamento del Caquetá, se ubica entre las estribaciones de la cordillera Oriental y la extensa región amazónica, lo que le otorga una particular diversidad de paisajes y ecosistemas. Es un destino ideal para acercarse a la naturaleza y conocer la identidad del sur del país.</p>
        <p>La ciudad ofrece restaurantes, espacios culturales, zonas comerciales y lugares de esparcimiento, especialmente en la zona rosa y sus alrededores, además de parques, iglesias, edificaciones históricas y construcciones modernas que hacen parte de su memoria.</p>
        <p>Sus alrededores ofrecen ríos, quebradas, cascadas y bosques con gran diversidad de flora y fauna, ideales para el senderismo, el avistamiento de aves y el ecoturismo: la misma biodiversidad que inspira buena parte de las investigaciones en productos naturales amazónicos.</p>
      </div>
      <div class="split__media reveal reveal--right"><?= imagen('img/sede/monumento-colonos.jpg', 'Monumento a los Colonos, Florencia') ?></div>
    </div>
  </div>
</section>

<div class="ribbon" aria-label="Florencia: una experiencia más allá del congreso">
  <div class="ribbon__track">
    <?php for ($k = 0; $k < 2; $k++): ?><span><?php foreach ($palabras as $p): ?><?= e($p) ?> <?= icon('leaf') ?> <?php endforeach; ?></span><?php endfor; ?>
  </div>
</div>

<section class="section" aria-label="Galería de Florencia">
  <div class="container">
    <div class="strip">
      <?php foreach ($tira as $i => $img): ?>
        <figure class="reveal" style="--d:<?= $i * 80 ?>ms"><?= imagen($img, 'Paisaje de Florencia, Caquetá') ?></figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--soft" aria-labelledby="visitar-t">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Florencia: una experiencia más allá del congreso</span>
      <h2 class="section-title" id="visitar-t">¿Qué visitar en <em>Florencia</em>?</h2>
      <p class="section-lead">Monumentos, museos y escenarios naturales para quienes llegan desde otras regiones de Colombia o desde otros países.</p>
    </div>
    <div class="places">
      <?php foreach ($lugares as $i => $l): ?>
        <article class="place reveal" style="--d:<?= ($i % 4) * 80 ?>ms" tabindex="0">
          <?= imagen($l['imagen'], $l['nombre'], '', 'Foto pendiente') ?>
          <div class="place__body">
            <span class="place__tag"><?= e($l['tipo']) ?></span>
            <h3><?= e($l['nombre']) ?></h3>
            <p><?= e($l['descripcion']) ?></p>
            <?php if ($l['credito']): ?><small><?= icon('image') ?> Fuente: <?= e($l['credito']) ?></small><?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" aria-labelledby="udla-t">
  <div class="container">
    <div class="split split--rev">
      <div class="reveal reveal--right">
        <span class="eyebrow">Universidad de la Amazonia</span>
        <h2 class="section-title" id="udla-t">Escenario de <em>cuatro encuentros</em></h2>
        <p>Comprometida con la excelencia académica, la investigación científica y el desarrollo sostenible de la región amazónica, la Universidad de la Amazonia será el escenario del II Congreso Colombiano de Productos Naturales, el X SEQUIAMAZ, la V EAAQ y el III Simposio Internacional de Alimentos Funcionales para la Amazonía.</p>
        <p>Ubicada en el corazón de la Amazonía colombiana, ofrece un escenario privilegiado para conectar la investigación con la riqueza biológica, cultural y social del territorio.</p>
        <p class="welcome__quote" style="margin-top:1rem"><?= icon('quote') ?>Te esperamos para vivir una experiencia de ciencia, conocimiento, formación y conexión con uno de los territorios más biodiversos del planeta.</p>
        <address class="info-card" style="margin-top:1.5rem">
          <div class="card__icon"><?= icon('pin') ?></div>
          <div><h3><?= e($SITE['universidad']) ?></h3><p><?= e($SITE['direccion']) ?><br><?= e($SITE['direccion_2']) ?></p></div>
        </address>
      </div>
      <div class="split__media reveal reveal--left"><?= imagen('img/sede/auditorio-angel-cuniberti.jpeg', 'Auditorio Angel Cuniberti, Universidad de la Amazonia') ?></div>
    </div>
    <div class="map reveal" style="margin-top:3rem">
      <iframe title="Mapa: Universidad de la Amazonia, Sede Porvenir" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
        src="https://maps.google.com/maps?q=<?= rawurlencode($SITE['mapa_query']) ?>&z=15&output=embed"></iframe>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

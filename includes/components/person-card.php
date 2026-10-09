<?php
/**
 * Tarjeta dinámica y animada para conferencistas, comités y ponentes.
 * Datos desde data/personas.json. Campos:
 *   id, titulo, nombre, grupo, institucion, nacionalidad, perfil,
 *   biografia (array de párrafos), ponencia, foto, orcid, scholar, enlaces[]
 *
 * La biografía completa se abre en un modal (assets/js/people.js).
 */
function person_card(array $p, int $indice = 0): string
{
    $grupos = grupos_personas();
    $g = $grupos[$p['grupo'] ?? ''] ?? ['label' => '', 'corto' => '', 'tipo' => ''];
    $nombreCompleto = trim(($p['titulo'] ?? '') . ' ' . ($p['nombre'] ?? ''));
    $foto = $p['foto'] ?? '';
    $orcid = enlace($p['orcid'] ?? '');
    $scholar = enlace($p['scholar'] ?? '');
    $bio = $p['biografia'] ?? [];
    $tid = 'bio-' . e($p['id'] ?? $indice);

    ob_start(); ?>
<article class="person-card reveal" data-filter-item="personas" data-grupo="<?= e($p['grupo'] ?? '') ?>" data-tipo="<?= e($g['tipo']) ?>" style="--d:<?= ($indice % 4) * 80 ?>ms" tabindex="-1">
  <div class="person-card__inner" data-tilt>
    <span class="person-card__badge person-card__badge--<?= e($p['grupo'] ?? '') ?>"><?= e($g['corto']) ?></span>

    <div class="person-card__photo">
      <span class="person-card__ring" aria-hidden="true"></span>
      <?php if ($foto && asset_existe($foto)): ?>
        <img src="<?= e(asset($foto)) ?>" alt="Fotografía de <?= e($nombreCompleto) ?>" loading="lazy" decoding="async">
      <?php else: ?>
        <div class="person-card__avatar" role="img" aria-label="Foto pendiente de <?= e($nombreCompleto) ?>">
          <span><?= e(iniciales($p['nombre'] ?? '')) ?></span><small>Foto pendiente</small>
        </div>
      <?php endif; ?>
    </div>

    <div class="person-card__body">
      <h3 class="person-card__name"><?= e($nombreCompleto) ?></h3>
      <?php if (!empty($p['institucion'])): ?>
        <p class="person-card__inst"><?= icon('building') ?><span><?= e($p['institucion']) ?></span></p>
      <?php endif; ?>
      <p class="person-card__nat"><?= icon('globe') ?><span><?= e($p['nacionalidad'] ?: 'Nacionalidad por confirmar') ?></span></p>

      <?php if (!empty($p['perfil'])): ?>
        <p class="person-card__profile"><?= e($p['perfil']) ?></p>
      <?php endif; ?>

      <?php if (!empty($p['ponencia'])): ?>
        <p class="person-card__talk"><?= icon('molecule') ?><span><?= e($p['ponencia']) ?></span></p>
      <?php endif; ?>
    </div>

    <div class="person-card__actions">
      <?php if ($orcid): ?>
        <a class="chip chip--orcid" href="<?= e($orcid) ?>" target="_blank" rel="noopener" title="Perfil ORCID">
          <?= icon('orcid') ?><span><?= e(preg_replace('#^https?://orcid\.org/#', '', $orcid)) ?></span>
        </a>
      <?php else: ?>
        <span class="chip chip--off" title="ORCID no disponible"><?= icon('orcid') ?><span>ORCID —</span></span>
      <?php endif; ?>
      <?php if ($scholar): ?>
        <a class="chip chip--scholar" href="<?= e($scholar) ?>" target="_blank" rel="noopener" title="Google Scholar"><?= icon('scholar') ?><span>Scholar</span></a>
      <?php else: ?>
        <span class="chip chip--off" title="Google Scholar no disponible"><?= icon('scholar') ?><span>Scholar —</span></span>
      <?php endif; ?>
      <button class="btn btn--sm btn--ghost person-card__more" type="button" data-bio="<?= $tid ?>" aria-haspopup="dialog">
        <?= icon('book') ?><span>Biografía</span>
      </button>
    </div>
  </div>

  <?= person_bio($p) ?>
</article>
<?php
    return (string) ob_get_clean();
}

/** Datos comunes de una persona. */
function persona_meta(array $p): array
{
    $grupos = grupos_personas();
    return [
        'g'       => $grupos[$p['grupo'] ?? ''] ?? ['label' => '', 'corto' => '', 'tipo' => ''],
        'nombre'  => trim(($p['titulo'] ?? '') . ' ' . ($p['nombre'] ?? '')),
        'foto'    => $p['foto'] ?? '',
        'orcid'   => enlace($p['orcid'] ?? ''),
        'scholar' => enlace($p['scholar'] ?? ''),
        'tid'     => 'bio-' . ($p['id'] ?? ''),
    ];
}

/** Foto o avatar con iniciales (si falta la imagen). */
function persona_foto(array $p, string $clase = ''): string
{
    $m = persona_meta($p);
    if ($m['foto'] && asset_existe($m['foto'])) {
        return '<img class="' . e($clase) . '" src="' . e(asset($m['foto'])) . '" alt="Fotografía de ' . e($m['nombre']) . '" loading="lazy" decoding="async">';
    }
    return '<div class="person-card__avatar ' . e($clase) . '" role="img" aria-label="Foto pendiente de ' . e($m['nombre']) . '"><span>' . e(iniciales($p['nombre'] ?? '')) . '</span><small>Foto pendiente</small></div>';
}

/** Plantilla oculta con la biografía completa (se abre en el modal). Se imprime una sola vez por persona. */
function person_bio(array $p): string
{
    static $impresas = [];
    $id = $p['id'] ?? '';
    if (isset($impresas[$id])) {
        return '';
    }
    $impresas[$id] = true;
    $m = persona_meta($p);
    $g = $m['g']; $nombreCompleto = $m['nombre']; $foto = $m['foto'];
    $orcid = $m['orcid']; $scholar = $m['scholar']; $tid = e($m['tid']);
    $bio = $p['biografia'] ?? [];
    ob_start(); ?>
<template id="<?= $tid ?>">
    <div class="bio">
      <div class="bio__head">
        <div class="bio__photo">
          <?php if ($foto && asset_existe($foto)): ?>
            <img src="<?= e(asset($foto)) ?>" alt="<?= e($nombreCompleto) ?>">
          <?php else: ?>
            <div class="person-card__avatar"><span><?= e(iniciales($p['nombre'] ?? '')) ?></span></div>
          <?php endif; ?>
        </div>
        <div>
          <span class="eyebrow"><?= e($g['label']) ?></span>
          <h2 class="bio__name" id="modal-title"><?= e($nombreCompleto) ?></h2>
          <p class="bio__meta"><?= icon('building') ?> <?= e($p['institucion'] ?? '') ?></p>
          <p class="bio__meta"><?= icon('globe') ?> <?= e($p['nacionalidad'] ?: 'Nacionalidad por confirmar') ?></p>
        </div>
      </div>
      <?php if (!empty($p['perfil'])): ?>
        <h3 class="bio__h">Perfil profesional</h3>
        <p><?= e($p['perfil']) ?></p>
      <?php endif; ?>
      <h3 class="bio__h">Biografía</h3>
      <?php if ($bio): foreach ($bio as $parrafo): ?>
        <p><?= e($parrafo) ?></p>
      <?php endforeach; else: ?>
        <p class="pendiente"><?= icon('info') ?> Biografía pendiente por recibir.</p>
      <?php endif; ?>
      <?php if (!empty($p['ponencia'])): ?>
        <div class="bio__talk"><?= icon('molecule') ?><div><small>Conferencia</small><strong><?= e($p['ponencia']) ?></strong></div></div>
      <?php endif; ?>
      <div class="bio__links">
        <?php if ($orcid): ?><a class="chip chip--orcid" href="<?= e($orcid) ?>" target="_blank" rel="noopener"><?= icon('orcid') ?><span>ORCID <?= e(preg_replace('#^https?://orcid\.org/#', '', $orcid)) ?></span></a><?php endif; ?>
        <?php if ($scholar): ?><a class="chip chip--scholar" href="<?= e($scholar) ?>" target="_blank" rel="noopener"><?= icon('scholar') ?><span>Google Scholar</span></a><?php endif; ?>
        <?php foreach (($p['enlaces'] ?? []) as $l): ?>
          <a class="chip" href="<?= e($l['url']) ?>" target="_blank" rel="noopener"><?= icon('external') ?><span><?= e($l['label']) ?></span></a>
        <?php endforeach; ?>
      </div>
    </div>
</template>
<?php
    return (string) ob_get_clean();
}

/**
 * Tarjeta DESTACADA (horizontal, grande) para conferencistas principales.
 */
function speaker_feature(array $p, int $indice = 0): string
{
    $m = persona_meta($p);
    ob_start(); ?>
<article class="speaker-feature reveal<?= $indice % 2 ? ' speaker-feature--alt' : '' ?>" style="--d:<?= $indice * 120 ?>ms">
  <div class="speaker-feature__media">
    <span class="speaker-feature__ring" aria-hidden="true"></span>
    <?= persona_foto($p, 'speaker-feature__img') ?>
    <span class="speaker-feature__country"><?= icon('globe') ?> <?= e($p['nacionalidad'] ?: 'Por confirmar') ?></span>
  </div>
  <div class="speaker-feature__body">
    <span class="speaker-feature__tag"><?= icon('star') ?> Conferencista plenario internacional</span>
    <h3 class="speaker-feature__name"><?= e($m['nombre']) ?></h3>
    <p class="speaker-feature__inst"><?= icon('building') ?> <?= e($p['institucion'] ?? '') ?></p>
    <?php if (!empty($p['ponencia'])): ?>
      <blockquote class="speaker-feature__talk"><small>Conferencia</small><?= e($p['ponencia']) ?></blockquote>
    <?php endif; ?>
    <p class="speaker-feature__profile"><?= e(resumen($p['perfil'] ?? '', 210)) ?></p>
    <div class="speaker-feature__actions">
      <button class="btn btn--accent btn--sm" type="button" data-bio="<?= e($m['tid']) ?>" aria-haspopup="dialog"><?= icon('book') ?><span>Ver biografía</span></button>
      <?php if ($m['orcid']): ?><a class="chip chip--orcid" href="<?= e($m['orcid']) ?>" target="_blank" rel="noopener"><?= icon('orcid') ?><span><?= e(preg_replace('#^https?://orcid\.org/#', '', $m['orcid'])) ?></span></a><?php endif; ?>
      <?php if ($m['scholar']): ?><a class="chip chip--scholar" href="<?= e($m['scholar']) ?>" target="_blank" rel="noopener"><?= icon('scholar') ?><span>Scholar</span></a><?php endif; ?>
    </div>
  </div>
  <?= person_bio($p) ?>
</article>
<?php
    return (string) ob_get_clean();
}

/**
 * Tarjeta COMPACTA para integrantes de comités.
 */
function committee_card(array $p, int $indice = 0): string
{
    $m = persona_meta($p);
    ob_start(); ?>
<article class="committee-card reveal" style="--d:<?= $indice * 90 ?>ms">
  <button class="committee-card__btn" type="button" data-bio="<?= e($m['tid']) ?>" aria-haspopup="dialog">
    <span class="committee-card__photo"><?= persona_foto($p) ?></span>
    <span class="committee-card__txt">
      <strong><?= e($m['nombre']) ?></strong>
      <small><?= e(resumen($p['perfil'] ?? ($p['institucion'] ?? ''), 95)) ?></small>
      <span class="committee-card__more">Ver perfil <?= icon('arrow') ?></span>
    </span>
  </button>
  <div class="committee-card__links">
    <?php if ($m['orcid']): ?><a href="<?= e($m['orcid']) ?>" target="_blank" rel="noopener" aria-label="ORCID de <?= e($m['nombre']) ?>" title="ORCID"><?= icon('orcid') ?></a><?php endif; ?>
    <?php if ($m['scholar']): ?><a href="<?= e($m['scholar']) ?>" target="_blank" rel="noopener" aria-label="Google Scholar de <?= e($m['nombre']) ?>" title="Google Scholar"><?= icon('scholar') ?></a><?php endif; ?>
  </div>
  <?= person_bio($p) ?>
</article>
<?php
    return (string) ob_get_clean();
}

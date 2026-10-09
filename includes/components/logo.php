<?php
/**
 * Logo animado del evento en SVG en línea.
 * - Emblema (mapa de biodiversidad) flotando
 * - Anillo naranja que se dibuja al cargar + órbita punteada giratoria
 * - "Electrones" orbitando (SMIL animateMotion)
 * - Texto con entrada escalonada y brillo degradado en "PRODUCTOS NATURALES"
 *
 * $variante: 'header' (compacto) | 'footer' (texto claro) | 'hero' (grande con lema)
 */
function logo_svg(string $variante = 'header'): string
{
    static $n = 0;
    $n++;
    $id = 'lg' . $n;
    $emblema = asset('img/brand/emblema.png');
    $claro = $variante === 'footer';
    $verde = $claro ? '#ffffff' : '#0B6E0C';
    $gris  = $claro ? 'rgba(255,255,255,.85)' : '#4f4d4b';
    $conLema = $variante === 'hero';
    $alto = $conLema ? 196 : 132;

    ob_start(); ?>
<svg class="logo-svg logo-svg--<?= e($variante) ?>" viewBox="0 0 <?= $conLema ? 520 : 384 ?> <?= $alto ?>" role="img" aria-labelledby="<?= $id ?>-t" xmlns="http://www.w3.org/2000/svg">
  <title id="<?= $id ?>-t">II Congreso Colombiano de Productos Naturales</title>
  <defs>
    <linearGradient id="<?= $id ?>-ring" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#EDB02E"/><stop offset="1" stop-color="#F28E13"/>
    </linearGradient>
    <linearGradient id="<?= $id ?>-shine" x1="0" y1="0" x2="1" y2="0" gradientUnits="objectBoundingBox">
      <stop offset="0"   stop-color="<?= $claro ? '#E4F2E5' : '#96A61C' ?>"/>
      <stop offset=".45" stop-color="<?= $claro ? '#E4F2E5' : '#96A61C' ?>"/>
      <stop offset=".5"  stop-color="#F9FF9A"/>
      <stop offset=".55" stop-color="<?= $claro ? '#E4F2E5' : '#96A61C' ?>"/>
      <stop offset="1"   stop-color="<?= $claro ? '#E4F2E5' : '#96A61C' ?>"/>
      <animateTransform attributeName="gradientTransform" type="translate" values="-1 0;1 0;1 0" keyTimes="0;.45;1" dur="5s" repeatCount="indefinite"/>
    </linearGradient>
    <filter id="<?= $id ?>-glow" x="-30%" y="-30%" width="160%" height="160%">
      <feGaussianBlur stdDeviation="3" result="b"/>
      <feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
    </filter>
    <path id="<?= $id ?>-orbit" d="M66,3 a63,63 0 1,1 -0.1,0"/>
  </defs>

  <!-- Emblema -->
  <g transform="translate(0 <?= $conLema ? 20 : 0 ?>)"><g class="logo-mark">
    <circle class="logo-ring" cx="66" cy="66" r="63" fill="none" stroke="url(#<?= $id ?>-ring)" stroke-width="1.6" stroke-linecap="round" opacity=".75"/>
    <circle class="logo-orbit" cx="66" cy="66" r="66" fill="none" stroke="#84BFA4" stroke-width="1.2" stroke-dasharray="2 7" opacity=".9"/>
    <g class="logo-emblem">
      <image href="<?= e($emblema) ?>" x="6" y="4" width="120" height="124" preserveAspectRatio="xMidYMid meet"/>
    </g>
    <g filter="url(#<?= $id ?>-glow)">
      <circle r="3.4" fill="#F28E13"><animateMotion dur="7s" repeatCount="indefinite" rotate="auto"><mpath href="#<?= $id ?>-orbit"/></animateMotion></circle>
      <circle r="2.6" fill="#048C8C"><animateMotion dur="7s" begin="-3.5s" repeatCount="indefinite"><mpath href="#<?= $id ?>-orbit"/></animateMotion></circle>
      <circle r="2" fill="#96A61C"><animateMotion dur="11s" begin="-2s" repeatCount="indefinite" keyPoints="1;0" keyTimes="0;1" calcMode="linear"><mpath href="#<?= $id ?>-orbit"/></animateMotion></circle>
    </g>
  </g></g>

  <!-- Texto -->
  <g class="logo-text" transform="translate(146 <?= $conLema ? 20 : 0 ?>)" font-family="Oswald, 'Arial Narrow', sans-serif">
    <g class="logo-roman" fill="<?= $verde ?>">
      <rect class="lt lt1" x="0"  y="8" width="12" height="62" rx="1.5"/>
      <rect class="lt lt1" x="18" y="8" width="12" height="62" rx="1.5"/>
    </g>
    <text class="lt lt2" x="38" y="42" font-size="43" font-weight="700" letter-spacing=".3" fill="<?= $verde ?>">CONGRESO</text>
    <text class="lt lt3" x="39" y="69" font-size="24.5" font-weight="600" letter-spacing=".9" fill="<?= $verde ?>">COLOMBIANO DE</text>
    <text class="lt lt4 logo-shine" x="0" y="101" font-size="34" font-weight="700" letter-spacing="2.6" fill="url(#<?= $id ?>-shine)" stroke="<?= $claro ? 'none' : '#6f7d10' ?>" stroke-width=".4">PRODUCTOS</text>
    <text class="lt lt5" x="0" y="130" font-size="34" font-weight="700" letter-spacing="4.4" fill="<?= $claro ? '#ffffff' : '#0B750E' ?>">NATURALES</text>
    <?php if ($conLema): ?>
    <g class="lt lt6">
      <path d="M0 142h372l-14 24H0z" fill="<?= $claro ? 'rgba(255,255,255,.15)' : '#0B750E' ?>"/>
      <text x="10" y="159" font-family="Montserrat, sans-serif" font-size="11.5" font-weight="700" fill="#fff">Biodiversidad que inspira, ciencia que transforma</text>
    </g>
    <?php endif; ?>
    <!-- Hoja decorativa -->
    <path class="lt lt5 logo-leaf" d="M198 127c4-14 20-24 38-22-6 16-22 24-38 22zm4-3c8-6 17-11 28-15" fill="#96A61C" stroke="#0B750E" stroke-width="1"/>
  </g>
</svg>
<?php
    return trim((string) ob_get_clean());
}

<?php
/**
 * Librería de iconos SVG en línea (temática química / naturaleza).
 * Uso: <?= icon('flask') ?>   o   <?= icon('atom', 'icon--lg') ?>
 * Todos usan currentColor, así heredan el color del texto y
 * pueden animarse con CSS (ver assets/css/animations.css).
 */
function icon(string $nombre, string $clase = ''): string
{
    static $i = [
        // ---- Química ----
        'flask'    => '<path d="M9 3h6M10 3v6.2L4.6 18.4A1.7 1.7 0 0 0 6.1 21h11.8a1.7 1.7 0 0 0 1.5-2.6L14 9.2V3"/><path d="M7.2 14h9.6" class="i-liquid"/><circle cx="10" cy="17" r=".9" class="i-bubble b1"/><circle cx="13.5" cy="18.2" r=".7" class="i-bubble b2"/><circle cx="12" cy="15.6" r=".5" class="i-bubble b3"/>',
        'beaker'   => '<path d="M5 3h14M6 3v15a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V3"/><path d="M6 12h12" class="i-liquid"/><path d="M9 7h2M9 9.5h1.5"/>',
        'tube'     => '<path d="M14.5 2.5l7 7M16 4l-9.9 9.9a3.3 3.3 0 0 0 4.7 4.7L20.6 8.7"/><path d="M9.5 12.5h7" class="i-liquid"/>',
        'atom'     => '<circle cx="12" cy="12" r="1.6" class="i-nucleus"/><g class="i-orbit"><ellipse cx="12" cy="12" rx="10" ry="4"/><ellipse cx="12" cy="12" rx="10" ry="4" transform="rotate(60 12 12)"/><ellipse cx="12" cy="12" rx="10" ry="4" transform="rotate(120 12 12)"/></g>',
        'molecule' => '<circle cx="6" cy="7" r="2.2"/><circle cx="18" cy="7" r="2.2"/><circle cx="12" cy="17" r="2.6" class="i-nucleus"/><path d="M7.7 8.5l2.8 6.3M16.3 8.5l-2.8 6.3M8.2 7h7.6"/>',
        'hexagon'  => '<path d="M12 2.8l8 4.6v9.2l-8 4.6-8-4.6V7.4z"/><path d="M12 6.5l4.8 2.8v5.4L12 17.5l-4.8-2.8V9.3z" class="i-inner"/>',
        'leaf'     => '<path d="M5 19c0-8 5-14 15-15-1 10-7 15-15 15z"/><path d="M5 19c3-4 6-7 10-9.5"/>',
        'sprout'   => '<path d="M12 21v-8"/><path d="M12 13c0-4-3-7-8-7 0 4 3 7 8 7z"/><path d="M12 11c0-3.5 2.6-6 7-6 0 3.5-2.6 6-7 6z"/>',
        'microscope'=> '<path d="M6 21h12M9 18h6M14 4l3 3-5.5 5.5-3-3z"/><path d="M12.5 14a5 5 0 1 1-6.5 4"/><path d="M15.5 2.5l2 2"/>',
        'dna'      => '<path d="M7 3c0 6 10 6 10 12s-10 6-10 6M17 3c0 6-10 6-10 12s10 6 10 6"/><path d="M8.5 7h7M8.5 17h7M10 12h4"/>',
        'pipette'  => '<path d="M19 5l-1-1a2 2 0 0 0-2.8 0l-2.4 2.4 3.8 3.8L19 7.8A2 2 0 0 0 19 5z"/><path d="M12.8 6.4l-8 8L4 20l5.6-.8 8-8"/>',
        'food'     => '<path d="M12 21c-5 0-8-3.5-8-8 0-3.5 2.5-6 5-6 1.3 0 2.2.5 3 1 .8-.5 1.7-1 3-1 2.5 0 5 2.5 5 6 0 4.5-3 8-8 8z"/><path d="M12 8c0-2.5 1-4 3-5"/>',
        // ---- Interfaz ----
        'news'     => '<rect x="3" y="4" width="15" height="16" rx="2"/><path d="M18 8h2a1 1 0 0 1 1 1v9a2 2 0 0 1-4 0M7 8h7M7 12h7M7 16h4"/>',
        'users'    => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><circle cx="17" cy="9" r="2.6"/><path d="M16 14.2a5 5 0 0 1 5.5 5"/>',
        'user'     => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'ticket'   => '<path d="M3 8a2 2 0 0 0 0 4v0a2 2 0 0 0 0 4v2h18v-2a2 2 0 0 1 0-4 2 2 0 0 1 0-4V6H3z"/><path d="M14 6v12" stroke-dasharray="2 2"/>',
        'pin'      => '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
        'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3.5 6.5l8.5 6 8.5-6"/>',
        'phone'    => '<path d="M5 3h3l2 5-2.5 1.5a11 11 0 0 0 7 7L16 14l5 2v3a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2z"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/><circle cx="12" cy="15" r="1.3"/>',
        'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'globe'    => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.5 3.5 5.5 3.5 9s-1 6.5-3.5 9c-2.5-2.5-3.5-5.5-3.5-9s1-6.5 3.5-9z"/>',
        'book'     => '<path d="M4 4.5A1.5 1.5 0 0 1 5.5 3H20v15H5.5A1.5 1.5 0 0 0 4 19.5z"/><path d="M4 19.5A1.5 1.5 0 0 0 5.5 21H20v-3M8 7h8M8 10.5h6"/>',
        'cap'      => '<path d="M2 9l10-5 10 5-10 5z"/><path d="M6 11v5c3 2.5 9 2.5 12 0v-5M22 9v6"/>',
        'building' => '<path d="M4 21V5l8-3 8 3v16M4 21h16M9 21v-5h6v5M8 8h2M14 8h2M8 12h2M14 12h2"/>',
        'download' => '<path d="M12 3v12M7 10l5 5 5-5M4 20h16"/>',
        'upload'   => '<path d="M12 21V9M7 14l5-5 5 5M4 4h16"/>',
        'send'     => '<path d="M21 3L10 14M21 3l-7 18-4-7-7-4z"/>',
        'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'arrow-l'  => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
        'chev-l'   => '<path d="M15 5l-7 7 7 7"/>',
        'chev-r'   => '<path d="M9 5l7 7-7 7"/>',
        'chev-d'   => '<path d="M5 9l7 7 7-7"/>',
        'up'       => '<path d="M12 19V5M6 11l6-6 6 6"/>',
        'close'    => '<path d="M6 6l12 12M18 6L6 18"/>',
        'check'    => '<path d="M4 12.5l5 5L20 6.5"/>',
        'alert'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v6M12 16.5v.5"/>',
        'search'   => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/>',
        'external' => '<path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
        'image'    => '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M21 16l-5-5-9 9"/>',
        'target'   => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.5"/>',
        'network'  => '<circle cx="12" cy="5" r="2.2"/><circle cx="5" cy="18" r="2.2"/><circle cx="19" cy="18" r="2.2"/><path d="M11 7l-5 9M13 7l5 9M7.2 18h9.6"/>',
        'handshake'=> '<path d="M2 11l4-4 4 2 3-2 5 1 4 4-5 5-3-1-3 2-3-3-3-1z"/><path d="M10 9l-2 3 2 1 3-3"/>',
        'quote'    => '<path d="M10 11H6.5A3.5 3.5 0 0 1 10 7.5V6a5 5 0 0 0-5 5v6h5zM20 11h-3.5A3.5 3.5 0 0 1 20 7.5V6a5 5 0 0 0-5 5v6h5z" fill="currentColor" stroke="none"/>',
        'star'     => '<path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"/>',
        'mic'      => '<rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5 11a7 7 0 0 0 14 0M12 18v3M8 21h8"/>',
        'info'     => '<circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7.5v.5"/>',
        'menu'     => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        // ---- Académicos ----
        'orcid'    => '<circle cx="12" cy="12" r="9.5"/><path d="M8.5 10v7M8.5 7.2v.3M11.5 10h2.2a3.5 3.5 0 0 1 0 7h-2.2z"/>',
        'scholar'  => '<path d="M12 3L2 10h20z"/><circle cx="12" cy="15" r="5"/><path d="M8 14h8"/>',
        // ---- Redes (relleno) ----
        'facebook' => '<path d="M14 8h3V4h-3a4 4 0 0 0-4 4v2H7v4h3v7h4v-7h3l1-4h-4V8.5a.5.5 0 0 1 .5-.5z" fill="currentColor" stroke="none"/>',
        'instagram'=> '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>',
        'youtube'  => '<rect x="2" y="5" width="20" height="14" rx="4"/><path d="M10 9l5 3-5 3z" fill="currentColor"/>',
        'x'        => '<path d="M4 4l16 16M20 4L4 20"/>',
        'linkedin' => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M7.5 10v7M7.5 7v.2M11 17v-7M11 13a3 3 0 0 1 6 0v4"/>',
        'whatsapp' => '<path d="M4 20l1.3-4A8.5 8.5 0 1 1 8 19z"/><path d="M9 8.5c0 3.5 3 6.5 6.5 6.5l1-1.5-2-1-1 .8a5 5 0 0 1-2.8-2.8l.8-1-1-2z"/>',
    ];
    $svg = $i[$nombre] ?? $i['info'];
    return '<svg class="icon icon-' . e($nombre) . ($clase ? ' ' . e($clase) : '') . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $svg . '</svg>';
}

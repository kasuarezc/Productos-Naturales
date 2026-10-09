<?php
// La página de Inscripciones ahora vive dentro de "Resúmenes y Fechas".
require_once __DIR__ . '/includes/config.php';
header('Location: ' . url('resumenes.php#inscripcion'), true, 301);
exit;

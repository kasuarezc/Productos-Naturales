<?php
/**
 * ============================================================
 *  CONFIGURACIÓN CENTRAL DEL PORTAL
 *  II Congreso Colombiano de Productos Naturales
 * ============================================================
 *  Todo lo que cambia con frecuencia (enlaces, fechas, redes,
 *  correo, URL del formulario) se edita AQUÍ y se refleja en
 *  todo el sitio. No es necesario tocar las páginas.
 * ============================================================
 */

// ---------- Entorno ----------
// 'local' mientras trabajas en XAMPP, 'produccion' en el servidor institucional.
define('ENTORNO', 'local');

if (ENTORNO === 'local') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

date_default_timezone_set('America/Bogota');
mb_internal_encoding('UTF-8');

// ---------- Rutas ----------
define('ROOT_PATH', dirname(__DIR__));
define('INC_PATH',  ROOT_PATH . '/includes');
define('DATA_PATH', ROOT_PATH . '/data');
define('STORAGE_PATH', ROOT_PATH . '/storage');

/**
 * URL base detectada automáticamente.
 * Funciona igual en http://localhost/congreso/ que en
 * https://dominio.edu.co/  o  https://dominio.edu.co/congreso/
 */
if (!defined('BASE_URL')) {
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
    $dir = rtrim(dirname($script), '/');
    // Si el script vive en /api, la base es la carpeta superior.
    if (substr($dir, -4) === '/api') {
        $dir = substr($dir, 0, -4);
    }
    define('BASE_URL', $dir . '/');
}

// ---------- Identidad del evento ----------
$SITE = [
    'nombre'        => 'II Congreso Colombiano de Productos Naturales',
    'nombre_corto'  => 'II CCPN 2027',
    'lema'          => 'Biodiversidad que inspira, ciencia que transforma la Amazonia',
    'descripcion'   => 'II Congreso Colombiano de Productos Naturales, X SEQUIAMAZ, III Simposio Internacional de Alimentos Funcionales para la Amazonía y V Escuela Andino-Amazónica de Química. Florencia, Caquetá, 11 al 14 de mayo de 2027.',
    'ciudad'        => 'Florencia, Caquetá',
    'fecha_texto'   => '11 – 14 de mayo de 2027',
    // Fecha/hora de inicio para la cuenta regresiva (ISO 8601, hora Colombia)
    'fecha_inicio'  => '2027-05-11T08:00:00-05:00',
    'universidad'   => 'Universidad de la Amazonia',
    'correo'        => 'sequiamaz@uniamazonia.edu.co',
    'telefono'      => '',  // PENDIENTE: teléfono de contacto (opcional)
    'direccion'     => 'Sede Porvenir, Calle 17 Diagonal 17 con Carrera 3F, Barrio Porvenir',
    'direccion_2'   => 'Florencia, Caquetá, Colombia, Suramérica',
    'mapa_query'    => 'Universidad de la Amazonia Sede Porvenir, Florencia, Caquetá',
];

// ---------- Enlaces externos (PENDIENTES marcados con '') ----------
$LINKS = [
    'inscripcion'       => '',   // PENDIENTE: enlace del formulario de inscripción
    'envio_resumenes'   => '',   // PENDIENTE: enlace para envío de resúmenes
    'plantilla_resumen' => BASE_URL . 'assets/docs/plantilla-resumen.docx',
    'inscripcion_escuela' => '', // PENDIENTE: inscripción a cursos de la Escuela
    'universidad'       => 'https://www.uniamazonia.edu.co',
];

// ---------- Redes sociales (deja '' para ocultar un botón) ----------
$REDES = [
    'facebook'  => 'https://web.facebook.com/profile.php?id=61595360933281',  // PENDIENTE
    'instagram' => 'https://www.instagram.com/sequiamaz/',  // PENDIENTE
    'youtube'   => '',  // PENDIENTE
    'x'         => '',  // PENDIENTE
    'linkedin'  => 'https://www.linkedin.com/events/7513395190154452992/?viewAsMember=true',  // PENDIENTE
    'whatsapp'  => '',  // PENDIENTE (formato: https://wa.me/57XXXXXXXXXX)
];

// ---------- Formulario de contacto ----------
// URL del Web App de Google Apps Script (ver docs/GUIA-PASO-A-PASO.md, Paso 6)
define('GAS_WEBAPP_URL', 'https://script.google.com/macros/s/AKfycbxt1pATuU6YUlfrn9VP0HGex4jv8XuSkSs6k2KZ_Q5kJI-sRQV_lVmnItHW-z3wkSnB/exec');           // PENDIENTE: pegar aquí la URL /exec
define('GAS_SECRET', 'CongresoPN2027-5347001f9fefae5d'); // Debe coincidir con Code.gs
define('CURL_VERIFY_SSL', true);        // En XAMPP Windows sin cacert.pem: false (solo local)
define('GUARDAR_COPIA_LOCAL', true);    // Respaldo CSV en /storage (recomendado)

// ---------- Menú principal ----------
$MENU = [
    ['key' => 'inicio',        'label' => 'Inicio',                          'url' => '',                 'icon' => 'flask'],
    ['key' => 'noticias',      'label' => 'Noticias',                        'url' => 'noticias.php',     'icon' => 'news'],
    ['key' => 'comites',       'label' => 'Conferencistas y Comités',        'url' => 'comites.php',      'icon' => 'users'],
    ['key' => 'resumenes',     'label' => 'Resúmenes y Fechas',              'url' => 'resumenes.php',    'icon' => 'calendar'],
    ['key' => 'escuela',       'label' => 'Escuela Andino-Amazónica de Química', 'label_corto' => 'Escuela EAAQ', 'url' => 'escuela.php', 'icon' => 'atom'],
    ['key' => 'sede',          'label' => 'Sede',                            'url' => 'sede.php',         'icon' => 'pin'],
    ['key' => 'contacto',      'label' => 'Contacto',                        'url' => 'contacto.php',     'icon' => 'mail'],
];

require_once INC_PATH . '/functions.php';
require_once INC_PATH . '/icons.php';

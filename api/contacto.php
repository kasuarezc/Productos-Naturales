<?php
/**
 * API del formulario de contacto.
 * 1. Valida CSRF, honeypot, tiempo mínimo y límite de envíos.
 * 2. Valida y limpia los campos.
 * 3. Envía los datos al Web App de Google Apps Script
 *    (guarda en Google Sheets + notifica a sequiamaz@uniamazonia.edu.co
 *     + respuesta automática al remitente).
 * 4. Guarda copia de respaldo en /storage/contactos.csv
 * Responde siempre JSON.
 */
require_once dirname(__DIR__) . '/includes/config.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function responder(int $codigo, array $datos): void
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    responder(405, ['ok' => false, 'mensaje' => 'Método no permitido.']);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// ---------- Seguridad ----------
$token = $_POST['csrf'] ?? '';
if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
    responder(419, ['ok' => false, 'mensaje' => 'La sesión expiró. Recarga la página e inténtalo de nuevo.']);
}
// Honeypot: los humanos no ven este campo
if (!empty($_POST['website'])) {
    responder(200, ['ok' => true]); // se descarta en silencio
}
// Tiempo mínimo para llenar el formulario (anti-bots)
$inicio = (int) ($_POST['ts'] ?? 0);
if ($inicio > 0 && (time() - $inicio) < 4) {
    responder(429, ['ok' => false, 'mensaje' => 'Envío demasiado rápido. Espera unos segundos e inténtalo de nuevo.']);
}
// Límite: 1 envío cada 45 s y máximo 5 por sesión
$ahora = time();
$_SESSION['envios'] = array_values(array_filter($_SESSION['envios'] ?? [], fn($t) => $ahora - $t < 3600));
if (count($_SESSION['envios']) >= 5) {
    responder(429, ['ok' => false, 'mensaje' => 'Has alcanzado el límite de mensajes por hora. Escríbenos directamente a ' . $SITE['correo'] . '.']);
}
if ($_SESSION['envios'] && $ahora - end($_SESSION['envios']) < 45) {
    responder(429, ['ok' => false, 'mensaje' => 'Ya recibimos un mensaje tuyo hace un momento. Espera unos segundos antes de enviar otro.']);
}

// ---------- Validación ----------
$limpiar = fn($k, $max = 200) => mb_substr(trim(strip_tags((string) ($_POST[$k] ?? ''))), 0, $max);

$perfiles = ['Estudiante de pregrado', 'Estudiante de posgrado', 'Docente / Investigador', 'Profesional', 'Sector empresarial', 'Otro'];
$asuntos = ['Inscripciones y pagos', 'Envío de resúmenes', 'Escuela Andino-Amazónica de Química', 'Patrocinios y alianzas', 'Alojamiento y llegada a Florencia', 'Prensa y comunicaciones', 'Otro'];

$d = [
    'nombre'      => $limpiar('nombre', 120),
    'correo'      => mb_strtolower($limpiar('correo', 160)),
    'telefono'    => $limpiar('telefono', 25),
    'institucion' => $limpiar('institucion', 160),
    'pais'        => $limpiar('pais', 80),
    'perfil'      => $limpiar('perfil', 60),
    'asunto'      => $limpiar('asunto', 80),
    'mensaje'     => $limpiar('mensaje', 2000),
    'acepto'      => !empty($_POST['acepto']) ? 'Sí' : '',
];

$err = [];
if (mb_strlen($d['nombre']) < 3) $err['nombre'] = 'Escribe tu nombre completo.';
if (!filter_var($d['correo'], FILTER_VALIDATE_EMAIL)) $err['correo'] = 'Correo electrónico no válido.';
if ($d['telefono'] !== '' && !preg_match('/^[+\d][\d\s-]{6,19}$/', $d['telefono'])) $err['telefono'] = 'Número no válido.';
if (mb_strlen($d['institucion']) < 2) $err['institucion'] = 'Indica tu institución.';
if (mb_strlen($d['pais']) < 2) $err['pais'] = 'Indica tu país.';
if (!in_array($d['perfil'], $perfiles, true)) $err['perfil'] = 'Selecciona un perfil válido.';
if (!in_array($d['asunto'], $asuntos, true)) $err['asunto'] = 'Selecciona un motivo válido.';
if (mb_strlen($d['mensaje']) < 20) $err['mensaje'] = 'El mensaje debe tener al menos 20 caracteres.';
if ($d['acepto'] !== 'Sí') $err['acepto'] = 'Debes autorizar el tratamiento de datos.';
// Enlaces excesivos = spam
if (preg_match_all('#https?://#i', $d['mensaje']) > 3) $err['mensaje'] = 'El mensaje contiene demasiados enlaces.';

if ($err) {
    responder(422, ['ok' => false, 'mensaje' => 'Revisa los campos marcados.', 'errores' => $err]);
}

$d['fecha'] = date('Y-m-d H:i:s');
$d['ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
$d['origen'] = ($_SERVER['HTTP_HOST'] ?? '') . BASE_URL;

// ---------- Respaldo local CSV ----------
$guardadoLocal = false;
if (GUARDAR_COPIA_LOCAL) {
    if (!is_dir(STORAGE_PATH)) {
        @mkdir(STORAGE_PATH, 0750, true);
    }
    $csv = STORAGE_PATH . '/contactos.csv';
    $nuevo = !is_file($csv);
    if ($fh = @fopen($csv, 'a')) {
        if (flock($fh, LOCK_EX)) {
            if ($nuevo) {
                fwrite($fh, "\xEF\xBB\xBF"); // BOM para que Excel lea tildes
                fputcsv($fh, array_keys($d), ';');
            }
            // Evita inyección de fórmulas en Excel
            $fila = array_map(fn($v) => preg_match('/^[=+\-@]/', (string) $v) ? "'" . $v : $v, $d);
            fputcsv($fh, $fila, ';');
            flock($fh, LOCK_UN);
            $guardadoLocal = true;
        }
        fclose($fh);
    }
}

// ---------- Envío a Google Apps Script ----------
require_once INC_PATH . '/google.php';
$g = enviar_google($d);
$enviadoGoogle = $g['ok'];
$detalle = $g['detalle'];
if (!$enviadoGoogle) {
    registrar_error('Google: ' . $detalle . ' | método=' . $g['metodo'] . ' | csv=' . ($guardadoLocal ? 'sí' : 'no'));
}
if (GUARDAR_COPIA_LOCAL && !$guardadoLocal) {
    registrar_error('No se pudo escribir storage/contactos.csv (verifique permisos de escritura de la carpeta storage)');
}

// ---------- Respaldo 3: correo directo desde el servidor ----------
$enviadoMail = false;
if (!$enviadoGoogle && function_exists('mail')) {
    $cuerpo = "Nuevo mensaje desde el sitio web (respaldo: Google no respondió)\n\n";
    foreach (['nombre', 'correo', 'telefono', 'institucion', 'pais', 'perfil', 'asunto', 'fecha'] as $k) {
        $cuerpo .= ucfirst($k) . ': ' . $d[$k] . "\n";
    }
    $cuerpo .= "\nMensaje:\n" . $d['mensaje'] . "\n";
    $host = preg_replace('/[^a-z0-9.\-]/i', '', $_SERVER['HTTP_HOST'] ?? 'localhost');
    $cabeceras = "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n"
        . 'From: Sitio web Congreso <no-responder@' . $host . ">\r\n"
        . 'Reply-To: ' . str_replace(["\r", "\n"], '', $d['correo']) . "\r\n";
    $enviadoMail = @mail($SITE['correo'], '=?UTF-8?B?' . base64_encode('[Congreso PN] ' . $d['asunto'] . ' – ' . $d['nombre']) . '?=', $cuerpo, $cabeceras);
}

if ($enviadoGoogle || $guardadoLocal || $enviadoMail) {
    $_SESSION['envios'][] = $ahora;
    $resp = ['ok' => true, 'mensaje' => '¡Gracias! Hemos recibido tu mensaje.'];
    if (ENTORNO === 'local') {
        $resp['debug'] = ['google' => $enviadoGoogle, 'csv' => $guardadoLocal, 'mail' => $enviadoMail, 'detalle' => $detalle];
    }
    responder(200, $resp);
}

$resp = ['ok' => false, 'mensaje' => 'No fue posible registrar tu mensaje en este momento. Escríbenos a ' . $SITE['correo'] . '.'];
if (ENTORNO === 'local') {
    $resp['debug'] = $detalle;
}
responder(500, $resp);

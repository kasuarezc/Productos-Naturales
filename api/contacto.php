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
$enviadoGoogle = false;
$detalle = '';
if (GAS_WEBAPP_URL !== '' && function_exists('curl_init')) {
    $payload = json_encode(['secret' => GAS_SECRET] + $d, JSON_UNESCAPED_UNICODE);
    $ch = curl_init(GAS_WEBAPP_URL);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,   // Apps Script responde con una redirección
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT        => 25,
        CURLOPT_SSL_VERIFYPEER => CURL_VERIFY_SSL,
        CURLOPT_SSL_VERIFYHOST => CURL_VERIFY_SSL ? 2 : 0,
    ]);
    $respuesta = curl_exec($ch);
    $codigo = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $detalle = curl_error($ch);
    curl_close($ch);
    $json = json_decode((string) $respuesta, true);
    $enviadoGoogle = $codigo === 200 && is_array($json) && !empty($json['ok']);
    if (!$enviadoGoogle && $detalle === '') {
        $detalle = 'HTTP ' . $codigo . ' ' . mb_substr((string) $respuesta, 0, 200);
    }
}

if ($enviadoGoogle || $guardadoLocal) {
    $_SESSION['envios'][] = $ahora;
    $resp = ['ok' => true, 'mensaje' => '¡Gracias! Hemos recibido tu mensaje.'];
    if (ENTORNO === 'local') {
        $resp['debug'] = ['google' => $enviadoGoogle, 'csv' => $guardadoLocal, 'detalle' => $detalle];
    }
    responder(200, $resp);
}

$resp = ['ok' => false, 'mensaje' => 'No fue posible registrar tu mensaje en este momento. Escríbenos a ' . $SITE['correo'] . '.'];
if (ENTORNO === 'local') {
    $resp['debug'] = $detalle;
}
responder(500, $resp);

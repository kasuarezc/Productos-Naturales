<?php
/**
 * DIAGNÓSTICO DEL FORMULARIO DE CONTACTO
 * Uso: https://TU-DOMINIO/diagnostico.php?clave=TU_GAS_SECRET
 * (la clave es el valor de GAS_SECRET en includes/config.php)
 * ⚠️ Borra este archivo del servidor cuando el formulario funcione.
 */
require_once __DIR__ . '/includes/config.php';
require_once INC_PATH . '/google.php';

header('X-Robots-Tag: noindex, nofollow');
if (!hash_equals(GAS_SECRET, (string) ($_GET['clave'] ?? ''))) {
    http_response_code(403);
    exit('Acceso denegado. Usa: diagnostico.php?clave=TU_GAS_SECRET');
}

$checks = [];
$add = function (string $nombre, string $estado, string $detalle, string $solucion = '') use (&$checks) {
    $checks[] = compact('nombre', 'estado', 'detalle', 'solucion');
};

// 1. PHP
$add('Versión de PHP', version_compare(PHP_VERSION, '7.4', '>=') ? 'ok' : 'error', PHP_VERSION, 'Se requiere PHP 7.4 o superior (recomendado 8.x). Solicítalo al administrador del servidor.');

// 2. Configuración
if (GAS_WEBAPP_URL === '') {
    $add('URL de Google (GAS_WEBAPP_URL)', 'error', 'Está vacía en includes/config.php',
        'Abre includes/config.php en el SERVIDOR y pega la URL /exec de tu implementación. Es lo más común: al subir el zip se reemplazó tu config.php.');
} elseif (!preg_match('#^https://script\.google\.com/macros/s/[^/]+/exec$#', GAS_WEBAPP_URL)) {
    $add('URL de Google (GAS_WEBAPP_URL)', 'error', GAS_WEBAPP_URL,
        'Debe terminar en /exec (no /dev) y empezar por https://script.google.com/macros/s/');
} else {
    $add('URL de Google (GAS_WEBAPP_URL)', 'ok', substr(GAS_WEBAPP_URL, 0, 60) . '…');
}
$add('Entorno', ENTORNO === 'produccion' ? 'ok' : 'aviso', ENTORNO, "En el servidor público conviene ENTORNO = 'produccion'.");

// 3. Capacidad de salir a internet
$curl = function_exists('curl_init');
$fopen = filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN);
$add('Extensión cURL', $curl ? 'ok' : ($fopen ? 'aviso' : 'error'), $curl ? 'Disponible (' . (curl_version()['version'] ?? '') . ')' : 'No instalada',
    $fopen ? 'Se usará file_get_contents como alternativa. Ideal: pedir al administrador instalar php-curl.' : 'Pide al administrador instalar php-curl (ej. "sudo apt install php-curl" y reiniciar Apache).');
$add('allow_url_fopen', $fopen ? 'ok' : ($curl ? 'aviso' : 'error'), $fopen ? 'Activo' : 'Inactivo');
$add('OpenSSL', extension_loaded('openssl') ? 'ok' : 'error', extension_loaded('openssl') ? 'Disponible' : 'No disponible', 'Se requiere la extensión openssl para conexiones HTTPS.');

$ip = gethostbyname('script.google.com');
$add('DNS script.google.com', $ip !== 'script.google.com' ? 'ok' : 'error', $ip !== 'script.google.com' ? "Resuelve a $ip" : 'No se pudo resolver',
    'El servidor no tiene DNS hacia internet. Consulta con el área de sistemas (firewall / DNS).');

// 4. Prueba GET (doGet) — no crea filas ni envía correos
if (GAS_WEBAPP_URL !== '' && ($curl || $fopen)) {
    $body = ''; $http = 0; $err = '';
    if ($curl) {
        $ch = curl_init(GAS_WEBAPP_URL);
        $o = [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_TIMEOUT => 20, CURLOPT_CONNECTTIMEOUT => 10,
              CURLOPT_SSL_VERIFYPEER => CURL_VERIFY_SSL, CURLOPT_SSL_VERIFYHOST => CURL_VERIFY_SSL ? 2 : 0];
        if (defined('HTTP_PROXY') && HTTP_PROXY !== '') $o[CURLOPT_PROXY] = HTTP_PROXY;
        curl_setopt_array($ch, $o);
        $body = (string) curl_exec($ch); $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); $err = curl_error($ch); curl_close($ch);
    } else {
        $body = (string) @file_get_contents(GAS_WEBAPP_URL, false, stream_context_create(['http' => ['timeout' => 20, 'ignore_errors' => true]]));
        $http = $body !== '' ? 200 : 0; $err = $body === '' ? (error_get_last()['message'] ?? 'sin respuesta') : '';
    }
    $j = json_decode($body, true);
    if ($err !== '') {
        $sol = 'El servidor no logra conectarse a Google. ';
        if (stripos($err, 'SSL') !== false || stripos($err, 'certificate') !== false) {
            $sol .= 'Problema de certificados SSL: pide al administrador actualizar ca-certificates o configurar curl.cainfo en php.ini.';
        } elseif (stripos($err, 'timed out') !== false || stripos($err, 'connect') !== false) {
            $sol .= 'Probablemente el firewall institucional bloquea la salida HTTPS (puerto 443) del servidor. Pide a sistemas permitir salida hacia script.google.com y script.googleusercontent.com, o indicar el proxy y ponerlo en HTTP_PROXY de config.php.';
        }
        $add('Conexión con Google (GET)', 'error', $err, $sol);
    } elseif (is_array($j) && !empty($j['ok'])) {
        $add('Conexión con Google (GET)', 'ok', 'El Web App responde: ' . ($j['servicio'] ?? 'ok'));
    } elseif (stripos($body, 'accounts.google.com') !== false || stripos($body, 'ServiceLogin') !== false || stripos($body, 'signin') !== false) {
        $add('Conexión con Google (GET)', 'error', 'Google pide iniciar sesión (HTTP ' . $http . ')',
            'En Apps Script → Implementar → Gestionar implementaciones → ✏️ → "Quién tiene acceso" debe ser "Cualquier persona". Si solo aparece "Cualquier persona de Universidad de la Amazonia", el administrador de Google Workspace de la universidad debe habilitar compartir Web Apps fuera del dominio (o usa una cuenta Gmail para el script).');
    } else {
        $add('Conexión con Google (GET)', 'error', 'HTTP ' . $http . ' — ' . mb_substr(strip_tags($body), 0, 160),
            'Revisa que la URL sea la de la implementación más reciente y que no esté archivada.');
    }
}

// 5. Prueba POST real (opcional)
$post = null;
if (isset($_GET['probar'])) {
    $post = enviar_google([
        'nombre' => 'Prueba de diagnóstico', 'correo' => $SITE['correo'], 'telefono' => '', 'institucion' => 'Diagnóstico del sitio',
        'pais' => 'Colombia', 'perfil' => 'Otro', 'asunto' => 'Otro', 'mensaje' => 'Mensaje de prueba generado por diagnostico.php el ' . date('Y-m-d H:i:s'),
        'acepto' => 'Sí', 'fecha' => date('Y-m-d H:i:s'), 'ip' => $_SERVER['REMOTE_ADDR'] ?? '', 'origen' => 'diagnostico',
    ]);
    $sol = '';
    if (stripos($post['detalle'], 'No autorizado') !== false) {
        $sol = 'La clave SECRET de Code.gs no coincide con GAS_SECRET de config.php. Corrige una de las dos y, si cambias Code.gs, publica una NUEVA VERSIÓN de la implementación.';
    } elseif (stripos($post['detalle'], 'iniciar sesión') !== false) {
        $sol = 'Publica la implementación con acceso "Cualquier persona".';
    } elseif ($post['detalle'] !== '') {
        $sol = 'Revisa en Apps Script → Ejecuciones el error detallado. Si dice "permisos", ejecuta configurarPrimeraVez de nuevo y autoriza.';
    }
    $add('Envío de prueba (POST)', $post['ok'] ? 'ok' : 'error',
        $post['ok'] ? 'Fila creada en la hoja y correos enviados a ' . $SITE['correo'] : $post['detalle'] . ' [' . $post['metodo'] . ']', $sol);
}

// 6. Carpeta storage
$w = is_dir(STORAGE_PATH) && is_writable(STORAGE_PATH);
$testW = $w && @file_put_contents(STORAGE_PATH . '/.prueba', 'ok') !== false;
if ($testW) @unlink(STORAGE_PATH . '/.prueba');
$add('Carpeta storage/ escribible', $testW ? 'ok' : 'error', $testW ? STORAGE_PATH : 'Sin permiso de escritura: ' . STORAGE_PATH,
    'Da permisos de escritura a la carpeta storage (por FTP: clic derecho → Permisos → 775; o por consola: chmod 775 storage y chown www-data storage). Sin esto no se guarda el respaldo CSV.');

// 7. Sesiones (necesarias para el token de seguridad)
$sp = session_save_path() ?: sys_get_temp_dir();
$add('Sesiones PHP', is_writable($sp) ? 'ok' : 'error', 'Ruta: ' . $sp, 'La carpeta de sesiones no es escribible: el formulario mostraría "La sesión expiró". Avisar al administrador.');

// 8. mail()
$add('Función mail() (respaldo)', function_exists('mail') ? 'aviso' : 'aviso', function_exists('mail') ? 'Existe (no garantiza que el servidor tenga correo configurado)' : 'Deshabilitada', 'Es solo un respaldo adicional; no es obligatoria.');

$log = is_file(STORAGE_PATH . '/errores.log') ? array_slice(file(STORAGE_PATH . '/errores.log'), -15) : [];
$errores = count(array_filter($checks, fn($c) => $c['estado'] === 'error'));
?><!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex">
<title>Diagnóstico del formulario</title>
<style>
body{font-family:system-ui,Segoe UI,Arial,sans-serif;background:#F4FAF4;color:#13302A;margin:0;padding:24px}
.w{max-width:900px;margin:auto}h1{color:#048C8C;margin:.2em 0}
.sum{padding:14px 18px;border-radius:12px;font-weight:700;margin:16px 0}.sum.ok{background:#E4F2E5;color:#3c5a07}.sum.bad{background:#FDE8E8;color:#9b1c1c}
table{width:100%;border-collapse:collapse;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.06)}
td,th{padding:12px 14px;border-bottom:1px solid #E1EDE3;vertical-align:top;text-align:left;font-size:14px}th{background:#048C8C;color:#fff}
.b{display:inline-block;padding:3px 10px;border-radius:99px;font-size:12px;font-weight:800;color:#fff}.b.ok{background:#96A61C}.b.error{background:#D64545}.b.aviso{background:#F28E13}
.sol{margin-top:6px;padding:8px 10px;background:#FFF6E9;border-left:3px solid #F28E13;border-radius:6px;font-size:13px}
.btn{display:inline-block;margin:6px 6px 0 0;padding:10px 16px;border-radius:99px;background:#048C8C;color:#fff;text-decoration:none;font-weight:700}
.btn.o{background:#F28E13}pre{background:#13302A;color:#E4F2E5;padding:14px;border-radius:10px;overflow:auto;font-size:12px}
</style></head><body><div class="w">
<h1>Diagnóstico del formulario de contacto</h1>
<p>Servidor: <b><?= e($_SERVER['HTTP_HOST'] ?? '') ?></b> · <?= date('Y-m-d H:i:s') ?></p>
<div class="sum <?= $errores ? 'bad' : 'ok' ?>"><?= $errores ? "Se encontraron $errores problema(s). Revisa las filas en rojo y su solución." : 'Todo en orden. Si aún no has hecho el envío de prueba, haz clic en el botón naranja.' ?></div>
<a class="btn" href="?clave=<?= e(urlencode($_GET['clave'])) ?>">Repetir diagnóstico</a>
<a class="btn o" href="?clave=<?= e(urlencode($_GET['clave'])) ?>&amp;probar=1">Enviar mensaje de prueba real</a>
<p style="font-size:13px;color:#45605A">El envío de prueba crea una fila en la hoja y envía 2 correos a <?= e($SITE['correo']) ?>.</p>
<table><tr><th>Comprobación</th><th>Estado</th><th>Detalle</th></tr>
<?php foreach ($checks as $c): ?>
<tr><td><b><?= e($c['nombre']) ?></b></td><td><span class="b <?= e($c['estado']) ?>"><?= $c['estado'] === 'ok' ? 'OK' : ($c['estado'] === 'error' ? 'ERROR' : 'AVISO') ?></span></td>
<td><?= e($c['detalle']) ?><?php if ($c['estado'] !== 'ok' && $c['solucion']): ?><div class="sol"><b>Solución:</b> <?= e($c['solucion']) ?></div><?php endif; ?></td></tr>
<?php endforeach; ?>
</table>
<?php if ($post && !$post['ok'] && $post['respuesta']): ?><h3>Respuesta recibida de Google</h3><pre><?= e($post['respuesta']) ?></pre><?php endif; ?>
<?php if ($log): ?><h3>Últimos errores registrados (storage/errores.log)</h3><pre><?= e(implode('', $log)) ?></pre><?php endif; ?>
<p style="margin-top:24px;color:#9b1c1c;font-weight:700">⚠️ Cuando el formulario funcione, borra diagnostico.php del servidor.</p>
</div></body></html>

<?php
/**
 * Envío de datos al Web App de Google Apps Script.
 * Usa cURL y, si el servidor no lo tiene, file_get_contents (allow_url_fopen).
 * Devuelve ['ok' => bool, 'detalle' => string, 'metodo' => string, 'http' => int, 'respuesta' => string]
 */
function enviar_google(array $datos, int $timeout = 25): array
{
    $r = ['ok' => false, 'detalle' => '', 'metodo' => '', 'http' => 0, 'respuesta' => ''];
    if (GAS_WEBAPP_URL === '') {
        $r['detalle'] = 'GAS_WEBAPP_URL está vacía en includes/config.php';
        return $r;
    }
    if (!preg_match('#^https://script\.google\.com/macros/s/[^/]+/exec$#', GAS_WEBAPP_URL)) {
        $r['detalle'] = 'La URL no tiene el formato esperado https://script.google.com/macros/s/.../exec';
    }
    $payload = json_encode(['secret' => GAS_SECRET] + $datos, JSON_UNESCAPED_UNICODE);

    if (function_exists('curl_init')) {
        $r['metodo'] = 'curl';
        $ch = curl_init(GAS_WEBAPP_URL);
        $opts = [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,   // Apps Script responde con una redirección 302
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_SSL_VERIFYPEER => CURL_VERIFY_SSL,
            CURLOPT_SSL_VERIFYHOST => CURL_VERIFY_SSL ? 2 : 0,
            CURLOPT_USERAGENT      => 'CongresoPN-Web/1.0',
        ];
        if (defined('HTTP_PROXY') && HTTP_PROXY !== '') {
            $opts[CURLOPT_PROXY] = HTTP_PROXY;   // Algunos servidores institucionales salen por proxy
        }
        curl_setopt_array($ch, $opts);
        $resp = curl_exec($ch);
        $r['http'] = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err !== '') {
            $r['detalle'] = 'cURL: ' . $err;
            return $r;
        }
    } elseif (filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN)) {
        $r['metodo'] = 'file_get_contents';
        $ctx = stream_context_create([
            'http' => ['method' => 'POST', 'header' => "Content-Type: application/json\r\n", 'content' => $payload,
                       'timeout' => $timeout, 'follow_location' => 1, 'max_redirects' => 5, 'ignore_errors' => true]
                      + ((defined('HTTP_PROXY') && HTTP_PROXY !== '') ? ['proxy' => str_replace('http://', 'tcp://', HTTP_PROXY), 'request_fulluri' => true] : []),
            'ssl'  => ['verify_peer' => CURL_VERIFY_SSL, 'verify_peer_name' => CURL_VERIFY_SSL],
        ]);
        $resp = @file_get_contents(GAS_WEBAPP_URL, false, $ctx);
        if ($resp === false) {
            $e = error_get_last();
            $r['detalle'] = 'Sin conexión con Google: ' . ($e['message'] ?? 'error desconocido');
            return $r;
        }
        foreach (array_reverse($http_response_header ?? []) as $h) {
            if (preg_match('#^HTTP/\S+\s+(\d{3})#', $h, $m)) { $r['http'] = (int) $m[1]; break; }
        }
    } else {
        $r['detalle'] = 'El servidor no tiene la extensión cURL ni allow_url_fopen activos';
        return $r;
    }

    $r['respuesta'] = mb_substr((string) $resp, 0, 400);
    $json = json_decode((string) $resp, true);
    if (is_array($json) && !empty($json['ok'])) {
        $r['ok'] = true;
        $r['detalle'] = '';
        return $r;
    }
    if (is_array($json) && !empty($json['error'])) {
        $r['detalle'] = 'Google respondió: ' . $json['error'];
    } elseif (stripos((string) $resp, 'accounts.google.com') !== false || stripos((string) $resp, 'ServiceLogin') !== false) {
        $r['detalle'] = 'Google pide iniciar sesión: la implementación NO está publicada para "Cualquier persona"';
    } elseif ($r['http'] === 404) {
        $r['detalle'] = 'HTTP 404: la URL del Web App no existe o la implementación fue archivada';
    } else {
        $r['detalle'] = 'Respuesta inesperada (HTTP ' . $r['http'] . ')';
    }
    return $r;
}

/** Guarda una línea en storage/errores.log (y en el log de PHP del servidor). */
function registrar_error(string $mensaje): void
{
    $linea = '[' . date('Y-m-d H:i:s') . '] ' . $mensaje;
    error_log('[Congreso contacto] ' . $mensaje);
    if (is_dir(STORAGE_PATH) && is_writable(STORAGE_PATH)) {
        @file_put_contents(STORAGE_PATH . '/errores.log', $linea . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

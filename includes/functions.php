<?php
/**
 * Funciones utilitarias reutilizables en todo el sitio.
 */

/** Escapa texto para HTML. */
function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Construye una URL interna a partir de BASE_URL. */
function url(string $ruta = ''): string
{
    return BASE_URL . ltrim($ruta, '/');
}

/** Ruta pública de un recurso dentro de /assets. */
function asset(string $ruta): string
{
    $archivo = ROOT_PATH . '/assets/' . ltrim($ruta, '/');
    $version = is_file($archivo) ? '?v=' . filemtime($archivo) : '';
    return BASE_URL . 'assets/' . ltrim($ruta, '/') . $version;
}

/** ¿Existe el archivo dentro de /assets? */
function asset_existe(string $ruta): bool
{
    return $ruta !== '' && is_file(ROOT_PATH . '/assets/' . ltrim($ruta, '/'));
}

/** Carga un archivo JSON de /data y lo cachea en memoria. */
function data(string $nombre): array
{
    static $cache = [];
    if (isset($cache[$nombre])) {
        return $cache[$nombre];
    }
    $archivo = DATA_PATH . '/' . $nombre . '.json';
    if (!is_file($archivo)) {
        return $cache[$nombre] = [];
    }
    $json = json_decode((string) file_get_contents($archivo), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        if (ENTORNO === 'local') {
            trigger_error("JSON inválido en data/{$nombre}.json: " . json_last_error_msg(), E_USER_WARNING);
        }
        $json = [];
    }
    return $cache[$nombre] = $json;
}

/** Fecha en español: 2027-05-11 → 11 de mayo de 2027 */
function fecha_es(string $fecha, bool $corta = false): string
{
    $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    $ts = strtotime($fecha);
    if (!$ts) {
        return e($fecha);
    }
    $mes = $meses[(int) date('n', $ts) - 1];
    if ($corta) {
        return date('j', $ts) . ' ' . mb_substr($mes, 0, 3) . ' ' . date('Y', $ts);
    }
    return date('j', $ts) . ' de ' . $mes . ' de ' . date('Y', $ts);
}

/** Recorta texto plano a N caracteres sin cortar palabras. */
function resumen(string $texto, int $max = 160): string
{
    $texto = trim(strip_tags($texto));
    if (mb_strlen($texto) <= $max) {
        return $texto;
    }
    $corte = mb_substr($texto, 0, $max);
    $corte = mb_substr($corte, 0, (int) mb_strrpos($corte, ' '));
    return rtrim($corte, ' ,.;:') . '…';
}

/** Iniciales para avatares de respaldo. */
function iniciales(string $nombre): string
{
    $limpio = preg_replace('/^(Dr\.|Dra\.|MSc\.|PhD\.?)\s*/iu', '', $nombre);
    $partes = preg_split('/\s+/', trim($limpio));
    $ini = mb_substr($partes[0] ?? '', 0, 1) . mb_substr($partes[count($partes) > 2 ? 2 : 1] ?? '', 0, 1);
    return mb_strtoupper($ini);
}

/**
 * Imagen con respaldo: si el archivo no existe muestra un
 * contenedor "pendiente" con el texto indicado (requisito 8).
 */
function imagen(string $ruta, string $alt, string $clase = '', string $placeholder = 'Imagen pendiente', array $attrs = []): string
{
    if (asset_existe($ruta)) {
        $extra = '';
        foreach ($attrs as $k => $v) {
            $extra .= ' ' . e($k) . '="' . e($v) . '"';
        }
        return '<img src="' . e(asset($ruta)) . '" alt="' . e($alt) . '" class="' . e($clase) . '" loading="lazy" decoding="async"' . $extra . '>';
    }
    return '<div class="img-pendiente ' . e($clase) . '" role="img" aria-label="' . e($alt) . '">'
        . icon('image') . '<span>' . e($placeholder) . '</span></div>';
}

/** Token CSRF de sesión para formularios. */
function csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/** Enlace externo seguro o null si está pendiente. */
function enlace(?string $url): ?string
{
    $url = trim((string) $url);
    return $url === '' ? null : $url;
}

/** Nombre legible de los grupos de personas. */
function grupos_personas(): array
{
    return [
        'internacional' => ['label' => 'Ponentes internacionales', 'corto' => 'Internacionales', 'tipo' => 'conferencista'],
        'nacional'      => ['label' => 'Ponentes nacionales',      'corto' => 'Nacionales',      'tipo' => 'conferencista'],
        'sequiamaz'     => ['label' => 'Ponentes SEQUIAMAZ',       'corto' => 'SEQUIAMAZ',       'tipo' => 'conferencista'],
        'organizador'   => ['label' => 'Comité Organizador',       'corto' => 'C. Organizador',  'tipo' => 'comite'],
        'editorial'     => ['label' => 'Comité Editorial',         'corto' => 'C. Editorial',    'tipo' => 'comite'],
        'cientifico'    => ['label' => 'Comité Científico',        'corto' => 'C. Científico',   'tipo' => 'comite'],
    ];
}

/** Personas filtradas por grupo(s). */
function personas(array $grupos = []): array
{
    $todas = data('personas');
    if (!$grupos) {
        return $todas;
    }
    return array_values(array_filter($todas, fn($p) => in_array($p['grupo'] ?? '', $grupos, true)));
}

/** Busca una persona por id. */
function persona(string $id): ?array
{
    foreach (data('personas') as $p) {
        if (($p['id'] ?? '') === $id) {
            return $p;
        }
    }
    return null;
}

/** Noticias ordenadas de la más reciente a la más antigua. */
function noticias(int $limite = 0): array
{
    $lista = data('noticias');
    usort($lista, fn($a, $b) => strcmp($b['fecha'] ?? '', $a['fecha'] ?? ''));
    return $limite > 0 ? array_slice($lista, 0, $limite) : $lista;
}

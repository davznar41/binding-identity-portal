<?php

// Enrutador del servidor local de PHP; el directorio público de la aplicación es public/.
$public = realpath(__DIR__.'/../public');
$base = rtrim(getenv('DOCS_BASE') ?: '', '/');
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
if ($base !== '' && $path !== $base && ! str_starts_with($path, $base.'/')) {
    http_response_code(404);

    return true;
}
$relative = ltrim(substr($path, strlen($base)), '/');
$file = realpath($public.DIRECTORY_SEPARATOR.$relative);
if ($file && str_starts_with($file, $public.DIRECTORY_SEPARATOR) && is_file($file) && pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
    $types = ['css' => 'text/css', 'js' => 'text/javascript', 'woff2' => 'font/woff2', 'webp' => 'image/webp',
        'svg' => 'image/svg+xml', 'json' => 'application/json', 'map' => 'application/json', 'txt' => 'text/plain'];
    header('Content-Type: '.($types[pathinfo($file, PATHINFO_EXTENSION)] ?? 'application/octet-stream'));
    header('Content-Length: '.filesize($file));
    if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') {
        readfile($file);
    }

    return true;
}
$_SERVER['SCRIPT_NAME'] = $base.'/index.php';
$_SERVER['PHP_SELF'] = $base.'/index.php';
$_SERVER['SCRIPT_FILENAME'] = $public.'/index.php';
require $public.'/index.php';

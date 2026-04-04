<?php

if (PHP_SAPI === 'cli-server') {
    $requestedPath = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if (is_file($requestedPath)) {
        return false;
    }
}

require_once dirname(__DIR__) . '/vendor/autoload.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/') ?: '/';

$routes = [
    '/' => 'adm.php',
    '/adm' => 'adm.php',
    '/register' => 'register-user.php',
    '/edit' => 'edit.php',
    '/update' => 'update.php',
    '/delete' => 'delete.php',
    '/download' => 'download-pdf.php',
    '/financial' => 'financial.php',
];

if (array_key_exists($uri, $routes)) {
    require dirname(__DIR__) . '/' . $routes[$uri];
    return;
}

http_response_code(404);
echo '<h1>404 - Pagina nao encontrada</h1>';

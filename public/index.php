<?php

use App\Controller\UserController;

if (PHP_SAPI === 'cli-server') {
    $requestedPath = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if (is_file($requestedPath)) {
        return false;
    }
}

require_once dirname(__DIR__) . '/vendor/autoload.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

$userController = new UserController();

if ($uri === '/register' && $method === 'GET') {
    $userController->create();
    return;
}

if ($uri === '/register' && $method === 'POST') {
    $userController->store();
    return;
}

if ($uri === '/edit' && $method === 'GET') {
    $userController->edit();
    return;
}

if ($uri === '/update' && $method === 'POST') {
    $userController->update();
    return;
}

$routes = [
    '/' => 'adm.php',
    '/adm' => 'adm.php',
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

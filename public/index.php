<?php

session_start();

if (PHP_SAPI === 'cli-server') {
    $requestedPath = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($requestedPath)) {
        return false;
    }
}

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Controller\AuthController;
use App\Controller\DashboardController;
use App\Controller\FinancialController;
use App\Controller\UserController;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];
$isAuthenticated = isset($_SESSION['admin']);

$routes = [
    ['GET',  '/',          AuthController::class,      'login',       false],
    ['GET',  '/login',     AuthController::class,      'login',       false],
    ['POST', '/login',     AuthController::class,      'authenticate',false],
    ['GET',  '/logout',    AuthController::class,      'logout',      false],
    ['GET',  '/adm',       DashboardController::class, 'index',       true],
    ['GET',  '/register',  UserController::class,      'create',      true],
    ['POST', '/register',  UserController::class,      'store',       true],
    ['GET',  '/edit',      UserController::class,      'edit',        true],
    ['POST', '/update',    UserController::class,      'update',      true],
    ['GET',  '/delete',    UserController::class,      'destroy',     true],
    ['GET',  '/download',  UserController::class,      'downloadPdf', true],
    ['GET',  '/financial', FinancialController::class, 'index',       true],
];

foreach ($routes as [$routeMethod, $routeUri, $controllerClass, $action, $requiresAuth]) {
    if ($method !== $routeMethod || $uri !== $routeUri) {
        continue;
    }

    if (!$requiresAuth && $isAuthenticated && ($routeUri === '/' || $routeUri === '/login')) {
        header('Location: /adm');
        return;
    }

    if ($requiresAuth && !$isAuthenticated) {
        header('Location: /login');
        return;
    }

    (new $controllerClass())->$action();
    return;
}

http_response_code(404);
echo '<h1>404 - Pagina nao encontrada</h1>';

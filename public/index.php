<?php

session_start();

use App\Controller\UserController;
use App\Controller\AuthController;
use App\Controller\FinancialController;
use App\Controller\DashboardController;

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
$authController = new AuthController();
$financialController = new FinancialController();
$dashboardController = new DashboardController();

$isAuthenticated = isset($_SESSION['admin']);

if (($uri === '/' || $uri === '/login') && $method === 'GET') {
    if ($isAuthenticated) {
        header('Location: /adm');
        return;
    }

    $authController->login();
    return;
}

if ($uri === '/login' && $method === 'POST') {
    $authController->authenticate();
    return;
}

if ($uri === '/logout' && $method === 'GET') {
    $authController->logout();
    return;
}

if (!$isAuthenticated) {
    header('Location: /login');
    return;
}

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

if ($uri === '/download' && $method === 'GET') {
    $userController->downloadPdf();
    return;
}

if ($uri === '/delete' && $method === 'GET') {
    $userId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$userId) {
        http_response_code(404);
        echo '<h1>404 - Usuario nao encontrado</h1>';
        return;
    }

    $userController->destroy();
    return;
}

if ($uri === '/financial' && $method === 'GET') {
    $financialController->index();
    return;
}

if ($uri === '/adm' && $method === 'GET') {
    $dashboardController->index();
    return;
}

http_response_code(404);
echo '<h1>404 - Pagina nao encontrada</h1>';

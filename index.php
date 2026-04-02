<?php
 
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$uri = rtrim($uri, '/') ?: '/';
 
$routes = [
    '/'          => 'adm.php',
    '/adm'       => 'adm.php',
    '/register'  => 'register-user.php',
    '/edit'      => 'edit.php',
    '/update'    => 'update.php',
    '/delete'    => 'delete.php',
    '/download'  => 'download-pdf.php',
];
 
if (array_key_exists($uri, $routes)) {
    require $routes[$uri];
} else {
    http_response_code(404);
    echo "<h1>404 – Página não encontrada</h1>";
}
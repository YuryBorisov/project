<?php

require_once '../vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $route) {
    $route->addRoute('POST', '/get', 'get');
    $route->addRoute('GET', '/', 'home');
    $route->addRoute('GET', '/download/{filename}', 'download');
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo 'NOT_FOUND';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo '405 Method Not Allowed ';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        switch ($routeInfo[1]){
            case 'home':
                include_once '../app/Views/index.html';
                break;
            case 'get':
                $controller = new \App\Controllers\Controller();
                echo $controller->get($_POST['type_service'], $_POST['id']);
                break;
            case 'download':
                $controller = new \App\Controllers\Controller();
                echo $controller->downloadZIP($routeInfo[2]['filename']);
                break;
        }
        break;
}
<?php
require_once '../vendor/autoload.php';

session_start();

function h($value)
{
    return htmlspecialchars($value);
}

$builder = new \DI\ContainerBuilder();
$builder->useAnnotations(true);
$builder->addDefinitions('../config/config.php');

$router = new \Vtk13\Mvc\Handlers\ControllerRouter($builder->build(), 'Vtk13\\Cc\\Controller\\', '/', 'index');
$response = $router->handle(\Vtk13\Mvc\Http\Request::createFromGlobals());

if (!headers_sent()) {
    header($response->getStatusLine());
    foreach ($response->getHeaders() as $name => $value) {
        header("{$name}: {$value}");
    }
    echo $response->getBody();
}

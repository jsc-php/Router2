<?php

require __DIR__ . '/../vendor/autoload.php';

$router_config = new \JscPhp\Router2\RouterConfig();
$router_config->addPath(__DIR__ . '/classes');

$router = new \JscPhp\Router2\Router($router_config);

$router->getRoute('/', 'get');
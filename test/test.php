<?php

require __DIR__ . '/../vendor/autoload.php';

$router_config = new \JscPhp\Router2\RouterConfig();
$router_config->addPath(__DIR__ . '/classes');
//$router_config->addPath('/var/www/app-server/App/Controllers');

$router = new \JscPhp\Router2\Router($router_config);

var_dump($router);

//var_dump($router->getRoute('/', 'get'));
<?php

require __DIR__ . '/../vendor/autoload.php';

$router_config = new \JscPhp\Router2\RouterConfig();
//$router_config->addPath(__DIR__ . '/classes');
$router_config->addPath('/var/www/app-server/src', '/Controllers');
$router_config->setDev(true);

$router = new \JscPhp\Router2\Router($router_config);


/*if ($router->getRoute('/sample/view/1234/abcd', 'get')) {
    $router->go();
}*/


var_dump($router);
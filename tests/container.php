<?php

// Container for functional test

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;
use Slim\Container;

session_start();
require_once __DIR__ . '/../vendor/autoload.php';

$container = new Container([
    App::class => function (ContainerInterface $c) {
        $app = new App($c);
//        routes and middlewares here
        require_once __DIR__ . '/../app/routes.php';
        return $app;
    }
]);

require_once __DIR__ . '/../app/container.php';

// for tests a test config "tests/_data/config.json" is used.
$container['config'] = function (Container $c) {
    $config = json_decode(file_get_contents(__DIR__ . '/_data/config/config.json'), true);
    if ($config === null) {
        throw new Exception(json_last_error_msg());
    }
    return $config;
};

$app = $container->get('Slim\App');
$app->add($container['csrf']);

return $container;



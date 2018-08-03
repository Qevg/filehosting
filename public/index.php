<?php

error_reporting(-1);

ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.gc_maxlifetime', '43200'); //12h

$config['settings'] = [
    'displayErrorDetails' => boolval(ini_get("display_errors"))
];

session_start();
require_once __DIR__ . '/../vendor/autoload.php';

$container = new \Slim\Container($config);
require_once __DIR__ . '/../app/container.php';

$app = new \Slim\App($container);
$app->add($container['csrf']);
require_once __DIR__ . '/../app/routes.php';

$app->run();

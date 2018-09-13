<?php

use Slim\Container;

$container['config'] = function (Container $c) {
    $config = json_decode(file_get_contents(__DIR__ . '/../config/config.json'), true);
    if ($config === null) {
        throw new Exception(json_last_error_msg());
    }
    return $config;
};

$container['db'] = function (Container $c) {
    $db = new PDO(
        sprintf(
            "pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
            $c['config']['db']['host'],
            $c['config']['db']['port'],
            $c['config']['db']['dbname'],
            $c['config']['db']['user'],
            $c['config']['db']['password']
        )
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
};

$container['redis'] = function (Container $c) {
    $redis = new Redis();
    $redis->connect($c['config']['redis']['host'], $c['config']['redis']['port']);
    return $redis;
};

$container['sphinx'] = function (Container $c) {
    $db = new PDO("mysql:host={$c['config']['sphinx']['host']};port={$c['config']['sphinx']['port']}");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
};

$container['twig'] = function (Container $c) {
    $twig = new \Slim\Views\Twig(__DIR__ . '/../templates', array('strict_variables' => true));

    $twig->getEnvironment()->addExtension(new \Filehosting\Twig\Extensions\JsonDecodeExtension());

    $twig->getEnvironment()->addExtension(new \Filehosting\Twig\Extensions\FormatSizeExtension());

    $twig->getEnvironment()->addExtension(new \Filehosting\Middleware\CsrfMiddleware($c['csrf']));

    return $twig;
};

$container['csrf'] = function (Container $c) {
    $csrf = new Slim\Csrf\Guard();
    $csrf->setPersistentTokenMode(true);

    // https://github.com/slimphp/Slim-Csrf#handling-validation-failure
    $csrf->setFailureCallable(function ($request, $response, $next) {
        throw new \Filehosting\Exception\CsrfException('Failed CSRF check!');
    });
    return $csrf;
};

$container['FileMapper'] = function (Container $c) {
    return new \Filehosting\Database\FileMapper($c['db']);
};

$container['PathHelper'] = function (Container $c) {
    return new \Filehosting\Helper\PathHelper();
};

$container['FileHelper'] = function (Container $c) {
    return new \Filehosting\Helper\FileHelper($c);
};

$container['ThumbnailHelper'] = function (Container $c) {
    return new \Filehosting\Helper\ThumbnailHelper($c['PathHelper'], $c['FileSystemHelper']);
};

$container['MediaInfoHelper'] = function (Container $c) {
    return new \Filehosting\Helper\MediaInfoHelper(new getID3());
};

$container['SearchMapper'] = function (Container $c) {
    return new \Filehosting\Database\SearchMapper($c['sphinx']);
};

$container['UserMapper'] = function (Container $c) {
    return new \Filehosting\Database\UserMapper($c['db']);
};

$container['CommentMapper'] = function (Container $c) {
    return new \Filehosting\Database\CommentMapper($c['db']);
};

$container['UserValidator'] = function (Container $c) {
    return new Filehosting\Validator\UserValidator($c['UserMapper']);
};

$container['FileValidator'] = function (Container $c) {
    return new \Filehosting\Validator\FileValidator(intval($c['config']['maxFileSize']));
};

$container['CommentValidator'] = function (Container $c) {
    return new \Filehosting\Validator\CommentValidator();
};

$container['AuthHelper'] = function (Container $c) {
    return new \Filehosting\Helper\AuthHelper($c['UserMapper'], $c['UserValidator'], $c['FileMapper'], $c['CookieHelper']);
};

$container['CommentHelper'] = function (Container $c) {
    return new \Filehosting\Helper\CommentHelper($c['CommentMapper']);
};


$container['FileSystemHelper'] = function (Container $c) {
    return new \Filehosting\Helper\FileSystemHelper();
};

$container['CookieHelper'] = function (Container $c) {
    return new \Filehosting\Helper\CookieHelper();
};

$container['notFoundHandler'] = function (Container $c) {
    return function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($c) {
        $response = $response->withStatus(404)->withHeader('Content-Type', 'text/html');
        return $c->get('twig')->render(
            $response,
            '/page/error.twig',
            [
                'title' => "Page Not Found",
                'caption' => "Page Not Found",
                'message' => "The page you are looking for could not be found",
                'displayErrors' => "off",
                'debugInfo' => null
            ]
        );
    };
};

$container['errorHandler'] = function (Container $c) {
    return function (\Slim\Http\Request $request, \Slim\Http\Response $response, Exception $exception) use ($c) {
        error_log($exception->__toString());
        $response = $response->withStatus(500)->withHeader('Content-Type', 'text/html');
        return $c->get('twig')->render(
            $response,
            '/page/error.twig',
            [
                'title' => "Internal Server Error",
                'caption' => "Internal Server Error",
                'message' => "We have technical problems. Refresh the page after some time",
                'displayErrors' => ini_get("display_errors"),
                'debugInfo' => $exception->__toString()
            ]
        );
    };
};

$container['notAllowedHandler'] = function ($c) {
    return function (\Slim\Http\Request $request, \Slim\Http\Response $response, array $methods) use ($c) {
        $response = $response->withStatus(405)->withHeader('Allow', implode(', ', $methods))->withHeader('Content-Type', 'text/html');
        return $c->get('twig')->render(
            $response,
            '/page/error.twig',
            [
                'title' => "Method Not Allowed",
                'caption' => "Method Not Allowed",
                'message' => 'Method must be one of: ' . implode(', ', $methods),
                'displayErrors' => 'off',
                'debugInfo' => null
            ]
        );
    };
};

$container['phpErrorHandler'] = function (Container $c) {
    return function (\Slim\Http\Request $request, \Slim\Http\Response $response, Error $error) use ($c) {
        error_log($error->__toString());
        $response = $response->withStatus(500)->withHeader('Content-Type', 'text/html');
        return $c->get('twig')->render(
            $response,
            '/page/error.twig',
            [
                'title' => "Internal Server Error",
                'caption' => "Internal Server Error",
                'message' => "We have technical problems. Refresh the page after some time",
                'displayErrors' => ini_get("display_errors"),
                'debugInfo' => $error->__toString()
            ]
        );
    };
};

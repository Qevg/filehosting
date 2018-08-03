<?php

namespace Filehosting\Controller;

use Filehosting\Helper\AuthHelper;
use PHPUnit\Runner\Exception;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

/**
 * Class AuthController
 * @package Filehosting\Controller
 */
class AuthController
{
    /**
     * @var Twig $twig
     */
    private $twig;

    /**
     * @var AuthHelper $authHelper
     */
    private $authHelper;

    /**
     * @var $errors
     */
    private $errors;

    /**
     * AuthController constructor.
     *
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        $this->twig = $c->get('twig');
        $this->authHelper = $c->get('AuthHelper');
    }

    /**
     * Register
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return string|Response
     */
    public function register(Request $request, Response $response, array $args)
    {
        if ($this->authHelper->isAuth()) {
            return $response->withRedirect('/', 302);
        }

        if ($request->isPost()) {
            $errors = $this->authHelper->register($request->getParams());
            if (empty($errors)) {
                return json_encode(array('status' => 'success'));
            } else {
                return json_encode(array('status' => 'error', 'errors' => $errors));
            }
        }
        return $response = $this->twig->render($response, '/page/register.twig');
    }

    /**
     * Log in
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return string|Response
     */
    public function logIn(Request $request, Response $response, array $args)
    {
        if ($this->authHelper->isAuth()) {
            return $response->withRedirect('/', 302);
        }

        if ($request->isPost()) {
            $errors = $this->authHelper->logIn($request->getParams());
            if (empty($errors)) {
                return json_encode(array('status' => 'success'));
            } else {
                return json_encode(array('status' => 'error', 'errors' => $errors));
            }
        }
        return $response = $this->twig->render($response, '/page/login.twig');
    }

    /**
     * Log out
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     */
    public function logOut(Request $request, Response $response, array $args): Response
    {
        if ($this->authHelper->isAuth()) {
            $this->authHelper->logOut();
        }
        return $response->withRedirect('/', 302);
    }
}

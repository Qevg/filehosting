<?php

namespace Filehosting\Middleware;

use Slim\Csrf\Guard;

/**
 * Class CsrfMiddleware
 * @package Filehosting\Middleware
 */
class CsrfMiddleware extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * @var Guard $csrf
     */
    protected $csrf;

    /**
     * CsrfMiddleware constructor.
     *
     * @param Guard $csrf
     */
    public function __construct(Guard $csrf)
    {
        $this->csrf = $csrf;
    }

    /**
     * Implements method in interface 'Twig_Extension_GlobalsInterface'
     *
     * @return array
     */
    public function getGlobals(): array
    {
        $csrfNameKey = $this->csrf->getTokenNameKey();
        $csrfValueKey = $this->csrf->getTokenValueKey();
        $csrfName = $this->csrf->getTokenName();
        $csrfValue = $this->csrf->getTokenValue();

        return [
            'csrf'   => [
                'keys' => [
                    'name'  => $csrfNameKey,
                    'value' => $csrfValueKey
                ],
                'name'  => $csrfName,
                'value' => $csrfValue
            ]
        ];
    }
}

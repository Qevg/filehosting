<?php

namespace Filehosting\Helper;

/**
 * Class CookieHelper
 * @package Filehosting\Helper
 */
class CookieHelper
{
    const AUTH_TOKEN_NAME = 'auth';
    const AUTH_TOKEN_LIFETIME = '1 year';

    /**
     * Sets cookie to client
     *
     * @param string $name
     * @param string $value
     * @param string $time
     */
    public function setCookieToClient(string $name, string $value, string $time): void
    {
        setcookie($name, $value, strtotime($time), '/', null, false, true);
    }

    /**
     * Deletes cookie to client
     *
     * @param string $name
     * @param string $time
     */
    public function deleteCookieToClient(string $name, string $time): void
    {
        setcookie($name, "", strtotime("-{$time}"), '/', null, false, true);
    }
}

<?php

namespace Filehosting\Twig\Extensions;

/**
 * Class JsonDecodeExtension
 * @package Filehosting\Twig\Extensions
 */
class JsonDecodeExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('json_decode', array($this, 'jsonDecode'))
        );
    }

    /**
     * Json decode
     *
     * @param string $json
     *
     * @return array
     * @throws \Twig_Error
     */
    public function jsonDecode(string $json): array
    {
        $array = json_decode($json, true);
        if ($array === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Twig_Error(json_last_error_msg());
        }
        return $array;
    }
}

<?php

namespace Filehosting\Twig\Extensions;

/**
 * Class FormatSizeExtension
 * @package Filehosting\Twig\Extensions
 */
class FormatSizeExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('formatSize', array($this, 'formatSize'))
        );
    }

    /**
     * Formats bytes to GB, MB, KB
     *
     * @param int $size bytes
     *
     * @return string
     */
    public function formatSize(int $size): string
    {
        switch (true) {
            case ($size >= pow(1024, 3)):
                return round($size / pow(1024, 3), 2, PHP_ROUND_HALF_DOWN) . 'GB';
            case ($size >= pow(1024, 2)):
                return round($size / pow(1024, 2), 1, PHP_ROUND_HALF_DOWN) . 'MB';
            case ($size >= 1024):
                return round($size / 1024, 0, PHP_ROUND_HALF_DOWN) . 'KB';
            default:
                return $size . 'B';
        }
    }
}

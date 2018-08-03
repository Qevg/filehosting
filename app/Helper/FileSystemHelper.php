<?php

namespace Filehosting\Helper;

use Filehosting\Exception\FileSystemException;

/**
 * Class FileSystemHelper
 * @package Filehosting\Helper
 */
class FileSystemHelper
{
    /**
     * Chmod recursive
     * If $path is dir, it changes the directory mode and parent directories until it reaches $pathtoStop
     *
     * @param string $path
     * @param string $pathToStop
     *
     * @throws FileSystemException
     */
    public function chmodR(string $path, string $pathToStop = ""): void
    {
        if (!file_exists($path)) {
            throw new FileSystemException("Failed file not exists {$path}");
        }

        if (is_file($path)) {
            chmod($path, 0644);
        } elseif (is_dir($path)) {
            if (empty($pathToStop)) {
                throw new FileSystemException("Not passed the second argument to function chmodR()");
            }
            while ($path !== $pathToStop) {
                chmod($path, 0755);
                $path = dirname($path);
            }
        }
    }

    /**
     * Mkdir recursive
     *
     * @param string $path
     *
     * @throws FileSystemException
     */
    public function mkdirR(string $path): void
    {
        if (!mkdir($path, 0755, true)) {
            throw new FileSystemException("Cannot create directory {$path}");
        }
    }

    /**
     * Deletes a file
     *
     * @param string $path
     *
     * @throws FileSystemException
     */
    public function unlink(string $path): void
    {
        if (!file_exists($path)) {
            throw new FileSystemException("Failed file not exists {$path}");
        }

        if (!unlink($path)) {
            throw new FileSystemException("Cannot remove file {$path}");
        }
    }
}

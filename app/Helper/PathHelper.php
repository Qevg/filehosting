<?php

namespace Filehosting\Helper;

/**
 * Class PathHelper
 * @package Filehosting\Helper
 */
class PathHelper
{
    /**
     * @var string $basePath
     */
    private $basePath;

    /**
     * PathHelper constructor.
     *
     * Do not use $_SERVER['DOCUMENT_ROOT'], it does not work in CLI mode
     *
     * @param string $basePath
     */
    public function __construct(string $basePath = "")
    {
        $this->basePath = $basePath === "" ? dirname(__DIR__, 2) : $basePath;
    }

    /**
     * Return base path
     *
     * @return string base path
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Return path to the directory where the downloaded files are stored
     *
     * @return string
     */
    public function getPathToFilesStorage(): string
    {
        return "{$this->getBasePath()}/storage";
    }

    /**
     * Return path to the directory where thumbnails are stored
     *
     * @return string
     */
    public function getPathToThumbnailsStorage(): string
    {
        return "{$this->getBasePath()}/public/thumbnails";
    }

    /**
     * Return path to the directory in which the file is stored
     *
     * @param string $fileName
     *
     * @return string
     */
    public function getPathToFileDirectory(string $fileName): string
    {
        return "{$this->getPathToFilesStorage()}/{$fileName[0]}{$fileName[1]}";
    }


    /**
     * Return path to the file
     *
     * @param string $fileName
     *
     * @return string
     */
    public function getPathToFile(string $fileName): string
    {
        return "{$this->getPathToFilesStorage()}/{$fileName[0]}{$fileName[1]}/{$fileName}";
    }

    /**
     * Return path to the directory in which the thumbnail is stored
     *
     * @param string $fileName
     *
     * @return string
     */
    public function getPathToThumbnailDirectory(string $fileName): string
    {
        return "{$this->getPathToThumbnailsStorage()}/{$fileName[0]}{$fileName[1]}";
    }

    /**
     * Return path to the thumbnail
     *
     * @param string $fileName
     *
     * @return string
     */
    public function getPathToThumbnail(string $fileName): string
    {
        return "{$this->getPathToThumbnailsStorage()}/{$fileName[0]}{$fileName[1]}/{$fileName}";
    }

    /**
     * Return the relative path relative to the public directory.
     *
     * Do not use $_SERVER['DOCUMENT_ROOT'], it does not work in CLI mode
     *
     * @param string $path
     *
     * @return string
     */
    public function getRelativePath(string $path): string
    {
        return str_replace("{$this->basePath}/public", '', $path);
    }

    /**
     * Return path for X-Accel
     *
     * @param string $filePath
     *
     * @return string
     */
    public function getXAccelPath(string $filePath): string
    {
        return str_replace($this->getBasePath(), '', $filePath);
    }
}

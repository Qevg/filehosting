<?php

namespace Filehosting\Controller;

use Filehosting\Database\FileMapper;
use Filehosting\Helper\PathHelper;
use Slim\Container;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class DownloadController
 * @package Filehosting\Controller
 */
class DownloadController
{
    /**
     * @var FileMapper $fileMapper
     */
    private $fileMapper;

    /**
     * @var PathHelper $pathHelper
     */
    private $pathHelper;

    /**
     * @var array $config
     */
    private $config;

    /**
     * DownloadController constructor.
     *
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        $this->fileMapper = $c->get('FileMapper');
        $this->pathHelper = $c->get('PathHelper');
        $this->config = $c->get('config');
    }

    /**
     * File download
     * If XSendFile is supported, used one. If XSendFile is not supported, used readfile
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @throws NotFoundException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $serverSoftware = $request->getServerParam('SERVER_SOFTWARE');
        $file = $this->fileMapper->getFileByFileName(strval($args['name']));
        if (!empty($file) && file_exists($file->getPath())) {
            $response = $response->withHeader('Content-Type', $file->getMimeType());
            $response = $response->withHeader('Content-Lenth', $file->getSize());
            $response = $response->withHeader('Content-Disposition', "attachment; filename={$file->getOriginalName()}");
            if (strpos($serverSoftware, "nginx") !== false && strtolower($this->config['XSendFile']) === 'on') {
                $response = $response->withHeader('X-Accel-Redirect', $this->pathHelper->getXAccelPath($file->getPath()));
            } elseif (strpos($serverSoftware, "apache") !== false && strtolower($this->config['XSendFile']) === 'on' && in_array('mod_xsendfile', apache_get_modules())) {
                $response = $response->withHeader('X-SendFile', $file->getPath());
            } else {
                readfile($file->getPath());
            }
            $this->fileMapper->increaseNumOfDownloads($file->getName());
            return $response;
        } else {
            throw new NotFoundException($request, $response);
        }
    }
}

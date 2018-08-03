<?php

namespace Filehosting\Controller;

use Filehosting\Database\FileMapper;
use Filehosting\Entity\File;
use Filehosting\Entity\User;
use Filehosting\Helper\AuthHelper;
use Filehosting\Helper\CommentHelper;
use Filehosting\Helper\FileHelper;
use Slim\Container;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

/**
 * Class FileController
 * @package Filehosting\Controller
 */
class FileController
{
    /**
     * @var Twig $twig
     */
    private $twig;

    /**
     * @var FileMapper $fileMapper
     */
    private $fileMapper;

    /**
     * @var FileHelper $fileHelper
     */
    private $fileHelper;

    /**
     * @var CommentHelper $commentHelper
     */
    private $commentHelper;

    /**
     * @var AuthHelper $authHelper
     */
    private $authHelper;

    /**
     * @var User $user
     */
    private $user;

    /**
     * FileController constructor.
     *
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        $this->twig = $c->get('twig');
        $this->fileMapper = $c->get('FileMapper');
        $this->fileHelper = $c->get('FileHelper');
        $this->commentHelper = $c->get('CommentHelper');
        $this->authHelper = $c->get('AuthHelper');
        $this->user = $this->authHelper->getUser();
    }

    /**
     * File page
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
        $file = $this->fileMapper->getFileByFileName(strval($args['name']));
        if (empty($file)) {
            throw new NotFoundException($request, $response);
        }
        $file->setCanManage($this->authHelper->userCanManageFile($file->getName(), $file->getUserId()));
        $file->setComments($this->commentHelper->getComments($file->getId()));

        return $response = $this->twig->render($response, '/page/file.twig', ['controller' => 'file', 'user' => $this->user, 'file' => $file]);
    }

    /**
     * Update file data
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @throws NotFoundException
     */
    public function updateFileData(Request $request, Response $response, array $args): Response
    {
        $file = $this->fileMapper->getFileByFileName(strval($args['name']));
        if (!empty($file) && $this->authHelper->userCanManageFile($file->getName(), $file->getUserId())) {
            $this->fileHelper->updateFileData($file->getName(), $request->getParams(), File::STATUS_IS_LOADED);
        }
        return $response->withRedirect("/file/{$file->getName()}", 302);
    }

    /**
     * Remove file
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return string
     */
    public function removeFile(Request $request, Response $response, array $args): string
    {
        $file = $this->fileMapper->getFileByFileName(strval($args['name']));
        if (!empty($file) && $this->authHelper->userCanManageFile($file->getName(), $file->getUserId())) {
            $this->fileHelper->removeFile($file);
            return json_encode(array('status' => 'success'));
        }
        return json_encode(array('status' => 'error'));
    }
}

<?php

namespace Filehosting\Controller;

use Filehosting\Database\FileMapper;
use Filehosting\Database\SearchMapper;
use Filehosting\Entity\User;
use Filehosting\Helper\AuthHelper;
use Filehosting\Helper\CommentHelper;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

/**
 * Class FilesController
 * @package Filehosting\Controller
 */
class FilesController
{
    /**
     * @var Twig $twig
     */
    private $twig;

    /**
     * @var SearchMapper $searchMapper
     */
    private $searchMapper;

    /**
     * @var FileMapper $fileMapper
     */
    private $fileMapper;

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
     * FilesController constructor.
     *
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        $this->twig = $c->get('twig');
        $this->searchMapper = $c->get('SearchMapper');
        $this->fileMapper = $c->get('FileMapper');
        $this->commentHelper = $c->get('CommentHelper');
        $this->authHelper = $c->get('AuthHelper');
        $this->user = $this->authHelper->getUser();
    }

    /**
     * Files page and search
     * Files page by default shows 10 latest files
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $files = array();
        $comments = array();
        if (!empty($request->getParam('query'))) {
            $result = $this->searchMapper->searchQuery(strval($request->getParam('query')));
        } else {
            $result = $this->searchMapper->getLatestFiles(10);
        }

        if (!empty($result)) {
            foreach ($result as $item => $value) {
                $files[] = $this->fileMapper->getFileById($value->getId());
                $files[$item]->setCanManage($this->authHelper->userCanManageFile($files[$item]->getName(), $files[$item]->getUserId()));
                $files[$item]->setComments($this->commentHelper->getComments($files[$item]->getId()));
            }
        }
        return $response = $this->twig->render($response, '/page/files.twig', ['controller' => 'files', 'user' => $this->user, 'files' => $files]);
    }
}

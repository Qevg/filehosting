<?php

namespace Filehosting\Controller;

use Filehosting\Entity\File;
use Filehosting\Entity\User;
use Filehosting\Exception\FileUploadException;
use Filehosting\Helper\AuthHelper;
use Filehosting\Helper\FileHelper;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

/**
 * Class UploadController
 * @package Filehosting\Controller
 */
class UploadController
{
    /**
     * @var Twig $twig
     */
    private $twig;

    /**
     * @var \Redis $redis
     */
    private $redis;

    /**
     * @var FileHelper $fileHelper
     */
    private $fileHelper;

    /**
     * @var AuthHelper $authHelper
     */
    private $authHelper;

    /**
     * @var User $user
     */
    private $user;

    /**
     * UploadController constructor.
     *
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        $this->twig = $c->get('twig');
        $this->redis = $c->get('redis');
        $this->fileHelper = $c->get('FileHelper');
        $this->authHelper = $c->get('AuthHelper');
        $this->user = $this->authHelper->getUser();
    }

    /**
     * Main page and upload file
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response|string
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        if ($request->isPost() && !empty($request->getParam('preUploadFile'))) {
            return json_encode($this->preUploadFile());
        }

        if ($request->isPost() && !empty($request->getParam('uploadFile'))) {
            return json_encode($this->uploadFile($request));
        }

        if ($request->isPost() && !empty($request->getParam('updateFileData'))) {
            return json_encode($this->updateFileData($request));
        }

        return $response = $this->twig->render($response, '/page/upload.twig', ['controller' => 'upload', 'user' => $this->user]);
    }


    /**
     * Generates the name of the future file and stores it in the redis.
     * Automatically remove after 24 hours.
     *
     * $filename is the name of the file under which the file will be stored on disk
     *
     * @return string name of the future file
     */
    private function preUploadFile(): string
    {
        $fileName = bin2hex(random_bytes(8));
        $this->redis->set("PHPREDIS_SESSION:" . session_id(), serialize(array('fileName' => $fileName)));
        $this->redis->expire("PHPREDIS_SESSION:" . session_id(), 86400);
        return $fileName;
    }

    /**
     * Loads a file.
     * When the file is loading, the user can add data to the file and this will be saved to redis.
     * After the file is loaded, if in redis is data, it will be moved from redis to the main database.
     * After, data from the redis will be removed.
     *
     * @param Request $request
     *
     * @return array array of errors
     * @throws FileUploadException
     */
    private function uploadFile(Request $request): array
    {
        $session = unserialize($this->redis->get("PHPREDIS_SESSION:" . session_id()));
        if ($session === false || empty($session['fileName'])) {
            throw new FileUploadException("Error loading file");
        }
        $errors = $this->fileHelper->saveUploadedFile($request->getUploadedFiles()['file'], $session['fileName']);
        if (empty($errors) && $this->fileHelper->isDataInRedis()) {
            $this->fileHelper->moveDataFromRedisToMainDatabase();
        }
        $session = unserialize($this->redis->get("PHPREDIS_SESSION:" . session_id()));
        $this->redis->set("PHPREDIS_SESSION:" . session_id(), '');
        return $errors;
    }

    /**
     * Adds data about file in redis
     *
     * @param Request $request
     *
     * @return array array of errors
     * @throws FileUploadException
     */
    private function updateFileData(Request $request): array
    {
        $session = unserialize($this->redis->get("PHPREDIS_SESSION:" . session_id()));
        if ($session === false || empty($session['fileName'])) {
            throw new FileUploadException("Failed to add file information");
        }
        return $errors = $this->fileHelper->updateFileData($session['fileName'], $request->getParams(), File::STATUS_IS_LOADING);
    }
}

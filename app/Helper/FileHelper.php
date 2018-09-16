<?php

namespace Filehosting\Helper;

use Filehosting\Database\FileMapper;
use Filehosting\Database\SearchMapper;
use Filehosting\Entity\File;
use Filehosting\Entity\User;
use Filehosting\Exception\FileUploadException;
use Filehosting\Helper\PathHelper;
use Filehosting\Validator\FileValidator;
use Slim\Container;
use Slim\Http\UploadedFile;

/**
 * Class FileHelper
 *
 * @package Filehosting\Helper
 */
class FileHelper
{
    /**
     * @var FileSystemHelper $fileSystemHelper
     */
    private $fileSystemHelper;

    /**
     * @var ThumbnailHelper $thumbnailHelper
     */
    private $thumbnailHelper;

    /**
     * @var MediaInfoHelper $mediaInfoHelper
     */
    private $mediaInfoHelper;

    /**
     * @var PathHelper $pathHelper
     */
    private $pathHelper;

    /**
     * @var FileMapper $fileMapper
     */
    private $fileMapper;

    /**
     * @var SearchMapper $searchMapper
     */
    private $searchMapper;

    /**
     * @var FileValidator $fileValidator
     */
    private $fileValidator;

    /**
     * @var AuthHelper $authHelper
     */
    private $authHelper;

    /**
     * @var CookieHelper $cookieHelper
     */
    private $cookieHelper;

    /**
     * @var User $user
     */
    private $user;

    /**
     * @var \Redis $redis
     */
    private $redis;

    /**
     * FileHelper constructor.
     *
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        $this->fileSystemHelper = $c->get('FileSystemHelper');
        $this->thumbnailHelper = $c->get('ThumbnailHelper');
        $this->mediaInfoHelper = $c->get('MediaInfoHelper');
        $this->pathHelper = $c->get('PathHelper');
        $this->fileMapper = $c->get('FileMapper');
        $this->searchMapper = $c->get('SearchMapper');
        $this->fileValidator = $c->get('FileValidator');
        $this->authHelper = $c->get('AuthHelper');
        $this->cookieHelper = $c->get('CookieHelper');
        $this->user = $this->authHelper->getUser();
        $this->redis = $c->get('redis');
    }

    /**
     * Moves the uploaded file to the upload directory, generates a thumbnail,
     * analyzes the file using getId3, save to the database, adds realtime index in the sphinx
     *
     * When anonymous upload, the user will be set a token in the cookies
     *
     * @param $uploadedFile file that the user uploaded
     * @param $fileName is the name of the file under which the file will be stored on disk
     *
     * @return array errors
     */
    public function saveUploadedFile(UploadedFile $uploadedFile, string $fileName): array
    {
        $errors = $this->fileValidator->validateUploadedFile($uploadedFile);
        if (empty($errors)) {
            $fileDir = $this->pathHelper->getPathToFileDirectory($fileName);
            if (!file_exists($fileDir)) {
                $this->fileSystemHelper->mkdirR($fileDir);
                $this->fileSystemHelper->chmodR($fileDir, dirname($this->pathHelper->getPathToFilesStorage()));
            }
            $originalName = $uploadedFile->getClientFilename();
            $filePath = $this->pathHelper->getPathToFile($fileName);
            $size = $uploadedFile->getSize();
            $userId = !empty($this->user) ? $this->user->getId() : null;
            $userToken = $userId === null ? bin2hex(random_bytes(16)) : null;
            $file = new File();
            $file->setValues(array(
                'name' => $fileName,
                'originalName' => $originalName,
                'path' => $filePath,
                'size' => $size,
                'userId' => $userId,
                'userToken' => $userToken
            ));

            $uploadedFile->moveTo($file->getPath());
            $this->fileSystemHelper->chmodR($file->getPath());

            $mimeType = mime_content_type($file->getPath());
            $file->setMimeType($mimeType);

            if ($this->thumbnailHelper->isTypeSupported($file->getFormat())) {
                $thumbnailPath = $this->thumbnailHelper->generateThumbnail($file);
                $file->setThumbnailPath($thumbnailPath);
            }

            $fileInfo = $this->mediaInfoHelper->analyze($file->getPath(), $file->getType());
            $file->setMediaInfo($fileInfo);

            $id = $this->fileMapper->uploadFile($file);

            if ($userToken !== null) {
                $this->cookieHelper->setCookieToClient($file->getName(), $file->getUserToken(), CookieHelper::AUTH_TOKEN_LIFETIME);
            }

            $this->searchMapper->addIndex($id, $file->getOriginalName());
        }
        return $errors;
    }

    /**
     * First removes the file from the database, then from the sphinx, and only then from the disk.
     * And removes the thumbnail if it exists.
     *
     * @param File $file
     */
    public function removeFile(File $file)
    {
        $this->fileMapper->removeFile($file->getId());
        $this->searchMapper->deleteIndex($file->getId());
        $this->fileSystemHelper->unlink($file->getPath());
        if (!empty($file->getThumbnailPath())) {
            $this->thumbnailHelper->removeThumbnail($file->getName());
        }
    }

    /**
     * Update file data
     * if the file is not already loaded, the data is added to the redis
     * if the file is already loaded, the data is added to the main database
     *
     * @param string $fileName
     * @param array $params
     * @param string $status
     *
     * @return array array of errors
     */
    public function updateFileData(string $fileName, array $params, string $status): array
    {
        $file = new File();
        $file->setDescription(isset($params['description']) ? trim(strval($params['description'])) : '');
        $errors = $this->fileValidator->validateFileData($file);
        if (empty($errors)) {
            if ($status === File::STATUS_IS_LOADING) { // if the file is not already loaded
                $session = array(
                    'fileName' => $fileName,
                    'description' => $file->getDescription()
                );
                $this->redis->set("PHPREDIS_SESSION:" . session_id(), serialize($session));
            } elseif ($status === File::STATUS_IS_LOADED) { // if the file is already loaded
                $this->fileMapper->updateFileData($fileName, $file->getDescription());
            }
        }
        return $errors;
    }

    /**
     * Checks if there are data in the redis
     *
     * @return bool
     */
    public function isDataInRedis(): bool
    {
        $session = unserialize($this->redis->get("PHPREDIS_SESSION:" . session_id()));
        if (!empty($session['fileName']) && !empty($session['description'])) {
            return true;
        }
        return false;
    }

    /**
     * If in redis is data, move data in redis to main database
     *
     * @throws FileUploadException
     */
    public function moveDataFromRedisToMainDatabase(): void
    {
        $session = unserialize($this->redis->get("PHPREDIS_SESSION:" . session_id()));
        if ($this->isDataInRedis()) {
            $this->fileMapper->updateFileData($session['fileName'], $session['description']);
        } else {
            throw new FileUploadException('Failed to move data to redis in the main database');
        }
    }
}

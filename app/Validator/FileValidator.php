<?php

namespace Filehosting\Validator;

use Filehosting\Entity\File;
use Slim\Http\UploadedFile;

/**
 * Class FileValidator
 * @package Filehosting\Validator
 */
class FileValidator extends Validator
{
    /**
     * @var int $maxFileSize
     */
    private $maxFileSize;

    /**
     * FileValidator constructor.
     *
     * @param int $maxFileSize
     */
    public function __construct(int $maxFileSize)
    {
        $this->maxFileSize = $maxFileSize;
    }

    /**
     * Validate the uploaded file
     *
     * @param UploadedFile $file
     *
     * @return array array with errors
     */
    public function validateUploadedFile(UploadedFile $file): array
    {
        $errors = array();
        if ($file->getError() !== UPLOAD_ERR_OK) {
            $errors['errorUploadFile'] = "Ошибка при загрузке файла";
        }
        if ($file->getSize() > $this->maxFileSize) {
            $errors['maxFileSize'] = "Превышен максимально допустимый размер файла";
        }
        return $errors;
    }

    /**
     * Validate the file data
     *
     * @param File $file
     *
     * @return array array with errors
     */
    public function validateFileData(File $file): array
    {
        $errors = array();
        if (!$this->validateLength($file->getDescription(), 0, 120)) {
            $errors['description'] = "Это поле должно содержать не больше 120 символов";
        }
        return $errors;
    }
}

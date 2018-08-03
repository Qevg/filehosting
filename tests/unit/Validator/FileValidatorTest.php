<?php

namespace Filehosting\Tests\Unit\Validator;

use Filehosting\Validator\FileValidator;
use Filehosting\Entity\File;
use PHPUnit\Framework\TestCase;

/**
 * Class FileValidatorTest
 * @package Filehosting\Tests\Unit\Validator
 */
class FileValidatorTest extends TestCase
{
    /**
     * @var FileValidator $fileValidator
     */
    private $fileValidator;

    protected function setUp()
    {
        $this->fileValidator = new FileValidator(104857600);
    }

    public function testSuccessfulValidateUploadedFile()
    {
        $uploadedFile = new \Slim\Http\UploadedFile('/tmp/Kv3mV4fs', 'file-template.jpg', 'image/jpeg', 2097152, UPLOAD_ERR_OK);
        $this->assertEmpty($this->fileValidator->validateUploadedFile($uploadedFile));
    }

    public function testUnsuccessfulValidateUploadedFile()
    {
        $uploadedFile = new \Slim\Http\UploadedFile('/tmp/Kv3mV4fs', 'file-template.jpg', 'image/jpeg', 2097152, UPLOAD_ERR_NO_FILE);
        $this->assertNotEmpty($this->fileValidator->validateUploadedFile($uploadedFile));
    }

    public function testSuccessfulValidateFileData()
    {
        $file = new File();
        $file->setDescription('description');
        $this->assertEmpty($this->fileValidator->validateFileData($file));
    }

    public function testUnsuccessfulValidateFileData()
    {
        $file = new File();
        $file->setDescription(str_repeat('q', 121));
        $this->assertNotEmpty($this->fileValidator->validateFileData($file));
    }
}

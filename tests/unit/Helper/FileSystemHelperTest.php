<?php

namespace Filehosting\Tests\Unit\Helper;

use Filehosting\Exception\FileSystemException;
use Filehosting\Helper\FileSystemHelper;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamContainer;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use PHPUnit\Framework\TestCase;

/**
 * Class FileSystemHelperTest
 * @package Filehosting\Tests\Unit\Helper
 */
class FileSystemHelperTest extends TestCase
{
    /**
     * @var vfsStreamContainer $root
     */
    private $root;

    /**
     * @var FileSystemHelper $fileSystemHelper
     */
    private $fileSystemHelper;

    protected function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('exampleDir'));
        $this->root = vfsStreamWrapper::getRoot();
        $this->fileSystemHelper = new FileSystemHelper();
    }

    public function testUnsuccessfulChnodR()
    {
        $this->expectException(FileSystemException::class);
        $this->fileSystemHelper->chmodR('/dshfdhfdhgfhj');
    }

    public function testChmodRFile()
    {
        $file = vfsStream::newFile('exampleFile');
        $this->root->addChild($file);
        $this->assertNotEquals($file->getPermissions(), 0644);
        $this->fileSystemHelper->chmodR($file->url());
        $this->assertEquals($file->getPermissions(), 0644);
    }

    public function testChmodRDir()
    {
        $dir = vfsStream::newDirectory('childDir');
        $this->root->addChild($dir);
        $this->assertNotEquals($dir->getPermissions(), 0755);
        $this->fileSystemHelper->chmodR($dir->url(), dirname($dir->url()));
        $this->assertEquals($dir->getPermissions(), 0755);
    }

    public function testUnsuccessfulChmodRDir()
    {
        $this->expectException(FileSystemException::class);
        $dir = vfsStream::newDirectory('childDir');
        $this->root->addChild($dir);
        $this->fileSystemHelper->chmodR($dir->url());
    }

    public function testMkdirR()
    {
        $childDir = vfsStream::newDirectory('childDir');
        $this->assertFalse($this->root->hasChild('childDir'));
        $this->fileSystemHelper->mkdirR($this->root->url() . '/childDir');
        $this->assertTrue($this->root->hasChild('childDir'));
    }

    public function testUnsuccessfulMkdirR()
    {
        $this->expectException(FileSystemException::class);
        $this->fileSystemHelper->chmodR('/dshfdhfdhgfhj');
    }

    public function testUnlink()
    {
        $file = vfsStream::newFile('exampleFile');
        $this->root->addChild($file);
        $this->assertTrue($this->root->hasChild('exampleFile'));
        $this->fileSystemHelper->unlink($file->url());
        $this->assertFalse($this->root->hasChild('exampleFile'));
    }

    public function testUnsuccessfulUnlink()
    {
        $this->expectException(FileSystemException::class);
        $this->fileSystemHelper->unlink('/dshfdhfdhgfhj');
    }
}

<?php

namespace Filehosting\Tests\Unit\Entity;

use Filehosting\Entity\File;
use PHPUnit\Framework\TestCase;

/**
 * Class FileTest
 * @package Filehosting\Tests\Unit\Entity
 */
class FileTest extends TestCase
{
    public function testSetterAndGetter()
    {
        $file = new File();
        $id = 1000;
        $file->setId($id);
        $this->assertEquals($file->getId(), $id);
    }

    public function testIsImage()
    {
        $file = new File();
        $file->setMimeType('image/jpeg');
        $this->assertTrue($file->isImage());
    }

    public function testIsNotImage()
    {
        $file = new File();
        $file->setMimeType('audio/webm');
        $this->assertFalse($file->isImage());
    }

    public function testIsAudio()
    {
        $file = new File();
        $file->setMimeType('audio/webm');
        $this->assertTrue($file->isAudio());
    }

    public function testIsNotAudio()
    {
        $file = new File();
        $file->setMimeType('image/jpeg');
        $this->assertFalse($file->isAudio());
    }

    public function testIsVideo()
    {
        $file = new File();
        $file->setMimeType('video/webm');
        $this->assertTrue($file->isVideo());
    }

    public function testIsNotVideo()
    {
        $file = new File();
        $file->setMimeType('image/jpeg');
        $this->assertFalse($file->isVideo());
    }

    public function testSuccessfulGetType()
    {
        $file = new File();
        $file->setMimeType('video/webm');
        $this->assertEquals($file->getType(), 'video');
    }

    public function testUnsuccessfulGetType()
    {
        $file = new File();
        $file->setMimeType('video/webm');
        $this->assertNotEquals($file->getType(), 'webm');
    }

    public function testSuccessfulGetFormat()
    {
        $file = new File();
        $file->setMimeType('video/webm');
        $this->assertEquals($file->getFormat(), 'webm');
    }

    public function testUnsuccessfulGetFormat()
    {
        $file = new File();
        $file->setMimeType('video/webm');
        $this->assertNotEquals($file->getFormat(), 'video');
    }
}

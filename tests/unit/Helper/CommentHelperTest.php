<?php

namespace Filehosting\Tests\Unit\Helper;

use Filehosting\Database\CommentMapper;
use Filehosting\Entity\Comment;
use Filehosting\Exception\CommentException;
use Filehosting\Helper\CommentHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class CommentHelperTest
 * @package Filehosting\Tests\Unit\Helper
 */
class CommentHelperTest extends TestCase
{
    /**
     * @var CommentHelper $commentHelper
     */
    private $commentHelper;

    /**
     * @var MockObject $commentMapperMock
     */
    private $commentMapperMock;

    protected function setUp()
    {
        $this->commentMapperMock = $this->createMock(CommentMapper::class);
        $this->commentHelper = new CommentHelper($this->commentMapperMock);
    }

    public function testGeneratorMatPathForRootComment()
    {
        $this->commentMapperMock->method('getRootMaxPath')->willReturn('1');
        $this->assertEquals($this->commentHelper->generateMatPath(null, 1), '002');
    }

    public function testGeneratorMatPathForChildComment()
    {
        $testComment = new Comment();
        $testComment->setValues(['id' => 1, 'matpath' => '001']);
        $this->commentMapperMock->method('getComment')->willReturn($testComment);
        $this->commentMapperMock->method('getChildMaxPath')->willReturn('001.002');
        $this->assertEquals($this->commentHelper->generateMatPath(1, 1), '001.003');
    }

    public function testGeneratorMatPathIfParentCommentDoesNotExist()
    {
        $this->commentMapperMock->method('getComment')->willReturn(null);
        $this->expectException(CommentException::class);
        $this->commentHelper->generateMatPath(13464234234, 1);
    }
}

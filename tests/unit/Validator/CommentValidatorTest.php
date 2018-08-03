<?php

namespace Filehosting\Tests\Unit\Validator;

use Filehosting\Validator\CommentValidator;
use Filehosting\Entity\Comment;
use PHPUnit\Framework\TestCase;

/**
 * Class CommentValidatorTest
 * @package Filehosting\Tests\Unit\Validator
 */
class CommentValidatorTest extends TestCase
{
    /**
     * @var CommentValidator $commentValidator
     */
    private $commentValidator;

    protected function setUp()
    {
        $this->commentValidator = new CommentValidator();
    }

    public function testSuccessfulValidateComment()
    {
        $comment = new Comment();
        $comment->setFileId(1);
        $comment->setText('comment');
        $comment->setMatpath(001);
        $this->assertEmpty($this->commentValidator->validateComment($comment));
    }

    public function testUnsuccessfulValidateComment()
    {
        $comment = new Comment();
        $comment->setFileId(null);
        $comment->setText('comment');
        $comment->setMatpath(001);
        $this->assertNotEmpty($this->commentValidator->validateComment($comment));
    }
}

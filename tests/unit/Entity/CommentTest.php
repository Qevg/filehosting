<?php

namespace Filehosting\Tests\Unit\Entity;

use Filehosting\Entity\Comment;
use PHPUnit\Framework\TestCase;

/**
 * Class CommentTest
 * @package Filehosting\Tests\Unit\Entity
 */
class CommentTest extends TestCase
{
    public function testSetterAndGetter()
    {
        $comment = new Comment();
        $id = 1000;
        $comment->setId($id);
        $this->assertEquals($comment->getId(), $id);
    }

    public function testDepth()
    {
        $comment = new Comment();
        $matpath = '001.002.001';
        $depth = 3;
        $comment->setMatpath($matpath);
        $this->assertEquals($comment->getDepth(), $depth);
    }
}

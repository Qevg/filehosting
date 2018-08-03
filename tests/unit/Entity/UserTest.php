<?php

namespace Filehosting\Tests\Unit\Entity;

use Filehosting\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest
 * @package Filehosting\Tests\Unit\Entity
 */
class UserTest extends TestCase
{
    public function testSetterAndGetter()
    {
        $user = new User();
        $id = 1000;
        $user->setId($id);
        $this->assertEquals($user->getId(), $id);
    }
}

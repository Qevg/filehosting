<?php

namespace Filehosting\Tests\Unit\Validator;

use Filehosting\Validator\UserValidator;
use Filehosting\Entity\User;
use Filehosting\Database\UserMapper;
use PHPUnit\Framework\TestCase;

/**
 * Class UserValidatorTest
 * @package Filehosting\Tests\Unit\Validator
 */
class UserValidatorTest extends TestCase
{
    /**
     * @var UserValidator $userValidator
     */
    private $userValidator;

    protected function setUp()
    {
        $userMapper = $this->createMock(UserMapper::class);
        $userMapper->method('isEmailUsed')->willReturn(false);
        $this->userValidator = new UserValidator($userMapper);
    }

    public function testSuccessfulValidateUser()
    {
        $user = new User();
        $user->setId(1);
        $user->setName('test');
        $user->setEmail('test@example.com');
        $user->setPassword('qwerty123456');
        $this->assertEmpty($this->userValidator->validateUser($user));
    }

    public function testUnsuccessfulValidateUser()
    {
        $user = new User();
        $user->setId(1);
        $user->setName('test');
        $user->setEmail('test@example.com');
        $user->setPassword('q');
        $this->assertNotEmpty($this->userValidator->validateUser($user));
    }
}

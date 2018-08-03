<?php

namespace Filehosting\Validator;

use Filehosting\Database\UserMapper;
use Filehosting\Entity\User;

/**
 * Class UserValidator
 * @package Filehosting\Validator
 */
class UserValidator extends Validator
{
    /**
     * @var UserMapper $userMapper
     */
    private $userMapper;

    /**
     * UserValidator constructor.
     *
     * @param UserMapper $userMapper
     */
    public function __construct(UserMapper $userMapper)
    {
        $this->userMapper = $userMapper;
    }

    /**
     * Validate the user data
     *
     * @param User $user
     *
     * @return array array with errors
     */
    public function validateUser(User $user): array
    {
        $errors['name'] = $this->validateName($user->getName());
        $errors['email'] = $this->validateEmail($user->getEmail(), $user->getId());
        $errors['password'] = $this->validatePassword($user->getPassword());
        return array_filter($errors);
    }

    /**
     * Validate the user name
     *
     * @param string $name
     *
     * @return string|null
     */
    private function validateName(string $name)
    {
        $min = 3;
        $max = 30;
        $errorLength = "Имя пользователя должно содержать не меньше $min и не больше $max символов";

        if (!$this->validateLength($name, $min, $max)) {
            return $errorLength;
        }
    }

    /**
     * Validate the email address
     *
     * @param string $email
     * @param int|null $id
     *
     * @return string|null
     */
    private function validateEmail(string $email, $id)
    {
        $pattern = '/^.+@.+$/u';
        $errorPattern = 'Адрес электронной почты должен состоять из двух частей, разделённых символом «@». Пример: name@example.com';

        $min = 3;
        $max = 255;
        $errorLength = "Это поле должно содержать не меньше $min и не больше $max символов";
        $errorEmailUsed = "Пользователь с таким email уже существует";

        if (!$this->validatePattern($pattern, $email)) {
            return $errorPattern;
        } elseif (!$this->validateLength($email, $min, $max)) {
            return $errorLength;
        } elseif ($this->userMapper->isEmailUsed($email, $id)) {
            return $errorEmailUsed;
        }
    }

    /**
     * Validate the password
     *
     * @param string $password
     *
     * @return string|null
     */
    private function validatePassword(string $password)
    {
        $min = 8;
        $max = 255;
        $errorLength = "Пароль должнен содержать не меньше $min и не больше $max символов";

        if (!$this->validateLength($password, $min, $max)) {
            return $errorLength;
        }
    }
}

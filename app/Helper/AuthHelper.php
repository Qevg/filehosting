<?php

namespace Filehosting\Helper;

use Filehosting\Database\FileMapper;
use Filehosting\Database\UserMapper;
use Filehosting\Entity\User;
use Filehosting\Validator\UserValidator;

/**
 * Class AuthHelper
 * @package Filehosting\Helper
 */
class AuthHelper
{
    /**
     * @var UserMapper $userMapper
     */
    private $userMapper;

    /**
     * @var UserValidator $userValidator
     */
    private $userValidator;

    /**
     * @var FileMapper $fileMapper
     */
    private $fileMapper;

    /**
     * @var CookieHelper $cookieHelper
     */
    private $cookieHelper;

    /**
     * AuthHelper constructor.
     *
     * @param UserMapper $userMapper
     * @param UserValidator $userValidator
     * @param FileMapper $fileMapper
     * @param CookieHelper $cookieHelper
     */
    public function __construct(UserMapper $userMapper, UserValidator $userValidator, FileMapper $fileMapper, CookieHelper $cookieHelper)
    {
        $this->userMapper = $userMapper;
        $this->userValidator = $userValidator;
        $this->fileMapper = $fileMapper;
        $this->cookieHelper = $cookieHelper;
    }

    /**
     * Register
     *
     * @param array $params
     *
     * @return array
     */
    public function register(array $params): array
    {
        $user = new User();
        $user->setName(isset($params['name']) ? trim(strval($params['name'])) : '');
        $user->setEmail(isset($params['email']) ? trim(strval($params['email'])) : '');
        $user->setPassword(isset($params['password']) ? trim(strval($params['password'])) : '');
        $errors = $this->userValidator->validateUser($user);
        if (empty($errors)) {
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));
            $user->setAuthToken(bin2hex(random_bytes(16)));
            $this->cookieHelper->setCookieToClient(CookieHelper::AUTH_TOKEN_NAME, $user->getAuthToken(), CookieHelper::AUTH_TOKEN_LIFETIME);
            $this->userMapper->addUser($user);
        }
        return $errors;
    }

    /**
     * Log in
     *
     * @param array $params
     *
     * @return array
     */
    public function logIn(array $params): array
    {
        $errors = array();
        $user = new User();
        $user->setEmail(isset($params['email']) ? trim(strval($params['email'])) : '');
        $user->setPassword(isset($params['password']) ? trim(strval($params['password'])) : '');
        $dbUser = $this->userMapper->getUserByEmail($user->getEmail());
        if (!empty($dbUser) && password_verify($user->getPassword(), $dbUser->getPassword())) {
            if (password_needs_rehash($dbUser->getPassword(), PASSWORD_DEFAULT)) {
                $this->userMapper->updatePassword(password_hash($user->getPassword(), PASSWORD_DEFAULT), $dbUser->getId());
            }
            $this->cookieHelper->setCookieToClient(CookieHelper::AUTH_TOKEN_NAME, $dbUser->getAuthToken(), CookieHelper::AUTH_TOKEN_LIFETIME);
        } else {
            $errors['all'] = "Не удалось войти. Пожалуйста, проверьте правильность логина и пароля.";
        }
        return $errors;
    }

    /**
     * Log out
     */
    public function logOut(): void
    {
        $this->cookieHelper->deleteCookieToClient(CookieHelper::AUTH_TOKEN_NAME, CookieHelper::AUTH_TOKEN_LIFETIME);
    }

    /**
     * Checks user auth
     *
     * @return bool
     */
    public function isAuth(): bool
    {
        if (isset($_COOKIE[CookieHelper::AUTH_TOKEN_NAME])) {
            return $this->userMapper->checkAuthToken(strval($_COOKIE[CookieHelper::AUTH_TOKEN_NAME]));
        }
        return false;
    }

    /**
     * Gets user
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->isAuth()) {
            return $this->userMapper->getUserByAuthToken(strval($_COOKIE[CookieHelper::AUTH_TOKEN_NAME]));
        }
        return null;
    }

    /**
     * Checks can the user manage the file
     *
     * @param string $fileName
     * @param int|null $userId user id of the file
     *
     * @return bool
     */
    public function userCanManageFile(string $fileName, $userId): bool
    {
        // For a file uploaded by an authorized user
        $user = $this->getUser();
        if (!empty($user) && !empty($userId) && $user->getId() === $userId) {
            return true;
        }

        // For a file uploaded by an anonymous user
        $token = isset($_COOKIE[$fileName]) ? strval($_COOKIE[$fileName]) : null;
        if (!empty($token) && $this->fileMapper->userCanManageFile($fileName, $token)) {
            return true;
        }

        return false;
    }
}

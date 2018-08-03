<?php

namespace Filehosting\Entity;

/**
 * Class User
 * @package Filehosting\Entity
 */
class User extends BaseEntity
{
    /**
     * @var $id
     */
    private $id;

    /**
     * @var $name
     */
    private $name;

    /**
     * @var $email
     */
    private $email;

    /**
     * @var $password
     */
    private $password;

    /**
     * @var $auth_token
     */
    private $auth_token;

    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getAuthToken(): string
    {
        return $this->auth_token;
    }

    /**
     * @param string $auth_token
     */
    public function setAuthToken(string $auth_token): void
    {
        $this->auth_token = $auth_token;
    }
}

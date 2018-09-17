<?php

namespace Filehosting\Database;

use Filehosting\Entity\User;

/**
 * Class UserMapper
 * @package Filehosting\Database
 */
class UserMapper
{
    /**
     * @var \PDO $db
     */
    private $db;

    /**
     * UserMapper constructor.
     *
     * @param \PDO $db
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Adds user
     *
     * @param User $user
     */
    public function addUser(User $user): void
    {
        $sql = "INSERT INTO users (name, email, password, auth_token) VALUES (:name_bind, :email_bind, :password_bind, :authToken_bind)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name_bind', $user->getName(), \PDO::PARAM_STR);
        $stmt->bindValue(':email_bind', $user->getEmail(), \PDO::PARAM_STR);
        $stmt->bindValue(':password_bind', $user->getPassword(), \PDO::PARAM_STR);
        $stmt->bindValue(':authToken_bind', $user->getAuthToken(), \PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * Gets user by email
     *
     * @param string $email
     *
     * @return User|null
     */
    public function getUserByEmail(string $email)
    {
        $sql = "SELECT * FROM users WHERE email=:email_bind";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email_bind', $email, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_CLASS, 'Filehosting\Entity\User');
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Gets user by auth token
     *
     * @param string $authToken
     *
     * @return User|null
     */
    public function getUserByAuthToken(string $authToken)
    {
        $sql = "SELECT * FROM users WHERE auth_token=:auth_token_bind";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':auth_token_bind', $authToken, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_CLASS, 'Filehosting\Entity\User');
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Update password
     *
     * @param string $password
     * @param int $id
     */
    public function updatePassword(string $password, int $id): void
    {
        $sql = "UPDATE users SET password=:password_bind WHERE id=:id_bind";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':password_bind', $password, \PDO::PARAM_STR);
        $stmt->bindValue(':id_bind', $id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Checks whether the email is used
     *
     * @param string $email
     * @param int|null $id
     *
     * @return bool
     */
    public function isEmailUsed(string $email, $id): bool
    {
        if ($id !== null) {
            $sql = "SELECT COUNT(*) FROM users WHERE email=:email_bind AND id<>:id_bind";
        } else {
            $sql = "SELECT COUNT(*) FROM users WHERE email=:email_bind";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email_bind', $email, \PDO::PARAM_STR);
        if ($id !== null) {
            $stmt->bindValue(':id_bind', $id, \PDO::PARAM_INT);
        }
        $stmt->execute();
        return boolval($stmt->fetchColumn());
    }

    /**
     * Checks auth token
     *
     * @param string $authToken
     *
     * @return bool
     */
    public function checkAuthToken(string $authToken): bool
    {
        $sql = "SELECT COUNT(*) FROM users WHERE auth_token=:auth_token_bind";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':auth_token_bind', $authToken, \PDO::PARAM_STR);
        $stmt->execute();
        return boolval($stmt->fetchColumn());
    }
}

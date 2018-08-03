<?php

namespace Filehosting\Entity;

/**
 * Class Comment
 * @package Filehosting\Entity
 */
class Comment extends BaseEntity implements \JsonSerializable
{
    /**
     * @var $id
     */
    private $id;

    /**
     * @var $file_id
     */
    private $file_id;

    /**
     * @var $parent_id
     */
    private $parent_id;

    /**
     * @var $user_id
     */
    private $user_id;

    /**
     * @var $date
     */
    private $date;

    /**
     * @var $text
     */
    private $text;

    /**
     * @var $matpath
     */
    private $matpath;

    /**
     * @var $user_name
     */
    private $user_name;

    /**
     * @var $user_avatar
     */
    private $user_avatar;

    /**
     * Returns comment depth
     *
     * @return int
     */
    public function getDepth(): int
    {
        return count(explode('.', $this->getMatpath()));
    }

    /**
     * @return mixed
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
     * @return mixed
     */
    public function getFileId()
    {
        return $this->file_id;
    }

    /**
     * @param mixed $file_id
     */
    public function setFileId($file_id): void
    {
        $this->file_id = $file_id;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * @param mixed $parent_id
     */
    public function setParentId($parent_id): void
    {
        $this->parent_id = $parent_id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getMatpath()
    {
        return $this->matpath;
    }

    /**
     * @param string $matpath
     */
    public function setMatpath(string $matpath): void
    {
        $this->matpath = $matpath;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->user_name;
    }

    /**
     * @param mixed $user_name
     */
    public function setUserName($user_name): void
    {
        $this->user_name = $user_name;
    }

    /**
     * @return mixed
     */
    public function getUserAvatar()
    {
        return $this->user_avatar;
    }

    /**
     * @param mixed $user_avatar
     */
    public function setUserAvatar($user_avatar): void
    {
        $this->user_avatar = $user_avatar;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'parentId' => $this->parent_id,
            'date' => $this->date,
            'text' => $this->text,
            'depth' => $this->getDepth(),
            'userName' => $this->user_name,
            'userAvatar' => $this->user_avatar
        ];
    }
}

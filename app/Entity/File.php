<?php

namespace Filehosting\Entity;

/**
 * Class File
 * @package Filehosting\Entity
 */
class File extends BaseEntity
{
    /**
     * The status of the file is "loading"
     */
    const STATUS_IS_LOADING = "loading";

    /**
     * The status of the file is "loaded"
     */
    const STATUS_IS_LOADED = "loaded";

    /**
     * @var $id
     */
    private $id;

    /**
     * @var $name
     */
    private $name;

    /**
     * @var $original_name
     */
    private $original_name;

    /**
     * @var $path
     */
    private $path;

    /**
     * @var $thumbnail_path
     */
    private $thumbnail_path;

    /**
     * @var $description
     */
    private $description;

    /**
     * @var $size
     */
    private $size;

    /**
     * @var $mime_type
     */
    private $mime_type;

    /**
     * @var $user_id
     */
    private $user_id;

    /**
     * @var $user_token
     */
    private $user_token;

    /**
     * @var $media_info
     */
    private $media_info;

    /**
     * @var $user_name
     */
    private $user_name;

    /**
     * @var $user_avatar
     */
    private $user_avatar;

    /**
     * @var $can_manage
     */
    private $can_manage;

    /**
     * @var $comments
     */
    private $comments;

    /**
     * Is image?
     *
     * @return bool
     */
    public function isImage(): bool
    {
        if ($this->getType() === 'image') {
            return true;
        }
        return false;
    }

    /**
     * Is audio?
     *
     * @return bool
     */
    public function isAudio(): bool
    {
        if ($this->getType() === 'audio') {
            return true;
        }
        return false;
    }

    /**
     * Is video?
     *
     * @return bool
     */
    public function isVideo(): bool
    {
        if ($this->getType() === 'video') {
            return true;
        }
        return false;
    }

    /**
     * Get file type
     *
     * @return string
     */
    public function getType(): string
    {
        return explode('/', $this->mime_type)[0];
    }

    /**
     * Get file format
     *
     * @return string
     */
    public function getFormat(): string
    {
        return explode('/', $this->mime_type)[1];
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
    public function getName()
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
     * @return mixed
     */
    public function getOriginalName()
    {
        return $this->original_name;
    }

    /**
     * @param string $original_name
     */
    public function setOriginalName(string $original_name): void
    {
        $this->original_name = $original_name;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getThumbnailPath()
    {
        return $this->thumbnail_path;
    }

    /**
     * @param string $thumbnail_path
     */
    public function setThumbnailPath(string $thumbnail_path): void
    {
        $this->thumbnail_path = $thumbnail_path;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * @return mixed
     */
    public function getMimeType()
    {
        return $this->mime_type;
    }

    /**
     * @param string $mime_type
     */
    public function setMimeType(string $mime_type): void
    {
        $this->mime_type = $mime_type;
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
    public function getUserToken()
    {
        return $this->user_token;
    }

    /**
     * @param mixed $user_token
     */
    public function setUserToken($user_token): void
    {
        $this->user_token = $user_token;
    }

    /**
     * @return mixed
     */
    public function getMediaInfo()
    {
        return $this->media_info;
    }

    /**
     * @param mixed $media_info
     */
    public function setMediaInfo($media_info): void
    {
        $this->media_info = $media_info;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->user_name;
    }

    /**
     * @param string $user_name
     */
    public function setUserName(string $user_name): void
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
     * @param string $user_avatar
     */
    public function setUserAvatar(string $user_avatar): void
    {
        $this->user_avatar = $user_avatar;
    }

    /**
     * @return mixed
     */
    public function getCanManage()
    {
        return $this->can_manage;
    }

    /**
     * @param bool $can_manage
     */
    public function setCanManage(bool $can_manage): void
    {
        $this->can_manage = $can_manage;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param array $comments
     */
    public function setComments(array $comments): void
    {
        $this->comments = $comments;
    }
}

<?php

namespace Filehosting\Database;

use Filehosting\Entity\File;

/**
 * Class FileMapper
 * @package Filehosting\Database
 */
class FileMapper
{
    /**
     * @var \PDO $db
     */
    private $db;

    /**
     * FileMapper constructor.
     *
     * @param \PDO $db
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Uploads file
     *
     * @param File $file
     *
     * @return int
     */
    public function uploadFile(File $file): int
    {
        $sql = "INSERT INTO files 
                (name, original_name, path, thumbnail_path, size, mime_type, user_id, user_token, media_info)
                VALUES 
                (:name_bind, :original_name_bind, :path_bind, :thumbnail_path_bind, :size_bind, :mime_type_bind, :user_id_bind, :user_token_bind, :media_info_bind)
                RETURNING id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name_bind', $file->getName(), \PDO::PARAM_STR);
        $stmt->bindValue(':original_name_bind', $file->getOriginalName(), \PDO::PARAM_STR);
        $stmt->bindValue(':path_bind', $file->getPath(), \PDO::PARAM_STR);
        $stmt->bindValue(':thumbnail_path_bind', $file->getThumbnailPath(), \PDO::PARAM_STR);
        $stmt->bindValue(':size_bind', $file->getSize(), \PDO::PARAM_INT);
        $stmt->bindValue(':mime_type_bind', $file->getMimeType(), \PDO::PARAM_STR);
        $stmt->bindValue(':user_id_bind', $file->getUserId(), \PDO::PARAM_INT);
        $stmt->bindValue(':user_token_bind', $file->getUserToken(), \PDO::PARAM_STR);
        $stmt->bindValue(':media_info_bind', $file->getMediaInfo(), \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Updates file data
     *
     * @param string $fileName
     * @param string $description
     */
    public function updateFileData(string $fileName, string $description): void
    {
        $sql = "UPDATE files 
                SET description=:description_bind
                WHERE name=:name_bind";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name_bind', $fileName, \PDO::PARAM_STR);
        $stmt->bindValue(':description_bind', $description, \PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * Increases the number of downloads per one
     *
     * @param string $fileName
     */
    public function increaseNumOfDownloads(string $fileName): void
    {
        $sql = "UPDATE files SET downloads = downloads + 1 WHERE name=:name_bind";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name_bind', $fileName, \PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * Gets file by fileName
     *
     * @param string $fileName
     *
     * @return File|null
     */
    public function getFileByFileName(string $fileName)
    {
        $sql = "SELECT files.*, users.name as user_name, users.avatar as user_avatar FROM files LEFT JOIN users on files.user_id = users.id WHERE files.name=:name_bind";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name_bind', $fileName, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_CLASS, 'Filehosting\Entity\File');
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Gets file by id
     *
     * @param int $id
     *
     * @return File|null
     */
    public function getFileById(int $id)
    {
        $sql = "SELECT files.*, users.name as user_name, users.avatar as user_avatar FROM files LEFT JOIN users on files.user_id = users.id WHERE files.id=:id_bind";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_bind', $id, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_CLASS, 'Filehosting\Entity\File');
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Removes file
     * First deletes comments to the file then deletes the file
     *
     * @param int $id
     */
    public function removeFile(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM comments WHERE file_id=:id_bind");
        $stmt->bindValue(':id_bind', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $this->db->prepare("DELETE FROM files WHERE id=:id_bind");
        $stmt->bindValue(':id_bind', $id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Checks that user can manage file
     * For anonymous aploaded file
     *
     * @param string $name file name
     * @param string $token
     *
     * @return bool
     */
    public function userCanManageFile(string $name, string $token): bool
    {
        $sql = "SELECT COUNT(*) FROM files WHERE name=:name_bind AND user_token=:user_token_bind";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name_bind', $name, \PDO::PARAM_STR);
        $stmt->bindValue(':user_token_bind', $token, \PDO::PARAM_STR);
        $stmt->execute();
        return boolval($stmt->fetchColumn());
    }
}

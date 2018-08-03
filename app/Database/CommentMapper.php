<?php

namespace Filehosting\Database;

use Filehosting\Entity\Comment;
use function PHPSTORM_META\type;

/**
 * Class CommentMapper
 * @package Filehosting\Database
 */
class CommentMapper
{
    /**
     * @var \PDO $db
     */
    private $db;

    /**
     * CommentMapper constructor.
     *
     * @param \PDO $db
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Adds comment
     *
     * @param Comment $comment
     *
     * @return int
     */
    public function addComent(Comment $comment): int
    {
        $sql = "INSERT INTO comments(file_id, parent_id, user_id, text, matpath) VALUES (:file_id_bind, :parent_id_bind, :user_id_bind, :text_bind, :matpath_bind) RETURNING id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':file_id_bind', $comment->getFileId(), \PDO::PARAM_INT);
        $stmt->bindValue(':parent_id_bind', ($comment->getParentId() !== null ? $comment->getParentId() : null), \PDO::PARAM_INT);
        $stmt->bindValue(':user_id_bind', $comment->getUserId(), \PDO::PARAM_INT);
        $stmt->bindValue(':text_bind', $comment->getText(), \PDO::PARAM_STR);
        $stmt->bindValue(':matpath_bind', $comment->getMatpath(), \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Gets comment by comment id
     *
     * @param int $id
     *
     * @return Comment|null
     */
    public function getComment(int $id)
    {
        $sql = "SELECT comments.*, users.name as user_name, users.avatar as user_avatar FROM comments LEFT JOIN users ON comments.user_id = users.id WHERE comments.id=:id_bind";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_bind', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_CLASS, 'Filehosting\Entity\Comment');
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Gets all comments by file id
     *
     * @param int $fileId
     *
     * @return array
     */
    public function getComments(int $fileId): array
    {
        $sql = "SELECT comments.*, users.name as user_name, users.avatar as user_avatar FROM comments LEFT JOIN users ON comments.user_id = users.id WHERE comments.file_id=:file_id_bind ORDER BY comments.matpath";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':file_id_bind', $fileId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_CLASS, 'Filehosting\Entity\Comment');
    }

    /**
     * Gets max value matPath of the root comment
     *
     * @param int $fileId
     *
     * @return string|null
     */
    public function getRootMaxPath(int $fileId)
    {
        $sql = "SELECT MAX(matpath) FROM comments WHERE parent_id IS NULL AND file_id=:file_id_bind";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':file_id_bind', $fileId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Gets max value matPath of the child comment
     *
     * @param int $parentId
     *
     * @return string|null
     */
    public function getChildMaxPath(int $parentId)
    {
        $sql = "SELECT MAX(matpath) FROM comments WHERE parent_id=:parent_id_bind";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':parent_id_bind', $parentId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}

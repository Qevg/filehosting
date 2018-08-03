<?php

namespace Filehosting\Helper;

use Filehosting\Database\CommentMapper;
use Filehosting\Entity\Comment;
use Filehosting\Exception\CommentException;

/**
 * Class CommentHelper
 * @package Filehosting\Helper
 */
class CommentHelper
{
    /**
     * @var CommentMapper $commentMapper
     */
    private $commentMapper;

    /**
     * CommentHelper constructor.
     *
     * @param CommentMapper $commentMapper
     */
    public function __construct(CommentMapper $commentMapper)
    {
        $this->commentMapper = $commentMapper;
    }

    /**
     * Adds comment
     *
     * @param Comment $comment
     *
     * @return Comment
     */
    public function addComment(Comment $comment): Comment
    {
        $id = $this->commentMapper->addComent($comment);
        return $this->commentMapper->getComment($id);
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
        return $this->commentMapper->getComments($fileId);
    }

    /**
     * Generates materialized path
     *
     * @param mixed $parentId
     * @param int $fileId
     *
     * @return string
     */
    public function generateMatPath($parentId, int $fileId): string
    {
        if (empty($parentId)) {
            $maxPath = intval($this->commentMapper->getRootMaxPath($fileId)) + 1;
            $matPath = $this->pathPreparation($maxPath);
        } else {
            $parentComment = $this->commentMapper->getComment($parentId);
            if (empty($parentComment)) {
                throw new CommentException('Parent comment does not exist');
            }
            $childMaxPath = $this->commentMapper->getChildMaxPath($parentComment->getId());
            if ($childMaxPath === null) {
                $maxPath = intval($childMaxPath) + 1;
            } else {
                $splitPath = $this->splitPath($childMaxPath);
                $maxPath = intval(end($splitPath)) + 1;
            }
            $matPath = $parentComment->getMatpath() . '.' . $this->pathPreparation($maxPath);
        }
        return $matPath;
    }

    /**
     * Splits path by dot.
     *
     * @param string $path
     *
     * @return array
     */
    private function splitPath(string $path): array
    {
        return explode('.', $path);
    }

    /**
     * Adds the missing zeros at the beginning to match the pattern
     *
     * @param string $path
     *
     * @return string
     */
    private function pathPreparation(string $path): string
    {
        return str_pad($path, 3, '0', STR_PAD_LEFT);
    }
}

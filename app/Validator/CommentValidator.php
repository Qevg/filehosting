<?php

namespace Filehosting\Validator;

use Filehosting\Entity\Comment;

/**
 * Class CommentValidator
 * @package Filehosting\Validator
 */
class CommentValidator extends Validator
{
    /**
     * Validate comment
     *
     * @param Comment $comment
     *
     * @return array array with errors
     */
    public function validateComment(Comment $comment): array
    {
        $errors = array();
        if (empty($comment->getFileId())) {
            $errors[] = 'Такого файла не существует';
        }
        if (empty($comment->getText()) || !$this->validateLength($comment->getText(), 1, 250)) {
            $errors[] = 'Это поле должно содержать не меньше 1 и не больше 250 символов';
        }
        if ($comment->getDepth() > 5) {
            $errors[] = 'Достигнута максимально разрешенная вложенность';
        }
        return $errors;
    }
}

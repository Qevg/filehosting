<?php

namespace Filehosting\Controller;

use Filehosting\Database\FileMapper;
use Filehosting\Entity\Comment;
use Filehosting\Entity\User;
use Filehosting\Helper\AuthHelper;
use Filehosting\Helper\CommentHelper;
use Filehosting\Validator\CommentValidator;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CommentController
 * @package Filehosting\Controller
 */
class CommentController
{
    /**
     * @var AuthHelper $authHelper
     */
    private $authHelper;

    /**
     * @var CommentHelper $commentHelper
     */
    private $commentHelper;

    /**
     * @var CommentValidator $commentValidator
     */
    private $commentValidator;

    /**
     * @var FileMapper $fileMapper
     */
    private $fileMapper;

    /**
     * @var User $user
     */
    private $user;

    /**
     * CommentController constructor.
     *
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        $this->authHelper = $c->get('AuthHelper');
        $this->commentHelper = $c->get('CommentHelper');
        $this->commentValidator = $c->get('CommentValidator');
        $this->fileMapper = $c->get('FileMapper');
        $this->user = $this->authHelper->getUser();
    }

    /**
     * Adds comment
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return string
     */
    public function __invoke(Request $request, Response $response, array $args): string
    {
        $file = !empty($args['name']) ? $this->fileMapper->getFileByFileName(strval($args['name'])) : null;
        $fileId = $file !== null ? $file->getId() : null;
        $parentId = !empty($request->getParam('reply-comment-id')) ? $request->getParam('reply-comment-id') : null;
        $userId = !empty($this->user) ? $this->user->getId() : null;
        $text = !empty($request->getParam('comment-text')) ? $request->getParam('comment-text') : null;
        $matPath = $this->commentHelper->generateMatPath($parentId, $fileId);
        $comment = new Comment();
        $comment->setValues(array(
            'fileId' => $fileId,
            'parentId' => $parentId,
            'userId' => $userId,
            'text' => $text,
            'matpath' => $matPath
        ));
        $errors = $this->commentValidator->validateComment($comment);
        if (empty($errors)) {
            $comment = $this->commentHelper->addComment($comment);
            return json_encode(array('status' => 'success', 'comment' => $comment));
        }
        return json_encode(array('status' => 'error', 'errors' => $errors));
    }

    /**
     * Gets all comments
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return string
     */
    public function getAllComments(Request $request, Response $response, array $args): string
    {
        $file = !empty($args['name']) ? $this->fileMapper->getFileByFileName(strval($args['name'])) : null;
        $fileId = $file !== null ? $file->getId() : null;
        if ($fileId !== null) {
            $comments = $this->commentHelper->getComments($fileId);
            return json_encode(array('status' => 'success', 'comments' => $comments));
        }
        return json_encode(array('status' => 'error'));
    }
}

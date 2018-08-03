<?php

$app->map(['GET', 'POST'], '/', \Filehosting\Controller\UploadController::class);
$app->map(['GET', 'POST'], '/files', \Filehosting\Controller\FilesController::class);
$app->map(['GET', 'POST'], '/file/{name}', \Filehosting\Controller\FileController::class);
$app->post('/remove/{name}', \Filehosting\Controller\FileController::class . ':removeFile');
$app->post('/update/{name}', \Filehosting\Controller\FileController::class . ':updateFileData');
$app->get('/download/{name}', \Filehosting\Controller\DownloadController::class);
$app->post('/comment/{name}', \Filehosting\Controller\CommentController::class);
$app->post('/getAllComments/{name}', \Filehosting\Controller\CommentController::class . ':getAllComments');
$app->map(['GET', 'POST'], '/register', \Filehosting\Controller\AuthController::class . ':register');
$app->map(['GET', 'POST'], '/login', \Filehosting\Controller\AuthController::class . ':logIn');
$app->post('/logout', \Filehosting\Controller\AuthController::class . ':logOut');

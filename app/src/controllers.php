<?php

use Controller\MovieListController;
use Controller\ScreeningController;
use Controller\OrderController;
use Controller\TransactionController;
use Controller\AuthController;
use Controller\UserController;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



$app->mount('/movies', new MovieListController());
$app->mount('/screening', new ScreeningController());
$app->mount('/order', new OrderController());
$app->mount('/transaction', new TransactionController());
$app->mount('/auth', new AuthController());
$app->mount('/auth', new UserController());

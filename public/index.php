<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\ProductController;
use App\Controllers\NotFoundController;

$router = new Router();

$router->get('/products', [ProductController::class, 'index']);
$router->get('/product/{id}', [ProductController::class, 'show']);

$router->setNotFoundHandler([NotFoundController::class, 'index']);

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

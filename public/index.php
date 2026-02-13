<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\PageController;
use App\Controllers\ProductController;
use App\Controllers\NotFoundController;

$router = new Router();

$router->get('/', [PageController::class, 'home']);
$router->get('/o-nas', [PageController::class, 'about']);
$router->get('/kontakt', [PageController::class, 'contact']);
$router->get('/pisite-nam', [PageController::class, 'writeToUs']);
$router->get('/izdelki', [ProductController::class, 'index']);
$router->get('/product/{id}', [ProductController::class, 'show']);

$router->setNotFoundHandler([NotFoundController::class, 'index']);

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

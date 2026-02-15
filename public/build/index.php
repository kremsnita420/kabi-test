<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\PageController;
use App\Controllers\ProductController;
use App\Controllers\NotFoundController;

$router = new Router();

// Public routes
$router->get('/', [PageController::class, 'home']);
$router->get('/o-nas', [PageController::class, 'about']);
$router->get('/kontakt', [PageController::class, 'contact']);
$router->get('/pisite-nam', [PageController::class, 'writeToUs']);
$router->get('/izdelki', [ProductController::class, 'index']);
$router->get('/product/{id:\d+}', [ProductController::class, 'show']);

// Admin routes
$router->post('/admin/upload/{slug:[a-z0-9\-]+}', [\App\Controllers\Admin\UploadController::class, 'store']);

$router->get('/admin/upload/{slug:[a-z0-9\-]+}', [\App\Controllers\Admin\UploadPageController::class, 'index']);
$router->post('/admin/upload/{slug:[a-z0-9\-]+}', [\App\Controllers\Admin\UploadController::class, 'store']);

// 404 handler
$router->setNotFoundHandler([NotFoundController::class, 'index']);

// Dispatch the request
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

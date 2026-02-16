<?php

declare(strict_types=1);

/**
 * Front controller for the application when served from the public directory.
 *
 * This script bootstraps a simple PSR-4 autoloader, registers routes using
 * the UrlGenerator for all supported language variants, and dispatches
 * incoming HTTP requests to the appropriate controller methods. It also
 * defines static admin routes for the upload hub and image uploads.
 */

// Simple PSR-4 autoloader for classes in the App\ namespace. Resolve files
// relative to the project root (one level up from this public directory).
spl_autoload_register(function (string $class): void
{
    $prefix = 'App\\';
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0)
    {
        // Not a class from our namespace; ignore
        return;
    }

    // Convert the class name into a relative file path
    $relativeClass = substr($class, $len);
    $relativePath  = str_replace('\\', '/', $relativeClass) . '.php';

    // Try loading from possible base directories. Depending on how the
    // project is deployed the PHP classes may live in either `src/`
    // directly or nested under `src/src/`. Because this file lives in
    // `public/`, we resolve both possibilities relative to the project root.
    $baseDir = dirname(__DIR__) . "/src/";


    $file = $baseDir . $relativePath;
    if (is_file($file))
    {
        require $file;
        return;
    }
});

use App\Core\Router;
use App\Controllers\PageController;
use App\Controllers\ProductController;
use App\Controllers\NotFoundController;
use App\Support\UrlGenerator;

// Instantiate router and URL generator
$router = new Router();
$urlGen = new UrlGenerator();

// Register localized routes for static pages
foreach ($urlGen->paths('home') as $path)
{
    $router->get($path, [PageController::class, 'home']);
}
foreach ($urlGen->paths('about') as $path)
{
    $router->get($path, [PageController::class, 'about']);
}
foreach ($urlGen->paths('contact') as $path)
{
    $router->get($path, [PageController::class, 'contact']);
}
foreach ($urlGen->paths('write_us') as $path)
{
    $router->get($path, [PageController::class, 'writeToUs']);
}

// Register localized routes for the products listing page
foreach ($urlGen->paths('products') as $path)
{
    $router->get($path, [ProductController::class, 'index']);
}

// Register product detail routes with ID parameter
foreach ($urlGen->productShowPatterns() as $pattern)
{
    $router->get($pattern, [ProductController::class, 'show']);
}

// Static admin routes (not localized)
$router->get('/admin/upload-hub', [App\Controllers\Admin\UploadHubController::class, 'index']);
$router->post('/admin/upload/{slug}', [App\Controllers\Admin\UploadController::class, 'store']);

// Fallback 404 handler
$router->setNotFoundHandler([NotFoundController::class, 'index']);

// Dispatch the current request
$uri    = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($uri, $method);

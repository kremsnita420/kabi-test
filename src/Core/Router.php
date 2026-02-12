<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];
    private $notFoundHandler = null;

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function setNotFoundHandler(array $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    public function dispatch(string $uri, string $method): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);

        foreach ($this->routes[$method] ?? [] as $route => $handler) {

            $pattern = preg_replace('#\{[^}]+\}#', '([^/]+)', $route);
            $pattern = "#^$pattern$#";

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                [$class, $controllerMethod] = $handler;
                $controller = new $class();

                call_user_func_array([$controller, $controllerMethod], $matches);
                return;
            }
        }

        $this->handleNotFound();
    }

    private function handleNotFound(): void
    {
        http_response_code(404);

        if ($this->notFoundHandler) {
            [$class, $method] = $this->notFoundHandler;
            (new $class())->$method();
            return;
        }

        echo '404 Not Found';
    }
}

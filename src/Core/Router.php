<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /**
     * @var array<string, array<int, array{route:string, regex:string, params:array<int,string>, handler:array{0:string,1:string}}>>
     */
    private array $routes = [];

    /** @var array{0:string,1:string}|null */
    private ?array $notFoundHandler = null;

    public function get(string $path, array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function put(string $path, array $handler): void
    {
        $this->add('PUT', $path, $handler);
    }

    public function patch(string $path, array $handler): void
    {
        $this->add('PATCH', $path, $handler);
    }

    public function delete(string $path, array $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    public function setNotFoundHandler(array $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $path = $this->normalizePath($path);

        $method = strtoupper($method);
        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $r)
        {
            if (!preg_match($r['regex'], $path, $matches))
            {
                continue;
            }

            // collect params in declared order
            $args = [];
            foreach ($r['params'] as $name)
            {
                $args[] = $matches[$name] ?? null;
            }

            [$class, $controllerMethod] = $r['handler'];
            $controller = new $class();

            \call_user_func_array([$controller, $controllerMethod], $args);
            return;
        }

        $this->handleNotFound();
    }

    private function add(string $method, string $route, array $handler): void
    {
        $method = strtoupper($method);
        $route  = $this->normalizeRoute($route);

        [$regex, $params] = $this->compileRouteToRegex($route);

        $this->routes[$method][] = [
            'route'   => $route,
            'regex'   => $regex,
            'params'  => $params,
            'handler' => $handler,
        ];
    }

    private function normalizePath(string $path): string
    {
        // decode and normalize slashes
        $path = rawurldecode($path);
        $path = preg_replace('#/+#', '/', $path) ?? $path;

        // trim trailing slash except root
        if ($path !== '/' && str_ends_with($path, '/'))
        {
            $path = rtrim($path, '/');
        }

        // ensure leading slash
        if ($path === '' || $path[0] !== '/')
        {
            $path = '/' . $path;
        }

        return $path;
    }

    private function normalizeRoute(string $route): string
    {
        $route = trim($route);
        if ($route === '')
        {
            return '/';
        }

        // ensure leading slash
        if ($route[0] !== '/')
        {
            $route = '/' . $route;
        }

        // trim trailing slash except root
        if ($route !== '/' && str_ends_with($route, '/'))
        {
            $route = rtrim($route, '/');
        }

        return $route;
    }

    /**
     * Supports:
     *  - /product/{id}
     *  - /product/{id:\d+}
     */
    private function compileRouteToRegex(string $route): array
    {
        $params = [];

        // Escape everything first, then replace placeholders.
        // We build a regex with named capture groups.
        $pattern = preg_replace_callback(
            '#\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^}]+))?\}#',
            function (array $m) use (&$params): string
            {
                $name = $m[1];
                $re   = $m[2] ?? '[^/]+';
                $params[] = $name;
                return '(?P<' . $name . '>' . $re . ')';
            },
            preg_quote($route, '#')
        );

        // preg_quote() also quoted our braces replacement markers, so fix it:
        // We re-run compile without preg_quote on whole string by doing a safer approach:
        // Build route regex by replacing tokens on original route.
        $pattern = preg_replace_callback(
            '#\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^}]+))?\}#',
            function (array $m) use (&$params): string
            {
                $name = $m[1];
                $re   = $m[2] ?? '[^/]+';
                if (!in_array($name, $params, true))
                {
                    $params[] = $name;
                }
                return '(?P<' . $name . '>' . $re . ')';
            },
            $route
        );

        // Allow optional trailing slash for non-root paths
        if ($route !== '/')
        {
            $regex = '#^' . $pattern . '/?$#';
        }
        else
        {
            $regex = '#^/$#';
        }

        return [$regex, $params];
    }

    private function handleNotFound(): void
    {
        http_response_code(404);

        if ($this->notFoundHandler)
        {
            [$class, $method] = $this->notFoundHandler;
            (new $class())->$method();
            return;
        }

        echo '404 Not Found';
    }
}

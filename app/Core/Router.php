<?php

namespace App\Core;

/**
 * Very simple router implementation for mapping paths to controller actions.
 */
class Router
{
    /** @var array<string, callable> */
    private array $routes = [];

    /**
     * Register a GET route.
     *
     * @param string $path
     * @param callable $handler
     */
    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    /**
     * Register a POST route.
     *
     * @param string $path
     * @param callable $handler
     */
    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    /**
     * Dispatch the current request to a registered handler.
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        $handler = $this->routes[$method][$path] ?? null;
        if (!$handler) {
            http_response_code(404);
            echo 'Not found';
            return;
        }

        call_user_func($handler, $this);
    }
}

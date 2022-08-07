<?php

declare(strict_types=1);

namespace K1\App;

use Dotenv\Dotenv;

class Router
{
    private array $routes = [];
    private string $controllerPath = 'K1\Controllers\\';

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();
    }

    private function addRoute(array $methods, string $route, string $callback): void
    {
        foreach ($methods as $method) {
            $this->routes[$method][$route] = $callback;
        }
    }

    public function add(array $methods, string $route, string $callback): void
    {
        $this->addRoute($methods, $route, $callback);
    }

    private function match(): string
    {
        Response::header( "Content-Type: application/json");

        $requestUri = $_SERVER['REQUEST_URI'];
        $requestUri = str_contains($requestUri, '?') ? substr($requestUri, 0, strpos($requestUri, '?')) : $requestUri;
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        if (!array_key_exists($method, $this->routes))
            throw new \Exception('Route not found');

        if (!array_key_exists($requestUri, $this->routes[$method]))
            throw new \Exception('Route not found');

        $callback = $this->routes[$method][$requestUri];

        $callback = explode('::', $callback, 2);

        $controller = $this->controllerPath .  $callback[0] . 'Controller';
        $method     = $callback[1];

        if (!class_exists($controller))
            throw new \Exception("class '$controller' does not exist");

        if (!method_exists($controller, $method))
            throw new \Exception("Method '$method' does not exist in class '$controller'");

        return (new $controller)->$method();
    }

    public function resolve(): void
    {
        echo $this->match();
    }
}
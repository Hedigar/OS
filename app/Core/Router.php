<?php

namespace App\Core;

class Router
{
    protected $routes = [];

    public function add(string $method, string $uri, string $controller)
    {
        $this->routes[] = [
            'uri' => $uri,
            'controller' => $controller,
            'method' => strtoupper($method),
        ];
    }

    public function get(string $uri, string $controller)
    {
        $this->add('GET', $uri, $controller);
    }

    public function post(string $uri, string $controller)
    {
        $this->add('POST', $uri, $controller);
    }

    public function dispatch(string $uri, string $method)
    {
        $uri = trim($uri, '/');
        $method = strtoupper($method);

        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === $method) {
                // O formato do controller é "Controller@method"
                list($controllerName, $action) = explode('@', $route['controller']);
                $controllerClass = "App\\Controllers\\" . $controllerName;

                if (class_exists($controllerClass)) {
                    // Define constantes globais para que o BaseController saiba exatamente onde estamos
                    define('CURRENT_CONTROLLER', $controllerName);
                    define('CURRENT_ACTION', $action);

                    $controller = new $controllerClass();
                    if (method_exists($controller, $action)) {
                        // Chama o método do controller
                        return $controller->$action();
                    }
                }
            }
        }

        // Se a rota não for encontrada, exibe um erro 404
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 - Página Não Encontrada</h1>";
        exit();
    }
}

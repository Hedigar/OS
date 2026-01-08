<?php

namespace App\Core;

/**
 * Sistema de roteamento simples e moderno.
 */
class Router
{
    protected array $routes = [];

    /**
     * Adiciona uma rota ao sistema.
     */
    public function add(string $method, string $uri, string $controller): void
    {
        $this->routes[] = [
            'uri' => trim($uri, '/'),
            'controller' => $controller,
            'method' => strtoupper($method),
        ];
    }

    public function get(string $uri, string $controller): void
    {
        $this->add('GET', $uri, $controller);
    }

    public function post(string $uri, string $controller): void
    {
        $this->add('POST', $uri, $controller);
    }

    /**
     * Despacha a requisição para o controlador correspondente.
     */
    public function dispatch(string $uri, string $method): void
    {
        $uri = trim($uri, '/');
        $method = strtoupper($method);

        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === $method) {
                $parts = explode('@', $route['controller']);
                $controllerName = $parts[0];
                $action = $parts[1] ?? 'index';
                
                $controllerClass = "App\\Controllers\\" . $controllerName;

                if (!class_exists($controllerClass)) {
                    $this->error(500, "Controlador {$controllerClass} não encontrado.");
                }

                define('CURRENT_CONTROLLER', $controllerName);
                define('CURRENT_ACTION', $action);

                $controller = new $controllerClass();
                
                if (!method_exists($controller, $action)) {
                    $this->error(500, "Método {$action} não encontrado no controlador {$controllerName}.");
                }

                $controller->$action();
                return;
            }
        }

        $this->error(404, "Página Não Encontrada");
    }

    /**
     * Exibe uma página de erro.
     */
    protected function error(int $code, string $message): void
    {
        http_response_code($code);
        echo "<h1>{$code} - {$message}</h1>";
        exit();
    }
}

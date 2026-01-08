<?php

/**
 * Ponto de entrada da aplicação.
 */

// 1. Carrega o Autoloader (que já carrega o Composer e o .env)
require_once __DIR__ . '/../app/Core/Autoload.php';

// 2. Inicia a sessão com configurações seguras
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Carrega as Rotas
$router = require_once __DIR__ . '/../app/routes.php';

// 4. Obtém a URI e o Método da Requisição
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

// 5. Ajusta a URI se o sistema estiver em um subdiretório
$base_url_path = trim(parse_url(BASE_URL, PHP_URL_PATH), '/');
if (!empty($base_url_path) && strpos($uri, $base_url_path) === 0) {
    $uri = trim(substr($uri, strlen($base_url_path)), '/');
}

// 6. Despacha a Requisição
$router->dispatch($uri, $method);

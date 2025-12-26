<?php

// 1. Carrega o Autoloader e as Configurações
require_once __DIR__ . '/../app/Core/Autoload.php';

// 2. Carrega as Rotas
$router = require_once __DIR__ . '/../app/routes.php';

// 3. Obtém a URI e o Método da Requisição
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

// Remove o subdiretório se estiver sendo executado em um
// Exemplo: se a URL for /os_system/login, a URI deve ser apenas login
$base_url_path = trim(parse_url(BASE_URL, PHP_URL_PATH), '/');
if (!empty($base_url_path) && strpos($uri, $base_url_path) === 0) {
    $uri = trim(substr($uri, strlen($base_url_path)), '/');
}

// 4. Despacha a Requisição
$router->dispatch($uri, $method);

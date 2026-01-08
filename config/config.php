<?php

/**
 * Configurações globais do sistema.
 * Este arquivo detecta automaticamente a URL base para funcionar em qualquer IP ou domínio.
 */

// 1. Configurações do Banco de Dados (Vindas do .env)
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USERNAME'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASSWORD'] ?? '');
define('DB_NAME', $_ENV['DB_DATABASE'] ?? 'os');

// 2. Configurações do Sistema
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Sistema OS');

// 3. Detecção Dinâmica de BASE_URL
// Se APP_URL estiver no .env, usamos ela. Caso contrário, detectamos pelo navegador.
if (!empty($_ENV['APP_URL'])) {
    define('BASE_URL', rtrim($_ENV['APP_URL'], '/') . '/');
} else {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Pega o caminho do script atual para lidar com subdiretórios automaticamente
    // Ex: se acessar 192.168.0.233/sistema/public/index.php, o basePath será /sistema/
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $basePath = str_replace('/public/index.php', '', $scriptName);
    $basePath = str_replace('/index.php', '', $basePath);
    
    define('BASE_URL', $protocol . '://' . $host . rtrim($basePath, '/') . '/');
}

// 4. Caminho dos Ativos (CSS, JS, Imagens)
// Se o .htaccess redireciona para public, os ativos estão na raiz do BASE_URL
define('ASSETS_URL', BASE_URL . 'assets/');

// 5. Configurações de Sessão
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 1 : 0);

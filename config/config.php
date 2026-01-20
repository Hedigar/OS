<?php


// 0. Configuração de Fuso Horário (Adicione isso aqui!)
// Tenta pegar do .env, se não existir, usa America/Sao_Paulo
$timezone = $_ENV['APP_TIMEZONE'] ?? 'America/Sao_Paulo';
date_default_timezone_set($timezone);
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
// Se APP_URL estiver no .env, usamos ela. Caso contrário, detectamos pelo navegador (considerando proxy).
if (!empty($_ENV['APP_URL'])) {
    define('BASE_URL', rtrim($_ENV['APP_URL'], '/') . '/');
} else {
    $httpsCandidates = [
        $_SERVER['HTTPS'] ?? null,
        $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? null,
        $_SERVER['HTTP_X_FORWARDED_SSL'] ?? null,
    ];
    $isHttps = false;
    foreach ($httpsCandidates as $val) {
        if ($val === 'on' || $val === '1' || $val === 'https') { $isHttps = true; break; }
    }
    if (!$isHttps && isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) {
        $isHttps = true;
    }
    $protocol = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/';
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
// 5.1 Cookie Secure se HTTPS (inclui proxies)
$cookieSecure = 0;
if (!empty($_ENV['APP_URL']) && strpos($_ENV['APP_URL'], 'https://') === 0) {
    $cookieSecure = 1;
} else {
    $httpsCandidates = [
        $_SERVER['HTTPS'] ?? null,
        $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? null,
        $_SERVER['HTTP_X_FORWARDED_SSL'] ?? null,
    ];
    foreach ($httpsCandidates as $val) {
        if ($val === 'on' || $val === '1' || $val === 'https') { $cookieSecure = 1; break; }
    }
}
ini_set('session.cookie_secure', $cookieSecure);

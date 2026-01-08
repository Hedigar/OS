<?php
// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_USER', 'u233127180_os');
define('DB_PASS', 'VDJVpn7Zur');
define('DB_NAME', 'u233127180_os');

// Configurações do Sistema
define('APP_NAME', 'Sistema OS - Assistência Técnica');

// Define BASE_URL dinamicamente com base no host da requisição
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
define('BASE_URL', $protocol . '://' . $host . '/');

// Configurações de Sessão
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Mudar para 1 em produção com HTTPS
session_start();
?>

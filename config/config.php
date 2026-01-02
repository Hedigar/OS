<?php
// Configurações do Banco de Dados
define('DB_HOST', 'os_db');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'os');

// Configurações do Sistema
define('APP_NAME', 'Sistema OS - Assistência Técnica');

// Define BASE_URL dinamicamente com base no host da requisição
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
define('BASE_URL', $protocol . '://' . $host . '/');

// Configurações de Sessão
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Mudar para 1 em produção com HTTPS
session_start();
?>

<?php
/**
 * Script utilitário para verificar se o ambiente está configurado corretamente.
 * Pode ser removido após o deploy.
 */

header('Content-Type: text/plain; charset=utf-8');

echo "--- Verificação de Ambiente ---\n\n";

$files = [
    '.env' => 'Arquivo de configuração',
    'app/Core/Autoload.php' => 'Autoloader principal',
    'vendor/autoload.php' => 'Dependências do Composer',
    'public/index.php' => 'Ponto de entrada'
];

foreach ($files as $path => $desc) {
    $exists = file_exists(__DIR__ . '/' . $path);
    echo "[ " . ($exists ? "OK" : "!!") . " ] $desc ($path)\n";
}

echo "\n--- Variáveis de Ambiente Carregadas ---\n";
require_once __DIR__ . '/app/Core/Autoload.php';

$vars = ['APP_NAME', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME'];
foreach ($vars as $v) {
    $val = $_ENV[$v] ?? (getenv($v) ?: 'NÃO DEFINIDA');
    if ($v === 'DB_PASSWORD') $val = '********';
    echo "$v: $val\n";
}

echo "\n--- Versão do PHP ---\n";
echo PHP_VERSION . "\n";

echo "\n--- Extensões Necessárias ---\n";
$exts = ['pdo_mysql', 'mbstring', 'openssl', 'gd'];
foreach ($exts as $e) {
    echo "[ " . (extension_loaded($e) ? "OK" : "!!") . " ] $e\n";
}

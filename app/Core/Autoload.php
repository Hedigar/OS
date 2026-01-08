<?php

/**
 * Autoloader e Inicializador do Ambiente
 */

// 1. Autoloader manual para classes do projeto (PSR-4)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// 2. Carrega o autoload do Composer se existir (para Dompdf, etc)
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

// 3. Carrega variáveis de ambiente
// Tenta usar o Dotenv do Composer, se falhar usa o EnvLoader nativo
if (class_exists('Dotenv\Dotenv')) {
    try {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->safeLoad();
    } catch (\Exception $e) {
        \App\Core\EnvLoader::load(__DIR__ . '/../../');
    }
} else {
    \App\Core\EnvLoader::load(__DIR__ . '/../../');
}

// 4. Carrega as configurações globais
require_once __DIR__ . '/../../config/config.php';

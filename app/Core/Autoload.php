<?php

spl_autoload_register(function ($class) {
    // Converte o namespace para caminho de arquivo
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../'; // app/

    // Verifica se a classe usa o prefixo do namespace
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // Não, move para o próximo autoloader registrado
        return;
    }

    // Obtém o nome da classe relativa
    $relative_class = substr($class, $len);

    // Substitui separadores de namespace por separadores de diretório,
    // anexa .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Se o arquivo existir, requer ele
    if (file_exists($file)) {
        require $file;
    }
});

// Carrega o autoload do Composer
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

// Carrega as configurações
require_once __DIR__ . '/../../config/config.php';

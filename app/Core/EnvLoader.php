<?php

namespace App\Core;

/**
 * Carregador simples de arquivos .env para ambientes onde o Composer 
 * pode ser difícil de gerenciar ou para evitar dependências pesadas.
 */
class EnvLoader
{
    /**
     * Carrega as variáveis de um arquivo .env para o $_ENV e $_SERVER.
     * 
     * @param string $path Caminho para o diretório contendo o arquivo .env
     */
    public static function load(string $path): void
    {
        $file = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.env';

        if (!file_exists($file)) {
            return;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Ignora comentários
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Divide a linha em chave e valor
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);

                // Remove aspas se existirem
                $value = trim($value, '"\'');

                // Define a variável de ambiente se não estiver definida
                if (!array_key_exists($key, $_ENV)) {
                    $_ENV[$key] = $value;
                    putenv("{$key}={$value}");
                }
            }
        }
    }
}

<?php

namespace App\Core;

abstract class Controller
{
    /**
     * Carrega uma view.
     * @param string $view O nome do arquivo da view (sem a extensão .php).
     * @param array $data Dados a serem passados para a view.
     */
    protected function view(string $view, array $data = [])
    {
        // Extrai os dados para que as chaves se tornem variáveis na view
        extract($data);

        $path = __DIR__ . "/../Views/{$view}.php";
        if (file_exists($path)) {
            require $path;
        } else {
            // Em um sistema real, você faria um tratamento de erro mais elegante
            die("View '{$view}' não encontrada.");
        }
    }

    /**
     * Redireciona o usuário para uma URL.
     * @param string $url A URL para redirecionar.
     */
    protected function redirect(string $url)
    {
        header("Location: " . BASE_URL . $url);
        exit();
    }
}

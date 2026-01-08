<?php

namespace App\Core;

/**
 * Classe base para todos os controladores.
 */
abstract class Controller
{
    /**
     * Renderiza uma view.
     * 
     * @param string $view Nome da view (ex: 'auth/login').
     * @param array $data Dados para a view.
     */
    protected function view(string $view, array $data = []): void
    {
        extract($data);

        $path = __DIR__ . "/../Views/{$view}.php";
        
        if (!file_exists($path)) {
            $this->abort(404, "View '{$view}' não encontrada.");
        }

        require $path;
    }

    /**
     * Redireciona para uma rota específica.
     * 
     * @param string $url
     */
    protected function redirect(string $url): void
    {
        header("Location: " . BASE_URL . ltrim($url, '/'));
        exit();
    }

    /**
     * Retorna uma resposta JSON.
     * 
     * @param mixed $data
     * @param int $status
     */
    protected function json(mixed $data, int $status = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit();
    }

    /**
     * Aborta a execução com um código de status e mensagem.
     * 
     * @param int $code
     * @param string $message
     */
    protected function abort(int $code = 404, string $message = ''): void
    {
        http_response_code($code);
        
        if (empty($message)) {
            $messages = [
                403 => 'Acesso Proibido',
                404 => 'Página Não Encontrada',
                500 => 'Erro Interno do Servidor'
            ];
            $message = $messages[$code] ?? 'Erro Desconhecido';
        }

        echo "<h1>{$code} - {$message}</h1>";
        exit();
    }
}

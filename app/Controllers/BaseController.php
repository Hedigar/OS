<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;

abstract class BaseController extends Controller
{
    public function __construct()
    {
        // Verifica se o usuário está logado antes de permitir acesso
        if (!Auth::check()) {
            // Verifica se a requisição é AJAX (para não quebrar a busca de clientes)
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            
            if ($isAjax ) {
                $this->jsonResponse(['error' => 'Sessão expirada'], 401);
            } else {
                $this->redirect('login');
            }
        }
    }

    /**
     * Carrega uma view dentro do layout principal.
     * @param string $view O nome do arquivo da view (sem a extensão .php).
     * @param array $data Dados a serem passados para a view.
     */
    protected function render(string $view, array $data = [])
    {
        // Define o caminho para o conteúdo da view
        $data['content'] = __DIR__ . "/../Views/{$view}.php";
        
        // Carrega o layout principal
        $this->view('layout/main', $data);
    }

    /**
     * Retorna uma resposta JSON limpa
     */
    protected function jsonResponse($data, $status = 200)
    {
        if (ob_get_length()) ob_clean();
        http_response_code($status );
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    protected function requireAjax()
    {
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        if (!$isAjax ) {
            $this->jsonResponse(['error' => 'Requisição inválida'], 400);
        }
    }

    protected function requirePost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Método não permitido'], 405);
        }
    }
}

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
            
            if ($isAjax) {
                $this->jsonResponse(['error' => 'Sessão expirada'], 401);
            } else {
                $this->redirect('login');
            }
        }

        // Verifica se o usuário precisa trocar a senha
        $user = Auth::user();
        if (isset($user['trocar_senha']) && $user['trocar_senha'] == 1) {
            // Usa as constantes definidas no Router para saber onde estamos
            $controller = defined('CURRENT_CONTROLLER') ? CURRENT_CONTROLLER : '';
            $action = defined('CURRENT_ACTION') ? CURRENT_ACTION : '';

            // Lista de ações permitidas durante a troca de senha obrigatória
            $allowed_actions = [
                'UsuarioController@showTrocarSenha',
                'UsuarioController@salvarNovaSenha',
                'AuthController@logout'
            ];

            $current_route = "{$controller}@{$action}";

            // Se não for uma rota permitida, redireciona para a troca de senha
            if (!in_array($current_route, $allowed_actions)) {
                $this->redirect('usuarios/trocar-senha');
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

    protected function requireAdmin()
    {
        if (!Auth::isAdmin()) {
            $this->redirect('dashboard');
        }
    }

    protected function requireTecnico()
    {
        if (!Auth::isTecnico()) {
            $this->redirect('dashboard');
        }
    }
}

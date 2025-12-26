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
            $this->redirect('login');
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
}

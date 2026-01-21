<?php

namespace App\Controllers;

use App\Models\Usuario;
use App\Services\UsuarioService;

class UsuarioController extends BaseController
{
    private $usuarioModel;
    private UsuarioService $service;

    public function __construct()
    {
        parent::__construct();
        
        // Rotas que não exigem ser admin
        $public_actions = ['showTrocarSenha', 'salvarNovaSenha'];
        
        // Usa a constante definida no Router
        $current_action = defined('CURRENT_ACTION') ? CURRENT_ACTION : '';

        // Se não for uma ação pública de usuário, exige admin
        if (!in_array($current_action, $public_actions)) {
            $this->requireAdmin();
        }
        
        $this->usuarioModel = new Usuario();
        $this->service = new UsuarioService();
    }

    // Listar todos os usuários
    public function index()
    {
        $usuarios = $this->usuarioModel->all();
        $this->render('usuario/index', [
            'title' => 'Gerenciar Usuários',
            'usuarios' => $usuarios,
            'current_page' => 'usuarios'
        ]);
    }

    // Mostrar formulário de criação/edição
    public function form()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $usuario = null;
        $title = 'Novo Usuário';

        if ($id) {
            $usuario = $this->usuarioModel->find($id);
            $title = 'Editar Usuário';
            if (!$usuario) {
                $this->redirect('usuarios');
            }
        }

        $this->render('usuario/form', [
            'title' => $title,
            'usuario' => $usuario,
            'current_page' => 'usuarios'
        ]);
    }

    // Salvar novo usuário
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->service->normalizeCreateData($_POST);

            if ($this->usuarioModel->create($data)) {
                $this->redirect('usuarios');
            } else {
                $this->render('usuario/form', ['error' => 'Erro ao salvar usuário.']);
            }
        }
    }

    // Atualizar usuário
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                $this->redirect('usuarios');
            }

            $data = $this->service->normalizeUpdateData($_POST);
            
            // A senha não é mais alterada por aqui, apenas via Resetar Senha na listagem.

            if ($this->usuarioModel->update($id, $data)) {
                $this->redirect('usuarios');
            } else {
                $usuario = $this->usuarioModel->find($id);
                $this->render('usuario/form', ['error' => 'Erro ao atualizar usuário.', 'usuario' => $usuario]);
            }
        }
    }

    // Deletar usuário
    public function destroy()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if ($id && $this->usuarioModel->delete($id)) {
                // Sucesso
            }
        }
        $this->redirect('usuarios');
    }

    // Mostrar formulário de troca de senha obrigatória
    public function showTrocarSenha()
    {
        if (!\App\Core\Auth::check()) {
            $this->redirect('login');
        }
        
        $this->render('usuario/trocar_senha', [
            'title' => 'Trocar Senha Obrigatória'
        ]);
    }

    // Salvar a nova senha definida pelo usuário
    public function salvarNovaSenha()
    {
        if (!\App\Core\Auth::check()) {
            $this->redirect('login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_SESSION['user_id'];
            $nova_senha = $_POST['nova_senha'] ?? null;
            $confirmar_senha = $_POST['confirmar_senha'] ?? null;

            $error = $this->service->validateNewPassword($nova_senha, $confirmar_senha);
            if ($error) {
                $this->render('usuario/trocar_senha', [
                    'title' => 'Trocar Senha Obrigatória',
                    'error' => $error
                ]);
                return;
            }

            if ($this->service->updatePassword($id, $nova_senha)) {
                // Atualizar a sessão para refletir que a senha foi trocada
                $_SESSION['user']['trocar_senha'] = 0;
                $this->redirect('dashboard');
            } else {
                $this->render('usuario/trocar_senha', [
                    'title' => 'Trocar Senha Obrigatória',
                    'error' => 'Erro ao atualizar senha.'
                ]);
            }
        }
    }

    // Resetar senha para o padrão
    public function resetarSenha()
    {
        if (!\App\Core\Auth::check() || !\App\Core\Auth::isAdmin()) {
            $this->redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if ($id) {
                $this->service->resetPasswordDefault($id);
            }
        }
        $this->redirect('usuarios');
    }
}

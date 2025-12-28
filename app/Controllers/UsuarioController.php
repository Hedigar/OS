<?php

namespace App\Controllers;

use App\Models\Usuario;

class UsuarioController extends BaseController
{
    private $usuarioModel;

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
    }

    // Listar todos os usuários
    public function index()
    {
        $usuarios = $this->usuarioModel->all();
        $this->render('usuario/index', [
            'title' => 'Gerenciar Usuários',
            'usuarios' => $usuarios
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
            'usuario' => $usuario
        ]);
    }

    // Salvar novo usuário
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nome' => filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS),
                'email' => filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL),
                'senha' => password_hash('12345678', PASSWORD_DEFAULT),
                'nivel_acesso' => filter_input(INPUT_POST, 'nivel_acesso', FILTER_SANITIZE_SPECIAL_CHARS),
                'trocar_senha' => 1
            ];

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

            $data = [
                'nome' => filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS),
                'email' => filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL),
                'nivel_acesso' => filter_input(INPUT_POST, 'nivel_acesso', FILTER_SANITIZE_SPECIAL_CHARS),
            ];
            
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
            $nova_senha = filter_input(INPUT_POST, 'nova_senha', FILTER_SANITIZE_SPECIAL_CHARS);
            $confirmar_senha = filter_input(INPUT_POST, 'confirmar_senha', FILTER_SANITIZE_SPECIAL_CHARS);

            if (empty($nova_senha) || $nova_senha !== $confirmar_senha) {
                $this->render('usuario/trocar_senha', [
                    'title' => 'Trocar Senha Obrigatória',
                    'error' => 'As senhas não coincidem ou estão vazias.'
                ]);
                return;
            }

            $data = [
                'senha' => password_hash($nova_senha, PASSWORD_DEFAULT),
                'trocar_senha' => 0
            ];

            if ($this->usuarioModel->update($id, $data)) {
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
                $data = [
                    'senha' => password_hash('12345678', PASSWORD_DEFAULT),
                    'trocar_senha' => 1
                ];
                $this->usuarioModel->update($id, $data);
            }
        }
        $this->redirect('usuarios');
    }
}

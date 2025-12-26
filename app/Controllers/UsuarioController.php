<?php

namespace App\Controllers;

use App\Models\Usuario;

class UsuarioController extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        parent::__construct();
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
                'senha' => password_hash(filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS), PASSWORD_DEFAULT),
                'nivel_acesso' => filter_input(INPUT_POST, 'nivel_acesso', FILTER_VALIDATE_INT),
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
                'nivel_acesso' => filter_input(INPUT_POST, 'nivel_acesso', FILTER_VALIDATE_INT),
            ];
            
            // Se uma nova senha foi fornecida, atualiza
            $nova_senha = filter_input(INPUT_POST, 'nova_senha', FILTER_SANITIZE_SPECIAL_CHARS);
            if (!empty($nova_senha)) {
                $data['senha'] = password_hash($nova_senha, PASSWORD_DEFAULT);
            }

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
}

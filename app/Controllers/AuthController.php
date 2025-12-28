<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Usuario;

class AuthController extends Controller
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    public function showLogin()
    {
        if (Auth::check()) {
            $this->redirect('dashboard');
        }
        $this->view('auth/login');
    }

    public function login()
    {
        if (Auth::check()) {
            $this->redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Pegamos os valores brutos e removemos espaços extras nas pontas
            $emailRaw = trim($_POST['email'] ?? '');
            $senhaRaw = $_POST['senha'] ?? ''; // Senha não precisa de trim se aceitar espaços

            // 2. Validamos o formato do e-mail separadamente
            $email = filter_var($emailRaw, FILTER_VALIDATE_EMAIL);

            // 3. Verificamos se estão vazios ou se o e-mail é inválido
            if (!$email || empty($senhaRaw)) {
                $this->view('auth/login', [
                    'error' => 'Preencha todos os campos corretamente (e-mail válido é obrigatório).'
                ]);
                return;
            }

            $user = $this->usuarioModel->findByEmail($email);

            // Use a senha bruta ($senhaRaw) para o password_verify
            if ($user && password_verify($senhaRaw, $user['senha'])) {
                Auth::login($user);
                
                // Redireciona para o dashboard; se precisar trocar senha, 
                // o BaseController interceptará e redirecionará corretamente.
                $this->redirect('dashboard');
            } else {
                $this->view('auth/login', ['error' => 'E-mail ou senha inválidos.']);
            }
        } else {
            $this->redirect('login');
        }
    }

    public function logout()
    {
        Auth::logout();
        $this->redirect('login');
    }
}

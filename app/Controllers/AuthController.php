<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Usuario;
use App\Models\Log;

/**
 * Controlador responsável pela autenticação.
 */
class AuthController extends Controller
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    /**
     * Exibe a página de login.
     */
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('dashboard');
        }
        $this->view('auth/login');
    }

    /**
     * Processa a tentativa de login.
     */
    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect('dashboard');
        }

        $logModel = new Log();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $emailRaw = trim($_POST['email'] ?? '');
            $senhaRaw = $_POST['senha'] ?? '';

            $email = filter_var($emailRaw, FILTER_VALIDATE_EMAIL);

            if (!$email || empty($senhaRaw)) {
                $this->view('auth/login', [
                    'error' => 'Preencha todos os campos corretamente (e-mail válido é obrigatório).'
                ]);
                return;
            }

            $user = $this->usuarioModel->findByEmail($email);

            if ($user && password_verify($senhaRaw, $user['senha'])) {
                Auth::login($user);
                $logModel->registrar($user['id'], "Realizou login no sistema");
                $this->redirect('dashboard');
            } else {
                $logModel->registrar(null, "Tentativa de login falhou", "E-mail: {$emailRaw}");
                $this->view('auth/login', ['error' => 'E-mail ou senha inválidos.']);
            }
        } else {
            $this->redirect('login');
        }
    }

    /**
     * Realiza o logout do usuário.
     */
    public function logout(): void
    {
        $logModel = new Log();
        $logModel->registrar(Auth::id(), "Realizou logout do sistema");
        Auth::logout();
        $this->redirect('login');
    }
}

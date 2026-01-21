<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Services\AuthService;

/**
 * Controlador responsável pela autenticação.
 */
class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Exibe a página de login.
     */
    public function showLogin(): void
    {
        if ($this->authService->isLoggedIn()) {
            $this->redirect('dashboard');
        }
        $this->view('auth/login');
    }

    /**
     * Processa a tentativa de login.
     */
    public function login(): void
    {
        if ($this->authService->isLoggedIn()) {
            $this->redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $emailRaw = $_POST['email'] ?? '';
            $senhaRaw = $_POST['senha'] ?? '';
            $result = $this->authService->attemptLogin((string)$emailRaw, (string)$senhaRaw);
            if (!empty($result['success'])) {
                $this->redirect('dashboard');
            } elseif (($result['error'] ?? '') === 'invalid_input') {
                $this->view('auth/login', [
                    'error' => 'Preencha todos os campos corretamente (e-mail válido é obrigatório).'
                ]);
                return;
            } else {
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
        $this->authService->logout();
        $this->redirect('login');
    }
}

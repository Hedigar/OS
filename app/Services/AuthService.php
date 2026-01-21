<?php

namespace App\Services;

use App\Models\Usuario;
use App\Core\Auth;
use App\Models\Log;

class AuthService
{
    private Usuario $usuarioModel;
    private Log $logModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
        $this->logModel = new Log();
    }

    public function isLoggedIn(): bool
    {
        return Auth::check();
    }

    public function attemptLogin(string $emailRaw, string $senhaRaw): array
    {
        $email = filter_var(trim($emailRaw), FILTER_VALIDATE_EMAIL);
        if (!$email || $senhaRaw === '') {
            return ['success' => false, 'error' => 'invalid_input'];
        }

        $user = $this->usuarioModel->findByEmail($email);
        if ($user && password_verify($senhaRaw, $user['senha'])) {
            Auth::login($user);
            $this->logModel->registrar($user['id'], "Realizou login no sistema");
            return ['success' => true];
        }

        $this->logModel->registrar(null, "Tentativa de login falhou", "E-mail: {$emailRaw}");
        return ['success' => false, 'error' => 'invalid_credentials'];
    }

    public function logout(): void
    {
        $this->logModel->registrar(Auth::id(), "Realizou logout do sistema");
        Auth::logout();
    }
}

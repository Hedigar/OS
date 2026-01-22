<?php

namespace App\Services;

use App\Models\Usuario;
use App\Core\Auth;

class UsuarioService
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    public function normalizeCreateData(array $post): array
    {
        $nivel = filter_var($post['nivel_acesso'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $allowed = ['usuario', 'tecnico', 'admin', 'superadmin'];
        if (!in_array($nivel, $allowed, true)) {
            $nivel = 'usuario';
        }
        if ($nivel === 'superadmin' && !Auth::isSuperAdmin()) {
            $nivel = 'admin';
        }
        return [
            'nome' => filter_var($post['nome'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'email' => filter_var($post['email'] ?? '', FILTER_VALIDATE_EMAIL),
            'senha' => password_hash('12345678', PASSWORD_DEFAULT),
            'nivel_acesso' => $nivel,
            'trocar_senha' => 1
        ];
    }

    public function normalizeUpdateData(array $post): array
    {
        $nivel = filter_var($post['nivel_acesso'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $allowed = ['usuario', 'tecnico', 'admin', 'superadmin'];
        if (!in_array($nivel, $allowed, true)) {
            $nivel = 'usuario';
        }
        if ($nivel === 'superadmin' && !Auth::isSuperAdmin()) {
            $nivel = 'admin';
        }
        return [
            'nome' => filter_var($post['nome'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'email' => filter_var($post['email'] ?? '', FILTER_VALIDATE_EMAIL),
            'nivel_acesso' => $nivel,
        ];
    }

    public function validateNewPassword(?string $nova, ?string $confirmacao): ?string
    {
        if (empty($nova) || empty($confirmacao)) {
            return 'As senhas não coincidem ou estão vazias.';
        }
        if ($nova !== $confirmacao) {
            return 'As senhas não coincidem ou estão vazias.';
        }
        if (strlen($nova) < 8) {
            return 'A senha deve ter pelo menos 8 caracteres.';
        }
        return null;
    }

    public function updatePassword(int $id, string $novaSenha): bool
    {
        $data = [
            'senha' => password_hash($novaSenha, PASSWORD_DEFAULT),
            'trocar_senha' => 0
        ];
        return $this->usuarioModel->update($id, $data);
    }

    public function resetPasswordDefault(int $id): bool
    {
        $data = [
            'senha' => password_hash('12345678', PASSWORD_DEFAULT),
            'trocar_senha' => 1
        ];
        return $this->usuarioModel->update($id, $data);
    }
}

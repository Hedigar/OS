<?php

namespace App\Core;

/**
 * Gerenciamento de autenticação e sessões.
 */
class Auth
{
    /**
     * Inicia a sessão se ainda não tiver sido iniciada.
     */
    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Verifica se o usuário está logado.
     */
    public static function check(): bool
    {
        self::startSession();
        return isset($_SESSION['user_id']);
    }

    /**
     * Retorna o ID do usuário logado.
     */
    public static function id(): ?int
    {
        self::startSession();
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Retorna os dados do usuário logado.
     */
    public static function user(): ?array
    {
        self::startSession();
        return $_SESSION['user'] ?? null;
    }

    /**
     * Verifica se o usuário tem perfil de administrador.
     */
    public static function isAdmin(): bool
    {
        self::startSession();
        $nivel = $_SESSION['user']['nivel_acesso'] ?? '';
        return in_array($nivel, ['admin', 'superadmin', '1', 1], true);
    }

    /**
     * Verifica se o usuário tem perfil de técnico ou superior.
     */
    public static function isTecnico(): bool
    {
        self::startSession();
        $nivel = $_SESSION['user']['nivel_acesso'] ?? 'usuario';
        return self::isAdmin() || $nivel === 'tecnico';
    }

    /**
     * Verifica se o usuário é super administrador.
     */
    public static function isSuperAdmin(): bool
    {
        self::startSession();
        $nivel = $_SESSION['user']['nivel_acesso'] ?? '';
        return $nivel === 'superadmin';
    }

    /**
     * Faz o login do usuário.
     */
    public static function login(array $user): void
    {
        self::startSession();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = $user;
    }

    /**
     * Faz o logout do usuário.
     */
    public static function logout(): void
    {
        self::startSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }
}

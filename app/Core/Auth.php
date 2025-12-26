<?php

namespace App\Core;

class Auth
{
    /**
     * Verifica se o usuário está logado.
     * @return bool
     */
    public static function check()
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Retorna o ID do usuário logado.
     * @return int|null
     */
    public static function id()
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Retorna os dados do usuário logado.
     * @return array|null
     */
    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Faz o login do usuário.
     * @param array $user Dados do usuário.
     */
    public static function login(array $user)
    {
        // Regenera o ID da sessão para prevenir Session Fixation
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = $user;
    }

    /**
     * Faz o logout do usuário.
     */
    public static function logout()
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}

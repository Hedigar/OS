<?php

namespace App\Services;

use App\Core\Auth;

class AccessControlService
{
    public function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public function isAuthenticated(): bool
    {
        return Auth::check();
    }

    public function userMustChangePassword(): bool
    {
        $user = Auth::user();
        return isset($user['trocar_senha']) && (int)$user['trocar_senha'] === 1;
    }

    public function currentRoute(): string
    {
        $controller = defined('CURRENT_CONTROLLER') ? CURRENT_CONTROLLER : '';
        $action = defined('CURRENT_ACTION') ? CURRENT_ACTION : '';
        return "{$controller}@{$action}";
    }

    public function isAllowedWhenChangePassword(string $route): bool
    {
        $allowed = [
            'UsuarioController@showTrocarSenha',
            'UsuarioController@salvarNovaSenha',
            'AuthController@logout'
        ];
        return in_array($route, $allowed, true);
    }

    public function isAdmin(): bool
    {
        return Auth::isAdmin();
    }

    public function isSuperAdmin(): bool
    {
        return Auth::isSuperAdmin();
    }

    public function isTecnico(): bool
    {
        return Auth::isTecnico();
    }

    public function isValidPostReferer(string $referer, string $host): bool
    {
        return empty($referer) || strpos($referer, $host) !== false;
    }
}

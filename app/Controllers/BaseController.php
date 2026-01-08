<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Log;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Controlador base para rotas autenticadas.
 */
abstract class BaseController extends Controller
{
    protected Log $logModel;

    public function __construct()
    {
        $this->logModel = new Log();
        
        // Verifica se o usuário está logado
        if (!Auth::check()) {
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            
            if ($isAjax) {
                $this->json(['error' => 'Sessão expirada'], 401);
            } else {
                $this->redirect('login');
            }
        }

        // Verifica se o usuário precisa trocar a senha
        $user = Auth::user();
        if (isset($user['trocar_senha']) && (int)$user['trocar_senha'] === 1) {
            $controller = defined('CURRENT_CONTROLLER') ? CURRENT_CONTROLLER : '';
            $action = defined('CURRENT_ACTION') ? CURRENT_ACTION : '';

            $allowed_actions = [
                'UsuarioController@showTrocarSenha',
                'UsuarioController@salvarNovaSenha',
                'AuthController@logout'
            ];

            $current_route = "{$controller}@{$action}";

            if (!in_array($current_route, $allowed_actions, true)) {
                $this->redirect('usuarios/trocar-senha');
            }
        }
    }

    /**
     * Renderiza uma view dentro do layout principal.
     */
    protected function render(string $view, array $data = []): void
    {
        $data['content'] = __DIR__ . "/../Views/{$view}.php";
        
        if (!isset($data['user'])) {
            $data['user'] = Auth::user();
        }
        
        $this->view('layout/main', $data);
    }

    /**
     * Exige que a requisição seja AJAX.
     */
    protected function requireAjax(): void
    {
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        if (!$isAjax) {
            $this->json(['error' => 'Requisição inválida'], 400);
        }
    }

    /**
     * Exige que o método da requisição seja POST.
     */
    protected function requirePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método não permitido'], 405);
        }
        
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if (!empty($referer) && strpos($referer, $host) === false) {
            $this->log("Tentativa de CSRF detectada", "Referer: {$referer}");
            $this->json(['error' => 'Origem da requisição inválida'], 403);
        }
    }

    /**
     * Exige privilégios de administrador.
     */
    protected function requireAdmin(): void
    {
        if (!Auth::isAdmin()) {
            $this->redirect('dashboard');
        }
    }

    /**
     * Exige privilégios de técnico.
     */
    protected function requireTecnico(): void
    {
        if (!Auth::isTecnico()) {
            $this->redirect('dashboard');
        }
    }

    /**
     * Renderiza uma view e gera um PDF.
     */
    protected function renderPDF(string $view, array $data = [], string $filename = 'documento.pdf', string $paper = 'a4', string $orientation = 'portrait'): void
    {
        extract($data);

        ob_start();
        $path = __DIR__ . "/../Views/{$view}.php";
        if (!file_exists($path)) {
            $this->abort(404, "View '{$view}' não encontrada.");
        }
        require $path;
        $html = ob_get_clean();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();

        if (ob_get_length()) ob_clean();

        $dompdf->stream($filename, ["Attachment" => false]);
        exit;
    }

    /**
     * Registra uma ação no log.
     */
    protected function log(string $acao, ?string $referencia = null): void
    {
        $this->logModel->registrar(Auth::id(), $acao, $referencia);
    }
}

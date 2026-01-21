<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Log;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Services\AccessControlService;

/**
 * Controlador base para rotas autenticadas.
 */
abstract class BaseController extends Controller
{
    protected Log $logModel;
    protected AccessControlService $access;

    public function __construct()
    {
        $this->logModel = new Log();
        $this->access = new AccessControlService();
        
        if (!$this->access->isAuthenticated()) {
            if ($this->access->isAjax()) {
                $this->json(['error' => 'Sessão expirada'], 401);
            } else {
                $this->redirect('login');
            }
        }

        if ($this->access->userMustChangePassword()) {
            $route = $this->access->currentRoute();
            if (!$this->access->isAllowedWhenChangePassword($route)) {
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
        if (!$this->access->isAjax()) {
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
        if (!$this->access->isValidPostReferer($referer, $host)) {
            $this->log("Tentativa de CSRF detectada", "Referer: {$referer}");
            $this->json(['error' => 'Origem da requisição inválida'], 403);
        }
    }

    /**
     * Exige privilégios de administrador.
     */
    protected function requireAdmin(): void
    {
        if (!$this->access->isAdmin()) {
            $this->redirect('dashboard');
        }
    }

    /**
     * Exige privilégios de técnico.
     */
    protected function requireTecnico(): void
    {
        if (!$this->access->isTecnico()) {
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

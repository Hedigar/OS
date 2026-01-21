<?php

namespace App\Controllers;

use App\Models\ConfiguracaoGeral;
use App\Services\SettingsService;

class ConfiguracoesController extends BaseController
{
    private $configModel;
    private SettingsService $settings;

    public function __construct()
    {
        parent::__construct();
        $this->configModel = new ConfiguracaoGeral();
        $this->settings = new SettingsService();
    }

    public function index()
    {
        $this->render('configuracoes/index', [
            'title' => 'Configurações',
            'current_page' => 'configuracoes'
        ]);
    }

    public function os()
    {
        $imp = $this->settings->getImpressaoOS();
        $this->render('configuracoes/os', [
            'title' => 'Configurações de OS',
            'current_page' => 'configuracoes_os',
            'fonte_tamanho' => $imp['fonte_tamanho'],
            'exibir_observacoes' => $imp['exibir_observacoes'],
            'texto_observacoes' => $imp['texto_observacoes']
        ]);
    }

    public function salvarImpressao()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ok = $this->settings->salvarImpressao($_POST);
            $_SESSION['success'] = $ok ? "Configurações de impressão atualizadas!" : "Falha ao salvar configurações.";
            $this->redirect('configuracoes/os');
        }
    }
}

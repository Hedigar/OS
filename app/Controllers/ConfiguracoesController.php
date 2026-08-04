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

    public function financeiro()
    {
        $fin = $this->settings->getFinanceiro();
        $periodService = new \App\Services\PeriodControlService();
        $fechamentos = $periodService->getClosedPeriods();

        $this->render('configuracoes/financeiro', [
            'title' => 'Configurações Financeiras',
            'current_page' => 'configuracoes_financeiro',
            'nf_porcentagem_produtos' => $fin['nf_porcentagem_produtos'],
            'nf_porcentagem_servicos' => $fin['nf_porcentagem_servicos'],
            'fechamentos' => $fechamentos
        ]);
    }

    public function salvarFinanceiro()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ok = $this->settings->salvarFinanceiro($_POST);
            $_SESSION['success'] = $ok ? "Configurações financeiras atualizadas!" : "Falha ao salvar configurações.";
            $this->redirect('configuracoes/financeiro');
        }
    }

    public function fecharPeriodo()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ano = filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT);
            $mes = filter_input(INPUT_POST, 'mes', FILTER_VALIDATE_INT);
            $observacoes = filter_input(INPUT_POST, 'observacoes', FILTER_SANITIZE_SPECIAL_CHARS);
            $usuarioId = \App\Core\Auth::id() ?: 1;

            if ($ano && $mes) {
                $periodService = new \App\Services\PeriodControlService();
                try {
                    $ok = $periodService->closePeriod($ano, $mes, $usuarioId, $observacoes);
                    $_SESSION['success'] = $ok ? "Período fiscal {$mes}/{$ano} fechado com sucesso!" : "Erro ao fechar período.";
                } catch (\PDOException $e) {
                    $_SESSION['error'] = "Este período já se encontra fechado!";
                }
            } else {
                $_SESSION['error'] = "Mês e Ano são obrigatórios para o fechamento.";
            }
            $this->redirect('configuracoes/financeiro');
        }
    }

    public function reabrirPeriodo()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ano = filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT);
            $mes = filter_input(INPUT_POST, 'mes', FILTER_VALIDATE_INT);

            if ($ano && $mes) {
                $periodService = new \App\Services\PeriodControlService();
                $ok = $periodService->reopenPeriod($ano, $mes);
                $_SESSION['success'] = $ok ? "Período fiscal {$mes}/{$ano} reaberto com sucesso!" : "Erro ao reabrir período.";
            } else {
                $_SESSION['error'] = "Mês e Ano são obrigatórios para a reabertura.";
            }
            $this->redirect('configuracoes/financeiro');
        }
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

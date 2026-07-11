<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Services\FinanceReportService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RelatorioController extends BaseController
{
    private FinanceReportService $financeService;

    public function __construct()
    {
        parent::__construct();
        $this->financeService = new FinanceReportService();
    }

    // Método original mantido para compatibilidade
    public function index()
    {
        Auth::check();
        $this->render('relatorios/financeiro', [
            'title' => 'Relatórios Financeiros',
            'current_page' => 'relatorios'
        ]);
    }

    /**
     * VISÃO 1: Competência (Produção)
     */
    public function competencia()
    {
        Auth::check();
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');

        $dados = $this->financeService->getVisaoCompetencia($dataInicio, $dataFim);

        $this->render('relatorios/competencia', [
            'title' => 'Visão de Competência (Produção)',
            'current_page' => 'relatorios',
            'filtros' => ['data_inicio' => $dataInicio, 'data_fim' => $dataFim],
            'dados' => $dados
        ]);
    }

    /**
     * VISÃO 2: Caixa (Fluxo Financeiro Real)
     */
    public function caixa()
    {
        Auth::check();
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');

        $dados = $this->financeService->getVisaoCaixa($dataInicio, $dataFim);
        $auditoria = $this->financeService->auditarSaldo($dataInicio, $dataFim);

        $this->render('relatorios/caixa', [
            'title' => 'Visão de Caixa (Fluxo Financeiro)',
            'current_page' => 'relatorios',
            'filtros' => ['data_inicio' => $dataInicio, 'data_fim' => $dataFim],
            'dados' => $dados,
            'auditoria' => $auditoria
        ]);
    }

    /**
     * VISÃO 3: Analítica de OS
     */
    public function analitica()
    {
        Auth::check();
        $osIdInicio = $_GET['os_id_inicio'] ?? 1;
        $osIdFim = $_GET['os_id_fim'] ?? 100;

        $dados = $this->financeService->getVisaoAnaliticaOs((int)$osIdInicio, (int)$osIdFim);

        $this->render('relatorios/analitica', [
            'title' => 'Visão Analítica de OS',
            'current_page' => 'relatorios',
            'filtros' => ['os_id_inicio' => $osIdInicio, 'os_id_fim' => $osIdFim],
            'dados' => $dados
        ]);
    }

    /**
     * VISÃO 4: Entradas Órfãs
     */
    public function orfas()
    {
        Auth::check();
        $dados = $this->financeService->getVisaoEntradasOrfas();

        $this->render('relatorios/orfas', [
            'title' => 'Visão de Entradas Órfãs',
            'current_page' => 'relatorios',
            'dados' => $dados
        ]);
    }

    /**
     * Ação de Auditoria
     */
    public function auditar()
    {
        Auth::check();
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');

        $auditoria = $this->financeService->auditarSaldo($dataInicio, $dataFim);

        header('Content-Type: application/json');
        echo json_encode($auditoria);
        exit;
    }

    // Métodos legados mantidos para compatibilidade
    public function clientes()
    {
        Auth::check();
        $this->render('relatorios/clientes', [
            'title' => 'Relatório de Clientes',
            'current_page' => 'relatorios'
        ]);
    }

    public function crm()
    {
        Auth::check();
        $this->render('relatorios/crm', [
            'title' => 'Relatório de CRM',
            'current_page' => 'relatorios'
        ]);
    }
}

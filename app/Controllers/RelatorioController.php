<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\OrdemServico;
use App\Models\AtendimentoExterno;
use App\Services\RelatorioService;

class RelatorioController extends BaseController
{
    private RelatorioService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new RelatorioService();
    }

    public function index()
    {
        Auth::check();
        
        // Filtros de data (padrão: mês atual)
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');

        $financeiro = $this->service->resumoFinanceiro($dataInicio, $dataFim);
        $statusResumo = $this->service->osPorStatus($dataInicio, $dataFim);
        $atendimentos = $this->service->atendimentosResumo($dataInicio, $dataFim);
        $custosPorOS = $this->service->custosPorOS($dataInicio, $dataFim);
        $custosPorAtendimento = $this->service->custosPorAtendimento($dataInicio, $dataFim);
        $custosOSCaixa = $this->service->custosPorOSCaixa($dataInicio, $dataFim);
        $custosAtendimentoCaixa = $this->service->custosPorAtendimentoCaixa($dataInicio, $dataFim);
        $nfsOSCaixa = $this->service->nfsPorOSCaixa($dataInicio, $dataFim);
        $nfsAtendimentoCaixa = $this->service->nfsPorAtendimentoCaixa($dataInicio, $dataFim);
        $lucroReal = $this->service->lucroReal($dataInicio, $dataFim);
        $clientesResumo = $this->service->clientesNovos($dataInicio, $dataFim);
        $novosClientes = $this->service->getNovosClientes($dataInicio, $dataFim);
        $clientesQueVoltaram = $this->service->getClientesQueVoltaram($dataInicio, $dataFim);
        $itensVendidos = $this->service->itensVendidos($dataInicio, $dataFim);

        $this->render('relatorios/index', [
            'title' => 'Relatórios e Resumos',
            'current_page' => 'relatorios',
            'filtros' => [
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim
            ],
            'financeiro' => $financeiro,
            'statusResumo' => $statusResumo,
            'atendimentos' => $atendimentos,
            'custosPorOS' => $custosPorOS,
            'custosPorAtendimento' => $custosPorAtendimento,
            'custosOSCaixa' => $custosOSCaixa,
            'custosAtendimentoCaixa' => $custosAtendimentoCaixa,
            'nfsOSCaixa' => $nfsOSCaixa,
            'nfsAtendimentoCaixa' => $nfsAtendimentoCaixa,
            'lucroReal' => $lucroReal,
            'clientesResumo' => $clientesResumo,
            'novosClientes' => $novosClientes,
            'clientesQueVoltaram' => $clientesQueVoltaram,
            'itensVendidos' => $itensVendidos
        ]);
    }

    public function clientes()
    {
        Auth::check();
        
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');

        $novosClientes = $this->service->getNovosClientes($dataInicio, $dataFim);
        $clientesQueVoltaram = $this->service->getClientesQueVoltaram($dataInicio, $dataFim);

        $this->render('relatorios/clientes', [
            'title' => 'Relatório de Clientes',
            'current_page' => 'relatorios',
            'filtros' => [
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim
            ],
            'novosClientes' => $novosClientes,
            'clientesQueVoltaram' => $clientesQueVoltaram
        ]);
    }
}

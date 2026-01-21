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

        $this->render('relatorios/index', [
            'title' => 'Relatórios e Resumos',
            'current_page' => 'relatorios',
            'filtros' => [
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim
            ],
            'financeiro' => $financeiro,
            'statusResumo' => $statusResumo,
            'atendimentos' => $atendimentos
        ]);
    }
}

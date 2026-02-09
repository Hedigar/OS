<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Services\ProdutividadeService;
use App\Models\AtividadePessoal;

class ProdutividadeController extends BaseController
{
    private ProdutividadeService $service;
    private AtividadePessoal $atividadeModel;

    public function __construct()
    {
        parent::__construct();
        $this->service = new ProdutividadeService();
        $this->atividadeModel = new AtividadePessoal();
    }

    public function index()
    {
        $user = Auth::user();
        $hoje = date('Y-m-d');
        $dia = $this->service->dashboardDia((int)$user['id'], $hoje);
        $semana = $this->service->dashboardSemana((int)$user['id'], $hoje);
        $mes = $this->service->dashboardMes((int)$user['id'], date('Y-m'));
        $this->render('produtividade/index', [
            'title' => 'Produtividade Pessoal',
            'current_page' => 'produtividade',
            'user' => $user,
            'dia' => $dia,
            'semana' => $semana,
            'mes' => $mes
        ]);
    }

    public function form()
    {
        $this->render('produtividade/form', [
            'title' => 'Registrar Atividade',
            'current_page' => 'produtividade'
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('produtividade');
        }
        $usuarioId = (int)Auth::id();
        $dataHora = trim((string)($_POST['data_hora'] ?? ''));
        $tipo = trim((string)($_POST['tipo'] ?? ''));
        $descricao = trim((string)($_POST['descricao'] ?? ''));
        $tempo = (int)($_POST['tempo_minutos'] ?? 0);
        $local = trim((string)($_POST['local'] ?? 'Presencial'));
        $obs = trim((string)($_POST['observacoes'] ?? ''));
        if ($dataHora === '' || $tipo === '' || $tempo <= 0) {
            $this->redirect('produtividade/form');
        }
        $categoria = $this->service->categorizar($tipo);
        $this->atividadeModel->createManual($usuarioId, [
            'data_hora' => $dataHora,
            'tipo' => $tipo,
            'descricao' => $descricao,
            'tempo_minutos' => $tempo,
            'local' => $local,
            'categoria' => $categoria,
            'observacoes' => $obs
        ]);
        $this->log('Registrou atividade pessoal', $tipo);
        $this->redirect('produtividade');
    }

    public function print()
    {
        $user = Auth::user();
        $hoje = date('Y-m-d');
        $dia = $this->service->dashboardDia((int)$user['id'], $hoje);
        $this->renderPDF('produtividade/print', [
            'title' => 'Produtividade - Diário',
            'user' => $user,
            'dia' => $dia
        ], "Produtividade_Diario_" . date('Ymd') . ".pdf");
    }
}

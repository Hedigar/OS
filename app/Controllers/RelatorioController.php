<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\OrdemServico;
use App\Models\AtendimentoExterno;

class RelatorioController extends BaseController
{
    public function index()
    {
        Auth::check();
        
        $osModel = new OrdemServico();
        $db = $osModel->getConnection();

        // Filtros de data (padrão: mês atual)
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');

        // 1. Resumo Financeiro de OS
        // Corrigido: Usando parâmetros distintos para a subquery para evitar erro de "Invalid parameter number"
        $sqlFinanceiro = "SELECT 
                            SUM(valor_total_os) as total_bruto,
                            SUM(valor_total_produtos) as total_produtos,
                            SUM(valor_total_servicos) as total_servicos,
                            (SELECT SUM(i.quantidade * i.custo) 
                             FROM itens_ordem_servico i 
                             JOIN ordens_servico o ON i.ordem_servico_id = o.id 
                             WHERE o.status_atual_id = 5 AND o.ativo = 1 
                             AND DATE(o.created_at) BETWEEN :sub_start AND :sub_end) as total_custo
                          FROM ordens_servico 
                          WHERE status_atual_id = 5 AND ativo = 1 
                          AND DATE(created_at) BETWEEN :start AND :end";
        
        $stmt = $db->prepare($sqlFinanceiro);
        $stmt->execute([
            'start' => $dataInicio, 
            'end' => $dataFim,
            'sub_start' => $dataInicio,
            'sub_end' => $dataFim
        ]);
        $financeiro = $stmt->fetch();

        // 2. OS por Status
        $sqlStatus = "SELECT s.nome, COUNT(os.id) as total, s.cor 
                      FROM status_os s
                      LEFT JOIN ordens_servico os ON os.status_atual_id = s.id AND os.ativo = 1 AND DATE(os.created_at) BETWEEN :start AND :end
                      GROUP BY s.id
                      ORDER BY s.ordem ASC";
        $stmtStatus = $db->prepare($sqlStatus);
        $stmtStatus->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $statusResumo = $stmtStatus->fetchAll();

        // 3. Atendimentos Externos
        $sqlAtendimentos = "SELECT 
                                COUNT(*) as total,
                                SUM(valor_total) as valor_total,
                                SUM(valor_deslocamento) as valor_deslocamento
                            FROM atendimentos_externos 
                            WHERE ativo = 1 AND DATE(created_at) BETWEEN :start AND :end";
        $stmtAtend = $db->prepare($sqlAtendimentos);
        $stmtAtend->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $atendimentos = $stmtAtend->fetch();

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

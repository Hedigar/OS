<?php

namespace App\Controllers;

use App\Models\PagamentoTransacao;
use App\Models\Despesa;
use App\Core\Auth;

class CaixaController extends BaseController
{
    private PagamentoTransacao $pgModel;
    private Despesa $despesaModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
        $this->pgModel = new PagamentoTransacao();
        $this->despesaModel = new Despesa();
    }

    public function index()
    {
        Auth::check();

        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-d');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-d');

        $db = $this->pgModel->getConnection();

        $stmtIn = $db->prepare("SELECT * FROM pagamentos_transacoes WHERE ativo = 1 AND DATE(created_at) BETWEEN :start AND :end ORDER BY created_at DESC");
        $stmtIn->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $entradas = $stmtIn->fetchAll() ?: [];

        $stmtInSum = $db->prepare("SELECT SUM(valor_liquido) as total_liquido, SUM(valor_bruto) as total_bruto, SUM(valor_taxa) as total_taxa FROM pagamentos_transacoes WHERE ativo = 1 AND DATE(created_at) BETWEEN :start AND :end");
        $stmtInSum->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $sumIn = $stmtInSum->fetch() ?: ['total_liquido' => 0, 'total_bruto' => 0, 'total_taxa' => 0];

        $stmtOut = $db->prepare("SELECT d.*, c.nome as categoria_nome FROM despesas d LEFT JOIN despesas_categorias c ON d.categoria_id = c.id WHERE d.ativo = 1 AND d.status_pagamento = 'pago' AND DATE(d.data_despesa) BETWEEN :start AND :end ORDER BY d.data_despesa DESC");
        $stmtOut->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $saidas = $stmtOut->fetchAll() ?: [];

        $stmtOutSum = $db->prepare("SELECT SUM(valor) as total FROM despesas WHERE ativo = 1 AND status_pagamento = 'pago' AND DATE(data_despesa) BETWEEN :start AND :end");
        $stmtOutSum->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $sumOut = $stmtOutSum->fetch() ?: ['total' => 0];

        $totalEntradas = (float)($sumIn['total_liquido'] ?? 0);
        $totalTaxas = (float)($sumIn['total_taxa'] ?? 0);
        $totalSaidas = (float)($sumOut['total'] ?? 0);

        // --- NOVO: Cálculo de Custos de OS/Atendimentos Finalizados no período ---
        // Isso garante que o lucro real seja refletido no saldo do caixa
        
        // 1. Custo de Peças de OS Finalizadas no período
        $sqlCustoPecas = "SELECT SUM(i.quantidade * COALESCE(NULLIF(i.valor_custo, 0), NULLIF(i.custo, 0), 0))
                          FROM itens_ordem_servico i
                          JOIN ordens_servico o ON i.ordem_servico_id = o.id
                          WHERE o.status_atual_id = 5 AND o.ativo = 1 AND i.ativo = 1
                          AND DATE(COALESCE(o.updated_at, o.created_at)) BETWEEN :start AND :end";
        $stmtCusto = $db->prepare($sqlCustoPecas);
        $stmtCusto->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $custoPecasOS = (float)$stmtCusto->fetchColumn();

        // 2. Custo de Peças de Atendimentos Externos Finalizados no período
        $sqlCustoAt = "SELECT SUM(i.quantidade * COALESCE(NULLIF(i.valor_custo, 0), NULLIF(i.custo, 0), 0))
                       FROM itens_ordem_servico i
                       JOIN atendimentos_externos a ON i.atendimento_externo_id = a.id
                       WHERE a.status = 'finalizado' AND a.ativo = 1 AND i.ativo = 1
                       AND DATE(COALESCE(a.updated_at, a.created_at)) BETWEEN :start AND :end";
        $stmtCustoAt = $db->prepare($sqlCustoAt);
        $stmtCustoAt->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $custoPecasAt = (float)$stmtCustoAt->fetchColumn();

        // 3. Custo de Impostos (NF) de OS/Atendimentos Finalizados no período
        $sqlNF = "SELECT 
                    (SELECT COALESCE(SUM(valor_taxa_nf), 0) FROM ordens_servico WHERE status_atual_id = 5 AND ativo = 1 AND DATE(COALESCE(updated_at, created_at)) BETWEEN :s1 AND :e1) +
                    (SELECT COALESCE(SUM(valor_taxa_nf), 0) FROM atendimentos_externos WHERE status = 'finalizado' AND ativo = 1 AND DATE(COALESCE(updated_at, created_at)) BETWEEN :s2 AND :e2)
                  as total_nf";
        $stmtNF = $db->prepare($sqlNF);
        $stmtNF->execute(['s1' => $dataInicio, 'e1' => $dataFim, 's2' => $dataInicio, 'e2' => $dataFim]);
        $custoNF = (float)$stmtNF->fetchColumn();

        $totalCustosVenda = $custoPecasOS + $custoPecasAt + $custoNF;
        $saldo = $totalEntradas - $totalSaidas - $totalCustosVenda;

        $this->render('caixa/index', [
            'title' => 'Caixa',
            'current_page' => 'caixa',
            'filtros' => [
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim
            ],
            'entradas' => $entradas,
            'saidas' => $saidas,
            'totais' => [
                'entradas' => $totalEntradas,
                'taxas' => $totalTaxas,
                'saidas' => $totalSaidas,
                'custos_venda' => $totalCustosVenda,
                'saldo' => $saldo
            ]
        ]);
    }

    public function revisar()
    {
        Auth::check();

        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');

        $db = $this->pgModel->getConnection();

        $stmtTx = $db->prepare("SELECT * FROM pagamentos_transacoes WHERE ativo = 1 AND DATE(created_at) BETWEEN :start AND :end ORDER BY created_at DESC");
        $stmtTx->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $transacoes = $stmtTx->fetchAll() ?: [];

        $stmtOsPend = $db->prepare("SELECT os.id, os.cliente_id, os.valor_total_os, os.status_pagamento, os.status_entrega, c.nome_completo as cliente_nome
                                    FROM ordens_servico os
                                    JOIN clientes c ON c.id = os.cliente_id
                                    WHERE os.ativo = 1 AND os.status_entrega = 'entregue' AND os.status_pagamento <> 'pago' AND DATE(os.created_at) BETWEEN :start AND :end
                                    ORDER BY os.id DESC");
        $stmtOsPend->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $osPendentes = $stmtOsPend->fetchAll() ?: [];

        $stmtOsPagas = $db->prepare("SELECT os.id, os.cliente_id, os.valor_total_os, os.status_pagamento, os.status_entrega, c.nome_completo as cliente_nome
                                    FROM ordens_servico os
                                    JOIN clientes c ON c.id = os.cliente_id
                                    WHERE os.ativo = 1 AND os.status_entrega = 'entregue' AND os.status_pagamento = 'pago' AND DATE(os.created_at) BETWEEN :start AND :end
                                    ORDER BY os.id DESC");
        $stmtOsPagas->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $osPagasEntregues = $stmtOsPagas->fetchAll() ?: [];

        $this->render('caixa/revisar', [
            'title' => 'Revisão do Caixa',
            'current_page' => 'caixa',
            'filtros' => [
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim
            ],
            'transacoes' => $transacoes,
            'osPendentes' => $osPendentes,
            'osPagasEntregues' => $osPagasEntregues
        ]);
    }

    public function marcarVerificado()
    {
        $this->requireAjax();
        $this->requirePost();
        $this->requireAdmin();

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->json(['error' => 'ID inválido'], 400);
        }

        $ok = $this->pgModel->marcarVerificado($id, Auth::id());
        if (!$ok) {
            $this->json(['error' => 'Falha ao atualizar'], 500);
        }

        $this->json(['success' => true]);
    }
}

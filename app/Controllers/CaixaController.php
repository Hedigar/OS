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

        // --- FONTE ÚNICA: Cálculo de Custos SOMENTE via tabela fluxo_caixa tipo='custo' ---
        // Elimina cálculos alternativos baseados em created_at das tabelas-fonte.
        // Data a ser usada: fc.data (data de aprovação/competência, gravada no lançamento).
        $fluxoCaixaModel = new \App\Models\FluxoCaixa();
        $fcDb = $fluxoCaixaModel->getConnection();
        $sqlCusto = "SELECT COALESCE(SUM(fc.valor), 0)
                     FROM fluxo_caixa fc
                     LEFT JOIN pagamentos_transacoes pt 
                            ON fc.referencia_tipo = 'pagamento' AND fc.referencia_id = pt.id
                     LEFT JOIN itens_ordem_servico ios_os
                            ON fc.referencia_tipo = 'item_os' AND fc.referencia_id = ios_os.id
                     LEFT JOIN itens_ordem_servico ios_at
                            ON fc.referencia_tipo = 'item_atendimento' AND fc.referencia_id = ios_at.id
                     WHERE fc.tipo = 'custo'
                       AND fc.data BETWEEN :start AND :end
                       AND (
                           fc.referencia_tipo IN ('taxa_nf_os', 'taxa_nf_atendimento', 'despesa', 'outros')
                           OR (fc.referencia_tipo = 'pagamento' AND pt.id IS NOT NULL AND pt.ativo = 1)
                           OR (fc.referencia_tipo = 'item_os' AND ios_os.id IS NOT NULL AND ios_os.ativo = 1)
                           OR (fc.referencia_tipo = 'item_atendimento' AND ios_at.id IS NOT NULL AND ios_at.ativo = 1)
                       )";
        $stmtCusto = $fcDb->prepare($sqlCusto);
        $stmtCusto->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $totalCustosVenda = (float)$stmtCusto->fetchColumn();

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

<?php

namespace App\Services;

use App\Models\OrdemServico;
use App\Models\AtendimentoExterno;
use App\Models\PagamentoTransacao;
use App\Models\FluxoCaixa;
use App\Models\Despesa;

class FinanceReportService
{
    private OrdemServico $osModel;
    private AtendimentoExterno $atendimentoModel;
    private PagamentoTransacao $pagamentoModel;
    private FluxoCaixa $fluxoCaixaModel;
    private Despesa $despesaModel;

    public function __construct()
    {
        $this->osModel = new OrdemServico();
        $this->atendimentoModel = new AtendimentoExterno();
        $this->pagamentoModel = new PagamentoTransacao();
        $this->fluxoCaixaModel = new FluxoCaixa();
        $this->despesaModel = new Despesa();
    }

    /**
     * Retorna o custo total de uma OS (soma dos custos dos itens)
     */
    private function getTotalCustoByOs(int $osId): float
    {
        $db = $this->osModel->getConnection();

        // Status aprovados para computar custos (os mesmos do DRE)
        $statusAprovados = [4, 5, 8, 11, 12, 14, 15];
        $placeholders = implode(',', array_fill(0, count($statusAprovados), '?'));

        $sql = "SELECT COALESCE(SUM(ios.quantidade * COALESCE(NULLIF(ios.valor_custo, 0), NULLIF(ios.custo, 0), 0)), 0) 
                FROM itens_ordem_servico ios
                JOIN ordens_servico os ON ios.ordem_servico_id = os.id
                WHERE ios.ordem_servico_id = ? 
                AND ios.ativo = 1 
                AND os.status_atual_id IN ($placeholders)";
        
        $stmt = $db->prepare($sql);
        $params = array_merge([$osId], $statusAprovados);
        $stmt->execute($params);
        return (float)($stmt->fetchColumn() ?: 0);
    }

    /**
     * Retorna o custo total de taxas de uma OS (taxa NF)
     */
    private function getTotalTaxasByOs(int $osId): float
    {
        $db = $this->osModel->getConnection();
        $sql = "SELECT COALESCE(valor_taxa_nf, 0) FROM ordens_servico WHERE id = ? AND ativo = 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$osId]);
        return (float)($stmt->fetchColumn() ?: 0);
    }

    /**
     * Retorna o total pago para uma OS (soma dos pagamentos)
     */
    private function getTotalPagoByOs(int $osId): float
    {
        return $this->pagamentoModel->sumByOrigem('os', $osId);
    }

    /**
     * ======================================
     * VISÃO 1: Competência (Produção)
     * Objetivo: Saber quanto a empresa produziu de riqueza no período
     * ======================================
     */
    public function getVisaoCompetencia(string $dataInicio, string $dataFim): array
    {
        $db = $this->osModel->getConnection();

        // Status que representam serviço efetivo/aprovado/concluído para o DRE
        $statusAprovados = [
            4,  // Em Execucao
            5,  // Finalizada
            8,  // Para POA autorizado
            11, // Comprar Peça
            12, // Aguardando Peça
            14, // Autorizado
            15  // Diagnóstico Finalizado
        ];
        $placeholders = implode(',', array_fill(0, count($statusAprovados), '?'));

        // Fetch OS aprovadas/finalizadas
        $sqlOs = "SELECT 
                    os.id,
                    os.valor_total_os,
                    os.valor_desconto,
                    os.valor_taxa_nf,
                    os.defeito_relatado,
                    c.nome_completo as cliente,
                    DATE(COALESCE(h.created_at, os.updated_at)) as data_finalizacao
                FROM ordens_servico os
                JOIN clientes c ON os.cliente_id = c.id
                LEFT JOIN (
                    SELECT ordem_servico_id, MAX(created_at) as created_at
                    FROM ordens_servico_status_historico
                    WHERE status_id = ?
                    GROUP BY ordem_servico_id
                ) h ON os.id = h.ordem_servico_id
                WHERE os.ativo = 1 
                AND os.status_atual_id IN ($placeholders)
                AND DATE(COALESCE(h.created_at, os.updated_at)) BETWEEN ? AND ?
                ORDER BY h.created_at DESC, os.updated_at DESC";
        
        $stmtOs = $db->prepare($sqlOs);
        $params = array_merge([OrdemServico::STATUS_FINALIZADA], $statusAprovados, [$dataInicio, $dataFim]);
        $stmtOs->execute($params);
        $osFinalizadas = $stmtOs->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        // Fetch atendimentos externos concluídos
        $sqlAtendimentos = "SELECT 
                    ae.id,
                    (COALESCE(ae.valor_total, 0) + COALESCE(ae.valor_deslocamento, 0)) as valor_total_os,
                    0 as valor_desconto,
                    ae.valor_taxa_nf,
                    ae.descricao_problema as defeito_relatado,
                    c.nome_completo as cliente,
                    DATE(COALESCE(MAX(pt.created_at), ae.created_at)) as data_finalizacao
                FROM atendimentos_externos ae
                JOIN clientes c ON ae.cliente_id = c.id
                LEFT JOIN pagamentos_transacoes pt ON pt.tipo_origem = 'atendimento' AND pt.origem_id = ae.id
                WHERE ae.ativo = 1 
                AND ae.status = ?
                GROUP BY ae.id, ae.created_at, ae.valor_total, ae.valor_deslocamento, ae.valor_taxa_nf, ae.descricao_problema, c.nome_completo
                HAVING DATE(COALESCE(MAX(pt.created_at), ae.created_at)) BETWEEN ? AND ?
                ORDER BY data_finalizacao DESC";
        
        $stmtAtendimentos = $db->prepare($sqlAtendimentos);
        $stmtAtendimentos->execute(['concluido', $dataInicio, $dataFim]);
        $atendimentosConcluidos = $stmtAtendimentos->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $itens = [];
        $totalOs = 0;
        $totalAtendimentos = 0;

        foreach ($osFinalizadas as $os) {
            $valorFaturado = (float)($os['valor_total_os'] ?? 0) - (float)($os['valor_desconto'] ?? 0);
            $custosPecas = $this->getTotalCustoByOs($os['id']);
            $custosTaxas = $this->getTotalTaxasByOs($os['id']);
            $lucroPrejuizo = $valorFaturado - $custosPecas - $custosTaxas;

            $itens[] = [
                'tipo' => 'os',
                'id' => $os['id'],
                'data_finalizacao' => $os['data_finalizacao'],
                'cliente' => $os['cliente'],
                'defeito_relatado' => $os['defeito_relatado'],
                'valor_faturado' => $valorFaturado,
                'custos_pecas' => $custosPecas,
                'custos_taxas' => $custosTaxas,
                'lucro_prejuizo' => $lucroPrejuizo
            ];

            $totalOs += $valorFaturado;
        }

        foreach ($atendimentosConcluidos as $atendimento) {
            $valorFaturado = (float)($atendimento['valor_total_os'] ?? 0) - (float)($atendimento['valor_desconto'] ?? 0);
            $custosPecas = $this->getTotalCustoByAtendimento($atendimento['id']);
            $custosTaxas = (float)($atendimento['valor_taxa_nf'] ?? 0);
            $lucroPrejuizo = $valorFaturado - $custosPecas - $custosTaxas;

            $itens[] = [
                'tipo' => 'atendimento',
                'id' => $atendimento['id'],
                'data_finalizacao' => $atendimento['data_finalizacao'],
                'cliente' => $atendimento['cliente'],
                'defeito_relatado' => $atendimento['defeito_relatado'],
                'valor_faturado' => $valorFaturado,
                'custos_pecas' => $custosPecas,
                'custos_taxas' => $custosTaxas,
                'lucro_prejuizo' => $lucroPrejuizo
            ];

            $totalAtendimentos += $valorFaturado;
        }

        // Sort items by date descending
        usort($itens, function($a, $b) {
            return strtotime($b['data_finalizacao']) - strtotime($a['data_finalizacao']);
        });

        return [
            'itens' => $itens,
            'total_os' => $totalOs,
            'total_atendimentos' => $totalAtendimentos,
            'total' => $totalOs + $totalAtendimentos
        ];
    }

    /**
     * Retorna o custo total de um atendimento externo
     */
    private function getTotalCustoByAtendimento(int $atendimentoId): float
    {
        $db = $this->osModel->getConnection();
        $sql = "SELECT COALESCE(SUM(quantidade * COALESCE(NULLIF(valor_custo, 0), NULLIF(custo, 0), 0)), 0) 
                FROM itens_ordem_servico 
                WHERE atendimento_externo_id = ? AND ativo = 1";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$atendimentoId]);
        return (float)($stmt->fetchColumn() ?: 0);
    }

    /**
     * ======================================
     * VISÃO 2: Caixa (Fluxo Financeiro Real)
     * Objetivo: Bater com o saldo do banco
     * ======================================
     */
    public function getVisaoCaixa(string $dataInicio, string $dataFim): array
    {
        $db = $this->osModel->getConnection();
        
        // Entradas (pagamentos ativos)
        $sqlEntradas = "SELECT 
                            pt.id,
                            pt.tipo_origem,
                            pt.origem_id,
                            pt.valor_bruto,
                            pt.valor_liquido,
                            pt.valor_taxa as taxa_cartao,
                            pt.created_at as data_transacao,
                            CASE 
                                WHEN pt.tipo_origem = 'os' THEN os.defeito_relatado
                                WHEN pt.tipo_origem = 'atendimento' THEN ae.descricao_problema
                                ELSE 'Entrada'
                            END as descricao,
                            CASE 
                                WHEN pt.tipo_origem = 'os' THEN c.nome_completo
                                WHEN pt.tipo_origem = 'atendimento' THEN c_at.nome_completo
                                ELSE ''
                            END as cliente
                        FROM pagamentos_transacoes pt
                        LEFT JOIN ordens_servico os ON pt.tipo_origem = 'os' AND pt.origem_id = os.id
                        LEFT JOIN atendimentos_externos ae ON pt.tipo_origem = 'atendimento' AND pt.origem_id = ae.id
                        LEFT JOIN clientes c ON os.cliente_id = c.id
                        LEFT JOIN clientes c_at ON ae.cliente_id = c_at.id
                        WHERE pt.ativo = 1 
                        AND DATE(pt.created_at) BETWEEN ? AND ?
                        ORDER BY pt.created_at DESC";
        
        $stmtEntradas = $db->prepare($sqlEntradas);
        $stmtEntradas->execute([$dataInicio, $dataFim]);
        $entradas = $stmtEntradas->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        
        // Saídas (despesas, custos, etc.)
        $sqlSaidas = "SELECT 
                        d.id,
                        'despesa' as tipo_origem,
                        d.id as origem_id,
                        d.valor,
                        d.descricao,
                        d.data_despesa as data_transacao,
                        dc.nome as categoria,
                        NULL as origem_id_relacionada
                    FROM despesas d
                    JOIN despesas_categorias dc ON d.categoria_id = dc.id
                    WHERE d.ativo = 1 
                    AND DATE(d.data_despesa) BETWEEN ? AND ?
                    UNION ALL
                    SELECT 
                        fc.id,
                        fc.referencia_tipo as tipo_origem,
                        fc.referencia_id as origem_id,
                        fc.valor,
                        CASE 
                            WHEN fc.referencia_tipo = 'item_os' THEN CONCAT(COALESCE(ios.descricao, 'Custo Item'), ' (OS #', ios.ordem_servico_id, ')')
                            WHEN fc.referencia_tipo = 'item_atendimento' THEN CONCAT(COALESCE(ios.descricao, 'Custo Item'), ' (Atend #', ios.atendimento_externo_id, ')')
                            ELSE 'Saída'
                        END as descricao,
                        fc.data as data_transacao,
                        '' as categoria,
                        CASE 
                            WHEN fc.referencia_tipo = 'item_os' THEN ios.ordem_servico_id
                            WHEN fc.referencia_tipo = 'item_atendimento' THEN ios.atendimento_externo_id
                            ELSE NULL
                        END as origem_id_relacionada
                    FROM fluxo_caixa fc
                    LEFT JOIN itens_ordem_servico ios 
                        ON ((fc.referencia_tipo = 'item_os' AND fc.referencia_id = ios.id)
                        OR (fc.referencia_tipo = 'item_atendimento' AND fc.referencia_id = ios.id))
                    WHERE fc.tipo = 'custo'
                    AND DATE(fc.data) BETWEEN ? AND ?
                    AND (
                        fc.referencia_tipo NOT IN ('item_os', 'item_atendimento')
                        OR (ios.id IS NOT NULL AND ios.ativo = 1)
                    )
                    ORDER BY data_transacao DESC";
        
        $stmtSaidas = $db->prepare($sqlSaidas);
        $stmtSaidas->execute([$dataInicio, $dataFim, $dataInicio, $dataFim]);
        $saidas = $stmtSaidas->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        // Calculate DRE values
        $entradaBruta = array_sum(array_column($entradas, 'valor_bruto'));
        $deducoesVenda = array_sum(array_column($entradas, 'taxa_cartao'));
        $receitaLiquida = $entradaBruta - $deducoesVenda;
        $custosDiretos = array_sum(array_column($saidas, 'valor'));
        $resultadoFinal = $receitaLiquida - $custosDiretos;

        return [
            'entradas' => $entradas,
            'saidas' => $saidas,
            'entrada_bruta' => $entradaBruta,
            'deducoes_venda' => $deducoesVenda,
            'receita_liquida' => $receitaLiquida,
            'custos_diretos' => $custosDiretos,
            'resultado_final' => $resultadoFinal
        ];
    }

    /**
     * ======================================
     * VISÃO 3: Analítica de OS (Individual e por Range)
     * Objetivo: Avaliar a rentabilidade de um conjunto de ordens
     * ======================================
     */
    public function getVisaoAnaliticaOs(int $osIdInicio, int $osIdFim): array
    {
        $db = $this->osModel->getConnection();

        // Status permitidos para análise de lucratividade (mesmos do DRE)
        $statusAprovados = [4, 5, 8, 11, 12, 14, 15];
        $placeholders = implode(',', array_fill(0, count($statusAprovados), '?'));

        $sql = "SELECT 
                    os.id,
                    os.valor_total_os,
                    os.valor_desconto,
                    os.valor_taxa_nf,
                    os.defeito_relatado,
                    os.status_atual_id,
                    c.nome_completo as cliente
                FROM ordens_servico os
                JOIN clientes c ON os.cliente_id = c.id
                WHERE os.ativo = 1 
                AND os.id BETWEEN ? AND ?
                AND os.status_atual_id IN ($placeholders)
                ORDER BY os.id ASC";
        
        $stmt = $db->prepare($sql);
        $params = array_merge([$osIdInicio, $osIdFim], $statusAprovados);
        $stmt->execute($params);
        $osList = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $itens = [];
        $totalFaturamento = 0;
        $totalCustos = 0;
        $totalLucro = 0;
        $ordensPrejuizo = [];

        foreach ($osList as $os) {
            $faturamento = (float)($os['valor_total_os'] ?? 0) - (float)($os['valor_desconto'] ?? 0);
            $custosPecas = $this->getTotalCustoByOs($os['id']);
            $custosTaxas = $this->getTotalTaxasByOs($os['id']);
            $custosTotal = $custosPecas + $custosTaxas;
            $lucro = $faturamento - $custosTotal;

            $itens[] = [
                'os_id' => $os['id'],
                'cliente' => $os['cliente'],
                'defeito_relatado' => $os['defeito_relatado'],
                'faturamento' => $faturamento,
                'custos_pecas' => $custosPecas,
                'custos_taxas' => $custosTaxas,
                'custos_total' => $custosTotal,
                'lucro' => $lucro,
                'status' => $os['status_atual_id']
            ];

            $totalFaturamento += $faturamento;
            $totalCustos += $custosTotal;
            $totalLucro += $lucro;

            if ($lucro < 0) {
                $ordensPrejuizo[] = $os['id'];
            }
        }

        $mediaLucro = count($itens) > 0 ? $totalLucro / count($itens) : 0;

        return [
            'itens' => $itens,
            'total_faturamento' => $totalFaturamento,
            'total_custos' => $totalCustos,
            'total_lucro' => $totalLucro,
            'media_lucro' => $mediaLucro,
            'ordens_prejuizo' => $ordensPrejuizo
        ];
    }

    /**
     * ======================================
     * VISÃO 4: Entradas Órfãs (Gestão de Adiantamentos)
     * Objetivo: Identificar dinheiro que entrou no Caixa mas não foi faturado
     * ======================================
     */
    public function getVisaoEntradasOrfas(): array
    {
        $db = $this->osModel->getConnection();
        $sql = "SELECT 
                    pt.id as pagamento_id,
                    pt.tipo_origem,
                    pt.origem_id,
                    pt.valor_bruto,
                    pt.valor_liquido,
                    pt.created_at as data_pagamento,
                    CASE 
                        WHEN pt.tipo_origem = 'os' THEN c.nome_completo
                        WHEN pt.tipo_origem = 'atendimento' THEN c_at.nome_completo
                        ELSE ''
                    END as cliente,
                    CASE 
                        WHEN pt.tipo_origem = 'os' THEN os.defeito_relatado
                        WHEN pt.tipo_origem = 'atendimento' THEN ae.descricao_problema
                        ELSE ''
                    END as descricao,
                    CASE 
                        WHEN pt.tipo_origem = 'os' THEN os.status_atual_id
                        WHEN pt.tipo_origem = 'atendimento' THEN ae.status
                        ELSE NULL
                    END as status_origem
                FROM pagamentos_transacoes pt
                LEFT JOIN ordens_servico os ON pt.tipo_origem = 'os' AND pt.origem_id = os.id
                LEFT JOIN atendimentos_externos ae ON pt.tipo_origem = 'atendimento' AND pt.origem_id = ae.id
                LEFT JOIN clientes c ON os.cliente_id = c.id
                LEFT JOIN clientes c_at ON ae.cliente_id = c_at.id
                WHERE pt.ativo = 1
                AND (
                    (pt.tipo_origem = 'os' AND os.status_atual_id != ?)
                    OR (pt.tipo_origem = 'atendimento' AND ae.status != ?)
                    OR (pt.tipo_origem NOT IN ('os', 'atendimento'))
                )
                ORDER BY pt.created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([OrdemServico::STATUS_FINALIZADA, 'concluido']); 
        $entradasOrfas = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $totalOrfao = array_sum(array_column($entradasOrfas, 'valor_bruto'));

        return [
            'itens' => $entradasOrfas,
            'total' => $totalOrfao
        ];
    }

    /**
     * ======================================
     * AUDITORIA: Compara (Entradas - Saídas) com o saldo esperado
     * ======================================
     */
    public function auditarSaldo(string $dataInicio, string $dataFim): array
    {
        $visaoCaixa = $this->getVisaoCaixa($dataInicio, $dataFim);
        
        // Verificar se há divergências
        $divergencias = [];
        $db = $this->osModel->getConnection();
        
        // Verificar pagamentos inativos que ainda estão no fluxo de caixa
        $sqlPagamentos = "SELECT fc.id, fc.referencia_id 
                FROM fluxo_caixa fc
                LEFT JOIN pagamentos_transacoes pt ON fc.referencia_tipo = 'pagamento' AND fc.referencia_id = pt.id
                WHERE fc.referencia_tipo = 'pagamento'
                AND DATE(fc.data) BETWEEN ? AND ?
                AND (pt.id IS NULL OR pt.ativo = 0)";
        
        $stmtPag = $db->prepare($sqlPagamentos);
        $stmtPag->execute([$dataInicio, $dataFim]);
        $entradasInvalidas = $stmtPag->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        
        if (!empty($entradasInvalidas)) {
            $divergencias[] = [
                'tipo' => 'Entradas Inválidas',
                'descricao' => 'Há pagamentos inativos ou inexistentes no fluxo de caixa',
                'quantidade' => count($entradasInvalidas)
            ];
        }
        
        // Verificar custos de itens de OS inativos ou inexistentes
        $sqlItensOs = "SELECT fc.id, fc.referencia_id 
                FROM fluxo_caixa fc
                LEFT JOIN itens_ordem_servico ios ON fc.referencia_tipo = 'item_os' AND fc.referencia_id = ios.id
                WHERE fc.referencia_tipo = 'item_os'
                AND fc.tipo = 'custo'
                AND DATE(fc.data) BETWEEN ? AND ?
                AND (ios.id IS NULL OR ios.ativo = 0)";
        
        $stmtItensOs = $db->prepare($sqlItensOs);
        $stmtItensOs->execute([$dataInicio, $dataFim]);
        $custosItensOsInvalidos = $stmtItensOs->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        
        if (!empty($custosItensOsInvalidos)) {
            $divergencias[] = [
                'tipo' => 'Custos Inválidos (Itens OS)',
                'descricao' => 'Há custos de itens de OS inativos ou inexistentes no fluxo de caixa',
                'quantidade' => count($custosItensOsInvalidos)
            ];
        }
        
        // Verificar custos de itens de atendimento inativos ou inexistentes
        $sqlItensAtend = "SELECT fc.id, fc.referencia_id 
                FROM fluxo_caixa fc
                LEFT JOIN itens_ordem_servico ios ON fc.referencia_tipo = 'item_atendimento' AND fc.referencia_id = ios.id
                WHERE fc.referencia_tipo = 'item_atendimento'
                AND fc.tipo = 'custo'
                AND DATE(fc.data) BETWEEN ? AND ?
                AND (ios.id IS NULL OR ios.ativo = 0)";
        
        $stmtItensAtend = $db->prepare($sqlItensAtend);
        $stmtItensAtend->execute([$dataInicio, $dataFim]);
        $custosItensAtendInvalidos = $stmtItensAtend->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        
        if (!empty($custosItensAtendInvalidos)) {
            $divergencias[] = [
                'tipo' => 'Custos Inválidos (Itens Atendimento)',
                'descricao' => 'Há custos de itens de atendimento inativos ou inexistentes no fluxo de caixa',
                'quantidade' => count($custosItensAtendInvalidos)
            ];
        }

        return [
            'saldo_calculado' => $visaoCaixa['resultado_final'],
            'divergencias' => $divergencias,
            'status' => empty($divergencias) ? 'OK' : 'DIVERGÊNCIAS ENCONTRADAS'
        ];
    }

    /**
     * ======================================
     * Métodos legados mantidos para compatibilidade
     * ======================================
     */
    public function calcularProduzido(string $dataInicio, string $dataFim): array
    {
        $visao = $this->getVisaoCompetencia($dataInicio, $dataFim);
        
        $itens = [];
        foreach ($visao['itens'] as $item) {
            $itens[] = [
                'tipo' => 'OS - Produção de Reparo',
                'origem_id' => $item['id'],
                'data' => $item['data_finalizacao'],
                'cliente' => $item['cliente'],
                'valor_total' => $item['valor_faturado'],
                'taxa_nf' => $item['custos_taxas'],
                'descricao' => $item['defeito_relatado'],
                'numero' => $item['id'],
                'custos' => $item['custos_pecas'],
                'lucro_previsto' => $item['lucro_prejuizo']
            ];
        }

        return [
            'itens' => $itens,
            'totais' => [
                'valor_total' => $visao['total'],
                'custos' => array_sum(array_column($visao['itens'], 'custos_pecas')),
                'taxas' => array_sum(array_column($visao['itens'], 'custos_taxas')),
                'lucro_previsto' => array_sum(array_column($visao['itens'], 'lucro_prejuizo'))
            ]
        ];
    }

    public function calcularEntradas(string $dataInicio, string $dataFim): array
    {
        $visaoCaixa = $this->getVisaoCaixa($dataInicio, $dataFim);
        $pagamentos = [];
        
        foreach ($visaoCaixa['entradas'] as $item) {
            $pagamentos[] = [
                'id' => $item['id'],
                'tipo_origem' => $item['tipo_origem'],
                'origem_id' => $item['origem_id'],
                'valor_bruto' => $item['valor_bruto'],
                'valor_liquido' => $item['valor_liquido'],
                'taxa_cartao' => $item['taxa_cartao'],
                'created_at' => $item['data_transacao'],
                'descricao' => $item['descricao'],
                'cliente' => $item['cliente'],
                'taxa_nf' => 0,
                'custos' => 0,
                'lucro' => 0
            ];
        }

        return [
            'itens' => $pagamentos,
            'totais' => [
                'valor_bruto' => $visaoCaixa['entrada_bruta'],
                'custos' => 0,
                'taxas_nf' => 0,
                'taxas_cartao' => array_sum(array_column($visaoCaixa['entradas'], 'taxa_cartao')),
                'lucro' => $visaoCaixa['entrada_bruta'] - array_sum(array_column($visaoCaixa['entradas'], 'taxa_cartao'))
            ]
        ];
    }
}

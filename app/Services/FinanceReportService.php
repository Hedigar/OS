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

    // Status que indicam que o serviço foi autorizado/entrou em produção
    private array $statusAprovados = [4, 5, 8, 11, 12, 14, 15];

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
        $placeholders = implode(',', array_fill(0, count($this->statusAprovados), '?'));

        $sql = "SELECT COALESCE(SUM(ios.quantidade * COALESCE(NULLIF(ios.valor_custo, 0), NULLIF(ios.custo, 0), 0)), 0) 
                FROM itens_ordem_servico ios
                JOIN ordens_servico os ON ios.ordem_servico_id = os.id
                WHERE ios.ordem_servico_id = ? 
                AND ios.ativo = 1 
                AND os.status_atual_id IN ($placeholders)";
        
        $stmt = $db->prepare($sql);
        $params = array_merge([$osId], $this->statusAprovados);
        $stmt->execute($params);
        return (float)($stmt->fetchColumn() ?: 0);
    }

    /**
     * Retorna o custo total de taxas de uma OS (taxa NF)
     */
    private function getTotalTaxasByOs(int $osId): float
    {
        $db = $this->osModel->getConnection();
        $placeholders = implode(',', array_fill(0, count($this->statusAprovados), '?'));
        
        $sql = "SELECT COALESCE(valor_taxa_nf, 0) 
                FROM ordens_servico 
                WHERE id = ? AND ativo = 1 
                AND status_atual_id IN ($placeholders)";
        
        $stmt = $db->prepare($sql);
        $params = array_merge([$osId], $this->statusAprovados);
        $stmt->execute($params);
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
     * VISÃO 1: Competência (Produção / DRE)
     * Objetivo: Saber quanto a empresa produziu de riqueza no período.
     * Amarramos o faturamento e o custo à data da PRIMEIRA APROVAÇÃO no período.
     * ======================================
     */
    public function getVisaoCompetencia(string $dataInicio, string $dataFim): array
    {
        $db = $this->osModel->getConnection();
        $placeholders = implode(',', array_fill(0, count($this->statusAprovados), '?'));

        // Query complexa para buscar a data da PRIMEIRA aprovação (status aprovado) dentro do período
        // Ou se ela já estava aprovada e permanece aprovada (continuidade).
        // Para simplificar e atender o requisito: vamos buscar a data mínima do histórico de status aprovados.
        $sqlOs = "SELECT 
                    os.id,
                    os.valor_total_os,
                    os.valor_desconto,
                    os.valor_taxa_nf,
                    os.defeito_relatado,
                    c.nome_completo as cliente,
                    DATE(COALESCE(h.data_aprovacao, os.updated_at)) as data_competencia
                FROM ordens_servico os
                JOIN clientes c ON os.cliente_id = c.id
                JOIN (
                    /* Subquery para pegar a primeira data de aprovação de cada OS */
                    SELECT ordem_servico_id, MIN(created_at) as data_aprovacao
                    FROM ordens_servico_status_historico
                    WHERE status_id IN ($placeholders)
                    GROUP BY ordem_servico_id
                ) h ON os.id = h.ordem_servico_id
                WHERE os.ativo = 1 
                AND os.status_atual_id IN ($placeholders)
                AND DATE(h.data_aprovacao) BETWEEN ? AND ?
                ORDER BY data_competencia DESC";
        
        $stmtOs = $db->prepare($sqlOs);
        $params = array_merge($this->statusAprovados, $this->statusAprovados, [$dataInicio, $dataFim]);
        $stmtOs->execute($params);
        $osAprovadas = $stmtOs->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        // Atendimentos Externos (consideramos a data de criação ou conclusão se aprovado)
        $sqlAtendimentos = "SELECT 
                    ae.id,
                    (COALESCE(ae.valor_total, 0) + COALESCE(ae.valor_deslocamento, 0)) as valor_total_os,
                    0 as valor_desconto,
                    ae.valor_taxa_nf,
                    ae.descricao_problema as defeito_relatado,
                    c.nome_completo as cliente,
                    DATE(ae.created_at) as data_competencia
                FROM atendimentos_externos ae
                JOIN clientes c ON ae.cliente_id = c.id
                WHERE ae.ativo = 1 
                AND ae.status = 'concluido'
                AND DATE(ae.created_at) BETWEEN ? AND ?
                ORDER BY data_competencia DESC";
        
        $stmtAtendimentos = $db->prepare($sqlAtendimentos);
        $stmtAtendimentos->execute([$dataInicio, $dataFim]);
        $atendimentosConcluidos = $stmtAtendimentos->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $itens = [];
        $totalOs = 0;
        $totalAtendimentos = 0;

        foreach ($osAprovadas as $os) {
            $valorFaturado = (float)($os['valor_total_os'] ?? 0) - (float)($os['valor_desconto'] ?? 0);
            
            // Custo da peça amarrado à mesma data de competência (Primeira Aprovação)
            $sqlCustos = "SELECT COALESCE(SUM(quantidade * COALESCE(NULLIF(valor_custo, 0), NULLIF(custo, 0), 0)), 0) 
                          FROM itens_ordem_servico WHERE ordem_servico_id = ? AND ativo = 1";
            $stmtC = $db->prepare($sqlCustos);
            $stmtC->execute([$os['id']]);
            $custosPecas = (float)$stmtC->fetchColumn();

            $custosTaxas = (float)($os['valor_taxa_nf'] ?? 0);
            $lucroPrejuizo = $valorFaturado - $custosPecas - $custosTaxas;

            $itens[] = [
                'tipo' => 'os',
                'id' => $os['id'],
                'data_finalizacao' => $os['data_competencia'],
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
                'data_finalizacao' => $atendimento['data_competencia'],
                'cliente' => $atendimento['cliente'],
                'defeito_relatado' => $atendimento['defeito_relatado'],
                'valor_faturado' => $valorFaturado,
                'custos_pecas' => $custosPecas,
                'custos_taxas' => $custosTaxas,
                'lucro_prejuizo' => $lucroPrejuizo
            ];

            $totalAtendimentos += $valorFaturado;
        }

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
     * ======================================
     */
    public function getVisaoCaixa(string $dataInicio, string $dataFim): array
    {
        $db = $this->osModel->getConnection();
        $placeholders = implode(',', array_fill(0, count($this->statusAprovados), '?'));
        
        // Entradas (pagamentos ativos)
        $sqlEntradas = "SELECT pt.*, 
                               CASE WHEN pt.tipo_origem = 'os' THEN c.nome_completo ELSE c_at.nome_completo END as cliente,
                               CASE WHEN pt.tipo_origem = 'os' THEN os.defeito_relatado ELSE ae.descricao_problema END as descricao,
                               pt.valor_taxa as taxa_cartao
                        FROM pagamentos_transacoes pt
                        LEFT JOIN ordens_servico os ON pt.tipo_origem = 'os' AND pt.origem_id = os.id
                        LEFT JOIN atendimentos_externos ae ON pt.tipo_origem = 'atendimento' AND pt.origem_id = ae.id
                        LEFT JOIN clientes c ON os.cliente_id = c.id
                        LEFT JOIN clientes c_at ON ae.cliente_id = c_at.id
                        WHERE pt.ativo = 1 AND DATE(pt.created_at) BETWEEN ? AND ?
                        ORDER BY pt.created_at DESC";
        
        $stmtEntradas = $db->prepare($sqlEntradas);
        $stmtEntradas->execute([$dataInicio, $dataFim]);
        $entradas = $stmtEntradas->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        
        $sqlSaidas = "SELECT 
                            d.id as origem_id,
                            'despesa' as tipo_origem,
                            d.valor,
                            d.descricao,
                            d.data_despesa as data_transacao,
                            COALESCE(c.nome, '') as categoria,
                            NULL as origem_id_relacionada
                      FROM despesas d
                      LEFT JOIN despesas_categorias c ON d.categoria_id = c.id
                      WHERE d.ativo = 1 AND DATE(d.data_despesa) BETWEEN ? AND ?
                      UNION ALL
                      SELECT 
                            fc.referencia_id as origem_id,
                            fc.referencia_tipo as tipo_origem,
                            fc.valor,
                            CASE
                                WHEN fc.referencia_tipo = 'item_os' THEN CONCAT('Custo OS #', os_fc.id)
                                WHEN fc.referencia_tipo = 'item_atendimento' THEN CONCAT('Custo Atend #', ae_fc.id)
                                ELSE 'Custo'
                            END as descricao,
                            fc.data as data_transacao,
                            CASE
                                WHEN fc.referencia_tipo = 'item_os' THEN 'Custo OS'
                                WHEN fc.referencia_tipo = 'item_atendimento' THEN 'Custo Atendimento'
                                ELSE 'Custo'
                            END as categoria,
                            CASE
                                WHEN fc.referencia_tipo = 'item_os' THEN os_fc.id
                                WHEN fc.referencia_tipo = 'item_atendimento' THEN ae_fc.id
                                ELSE NULL
                            END as origem_id_relacionada
                      FROM fluxo_caixa fc
                      LEFT JOIN itens_ordem_servico ios ON (
                          (fc.referencia_tipo = 'item_os' AND fc.referencia_id = ios.id)
                          OR (fc.referencia_tipo = 'item_atendimento' AND fc.referencia_id = ios.id)
                      )
                      LEFT JOIN ordens_servico os_fc ON ios.ordem_servico_id = os_fc.id
                      LEFT JOIN atendimentos_externos ae_fc ON ios.atendimento_externo_id = ae_fc.id
                      WHERE fc.tipo = 'custo' AND DATE(fc.data) BETWEEN ? AND ?
                      AND (fc.referencia_tipo != 'item_os' OR os_fc.status_atual_id IN ($placeholders))
                      ORDER BY data_transacao DESC";
        
        $stmtSaidas = $db->prepare($sqlSaidas);
        $paramsSaidas = array_merge([$dataInicio, $dataFim, $dataInicio, $dataFim], $this->statusAprovados);
        $stmtSaidas->execute($paramsSaidas);
        $saidas = $stmtSaidas->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $entradaBruta = array_sum(array_column($entradas, 'valor_bruto'));
        $deducoesVenda = array_sum(array_column($entradas, 'taxa_cartao'));
        $custosDiretos = array_sum(array_column($saidas, 'valor'));

        return [
            'entradas' => $entradas,
            'saidas' => $saidas,
            'entrada_bruta' => $entradaBruta,
            'deducoes_venda' => $deducoesVenda,
            'receita_liquida' => $entradaBruta - $deducoesVenda,
            'custos_diretos' => $custosDiretos,
            'resultado_final' => ($entradaBruta - $deducoesVenda) - $custosDiretos
        ];
    }

    /**
     * VISÃO 3: Analítica de OS
     */
    public function getVisaoAnaliticaOs(int $osIdInicio, int $osIdFim): array
    {
        $db = $this->osModel->getConnection();
        $placeholders = implode(',', array_fill(0, count($this->statusAprovados), '?'));

        $sql = "SELECT os.*, c.nome_completo as cliente
                FROM ordens_servico os
                JOIN clientes c ON os.cliente_id = c.id
                WHERE os.ativo = 1 AND os.id BETWEEN ? AND ? AND os.status_atual_id IN ($placeholders)
                ORDER BY os.id ASC";
        
        $stmt = $db->prepare($sql);
        $params = array_merge([$osIdInicio, $osIdFim], $this->statusAprovados);
        $stmt->execute($params);
        $osList = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $itens = [];
        foreach ($osList as $os) {
            $faturamento = (float)$os['valor_total_os'] - (float)$os['valor_desconto'];
            $custosPecas = $this->getTotalCustoByOs($os['id']);
            $custosTaxas = (float)$os['valor_taxa_nf'];
            
            $itens[] = [
                'os_id' => $os['id'],
                'cliente' => $os['cliente'],
                'faturamento' => $faturamento,
                'custos_pecas' => $custosPecas,
                'custos_taxas' => $custosTaxas,
                'lucro' => $faturamento - ($custosPecas + $custosTaxas)
            ];
        }

        return ['itens' => $itens];
    }

    public function auditarSaldo(string $dataInicio, string $dataFim): array
    {
        $visao = $this->getVisaoCaixa($dataInicio, $dataFim);
        $saldoCalculado = (float)($visao['resultado_final'] ?? 0);

        $db = $this->osModel->getConnection();

        $sqlPag = "SELECT COUNT(*) FROM fluxo_caixa fc
                   LEFT JOIN pagamentos_transacoes pt ON fc.referencia_tipo = 'pagamento' AND fc.referencia_id = pt.id
                   WHERE fc.referencia_tipo = 'pagamento'
                   AND fc.data BETWEEN ? AND ?
                   AND (pt.id IS NULL OR pt.ativo = 0)";
        $stmtPag = $db->prepare($sqlPag);
        $stmtPag->execute([$dataInicio, $dataFim]);
        $pagInvalidos = (int)($stmtPag->fetchColumn() ?: 0);

        $sqlItensOs = "SELECT COUNT(*) FROM fluxo_caixa fc
                       LEFT JOIN itens_ordem_servico ios ON fc.referencia_tipo = 'item_os' AND fc.referencia_id = ios.id
                       WHERE fc.referencia_tipo = 'item_os'
                       AND fc.data BETWEEN ? AND ?
                       AND (ios.id IS NULL OR ios.ativo = 0)";
        $stmtItensOs = $db->prepare($sqlItensOs);
        $stmtItensOs->execute([$dataInicio, $dataFim]);
        $itensOsInvalidos = (int)($stmtItensOs->fetchColumn() ?: 0);

        $sqlItensAtend = "SELECT COUNT(*) FROM fluxo_caixa fc
                          LEFT JOIN itens_ordem_servico ios ON fc.referencia_tipo = 'item_atendimento' AND fc.referencia_id = ios.id
                          WHERE fc.referencia_tipo = 'item_atendimento'
                          AND fc.data BETWEEN ? AND ?
                          AND (ios.id IS NULL OR ios.ativo = 0)";
        $stmtItensAtend = $db->prepare($sqlItensAtend);
        $stmtItensAtend->execute([$dataInicio, $dataFim]);
        $itensAtendInvalidos = (int)($stmtItensAtend->fetchColumn() ?: 0);

        $divergencias = [];

        if ($pagInvalidos > 0) {
            $divergencias[] = [
                'descricao' => 'Pagamentos órfãos/inativos no fluxo de caixa',
                'quantidade' => $pagInvalidos
            ];
        }

        if ($itensOsInvalidos > 0) {
            $divergencias[] = [
                'descricao' => 'Itens de OS órfãos/inativos no fluxo de caixa',
                'quantidade' => $itensOsInvalidos
            ];
        }

        if ($itensAtendInvalidos > 0) {
            $divergencias[] = [
                'descricao' => 'Itens de atendimento órfãos/inativos no fluxo de caixa',
                'quantidade' => $itensAtendInvalidos
            ];
        }

        return [
            'status' => empty($divergencias) ? 'OK' : 'DIVERGENTE',
            'saldo_calculado' => $saldoCalculado,
            'divergencias' => $divergencias
        ];
    }

    /**
     * COMPATIBILIDADE
     */
    public function calcularProduzido(string $dataInicio, string $dataFim): array
    {
        $visao = $this->getVisaoCompetencia($dataInicio, $dataFim);
        return [
            'itens' => $visao['itens'],
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
        return [
            'totais' => [
                'valor_bruto' => $visaoCaixa['entrada_bruta'],
                'taxas_cartao' => $visaoCaixa['deducoes_venda'],
                'lucro' => $visaoCaixa['receita_liquida']
            ]
        ];
    }
}

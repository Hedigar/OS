<?php

namespace App\Services;

use App\Models\OrdemServico;
use App\Models\AtendimentoExterno;
use App\Models\PagamentoTransacao;

class FinanceReportService
{
    private OrdemServico $osModel;
    private AtendimentoExterno $atendimentoModel;
    private PagamentoTransacao $pagamentoModel;

    public function __construct()
    {
        $this->osModel = new OrdemServico();
        $this->atendimentoModel = new AtendimentoExterno();
        $this->pagamentoModel = new PagamentoTransacao();
    }

    /**
     * Retorna o custo total de uma OS (soma dos custos dos itens)
     */
    private function getTotalCustoByOs(int $osId): float
    {
        $db = $this->osModel->getConnection();
        $sql = "SELECT COALESCE(SUM(quantidade * COALESCE(NULLIF(valor_custo, 0), NULLIF(custo, 0), 0)), 0) 
                FROM itens_ordem_servico 
                WHERE ordem_servico_id = ? AND ativo = 1";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$osId]);
        return (float)($stmt->fetchColumn() ?: 0);
    }

    /**
     * Retorna o custo total de um atendimento (soma dos custos dos itens)
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
     * Retorna o total pago para uma OS (soma dos pagamentos)
     */
    private function getTotalPagoByOs(int $osId): float
    {
        return $this->pagamentoModel->sumByOrigem('os', $osId);
    }

    /**
     * Retorna o total pago para um atendimento (soma dos pagamentos)
     */
    private function getTotalPagoByAtendimento(int $atendimentoId): float
    {
        return $this->pagamentoModel->sumByOrigem('atendimento', $atendimentoId);
    }

    /**
     * Retorna os itens de uma OS ou atendimento
     */
    private function getItensPorOrigem(string $tipo, int $origemId): array
    {
        $db = $this->osModel->getConnection();
        $campoId = $tipo === 'OS' ? 'ordem_servico_id' : 'atendimento_externo_id';

        $sql = "SELECT descricao, tipo_item, quantidade 
                FROM itens_ordem_servico 
                WHERE $campoId = ? AND ativo = 1";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$origemId]);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Calcula o total PRODUZIDO (competência) em um período.
     * Somente OS Finalizadas (status_id=5) e Atendimentos Concluídos.
     */
    public function calcularProduzido(string $dataInicio, string $dataFim): array
    {
        $osFinalizadas = $this->osModel->finalizadasNoPeriodo($dataInicio, $dataFim);
        $atendimentosConcluidos = $this->atendimentoModel->concluidosNoPeriodo($dataInicio, $dataFim);

        $itens = [];
        $totalProduzido = 0;
        $totalCustos = 0;
        $totalTaxas = 0;
        $totalLucroPrevisto = 0;

        // Processar OS finalizadas
        foreach ($osFinalizadas as $os) {
            $item = $this->processarItemProducaoOS($os);
            $itens[] = $item;
            $totalProduzido += $item['valor_total'];
            $totalCustos += $item['custos'];
            $totalTaxas += $item['taxa_nf'];
            $totalLucroPrevisto += $item['lucro_previsto'];
        }

        // Processar atendimentos concluídos
        foreach ($atendimentosConcluidos as $atendimento) {
            $item = $this->processarItemProducaoAtendimento($atendimento);
            $itens[] = $item;
            $totalProduzido += $item['valor_total'];
            $totalCustos += $item['custos'];
            $totalTaxas += $item['taxa_nf'];
            $totalLucroPrevisto += $item['lucro_previsto'];
        }

        // Ordenar por data decrescente
        usort($itens, fn($a, $b) => strtotime($b['data']) - strtotime($a['data']));

        return [
            'itens' => $itens,
            'totais' => [
                'valor_total' => $totalProduzido,
                'custos' => $totalCustos,
                'taxas' => $totalTaxas,
                'lucro_previsto' => $totalLucroPrevisto
            ]
        ];
    }

    /**
     * Calcula o total da RECEITA DE DIAGNÓSTICO em um período.
     * Somente OS com status Diagnóstico Finalizado (status_id=15).
     */
    public function calcularReceitaDiagnostico(string $dataInicio, string $dataFim): array
    {
        $osDiagnostico = $this->osModel->diagnosticoFinalizadoNoPeriodo($dataInicio, $dataFim);
        
        $itens = [];
        $totalReceitaDiagnostico = 0;
        $totalCustos = 0;
        $totalTaxas = 0;
        $totalLucro = 0;

        foreach ($osDiagnostico as $os) {
            $osId = $os['id'];
            $totalPago = $this->getTotalPagoByOs($osId);
            $custos = $this->getTotalCustoByOs($osId);
            $taxaNf = $os['valor_taxa_nf'] ?? 0;
            $lucro = $totalPago - $custos - $taxaNf;
            
            $itens[] = [
                'tipo' => 'OS - Receita de Diagnóstico',
                'origem_id' => $osId,
                'data' => $os['data_finalizacao'],
                'cliente' => $os['cliente'],
                'valor_total' => $totalPago,
                'taxa_nf' => $taxaNf,
                'descricao' => $os['defeito_relatado'],
                'numero' => $osId,
                'custos' => $custos,
                'itens' => $this->getItensPorOrigem('OS', $osId),
                'lucro' => $lucro
            ];
            
            $totalReceitaDiagnostico += $totalPago;
            $totalCustos += $custos;
            $totalTaxas += $taxaNf;
            $totalLucro += $lucro;
        }
        
        // Ordenar por data decrescente
        usort($itens, fn($a, $b) => strtotime($b['data']) - strtotime($a['data']));

        return [
            'itens' => $itens,
            'totais' => [
                'valor_total' => $totalReceitaDiagnostico,
                'custos' => $totalCustos,
                'taxas' => $totalTaxas,
                'lucro' => $totalLucro
            ]
        ];
    }

    /**
     * Calcula o total de ENTRADAS (caixa) em um período.
     */
    public function calcularEntradas(string $dataInicio, string $dataFim): array
    {
        $fluxoCaixaModel = new \App\Models\FluxoCaixa();
        $fluxoItens = $fluxoCaixaModel->getRelatorioPorPeriodo($dataInicio, $dataFim);
        $totaisFluxo = $fluxoCaixaModel->getTotaisPorPeriodo($dataInicio, $dataFim);

        $pagamentos = [];
        $totalCaixa = $totaisFluxo['entrada'];
        $totalCustosCaixa = $totaisFluxo['custo'];
        $totalTaxasNF = 0;
        $totalTaxasCartao = 0;
        $totalLucroCaixa = $totalCaixa - $totalCustosCaixa - $totalTaxasNF - $totalTaxasCartao;

        foreach ($fluxoItens as $item) {
            $pagamentos[] = [
                'id' => $item['id'],
                'tipo_origem' => $item['os_id'] ? 'os' : ($item['atendimento_externo_id'] ? 'atendimento' : null),
                'origem_id' => $item['os_id'] ?? $item['atendimento_externo_id'],
                'valor_bruto' => $item['valor'],
                'valor_liquido' => $item['valor'],
                'taxa_cartao' => 0,
                'created_at' => $item['data'],
                'descricao' => $item['descricao_origem'] ?? '',
                'cliente' => $item['cliente_nome'] ?? '',
                'taxa_nf' => 0,
                'custos' => 0,
                'lucro' => 0,
                'valor_total_origem' => 0,
            ];
        }

        return [
            'itens' => $pagamentos,
            'totais' => [
                'valor_bruto' => $totalCaixa,
                'custos' => $totalCustosCaixa,
                'taxas_nf' => $totalTaxasNF,
                'taxas_cartao' => $totalTaxasCartao,
                'lucro' => $totalLucroCaixa
            ]
        ];
    }

    /**
     * Calcula o CONTAS A RECEBER (pendências).
     */
    public function calcularContasAReceber(): array
    {
        $osPendentes = $this->osModel->comPendencias();
        $atendimentosPendentes = $this->atendimentoModel->comPendencias();

        $itens = [];
        $totalPendente = 0;
        $totalCustosPendente = 0;

        foreach ($osPendentes as $os) {
            $item = $this->processarItemPendenciaOS($os);
            $itens[] = $item;
            $totalPendente += $item['valor_pendente'];
            $totalCustosPendente += $item['custos'];
        }

        foreach ($atendimentosPendentes as $atendimento) {
            $item = $this->processarItemPendenciaAtendimento($atendimento);
            $itens[] = $item;
            $totalPendente += $item['valor_pendente'];
            $totalCustosPendente += $item['custos'];
        }

        // Ordenar por data decrescente
        usort($itens, fn($a, $b) => strtotime($b['data']) - strtotime($a['data']));

        return [
            'itens' => $itens,
            'totais' => [
                'valor_total' => $totalPendente,
                'custos' => $totalCustosPendente
            ]
        ];
    }

    /**
     * Gera o relatório financeiro completo.
     */
    public function relatorioFinanceiroCompleto(string $dataInicio, string $dataFim): array
    {
        return [
            'produzido' => $this->calcularProduzido($dataInicio, $dataFim),
            'receita_diagnostico' => $this->calcularReceitaDiagnostico($dataInicio, $dataFim),
            'entradas' => $this->calcularEntradas($dataInicio, $dataFim),
            'contas_a_receber' => $this->calcularContasAReceber()
        ];
    }

    /**
     * Gera relatório analítico CSV com linhas detalhadas para auditoria.
     */
    public function gerarRelatorioAnaliticoCsv(string $dataInicio, string $dataFim): string
    {
        $relatorioCompleto = $this->relatorioFinanceiroCompleto($dataInicio, $dataFim);
        $linhas = [];
        
        // Cabeçalho
        $linhas[] = ['ID', 'Status', 'Valor Total', 'Valor Recebido', 'Saldo Pendente', 'Natureza da Receita'];
        
        // Produção
        foreach ($relatorioCompleto['produzido']['itens'] as $item) {
            if ($item['tipo'] === 'Atendimento') {
                continue; // Focar em OS por enquanto
            }
            $osId = $item['origem_id'];
            $totalPago = $this->getTotalPagoByOs($osId);
            $saldoPendente = $item['valor_total'] - $totalPago;
            $linhas[] = [
                $osId,
                'Finalizada',
                number_format($item['valor_total'], 2, ',', '.'),
                number_format($totalPago, 2, ',', '.'),
                number_format($saldoPendente, 2, ',', '.'),
                'Produção'
            ];
        }
        
        // Receita de Diagnóstico
        foreach ($relatorioCompleto['receita_diagnostico']['itens'] as $item) {
            $osId = $item['origem_id'];
            $totalPago = $this->getTotalPagoByOs($osId);
            $linhas[] = [
                $osId,
                'Diagnóstico Finalizado',
                number_format($totalPago, 2, ',', '.'),
                number_format($totalPago, 2, ',', '.'),
                '0,00',
                'Receita de Diagnóstico'
            ];
        }
        
        // Gerar CSV
        $output = fopen('php://temp', 'r+');
        foreach ($linhas as $linha) {
            fputcsv($output, $linha, ';');
        }
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);
        
        return $csvContent;
    }

    /**
     * Processa um item de OS para o relatório de produção.
     */
    private function processarItemProducaoOS(array $os): array
    {
        $custos = $this->getTotalCustoByOs($os['id']);
        $lucroPrevisto = $os['valor_total_os'] - $custos - ($os['valor_taxa_nf'] ?? 0);

        return [
            'tipo' => 'OS - Produção de Reparo',
            'origem_id' => $os['id'],
            'data' => $os['data_finalizacao'],
            'cliente' => $os['cliente'],
            'valor_total' => $os['valor_total_os'],
            'taxa_nf' => $os['valor_taxa_nf'],
            'descricao' => $os['defeito_relatado'],
            'numero' => $os['id'],
            'custos' => $custos,
            'itens' => $this->getItensPorOrigem('OS', $os['id']),
            'lucro_previsto' => $lucroPrevisto
        ];
    }

    /**
     * Processa um item de atendimento para o relatório de produção.
     */
    private function processarItemProducaoAtendimento(array $atendimento): array
    {
        $custos = $this->getTotalCustoByAtendimento($atendimento['id']);
        $lucroPrevisto = $atendimento['valor_total'] - $custos - ($atendimento['valor_taxa_nf'] ?? 0);

        return [
            'tipo' => 'Atendimento',
            'origem_id' => $atendimento['id'],
            'data' => $atendimento['data_finalizacao'],
            'cliente' => $atendimento['cliente'],
            'valor_total' => $atendimento['valor_total'],
            'taxa_nf' => $atendimento['valor_taxa_nf'],
            'descricao' => $atendimento['descricao_problema'],
            'numero' => $atendimento['id'],
            'custos' => $custos,
            'itens' => $this->getItensPorOrigem('Atendimento', $atendimento['id']),
            'lucro_previsto' => $lucroPrevisto
        ];
    }

    /**
     * Processa um item de OS para o relatório de pendências.
     */
    private function processarItemPendenciaOS(array $os): array
    {
        $custos = $this->getTotalCustoByOs($os['id']);
        $valorPendente = $os['valor_total_os'] - $os['valor_pago'];

        return [
            'tipo' => 'OS',
            'origem_id' => $os['id'],
            'data' => $os['data'],
            'cliente' => $os['cliente'],
            'valor_total' => $os['valor_total_os'],
            'taxa_nf' => $os['valor_taxa_nf'],
            'descricao' => $os['defeito_relatado'],
            'numero' => $os['id'],
            'valor_pago' => $os['valor_pago'],
            'valor_pendente' => $valorPendente,
            'custos' => $custos,
            'itens' => $this->getItensPorOrigem('OS', $os['id'])
        ];
    }

    /**
     * Processa um item de atendimento para o relatório de pendências.
     */
    private function processarItemPendenciaAtendimento(array $atendimento): array
    {
        $custos = $this->getTotalCustoByAtendimento($atendimento['id']);
        $valorPendente = $atendimento['valor_total'] - $atendimento['valor_pago'];

        return [
            'tipo' => 'Atendimento',
            'origem_id' => $atendimento['id'],
            'data' => $atendimento['data'],
            'cliente' => $atendimento['cliente'],
            'valor_total' => $atendimento['valor_total'],
            'taxa_nf' => $atendimento['valor_taxa_nf'],
            'descricao' => $atendimento['descricao_problema'],
            'numero' => $atendimento['id'],
            'valor_pago' => $atendimento['valor_pago'],
            'valor_pendente' => $valorPendente,
            'custos' => $custos,
            'itens' => $this->getItensPorOrigem('Atendimento', $atendimento['id'])
        ];
    }
}

<?php

namespace App\Services;

use App\Models\AtendimentoExterno;
use App\Models\ItemOS;

class AtendimentoService
{
    private $atendimentoModel;
    private $itemModel;

    public function __construct()
    {
        $this->atendimentoModel = new AtendimentoExterno();
        $this->itemModel = new ItemOS();
    }

    /**
     * Centraliza a busca de dados e cálculos de valores
     */
    public function obterDetalhesVisualizacao($id)
    {
        $atendimento = $this->atendimentoModel->findWithDetails($id);
        if (!$atendimento) return null;

        $itens = $this->atendimentoModel->listarItens($id);
        
        $totalProdutos = 0;
        $totalServicos = 0;
        $totalDesconto = 0;

        if (!empty($itens)) {
            foreach ($itens as $item) {
                $tipo = $item['tipo_item'] ?? 'servico';
                $valorItem = ($item['quantidade'] * ($item['valor_unitario'] + ($item['valor_mao_de_obra'] ?? 0)));
                $descontoItem = (float)($item['desconto'] ?? 0);
                
                if ($tipo === 'produto') {
                    $totalProdutos += ($valorItem - $descontoItem);
                } else {
                    $totalServicos += ($valorItem - $descontoItem);
                }
                $totalDesconto += $descontoItem;
            }
        }

        $valorDeslocamento = (float)($atendimento['valor_deslocamento'] ?? 0);
        $totalAtendimento = $totalProdutos + $totalServicos + $valorDeslocamento;

        // Cálculo da Taxa NF
        $emitirNF = (int)($atendimento['emitir_nf'] ?? 0);
        $valorTaxaNF = 0.00;

        if ($emitirNF) {
            $configModel = new \App\Models\ConfiguracaoGeral();
            $percProdutos = (float)$configModel->getValor('nf_porcentagem_produtos') ?: 3;
            $percServicos = (float)$configModel->getValor('nf_porcentagem_servicos') ?: 6;
            
            $valorTaxaNF = ($totalProdutos * ($percProdutos / 100)) + (($totalServicos + $valorDeslocamento) * ($percServicos / 100));
        }

        // Atualiza todos os totals no banco
        $this->atendimentoModel->updateTotals($id, $itens);
        
        return [
            'atendimento' => $atendimento,
            'itens'       => $itens,
            'valor_total' => $totalAtendimento,
            'valor_taxa_nf' => $valorTaxaNF,
            'valor_desconto' => $totalDesconto
        ];
    }

    /**
     * Regra de negócio para atualização: não sobrescreve campos essenciais se vierem vazios
     */
    public function atualizarAtendimento($id, $data)
    {
        $atual = $this->atendimentoModel->find($id);
        if (!$atual) return false;

        if (empty($data['endereco_visita'])) {
            $data['endereco_visita'] = $atual['endereco_visita'];
        }
        
        if (empty($data['descricao_problema'])) {
             $data['descricao_problema'] = $atual['descricao_problema'];
        }

        $statusAnterior = (string)($atual['status'] ?? 'pendente');
        $statusNovo = (string)($data['status'] ?? $statusAnterior);

        // Bloqueio de período fechado: impede saída de concluido ou edição de concluido em período fechado
        $periodService = new \App\Services\PeriodControlService();
        if ($statusAnterior === 'concluido' && $periodService->isPeriodClosed(date('Y-m-d', strtotime($atual['updated_at'] ?? $atual['created_at'])))) {
            throw new \RuntimeException('Não é possível alterar um atendimento concluído cujo período já foi fechado.');
        }

        $result = $this->atendimentoModel->update($id, $data);
        if ($result) {
            // Recalcula totals caso valor_deslocamento tenha sido alterado
            $itens = $this->atendimentoModel->listarItens($id);
            $this->atendimentoModel->updateTotals($id, $itens);

            // Gatilho: transição para concluido = lançar custo 1 única vez (INSERT IGNORE bloqueia duplos)
            if ($statusAnterior !== 'concluido' && $statusNovo === 'concluido') {
                $this->lancarCustoNaConclusao($id);
            }
        }

        return $result;
    }

    public function atualizarItem($itemId, $postData)
    {
        $venda = (float)($postData['valor_unitario'] ?? 0);
        $maoDeObra = (float)($postData['valor_mao_de_obra'] ?? 0);
        $quantidade = (float)($postData['quantidade'] ?? 1);
        $desconto = (float)($postData['desconto'] ?? 0);
        $custo = (float)($postData['custo'] ?? 0);
        $atendimentoId = filter_var($postData['atendimento_externo_id'], FILTER_VALIDATE_INT);

        // Bloqueio de período fechado: se atendimento já concluído com data_conclusão em período fechado
        $periodService = new \App\Services\PeriodControlService();
        if ($periodService->isPeriodClosed(date('Y-m-d'))) {
            throw new \RuntimeException('Não é possível atualizar itens no período fiscal atual pois ele está fechado.');
        }

        $dataCompetencia = null;
        $atendJaConcluido = false;
        if ($atendimentoId) {
            $atual = $this->atendimentoModel->find($atendimentoId);
            if ($atual && $atual['status'] === 'concluido') {
                $atendJaConcluido = true;
                $dataCompetencia = date('Y-m-d', strtotime($atual['updated_at'] ?? $atual['created_at'] ?? 'now'));
                if ($periodService->isPeriodClosed($dataCompetencia)) {
                    throw new \RuntimeException('Não é possível alterar itens de atendimento concluído cujo período já foi fechado.');
                }
            }
        }

        $itemData = [
            'quantidade' => $quantidade,
            'custo' => $custo,
            'valor_unitario' => $venda,
            'valor_mao_de_obra' => $maoDeObra,
            'desconto' => $desconto,
            'valor_total' => (($venda + $maoDeObra) * $quantidade) - $desconto
        ];

        $result = $this->itemModel->update($itemId, $itemData);
        if ($result && $atendimentoId) {
            // Recalcula totals do atendimento
            $itens = $this->atendimentoModel->listarItens($atendimentoId);
            $this->atendimentoModel->updateTotals($atendimentoId, $itens);

            // ATENDIMENTO JÁ CONCLUÍDO: atualiza o custo no fluxo_caixa (período ainda está aberto)
            if ($atendJaConcluido) {
                $novoValorCusto = (float)$quantidade * (float)$custo;
                $fluxoCaixaModel = new \App\Models\FluxoCaixa();
                $fluxoCaixaModel->atualizarCusto($itemId, 'item_atendimento', $novoValorCusto);
            }
        }

        return $result;
    }

    /**
     * Lógica de criação/cálculo de item
     */
    public function salvarItem($postData)
    {
        $atendimentoId = filter_var($postData['atendimento_externo_id'], FILTER_VALIDATE_INT);
        $venda = (float)($postData['valor_unitario'] ?? 0);
        $maoDeObra = (float)($postData['valor_mao_de_obra'] ?? 0);
        $quantidade = (float)($postData['quantidade'] ?? 1);
        $desconto = (float)($postData['desconto'] ?? 0);
        $custo = (float)($postData['valor_custo'] ?? 0);

        // Bloqueio de período fechado: não permite adicionar item em atendimento já concluído com período fechado
        $periodService = new \App\Services\PeriodControlService();
        if ($periodService->isPeriodClosed(date('Y-m-d'))) {
            throw new \RuntimeException('Não é possível adicionar itens no período fiscal atual pois ele está fechado.');
        }

        $dataCompetencia = null;
        $atendJaConcluido = false;
        if ($atendimentoId) {
            $atual = $this->atendimentoModel->find($atendimentoId);
            if ($atual && $atual['status'] === 'concluido') {
                $atendJaConcluido = true;
                $dataCompetencia = date('Y-m-d', strtotime($atual['updated_at'] ?? $atual['created_at'] ?? 'now'));
                if ($periodService->isPeriodClosed($dataCompetencia)) {
                    throw new \RuntimeException('Não é possível adicionar itens a atendimento concluído cujo período já foi fechado.');
                }
            }
        }

        $itemData = [
            'ordem_servico_id'      => null,
            'atendimento_externo_id' => $atendimentoId,
            'tipo_item'             => $postData['tipo'] ?? 'servico',
            'descricao'             => $postData['descricao'] ?? '',
            'quantidade'            => $quantidade,
            'custo'                 => $custo,
            'valor_unitario'        => $venda,
            'valor_mao_de_obra'     => $maoDeObra,
            'desconto'              => $desconto,
            'valor_total'           => (($venda + $maoDeObra) * $quantidade) - $desconto,
            'ativo'                 => 1
        ];

        $itemId = $this->itemModel->create($itemData);
        if ($itemId) {
            // Recalcula totals do atendimento
            $itens = $this->atendimentoModel->listarItens($atendimentoId);
            $this->atendimentoModel->updateTotals($atendimentoId, $itens);

            // ATENDIMENTO JÁ CONCLUÍDO: lança o custo do item NOVO imediatamente com a data da conclusão
            if ($atendJaConcluido) {
                $valorTotalCusto = (float)$quantidade * (float)$custo;
                if ($valorTotalCusto > 0) {
                    $fluxoCaixaModel = new \App\Models\FluxoCaixa();
                    $fluxoCaixaModel->registrarCustoItemAtendimento($itemId, $atendimentoId, $valorTotalCusto, $dataCompetencia);
                }
            }
        }

        return $itemId;
    }

    /**
     * Lança custo de itens + taxa NF no fluxo_caixa SOMENTE no momento da CONCLUSÃO do atendimento
     * (status muda de != 'concluido' para 'concluido'). Executado 1 única vez por atendimento.
     * Data do custo = data de conclusão do atendimento (imutável).
     */
    public function lancarCustoNaConclusao(int $atendimentoId): void
    {
        $atual = $this->atendimentoModel->find($atendimentoId);
        if (!$atual || $atual['status'] !== 'concluido') {
            return;
        }

        $periodService = new \App\Services\PeriodControlService();
        $db = $this->atendimentoModel->getConnection();
        $fluxoCaixaModel = new \App\Models\FluxoCaixa();

        // Data de competência = data da transição para concluido (updated_at preenchido no UPDATE; fallback created_at)
        $dataCompetencia = date('Y-m-d', strtotime($atual['updated_at'] ?? $atual['created_at'] ?? 'now'));

        // Bloqueio: período da conclusão já fechado
        if ($periodService->isPeriodClosed($dataCompetencia)) {
            throw new \RuntimeException('Período fiscal da conclusão do atendimento já está fechado. Contate o administrador.');
        }

        // 1. Custo de cada peça/item
        $itens = $this->atendimentoModel->listarItens($atendimentoId);
        foreach ($itens as $item) {
            if (empty($item['ativo']) || (int)$item['ativo'] !== 1) {
                continue;
            }
            $qtd = (float)($item['quantidade'] ?? 0);
            $custo = (float)($item['valor_custo'] ?? $item['custo'] ?? 0);
            $valorTotalCusto = $qtd * $custo;
            if ($valorTotalCusto > 0) {
                $fluxoCaixaModel->registrarCustoItemAtendimento((int)$item['id'], $atendimentoId, $valorTotalCusto, $dataCompetencia);
            }
        }

        // 2. Custo de taxa NF do atendimento (se estiver marcado para emitir)
        if ((int)($atual['emitir_nf'] ?? 0) === 1) {
            $taxaNf = (float)($atual['valor_taxa_nf'] ?? 0);
            if ($taxaNf > 0) {
                $fluxoCaixaModel->registrarCustoTaxaNf('atendimento', $atendimentoId, $taxaNf, $dataCompetencia);
            }
        }
    }
}

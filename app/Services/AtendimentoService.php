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

        // Salvar o valor calculado da taxa no banco para relatórios
        $this->atendimentoModel->update($id, ['valor_taxa_nf' => $valorTaxaNF]);
        
        return [
            'atendimento' => $atendimento,
            'itens'       => $itens,
            'valor_total' => $totalAtendimento,
            'valor_taxa_nf' => $valorTaxaNF
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

        return $this->atendimentoModel->update($id, $data);
    }

    public function atualizarItem($itemId, $postData)
    {
        $venda = (float)($postData['valor_unitario'] ?? 0);
        $maoDeObra = (float)($postData['valor_mao_de_obra'] ?? 0);
        $quantidade = (float)($postData['quantidade'] ?? 1);
        $desconto = (float)($postData['desconto'] ?? 0);

        $itemData = [
            'quantidade' => $quantidade,
            'valor_unitario' => $venda,
            'valor_mao_de_obra' => $maoDeObra,
            'desconto' => $desconto,
            'valor_total' => (($venda + $maoDeObra) * $quantidade) - $desconto
        ];

        return $this->itemModel->update($itemId, $itemData);
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

        $itemData = [
            'ordem_servico_id'      => null,
            'atendimento_externo_id' => $atendimentoId,
            'tipo_item'             => $postData['tipo'] ?? 'servico',
            'descricao'             => $postData['descricao'] ?? '',
            'quantidade'            => $quantidade,
            'custo'                 => (float)($postData['valor_custo'] ?? 0),
            'valor_unitario'        => $venda,
            'valor_mao_de_obra'     => $maoDeObra,
            'desconto'              => $desconto,
            'valor_total'           => (($venda + $maoDeObra) * $quantidade) - $desconto,
            'ativo'                 => 1
        ];

        return $this->itemModel->create($itemData);
    }
}

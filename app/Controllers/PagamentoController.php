<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\ConfiguracaoGeral;
use App\Models\OrdemServico;
use App\Models\AtendimentoExterno;
use App\Models\PagamentoTransacao;

class PagamentoController extends BaseController
{
    private ConfiguracaoGeral $configModel;
    private PagamentoTransacao $transacaoModel;

    public function __construct()
    {
        parent::__construct();
        $this->configModel = new ConfiguracaoGeral();
        $this->transacaoModel = new PagamentoTransacao();
    }

    public function configurar()
    {
        $json = $this->configModel->getValor('pagamentos_config') ?: '';
        $config = [];
        if (!empty($json)) {
            try { $config = json_decode($json, true) ?: []; } catch (\Throwable $e) { $config = []; }
        }

        $this->render('configuracoes/pagamentos', [
            'title' => 'Configurações de Pagamentos',
            'current_page' => 'configuracoes_pagamentos',
            'config' => $config,
            'config_json' => $json
        ]);
    }

    public function salvarConfig()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('configuracoes/pagamentos');
        }

        $enabled = $_POST['maquinas_enabled'] ?? [];
        $tom = [
            'nome' => 'TOM',
            'habilitada' => in_array('TOM', $enabled, true),
            'formas' => ['debito','credito','pix'],
            'bandeiras' => ['Visa','Mastercard','Elo','American Express'],
            'taxa_debito' => isset($_POST['tom_taxa_debito']) ? (float)$_POST['tom_taxa_debito'] : null,
            'bandeiras_taxas' => [
                'Visa' => isset($_POST['tom_taxa_visa_master']) ? (float)$_POST['tom_taxa_visa_master'] : null,
                'Mastercard' => isset($_POST['tom_taxa_visa_master']) ? (float)$_POST['tom_taxa_visa_master'] : null,
                'Elo' => isset($_POST['tom_taxa_elo_amex']) ? (float)$_POST['tom_taxa_elo_amex'] : null,
                'American Express' => isset($_POST['tom_taxa_elo_amex']) ? (float)$_POST['tom_taxa_elo_amex'] : null
            ],
            'taxa_padrao' => isset($_POST['tom_taxa_outros']) ? (float)$_POST['tom_taxa_outros'] : null
        ];
        $mp = [
            'nome' => 'Mercado Pago',
            'habilitada' => in_array('Mercado Pago', $enabled, true),
            'formas' => ['debito','credito','pix'],
            'bandeiras' => ['Visa','Mastercard','Elo','American Express'],
            'taxa_debito' => isset($_POST['mp_taxa_debito']) ? (float)$_POST['mp_taxa_debito'] : null,
            'bandeiras_taxas' => [
                'Visa' => isset($_POST['mp_taxa_visa_master']) ? (float)$_POST['mp_taxa_visa_master'] : null,
                'Mastercard' => isset($_POST['mp_taxa_visa_master']) ? (float)$_POST['mp_taxa_visa_master'] : null,
                'Elo' => isset($_POST['mp_taxa_elo_amex']) ? (float)$_POST['mp_taxa_elo_amex'] : null,
                'American Express' => isset($_POST['mp_taxa_elo_amex']) ? (float)$_POST['mp_taxa_elo_amex'] : null
            ],
            'taxa_padrao' => isset($_POST['mp_taxa_outros']) ? (float)$_POST['mp_taxa_outros'] : null
        ];
        $mod = [
            'nome' => 'Moderninha',
            'habilitada' => in_array('Moderninha', $enabled, true),
            'formas' => ['debito','credito','pix'],
            'bandeiras' => ['Visa','Mastercard','Elo','American Express'],
            'taxa_debito' => isset($_POST['mod_taxa_debito']) ? (float)$_POST['mod_taxa_debito'] : null,
            'bandeiras_taxas' => [
                'Visa' => isset($_POST['mod_taxa_visa_master']) ? (float)$_POST['mod_taxa_visa_master'] : null,
                'Mastercard' => isset($_POST['mod_taxa_visa_master']) ? (float)$_POST['mod_taxa_visa_master'] : null,
                'Elo' => isset($_POST['mod_taxa_elo_amex']) ? (float)$_POST['mod_taxa_elo_amex'] : null,
                'American Express' => isset($_POST['mod_taxa_elo_amex']) ? (float)$_POST['mod_taxa_elo_amex'] : null
            ],
            'taxa_padrao' => isset($_POST['mod_taxa_outros']) ? (float)$_POST['mod_taxa_outros'] : null
        ];
        $config = [
            'maquinas' => [$tom, $mp, $mod]
        ];
        $json = json_encode($config);

        $ok = $this->configModel->setValor(
            'pagamentos_config',
            $json,
            'Configuração de máquinas, bandeiras e taxas de pagamento (JSON)'
        );

        if ($ok) {
            $_SESSION['success'] = 'Configuração de pagamentos salva com sucesso.';
        } else {
            $_SESSION['error'] = 'Falha ao salvar configuração.';
        }

        $this->redirect('configuracoes/pagamentos');
    }

    public function registrar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->abort(405, 'Método não permitido');
        }

        $tipo = $_POST['tipo_origem'] ?? '';
        $origemId = filter_input(INPUT_POST, 'origem_id', FILTER_VALIDATE_INT);
        $maquina = $_POST['maquina'] ?? '';
        $forma = $_POST['forma'] ?? '';
        $bandeira = $_POST['bandeira'] ?? '';
        $parcelas = filter_input(INPUT_POST, 'parcelas', FILTER_VALIDATE_INT) ?: 1;
        $valorBruto = filter_input(INPUT_POST, 'valor_bruto', FILTER_VALIDATE_FLOAT);

        if (!in_array($tipo, ['os', 'atendimento'], true) || !$origemId || !$valorBruto || $valorBruto <= 0) {
            $this->json(['error' => 'Dados inválidos'], 400);
        }

        $configJson = $this->configModel->getValor('pagamentos_config') ?: '';
        $config = [];
        if (!empty($configJson)) { $config = json_decode($configJson, true) ?: []; }

        $apiResult = null;

        $taxaPercentual = $this->calcularTaxa($config, $maquina, $forma, $bandeira, $parcelas) ?? 0.0;
        $valorTaxa = round(($valorBruto * ($taxaPercentual / 100)), 2);
        $valorLiquido = round(($valorBruto - $valorTaxa), 2);

        $data = [
            'tipo_origem'   => $tipo,
            'origem_id'     => $origemId,
            'maquina'       => $maquina,
            'forma'         => $forma,
            'bandeira'      => $bandeira,
            'parcelas'      => $parcelas,
            'taxa_percentual' => $taxaPercentual,
            'valor_bruto'   => $valorBruto,
            'valor_taxa'    => $valorTaxa,
            'valor_liquido' => $valorLiquido,
            'usuario_id'    => Auth::id()
        ];

        $id = $this->transacaoModel->create($data);
        if (!$id) {
            $this->json(['error' => 'Falha ao registrar pagamento'], 500);
        }

        $this->atualizarStatusPagamento($tipo, $origemId);

        $this->json([
            'success' => true,
            'transacao_id' => $id,
            'taxa_percentual' => $taxaPercentual,
            'valor_taxa' => $valorTaxa,
            'valor_liquido' => $valorLiquido,
            'api' => $apiResult
        ]);
    }

    private function calcularTaxa(array $config, string $maquina, string $forma, string $bandeira, int $parcelas): ?float
    {
        if (empty($config['maquinas']) || !is_array($config['maquinas'])) return null;
        foreach ($config['maquinas'] as $mq) {
            if (($mq['nome'] ?? '') !== $maquina) continue;
            if ($forma === 'debito' && isset($mq['taxa_debito']) && $mq['taxa_debito'] !== null) {
                return (float)$mq['taxa_debito'];
            }
            if (!empty($mq['bandeiras_taxas']) && is_array($mq['bandeiras_taxas'])) {
                if (isset($mq['bandeiras_taxas'][$bandeira]) && $mq['bandeiras_taxas'][$bandeira] !== null) {
                    return (float)$mq['bandeiras_taxas'][$bandeira];
                }
                if (isset($mq['taxa_padrao']) && $mq['taxa_padrao'] !== null) {
                    return (float)$mq['taxa_padrao'];
                }
            }
            if (!empty($mq['formas']) && is_array($mq['formas'])) {
                if (is_array($mq['formas']) && in_array($forma, $mq['formas'], true) && isset($mq['taxa_padrao'])) {
                    return (float)$mq['taxa_padrao'];
                }
            }
        }
        return null;
    }

    private function atualizarStatusPagamento(string $tipo, int $origemId): void
    {
        $totalPago = $this->transacaoModel->sumByOrigem($tipo, $origemId);
        if ($tipo === 'os') {
            $osModel = new OrdemServico();
            $os = $osModel->findWithDetails($origemId);
            if (!$os) return;
            $totalDue = (float)($os['valor_total_os'] ?? 0) - (float)($os['valor_desconto'] ?? 0);
            $novoStatus = $totalPago >= $totalDue ? 'pago' : ($totalPago > 0 ? 'parcial' : 'pendente');
            $osModel->update($origemId, ['status_pagamento' => $novoStatus]);
        } else {
            $service = new \App\Services\AtendimentoService();
            $dados = $service->obterDetalhesVisualizacao($origemId);
            if (!$dados) return;
            $totalDue = (float)($dados['valor_total'] ?? 0);
            $novoStatus = $totalPago >= $totalDue ? 'pago' : ($totalPago > 0 ? 'parcial' : 'pendente');
            $aeModel = new AtendimentoExterno();
            $aeModel->update($origemId, ['pagamento' => $novoStatus]);
        }
    }

    public function deletar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->abort(405, 'Método não permitido');
        }
        $id = filter_input(INPUT_POST, 'transacao_id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->json(['error' => 'Transação inválida'], 400);
        }
        $tx = $this->transacaoModel->find($id);
        if (!$tx || (int)($tx['ativo'] ?? 0) === 0) {
            $this->json(['error' => 'Transação não encontrada ou já excluída'], 404);
        }
        $ok = $this->transacaoModel->softDelete($id);
        if (!$ok) {
            $this->json(['error' => 'Falha ao excluir transação'], 500);
        }
        $this->logModel->registrar(Auth::id(), 'Excluiu transação de pagamento', "Transação #{$id}", $tx, ['ativo' => 0]);
        $tipo = $tx['tipo_origem'] ?? '';
        $origemId = (int)($tx['origem_id'] ?? 0);
        if ($tipo && $origemId) {
            $this->atualizarStatusPagamento($tipo, $origemId);
        }
        $this->json(['success' => true]);
    }
}

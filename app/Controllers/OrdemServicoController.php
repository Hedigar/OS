<?php

namespace App\Controllers;

use App\Models\OrdemServico;
use App\Models\Cliente;
use App\Models\StatusOS;
use App\Models\ItemOS;
use App\Models\Equipamento;

class OrdemServicoController extends BaseController
{
    private $osModel;
    private $clienteModel;
    private $statusModel;
    private $itemModel;
    private $equipamentoModel;
    private $historicoModel;

    public function __construct()
    {
        parent::__construct();
        $this->osModel = new OrdemServico();
        $this->clienteModel = new Cliente();
        $this->statusModel = new StatusOS();
        $this->itemModel = new ItemOS();
        $this->equipamentoModel = new Equipamento();
        $this->historicoModel = new \App\Models\StatusHistorico();
    }

    public function searchClient()
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $termo = filter_input(INPUT_GET, 'termo', FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($termo)) {
            echo json_encode([]);
            exit;
        }

        try {
            $whereClause = "nome_completo LIKE :term_nome OR documento LIKE :term_documento";
            $params = [
                'term_nome' => "%{$termo}%",
                'term_documento' => "%{$termo}%"
            ];

            $clientes = $this->clienteModel->getPaginated(10, 0, $whereClause, $params);
            
            echo json_encode($clientes);
            exit;
        } catch (\Throwable $e) {
            echo json_encode([]);
            exit;
        }
    }

    public function index()
    {
        $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS) ?: '';
        $ordens = $this->osModel->getAllWithDetails($search);
        $this->render('os/index', [
            'title' => 'Gerenciar Ordens de Serviço',
            'ordens' => $ordens,
            'search' => $search
        ]);
    }

    public function form()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $ordem = null;
        $title = 'Nova Ordem de Serviço';

        if ($id) {
            $ordem = $this->osModel->findWithDetails($id);
            $title = 'Editar Ordem de Serviço';
            if (!$ordem) {
                $this->redirect('ordens');
            }
        }

        $this->render('os/form', [
            'title' => $title,
            'ordem' => $ordem,
            'statuses' => $this->statusModel->getAll()
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cliente_id = filter_input(INPUT_POST, 'cliente_id', FILTER_VALIDATE_INT);
            $equipamento_id = filter_input(INPUT_POST, 'equipamento_id', FILTER_VALIDATE_INT);
            
            if (!$cliente_id) {
                $this->redirect('ordens/form?error=Cliente obrigatório');
            }

            if (!$equipamento_id) {
                $serial = filter_input(INPUT_POST, 'serial_equipamento', FILTER_SANITIZE_SPECIAL_CHARS);
                $existente = $this->equipamentoModel->findBySerial($cliente_id, $serial);
                
                if ($existente) {
                    $equipamento_id = $existente['id'];
                } else {
                    $equipamentoData = [
                        'cliente_id' => $cliente_id,
                        'tipo' => filter_input(INPUT_POST, 'tipo_equipamento', FILTER_SANITIZE_SPECIAL_CHARS),
                        'marca' => filter_input(INPUT_POST, 'marca_equipamento', FILTER_SANITIZE_SPECIAL_CHARS),
                        'modelo' => filter_input(INPUT_POST, 'modelo_equipamento', FILTER_SANITIZE_SPECIAL_CHARS),
                        'serial' => $serial,
                        'senha' => filter_input(INPUT_POST, 'senha_equipamento', FILTER_SANITIZE_SPECIAL_CHARS),
	                        'acessorios' => filter_input(INPUT_POST, 'acessorios_equipamento', FILTER_SANITIZE_SPECIAL_CHARS),
	                        'possui_fonte' => filter_input(INPUT_POST, 'fonte_equipamento', FILTER_SANITIZE_SPECIAL_CHARS) === 'sim' ? 1 : 0,
	                        'sn_fonte' => filter_input(INPUT_POST, 'sn_fonte', FILTER_SANITIZE_SPECIAL_CHARS),
	                    ];
                    $equipamento_id = $this->equipamentoModel->create($equipamentoData);
                }
            }

	            $osData = [
	                'cliente_id' => $cliente_id,
	                'equipamento_id' => $equipamento_id,
	                'defeito_relatado' => filter_input(INPUT_POST, 'defeito', FILTER_SANITIZE_SPECIAL_CHARS),
	                'laudo_tecnico' => filter_input(INPUT_POST, 'laudo_tecnico', FILTER_SANITIZE_SPECIAL_CHARS),
	                'status_atual_id' => filter_input(INPUT_POST, 'status_id', FILTER_VALIDATE_INT) ?: 1,
	                'status_pagamento' => filter_input(INPUT_POST, 'status_pagamento', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'pendente',
	                'status_entrega' => filter_input(INPUT_POST, 'status_entrega', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'nao_entregue',
	            ];

            $osId = $this->osModel->create($osData);

            if ($osId) {
                $this->log("Criou nova Ordem de Serviço", "OS #{$osId}");
                $this->redirect('clientes/view?id=' . $cliente_id . '&new_os_id=' . $osId);
            } else {
                $this->redirect('ordens/form?error=Erro ao salvar OS');
            }
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) $this->redirect('ordens');

            $status_id = filter_input(INPUT_POST, 'status_id', FILTER_VALIDATE_INT);
            $status_pagamento = filter_input(INPUT_POST, 'status_pagamento', FILTER_SANITIZE_SPECIAL_CHARS);
            $status_entrega = filter_input(INPUT_POST, 'status_entrega', FILTER_SANITIZE_SPECIAL_CHARS);
            $observacao = filter_input(INPUT_POST, 'status_observacao', FILTER_SANITIZE_SPECIAL_CHARS);
            
            $osAntiga = $this->osModel->find($id);

	            $osData = [
	                'status_atual_id' => $status_id,
	                'status_pagamento' => $status_pagamento,
	                'status_entrega' => $status_entrega,
	                'laudo_tecnico' => filter_input(INPUT_POST, 'laudo_tecnico', FILTER_SANITIZE_SPECIAL_CHARS),
	            ];
	
	            $sn_fonte = filter_input(INPUT_POST, 'sn_fonte', FILTER_SANITIZE_SPECIAL_CHARS);
	            if ($sn_fonte !== null) {
	                $ordem_atual = $this->osModel->find($id);
	                if ($ordem_atual && $ordem_atual['equipamento_id']) {
	                    $this->equipamentoModel->update($ordem_atual['equipamento_id'], ['sn_fonte' => $sn_fonte]);
	                }
	            }

            if (isset($_SESSION['usuario_nivel']) && $_SESSION['usuario_nivel'] === 'admin') {
                $osData['defeito_relatado'] = filter_input(INPUT_POST, 'defeito', FILTER_SANITIZE_SPECIAL_CHARS);
            }

            if ($this->osModel->update($id, $osData)) {
                // Se o status mudou ou se há uma observação, registra no histórico
                if ($osAntiga['status_atual_id'] != $status_id || !empty($observacao)) {
                    $this->historicoModel->create([
                        'ordem_servico_id' => $id,
                        'status_id' => $status_id,
                        'usuario_id' => $_SESSION['usuario_id'] ?? null,
                        'observacao' => $observacao
                    ]);
                }

                $this->log("Atualizou Ordem de Serviço", "OS #{$id}");
                $this->redirect('ordens/view?id=' . $id);
            } else {
                $this->redirect('ordens/form?id=' . $id . '&error=Erro ao atualizar');
            }
        }
    }

    public function showView()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) $this->redirect('ordens');

        $ordem = $this->osModel->findWithDetails($id);
        if (!$ordem) $this->redirect('ordens');

        $itens = $this->itemModel->findByOsId($id);
        $historico = $this->historicoModel->findByOsId($id);
        $statuses = $this->statusModel->getAll();
        
        $configModel = new \App\Models\ConfiguracaoGeral();
        $margemLucro = $configModel->getValor('porcentagem_venda') ?: 0;

        $this->render('os/view', [
            'title' => 'Ordem de Serviço #' . $id,
            'ordem' => $ordem,
            'itens' => $itens,
            'historico' => $historico,
            'statuses' => $statuses,
            'margem_lucro' => $margemLucro
        ]);
    }

    public function printOS()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) $this->redirect('ordens');

        $ordem = $this->osModel->findWithDetails($id);
        if (!$ordem) $this->redirect('ordens');

        $itens = $this->itemModel->findByOsId($id);

        $this->renderPDF('os/print', [
            'ordem' => $ordem,
            'itens' => $itens
        ], "OS_{$id}.pdf");
    }

    public function printReceipt()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) $this->redirect('ordens');

        $ordem = $this->osModel->findWithDetails($id);
        if (!$ordem) $this->redirect('ordens');

        $itens = $this->itemModel->findByOsId($id);

        $this->renderPDF('os/print_receipt', [
            'ordem' => $ordem,
            'itens' => $itens
        ], "Recibo_OS_{$id}.pdf");
    }

    public function printEstimate()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) $this->redirect('ordens');

        $ordem = $this->osModel->findWithDetails($id);
        if (!$ordem) $this->redirect('ordens');

        $itens = $this->itemModel->findByOsId($id);

        $this->renderPDF('os/print_orcamento', [
            'ordem' => $ordem,
            'itens' => $itens
        ], "Orcamento_OS_{$id}.pdf");
    }

    public function searchEquipamentos()
    {
        error_reporting(0);
        if (ob_get_length()) ob_clean();
        $clienteId = filter_input(INPUT_GET, 'cliente_id', FILTER_VALIDATE_INT);

        if (!$clienteId) {
            $this->jsonResponse([]);
            return;
        }

        try {
            $equipamentos = $this->equipamentoModel->findByClienteId($clienteId);
            $this->jsonResponse($equipamentos);
        } catch (\Throwable $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function searchItems()
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $termo = filter_input(INPUT_GET, 'termo', FILTER_SANITIZE_SPECIAL_CHARS);
        if (empty($termo)) {
            echo json_encode([]);
            exit;
        }

        try {
            $produtoModel = new \App\Models\ProdutoServico();
            $sql = "SELECT * FROM produtos_servicos WHERE nome LIKE :termo AND ativo = 1 LIMIT 10";
            $stmt = $produtoModel->getConnection()->prepare($sql);
            $stmt->execute(['termo' => "%{$termo}%"]);
            $itens = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            echo json_encode($itens);
            exit;
        } catch (\Throwable $e) {
            echo json_encode([]);
            exit;
        }
    }

    public function saveItem()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $osId = filter_input(INPUT_POST, 'ordem_servico_id', FILTER_VALIDATE_INT);
            $produtoId = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);
            $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_FLOAT) ?: 1;
            
            $custo = filter_input(INPUT_POST, 'valor_custo', FILTER_VALIDATE_FLOAT) ?: 0;
            $venda = filter_input(INPUT_POST, 'valor_unitario', FILTER_VALIDATE_FLOAT) ?: 0;
            $maoDeObra = filter_input(INPUT_POST, 'valor_mao_de_obra', FILTER_VALIDATE_FLOAT) ?: 0;
            $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS);
            $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'produto';
            
            $comprarPeca = filter_input(INPUT_POST, 'comprar_peca', FILTER_VALIDATE_INT) ?: 0;
            $linkFornecedor = filter_input(INPUT_POST, 'link_fornecedor', FILTER_SANITIZE_URL);

            $itemData = [
                'ordem_servico_id' => $osId,
                'tipo_item' => $tipo,
                'descricao' => $descricao,
                'quantidade' => $quantidade,
                'custo' => $custo,
                'valor_unitario' => $venda,
                'valor_mao_de_obra' => $maoDeObra,
                'valor_total' => ($venda + $maoDeObra) * $quantidade,
                'comprar_peca' => $comprarPeca,
                'link_fornecedor' => $linkFornecedor,
                'ativo' => 1
            ];

            if ($this->itemModel->create($itemData)) {
                $itens = $this->itemModel->findByOsId($osId);
                $this->osModel->updateTotals($osId, $itens);
                $this->redirect('ordens/view?id=' . $osId);
            } else {
                $this->redirect('ordens/view?id=' . $osId . '&error=Erro ao adicionar item');
            }
        }
    }

    public function updateItem()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemId = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
            $osId = filter_input(INPUT_POST, 'ordem_servico_id', FILTER_VALIDATE_INT);
            
            $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_FLOAT);
            $custo = filter_input(INPUT_POST, 'custo', FILTER_VALIDATE_FLOAT);
            $venda = filter_input(INPUT_POST, 'valor_unitario', FILTER_VALIDATE_FLOAT);
            $maoDeObra = filter_input(INPUT_POST, 'valor_mao_de_obra', FILTER_VALIDATE_FLOAT);

            $itemData = [
                'quantidade' => $quantidade,
                'custo' => $custo,
                'valor_unitario' => $venda,
                'valor_mao_de_obra' => $maoDeObra,
                'valor_total' => ($venda + $maoDeObra) * $quantidade
            ];

            if ($this->itemModel->update($itemId, $itemData)) {
                $itens = $this->itemModel->findByOsId($osId);
                $this->osModel->updateTotals($osId, $itens);
                $this->redirect('ordens/view?id=' . $osId);
            } else {
                $this->redirect('ordens/view?id=' . $osId . '&error=Erro ao atualizar item');
            }
        }
    }

    public function removeItem()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemId = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
            $osId = filter_input(INPUT_POST, 'ordem_servico_id', FILTER_VALIDATE_INT);

            if ($this->itemModel->delete($itemId)) {
                $itens = $this->itemModel->findByOsId($osId);
                $this->osModel->updateTotals($osId, $itens);
                $this->redirect('ordens/view?id=' . $osId);
            } else {
                $this->redirect('ordens/view?id=' . $osId . '&error=Erro ao remover item');
            }
        }
    }

    public function destroy()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if ($id) {
                if ($this->osModel->delete($id)) {
                    $this->log("Excluiu Ordem de Serviço", "OS #{$id}");
                }
            }
        }
        $this->redirect('ordens');
    }
}

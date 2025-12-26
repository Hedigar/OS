<?php

namespace App\Controllers;

use App\Models\OrdemServico;
use App\Models\Cliente;
use App\Models\StatusOS;
use App\Models\ItemOS;
use App\Models\EquipamentoOS;

class OrdemServicoController extends BaseController
{
    private $osModel;
    private $clienteModel;
    private $statusModel;
    private $itemModel;
    private $equipamentoModel;

    public function __construct()
    {
        parent::__construct();
        $this->osModel = new OrdemServico();
        $this->clienteModel = new Cliente();
        $this->statusModel = new StatusOS();
        $this->itemModel = new ItemOS();
        $this->equipamentoModel = new EquipamentoOS();
    }

    // Endpoint para pesquisa de clientes via AJAX
    public function searchClient()
    {
        $this->requireAjax();
        $termo = filter_input(INPUT_GET, 'termo', FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($termo)) {
            $this->jsonResponse([]);
            return;
        }

        $clientes = $this->clienteModel->buscarPorTermo($termo);
        $this->jsonResponse($clientes);
    }

    // Listar todas as ordens de serviço
    public function index()
    {
        $ordens = $this->osModel->getAllWithDetails();
        $this->render('os/index', [
            'title' => 'Gerenciar Ordens de Serviço',
            'ordens' => $ordens
        ]);
    }

    // Mostrar formulário de criação/edição
    public function form()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $ordem = null;
        $title = 'Nova Ordem de Serviço';
        $clientes = $this->clienteModel->all(); // Para o select de clientes

        if ($id) {
            $ordem = $this->osModel->findWithDetails($id);
            $title = 'Editar Ordem de Serviço';
            if (!$ordem) {
                $this->redirect('ordens');
            }
            // Carrega o equipamento associado à OS (assumindo 1:1 no fluxo de edição)
            $equipamento = $this->equipamentoModel->findByOsId($id);
            // Mescla os dados do equipamento na ordem para facilitar o preenchimento do formulário
            if ($equipamento) {
                $ordem = array_merge($ordem, $equipamento);
            }
        }

        $this->render('os/form', [
            'title' => $title,
            'ordem' => $ordem,
            'clientes' => $clientes
        ]);
    }

    // Salvar nova ordem de serviço
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'cliente_id' => filter_input(INPUT_POST, 'cliente_id', FILTER_VALIDATE_INT),
                'servico' => filter_input(INPUT_POST, 'servico', FILTER_SANITIZE_SPECIAL_CHARS),
                'observacoes_tecnicas' => filter_input(INPUT_POST, 'observacoes_tecnicas', FILTER_SANITIZE_SPECIAL_CHARS),
                'valor' => filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT),
                'status_id' => filter_input(INPUT_POST, 'status_id', FILTER_VALIDATE_INT),
            ];

            // Dados do Equipamento para a tabela equipamentos_os
            $equipamentoData = [
                'tipo_equipamento' => filter_input(INPUT_POST, 'tipo_equipamento', FILTER_SANITIZE_SPECIAL_CHARS),
                'marca_equipamento' => filter_input(INPUT_POST, 'marca_equipamento', FILTER_SANITIZE_SPECIAL_CHARS),
                'modelo_equipamento' => filter_input(INPUT_POST, 'modelo_equipamento', FILTER_SANITIZE_SPECIAL_CHARS),
                'serial_equipamento' => filter_input(INPUT_POST, 'serial_equipamento', FILTER_SANITIZE_SPECIAL_CHARS),
                'senha_equipamento' => filter_input(INPUT_POST, 'senha_equipamento', FILTER_SANITIZE_SPECIAL_CHARS),
                'acessorios_equipamento' => filter_input(INPUT_POST, 'acessorios_equipamento', FILTER_SANITIZE_SPECIAL_CHARS),
                'fonte_equipamento' => filter_input(INPUT_POST, 'fonte_equipamento', FILTER_SANITIZE_SPECIAL_CHARS),
                'defeito_relatado' => filter_input(INPUT_POST, 'defeito', FILTER_SANITIZE_SPECIAL_CHARS),
                'observacoes_tecnicas' => filter_input(INPUT_POST, 'observacoes_tecnicas', FILTER_SANITIZE_SPECIAL_CHARS), // Será renomeado para laudo_tecnico no DB
            ];

            // Cria a Ordem de Serviço (apenas dados da OS)
            $osId = $this->osModel->create($data);

            if ($osId) {
                // Cria o registro do Equipamento na tabela equipamentos_os
                $equipamentoData['os_id'] = $osId;
                $this->equipamentoModel->create($equipamentoData);
                $itens = $_POST['itens'] ?? [];
                $itensSalvos = [];

                foreach ($itens as $item) {
                    if (!empty($item['descricao']) && is_numeric($item['quantidade']) && is_numeric($item['valor_unitario'])) {
                        $valorTotal = $item['quantidade'] * $item['valor_unitario'];
                        $itemData = [
                            'os_id' => $osId,
                            'tipo' => $item['tipo'],
                            'descricao' => filter_var($item['descricao'], FILTER_SANITIZE_SPECIAL_CHARS),
                            'quantidade' => $item['quantidade'],
                            'valor_unitario' => $item['valor_unitario'],
                            'valor_total' => $valorTotal
                        ];
                        $this->itemModel->create($itemData);
                        $itensSalvos[] = $itemData;
                    }
                }

                $this->osModel->updateTotals($osId, $itensSalvos);
                // Redireciona para a página do cliente, passando o ID da nova OS para abrir a impressão
                $this->redirect('clientes/view?id=' . $data['cliente_id'] . '&new_os_id=' . $osId);
            } else {
                $clientes = $this->clienteModel->all();
                $this->render('os/form', ['error' => 'Erro ao salvar Ordem de Serviço.', 'clientes' => $clientes]);
            }
        }
    }

    // Atualizar ordem de serviço
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                $this->redirect('ordens');
            }

            $data = [
                'cliente_id' => filter_input(INPUT_POST, 'cliente_id', FILTER_VALIDATE_INT),
                'servico' => filter_input(INPUT_POST, 'servico', FILTER_SANITIZE_SPECIAL_CHARS),
                'observacoes_tecnicas' => filter_input(INPUT_POST, 'observacoes_tecnicas', FILTER_SANITIZE_SPECIAL_CHARS),
                'valor' => filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT),
                'status_id' => filter_input(INPUT_POST, 'status_id', FILTER_VALIDATE_INT),
            ];

            // Dados do Equipamento para a tabela equipamentos_os
            $equipamentoData = [
                'tipo_equipamento' => filter_input(INPUT_POST, 'tipo_equipamento', FILTER_SANITIZE_SPECIAL_CHARS),
                'marca_equipamento' => filter_input(INPUT_POST, 'marca_equipamento', FILTER_SANITIZE_SPECIAL_CHARS),
                'modelo_equipamento' => filter_input(INPUT_POST, 'modelo_equipamento', FILTER_SANITIZE_SPECIAL_CHARS),
                'serial_equipamento' => filter_input(INPUT_POST, 'serial_equipamento', FILTER_SANITIZE_SPECIAL_CHARS),
                'senha_equipamento' => filter_input(INPUT_POST, 'senha_equipamento', FILTER_SANITIZE_SPECIAL_CHARS),
                'acessorios_equipamento' => filter_input(INPUT_POST, 'acessorios_equipamento', FILTER_SANITIZE_SPECIAL_CHARS),
                'fonte_equipamento' => filter_input(INPUT_POST, 'fonte_equipamento', FILTER_SANITIZE_SPECIAL_CHARS),
                'defeito_relatado' => filter_input(INPUT_POST, 'defeito', FILTER_SANITIZE_SPECIAL_CHARS),
                'observacoes_tecnicas' => filter_input(INPUT_POST, 'observacoes_tecnicas', FILTER_SANITIZE_SPECIAL_CHARS), // Será renomeado para laudo_tecnico no DB
            ];

            if ($this->osModel->update($id, $data)) {
                // Atualiza o registro do Equipamento na tabela equipamentos_os
                // Como o modelo atual suporta 1:N, vamos assumir que no modo de edição,
                // estamos editando o primeiro (e único, por enquanto) equipamento.
                // Na prática, seria necessário um campo oculto para o ID do equipamento.
                // Por simplicidade, vamos buscar o primeiro equipamento associado a esta OS.
                $equipamentoExistente = $this->equipamentoModel->findByOsId($id);
                if ($equipamentoExistente) {
                    $this->equipamentoModel->update($equipamentoExistente['id'], $equipamentoData);
                } else {
                    // Se por algum motivo não existir, cria um novo (não deve acontecer)
                    $equipamentoData['os_id'] = $id;
                    $this->equipamentoModel->create($equipamentoData);
                }
                $this->itemModel->deleteByOsId($id); // Limpa itens antigos

                $itens = $_POST['itens'] ?? [];
                $itensSalvos = [];

                foreach ($itens as $item) {
                    if (!empty($item['descricao']) && is_numeric($item['quantidade']) && is_numeric($item['valor_unitario'])) {
                        $valorTotal = $item['quantidade'] * $item['valor_unitario'];
                        $itemData = [
                            'os_id' => $id,
                            'tipo' => $item['tipo'],
                            'descricao' => filter_var($item['descricao'], FILTER_SANITIZE_SPECIAL_CHARS),
                            'quantidade' => $item['quantidade'],
                            'valor_unitario' => $item['valor_unitario'],
                            'valor_total' => $valorTotal
                        ];
                        $this->itemModel->create($itemData);
                        $itensSalvos[] = $itemData;
                    }
                }

                $this->osModel->updateTotals($id, $itensSalvos);
                $this->redirect('ordens/view?id=' . $id);
            } else {
                $ordem = $this->osModel->findWithDetails($id);
                $clientes = $this->clienteModel->all();
                $this->render('os/form', ['error' => 'Erro ao atualizar Ordem de Serviço.', 'ordem' => $ordem, 'clientes' => $clientes]);
            }
        }
    }

	    // Busca o primeiro equipamento associado a uma OS (para o modo de edição)
	    private function getEquipamentoByOsId(int $osId): ?array
	    {
	        // No modelo atual, assumimos que a tela de edição só lida com o primeiro equipamento
	        // (que é o único criado no fluxo de abertura).
	        $sql = "SELECT * FROM equipamentos_os WHERE os_id = :os_id LIMIT 1";
	        $stmt = $this->db->prepare($sql);
	        $stmt->execute(['os_id' => $osId]);
	        return $stmt->fetch(\PDO::FETCH_ASSOC);
	    }

	    // Visualizar ordem de serviço
	    public function showView()
	    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect('ordens');
        }

        $ordem = $this->osModel->findWithDetails($id);
        if (!$ordem) {
            $this->redirect('ordens');
        }

        $itens = $this->itemModel->findByOsId($id);

        $this->render('os/view', [
            'title' => 'Ordem de Serviço #' . $id,
            'ordem' => $ordem,
            'itens' => $itens
        ]);
    }

    // Imprimir ordem de serviço
    public function printOS()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect('ordens');
        }

        $ordem = $this->osModel->findWithDetails($id);
        if (!$ordem) {
            $this->redirect('ordens');
        }

        $itens = $this->itemModel->findByOsId($id);

        // Busca dados do cliente para a impressão
        $cliente = $this->clienteModel->find($ordem['cliente_id']);
        $ordem['telefone_principal'] = $cliente['telefone_principal'] ?? 'N/A';
        $ordem['email'] = $cliente['email'] ?? 'N/A';

        $this->render('os/print', [
            'ordem' => $ordem,
            'itens' => $itens
        ], false); // O terceiro parâmetro 'false' indica que não deve usar o layout principal
    }

    // Busca equipamentos anteriores de um cliente
    public function searchPreviousEquipments()
    {
        $this->requirePost(); // Garante que é um POST, embora GET seja comum para AJAX de busca
        $clienteId = filter_input(INPUT_GET, 'cliente_id', FILTER_VALIDATE_INT);

        if (!$clienteId) {
            $this->jsonResponse([]);
            return;
        }

        $equipamentos = $this->equipamentoModel->findPreviousEquipmentsByClient($clienteId);
        $this->jsonResponse($equipamentos);
    }

    // Deletar ordem de serviço
    public function destroy()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if ($id && $this->osModel->delete($id)) {
                // Sucesso
            }
        }
        $this->redirect('ordens');
    }
}

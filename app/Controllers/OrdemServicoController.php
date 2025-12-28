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

    public function __construct()
    {
        parent::__construct();
        $this->osModel = new OrdemServico();
        $this->clienteModel = new Cliente();
        $this->statusModel = new StatusOS();
        $this->itemModel = new ItemOS();
        $this->equipamentoModel = new Equipamento();
    }

    public function searchClient()
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        // Usando a mesma lógica de captura do ClienteController
        $termo = filter_input(INPUT_GET, 'termo', FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($termo)) {
            echo json_encode([]);
            exit;
        }

        try {
            // Em vez de usar o método buscarPorTermo, vamos usar a lógica direta que funciona
            $whereClause = "nome_completo LIKE :term_nome OR documento LIKE :term_documento";
            $params = [
                'term_nome' => "%{$termo}%",
                'term_documento' => "%{$termo}%"
            ];

            // Usando o método getPaginated que você já confirmou que funciona na listagem
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
        $ordens = $this->osModel->getAllWithDetails();
        $this->render('os/index', [
            'title' => 'Gerenciar Ordens de Serviço',
            'ordens' => $ordens
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
                    ];
                    $equipamento_id = $this->equipamentoModel->create($equipamentoData);
                }
            }

            $osData = [
                'cliente_id' => $cliente_id,
                'equipamento_id' => $equipamento_id,
                'defeito_relatado' => filter_input(INPUT_POST, 'defeito', FILTER_SANITIZE_SPECIAL_CHARS),
                'status_atual_id' => filter_input(INPUT_POST, 'status_id', FILTER_VALIDATE_INT) ?: 1,
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

            $osData = [
                'status_atual_id' => filter_input(INPUT_POST, 'status_id', FILTER_VALIDATE_INT),
                'defeito_relatado' => filter_input(INPUT_POST, 'defeito', FILTER_SANITIZE_SPECIAL_CHARS),
                'laudo_tecnico' => filter_input(INPUT_POST, 'laudo_tecnico', FILTER_SANITIZE_SPECIAL_CHARS),
            ];

            if ($this->osModel->update($id, $osData)) {
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

        $this->render('os/view', [
            'title' => 'Ordem de Serviço #' . $id,
            'ordem' => $ordem,
            'itens' => $itens
        ]);
    }

    public function printOS()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) $this->redirect('ordens');

        $ordem = $this->osModel->findWithDetails($id);
        if (!$ordem) $this->redirect('ordens');

        $itens = $this->itemModel->findByOsId($id);

        $this->render('os/print', [
            'ordem' => $ordem,
            'itens' => $itens
        ], false);
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

<?php

namespace App\Controllers;

use App\Models\Cliente;
use App\Models\OrdemServico;
use App\Models\Equipamento;
use App\Models\AtendimentoExterno;
use App\Services\ClienteService;

class ClienteController extends BaseController
{
    private $clienteModel;
    private ClienteService $service;

    public function __construct()
    {
        parent::__construct();
        $this->clienteModel = new Cliente();
        $this->service = new ClienteService();
    }

    public function index()
    {
        $porPagina = 10;
        $paginaAtual = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT) ?: 1;
        $offset = ($paginaAtual - 1) * $porPagina;
        $termo = filter_input(INPUT_GET, 'busca', FILTER_SANITIZE_SPECIAL_CHARS);

        [$whereClause, $params] = $this->service->buildSearchFilters($termo);

        $totalClientes = $this->clienteModel->countAll($whereClause, $params);
        $clientes = $this->clienteModel->getPaginated($porPagina, $offset, $whereClause, $params);
        $totalPaginas = ceil($totalClientes / $porPagina);

        $this->render('cliente/index', [
            'title' => 'Gerenciar Clientes',
            'clientes' => $clientes,
            'busca' => $termo,
            'paginaAtual' => $paginaAtual,
            'totalPaginas' => $totalPaginas
        ]);
    }

    public function create()
    {
        $this->render('cliente/form', [
            'title' => 'Novo Cliente',
            'cliente' => null
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->service->normalizePostData($_POST);
            
            $existenteId = $this->service->documentoExistente($data['documento'] ?? null);
            if ($existenteId) {
                $this->redirect('clientes/view?id=' . $existenteId);
                return;
            }

            try {
                if ($id = $this->clienteModel->create($data)) {
                    $this->log("Criou novo cliente", "Cliente #{$id} - {$data['nome_completo']}");
                    $this->redirect('clientes/view?id=' . $id);
                } else {
                    $this->render('cliente/form', ['error' => 'Erro ao salvar cliente.', 'cliente' => $data]);
                }
            } catch (\PDOException $e) {
                if ($e->getCode() == 23000) { // Duplicate entry
                    $existenteId = $this->service->documentoExistente($data['documento'] ?? null);
                    if ($existenteId) {
                        $this->redirect('clientes/view?id=' . $existenteId);
                    } else {
                        $this->render('cliente/form', ['error' => 'Este documento já está cadastrado no sistema.', 'cliente' => $data]);
                    }
                } else {
                    $this->render('cliente/form', ['error' => 'Erro no banco de dados: ' . $e->getMessage(), 'cliente' => $data]);
                }
            }
        }
    }

    public function showView()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) $this->redirect('clientes');

        $dados = $this->service->obterDadosVisualizacao($id);
        if (empty($dados)) $this->redirect('clientes');

        $this->render('cliente/view', [
            'title' => 'Visualizar Cliente',
            'cliente' => $dados['cliente'],
            'historicoOS' => $dados['historicoOS'],
            'equipamentos' => $dados['equipamentos'],
            'historicoExterno' => $dados['historicoExterno']
        ]);
    }

    public function printDebitos()
    {
        $clienteId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$clienteId) $this->redirect('clientes');

        $cliente = $this->clienteModel->find($clienteId);
        if (!$cliente) $this->redirect('clientes');

        [$debitosOS, $debitosAE] = $this->service->gerarDebitos($clienteId);

        $this->renderPDF('cliente/print_debitos', [
            'cliente' => $cliente,
            'debitosOS' => $debitosOS,
            'debitosAE' => $debitosAE
        ], "Debitos_{$clienteId}.pdf");
    }

    public function edit()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) $this->redirect('clientes');

        $cliente = $this->clienteModel->find($id);
        if (!$cliente) $this->redirect('clientes');

        $this->render('cliente/form', [
            'title' => 'Editar Cliente',
            'cliente' => $cliente
        ]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) $this->redirect('clientes');

            $data = $this->service->normalizePostData($_POST);
            
            if ($this->service->documentoExistenteOutroCliente($data['documento'] ?? null, $id)) {
                    $this->render('cliente/form', [
                        'error' => 'Este documento já está cadastrado para outro cliente.', 
                        'cliente' => array_merge($data, ['id' => $id])
                    ]);
                    return;
            }

            try {
                if ($this->clienteModel->update($id, $data)) {
                    $this->log("Atualizou dados do cliente", "Cliente #{$id} - {$data['nome_completo']}");
                    $this->redirect('clientes/view?id=' . $id);
                } else {
                    $this->render('cliente/form', ['error' => 'Erro ao atualizar cliente.', 'cliente' => array_merge($data, ['id' => $id])]);
                }
            } catch (\PDOException $e) {
                if ($e->getCode() == 23000) {
                    $this->render('cliente/form', ['error' => 'Este documento já está cadastrado para outro cliente.', 'cliente' => array_merge($data, ['id' => $id])]);
                } else {
                    $this->render('cliente/form', ['error' => 'Erro no banco de dados: ' . $e->getMessage(), 'cliente' => array_merge($data, ['id' => $id])]);
                }
            }
        }
    }

    // normalização movida para ClienteService

    public function destroy()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if ($id) {
                $cliente = $this->clienteModel->find($id);
                $nome = $cliente['nome_completo'] ?? 'N/A';
                if ($this->clienteModel->delete($id)) {
                    $this->log("Excluiu cliente", "Cliente #{$id} - {$nome}");
                }
            }
        }
        $this->redirect('clientes');
    }

    public function verificarDocumento()
    {
        $documento = filter_input(INPUT_GET, 'documento', FILTER_SANITIZE_SPECIAL_CHARS);
        $documento = preg_replace('/\D/', '', $documento); // Remove máscara

        if (empty($documento)) {
            header('Content-Type: application/json');
            echo json_encode(['exists' => false]);
            return;
        }

        $sql = "SELECT id, nome_completo FROM clientes WHERE REPLACE(REPLACE(REPLACE(documento, '.', ''), '-', ''), '/', '') = :documento AND ativo = 1 LIMIT 1";
        $stmt = $this->clienteModel->getConnection()->prepare($sql);
        $stmt->execute(['documento' => $documento]);
        $cliente = $stmt->fetch(\PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        if ($cliente) {
            echo json_encode([
                'exists' => true,
                'id' => $cliente['id'],
                'nome' => $cliente['nome_completo']
            ]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }
}

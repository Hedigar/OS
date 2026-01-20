<?php

namespace App\Controllers;

use App\Models\Cliente;
use App\Models\OrdemServico;
use App\Models\Equipamento;
use App\Models\AtendimentoExterno;

class ClienteController extends BaseController
{
    private $clienteModel;

    public function __construct()
    {
        parent::__construct();
        $this->clienteModel = new Cliente();
    }

    public function index()
    {
        $porPagina = 10;
        $paginaAtual = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT) ?: 1;
        $offset = ($paginaAtual - 1) * $porPagina;
        $termo = filter_input(INPUT_GET, 'busca', FILTER_SANITIZE_SPECIAL_CHARS);

        $whereClause = '';
        $params = [];

        if ($termo) {
            $whereClause = "nome_completo LIKE :term_nome OR documento LIKE :term_documento";
            $params['term_nome'] = "%{$termo}%";
            $params['term_documento'] = "%{$termo}%";
        }

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
            $data = $this->getPostData();
            
            // Verificar se o documento já existe antes de tentar criar
            if (!empty($data['documento'])) {
                $docLimpo = preg_replace('/\D/', '', $data['documento']);
                $sql = "SELECT id FROM clientes WHERE REPLACE(REPLACE(REPLACE(documento, '.', ''), '-', ''), '/', '') = :documento AND ativo = 1 LIMIT 1";
                $stmt = $this->clienteModel->getConnection()->prepare($sql);
                $stmt->execute(['documento' => $docLimpo]);
                $existente = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($existente) {
                    $this->redirect('clientes/view?id=' . $existente['id']);
                    return;
                }
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
                    // Tentar buscar o ID novamente caso tenha ocorrido uma race condition
                    $docLimpo = preg_replace('/\D/', '', $data['documento']);
                    $sql = "SELECT id FROM clientes WHERE REPLACE(REPLACE(REPLACE(documento, '.', ''), '-', ''), '/', '') = :documento AND ativo = 1 LIMIT 1";
                    $stmt = $this->clienteModel->getConnection()->prepare($sql);
                    $stmt->execute(['documento' => $docLimpo]);
                    $existente = $stmt->fetch(\PDO::FETCH_ASSOC);
                    
                    if ($existente) {
                        $this->redirect('clientes/view?id=' . $existente['id']);
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

        $cliente = $this->clienteModel->find($id);
        if (!$cliente) $this->redirect('clientes');

        $osModel = new OrdemServico();
        $equipamentoModel = new Equipamento();
        $atendimentoExternoModel = new AtendimentoExterno();

        $historicoOS = $osModel->findByClienteId($id);
        $equipamentos = $equipamentoModel->findByClienteId($id);
        $historicoExterno = $atendimentoExternoModel->findByClienteId($id);

        $this->render('cliente/view', [
            'title' => 'Visualizar Cliente',
            'cliente' => $cliente,
            'historicoOS' => $historicoOS,
            'equipamentos' => $equipamentos,
            'historicoExterno' => $historicoExterno
        ]);
    }

public function printDebitos()
{
    $clienteId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$clienteId) $this->redirect('clientes');

    $cliente = $this->clienteModel->find($clienteId);
    if (!$cliente) $this->redirect('clientes');

    $db = (new OrdemServico())->getConnection();

    // SQL CORRIGIDO: Removido 'data_saida' que não existe no seu banco
    $sqlOS = "SELECT os.id, os.created_at, os.valor_total_os, os.status_pagamento, 
                     os.defeito_relatado, os.laudo_tecnico,
                     s.nome as status_nome, s.cor as status_cor,
                     e.modelo as equipamento_modelo,
                     (SELECT SUM(desconto) FROM itens_ordem_servico WHERE ordem_servico_id = os.id AND ativo = 1) as valor_desconto
              FROM ordens_servico os
              JOIN status_os s ON os.status_atual_id = s.id
              LEFT JOIN equipamentos e ON os.equipamento_id = e.id
              WHERE os.cliente_id = :cid AND os.ativo = 1 AND (os.status_pagamento IS NULL OR os.status_pagamento != 'pago')
              ORDER BY os.id DESC";
    
    $stmtOS = $db->prepare($sqlOS);
    $stmtOS->execute(['cid' => $clienteId]);
    $debitosOS = $stmtOS->fetchAll(\PDO::FETCH_ASSOC) ?: [];

    // Busca Atendimentos Externos
    $sqlAE = "SELECT ae.id, ae.data_agendada, ae.pagamento, ae.valor_deslocamento, ae.descricao_problema, ae.detalhes_servico
              FROM atendimentos_externos ae
              WHERE ae.cliente_id = :cid AND (ae.pagamento IS NULL OR ae.pagamento != 'pago') AND ae.ativo = 1
              ORDER BY ae.id DESC";
    $stmtAE = $db->prepare($sqlAE);
    $stmtAE->execute(['cid' => $clienteId]);
    $debitosAE = $stmtAE->fetchAll(\PDO::FETCH_ASSOC) ?: [];

    $service = new \App\Services\AtendimentoService();
    $aeDetalhados = [];
    foreach ($debitosAE as $row) {
        $det = $service->obterDetalhesVisualizacao((int)$row['id']);
        $aeDetalhados[] = array_merge($row, [
            'valor_total' => $det['valor_total'] ?? (float)($row['valor_deslocamento'] ?? 0),
            'valor_desconto' => $det['valor_desconto'] ?? 0 
        ]);
    }

    $this->renderPDF('cliente/print_debitos', [
        'cliente' => $cliente,
        'debitosOS' => $debitosOS,
        'debitosAE' => $aeDetalhados
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

            $data = $this->getPostData();
            
            // Verificar se o documento já existe em OUTRO cliente
            if (!empty($data['documento'])) {
                $docLimpo = preg_replace('/\D/', '', $data['documento']);
                $sql = "SELECT id FROM clientes WHERE REPLACE(REPLACE(REPLACE(documento, '.', ''), '-', ''), '/', '') = :documento AND ativo = 1 AND id != :id LIMIT 1";
                $stmt = $this->clienteModel->getConnection()->prepare($sql);
                $stmt->execute(['documento' => $docLimpo, 'id' => $id]);
                $existente = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($existente) {
                    $this->render('cliente/form', [
                        'error' => 'Este documento já está cadastrado para outro cliente.', 
                        'cliente' => array_merge($data, ['id' => $id])
                    ]);
                    return;
                }
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

    private function getPostData()
    {
        return [
            'nome_completo' => filter_input(INPUT_POST, 'nome_completo', FILTER_SANITIZE_SPECIAL_CHARS),
            'telefone_principal' => filter_input(INPUT_POST, 'telefone_principal', FILTER_SANITIZE_SPECIAL_CHARS),
            'telefone_secundario' => filter_input(INPUT_POST, 'telefone_secundario', FILTER_SANITIZE_SPECIAL_CHARS),
            'email' => filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL),
            'documento' => filter_input(INPUT_POST, 'documento', FILTER_SANITIZE_SPECIAL_CHARS),
            'tipo_pessoa' => filter_input(INPUT_POST, 'tipo_pessoa', FILTER_SANITIZE_SPECIAL_CHARS),
            'data_nascimento' => filter_input(INPUT_POST, 'data_nascimento', FILTER_SANITIZE_SPECIAL_CHARS) ?: null,
            'endereco_logradouro' => filter_input(INPUT_POST, 'endereco_logradouro', FILTER_SANITIZE_SPECIAL_CHARS),
            'endereco_numero' => filter_input(INPUT_POST, 'endereco_numero', FILTER_SANITIZE_SPECIAL_CHARS),
            'endereco_bairro' => filter_input(INPUT_POST, 'endereco_bairro', FILTER_SANITIZE_SPECIAL_CHARS),
            'endereco_cidade' => filter_input(INPUT_POST, 'endereco_cidade', FILTER_SANITIZE_SPECIAL_CHARS),
            'observacoes' => filter_input(INPUT_POST, 'observacoes', FILTER_SANITIZE_SPECIAL_CHARS),
        ];
    }

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

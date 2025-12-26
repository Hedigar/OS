<?php

namespace App\Controllers;

use App\Models\Cliente;

class ClienteController extends BaseController
{
    private $clienteModel;

    public function __construct()
    {
        parent::__construct();
        $this->clienteModel = new Cliente();
    }

    // Listar clientes com paginacao e busca
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

    // Mostrar formulario de criacao
    public function create()
    {
        $this->render('cliente/form', [
            'title' => 'Novo Cliente',
            'cliente' => null
        ]);
    }

    // Salvar novo cliente
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [
                'nome_completo' => filter_input(INPUT_POST, 'nome_completo', FILTER_SANITIZE_SPECIAL_CHARS),

                'telefone_principal' => filter_input(INPUT_POST, 'telefone_principal', FILTER_SANITIZE_SPECIAL_CHARS),
                'telefone_secundario' => filter_input(INPUT_POST, 'telefone_secundario', FILTER_SANITIZE_SPECIAL_CHARS),

                'email' => filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL),
                'documento' => filter_input(INPUT_POST, 'documento', FILTER_SANITIZE_SPECIAL_CHARS),
                'tipo_pessoa' => filter_input(INPUT_POST, 'tipo_pessoa', FILTER_SANITIZE_SPECIAL_CHARS),

                'data_nascimento' => ($data_nascimento = filter_input(INPUT_POST, 'data_nascimento', FILTER_SANITIZE_SPECIAL_CHARS)) ? $data_nascimento : null,

                'endereco_logradouro' => filter_input(INPUT_POST, 'endereco_logradouro', FILTER_SANITIZE_SPECIAL_CHARS),
                'endereco_numero' => filter_input(INPUT_POST, 'endereco_numero', FILTER_SANITIZE_SPECIAL_CHARS),
                'endereco_bairro' => filter_input(INPUT_POST, 'endereco_bairro', FILTER_SANITIZE_SPECIAL_CHARS),
                'endereco_cidade' => filter_input(INPUT_POST, 'endereco_cidade', FILTER_SANITIZE_SPECIAL_CHARS),

                'observacoes' => filter_input(INPUT_POST, 'observacoes', FILTER_SANITIZE_SPECIAL_CHARS),
            ];

            if ($this->clienteModel->create($data)) {
                $this->redirect('clientes');
            } else {
                $this->render('cliente/form', [
                    'error' => 'Erro ao salvar cliente.',
                    'cliente' => $data
                ]);
            }
        }
    }

    // Visualizar cliente
    public function showView()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect('clientes');
        }

        $cliente = $this->clienteModel->find($id);
        if (!$cliente) {
            $this->redirect('clientes');
        }

        // Instanciar modelos necessários
        $osModel = new \App\Models\OrdemServico();
        $equipamentoModel = new \App\Models\EquipamentoOS();

        // Buscar histórico de OSs do cliente
        $historicoOS = $osModel->findByClienteId($id);

        // Buscar equipamentos associados a todas as OSs do cliente (para simplificar, vamos buscar os equipamentos das OSs)
        // Uma busca mais otimizada seria necessária, mas para o escopo, vamos usar o que temos.
        // O modelo EquipamentoOS não tem um método findByClienteId, então vamos buscar as OSs e depois os equipamentos.
        $equipamentos = [];
        foreach ($historicoOS as $os) {
            $equipamentos = array_merge($equipamentos, $equipamentoModel->findByOsId($os['id']));
        }
        // Remove duplicatas se houver, mas como o EquipamentoOS está ligado à OS, não deve haver.

        $this->render('cliente/view', [
            'title' => 'Visualizar Cliente',
            'cliente' => $cliente,
            'historicoOS' => $historicoOS,
            'equipamentos' => $equipamentos // Equipamentos associados às OSs
        ]);
    }

    // Mostrar formulario de edicao
    public function edit()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect('clientes');
        }

        $cliente = $this->clienteModel->find($id);
        if (!$cliente) {
            $this->redirect('clientes');
        }

        $this->render('cliente/form', [
            'title' => 'Editar Cliente',
            'cliente' => $cliente
        ]);
    }

    // Atualizar cliente
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                $this->redirect('clientes');
            }

            $data = [
                'nome_completo' => filter_input(INPUT_POST, 'nome_completo', FILTER_SANITIZE_SPECIAL_CHARS),

                'telefone_principal' => filter_input(INPUT_POST, 'telefone_principal', FILTER_SANITIZE_SPECIAL_CHARS),
                'telefone_secundario' => filter_input(INPUT_POST, 'telefone_secundario', FILTER_SANITIZE_SPECIAL_CHARS),

                'email' => filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL),
                'documento' => filter_input(INPUT_POST, 'documento', FILTER_SANITIZE_SPECIAL_CHARS),
                'tipo_pessoa' => filter_input(INPUT_POST, 'tipo_pessoa', FILTER_SANITIZE_SPECIAL_CHARS),

                'data_nascimento' => ($data_nascimento = filter_input(INPUT_POST, 'data_nascimento', FILTER_SANITIZE_SPECIAL_CHARS)) ? $data_nascimento : null,

                'endereco_logradouro' => filter_input(INPUT_POST, 'endereco_logradouro', FILTER_SANITIZE_SPECIAL_CHARS),
                'endereco_numero' => filter_input(INPUT_POST, 'endereco_numero', FILTER_SANITIZE_SPECIAL_CHARS),
                'endereco_bairro' => filter_input(INPUT_POST, 'endereco_bairro', FILTER_SANITIZE_SPECIAL_CHARS),
                'endereco_cidade' => filter_input(INPUT_POST, 'endereco_cidade', FILTER_SANITIZE_SPECIAL_CHARS),

                'observacoes' => filter_input(INPUT_POST, 'observacoes', FILTER_SANITIZE_SPECIAL_CHARS),
            ];

            if ($this->clienteModel->update($id, $data)) {
                $this->redirect('clientes');
            } else {
                $cliente = $this->clienteModel->find($id);
                $this->render('cliente/form', [
                    'error' => 'Erro ao atualizar cliente.',
                    'cliente' => $cliente
                ]);
            }
        }
    }

    // Busca de cliente para Autocomplete (AJAX)
    public function searchAjax()
    {
        $termo = filter_input(INPUT_GET, 'term', FILTER_SANITIZE_SPECIAL_CHARS);
        $clientes = [];

        if ($termo) {
            // Usando a lógica de busca existente no index, mas sem paginação
            $whereClause = "nome_completo LIKE :term_nome OR documento LIKE :term_documento";
            $params = [
                'term_nome' => "%{$termo}%",
                'term_documento' => "%{$termo}%"
            ];
            
            // Usando o método all() do modelo com a cláusula WHERE
            $sql = "SELECT id, nome_completo, documento, telefone_principal FROM {$this->clienteModel->getTable()} WHERE {$whereClause} LIMIT 10";
            $stmt = $this->clienteModel->getConnection()->prepare($sql);
            $stmt->execute($params);
            $clientes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        header('Content-Type: application/json');
        echo json_encode($clientes);
        exit;
    }

    // Busca de cliente por ID (AJAX) - Para completar dados na OS
    public function getClientDetails()
    {
        $this->requireAjax();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->jsonResponse(['error' => 'ID de cliente inválido'], 400);
            return;
        }

        $cliente = $this->clienteModel->find($id);

        if (!$cliente) {
            $this->jsonResponse(['error' => 'Cliente não encontrado'], 404);
            return;
        }

        $this->jsonResponse($cliente);
    }

    // Deletar cliente
    public function destroy()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if ($id) {
                $this->clienteModel->delete($id);
            }
        }

        $this->redirect('clientes');
    }
}

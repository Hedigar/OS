<?php

namespace App\Controllers;

use App\Models\Despesa;
use App\Models\DespesaCategoria;
use App\Core\Auth;

class DespesasController extends BaseController
{
    private Despesa $despesaModel;
    private DespesaCategoria $categoriaModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
        $this->despesaModel = new Despesa();
        $this->categoriaModel = new DespesaCategoria();
    }

    public function index()
    {
        $porPagina = 10;
        $paginaAtual = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT) ?: 1;
        $offset = ($paginaAtual - 1) * $porPagina;
        $termo = filter_input(INPUT_GET, 'busca', FILTER_SANITIZE_SPECIAL_CHARS) ?: '';
        $status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS) ?: '';
        $metodo = filter_input(INPUT_GET, 'metodo', FILTER_SANITIZE_SPECIAL_CHARS) ?: '';
        $categoriaId = filter_input(INPUT_GET, 'categoria_id', FILTER_VALIDATE_INT) ?: null;

        $where = [];
        $params = [];

        if ($termo !== '') {
            $where[] = "(d.descricao LIKE :termo OR c.nome LIKE :termo_cat)";
            $params[':termo'] = "%{$termo}%";
            $params[':termo_cat'] = "%{$termo}%";
        }
        if (!empty($status)) {
            $where[] = "d.status_pagamento = :status";
            $params[':status'] = $status;
        }
        if (!empty($metodo)) {
            $where[] = "d.metodo_pagamento = :metodo";
            $params[':metodo'] = $metodo;
        }
        if (!empty($categoriaId)) {
            $where[] = "d.categoria_id = :categoria_id";
            $params[':categoria_id'] = $categoriaId;
        }

        $totalRegistros = $this->despesaModel->countAllJoin($where, $params);
        $despesas = $this->despesaModel->getPaginatedJoin($porPagina, $offset, $where, $params);
        $totalPaginas = max(1, (int)ceil($totalRegistros / $porPagina));
        $categorias = $this->categoriaModel->getAll();

        $this->render('despesas/index', [
            'title' => 'Despesas',
            'current_page' => 'despesas',
            'despesas' => $despesas,
            'categorias' => $categorias,
            'busca' => $termo,
            'status' => $status,
            'metodo' => $metodo,
            'categoriaId' => $categoriaId,
            'paginaAtual' => $paginaAtual,
            'totalPaginas' => $totalPaginas
        ]);
    }

    public function form()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $despesa = null;
        if ($id) {
            $despesa = $this->despesaModel->findWithDetails($id);
            if (!$despesa) {
                $this->redirect('despesas');
            }
        }
        $categorias = $this->categoriaModel->getAll();
        $this->render('despesas/form', [
            'title' => $id ? 'Editar Despesa' : 'Nova Despesa',
            'current_page' => 'despesas',
            'despesa' => $despesa,
            'categorias' => $categorias
        ]);
    }

    public function store()
    {
        $data = [
            'categoria_id' => filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT) ?: null,
            'usuario_id' => Auth::id(),
            'descricao' => trim((string)($_POST['descricao'] ?? '')),
            'valor' => (float)str_replace(',', '.', (string)($_POST['valor'] ?? '0')),
            'data_despesa' => $_POST['data_despesa'] ?? date('Y-m-d'),
            'status_pagamento' => $_POST['status_pagamento'] ?? 'pendente',
            'metodo_pagamento' => $_POST['metodo_pagamento'] ?? 'outro',
            'observacoes' => $_POST['observacoes'] ?? null,
            'ativo' => 1
        ];

        if ($data['descricao'] === '' || $data['valor'] <= 0) {
            $categorias = $this->categoriaModel->getAll();
            $this->render('despesas/form', [
                'title' => 'Nova Despesa',
                'current_page' => 'despesas',
                'error' => 'Preencha os campos obrigatórios',
                'despesa' => $data,
                'categorias' => $categorias
            ]);
            return;
        }

        $id = $this->despesaModel->create($data);
        if ($id) {
            $this->log('Criou despesa', 'Despesa #' . $id);
            $this->redirect('despesas');
        }

        $categorias = $this->categoriaModel->getAll();
        $this->render('despesas/form', [
            'title' => 'Nova Despesa',
            'current_page' => 'despesas',
            'error' => 'Erro ao salvar',
            'despesa' => $data,
            'categorias' => $categorias
        ]);
    }

    public function update()
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect('despesas');
        }

        $data = [
            'categoria_id' => filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT) ?: null,
            'descricao' => trim((string)($_POST['descricao'] ?? '')),
            'valor' => (float)str_replace(',', '.', (string)($_POST['valor'] ?? '0')),
            'data_despesa' => $_POST['data_despesa'] ?? date('Y-m-d'),
            'status_pagamento' => $_POST['status_pagamento'] ?? 'pendente',
            'metodo_pagamento' => $_POST['metodo_pagamento'] ?? 'outro',
            'observacoes' => $_POST['observacoes'] ?? null
        ];

        if ($data['descricao'] === '' || $data['valor'] <= 0) {
            $categorias = $this->categoriaModel->getAll();
            $despesa = $this->despesaModel->findWithDetails($id);
            $this->render('despesas/form', [
                'title' => 'Editar Despesa',
                'current_page' => 'despesas',
                'error' => 'Preencha os campos obrigatórios',
                'despesa' => array_merge($despesa ?: [], $data, ['id' => $id]),
                'categorias' => $categorias
            ]);
            return;
        }

        if ($this->despesaModel->update($id, $data)) {
            $this->log('Atualizou despesa', 'Despesa #' . $id);
            $this->redirect('despesas');
        }

        $categorias = $this->categoriaModel->getAll();
        $despesa = $this->despesaModel->findWithDetails($id);
        $this->render('despesas/form', [
            'title' => 'Editar Despesa',
            'current_page' => 'despesas',
            'error' => 'Erro ao atualizar',
            'despesa' => array_merge($despesa ?: [], $data, ['id' => $id]),
            'categorias' => $categorias
        ]);
    }

    public function destroy()
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect('despesas');
        }
        if ($this->despesaModel->delete($id)) {
            $this->log('Removeu despesa', 'Despesa #' . $id);
        }
        $this->redirect('despesas');
    }
}

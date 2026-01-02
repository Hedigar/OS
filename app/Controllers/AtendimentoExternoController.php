<?php

namespace App\Controllers;

use App\Models\AtendimentoExterno;
use App\Models\Cliente;
use App\Models\Usuario;

class AtendimentoExternoController extends BaseController
{
    private $atendimentoModel;

    public function __construct()
    {
        parent::__construct();
        $this->atendimentoModel = new AtendimentoExterno();
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
            $whereClause = "c.nome_completo LIKE :term_nome OR ae.descricao_problema LIKE :term_desc";
            $params['term_nome'] = "%{$termo}%";
            $params['term_desc'] = "%{$termo}%";
        }

        $totalAtendimentos = $this->atendimentoModel->countAll($whereClause, $params);
        $atendimentos = $this->atendimentoModel->getPaginated($porPagina, $offset, $whereClause, $params);
        $totalPaginas = ceil($totalAtendimentos / $porPagina);

        $this->render('atendimento_externo/index', [
            'title' => 'Atendimentos Externos',
            'atendimentos' => $atendimentos,
            'busca' => $termo,
            'paginaAtual' => $paginaAtual,
            'totalPaginas' => $totalPaginas
        ]);
    }

    public function form()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $clienteId = filter_input(INPUT_GET, 'cliente_id', FILTER_VALIDATE_INT);
        
        $atendimento = null;
        $cliente = null;

        if ($id) {
            $atendimento = $this->atendimentoModel->findWithDetails($id);
        }

        if ($clienteId) {
            $clienteModel = new Cliente();
            $cliente = $clienteModel->find($clienteId);
        }

        $usuarioModel = new Usuario();
        $tecnicos = $usuarioModel->all(); // Assumindo que all() retorna técnicos ativos

        $this->render('atendimento_externo/form', [
            'title' => $id ? 'Editar Atendimento' : 'Novo Atendimento Externo',
            'atendimento' => $atendimento,
            'cliente' => $cliente,
            'tecnicos' => $tecnicos
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->getPostData();
            if ($id = $this->atendimentoModel->create($data)) {
                $this->log("Criou novo atendimento externo", "Atendimento #{$id}");
                $this->redirect('atendimentos-externos');
            } else {
                $this->redirect('atendimentos-externos/form?error=1');
            }
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) $this->redirect('atendimentos-externos');

            $data = $this->getPostData();
            if ($this->atendimentoModel->update($id, $data)) {
                $this->log("Atualizou atendimento externo", "Atendimento #{$id}");
                $this->redirect('atendimentos-externos');
            } else {
                $this->redirect('atendimentos-externos/form?id=' . $id . '&error=1');
            }
        }
    }

    public function destroy()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if ($id) {
                if ($this->atendimentoModel->delete($id)) {
                    $this->log("Excluiu atendimento externo", "Atendimento #{$id}");
                }
            }
        }
        $this->redirect('atendimentos-externos');
    }

    private function getPostData()
    {
        return [
            'cliente_id' => filter_input(INPUT_POST, 'cliente_id', FILTER_VALIDATE_INT),
            'usuario_id' => filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT) ?: null,
            'descricao_problema' => filter_input(INPUT_POST, 'descricao_problema', FILTER_SANITIZE_SPECIAL_CHARS),
            'endereco_visita' => filter_input(INPUT_POST, 'endereco_visita', FILTER_SANITIZE_SPECIAL_CHARS),
            'data_agendada' => filter_input(INPUT_POST, 'data_agendada', FILTER_SANITIZE_SPECIAL_CHARS) ?: null,
            'status' => filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS),
            'valor_deslocamento' => filter_input(INPUT_POST, 'valor_deslocamento', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?: 0,
            'observacoes_tecnicas' => filter_input(INPUT_POST, 'observacoes_tecnicas', FILTER_SANITIZE_SPECIAL_CHARS),
        ];
    }
}

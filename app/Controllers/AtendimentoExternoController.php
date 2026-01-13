<?php

namespace App\Controllers;

use App\Models\AtendimentoExterno;
use App\Models\Cliente;
use App\Models\Usuario;
use App\Services\AtendimentoService;

class AtendimentoExternoController extends BaseController
{
    private $atendimentoModel;
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->atendimentoModel = new AtendimentoExterno();
        $this->service = new AtendimentoService();
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
        
        $this->render('atendimento_externo/index', [
            'title' => 'Atendimentos Externos',
            'atendimentos' => $atendimentos,
            'busca' => $termo,
            'paginaAtual' => $paginaAtual,
            'totalPaginas' => ceil($totalAtendimentos / $porPagina)
        ]);
    }

    public function visualizar()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) $this->redirect('atendimentos-externos');

        $dados = $this->service->obterDetalhesVisualizacao($id);
        if (!$dados) $this->redirect('atendimentos-externos');

        $statusModel = new \App\Models\StatusOS();

        $this->render('atendimento_externo/view', [
            'title'       => 'Atendimento Externo - Execução',
            'atendimento' => $dados['atendimento'],
            'itens'       => $dados['itens'],
            'ordem'       => ['valor_total_os' => $dados['valor_total']],
            'statuses'    => $statusModel->getAll()
        ]);
    }

    public function print()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) $this->redirect('atendimentos-externos');

        $dados = $this->service->obterDetalhesVisualizacao($id);
        if (!$dados) $this->redirect('atendimentos-externos');

        $this->renderPDF('atendimento_externo/print', [
            'atendimento' => $dados['atendimento']
        ], "Atendimento_Externo_{$id}.pdf");
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->getPostData();
            if ($id = $this->atendimentoModel->create($data)) {
                $this->log("Criou novo atendimento", "Atendimento #{$id}");
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
            if ($this->service->atualizarAtendimento($id, $data)) {
                $this->log("Atualizou atendimento", "Atendimento #{$id}");
                $this->redirect('atendimentos-externos/view?id=' . $id);
            } else {
                $this->redirect('atendimentos-externos/form?id=' . $id . '&error=1');
            }
        }
    }

    public function saveItem()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $atendimentoId = filter_input(INPUT_POST, 'atendimento_externo_id', FILTER_VALIDATE_INT);
            if ($this->service->salvarItem($_POST)) {
                $this->redirect('atendimentos-externos/view?id=' . $atendimentoId);
            }
        }
    }

    public function updateItem()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemId = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
            $atendimentoId = filter_input(INPUT_POST, 'atendimento_externo_id', FILTER_VALIDATE_INT);

            if ($this->service->atualizarItem($itemId, $_POST)) {
                $this->redirect('atendimentos-externos/view?id=' . $atendimentoId);
            }
        }
    }

    public function removeItem()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemId = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
            $atendimentoId = filter_input(INPUT_POST, 'atendimento_externo_id', FILTER_VALIDATE_INT);

            $itemModel = new \App\Models\ItemOS();
            if ($itemModel->delete($itemId)) {
                $this->redirect('atendimentos-externos/view?id=' . $atendimentoId);
            }
        }
    }

    public function destroy()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if ($id && $this->atendimentoModel->delete($id)) {
                $this->log("Excluiu atendimento", "Atendimento #{$id}");
            }
        }
        $this->redirect('atendimentos-externos');
    }

    public function form()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $clienteId = filter_input(INPUT_GET, 'cliente_id', FILTER_VALIDATE_INT);
        
        $atendimento = $id ? $this->atendimentoModel->findWithDetails($id) : null;
        $cliente = $clienteId ? (new Cliente())->find($clienteId) : null;
        $tecnicos = (new Usuario())->all();

        $this->render('atendimento_externo/form', [
            'title' => $id ? 'Editar Atendimento' : 'Novo Atendimento Externo',
            'atendimento' => $atendimento,
            'cliente' => $cliente,
            'tecnicos' => $tecnicos
        ]);
    }

    private function getPostData()
    {
        return [
            'cliente_id'           => filter_input(INPUT_POST, 'cliente_id', FILTER_VALIDATE_INT),
            'usuario_id'           => filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT) ?: null,
            'equipamentos'         => filter_input(INPUT_POST, 'equipamentos', FILTER_SANITIZE_SPECIAL_CHARS),
            'descricao_problema'   => filter_input(INPUT_POST, 'descricao_problema', FILTER_SANITIZE_SPECIAL_CHARS),
            'detalhes_servico'     => filter_input(INPUT_POST, 'detalhes_servico', FILTER_SANITIZE_SPECIAL_CHARS),
            'endereco_visita'      => filter_input(INPUT_POST, 'endereco_visita', FILTER_SANITIZE_SPECIAL_CHARS),
            'data_agendada'        => filter_input(INPUT_POST, 'data_agendada', FILTER_SANITIZE_SPECIAL_CHARS) ?: null,
            'hora_entrada'         => filter_input(INPUT_POST, 'hora_entrada') ?: null,
            'hora_saida'           => filter_input(INPUT_POST, 'hora_saida') ?: null,
            'tempo_total'          => filter_input(INPUT_POST, 'tempo_total'),
            'status'               => filter_input(INPUT_POST, 'status') ?: 'pendente',
            'pagamento'            => filter_input(INPUT_POST, 'pagamento') ?: 'não',
            'valor_deslocamento'   => filter_input(INPUT_POST, 'valor_deslocamento', FILTER_VALIDATE_FLOAT) ?: 0,
            'observacoes_tecnicas' => filter_input(INPUT_POST, 'observacoes_tecnicas', FILTER_SANITIZE_SPECIAL_CHARS)
        ];
    }

    public function searchItems()
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        $termo = filter_input(INPUT_GET, 'termo', FILTER_SANITIZE_SPECIAL_CHARS);
        if (empty($termo)) { echo json_encode([]); exit; }
        try {
            $produtoModel = new \App\Models\ProdutoServico();
            $sql = "SELECT * FROM produtos_servicos WHERE nome LIKE :termo AND ativo = 1 LIMIT 10";
            $stmt = $produtoModel->getConnection()->prepare($sql);
            $stmt->execute(['termo' => "%{$termo}%"]);
            echo json_encode($stmt->fetchAll(\PDO::FETCH_ASSOC));
            exit;
        } catch (\Throwable $e) { echo json_encode([]); exit; }
    }
}
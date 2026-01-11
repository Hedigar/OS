<?php

namespace App\Controllers;

use App\Models\AtendimentoExterno;
use App\Models\Cliente;
use App\Models\Usuario;
use App\Models\ItemOS;

class AtendimentoExternoController extends BaseController
{
    private $atendimentoModel;
    private $itemModel;

    public function __construct()
    {
        parent::__construct();
        $this->atendimentoModel = new AtendimentoExterno();
        $this->itemModel = new \App\Models\ItemOS();    
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
        $tecnicos = $usuarioModel->all();

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
        $atual = $this->atendimentoModel->find($id);

        // Se o campo estiver vazio no POST, mantém o que já está no banco
        if (empty($data['endereco_visita'])) {
            $data['endereco_visita'] = $atual['endereco_visita'];
        }
        
        if (empty($data['descricao_problema'])) {
             $data['descricao_problema'] = $atual['descricao_problema'];
        }

        if ($this->atendimentoModel->update($id, $data)) {
            $this->log("Atualizou atendimento externo", "Atendimento #{$id}");
            // Redireciona de volta para a visualização correta
            $this->redirect('atendimentos-externos/view?id=' . $id);
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

    public function print()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) $this->redirect('atendimentos-externos');

        $atendimento = $this->atendimentoModel->findWithDetails($id);
        if (!$atendimento) $this->redirect('atendimentos-externos');

        $this->renderPDF('atendimento_externo/print', [
            'atendimento' => $atendimento
        ], "Atendimento_Externo_{$id}.pdf");
    }

   public function visualizar()
{
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        $this->redirect('atendimentos-externos');
    }

    // Busca os dados do atendimento
    $atendimento = $this->atendimentoModel->findWithDetails($id);
    if (!$atendimento) {
        $this->redirect('atendimentos-externos');
    }

    // Busca os itens vinculados a este atendimento
    $itens = $this->atendimentoModel->listarItens($id);

    // --- CÁLCULO DO TOTAL (Sincronizado com a View) ---
    $somaItens = 0;
    if (!empty($itens)) {
        foreach ($itens as $item) {
            $somaItens += ($item['quantidade'] * $item['valor_unitario']);
        }
    }
    
    $valorDeslocamento = (float)($atendimento['valor_deslocamento'] ?? 0);
    
    // Criamos o objeto $ordem que a view usa para exibir o total no rodapé
    $ordem = [
        'valor_total_os' => $somaItens + $valorDeslocamento
    ];

    // Busca a lista de status para o campo select
    $statusModel = new \App\Models\StatusOS();
    $statuses = $statusModel->getAll();

    $this->render('atendimento_externo/view', [
        'title'       => 'Atendimento Externo - Execução',
        'atendimento' => $atendimento,
        'itens'       => $itens,
        'ordem'       => $ordem,    // Variável que estava faltando
        'statuses'    => $statuses 
    ]);
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
        $atendimentoId = filter_input(INPUT_POST, 'atendimento_externo_id', FILTER_VALIDATE_INT);
        
        $venda = (float)filter_input(INPUT_POST, 'valor_unitario', FILTER_VALIDATE_FLOAT);
        $maoDeObra = (float)filter_input(INPUT_POST, 'valor_mao_de_obra', FILTER_VALIDATE_FLOAT);
        $quantidade = (float)filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_FLOAT) ?: 1;

        $itemData = [
            'ordem_servico_id'      => null,
            'atendimento_externo_id' => $atendimentoId,
            'tipo_item'             => filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS),
            'descricao'             => filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS),
            'quantidade'            => $quantidade,
            'custo'                 => filter_input(INPUT_POST, 'valor_custo', FILTER_VALIDATE_FLOAT) ?: 0,
            'valor_unitario'        => $venda,
            'valor_mao_de_obra'     => $maoDeObra,
            'valor_total'           => ($venda + $maoDeObra) * $quantidade,
            'ativo'                 => 1
        ];

        if ($this->itemModel->create($itemData)) {
            $this->redirect('atendimentos-externos/view?id=' . $atendimentoId);
        }
    }
}

public function updateItem()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $itemId = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
        $atendimentoId = filter_input(INPUT_POST, 'atendimento_externo_id', FILTER_VALIDATE_INT);

        $venda = (float)filter_input(INPUT_POST, 'valor_unitario', FILTER_VALIDATE_FLOAT);
        $maoDeObra = (float)filter_input(INPUT_POST, 'valor_mao_de_obra', FILTER_VALIDATE_FLOAT);
        $quantidade = (float)filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_FLOAT);

        $itemData = [
            'quantidade' => $quantidade,
            'valor_unitario' => $venda,
            'valor_mao_de_obra' => $maoDeObra,
            'valor_total' => ($venda + $maoDeObra) * $quantidade
        ];

        // Aqui usamos $this->itemModel que inicializamos no construtor
        if ($this->itemModel->update($itemId, $itemData)) {
            $this->redirect('atendimentos-externos/view?id=' . $atendimentoId);
        }
    }
}

public function removeItem()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $itemId = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
        $atendimentoId = filter_input(INPUT_POST, 'atendimento_externo_id', FILTER_VALIDATE_INT);

        if ($this->itemModel->delete($itemId)) {
            $this->redirect('atendimentos-externos/view?id=' . $atendimentoId);
        }
    }
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
}

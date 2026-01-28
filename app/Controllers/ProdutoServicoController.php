<?php

namespace App\Controllers;

use App\Models\ProdutoServico;
use App\Models\ConfiguracaoGeral;

class ProdutoServicoController extends BaseController
{
    private $produtoModel;
    private $configModel;

    public function __construct()
    {
        parent::__construct();
        $this->produtoModel = new ProdutoServico();
        $this->configModel = new ConfiguracaoGeral();
    }

    public function index()
    {
        $itens = $this->produtoModel->getAll();
        $porcentagem = $this->configModel->getValor('porcentagem_venda') ?? 0;

        $this->render('configuracoes/produtos_servicos/index', [
            'title' => 'Produtos e Serviços',
            'current_page' => 'configuracoes_produtos',
            'itens' => $itens,
            'porcentagem' => $porcentagem
        ]);
    }

    public function form()
    {
        $id = $_GET['id'] ?? null;
        $item = null;
        if ($id) {
            $item = $this->produtoModel->findById($id);
        }

        $porcentagem = $this->configModel->getValor('porcentagem_venda') ?? 0;

        $this->render('configuracoes/produtos_servicos/form', [
            'title' => $id ? 'Editar Item' : 'Novo Item',
            'current_page' => 'configuracoes_produtos',
            'item' => $item,
            'porcentagem' => $porcentagem
        ]);
    }

    public function store()
    {
        $data = [
            'nome' => strtoupper(trim($_POST['nome'])),
            'tipo' => $_POST['tipo'],
            'custo' => !empty($_POST['custo']) ? str_replace(',', '.', $_POST['custo']) : 0,
            'valor_venda' => !empty($_POST['valor_venda']) ? str_replace(',', '.', $_POST['valor_venda']) : 0,
            'mao_de_obra' => !empty($_POST['mao_de_obra']) ? str_replace(',', '.', $_POST['mao_de_obra']) : 0,
        ];

        $id = $this->produtoModel->create($data);

        if ($id) {
            $this->logModel->registrar(
                \App\Core\Auth::id(),
                'Cadastrou novo produto/serviço',
                "Item #{$id} - {$data['nome']}",
                null,
                $data
            );
            $_SESSION['success'] = "Item cadastrado com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao cadastrar item.";
        }

        header('Location: ' . BASE_URL . 'configuracoes/produtos-servicos');
    }

    public function update()
    {
        $id = $_POST['id'];
        $dados_anteriores = $this->produtoModel->findById($id);

        $data = [
            'nome' => strtoupper(trim($_POST['nome'])),
            'tipo' => $_POST['tipo'],
            'custo' => !empty($_POST['custo']) ? str_replace(',', '.', $_POST['custo']) : 0,
            'valor_venda' => !empty($_POST['valor_venda']) ? str_replace(',', '.', $_POST['valor_venda']) : 0,
            'mao_de_obra' => !empty($_POST['mao_de_obra']) ? str_replace(',', '.', $_POST['mao_de_obra']) : 0,
        ];

        if ($this->produtoModel->update($id, $data)) {
            $this->logModel->registrar(
                \App\Core\Auth::id(),
                'Atualizou produto/serviço',
                "Item #{$id} - {$data['nome']}",
                $dados_anteriores,
                $data
            );
            $_SESSION['success'] = "Item atualizado com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao atualizar item.";
        }

        header('Location: ' . BASE_URL . 'configuracoes/produtos-servicos');
    }

    public function destroy()
    {
        $id = $_POST['id'];
        $item = $this->produtoModel->findById($id);

        if ($this->produtoModel->delete($id)) {
            $this->logModel->registrar(
                $_SESSION['usuario_id'] ?? null,
                'Excluiu produto/serviço',
                "Item #{$id} - {$item['nome']}",
                $item,
                ['ativo' => 0]
            );
            $_SESSION['success'] = "Item excluído com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao excluir item.";
        }

        header('Location: ' . BASE_URL . 'configuracoes/produtos-servicos');
    }

    public function salvarConfiguracao()
    {
        $porcentagem = $_POST['porcentagem_venda'] ?? 0;
        
        if ($this->configModel->setValor('porcentagem_venda', $porcentagem, 'Percentual aplicado sobre o custo para calcular o valor de venda')) {
            $this->logModel->registrar(
                $_SESSION['usuario_id'] ?? null,
                'Alterou configuração de porcentagem de venda',
                "Nova porcentagem: {$porcentagem}%"
            );
            $_SESSION['success'] = "Configuração atualizada com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao atualizar configuração.";
        }

        header('Location: ' . BASE_URL . 'configuracoes/produtos-servicos');
    }

    public function atualizarPrecosGlobais()
    {
        $porcentagem = $this->configModel->getValor('porcentagem_venda') ?? 0;
        
        if ($this->produtoModel->atualizarPrecosEmMassa((float)$porcentagem)) {
            $this->logModel->registrar(
                \App\Core\Auth::id(),
                'Realizou atualização em massa de preços',
                "Margem aplicada: {$porcentagem}%"
            );
            $_SESSION['success'] = "Todos os preços de produtos foram atualizados com base na margem de {$porcentagem}%!";
        } else {
            $_SESSION['error'] = "Erro ao atualizar preços em massa.";
        }

        header('Location: ' . BASE_URL . 'configuracoes/produtos-servicos');
    }

    public function getPorcentagemAjax()
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        $porcentagem = $this->configModel->getValor('porcentagem_venda') ?? 0;
        echo json_encode(['valor' => $porcentagem]);
        exit;
    }
}

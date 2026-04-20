<?php

namespace App\Controllers;

use App\Models\Cliente;
use App\Models\OrdemServico;
use App\Models\ClienteInteracao;
use App\Models\ProdutoServico;
use App\Models\CRMCampanha;
use App\Models\ConfiguracaoGeral;
use App\Models\Log;
use App\Core\Auth;

class CRMController extends BaseController
{
    private $clienteModel;
    private $interacaoModel;
    private $produtoServicoModel;
    private $campanhaModel;
    private $configModel;

    public function __construct()
    {
        parent::__construct();
        $this->clienteModel = new Cliente();
        $this->interacaoModel = new ClienteInteracao();
        $this->produtoServicoModel = new ProdutoServico();
        $this->campanhaModel = new CRMCampanha();
        $this->configModel = new ConfiguracaoGeral();
    }

    public function index()
    {
        $campanhaId = filter_input(INPUT_GET, 'campanha_id', FILTER_VALIDATE_INT);
        $campanhaAtiva = null;

        if ($campanhaId) {
            $campanhaAtiva = $this->campanhaModel->findById($campanhaId);
            $filtros = $campanhaAtiva['filtros'] ?? [];
            $filtros['campanha_id'] = $campanhaId;
        } else {
            $filtros = [
                'dias_min' => filter_input(INPUT_GET, 'dias_min', FILTER_VALIDATE_INT),
                'termo_servico' => filter_input(INPUT_GET, 'termo_servico', FILTER_SANITIZE_SPECIAL_CHARS)
            ];
        }

        $clientes = $this->interacaoModel->getClientesFiltroCRM($filtros);
        $servicosExistentes = $this->produtoServicoModel->getDescricoesUsadas();
        $campanhasAbertas = $this->campanhaModel->getAtivas();
        $mensagemPadrao = $this->configModel->getValor('crm_mensagem_padrao') ?? 'Olá {nome}! Notamos que você fez um serviço conosco e gostaríamos de oferecer...';
        $posVendaMensagemPadrao = $this->configModel->getValor('pos_venda_mensagem_padrao') ?? 'Olá {nome}, tudo bem? Sobre a OS #{os_id}, gostaríamos de saber como está o equipamento e sua experiência. Seu feedback é importante.';

        $this->render('crm/index', [
            'title' => 'CRM - Gestão de Clientes',
            'current_page' => 'crm',
            'clientes' => $clientes,
            'filtros' => $filtros,
            'servicosExistentes' => $servicosExistentes,
            'campanhasAbertas' => $campanhasAbertas,
            'campanhaAtiva' => $campanhaAtiva,
            'mensagemPadrao' => $mensagemPadrao,
            'posVendaMensagemPadrao' => $posVendaMensagemPadrao
        ]);
    }

    public function salvarConfiguracao()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('crm');
        }

        $mensagem = filter_input(INPUT_POST, 'crm_mensagem_padrao', FILTER_SANITIZE_SPECIAL_CHARS);
        $posVendaMensagem = filter_input(INPUT_POST, 'pos_venda_mensagem_padrao', FILTER_SANITIZE_SPECIAL_CHARS);
        
        if ($mensagem) {
            $this->configModel->setValor('crm_mensagem_padrao', $mensagem, 'Mensagem padrão sugerida no CRM');
            $this->log("Atualizou Configuração CRM", "Nova mensagem padrão definida");
        }

        if ($posVendaMensagem) {
            $this->configModel->setValor('pos_venda_mensagem_padrao', $posVendaMensagem, 'Mensagem padrão enviada no Pós-Venda');
            $this->log("Atualizou Configuração Pós-Venda", "Nova mensagem de pós-venda definida");
        }

        $this->redirect('crm');
    }

    public function salvarCampanha()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('crm');
        }

        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
        $mensagem = filter_input(INPUT_POST, 'mensagem_padrao', FILTER_SANITIZE_SPECIAL_CHARS);
        $filtros = [
            'dias_min' => filter_input(INPUT_POST, 'dias_min', FILTER_VALIDATE_INT),
            'termo_servico' => filter_input(INPUT_POST, 'termo_servico', FILTER_SANITIZE_SPECIAL_CHARS)
        ];

        if (!$nome) {
            $this->redirect('crm');
        }

        $id = $this->campanhaModel->create([
            'nome' => $nome,
            'mensagem_padrao' => $mensagem,
            'filtros' => json_encode($filtros),
            'usuario_id' => Auth::id(),
            'status' => 'ativa'
        ]);

        $this->log("Criou Campanha CRM", "Campanha #{$id} - {$nome}");
        $this->redirect("crm?campanha_id={$id}");
    }

    public function finalizarCampanha()
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $this->campanhaModel->update($id, ['status' => 'finalizada']);
            $this->log("Finalizou Campanha CRM", "Campanha #{$id}");
        }
        $this->redirect('crm');
    }

    public function registrarInteracao()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('crm');
        }

        $clienteId = filter_input(INPUT_POST, 'cliente_id', FILTER_VALIDATE_INT);
        $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS);
        $canal = filter_input(INPUT_POST, 'canal', FILTER_SANITIZE_SPECIAL_CHARS);
        $assunto = filter_input(INPUT_POST, 'assunto', FILTER_SANITIZE_SPECIAL_CHARS);
        $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS);
        $resposta = filter_input(INPUT_POST, 'resposta_cliente', FILTER_SANITIZE_SPECIAL_CHARS);
        $nota = filter_input(INPUT_POST, 'nota_satisfacao', FILTER_VALIDATE_INT);
        $osId = filter_input(INPUT_POST, 'ordem_servico_id', FILTER_VALIDATE_INT) ?: null;
        $campanhaId = filter_input(INPUT_POST, 'campanha_id', FILTER_VALIDATE_INT) ?: null;

        if (!$clienteId || !$tipo) {
            $this->redirect('clientes');
        }

        $this->interacaoModel->create([
            'cliente_id' => $clienteId,
            'usuario_id' => Auth::id(),
            'tipo' => $tipo,
            'canal' => $canal,
            'assunto' => $assunto,
            'descricao' => $descricao,
            'resposta_cliente' => $resposta,
            'nota_satisfacao' => $nota,
            'ordem_servico_id' => $osId,
            'campanha_id' => $campanhaId
        ]);

        // Se for um pós-venda vindo de uma OS, atualiza o status na OS também
        if ($tipo === 'pos_venda' && $osId) {
            $osModel = new OrdemServico();
            $osModel->update($osId, [
                'pos_venda_status' => 1,
                'pos_venda_nota' => $nota,
                'pos_venda_data' => date('Y-m-d H:i:s')
            ]);
        }

        $log = new Log();
        $log->registrar(Auth::id(), "CRM: Interação {$tipo}", "Cliente #{$clienteId}");

        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }

        $this->redirect("clientes/view?id={$clienteId}");
    }

    public function registrarCampanhaLote()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('crm');
        }

        $clienteIds = $_POST['cliente_ids'] ?? [];
        $assunto = filter_input(INPUT_POST, 'assunto', FILTER_SANITIZE_SPECIAL_CHARS);
        $mensagem = filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($clienteIds) || !$assunto) {
            $this->redirect('crm');
        }

        foreach ($clienteIds as $id) {
            $this->interacaoModel->create([
                'cliente_id' => (int)$id,
                'usuario_id' => Auth::id(),
                'tipo' => 'campanha',
                'canal' => 'whatsapp',
                'assunto' => $assunto,
                'descricao' => $mensagem
            ]);
        }

        $log = new Log();
        $log->registrar(Auth::id(), "CRM: Campanha em Lote", count($clienteIds) . " clientes");

        $this->redirect('crm');
    }
}

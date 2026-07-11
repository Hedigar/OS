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
        $clientesContactados = [];
        $totalContactados = 0;

        if ($campanhaId) {
            $campanhaAtiva = $this->campanhaModel->findById($campanhaId);
            $filtros = $campanhaAtiva['filtros'] ?? [];
            $filtros['campanha_id'] = $campanhaId;
            $clientesContactados = $this->interacaoModel->getClientesContactadosCampanha($campanhaId, 1, 10);
            $totalContactados = $this->interacaoModel->countClientesContactadosCampanha($campanhaId);
        } else {
            $filtros = [
                'dias_min' => filter_input(INPUT_GET, 'dias_min', FILTER_VALIDATE_INT),
                'termo_servico' => filter_input(INPUT_GET, 'termo_servico', FILTER_SANITIZE_SPECIAL_CHARS)
            ];
        }

        $clientes = $this->interacaoModel->getClientesFiltroCRM($filtros, 1, 10);
        $totalSegmentados = $this->interacaoModel->countClientesFiltroCRM($filtros);
        $servicosExistentes = $this->produtoServicoModel->getDescricoesUsadas();
        $campanhasAbertas = $this->campanhaModel->getAtivas();
        $mensagemPadrao = $this->configModel->getValor('crm_mensagem_padrao') ?? 'Olá {nome}! Notamos que você fez um serviço conosco e gostaríamos de oferecer...';
        $posVendaMensagemPadrao = $this->configModel->getValor('pos_venda_mensagem_padrao') ?? 'Olá {nome}, tudo bem? Sobre a OS #{os_id}, gostaríamos de saber como está o equipamento e sua experiência. Seu feedback é importante.';

        $this->render('crm/index', [
            'title' => 'CRM - Gestão de Clientes',
            'current_page' => 'crm',
            'clientes' => $clientes,
            'totalSegmentados' => $totalSegmentados,
            'clientesContactados' => $clientesContactados,
            'totalContactados' => $totalContactados,
            'filtros' => $filtros,
            'servicosExistentes' => $servicosExistentes,
            'campanhasAbertas' => $campanhasAbertas,
            'campanhaAtiva' => $campanhaAtiva,
            'mensagemPadrao' => $mensagemPadrao,
            'posVendaMensagemPadrao' => $posVendaMensagemPadrao
        ]);
    }

    /**
     * Lista clientes segmentados via AJAX com paginação
     */
    public function listarSegmentadosAjax()
    {
        header('Content-Type: application/json');
        
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $perPage = filter_input(INPUT_GET, 'per_page', FILTER_VALIDATE_INT) ?: 10;
        $campanhaId = filter_input(INPUT_GET, 'campanha_id', FILTER_VALIDATE_INT);
        
        $filtros = [
            'dias_min' => filter_input(INPUT_GET, 'dias_min', FILTER_VALIDATE_INT),
            'termo_servico' => filter_input(INPUT_GET, 'termo_servico', FILTER_SANITIZE_SPECIAL_CHARS)
        ];
        
        if ($campanhaId) {
            $filtros['campanha_id'] = $campanhaId;
        }
        
        $clientes = $this->interacaoModel->getClientesFiltroCRM($filtros, $page, $perPage);
        $total = $this->interacaoModel->countClientesFiltroCRM($filtros);
        
        // Gerar HTML parcial
        ob_start();
        if (empty($clientes)) {
            echo '<tr><td colspan="5" class="text-center text-muted">Nenhum cliente encontrado com estes filtros.</td></tr>';
        } else {
            foreach ($clientes as $c) {
                $tel = preg_replace('/\D+/', '', $c['telefone_principal'] ?? '');
                $ultimaVisita = $c['ultima_visita'] ? date('d/m/Y', strtotime($c['ultima_visita'])) : 'Nunca';
                $badgeClass = $c['dias_sem_vir'] > 180 ? 'bg-danger' : ($c['dias_sem_vir'] > 90 ? 'bg-warning' : 'bg-info');
                ?>
                <tr class="cliente-row" 
                    data-id="<?= $c['id'] ?>" 
                    data-nome="<?= htmlspecialchars($c['nome_completo']) ?>" 
                    data-tel="<?= $tel ?>">
                    <td><strong><?= htmlspecialchars($c['nome_completo']) ?></strong></td>
                    <td>
                        <?php if ($tel): ?>
                            <a href="https://wa.me/55<?= $tel ?>" target="_blank"><?= htmlspecialchars($c['telefone_principal']) ?></a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td><?= $ultimaVisita ?></td>
                    <td>
                        <?php if ($c['dias_sem_vir'] !== null): ?>
                            <span class="badge <?= $badgeClass ?>"><?= (int)$c['dias_sem_vir'] ?> dias</span>
                        <?php else: ?>
                            --
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="<?= BASE_URL ?>clientes/view?id=<?= $c['id'] ?>" class="btn btn-primary btn-xs">Ver Jornada</a>
                            <?php if ($tel): ?>
                                <button class="btn btn-success btn-xs" onclick="abrirMensagemCRM(<?= $c['id'] ?>, <?= json_encode($c['nome_completo']) ?>, '<?= $tel ?>')">WhatsApp</button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php
            }
        }
        $html = ob_get_clean();
        
        echo json_encode([
            'success' => true,
            'html' => $html,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ]);
        exit;
    }

    /**
     * Lista clientes contactados via AJAX com paginação
     */
    public function listarContactadosAjax()
    {
        header('Content-Type: application/json');
        
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $perPage = filter_input(INPUT_GET, 'per_page', FILTER_VALIDATE_INT) ?: 10;
        $campanhaId = filter_input(INPUT_GET, 'campanha_id', FILTER_VALIDATE_INT);
        
        if (!$campanhaId) {
            echo json_encode(['success' => false, 'message' => 'Campanha não informada']);
            exit;
        }
        
        $clientes = $this->interacaoModel->getClientesContactadosCampanha($campanhaId, $page, $perPage);
        $total = $this->interacaoModel->countClientesContactadosCampanha($campanhaId);
        
        // Gerar HTML parcial
        ob_start();
        foreach ($clientes as $c) {
            $tel = preg_replace('/\D+/', '', $c['telefone_principal'] ?? '');
            ?>
            <tr data-interacao-id="<?= $c['interacao_id'] ?>" data-cliente-id="<?= $c['id'] ?>">
                <td><strong><?= htmlspecialchars($c['nome_completo']) ?></strong></td>
                <td>
                    <?php if ($tel): ?>
                        <a href="https://wa.me/55<?= $tel ?>" target="_blank"><?= htmlspecialchars($c['telefone_principal']) ?></a>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td><?= date('d/m/Y H:i', strtotime($c['data_envio'])) ?></td>
                <td class="resposta-cliente"><?= htmlspecialchars($c['resposta_cliente'] ?? '-') ?></td>
                <td class="lista-negra">
                    <?php if ($c['lista_negra']): ?>
                        <span class="badge bg-danger">Sim</span>
                    <?php else: ?>
                        <span class="badge bg-success">Não</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="btn btn-warning btn-xs btn-editar-interacao"
                            data-interacao-id="<?= $c['interacao_id'] ?>"
                            data-cliente-id="<?= $c['id'] ?>"
                            data-resposta-cliente='<?= json_encode($c['resposta_cliente'] ?? '', JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG) ?>'
                            data-lista-negra="<?= $c['lista_negra'] ?>"
                            data-campanha-id="<?= $campanhaId ?>">
                            ✏️ Editar
                        </button>
                        <a href="<?= BASE_URL ?>clientes/view?id=<?= $c['id'] ?>" class="btn btn-primary btn-xs">Ver Jornada</a>
                    </div>
                </td>
            </tr>
            <?php
        }
        $html = ob_get_clean();
        
        echo json_encode([
            'success' => true,
            'html' => $html,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ]);
        exit;
    }

    public function salvarConfiguracao()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('crm');
        }

        $mensagem = filter_input(INPUT_POST, 'crm_mensagem_padrao', FILTER_UNSAFE_RAW);
        $mensagem = htmlspecialchars(trim($mensagem ?? ''), ENT_QUOTES, 'UTF-8');
        $posVendaMensagem = filter_input(INPUT_POST, 'pos_venda_mensagem_padrao', FILTER_UNSAFE_RAW);
        $posVendaMensagem = htmlspecialchars(trim($posVendaMensagem ?? ''), ENT_QUOTES, 'UTF-8');
        
        if ($mensagem) {
            $this->configModel->setValor('crm_mensagem_padrao', $mensagem, 'Mensagem padrão sugerida no CRM');
            $this->log("Atualizou Configuracao CRM", "Nova mensagem padrão definida");
        }

        if ($posVendaMensagem) {
            $this->configModel->setValor('pos_venda_mensagem_padrao', $posVendaMensagem, 'Mensagem padrão enviada no Pós-Venda');
            $this->log("Atualizou Configuracao Pós-Venda", "Nova mensagem de pós-venda definida");
        }

        $this->redirect('crm');
    }

    public function salvarCampanha()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('crm');
        }

        $nome = filter_input(INPUT_POST, 'nome', FILTER_UNSAFE_RAW);
        $nome = htmlspecialchars(trim($nome ?? ''), ENT_QUOTES, 'UTF-8');
        $mensagem = filter_input(INPUT_POST, 'mensagem_padrao', FILTER_UNSAFE_RAW);
        $mensagem = htmlspecialchars(trim($mensagem ?? ''), ENT_QUOTES, 'UTF-8');
        $filtros = [
            'dias_min' => filter_input(INPUT_POST, 'dias_min', FILTER_VALIDATE_INT),
            'termo_servico' => filter_input(INPUT_POST, 'termo_servico', FILTER_UNSAFE_RAW)
        ];
        $filtros['termo_servico'] = htmlspecialchars($filtros['termo_servico'] ?? '', ENT_QUOTES, 'UTF-8');

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
        $tipo = filter_input(INPUT_POST, 'tipo', FILTER_UNSAFE_RAW);
        $tipo = htmlspecialchars(trim($tipo ?? ''), ENT_QUOTES, 'UTF-8');
        $canal = filter_input(INPUT_POST, 'canal', FILTER_UNSAFE_RAW);
        $canal = htmlspecialchars(trim($canal ?? ''), ENT_QUOTES, 'UTF-8');
        $assunto = filter_input(INPUT_POST, 'assunto', FILTER_UNSAFE_RAW);
        $assunto = htmlspecialchars(trim($assunto ?? ''), ENT_QUOTES, 'UTF-8');
        $descricao = filter_input(INPUT_POST, 'descricao', FILTER_UNSAFE_RAW);
        $descricao = htmlspecialchars(trim($descricao ?? ''), ENT_QUOTES, 'UTF-8');
        $resposta = filter_input(INPUT_POST, 'resposta_cliente', FILTER_UNSAFE_RAW);
        $resposta = htmlspecialchars(trim($resposta ?? ''), ENT_QUOTES, 'UTF-8');
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

        if ($campanhaId) {
            $redirectUrl = "crm?campanha_id={$campanhaId}";
            $this->redirect($redirectUrl);
        } else {
            $this->redirect("clientes/view?id={$clienteId}");
        }
    }

    public function registrarCampanhaLote()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('crm');
        }

        $clienteIds = $_POST['cliente_ids'] ?? [];
        $assunto = filter_input(INPUT_POST, 'assunto', FILTER_UNSAFE_RAW);
        $assunto = htmlspecialchars(trim($assunto ?? ''), ENT_QUOTES, 'UTF-8');
        $mensagem = filter_input(INPUT_POST, 'mensagem', FILTER_UNSAFE_RAW);
        $mensagem = htmlspecialchars(trim($mensagem ?? ''), ENT_QUOTES, 'UTF-8');
        $campanhaId = filter_input(INPUT_POST, 'campanha_id', FILTER_VALIDATE_INT);

        if (empty($clienteIds) || !$assunto) {
            $redirectUrl = $campanhaId ? "crm?campanha_id=" . $campanhaId : "crm";
            $this->redirect($redirectUrl);
        }

        foreach ($clienteIds as $id) {
            $this->interacaoModel->create([
                'cliente_id' => (int)$id,
                'usuario_id' => Auth::id(),
                'tipo' => 'campanha',
                'canal' => 'whatsapp',
                'assunto' => $assunto,
                'descricao' => $mensagem,
                'campanha_id' => $campanhaId
            ]);
        }

        $log = new Log();
        $log->registrar(Auth::id(), "CRM: Campanha em Lote", count($clienteIds) . " clientes");

        $redirectUrl = $campanhaId ? "crm?campanha_id=" . $campanhaId : "crm";
        $this->redirect($redirectUrl);
    }

    public function atualizarInteracaoCliente()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('crm');
        }

        $interacaoId = filter_input(INPUT_POST, 'interacao_id', FILTER_VALIDATE_INT);
        $clienteId = filter_input(INPUT_POST, 'cliente_id', FILTER_VALIDATE_INT);
        $respostaCliente = filter_input(INPUT_POST, 'resposta_cliente', FILTER_UNSAFE_RAW);
        $respostaCliente = htmlspecialchars(trim($respostaCliente ?? ''), ENT_QUOTES, 'UTF-8');
        $listaNegra = isset($_POST['lista_negra']) ? 1 : 0;
        $campanhaId = filter_input(INPUT_POST, 'campanha_id', FILTER_VALIDATE_INT);
        $isAjax = isset($_POST['ajax']);

        if (!$interacaoId || !$clienteId) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
                exit;
            }
            $this->redirect('crm');
        }

        // Atualizar a interação
        $this->interacaoModel->update($interacaoId, [
            'resposta_cliente' => $respostaCliente
        ]);

        // Atualizar o cliente (lista negra)
        $clienteModel = new Cliente();
        $clienteModel->update($clienteId, [
            'lista_negra' => $listaNegra
        ]);

        $this->log("CRM: Atualizou interacao e cliente", "Cliente #{$clienteId}, Interacao #{$interacaoId}");

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'resposta_cliente' => $respostaCliente,
                'lista_negra' => $listaNegra
            ]);
            exit;
        }

        $redirectUrl = $campanhaId ? "crm?campanha_id=" . $campanhaId : "crm";
        $this->redirect($redirectUrl);
    }
}
